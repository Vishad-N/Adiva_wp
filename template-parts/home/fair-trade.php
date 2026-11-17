<?php
/**
 * Fair trade & ethically made band.
 *
 * @package adiva
 */

defined( 'ABSPATH' ) || exit;
?>
<section class="fairtrade" aria-labelledby="fairtrade-title">
	<div class="container fairtrade__inner">
		<img class="fairtrade__art" src="<?php echo esc_url( adiva_asset( 'images/bag-outline.svg' ) ); ?>" alt="" width="180" height="180" loading="lazy">
		<div class="fairtrade__copy">
			<h2 class="fairtrade__title" id="fairtrade-title"><?php esc_html_e( 'Fair trade & ethically made', 'adiva' ); ?></h2>
			<p><?php echo esc_html( adiva_mod( 'adiva_fairtrade_text' ) ); ?></p>
		</div>
		<a class="button button--light" href="<?php echo esc_url( adiva_page_url( 'sustainability' ) ); ?>"><?php esc_html_e( 'Learn more', 'adiva' ); ?></a>
	</div>
</section>
