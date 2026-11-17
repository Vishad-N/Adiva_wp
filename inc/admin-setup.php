<?php
/**
 * Appearance → ADiva Setup: one-click creation of pages, product categories
 * and menus matching the brief. Every action is idempotent (re-running skips
 * anything that already exists) and nonce + capability protected.
 *
 * @package adiva
 */

defined( 'ABSPATH' ) || exit;

/**
 * Register the screen.
 */
function adiva_setup_menu(): void {
	add_theme_page( __( 'ADiva Setup', 'adiva' ), __( 'ADiva Setup', 'adiva' ), 'edit_theme_options', 'adiva-setup', 'adiva_setup_screen' );
}
add_action( 'admin_menu', 'adiva_setup_menu' );

/**
 * Render the screen.
 */
function adiva_setup_screen(): void {
	if ( ! current_user_can( 'edit_theme_options' ) ) {
		return;
	}
	// phpcs:ignore WordPress.Security.NonceVerification.Recommended -- display only.
	$done = isset( $_GET['adiva_done'] ) ? sanitize_text_field( wp_unslash( $_GET['adiva_done'] ) ) : '';
	$subs = (array) get_option( 'adiva_newsletter', array() );
	?>
	<div class="wrap">
		<h1><?php esc_html_e( 'ADiva Setup', 'adiva' ); ?></h1>
		<?php if ( $done ) : ?>
			<div class="notice notice-success"><p><?php echo esc_html( $done ); ?></p></div>
		<?php endif; ?>
		<?php if ( ! class_exists( 'WooCommerce' ) ) : ?>
			<div class="notice notice-warning"><p><?php esc_html_e( 'Install and activate WooCommerce first to create product categories and use the shop.', 'adiva' ); ?></p></div>
		<?php endif; ?>
		<p><?php esc_html_e( 'Each step is safe to run more than once — existing pages, categories and menus are left untouched.', 'adiva' ); ?></p>
		<form method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>">
			<?php wp_nonce_field( 'adiva_setup' ); ?>
			<input type="hidden" name="action" value="adiva_setup">
			<p><label><input type="checkbox" name="steps[]" value="pages" checked> <?php esc_html_e( 'Create starter pages (About, Our Story, Sustainability, policies, Wishlist…)', 'adiva' ); ?></label></p>
			<p><label><input type="checkbox" name="steps[]" value="cats" checked> <?php esc_html_e( 'Create product categories from the menu hierarchy', 'adiva' ); ?></label></p>
			<p><label><input type="checkbox" name="steps[]" value="menus" checked> <?php esc_html_e( 'Build Primary + footer menus and assign them', 'adiva' ); ?></label></p>
			<p><label><input type="checkbox" name="steps[]" value="home" checked> <?php esc_html_e( 'Create a “Home” page and set it as the front page', 'adiva' ); ?></label></p>
			<?php submit_button( __( 'Run setup', 'adiva' ) ); ?>
		</form>

		<h2><?php esc_html_e( 'Newsletter sign-ups', 'adiva' ); ?></h2>
		<p>
			<?php /* translators: %d: subscriber count */ printf( esc_html__( '%d subscribers collected by the footer form.', 'adiva' ), count( $subs ) ); ?>
			<?php if ( $subs ) : ?>
				<a class="button" href="<?php echo esc_url( wp_nonce_url( admin_url( 'admin-post.php?action=adiva_export_newsletter' ), 'adiva_export' ) ); ?>"><?php esc_html_e( 'Download CSV', 'adiva' ); ?></a>
			<?php endif; ?>
		</p>
	</div>
	<?php
}

/**
 * Run the selected steps.
 */
function adiva_setup_run(): void {
	if ( ! current_user_can( 'edit_theme_options' ) ) {
		wp_die( esc_html__( 'Sorry, you are not allowed to do that.', 'adiva' ) );
	}
	check_admin_referer( 'adiva_setup' );

	$steps = isset( $_POST['steps'] ) ? array_map( 'sanitize_key', (array) wp_unslash( $_POST['steps'] ) ) : array();
	$log   = array();

	if ( in_array( 'pages', $steps, true ) ) {
		$n = 0;
		foreach ( adiva_starter_pages() as $slug => $page ) {
			if ( get_page_by_path( $slug ) ) {
				continue;
			}
			$id = wp_insert_post(
				array(
					'post_type'    => 'page',
					'post_status'  => 'publish',
					'post_name'    => $slug,
					'post_title'   => $page[0],
					'post_content' => $page[2],
				)
			);
			if ( $id && ! is_wp_error( $id ) && $page[1] ) {
				update_post_meta( $id, '_wp_page_template', $page[1] );
			}
			++$n;
		}
		/* translators: %d: count */
		$log[] = sprintf( __( '%d pages created.', 'adiva' ), $n );
	}

	if ( in_array( 'cats', $steps, true ) && taxonomy_exists( 'product_cat' ) ) {
		$n = 0;
		foreach ( adiva_default_menu() as $node ) {
			$parent = 0;
			if ( ! empty( $node['cat'] ) ) {
				$parent = adiva_ensure_term( $node['label'], $node['cat'], 0, $n );
			}
			foreach ( $node['children'] as $child ) {
				if ( ! empty( $child['cat'] ) && $child['cat'] !== ( $node['cat'] ?? '' ) ) {
					adiva_ensure_term( $child['label'], $child['cat'], $parent, $n );
				}
			}
		}
		/* translators: %d: count */
		$log[] = sprintf( __( '%d product categories created.', 'adiva' ), $n );
	}

	if ( in_array( 'menus', $steps, true ) ) {
		$locations = get_theme_mod( 'nav_menu_locations', array() );

		if ( empty( $locations['primary'] ) ) {
			$menu_id = adiva_create_menu( __( 'ADiva Primary', 'adiva' ) );
			if ( $menu_id ) {
				foreach ( adiva_default_menu() as $node ) {
					$parent = wp_update_nav_menu_item( $menu_id, 0, array( 'menu-item-title' => $node['label'], 'menu-item-url' => adiva_menu_node_url( $node ), 'menu-item-status' => 'publish', 'menu-item-type' => 'custom', 'menu-item-description' => $node['feature']['text'] ?? '' ) );
					foreach ( $node['children'] as $child ) {
						wp_update_nav_menu_item( $menu_id, 0, array( 'menu-item-title' => $child['label'], 'menu-item-url' => adiva_menu_node_url( $child ), 'menu-item-status' => 'publish', 'menu-item-type' => 'custom', 'menu-item-parent-id' => $parent ) );
					}
				}
				$locations['primary'] = $menu_id;
			}
		}
		foreach ( adiva_default_footer_menus() as $loc => $col ) {
			if ( ! empty( $locations[ $loc ] ) ) {
				continue;
			}
			$menu_id = adiva_create_menu( 'ADiva ' . $col['title'] );
			if ( $menu_id ) {
				foreach ( $col['links'] as $link ) {
					wp_update_nav_menu_item( $menu_id, 0, array( 'menu-item-title' => $link[0], 'menu-item-url' => $link[1], 'menu-item-status' => 'publish', 'menu-item-type' => 'custom' ) );
				}
				$locations[ $loc ] = $menu_id;
			}
		}
		set_theme_mod( 'nav_menu_locations', $locations );
		$log[] = __( 'Menus built and assigned.', 'adiva' );
	}

	if ( in_array( 'home', $steps, true ) ) {
		$home = get_page_by_path( 'home' );
		$id   = $home ? $home->ID : wp_insert_post( array( 'post_type' => 'page', 'post_status' => 'publish', 'post_name' => 'home', 'post_title' => __( 'Home', 'adiva' ) ) );
		if ( $id && ! is_wp_error( $id ) ) {
			update_option( 'show_on_front', 'page' );
			update_option( 'page_on_front', (int) $id );
			$log[] = __( 'Front page set.', 'adiva' );
		}
	}

	wp_safe_redirect( add_query_arg( array( 'page' => 'adiva-setup', 'adiva_done' => rawurlencode( implode( ' ', $log ) ) ), admin_url( 'themes.php' ) ) );
	exit;
}
add_action( 'admin_post_adiva_setup', 'adiva_setup_run' );

/**
 * Create a product category if missing; returns its term ID.
 *
 * @param int $count Incremented when a term is created.
 */
function adiva_ensure_term( string $name, string $slug, int $parent, int &$count ): int {
	$term = get_term_by( 'slug', $slug, 'product_cat' );
	if ( $term ) {
		return (int) $term->term_id;
	}
	$res = wp_insert_term( $name, 'product_cat', array( 'slug' => $slug, 'parent' => $parent ) );
	if ( is_wp_error( $res ) ) {
		return 0;
	}
	++$count;
	return (int) $res['term_id'];
}

/**
 * Create (or reuse) a nav menu by name.
 */
function adiva_create_menu( string $name ): int {
	$existing = wp_get_nav_menu_object( $name );
	if ( $existing ) {
		return (int) $existing->term_id;
	}
	$id = wp_create_nav_menu( $name );
	return is_wp_error( $id ) ? 0 : (int) $id;
}

/**
 * Newsletter CSV export.
 */
function adiva_export_newsletter(): void {
	if ( ! current_user_can( 'edit_theme_options' ) ) {
		wp_die( esc_html__( 'Sorry, you are not allowed to do that.', 'adiva' ) );
	}
	check_admin_referer( 'adiva_export' );
	nocache_headers();
	header( 'Content-Type: text/csv; charset=utf-8' );
	header( 'Content-Disposition: attachment; filename=adiva-newsletter.csv' );
	$out = fopen( 'php://output', 'w' );
	fputcsv( $out, array( 'email', 'subscribed_at' ) );
	foreach ( (array) get_option( 'adiva_newsletter', array() ) as $email => $date ) {
		fputcsv( $out, array( $email, $date ) );
	}
	fclose( $out ); // phpcs:ignore WordPress.WP.AlternativeFunctions
	exit;
}
add_action( 'admin_post_adiva_export_newsletter', 'adiva_export_newsletter' );

/**
 * Nudge after activation.
 */
function adiva_setup_notice(): void {
	$screen = get_current_screen();
	if ( ! $screen || 'appearance_page_adiva-setup' === $screen->id || ! current_user_can( 'edit_theme_options' ) || get_option( 'adiva_setup_notice_dismissed' ) ) {
		return;
	}
	if ( has_nav_menu( 'primary' ) ) {
		return;
	}
	printf(
		'<div class="notice notice-info"><p>%s <a href="%s">%s</a></p></div>',
		esc_html__( 'Welcome to ADiva! Create your pages, categories and menus in one click:', 'adiva' ),
		esc_url( admin_url( 'themes.php?page=adiva-setup' ) ),
		esc_html__( 'Open ADiva Setup', 'adiva' )
	);
}
add_action( 'admin_notices', 'adiva_setup_notice' );
