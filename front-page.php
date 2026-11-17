<?php
/**
 * Homepage: the brief's vertical rhythm of carousels and brand statements.
 *
 *  1. Announcement bar (header.php)
 *  2. Hero with the faded ADiva illustration behind it
 *  3. As seen on — marquee
 *  4. Shop New Arrivals — carousel
 *  5. Our promise to you — marquee
 *  6. Made for every chapter — bags carousel
 *  7. Simply Stunning — clothing carousel
 *  8. Welcome to ADiva — brand story
 *  9. Fair trade & ethically made
 * 10. Shop bags / clothing / jewelry tiles
 *
 * @package adiva
 */

defined( 'ABSPATH' ) || exit;

get_header();

get_template_part( 'template-parts/home/hero' );
get_template_part( 'template-parts/home/as-seen-on' );

$adiva_cat1 = (string) adiva_mod( 'adiva_carousel_1_cat' );
get_template_part(
	'template-parts/product/carousel',
	null,
	array(
		'id'       => 'new-arrivals',
		'eyebrow'  => __( 'Just landed', 'adiva' ),
		'title'    => adiva_mod( 'adiva_carousel_1_title' ),
		'link'     => adiva_category_url( $adiva_cat1 ),
		'products' => adiva_get_products( array( 'category' => $adiva_cat1, 'limit' => 12 ) ),
	)
);

get_template_part( 'template-parts/home/promise' );

$adiva_cat2 = (string) adiva_mod( 'adiva_carousel_2_cat' );
get_template_part(
	'template-parts/product/carousel',
	null,
	array(
		'id'       => 'every-chapter',
		'eyebrow'  => __( 'Handbags', 'adiva' ),
		'title'    => adiva_mod( 'adiva_carousel_2_title' ),
		'link'     => adiva_category_url( $adiva_cat2 ),
		'products' => adiva_get_products( array( 'category' => $adiva_cat2, 'limit' => 12, 'orderby' => 'popularity' ) ),
		'class'    => 'carousel-section--tinted',
	)
);

$adiva_cat3 = (string) adiva_mod( 'adiva_carousel_3_cat' );
get_template_part(
	'template-parts/product/carousel',
	null,
	array(
		'id'       => 'simply-stunning',
		'eyebrow'  => __( 'Clothing', 'adiva' ),
		'title'    => adiva_mod( 'adiva_carousel_3_title' ),
		'link'     => adiva_category_url( $adiva_cat3 ),
		'products' => adiva_get_products( array( 'category' => $adiva_cat3, 'limit' => 12 ) ),
	)
);

get_template_part( 'template-parts/home/welcome' );
get_template_part( 'template-parts/home/fair-trade' );
get_template_part( 'template-parts/home/shop-cta' );

get_footer();
