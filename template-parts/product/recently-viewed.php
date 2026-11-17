<?php
/**
 * "Recently viewed" — filled client-side from the visitor's own browsing
 * (localStorage), so it works with full-page caching. Stays hidden when empty.
 *
 * @package adiva
 */

defined( 'ABSPATH' ) || exit;

$adiva_exclude = isset( $args['exclude'] ) ? (int) $args['exclude'] : 0;
?>
<section class="section recently-viewed" data-recently-viewed data-exclude="<?php echo esc_attr( (string) $adiva_exclude ); ?>" aria-labelledby="recent-title" hidden>
	<div class="container">
		<?php adiva_section_heading( __( 'Recently viewed', 'adiva' ), '', '', 'recent-title' ); ?>
		<div class="carousel" data-carousel>
			<button class="carousel__arrow carousel__arrow--prev" type="button" data-prev disabled><?php echo adiva_icon( 'left', 20 ); // phpcs:ignore ?><span class="screen-reader-text"><?php esc_html_e( 'Previous products', 'adiva' ); ?></span></button>
			<ul class="carousel__track" role="list" tabindex="0" aria-label="<?php esc_attr_e( 'Recently viewed', 'adiva' ); ?>"></ul>
			<button class="carousel__arrow carousel__arrow--next" type="button" data-next><?php echo adiva_icon( 'right', 20 ); // phpcs:ignore ?><span class="screen-reader-text"><?php esc_html_e( 'Next products', 'adiva' ); ?></span></button>
			<div class="carousel__dots" data-dots></div>
		</div>
	</div>
</section>
