<?php
/**
 * Shop bags / clothing / jewelry tiles.
 *
 * @package adiva
 */

defined( 'ABSPATH' ) || exit;

$adiva_tiles = array(
	array( 'handbags', __( 'Shop bags', 'adiva' ), __( 'Totes, clutches & beach bags', 'adiva' ), 'tote', '#F6D9A8' ),
	array( 'clothing', __( 'Shop clothing', 'adiva' ), __( 'Dresses, kaftans & co-ords', 'adiva' ), 'dress', '#F6C9C4' ),
	array( 'jewelry', __( 'Shop jewelry', 'adiva' ), __( 'Jhumkas, charms & straps', 'adiva' ), 'earrings', '#E8DDF0' ),
);
?>
<section class="section shop-cta" aria-labelledby="shop-cta-title">
	<div class="container">
		<h2 class="screen-reader-text" id="shop-cta-title"><?php esc_html_e( 'Shop by category', 'adiva' ); ?></h2>
		<ul class="tiles" role="list">
			<?php foreach ( $adiva_tiles as $adiva_t ) : ?>
				<li>
					<a class="tile" href="<?php echo esc_url( adiva_category_url( $adiva_t[0] ) ); ?>" style="--tint:<?php echo esc_attr( $adiva_t[4] ); ?>">
						<img class="tile__img" src="<?php echo esc_url( adiva_asset( 'images/demo/' . $adiva_t[3] . '.svg' ) ); ?>" alt="" width="400" height="500" loading="lazy" decoding="async">
						<span class="tile__text">
							<span class="tile__title"><?php echo esc_html( $adiva_t[1] ); ?></span>
							<span class="tile__sub"><?php echo esc_html( $adiva_t[2] ); ?></span>
						</span>
						<span class="tile__arrow" aria-hidden="true"><?php echo adiva_icon( 'arrow', 20 ); // phpcs:ignore ?></span>
					</a>
				</li>
			<?php endforeach; ?>
		</ul>
	</div>
</section>
