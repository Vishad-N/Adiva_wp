<?php
/**
 * Theme supports, menus, image sizes and asset loading.
 *
 * @package adiva
 */

defined( 'ABSPATH' ) || exit;

/**
 * Register theme features.
 */
function adiva_setup(): void {
	load_theme_textdomain( 'adiva', ADIVA_DIR . '/languages' );

	add_theme_support( 'title-tag' );
	add_theme_support( 'post-thumbnails' );
	add_theme_support( 'automatic-feed-links' );
	add_theme_support( 'responsive-embeds' );
	add_theme_support( 'align-wide' );
	add_theme_support( 'html5', array( 'search-form', 'comment-form', 'comment-list', 'gallery', 'caption', 'style', 'script', 'navigation-widgets' ) );
	add_theme_support(
		'custom-logo',
		array( 'height' => 120, 'width' => 360, 'flex-height' => true, 'flex-width' => true )
	);

	// WooCommerce: we render our own gallery, so only zoom/lightbox/slider
	// support is declared for plugins that check for it.
	add_theme_support(
		'woocommerce',
		array(
			'thumbnail_image_width' => 600,
			'single_image_width'    => 1000,
			'product_grid'          => array( 'default_columns' => 4, 'default_rows' => 3, 'min_columns' => 2, 'max_columns' => 4 ),
		)
	);

	// 4:5 portrait crop for every product card.
	add_image_size( 'adiva-card', 600, 750, true );

	register_nav_menus(
		array(
			'primary'            => __( 'Primary (mega menu)', 'adiva' ),
			'footer-collections' => __( 'Footer: Top Collections', 'adiva' ),
			'footer-company'     => __( 'Footer: Company', 'adiva' ),
			'footer-help'        => __( 'Footer: Get Help', 'adiva' ),
		)
	);

	add_editor_style( 'assets/css/editor.css' );
}
add_action( 'after_setup_theme', 'adiva_setup' );

/**
 * Content width for embeds.
 */
function adiva_content_width(): void {
	$GLOBALS['content_width'] = 760;
}
add_action( 'after_setup_theme', 'adiva_content_width', 0 );

/**
 * Google Fonts URL — Cormorant Garamond for editorial headings, Montserrat
 * (the instructions' named alternative to Gotham) for UI and body.
 * Return '' from the 'adiva_fonts_url' filter to self-host or use system fonts.
 */
function adiva_fonts_url(): string {
	return (string) apply_filters(
		'adiva_fonts_url',
		'https://fonts.googleapis.com/css2?family=Cormorant+Garamond:ital,wght@0,500;0,600;1,500&family=Montserrat:wght@400;500;600&display=swap'
	);
}

/**
 * Enqueue styles and scripts.
 */
function adiva_assets(): void {
	$fonts = adiva_fonts_url();
	if ( $fonts ) {
		wp_enqueue_style( 'adiva-fonts', $fonts, array(), null ); // phpcs:ignore WordPress.WP.EnqueuedResourceParameters.MissingVersion
	}
	wp_enqueue_style( 'adiva-main', ADIVA_URI . '/assets/css/main.css', array(), ADIVA_VERSION );
	if ( adiva_wc_active() || adiva_using_demo() ) {
		wp_enqueue_style( 'adiva-shop', ADIVA_URI . '/assets/css/woocommerce.css', array( 'adiva-main' ), ADIVA_VERSION );
	}

	wp_enqueue_script( 'adiva-main', ADIVA_URI . '/assets/js/main.js', array(), ADIVA_VERSION, array( 'strategy' => 'defer', 'in_footer' => true ) );

	$wc = adiva_wc_active();
	wp_localize_script(
		'adiva-main',
		'ADIVA',
		array(
			'wcAjax'      => $wc && class_exists( 'WC_AJAX' ) ? WC_AJAX::get_endpoint( '%%endpoint%%' ) : '',
			'cartUrl'     => $wc ? wc_get_cart_url() : home_url( '/cart/' ),
			'checkoutUrl' => $wc ? wc_get_checkout_url() : home_url( '/checkout/' ),
			'wishlistUrl' => adiva_page_url( 'wishlist' ),
			'demo'        => adiva_using_demo(),
			'i18n'        => array(
				'added'        => __( 'Added to your bag', 'adiva' ),
				'addToBag'     => __( 'Add to bag', 'adiva' ),
				'chooseOption' => __( 'Choose options', 'adiva' ),
				'viewDetails'  => __( 'View full details', 'adiva' ),
				'wishAdd'      => __( 'Add to wishlist', 'adiva' ),
				'wishRemove'   => __( 'Remove from wishlist', 'adiva' ),
				'wishEmpty'    => __( 'Your wishlist is empty — tap the heart on anything you love.', 'adiva' ),
				'loading'      => __( 'Loading…', 'adiva' ),
				'loadMore'     => __( 'Load more', 'adiva' ),
				'error'        => __( 'Something went wrong. Please try again.', 'adiva' ),
				'outOfStock'   => __( 'Sold out', 'adiva' ),
				'slide'        => __( 'Go to slide %d', 'adiva' ),
			),
		)
	);

	// WooCommerce's own cart-fragment script keeps the drawer + count fresh.
	if ( $wc ) {
		wp_enqueue_script( 'wc-cart-fragments' );
	}
}
add_action( 'wp_enqueue_scripts', 'adiva_assets' );

/**
 * Preconnect to Google Fonts.
 *
 * @param array  $urls          URLs.
 * @param string $relation_type Relation.
 */
function adiva_resource_hints( array $urls, string $relation_type ): array {
	if ( 'preconnect' === $relation_type && adiva_fonts_url() ) {
		$urls[] = array( 'href' => 'https://fonts.gstatic.com', 'crossorigin' );
		$urls[] = 'https://fonts.googleapis.com';
	}
	return $urls;
}
add_filter( 'wp_resource_hints', 'adiva_resource_hints', 10, 2 );

/**
 * Body classes for layout hooks.
 *
 * @param string[] $classes Classes.
 */
function adiva_body_classes( array $classes ): array {
	if ( get_theme_mod( 'adiva_announcement_enable', true ) ) {
		$classes[] = 'has-announcement';
	}
	if ( adiva_using_demo() ) {
		$classes[] = 'adiva-demo';
	}
	return $classes;
}
add_filter( 'body_class', 'adiva_body_classes' );

/**
 * Brand colour in the browser chrome on mobile.
 */
function adiva_meta_theme_color(): void {
	echo '<meta name="theme-color" content="#FDEFD4">' . "\n";
}
add_action( 'wp_head', 'adiva_meta_theme_color', 1 );
