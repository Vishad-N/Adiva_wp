<?php
/**
 * Trust badges: free shipping, secure checkout, easy returns, quality.
 *
 * @package adiva
 */

defined( 'ABSPATH' ) || exit;

$adiva_free = (int) adiva_mod( 'adiva_free_shipping' );
$adiva_ship = $adiva_free
	/* translators: %s: amount */
	? sprintf( __( 'On orders above %s', 'adiva' ), adiva_format_price( $adiva_free ) )
	: __( 'Fast, tracked delivery', 'adiva' );

$adiva_badges = array(
	array( 'truck', __( 'Free shipping', 'adiva' ), $adiva_ship ),
	array( 'lock', __( 'Secure checkout', 'adiva' ), __( 'Encrypted, trusted payments', 'adiva' ) ),
	array( 'return', __( 'Easy returns', 'adiva' ), __( '30-day return policy', 'adiva' ) ),
	array( 'award', __( 'Quality guarantee', 'adiva' ), __( 'Fair trade & hand-checked', 'adiva' ) ),
);
?>
<section class="trust" aria-label="<?php esc_attr_e( 'Why shop with us', 'adiva' ); ?>">
	<div class="container">
		<ul class="trust__list" role="list">
			<?php foreach ( $adiva_badges as $adiva_b ) : ?>
				<li class="trust__item">
					<span class="trust__icon"><?php echo adiva_icon( $adiva_b[0], 26 ); // phpcs:ignore ?></span>
					<span class="trust__text"><strong><?php echo esc_html( $adiva_b[1] ); ?></strong><span><?php echo esc_html( $adiva_b[2] ); ?></span></span>
				</li>
			<?php endforeach; ?>
		</ul>
	</div>
</section>
