<?php
/**
 * Menus.
 *
 * Both the desktop mega menu and the mobile accordion render from one tree,
 * built either from the menu assigned to the "Primary" location or — until one
 * is assigned — from the default hierarchy below (which mirrors the brief).
 * The setup tool (Appearance → ADiva Setup) can turn the default into a real,
 * editable WordPress menu.
 *
 * @package adiva
 */

defined( 'ABSPATH' ) || exit;

/**
 * Default primary hierarchy. 'cat' = product category slug, 'page' = page slug.
 *
 * @return array<int, array>
 */
function adiva_default_menu(): array {
	$c = static fn( string $label, string $slug ) => array( 'label' => $label, 'cat' => $slug );
	$p = static fn( string $label, string $slug ) => array( 'label' => $label, 'page' => $slug );

	return array(
		array(
			'label'    => __( 'New In', 'adiva' ),
			'cat'      => 'new',
			'feature'  => array( 'art' => 'dress', 'bg' => '#F6C9C4', 'text' => __( 'Fall ’26 has landed', 'adiva' ) ),
			'children' => array(
				$c( __( 'Fall ’26', 'adiva' ), 'fall' ),
				$c( __( 'Spring/Summer ’26', 'adiva' ), 'spring-summer' ),
				$c( __( 'The Vacation Shop', 'adiva' ), 'vacation-shop' ),
				$c( __( 'The Getaway Edit', 'adiva' ), 'getaway-edit' ),
				$c( __( 'New Arrivals', 'adiva' ), 'new' ),
			),
		),
		array(
			'label'    => __( 'Best Sellers', 'adiva' ),
			'cat'      => 'best-sellers',
			'feature'  => array( 'art' => 'shoulder', 'bg' => '#C9D9EE', 'text' => __( 'The pieces you keep coming back for', 'adiva' ) ),
			'children' => array(
				$c( __( 'Best Sellers', 'adiva' ), 'best-sellers' ),
				$c( __( 'Mahjong Collection', 'adiva' ), 'mahjong-collection' ),
				$c( __( 'Make Lemonade Collection', 'adiva' ), 'make-lemonade-collection' ),
				$c( __( 'Evil Eye Collection', 'adiva' ), 'evil-eye-collection' ),
				$c( __( 'Jaguar Collection', 'adiva' ), 'jaguar-collection' ),
				$c( __( 'Animal Print', 'adiva' ), 'animal-print' ),
			),
		),
		array(
			'label'    => __( 'Handbags', 'adiva' ),
			'cat'      => 'handbags',
			'feature'  => array( 'art' => 'tote', 'bg' => '#F6D9A8', 'text' => __( 'Hand-embroidered, one stitch at a time', 'adiva' ) ),
			'children' => array(
				$c( __( 'All Bags', 'adiva' ), 'handbags' ),
				$c( __( 'Beach Bags', 'adiva' ), 'beach-bags' ),
				$c( __( 'Totes', 'adiva' ), 'totes' ),
				$c( __( 'Weekenders', 'adiva' ), 'weekenders' ),
				$c( __( 'Medium Bags', 'adiva' ), 'medium-bags' ),
				$c( __( 'Shoulder Bags', 'adiva' ), 'shoulder-bags' ),
				$c( __( 'Clutches', 'adiva' ), 'clutches' ),
				$c( __( 'Make Up & Small Pouches', 'adiva' ), 'pouches' ),
				$c( __( 'The Daphne Bag Collection', 'adiva' ), 'daphne-bag-collection' ),
				$c( __( 'The Raffia Collection', 'adiva' ), 'raffia-collection' ),
				$c( __( 'The Sienna Bag Collection', 'adiva' ), 'sienna-bag-collection' ),
				$c( __( 'Special Occasion', 'adiva' ), 'special-occasion' ),
			),
		),
		array(
			'label'    => __( 'Clothing', 'adiva' ),
			'cat'      => 'clothing',
			'feature'  => array( 'art' => 'kaftan', 'bg' => '#E8DDF0', 'text' => __( 'Hand-block prints, made to move', 'adiva' ) ),
			'children' => array(
				$c( __( 'All Clothing', 'adiva' ), 'clothing' ),
				$c( __( 'Dresses', 'adiva' ), 'dresses' ),
				$c( __( 'Tops', 'adiva' ), 'tops' ),
				$c( __( 'Bottoms', 'adiva' ), 'bottoms' ),
				$c( __( 'Cover Ups', 'adiva' ), 'cover-ups' ),
				$c( __( 'Jumpsuits', 'adiva' ), 'jumpsuits' ),
				$c( __( 'Outerwear', 'adiva' ), 'outerwear' ),
				$c( __( 'Co-ord Sets', 'adiva' ), 'co-ord-sets' ),
				$c( __( 'Floral Print', 'adiva' ), 'floral-print' ),
				$c( __( 'Animal Print', 'adiva' ), 'animal-print' ),
				$c( __( 'Solids', 'adiva' ), 'solids' ),
				$c( __( 'Special Occasion', 'adiva' ), 'special-occasion' ),
				$c( __( 'Final Sale', 'adiva' ), 'sale' ),
				$c( __( 'ADiva Atelier', 'adiva' ), 'atelier' ),
			),
		),
		array(
			'label'    => __( 'Accessories', 'adiva' ),
			'cat'      => 'accessories',
			'feature'  => array( 'art' => 'earrings', 'bg' => '#F3D3A5', 'text' => __( 'Little finishing touches', 'adiva' ) ),
			'children' => array(
				$c( __( 'All Accessories', 'adiva' ), 'accessories' ),
				$c( __( 'Jewelry', 'adiva' ), 'jewelry' ),
				$c( __( 'Sandals', 'adiva' ), 'sandals' ),
				$c( __( 'Bag Straps', 'adiva' ), 'bag-straps' ),
				$c( __( 'Tassels', 'adiva' ), 'tassels' ),
				$c( __( 'Bag Charms', 'adiva' ), 'bag-charms' ),
			),
		),
		array(
			'label'    => __( 'Gift Guide', 'adiva' ),
			'page'     => 'gift-guide',
			'feature'  => array( 'art' => 'clutch', 'bg' => '#F3C6CF', 'text' => __( 'Wrapped with love, made by hand', 'adiva' ) ),
			'children' => array(
				$p( __( 'The Gift Guide', 'adiva' ), 'gift-guide' ),
				$c( __( 'Gifts for Her', 'adiva' ), 'gifts-for-her' ),
				$c( __( 'Gifts for Mom', 'adiva' ), 'gifts-for-mom' ),
				$c( __( 'Gifts for the Traveler', 'adiva' ), 'gifts-for-the-traveler' ),
				$c( __( 'Gifts Under $100', 'adiva' ), 'gifts-under-100' ),
				$c( __( 'Gifts Under $150', 'adiva' ), 'gifts-under-150' ),
				$c( __( 'Gifts Under $200', 'adiva' ), 'gifts-under-200' ),
				$c( __( 'Luxury Gifts', 'adiva' ), 'luxury-gifts' ),
				$c( __( 'Holiday', 'adiva' ), 'holiday' ),
				$c( __( 'Beaded Coin Pouches', 'adiva' ), 'beaded-coin-pouches' ),
				$c( __( 'Statement Clutches', 'adiva' ), 'statement-clutches' ),
				$c( __( 'Bestselling Gifts', 'adiva' ), 'bestselling-gifts' ),
				$c( __( 'Gift Cards', 'adiva' ), 'gift-cards' ),
			),
		),
		array(
			'label'    => __( 'Sale', 'adiva' ),
			'cat'      => 'sale',
			'class'    => 'is-sale',
			'children' => array(),
		),
		array(
			'label'    => __( 'About Us', 'adiva' ),
			'page'     => 'about-us',
			'feature'  => array( 'art' => 'round', 'bg' => '#EFD8B4', 'text' => __( 'Meet the hands behind every piece', 'adiva' ) ),
			'children' => array(
				$p( __( 'About Us', 'adiva' ), 'about-us' ),
				$p( __( 'Our Story', 'adiva' ), 'our-story' ),
				$p( __( 'Sustainability', 'adiva' ), 'sustainability' ),
				$p( __( 'Behind the Product', 'adiva' ), 'behind-the-product' ),
			),
		),
	);
}

/**
 * Resolve a default-menu node's URL.
 */
function adiva_menu_node_url( array $node ): string {
	if ( ! empty( $node['url'] ) ) {
		return $node['url'];
	}
	if ( ! empty( $node['page'] ) ) {
		return adiva_page_url( $node['page'] );
	}
	if ( isset( $node['cat'] ) && 'sale' === $node['cat'] && ! taxonomy_exists( 'product_cat' ) ) {
		return home_url( '/product-category/sale/' );
	}
	return adiva_category_url( $node['cat'] ?? '' );
}

/**
 * The primary menu as a normalised tree: label, url, class, feature, children.
 *
 * @return array<int, array>
 */
function adiva_menu_tree(): array {
	$locations = get_nav_menu_locations();
	if ( ! empty( $locations['primary'] ) ) {
		$items = wp_get_nav_menu_items( $locations['primary'] );
		if ( $items ) {
			$tree = array();
			$refs = array();
			foreach ( $items as $item ) {
				$refs[ $item->ID ] = array(
					'label'    => $item->title,
					'url'      => $item->url,
					'class'    => implode( ' ', array_filter( (array) $item->classes ) ),
					'current'  => ( (int) $item->object_id === get_queried_object_id() ),
					'feature'  => $item->description ? array( 'text' => $item->description ) : null,
					'children' => array(),
					'parent'   => (int) $item->menu_item_parent,
				);
			}
			foreach ( $refs as $id => &$node ) {
				if ( $node['parent'] && isset( $refs[ $node['parent'] ] ) ) {
					$refs[ $node['parent'] ]['children'][] = &$node;
				}
			}
			unset( $node );
			foreach ( $refs as $node ) {
				if ( ! $node['parent'] ) {
					$tree[] = $node;
				}
			}
			return (array) apply_filters( 'adiva_menu_tree', $tree );
		}
	}

	$tree = array();
	foreach ( adiva_default_menu() as $node ) {
		$out = array(
			'label'    => $node['label'],
			'url'      => adiva_menu_node_url( $node ),
			'class'    => $node['class'] ?? '',
			'current'  => false,
			'feature'  => $node['feature'] ?? null,
			'children' => array(),
		);
		foreach ( $node['children'] as $child ) {
			$out['children'][] = array( 'label' => $child['label'], 'url' => adiva_menu_node_url( $child ), 'children' => array() );
		}
		$tree[] = $out;
	}
	return (array) apply_filters( 'adiva_menu_tree', $tree );
}

/**
 * Desktop mega menu.
 */
function adiva_primary_nav(): void {
	$tree = adiva_menu_tree();
	$arts = array( 'dress', 'shoulder', 'tote', 'kaftan', 'earrings', 'clutch', 'round', 'beach' );
	?>
	<nav class="primary-nav" aria-label="<?php esc_attr_e( 'Primary', 'adiva' ); ?>">
		<ul class="primary-nav__list">
			<?php foreach ( $tree as $i => $node ) :
				$has  = ! empty( $node['children'] );
				$pid  = 'mega-' . $i;
				$feat = $node['feature'] ?? null;
				?>
				<li class="primary-nav__item<?php echo $has ? ' has-mega' : ''; ?> <?php echo esc_attr( $node['class'] ?? '' ); ?>">
					<a class="primary-nav__link" href="<?php echo esc_url( $node['url'] ); ?>"<?php echo ! empty( $node['current'] ) ? ' aria-current="page"' : ''; ?>><?php echo esc_html( $node['label'] ); ?></a>
					<?php if ( $has ) : ?>
						<button class="mega-toggle" type="button" aria-expanded="false" aria-controls="<?php echo esc_attr( $pid ); ?>">
							<span class="screen-reader-text"><?php /* translators: %s: menu label */ printf( esc_html__( 'Show %s menu', 'adiva' ), esc_html( $node['label'] ) ); ?></span>
							<?php echo adiva_icon( 'down', 12 ); // phpcs:ignore ?>
						</button>
						<div class="mega" id="<?php echo esc_attr( $pid ); ?>">
							<div class="mega__inner container">
								<div class="mega__links">
									<p class="mega__title"><?php echo esc_html( $node['label'] ); ?></p>
									<ul class="mega__list<?php echo count( $node['children'] ) > 6 ? ' is-multi' : ''; ?>">
										<?php foreach ( $node['children'] as $child ) : ?>
											<li><a href="<?php echo esc_url( $child['url'] ); ?>"><?php echo esc_html( $child['label'] ); ?></a></li>
										<?php endforeach; ?>
									</ul>
								</div>
								<?php
								$art = $feat['art'] ?? $arts[ $i % count( $arts ) ];
								$bg  = $feat['bg'] ?? '#F7E3C4';
								?>
								<a class="mega__feature" href="<?php echo esc_url( $node['url'] ); ?>" style="--tint:<?php echo esc_attr( $bg ); ?>" tabindex="-1">
									<img src="<?php echo esc_url( adiva_asset( 'images/demo/' . $art . '.svg' ) ); ?>" alt="" width="240" height="300" loading="lazy" decoding="async">
									<span class="mega__feature-text"><?php echo esc_html( $feat['text'] ?? $node['label'] ); ?></span>
									<span class="mega__feature-cta"><?php esc_html_e( 'Shop now', 'adiva' ); ?></span>
								</a>
								<img class="mega__bag-art" src="<?php echo esc_url( adiva_asset( 'images/bag-outline.svg' ) ); ?>" alt="" width="160" height="160" loading="lazy">
							</div>
						</div>
					<?php endif; ?>
				</li>
			<?php endforeach; ?>
		</ul>
	</nav>
	<?php
}

/**
 * Mobile accordion menu (inside the off-canvas drawer).
 */
function adiva_mobile_nav(): void {
	$tree = adiva_menu_tree();
	?>
	<nav class="mobile-nav" aria-label="<?php esc_attr_e( 'Mobile', 'adiva' ); ?>">
		<ul class="mobile-nav__list">
			<?php foreach ( $tree as $i => $node ) :
				$has = ! empty( $node['children'] );
				$sid = 'm-sub-' . $i;
				?>
				<li class="mobile-nav__item <?php echo esc_attr( $node['class'] ?? '' ); ?>">
					<?php if ( $has ) : ?>
						<button class="mobile-nav__toggle" type="button" aria-expanded="false" aria-controls="<?php echo esc_attr( $sid ); ?>">
							<span><?php echo esc_html( $node['label'] ); ?></span>
							<?php echo adiva_icon( 'plus', 18 ); // phpcs:ignore ?>
						</button>
						<ul class="mobile-nav__sub" id="<?php echo esc_attr( $sid ); ?>" hidden>
							<?php foreach ( $node['children'] as $child ) : ?>
								<li><a href="<?php echo esc_url( $child['url'] ); ?>"><?php echo esc_html( $child['label'] ); ?></a></li>
							<?php endforeach; ?>
						</ul>
					<?php else : ?>
						<a class="mobile-nav__link" href="<?php echo esc_url( $node['url'] ); ?>"><?php echo esc_html( $node['label'] ); ?></a>
					<?php endif; ?>
				</li>
			<?php endforeach; ?>
		</ul>
	</nav>
	<?php
}

/**
 * Default footer columns from the brief.
 *
 * @return array<string, array{title: string, links: array}>
 */
function adiva_default_footer_menus(): array {
	return array(
		'footer-collections' => array(
			'title' => __( 'Top Collections', 'adiva' ),
			'links' => array(
				array( __( 'Floral Collection', 'adiva' ), adiva_category_url( 'floral-print' ) ),
				array( __( 'Evil Eye Collection', 'adiva' ), adiva_category_url( 'evil-eye-collection' ) ),
				array( __( 'Jaguar Collection', 'adiva' ), adiva_category_url( 'jaguar-collection' ) ),
				array( __( 'Leopard Bags', 'adiva' ), adiva_category_url( 'animal-print' ) ),
			),
		),
		'footer-company'     => array(
			'title' => __( 'Company', 'adiva' ),
			'links' => array(
				array( __( 'About Us', 'adiva' ), adiva_page_url( 'about-us' ) ),
				array( __( 'Brand Ambassador', 'adiva' ), adiva_page_url( 'brand-ambassador' ) ),
				array( __( 'Careers', 'adiva' ), adiva_page_url( 'careers' ) ),
				array( __( 'Accessibility Statement', 'adiva' ), adiva_page_url( 'accessibility-statement' ) ),
				array( __( 'Do Not Sell or Share My Personal Information', 'adiva' ), adiva_page_url( 'data-privacy-opt-out' ) ),
				array( __( 'Privacy Policy', 'adiva' ), adiva_page_url( 'privacy-policy' ) ),
				array( __( 'Wholesale', 'adiva' ), adiva_page_url( 'wholesale' ) ),
				array( __( 'Corporate Gifting', 'adiva' ), adiva_page_url( 'corporate-gifting' ) ),
			),
		),
		'footer-help'        => array(
			'title' => __( 'Get Help', 'adiva' ),
			'links' => array(
				array( __( 'FAQ', 'adiva' ), adiva_page_url( 'faq' ) ),
				array( __( 'Contact Us', 'adiva' ), adiva_page_url( 'contact-us' ) ),
				array( __( 'Start a Return or Exchange', 'adiva' ), adiva_page_url( 'return-policy' ) ),
				array( __( 'Return Policy', 'adiva' ), adiva_page_url( 'return-policy' ) ),
				array( __( 'Shipping Info', 'adiva' ), adiva_page_url( 'shipping-info' ) ),
			),
		),
	);
}

/**
 * Print one footer column: the assigned menu, or the default links.
 */
function adiva_footer_column( string $location ): void {
	$defaults = adiva_default_footer_menus()[ $location ];
	$title    = $defaults['title'];
	$id       = 'fcol-' . $location;
	?>
	<div class="footer-col">
		<h2 class="footer-col__title" id="<?php echo esc_attr( $id ); ?>"><?php echo esc_html( $title ); ?></h2>
		<?php
		if ( has_nav_menu( $location ) ) {
			wp_nav_menu(
				array(
					'theme_location'  => $location,
					'container'       => 'nav',
					'container_aria_label' => $title,
					'menu_class'      => 'footer-col__list',
					'depth'           => 1,
					'fallback_cb'     => false,
				)
			);
		} else {
			echo '<nav aria-labelledby="' . esc_attr( $id ) . '"><ul class="footer-col__list">';
			foreach ( $defaults['links'] as $link ) {
				echo '<li><a href="' . esc_url( $link[1] ) . '">' . esc_html( $link[0] ) . '</a></li>';
			}
			echo '</ul></nav>';
		}
		?>
	</div>
	<?php
}
