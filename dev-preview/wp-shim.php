<?php
/**
 * Minimal WordPress function shim — DEVELOPMENT PREVIEW ONLY.
 *
 * Lets PHP's built-in web server render the real theme templates (header,
 * front page, collection, product, pages, footer) without a WordPress
 * install, so the Playwright layout tests have something real to load.
 * WooCommerce is absent here, so the theme's demo catalogue is used.
 *
 * It implements only what the theme calls, with WordPress's semantics
 * simplified. It is never loaded by WordPress itself, and router.php refuses
 * to run outside the PHP CLI server.
 *
 * @package adiva
 */

if ( PHP_SAPI !== 'cli-server' ) {
	http_response_code( 404 );
	exit;
}

define( 'ABSPATH', __DIR__ . '/' );
define( 'DAY_IN_SECONDS', 86400 );

$GLOBALS['adiva_preview'] = array(
	'theme_dir' => dirname( __DIR__ ),
	'base'      => '', // Set by the router.
	'qv'        => array(),
	'flags'     => array(),
	'hooks'     => array(),
	'styles'    => array(),
	'scripts'   => array(),
	'l10n'      => array(),
	'post'      => null,
	'loop_done' => false,
);

function adiva_pv( string $key ) {
	return $GLOBALS['adiva_preview'][ $key ] ?? null;
}

/* ---------- Escaping, i18n, sanitising ---------- */
function esc_html( $t ) { return htmlspecialchars( (string) $t, ENT_QUOTES, 'UTF-8' ); }
function esc_attr( $t ) { return htmlspecialchars( (string) $t, ENT_QUOTES, 'UTF-8' ); }
function esc_url( $u ) { return htmlspecialchars( (string) $u, ENT_QUOTES, 'UTF-8' ); }
function esc_url_raw( $u ) { return (string) $u; }
function esc_textarea( $t ) { return esc_html( $t ); }
function __( $t, $d = null ) { return $t; }
function _x( $t, $c, $d = null ) { return $t; }
function _e( $t, $d = null ) { echo $t; }
function _n( $s, $p, $n, $d = null ) { return 1 === (int) $n ? $s : $p; }
function esc_html__( $t, $d = null ) { return esc_html( $t ); }
function esc_attr__( $t, $d = null ) { return esc_attr( $t ); }
function esc_html_e( $t, $d = null ) { echo esc_html( $t ); }
function esc_attr_e( $t, $d = null ) { echo esc_attr( $t ); }
function number_format_i18n( $n, $dec = 0 ) { return number_format( (float) $n, $dec ); }
function wp_kses_post( $h ) { return (string) $h; }
function wp_strip_all_tags( $h ) { return trim( strip_tags( (string) $h ) ); }
function wp_json_encode( $d ) { return json_encode( $d, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE ); }
function wp_parse_args( $a, $d = array() ) { return array_merge( $d, (array) $a ); }
function sanitize_title( $t ) { return trim( preg_replace( '/[^a-z0-9]+/', '-', strtolower( (string) $t ) ), '-' ); }
function sanitize_key( $k ) { return preg_replace( '/[^a-z0-9_\-]/', '', strtolower( (string) $k ) ); }
function sanitize_text_field( $t ) { return trim( strip_tags( (string) $t ) ); }
function sanitize_textarea_field( $t ) { return strip_tags( (string) $t ); }
function sanitize_email( $e ) { return filter_var( $e, FILTER_SANITIZE_EMAIL ); }
function sanitize_hex_color( $c ) { return preg_match( '/^#([A-Fa-f0-9]{3}){1,2}$/', (string) $c ) ? $c : null; }
function wp_unslash( $v ) { return $v; }
function absint( $v ) { return abs( (int) $v ); }
function is_email( $e ) { return (bool) filter_var( $e, FILTER_VALIDATE_EMAIL ); }
function wpautop( $t ) { return '<p>' . $t . '</p>'; }
function wp_trim_words( $t, $n = 55 ) { $w = preg_split( '/\s+/', trim( (string) $t ) ); return implode( ' ', array_slice( $w, 0, $n ) ) . ( count( $w ) > $n ? '…' : '' ); }
function selected( $a, $b ) { if ( (string) $a === (string) $b ) { echo ' selected="selected"'; } }
function checked( $a, $b ) { if ( (string) $a === (string) $b ) { echo ' checked="checked"'; } }
function wp_unique_id( $p = '' ) { static $i = 0; return $p . ( ++$i ); }
function is_wp_error( $t ) { return false; }
function current_user_can() { return false; }
function is_admin() { return false; }

/* ---------- Hooks ---------- */
function add_filter( $tag, $cb, $prio = 10, $args = 1 ) { $GLOBALS['adiva_preview']['hooks'][ $tag ][ $prio ][] = array( $cb, $args ); return true; }
function add_action( $tag, $cb, $prio = 10, $args = 1 ) { return add_filter( $tag, $cb, $prio, $args ); }
function remove_action() { return true; }
function remove_filter() { return true; }
function apply_filters( $tag, $value, ...$rest ) {
	$hooks = $GLOBALS['adiva_preview']['hooks'][ $tag ] ?? array();
	ksort( $hooks );
	foreach ( $hooks as $cbs ) {
		foreach ( $cbs as $h ) {
			$value = call_user_func_array( $h[0], array_slice( array_merge( array( $value ), $rest ), 0, max( 1, $h[1] ) ) );
		}
	}
	return $value;
}
function do_action( $tag, ...$args ) {
	$hooks = $GLOBALS['adiva_preview']['hooks'][ $tag ] ?? array();
	ksort( $hooks );
	foreach ( $hooks as $cbs ) {
		foreach ( $cbs as $h ) {
			call_user_func_array( $h[0], array_slice( $args, 0, $h[1] ) );
		}
	}
}

/* ---------- Theme setup no-ops ---------- */
function add_theme_support() {}
function register_nav_menus() {}
function add_image_size() {}
function load_theme_textdomain() {}
function add_editor_style() {}

/* ---------- URLs & options ---------- */
function home_url( $path = '' ) { return adiva_pv( 'base' ) . '/' . ltrim( (string) $path, '/' ); }
function admin_url( $path = '' ) { return home_url( '/wp-admin/' . ltrim( $path, '/' ) ); }
function wp_login_url() { return home_url( '/my-account/' ); }
function get_template_directory() { return adiva_pv( 'theme_dir' ); }
function get_template_directory_uri() { return adiva_pv( 'base' ); }
function get_theme_mod( $k, $d = false ) { return $d; }
function get_option( $k, $d = false ) { return $d; }
function bloginfo( $k = '' ) { echo esc_html( get_bloginfo( $k ) ); }
function get_bloginfo( $k = '' ) { return 'charset' === $k ? 'UTF-8' : 'ADiva'; }
function language_attributes() { echo 'lang="en-US"'; }
function taxonomy_exists() { return false; }
function get_term_link() { return ''; }
function get_page_by_path() { return null; }
function get_permalink() { return ''; }
function get_nav_menu_locations() { return array(); }
function has_nav_menu() { return false; }
function wp_nav_menu() {}
function get_queried_object_id() { return 0; }
function has_custom_logo() { return false; }
function the_custom_logo() {}
function shortcode_exists() { return false; }
function do_shortcode( $c ) { return $c; }
function comments_open() { return false; }
function get_comments_number() { return 0; }
function wp_nonce_field() { echo '<input type="hidden" name="_wpnonce" value="preview">'; }

function get_query_var( $k, $d = '' ) { return $GLOBALS['adiva_preview']['qv'][ $k ] ?? $d; }
function adiva_pv_flag( $k ) { return ! empty( $GLOBALS['adiva_preview']['flags'][ $k ] ); }
function is_search() { return adiva_pv_flag( 'search' ); }
function is_tax() { return adiva_pv_flag( 'tax' ); }
function is_front_page() { return adiva_pv_flag( 'front' ); }
function is_home() { return false; }
function is_archive() { return adiva_pv_flag( 'tax' ); }
function get_search_query() { return esc_attr( $_GET['s'] ?? '' ); }
function term_description() { return ''; }

function add_query_arg( ...$args ) {
	if ( is_array( $args[0] ) ) {
		$params = $args[0];
		$url    = $args[1] ?? $_SERVER['REQUEST_URI'];
	} else {
		$params = array( $args[0] => $args[1] );
		$url    = $args[2] ?? $_SERVER['REQUEST_URI'];
	}
	$parts = parse_url( $url );
	parse_str( $parts['query'] ?? '', $q );
	$q = array_merge( $q, $params );
	return ( $parts['path'] ?? '/' ) . ( $q ? '?' . http_build_query( $q ) : '' );
}
function remove_query_arg( $keys, $url = null ) {
	$url   = $url ?? $_SERVER['REQUEST_URI'];
	$parts = parse_url( $url );
	parse_str( $parts['query'] ?? '', $q );
	foreach ( (array) $keys as $k ) { unset( $q[ $k ] ); }
	return ( $parts['path'] ?? '/' ) . ( $q ? '?' . http_build_query( $q ) : '' );
}
function get_pagenum_link( $n ) {
	$parts = parse_url( $_SERVER['REQUEST_URI'] );
	$path  = preg_replace( '#page/\d+/?$#', '', $parts['path'] ?? '/' );
	$path  = rtrim( $path, '/' ) . '/' . ( $n > 1 ? 'page/' . (int) $n . '/' : '' );
	return $path . ( isset( $parts['query'] ) ? '?' . $parts['query'] : '' );
}

/* ---------- Assets ---------- */
function wp_enqueue_style( $h, $src = '', $deps = array(), $ver = false ) { $GLOBALS['adiva_preview']['styles'][ $h ] = $src . ( $ver ? ( str_contains( $src, '?' ) ? '&' : '?' ) . 'ver=' . $ver : '' ); }
function wp_enqueue_script( $h, $src = '', $deps = array(), $ver = false ) { if ( $src ) { $GLOBALS['adiva_preview']['scripts'][ $h ] = $src . ( $ver ? '?ver=' . $ver : '' ); } }
function wp_localize_script( $h, $name, $data ) { $GLOBALS['adiva_preview']['l10n'][ $h ] = array( $name, $data ); }
function wp_head() {
	do_action( 'wp_enqueue_scripts' );
	echo '<title>' . esc_html( $GLOBALS['adiva_preview']['title'] ?? 'ADiva' ) . "</title>\n";
	if ( getenv( 'ADIVA_OFFLINE_FONTS' ) ) {
		unset( $GLOBALS['adiva_preview']['styles']['adiva-fonts'] );
	} else {
		echo "<link rel=\"preconnect\" href=\"https://fonts.googleapis.com\">\n<link rel=\"preconnect\" href=\"https://fonts.gstatic.com\" crossorigin>\n";
	}
	foreach ( $GLOBALS['adiva_preview']['styles'] as $h => $src ) {
		echo '<link rel="stylesheet" id="' . esc_attr( $h ) . '-css" href="' . esc_url( $src ) . "\">\n";
	}
	do_action( 'wp_head' );
}
function wp_body_open() { do_action( 'wp_body_open' ); }
function wp_footer() {
	foreach ( $GLOBALS['adiva_preview']['scripts'] as $h => $src ) {
		if ( isset( $GLOBALS['adiva_preview']['l10n'][ $h ] ) ) {
			list( $name, $data ) = $GLOBALS['adiva_preview']['l10n'][ $h ];
			echo '<script>var ' . $name . ' = ' . wp_json_encode( $data ) . ";</script>\n";
		}
		echo '<script defer src="' . esc_url( $src ) . "\"></script>\n";
	}
	do_action( 'wp_footer' );
}
function body_class( $extra = '' ) {
	$classes = apply_filters( 'body_class', array_filter( array( 'adiva-preview', $GLOBALS['adiva_preview']['body_class'] ?? '' ) ) );
	echo 'class="' . esc_attr( implode( ' ', $classes ) ) . '"';
}
function post_class( $c = '' ) { echo 'class="' . esc_attr( 'page type-page ' . $c ) . '"'; }

/* ---------- Templates ---------- */
function get_template_part( $slug, $name = null, $args = array() ) {
	$file = adiva_pv( 'theme_dir' ) . '/' . $slug . ( $name ? '-' . $name : '' ) . '.php';
	if ( is_file( $file ) ) {
		( static function () use ( $file, $args ) { include $file; } )();
	}
}
function get_header() { include adiva_pv( 'theme_dir' ) . '/header.php'; }
function get_footer() { include adiva_pv( 'theme_dir' ) . '/footer.php'; }
function get_search_form() { include adiva_pv( 'theme_dir' ) . '/searchform.php'; }

/* ---------- One-post loop for pages ---------- */
function have_posts() { return null !== adiva_pv( 'post' ) && ! adiva_pv( 'loop_done' ); }
function the_post() { $GLOBALS['adiva_preview']['loop_done'] = true; }
function wp_reset_postdata() {}
function the_ID() { echo 1; }
function get_the_ID() { return 1; }
function the_title() { echo esc_html( adiva_pv( 'post' )['title'] ?? '' ); }
function single_post_title() { the_title(); }
function the_content() { echo adiva_pv( 'post' )['content'] ?? ''; }
function wp_link_pages() {}
function the_archive_title() {}
function the_archive_description() {}
function the_posts_pagination() {}
function post_password_required() { return false; }
