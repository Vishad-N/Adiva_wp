<?php
/**
 * "As seen on" infinite marquee.
 *
 * Shows publication names from the Customizer. Until real press exists the
 * strip shows a marquee of product imagery instead — we never imply coverage
 * that hasn't happened.
 *
 * @package adiva
 */

defined( 'ABSPATH' ) || exit;

$adiva_press = array_filter( array_map( 'trim', preg_split( '/\r\n|\r|\n/', (string) adiva_mod( 'adiva_press' ) ) ) );
?>
<section class="marquee-section as-seen" aria-labelledby="as-seen-title">
	<h2 class="marquee-section__label" id="as-seen-title">
		<?php echo $adiva_press ? esc_html__( 'As seen on', 'adiva' ) : esc_html__( 'Spotted on the road · #ADivaTribe', 'adiva' ); ?>
	</h2>
	<div class="marquee" data-marquee>
		<?php if ( $adiva_press ) : ?>
			<ul class="marquee__track marquee__track--press">
				<?php for ( $adiva_r = 0; $adiva_r < 2; $adiva_r++ ) : ?>
					<?php foreach ( $adiva_press as $adiva_name ) : ?>
						<li<?php echo $adiva_r ? ' aria-hidden="true"' : ''; ?>><?php echo esc_html( $adiva_name ); ?></li>
					<?php endforeach; ?>
				<?php endfor; ?>
			</ul>
		<?php else : ?>
			<?php $adiva_items = adiva_get_products( array( 'limit' => 10, 'orderby' => 'popularity' ) ); ?>
			<ul class="marquee__track marquee__track--images">
				<?php for ( $adiva_r = 0; $adiva_r < 2; $adiva_r++ ) : ?>
					<?php foreach ( $adiva_items as $adiva_p ) : ?>
						<li<?php echo $adiva_r ? ' aria-hidden="true"' : ''; ?>>
							<a href="<?php echo esc_url( $adiva_p['url'] ); ?>" style="--tint:<?php echo esc_attr( $adiva_p['bg'] ? $adiva_p['bg'] : '#F7E3C4' ); ?>"<?php echo $adiva_r ? ' tabindex="-1"' : ''; ?>>
								<img src="<?php echo esc_url( $adiva_p['image'] ); ?>" alt="<?php echo $adiva_r ? '' : esc_attr( $adiva_p['title'] ); ?>" width="120" height="150" loading="lazy" decoding="async">
							</a>
						</li>
					<?php endforeach; ?>
				<?php endfor; ?>
			</ul>
		<?php endif; ?>
	</div>
</section>
