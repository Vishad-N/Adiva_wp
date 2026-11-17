<?php
/**
 * "Welcome to ADiva" brand statement over a faded illustration watermark.
 *
 * @package adiva
 */

defined( 'ABSPATH' ) || exit;
?>
<section class="section welcome" aria-labelledby="welcome-title">
	<div class="welcome__watermark" aria-hidden="true" style="background-image:url('<?php echo esc_url( adiva_asset( 'images/adiva-logo-illustration.webp' ) ); ?>')"></div>
	<div class="container welcome__inner">
		<p class="eyebrow"><?php esc_html_e( 'Handmade in India', 'adiva' ); ?></p>
		<h2 class="welcome__title" id="welcome-title"><?php echo esc_html( adiva_mod( 'adiva_welcome_title' ) ); ?></h2>
		<?php echo adiva_flourish(); // phpcs:ignore ?>
		<p class="welcome__text"><?php echo esc_html( adiva_mod( 'adiva_welcome_text' ) ); ?></p>
		<a class="button button--outline" href="<?php echo esc_url( adiva_page_url( 'about-us' ) ); ?>"><?php esc_html_e( 'Discover our story', 'adiva' ); ?></a>
	</div>
</section>
