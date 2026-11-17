<?php
/**
 * ADiva theme bootstrap.
 *
 * Every feature lives in its own file under inc/ so it can be read (and
 * removed) in isolation. Order matters only in that helpers load first.
 *
 * @package adiva
 */

defined( 'ABSPATH' ) || exit;

define( 'ADIVA_VERSION', '1.0.0' );
define( 'ADIVA_DIR', get_template_directory() );
define( 'ADIVA_URI', get_template_directory_uri() );

$adiva_includes = array(
	'inc/helpers.php',        // Icons, ratings, small template tags.
	'inc/demo-data.php',      // Demo catalogue shown until real products exist.
	'inc/products.php',       // Normalised product layer (WooCommerce or demo).
	'inc/setup.php',          // Theme supports, menus, image sizes, assets.
	'inc/menus.php',          // Mega menu walker + default menu tree.
	'inc/customizer.php',     // Customizer settings.
	'inc/woocommerce.php',    // WooCommerce integration (only when active).
	'inc/newsletter.php',     // Footer newsletter capture.
	'inc/setup-content.php',  // Starter page copy used by the setup tool.
	'inc/admin-setup.php',    // Appearance → ADiva Setup one-click tool.
);

foreach ( $adiva_includes as $adiva_file ) {
	require_once ADIVA_DIR . '/' . $adiva_file;
}
unset( $adiva_includes, $adiva_file );
