<?php
/**
 * Horizontal product carousel: native scroll-snap track (so touch swipe and
 * trackpads just work), arrow buttons and pagination dots added by JS.
 *
 * $args: title, eyebrow, link, products[], id, class.
 *
 * @package adiva
 */

defined( 'ABSPATH' ) || exit;

$adiva_products = $args['products'] ?? array();
if ( ! $adiva_products ) {
	return;
}
$adiva_id = sanitize_title( $args['id'] ?? ( 'carousel-' . wp_unique_id() ) );
?>
<section class="section carousel-section <?php echo esc_attr( $args['class'] ?? '' ); ?>" aria-labelledby="<?php echo esc_attr( $adiva_id ); ?>-title">
	<div class="container">
		<?php adiva_section_heading( $args['title'] ?? '', $args['eyebrow'] ?? '', $args['link'] ?? '', $adiva_id . '-title' ); ?>
		<div class="carousel" data-carousel>
			<button class="carousel__arrow carousel__arrow--prev" type="button" data-prev disabled>
				<?php echo adiva_icon( 'left', 20 ); // phpcs:ignore ?><span class="screen-reader-text"><?php esc_html_e( 'Previous products', 'adiva' ); ?></span>
			</button>
			<ul class="carousel__track" role="list" tabindex="0" aria-label="<?php echo esc_attr( $args['title'] ?? '' ); ?>">
				<?php foreach ( $adiva_products as $adiva_i => $adiva_p ) : ?>
					<li class="carousel__slide">
						<?php get_template_part( 'template-parts/product/card', null, array( 'product' => $adiva_p, 'eager' => ! empty( $args['eager'] ) && $adiva_i < 2 ) ); ?>
					</li>
				<?php endforeach; ?>
			</ul>
			<button class="carousel__arrow carousel__arrow--next" type="button" data-next>
				<?php echo adiva_icon( 'right', 20 ); // phpcs:ignore ?><span class="screen-reader-text"><?php esc_html_e( 'Next products', 'adiva' ); ?></span>
			</button>
			<div class="carousel__dots" data-dots></div>
		</div>
	</div>
</section>
