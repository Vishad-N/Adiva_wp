<?php
/**
 * Single product: gallery with zoom + thumbnails, info column (title,
 * wishlist, rating, price, options, ADD TO BAG + BUY NOW, accordions), then
 * promise strip, trust badges, You May Also Like and Recently viewed.
 *
 * Overrides woocommerce/templates/single-product.php.
 *
 * @package adiva
 * @version 1.6.4
 */

defined( 'ABSPATH' ) || exit;

get_header();

$adiva_wc = ! adiva_using_demo() && function_exists( 'wc_get_product' );

if ( $adiva_wc ) {
	the_post();
	// WooCommerce's add-to-cart templates read the global.
	$GLOBALS['product'] = wc_get_product( get_the_ID() );
}

$p = adiva_current_product();

if ( ! $p ) :
	?>
	<section class="section"><div class="container empty-state"><p><?php esc_html_e( 'Sorry, this piece could not be found.', 'adiva' ); ?></p><a class="button" href="<?php echo esc_url( adiva_shop_url() ); ?>"><?php esc_html_e( 'Back to the shop', 'adiva' ); ?></a></div></section>
	<?php
	get_footer();
	return;
endif;

$adiva_shipping = array_filter( array_map( 'trim', preg_split( '/\r\n|\r|\n/', (string) adiva_mod( 'adiva_shipping_text' ) ) ) );
$adiva_return   = trim( (string) adiva_mod( 'adiva_return_address' ) );
$adiva_free     = (int) adiva_mod( 'adiva_free_shipping' );
$adiva_cat      = $p['categories'][0] ?? '';
$adiva_reviews  = $adiva_wc && comments_open() && 'yes' === get_option( 'woocommerce_enable_reviews', 'yes' );

if ( $adiva_wc ) {
	echo '<div class="container">';
	do_action( 'woocommerce_before_single_product' );
	echo '</div>';
	if ( post_password_required() ) {
		echo get_the_password_form(); // phpcs:ignore WordPress.Security.EscapeOutput
		get_footer();
		return;
	}
}
?>
<div id="product-<?php echo esc_attr( (string) $p['id'] ); ?>" <?php echo $adiva_wc ? wc_product_class( 'product-page', $p['id'] ) : 'class="product-page product"'; // phpcs:ignore ?> data-track-recent="<?php echo esc_attr( adiva_product_json( $p ) ); ?>">
	<div class="container">
		<?php if ( $adiva_wc && function_exists( 'woocommerce_breadcrumb' ) ) : ?>
			<?php woocommerce_breadcrumb( array( 'wrap_before' => '<nav class="breadcrumb" aria-label="' . esc_attr__( 'Breadcrumb', 'adiva' ) . '">', 'wrap_after' => '</nav>', 'delimiter' => '<span aria-hidden="true"> / </span>' ) ); ?>
		<?php else : ?>
			<nav class="breadcrumb" aria-label="<?php esc_attr_e( 'Breadcrumb', 'adiva' ); ?>">
				<a href="<?php echo esc_url( home_url( '/' ) ); ?>"><?php esc_html_e( 'Home', 'adiva' ); ?></a> <span aria-hidden="true">/</span>
				<?php if ( $adiva_cat ) : ?>
					<a href="<?php echo esc_url( adiva_category_url( $adiva_cat ) ); ?>"><?php echo esc_html( ucwords( str_replace( '-', ' ', $adiva_cat ) ) ); ?></a> <span aria-hidden="true">/</span>
				<?php endif; ?>
				<span aria-current="page"><?php echo esc_html( $p['title'] ); ?></span>
			</nav>
		<?php endif; ?>

		<div class="product-layout">
			<div class="gallery" data-gallery>
				<div class="gallery__main" data-zoom style="--tint:<?php echo esc_attr( $p['gallery_bgs'][0] ?? $p['bg'] ); ?>">
					<img class="gallery__img" src="<?php echo esc_url( $p['gallery'][0] ); ?>" alt="<?php echo esc_attr( $p['image_alt'] ); ?>" width="1000" height="1250" fetchpriority="high" decoding="async">
					<span class="gallery__hint" aria-hidden="true"><?php echo adiva_icon( 'search', 14 ); // phpcs:ignore ?> <?php esc_html_e( 'Hover or tap to zoom', 'adiva' ); ?></span>
				</div>
				<?php if ( count( $p['gallery'] ) > 1 ) : ?>
					<ul class="gallery__thumbs" role="list" aria-label="<?php esc_attr_e( 'Product images', 'adiva' ); ?>">
						<?php foreach ( $p['gallery'] as $adiva_gi => $adiva_src ) : ?>
							<li>
								<button type="button" class="gallery__thumb<?php echo 0 === $adiva_gi ? ' is-active' : ''; ?>" data-src="<?php echo esc_url( $adiva_src ); ?>" data-tint="<?php echo esc_attr( $p['gallery_bgs'][ $adiva_gi ] ?? '' ); ?>" aria-pressed="<?php echo 0 === $adiva_gi ? 'true' : 'false'; ?>" style="--tint:<?php echo esc_attr( $p['gallery_bgs'][ $adiva_gi ] ?? $p['bg'] ); ?>">
									<img src="<?php echo esc_url( $adiva_src ); ?>" alt="" width="120" height="150" loading="lazy" decoding="async">
									<span class="screen-reader-text"><?php /* translators: %d: image number */ printf( esc_html__( 'Show image %d', 'adiva' ), (int) $adiva_gi + 1 ); ?></span>
								</button>
							</li>
						<?php endforeach; ?>
					</ul>
				<?php endif; ?>
			</div>

			<div class="product-info">
				<div class="product-info__head">
					<h1 class="product-title"><?php echo esc_html( $p['title'] ); ?></h1>
					<button class="icon-btn icon-btn--outline wish-toggle" type="button" aria-pressed="false" data-id="<?php echo esc_attr( (string) $p['id'] ); ?>">
						<?php echo adiva_icon( 'heart' ); // phpcs:ignore ?><span class="screen-reader-text"><?php esc_html_e( 'Add to wishlist', 'adiva' ); ?></span>
					</button>
				</div>
				<?php if ( $p['rating'] > 0 ) : ?>
					<a class="product-rating" href="#product-reviews"><?php echo adiva_rating( $p['rating'], $p['rating_count'] ); // phpcs:ignore ?></a>
				<?php endif; ?>
				<div class="product-price">
					<?php echo adiva_price_markup( $p ); // phpcs:ignore ?>
					<?php $adiva_off = adiva_discount_pct( $p ); if ( $adiva_off ) : ?>
						<span class="tag tag--sale"><?php /* translators: %d: percent */ printf( esc_html__( 'Save %d%%', 'adiva' ), (int) $adiva_off ); ?></span>
					<?php endif; ?>
				</div>
				<?php if ( $p['short'] ) : ?>
					<p class="product-short"><?php echo esc_html( $p['short'] ); ?></p>
				<?php endif; ?>

				<div class="product-buy">
					<?php if ( $adiva_wc && function_exists( 'woocommerce_template_single_add_to_cart' ) ) : ?>
						<?php woocommerce_template_single_add_to_cart(); ?>
					<?php else : ?>
						<form class="cart demo-cart" data-demo-cart action="#" method="post">
							<?php if ( $p['sizes'] ) : ?>
								<fieldset class="swatch-field">
									<legend class="label"><?php esc_html_e( 'Size', 'adiva' ); ?></legend>
									<div class="swatches">
										<?php foreach ( $p['sizes'] as $adiva_si => $adiva_size ) : ?>
											<label class="swatch"><input type="radio" name="size" value="<?php echo esc_attr( $adiva_size ); ?>" <?php checked( 0, $adiva_si ); ?>><span><?php echo esc_html( $adiva_size ); ?></span></label>
										<?php endforeach; ?>
									</div>
								</fieldset>
							<?php endif; ?>
							<div class="buy-row">
								<div class="quantity">
									<label class="screen-reader-text" for="qty"><?php esc_html_e( 'Quantity', 'adiva' ); ?></label>
									<input class="qty" id="qty" type="number" name="quantity" value="1" min="1" max="10" inputmode="numeric">
								</div>
								<button type="submit" class="button single_add_to_cart_button"><?php esc_html_e( 'Add to bag', 'adiva' ); ?></button>
							</div>
							<button type="submit" class="button button--outline buy-now" data-buy-now><?php esc_html_e( 'Buy now', 'adiva' ); ?></button>
						</form>
					<?php endif; ?>
				</div>

				<ul class="mini-promise" role="list">
					<li><?php echo adiva_icon( 'hand', 18 ); // phpcs:ignore ?> <?php esc_html_e( 'Handmade', 'adiva' ); ?></li>
					<li><?php echo adiva_icon( 'leaf', 18 ); // phpcs:ignore ?> <?php esc_html_e( 'Sustainably made', 'adiva' ); ?></li>
					<li><?php echo adiva_icon( 'rabbit', 18 ); // phpcs:ignore ?> <?php esc_html_e( 'Cruelty free', 'adiva' ); ?></li>
				</ul>

				<div class="accordion" data-accordion>
					<div class="accordion__item">
						<h2 class="accordion__heading"><button type="button" aria-expanded="true" aria-controls="acc-desc" id="acc-desc-btn"><?php esc_html_e( 'Description', 'adiva' ); ?><?php echo adiva_icon( 'plus', 16 ); // phpcs:ignore ?></button></h2>
						<div class="accordion__panel entry-content" id="acc-desc" role="region" aria-labelledby="acc-desc-btn">
							<?php echo wp_kses_post( $p['description'] ? $p['description'] : wpautop( $p['short'] ) ); ?>
						</div>
					</div>
					<div class="accordion__item">
						<h2 class="accordion__heading"><button type="button" aria-expanded="false" aria-controls="acc-ship" id="acc-ship-btn"><?php esc_html_e( 'Shipping & returns', 'adiva' ); ?><?php echo adiva_icon( 'plus', 16 ); // phpcs:ignore ?></button></h2>
						<div class="accordion__panel entry-content" id="acc-ship" role="region" aria-labelledby="acc-ship-btn" hidden>
							<?php foreach ( $adiva_shipping as $adiva_line ) : ?>
								<p><?php echo esc_html( $adiva_line ); ?></p>
							<?php endforeach; ?>
							<?php if ( $adiva_return ) : ?>
								<p><strong><?php esc_html_e( 'Returns address', 'adiva' ); ?></strong><br><?php echo nl2br( esc_html( $adiva_return ) ); ?></p>
							<?php endif; ?>
							<p><a class="link-underline" href="<?php echo esc_url( adiva_page_url( 'return-policy' ) ); ?>"><?php esc_html_e( 'Read the full return policy', 'adiva' ); ?></a></p>
						</div>
					</div>
					<?php if ( $adiva_reviews ) : ?>
						<div class="accordion__item" id="product-reviews">
							<h2 class="accordion__heading"><button type="button" aria-expanded="false" aria-controls="acc-rev" id="acc-rev-btn"><?php /* translators: %d: reviews */ printf( esc_html__( 'Reviews (%d)', 'adiva' ), (int) $p['rating_count'] ); ?><?php echo adiva_icon( 'plus', 16 ); // phpcs:ignore ?></button></h2>
							<div class="accordion__panel" id="acc-rev" role="region" aria-labelledby="acc-rev-btn" hidden>
								<?php comments_template(); ?>
							</div>
						</div>
					<?php endif; ?>
				</div>
			</div>
		</div>
	</div>

	<section class="section promise-strip" aria-labelledby="promise-strip-title">
		<div class="container">
			<h2 class="promise-strip__title" id="promise-strip-title"><?php esc_html_e( 'Our promise to you', 'adiva' ); ?></h2>
			<ul class="promise-strip__list" role="list">
				<li><?php echo adiva_icon( 'hand', 30 ); // phpcs:ignore ?><strong><?php esc_html_e( 'Handmade', 'adiva' ); ?></strong><span><?php esc_html_e( 'Made by artisan hands in India — never mass produced.', 'adiva' ); ?></span></li>
				<li><?php echo adiva_icon( 'leaf', 30 ); // phpcs:ignore ?><strong><?php esc_html_e( 'Sustainably made', 'adiva' ); ?></strong><span><?php esc_html_e( 'Small batches, natural fibres and low waste.', 'adiva' ); ?></span></li>
				<li><?php echo adiva_icon( 'globe', 30 ); // phpcs:ignore ?><strong><?php esc_html_e( 'Fair trade', 'adiva' ); ?></strong><span><?php esc_html_e( 'Fair wages and long-term artisan partnerships.', 'adiva' ); ?></span></li>
			</ul>
		</div>
	</section>

	<?php get_template_part( 'template-parts/product/trust-badges' ); ?>

	<?php
	get_template_part(
		'template-parts/product/carousel',
		null,
		array(
			'id'       => 'you-may-also-like',
			'title'    => __( 'You may also like', 'adiva' ),
			'products' => adiva_related_products( $p, 10 ),
		)
	);
	get_template_part( 'template-parts/product/recently-viewed', null, array( 'exclude' => $p['id'] ) );
	?>
</div>
<?php
if ( $adiva_wc ) {
	if ( isset( WC()->structured_data ) ) {
		WC()->structured_data->generate_product_data();
	}
	do_action( 'woocommerce_after_single_product' );
}

get_footer();
