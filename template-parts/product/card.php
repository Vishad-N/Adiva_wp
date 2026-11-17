<?php
/**
 * Product card used in carousels, grids, related products and search.
 *
 * Expects $args['product'] — a normalised product array (inc/products.php).
 * Optional $args['eager'] to skip lazy-loading for above-the-fold cards.
 *
 * @package adiva
 */

defined( 'ABSPATH' ) || exit;

$p = $args['product'] ?? null;
if ( ! $p ) {
	return;
}
$adiva_off   = adiva_discount_pct( $p );
$adiva_eager = ! empty( $args['eager'] );
$adiva_tint  = $p['bg'] ? ' style="--tint:' . esc_attr( $p['bg'] ) . '"' : '';
$adiva_hover = $p['gallery'][1] ?? '';
$adiva_hbg   = $p['gallery_bgs'][1] ?? '';
?>
<article class="card" data-product="<?php echo esc_attr( adiva_product_json( $p ) ); ?>" data-id="<?php echo esc_attr( (string) $p['id'] ); ?>">
	<div class="card__media"<?php echo $adiva_tint; // phpcs:ignore -- escaped above. ?>>
		<a class="card__img-link" href="<?php echo esc_url( $p['url'] ); ?>" tabindex="-1" aria-hidden="true">
			<img class="card__img" src="<?php echo esc_url( $p['image'] ); ?>" alt="" width="600" height="750" <?php echo $adiva_eager ? 'fetchpriority="high"' : 'loading="lazy"'; ?> decoding="async">
			<?php if ( $adiva_hover && $adiva_hover !== $p['image'] ) : ?>
				<img class="card__img card__img--alt" src="<?php echo esc_url( $adiva_hover ); ?>" alt="" width="600" height="750" loading="lazy" decoding="async">
			<?php elseif ( $adiva_hbg ) : ?>
				<span class="card__alt-tint" style="--tint2:<?php echo esc_attr( $adiva_hbg ); ?>"></span>
			<?php endif; ?>
		</a>
		<div class="card__badges">
			<?php if ( $adiva_off ) : ?>
				<span class="tag tag--sale"><?php /* translators: %d: percent off */ printf( esc_html__( '%d%% off', 'adiva' ), (int) $adiva_off ); ?></span>
			<?php elseif ( $p['badge'] ) : ?>
				<span class="tag"><?php echo esc_html( $p['badge'] ); ?></span>
			<?php endif; ?>
			<?php if ( ! $p['in_stock'] ) : ?>
				<span class="tag tag--muted"><?php esc_html_e( 'Sold out', 'adiva' ); ?></span>
			<?php endif; ?>
		</div>
		<button class="card__wish wish-toggle" type="button" aria-pressed="false" data-id="<?php echo esc_attr( (string) $p['id'] ); ?>">
			<?php echo adiva_icon( 'heart', 20 ); // phpcs:ignore ?>
			<span class="screen-reader-text"><?php /* translators: %s: product name */ printf( esc_html__( 'Add %s to wishlist', 'adiva' ), esc_html( $p['title'] ) ); ?></span>
		</button>
		<button class="card__quick" type="button" data-quick-view>
			<?php echo adiva_icon( 'eye', 16 ); // phpcs:ignore ?>
			<span><?php esc_html_e( 'Quick view', 'adiva' ); ?></span>
			<span class="screen-reader-text"><?php echo esc_html( $p['title'] ); ?></span>
		</button>
	</div>
	<div class="card__body">
		<h3 class="card__title"><a href="<?php echo esc_url( $p['url'] ); ?>"><?php echo esc_html( $p['title'] ); ?></a></h3>
		<?php echo adiva_price_markup( $p ); // phpcs:ignore -- escaped inside. ?>
		<?php echo adiva_rating( $p['rating'], $p['rating_count'], false ); // phpcs:ignore -- escaped inside. ?>
	</div>
</article>
