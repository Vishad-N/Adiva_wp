<?php
/**
 * "Our promise to you" — continuously scrolling pillars.
 *
 * @package adiva
 */

defined( 'ABSPATH' ) || exit;

$adiva_pillars = array(
	array( 'hand', __( 'Hand made', 'adiva' ) ),
	array( 'leaf', __( 'Sustainably made', 'adiva' ) ),
	array( 'rabbit', __( 'Cruelty free', 'adiva' ) ),
	array( 'globe', __( 'Fair trade', 'adiva' ) ),
	array( 'award', __( 'Ethically made', 'adiva' ) ),
);
?>
<section class="marquee-section promise" aria-labelledby="promise-title">
	<h2 class="marquee-section__label" id="promise-title"><?php esc_html_e( 'Our promise to you', 'adiva' ); ?></h2>
	<div class="marquee marquee--slow" data-marquee>
		<ul class="marquee__track marquee__track--promise">
			<?php for ( $adiva_r = 0; $adiva_r < 4; $adiva_r++ ) : ?>
				<?php foreach ( $adiva_pillars as $adiva_pl ) : ?>
					<li<?php echo $adiva_r ? ' aria-hidden="true"' : ''; ?>>
						<?php echo adiva_icon( $adiva_pl[0], 26 ); // phpcs:ignore ?>
						<span><?php echo esc_html( $adiva_pl[1] ); ?></span>
						<span class="promise__dot" aria-hidden="true">◆</span>
					</li>
				<?php endforeach; ?>
			<?php endfor; ?>
		</ul>
	</div>
</section>
