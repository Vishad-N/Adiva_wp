<?php
/**
 * Customizer: announcement bar, homepage copy, trust/shipping text, colours.
 *
 * @package adiva
 */

defined( 'ABSPATH' ) || exit;

/**
 * Default values in one place so templates and the Customizer agree.
 */
function adiva_defaults(): array {
	return array(
		'adiva_announcement_enable'  => true,
		'adiva_announcements'        => __( "THE FALL SALE IS ON: 30% OFF SELECT STYLES|/product-category/fall/\nFREE SHIPPING ON ORDERS OVER $75|/shipping-info/\nHANDMADE IN INDIA · EVERY PIECE ONE OF A KIND|/about-us/", 'adiva' ),
		'adiva_hero_eyebrow'         => __( 'Fall ’26 · Handmade in India', 'adiva' ),
		'adiva_hero_title'           => __( 'Crafted by hand, made for the wanderer.', 'adiva' ),
		'adiva_hero_text'            => __( 'Embroidered bags, hand-block printed dresses and little treasures — each one made slowly, by artisans whose craft runs through generations.', 'adiva' ),
		'adiva_press'                => '',
		'adiva_carousel_1_title'     => __( 'Shop New Arrivals', 'adiva' ),
		'adiva_carousel_1_cat'       => 'new',
		'adiva_carousel_2_title'     => __( 'Made for every chapter. Now in bloom.', 'adiva' ),
		'adiva_carousel_2_cat'       => 'handbags',
		'adiva_carousel_3_title'     => __( 'Simply Stunning: styles that define timeless fashion', 'adiva' ),
		'adiva_carousel_3_cat'       => 'clothing',
		'adiva_welcome_title'        => __( 'Welcome to ADiva', 'adiva' ),
		'adiva_welcome_text'         => __( 'ADiva began with a simple belief: the most beautiful things are made slowly, by hand. We pair centuries-old Indian techniques — zardozi, hand-block printing, mirror work, beading — with easy Western silhouettes, so every bag and dress feels both timeless and entirely yours. Sustainable, handmade and one of a kind.', 'adiva' ),
		'adiva_fairtrade_text'       => __( 'Our artisans are partners, not suppliers. Fair wages, safe workshops and long-term relationships are woven into every order you place.', 'adiva' ),
		'adiva_free_shipping'        => 75,
		'adiva_shipping_text'        => __( "Free standard shipping on orders over $75. Orders are hand-checked and dispatched within 1–2 business days; delivery usually takes 5–12 business days.\nReturns are accepted within 30 days for unused items with tags attached. A small processing fee may apply to non-defective returns. Final-sale items cannot be returned.", 'adiva' ),
		'adiva_return_address'       => '',
		'adiva_color_accent'         => '#FD0207',
		'adiva_social_instagram'     => '',
		'adiva_social_facebook'      => '',
		'adiva_social_pinterest'     => '',
		'adiva_social_youtube'       => '',
	);
}

/**
 * Theme mod with the central default.
 *
 * @return mixed
 */
function adiva_mod( string $key ) {
	$defaults = adiva_defaults();
	return get_theme_mod( $key, $defaults[ $key ] ?? '' );
}

/**
 * Announcement messages as [ [text, url], … ].
 */
function adiva_announcements(): array {
	$lines = preg_split( '/\r\n|\r|\n/', (string) adiva_mod( 'adiva_announcements' ) );
	$out   = array();
	foreach ( $lines as $line ) {
		$line = trim( $line );
		if ( '' === $line ) {
			continue;
		}
		$parts = array_map( 'trim', explode( '|', $line, 2 ) );
		$url   = $parts[1] ?? '';
		if ( $url && '/' === $url[0] ) {
			$url = home_url( $url );
		}
		$out[] = array( $parts[0], $url );
	}
	return $out;
}

/**
 * Register Customizer controls.
 *
 * @param WP_Customize_Manager $wp_customize Manager.
 */
function adiva_customize_register( $wp_customize ): void {
	$d = adiva_defaults();

	$wp_customize->add_panel( 'adiva', array( 'title' => __( 'ADiva Theme', 'adiva' ), 'priority' => 30 ) );

	$sections = array(
		'adiva_announce' => __( 'Announcement Bar', 'adiva' ),
		'adiva_home'     => __( 'Homepage', 'adiva' ),
		'adiva_shop'     => __( 'Shipping & Trust', 'adiva' ),
		'adiva_brand'    => __( 'Brand & Social', 'adiva' ),
	);
	foreach ( $sections as $id => $title ) {
		$wp_customize->add_section( $id, array( 'title' => $title, 'panel' => 'adiva' ) );
	}

	$fields = array(
		array( 'adiva_announcement_enable', 'adiva_announce', 'checkbox', __( 'Show announcement bar', 'adiva' ), '' ),
		array( 'adiva_announcements', 'adiva_announce', 'textarea', __( 'Messages', 'adiva' ), __( 'One per line. Optional link after a pipe: MESSAGE|/url/. Multiple lines auto-rotate.', 'adiva' ) ),
		array( 'adiva_hero_eyebrow', 'adiva_home', 'text', __( 'Hero eyebrow', 'adiva' ), '' ),
		array( 'adiva_hero_title', 'adiva_home', 'text', __( 'Hero title', 'adiva' ), '' ),
		array( 'adiva_hero_text', 'adiva_home', 'textarea', __( 'Hero text', 'adiva' ), '' ),
		array( 'adiva_press', 'adiva_home', 'textarea', __( '“As seen on” names', 'adiva' ), __( 'One publication per line. Leave empty to show a product-image marquee instead. Only list press that has genuinely featured ADiva.', 'adiva' ) ),
		array( 'adiva_carousel_1_title', 'adiva_home', 'text', __( 'Carousel 1 title', 'adiva' ), '' ),
		array( 'adiva_carousel_1_cat', 'adiva_home', 'text', __( 'Carousel 1 category slug', 'adiva' ), __( 'Empty = newest products.', 'adiva' ) ),
		array( 'adiva_carousel_2_title', 'adiva_home', 'text', __( 'Carousel 2 title', 'adiva' ), '' ),
		array( 'adiva_carousel_2_cat', 'adiva_home', 'text', __( 'Carousel 2 category slug', 'adiva' ), '' ),
		array( 'adiva_carousel_3_title', 'adiva_home', 'text', __( 'Carousel 3 title', 'adiva' ), '' ),
		array( 'adiva_carousel_3_cat', 'adiva_home', 'text', __( 'Carousel 3 category slug', 'adiva' ), '' ),
		array( 'adiva_welcome_title', 'adiva_home', 'text', __( 'Welcome title', 'adiva' ), '' ),
		array( 'adiva_welcome_text', 'adiva_home', 'textarea', __( 'Welcome text', 'adiva' ), '' ),
		array( 'adiva_fairtrade_text', 'adiva_home', 'textarea', __( 'Fair trade text', 'adiva' ), '' ),
		array( 'adiva_free_shipping', 'adiva_shop', 'number', __( 'Free shipping threshold', 'adiva' ), __( 'Shown in trust badges. Set the real rule in WooCommerce → Shipping.', 'adiva' ) ),
		array( 'adiva_shipping_text', 'adiva_shop', 'textarea', __( 'Shipping & returns (product page)', 'adiva' ), '' ),
		array( 'adiva_return_address', 'adiva_shop', 'textarea', __( 'Returns address', 'adiva' ), '' ),
		array( 'adiva_social_instagram', 'adiva_brand', 'url', __( 'Instagram URL', 'adiva' ), '' ),
		array( 'adiva_social_facebook', 'adiva_brand', 'url', __( 'Facebook URL', 'adiva' ), '' ),
		array( 'adiva_social_pinterest', 'adiva_brand', 'url', __( 'Pinterest URL', 'adiva' ), '' ),
		array( 'adiva_social_youtube', 'adiva_brand', 'url', __( 'YouTube URL', 'adiva' ), '' ),
	);

	foreach ( $fields as $f ) {
		list( $id, $section, $type, $label, $desc ) = $f;
		$sanitize = array(
			'checkbox' => 'adiva_sanitize_checkbox',
			'textarea' => 'sanitize_textarea_field',
			'number'   => 'absint',
			'url'      => 'esc_url_raw',
		)[ $type ] ?? 'sanitize_text_field';

		$wp_customize->add_setting( $id, array( 'default' => $d[ $id ], 'sanitize_callback' => $sanitize ) );
		$wp_customize->add_control( $id, array( 'label' => $label, 'description' => $desc, 'section' => $section, 'type' => $type ) );
	}

	$wp_customize->add_setting( 'adiva_color_accent', array( 'default' => $d['adiva_color_accent'], 'sanitize_callback' => 'sanitize_hex_color' ) );
	$wp_customize->add_control(
		new WP_Customize_Color_Control(
			$wp_customize,
			'adiva_color_accent',
			array( 'label' => __( 'Primary accent (buttons, sale)', 'adiva' ), 'section' => 'adiva_brand' )
		)
	);
}
add_action( 'customize_register', 'adiva_customize_register' );

/**
 * Checkbox sanitiser.
 *
 * @param mixed $value Value.
 */
function adiva_sanitize_checkbox( $value ): bool {
	return (bool) $value;
}

/**
 * Print the accent override only when it differs from the default.
 */
function adiva_customizer_css(): void {
	$accent = sanitize_hex_color( (string) adiva_mod( 'adiva_color_accent' ) );
	if ( $accent && strtoupper( $accent ) !== '#FD0207' ) {
		printf( "<style id=\"adiva-custom\">:root{--c-red:%s}</style>\n", esc_html( $accent ) );
	}
}
add_action( 'wp_head', 'adiva_customizer_css', 20 );
