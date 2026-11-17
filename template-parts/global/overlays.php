<?php
/**
 * Off-canvas mobile menu, search overlay, cart drawer and Quick View dialog.
 * All start hidden; assets/js/main.js handles open/close, focus trapping and
 * Escape-to-close.
 *
 * @package adiva
 */

defined( 'ABSPATH' ) || exit;
?>
<div class="scrim" data-scrim hidden></div>

<aside class="drawer drawer--left" id="mobile-drawer" aria-label="<?php esc_attr_e( 'Menu', 'adiva' ); ?>" role="dialog" aria-modal="true" hidden>
	<div class="drawer__head">
		<span class="drawer__title"><?php esc_html_e( 'Menu', 'adiva' ); ?></span>
		<button class="icon-btn" type="button" data-close><?php echo adiva_icon( 'close' ); // phpcs:ignore ?><span class="screen-reader-text"><?php esc_html_e( 'Close menu', 'adiva' ); ?></span></button>
	</div>
	<div class="drawer__body">
		<?php adiva_mobile_nav(); ?>
		<div class="mobile-utility">
			<a href="<?php echo esc_url( function_exists( 'wc_get_page_permalink' ) ? wc_get_page_permalink( 'myaccount' ) : wp_login_url() ); ?>"><?php echo adiva_icon( 'user', 18 ); // phpcs:ignore ?> <?php esc_html_e( 'Account / Login', 'adiva' ); ?></a>
			<a href="<?php echo esc_url( adiva_page_url( 'wishlist' ) ); ?>"><?php echo adiva_icon( 'heart', 18 ); // phpcs:ignore ?> <?php esc_html_e( 'Wishlist', 'adiva' ); ?></a>
		</div>
	</div>
</aside>

<div class="search-overlay" id="search-overlay" role="dialog" aria-modal="true" aria-label="<?php esc_attr_e( 'Search', 'adiva' ); ?>" hidden>
	<div class="search-overlay__inner container">
		<?php get_search_form(); ?>
		<button class="icon-btn" type="button" data-close><?php echo adiva_icon( 'close' ); // phpcs:ignore ?><span class="screen-reader-text"><?php esc_html_e( 'Close search', 'adiva' ); ?></span></button>
	</div>
	<div class="search-overlay__suggest container">
		<span><?php esc_html_e( 'Popular:', 'adiva' ); ?></span>
		<?php foreach ( array( 'clutch' => __( 'Clutches', 'adiva' ), 'tote' => __( 'Totes', 'adiva' ), 'dress' => __( 'Dresses', 'adiva' ), 'evil eye' => __( 'Evil Eye', 'adiva' ) ) as $adiva_q => $adiva_label ) : ?>
			<a href="<?php echo esc_url( add_query_arg( array( 's' => $adiva_q, 'post_type' => 'product' ), home_url( '/' ) ) ); ?>"><?php echo esc_html( $adiva_label ); ?></a>
		<?php endforeach; ?>
	</div>
</div>

<aside class="drawer drawer--right" id="cart-drawer" role="dialog" aria-modal="true" aria-label="<?php esc_attr_e( 'Shopping bag', 'adiva' ); ?>" hidden>
	<div class="drawer__head">
		<span class="drawer__title"><?php esc_html_e( 'Your bag', 'adiva' ); ?></span>
		<button class="icon-btn" type="button" data-close><?php echo adiva_icon( 'close' ); // phpcs:ignore ?><span class="screen-reader-text"><?php esc_html_e( 'Close bag', 'adiva' ); ?></span></button>
	</div>
	<div class="drawer__body">
		<?php if ( function_exists( 'woocommerce_mini_cart' ) ) : ?>
			<div class="widget_shopping_cart_content"><?php woocommerce_mini_cart(); ?></div>
		<?php else : ?>
			<div class="cart-empty-state">
				<?php echo adiva_icon( 'bag', 40 ); // phpcs:ignore ?>
				<p><?php esc_html_e( 'Your bag is empty.', 'adiva' ); ?></p>
				<a class="button" href="<?php echo esc_url( adiva_shop_url() ); ?>"><?php esc_html_e( 'Start shopping', 'adiva' ); ?></a>
			</div>
		<?php endif; ?>
		<?php
		$adiva_free = (int) adiva_mod( 'adiva_free_shipping' );
		if ( $adiva_free ) :
			?>
			<p class="drawer__note"><?php echo adiva_icon( 'truck', 18 ); // phpcs:ignore ?> <?php /* translators: %s: amount */ printf( esc_html__( 'Free shipping on orders over %s', 'adiva' ), esc_html( adiva_format_price( $adiva_free ) ) ); ?></p>
		<?php endif; ?>
	</div>
</aside>

<div class="modal" id="quick-view" role="dialog" aria-modal="true" aria-labelledby="qv-title" hidden>
	<div class="modal__panel">
		<button class="icon-btn modal__close" type="button" data-close><?php echo adiva_icon( 'close' ); // phpcs:ignore ?><span class="screen-reader-text"><?php esc_html_e( 'Close quick view', 'adiva' ); ?></span></button>
		<div class="qv">
			<div class="qv__media"><img class="qv__img" src="data:image/gif;base64,R0lGODlhAQABAAAAACw=" alt="" width="600" height="750"></div>
			<div class="qv__info">
				<h2 class="qv__title" id="qv-title"></h2>
				<div class="qv__rating"></div>
				<div class="qv__price"></div>
				<p class="qv__short"></p>
				<div class="qv__sizes" hidden><p class="label"><?php esc_html_e( 'Size', 'adiva' ); ?></p><div class="swatches" role="radiogroup" aria-label="<?php esc_attr_e( 'Size', 'adiva' ); ?>"></div></div>
				<div class="qv__actions">
					<button class="button qv__add" type="button"><?php esc_html_e( 'Add to bag', 'adiva' ); ?></button>
					<button class="icon-btn icon-btn--outline wish-toggle qv__wish" type="button" aria-pressed="false"><?php echo adiva_icon( 'heart' ); // phpcs:ignore ?><span class="screen-reader-text"><?php esc_html_e( 'Add to wishlist', 'adiva' ); ?></span></button>
				</div>
				<a class="link-arrow qv__link" href="#"><?php esc_html_e( 'View full details', 'adiva' ); ?> <?php echo adiva_icon( 'arrow', 16 ); // phpcs:ignore ?></a>
				<p class="qv__status" role="status"></p>
			</div>
		</div>
	</div>
</div>

<div class="toast" role="status" aria-live="polite" hidden></div>
