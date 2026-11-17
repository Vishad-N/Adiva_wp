<?php
/**
 * Router for the local preview — DEVELOPMENT ONLY.
 *
 *   cd adiva
 *   php -S 127.0.0.1:8089 -t . dev-preview/router.php
 *
 * Renders the theme's real templates with the demo catalogue:
 *   /                              front-page.php
 *   /shop/ , /product-category/x/  woocommerce/archive-product.php (+ /page/N/)
 *   /product/slug/                 woocommerce/single-product.php
 *   /?s=term&post_type=product     archive (search)
 *   /about-us/ etc.                starter pages via page.php / page templates
 *
 * @package adiva
 */

if ( PHP_SAPI !== 'cli-server' ) {
	http_response_code( 404 );
	exit;
}

$adiva_uri  = parse_url( $_SERVER['REQUEST_URI'], PHP_URL_PATH ) ?: '/';
$adiva_root = dirname( __DIR__ );

// Static assets are served directly; never execute other PHP files by URL.
if ( '/' !== $adiva_uri && is_file( $adiva_root . $adiva_uri ) ) {
	if ( str_ends_with( $adiva_uri, '.php' ) ) {
		http_response_code( 404 );
		exit;
	}
	return false;
}

require __DIR__ . '/wp-shim.php';
require $adiva_root . '/functions.php';
do_action( 'after_setup_theme' );

$pv = &$GLOBALS['adiva_preview'];
if ( preg_match( '#/page/(\d+)/?$#', $adiva_uri, $m ) ) {
	$pv['qv']['paged'] = (int) $m[1];
	$adiva_uri         = substr( $adiva_uri, 0, -strlen( $m[0] ) ) . '/';
}
if ( isset( $_GET['paged'] ) ) {
	$pv['qv']['paged'] = (int) $_GET['paged'];
}

$adiva_template = null;

if ( isset( $_GET['s'] ) ) {
	$pv['flags']['search'] = true;
	$pv['title']           = 'Search — ADiva';
	$adiva_template        = 'woocommerce/archive-product.php';
} elseif ( '/' === $adiva_uri ) {
	$pv['flags']['front'] = true;
	$pv['title']          = 'ADiva — Handcrafted in India';
	$pv['body_class']     = 'home';
	$adiva_template       = 'front-page.php';
} elseif ( preg_match( '#^/shop/?$#', $adiva_uri ) ) {
	$pv['title']      = 'Shop — ADiva';
	$pv['body_class'] = 'woocommerce archive';
	$adiva_template   = 'woocommerce/archive-product.php';
} elseif ( preg_match( '#^/product-category/([a-z0-9\-]+)/?$#', $adiva_uri, $m ) ) {
	$pv['flags']['tax']         = true;
	$pv['qv']['product_cat']    = $m[1];
	$pv['title']                = ucwords( str_replace( '-', ' ', $m[1] ) ) . ' — ADiva';
	$pv['body_class']           = 'woocommerce archive tax-product_cat';
	$adiva_template             = 'woocommerce/archive-product.php';
} elseif ( preg_match( '#^/product/([a-z0-9\-]+)/?$#', $adiva_uri, $m ) ) {
	$pv['qv']['product'] = $m[1];
	$pv['title']         = 'Product — ADiva';
	$pv['body_class']    = 'woocommerce single-product';
	$adiva_template      = 'woocommerce/single-product.php';
} else {
	$slug  = trim( $adiva_uri, '/' );
	$pages = adiva_starter_pages();
	$pages['cart']       = array( 'Your bag', '', '<p class="lead">Your bag is ready when you are. (The cart renders here once WooCommerce is active.)</p>' );
	$pages['checkout']   = array( 'Checkout', '', '<p class="lead">Checkout renders here once WooCommerce is active.</p>' );
	$pages['my-account'] = array( 'My account', '', '<p class="lead">Login and account pages render here once WooCommerce is active.</p>' );
	if ( isset( $pages[ $slug ] ) ) {
		$pv['post']  = array( 'title' => $pages[ $slug ][0], 'content' => $pages[ $slug ][2] );
		$pv['title'] = $pages[ $slug ][0] . ' — ADiva';
		$adiva_template = $pages[ $slug ][1] ? $pages[ $slug ][1] : 'page.php';
	} else {
		http_response_code( 404 );
		$pv['title']    = 'Not found — ADiva';
		$adiva_template = '404.php';
	}
}

include $adiva_root . '/' . $adiva_template;
