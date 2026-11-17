<?php
/**
 * Site header: announcement bar, sticky header with centred logo, utility
 * icons and the mega menu.
 *
 * @package adiva
 */

defined( 'ABSPATH' ) || exit;

$adiva_cart_count = ( function_exists( 'WC' ) && WC()->cart ) ? WC()->cart->get_cart_contents_count() : 0;
$adiva_account    = function_exists( 'wc_get_page_permalink' ) ? wc_get_page_permalink( 'myaccount' ) : wp_login_url();
?><!doctype html>
<html <?php language_attributes(); ?>>
<head>
	<meta charset="<?php bloginfo( 'charset' ); ?>">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<?php wp_head(); ?>
</head>
<body <?php body_class(); ?>>
<?php wp_body_open(); ?>
<a class="skip-link" href="#main"><?php esc_html_e( 'Skip to content', 'adiva' ); ?></a>

<?php get_template_part( 'template-parts/header/announcement' ); ?>

<header class="site-header" id="site-header">
	<div class="site-header__bar container">
		<div class="site-header__left">
			<button class="icon-btn menu-btn" type="button" aria-controls="mobile-drawer" aria-expanded="false" data-open="mobile-drawer">
				<?php echo adiva_icon( 'menu' ); // phpcs:ignore ?>
				<span class="screen-reader-text"><?php esc_html_e( 'Open menu', 'adiva' ); ?></span>
			</button>
			<button class="icon-btn search-btn" type="button" aria-controls="search-overlay" aria-expanded="false" data-open="search-overlay">
				<?php echo adiva_icon( 'search' ); // phpcs:ignore ?>
				<span class="search-btn__label"><?php esc_html_e( 'Search', 'adiva' ); ?></span>
			</button>
		</div>

		<div class="site-header__brand">
			<?php if ( has_custom_logo() ) : ?>
				<?php the_custom_logo(); ?>
			<?php else : ?>
				<a class="wordmark" href="<?php echo esc_url( home_url( '/' ) ); ?>" rel="home">
					<span class="wordmark__name"><span class="wordmark__a">A</span>Diva<svg class="wordmark__swoosh" viewBox="0 0 60 22" aria-hidden="true"><path d="M2 19c8-9 22-15 36-13 8 1 13 5 18 9" fill="none" stroke="currentColor" stroke-width="2.4" stroke-linecap="round"/><rect x="27" y="10" width="6" height="7" rx="1" fill="none" stroke="currentColor" stroke-width="1.6"/></svg></span>
					<span class="wordmark__tag"><?php esc_html_e( 'Made in India', 'adiva' ); ?></span>
					<span class="screen-reader-text"><?php bloginfo( 'name' ); ?></span>
				</a>
			<?php endif; ?>
		</div>

		<div class="site-header__actions">
			<a class="icon-btn account-btn" href="<?php echo esc_url( $adiva_account ); ?>">
				<?php echo adiva_icon( 'user' ); // phpcs:ignore ?>
				<span class="screen-reader-text"><?php esc_html_e( 'Account', 'adiva' ); ?></span>
			</a>
			<a class="icon-btn wishlist-btn" href="<?php echo esc_url( adiva_page_url( 'wishlist' ) ); ?>">
				<?php echo adiva_icon( 'heart' ); // phpcs:ignore ?>
				<span class="badge-count wishlist-count" data-count="0" aria-hidden="true">0</span>
				<span class="screen-reader-text"><?php esc_html_e( 'Wishlist', 'adiva' ); ?></span>
			</a>
			<a class="icon-btn cart-btn" href="<?php echo esc_url( function_exists( 'wc_get_cart_url' ) ? wc_get_cart_url() : home_url( '/cart/' ) ); ?>" data-open="cart-drawer" aria-controls="cart-drawer" aria-expanded="false">
				<?php echo adiva_icon( 'bag' ); // phpcs:ignore ?>
				<span class="badge-count"><span class="cart-count" data-count="<?php echo esc_attr( (string) $adiva_cart_count ); ?>"><?php echo esc_html( (string) $adiva_cart_count ); ?></span></span>
				<span class="screen-reader-text"><?php esc_html_e( 'Shopping bag', 'adiva' ); ?></span>
			</a>
		</div>
	</div>

	<div class="site-header__nav">
		<?php adiva_primary_nav(); ?>
	</div>
</header>

<main id="main" class="site-main" tabindex="-1">
