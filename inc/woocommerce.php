<?php
/**
 * WooCommerce integration. Loaded always; hooks only attach when WooCommerce
 * is active.
 *
 * @package adiva
 */

defined( 'ABSPATH' ) || exit;

/**
 * Sorting options in the order the brief lists them.
 *
 * @return array<string, string>
 */
function adiva_sort_options(): array {
	$options = array(
		'menu_order' => __( 'Featured', 'adiva' ),
		'popularity' => __( 'Best selling', 'adiva' ),
		'title-asc'  => __( 'Alphabetically, A–Z', 'adiva' ),
		'title-desc' => __( 'Alphabetically, Z–A', 'adiva' ),
		'price'      => __( 'Price, low to high', 'adiva' ),
		'price-desc' => __( 'Price, high to low', 'adiva' ),
		'date-asc'   => __( 'Date, old to new', 'adiva' ),
		'date'       => __( 'Date, new to old', 'adiva' ),
	);
	if ( is_search() ) {
		$options = array( 'relevance' => __( 'Most relevant', 'adiva' ) ) + $options;
	}
	return $options;
}

if ( ! class_exists( 'WooCommerce' ) ) {
	return;
}

// Our templates replace WooCommerce's wrappers, sidebar and breadcrumbs.
remove_action( 'woocommerce_before_main_content', 'woocommerce_output_content_wrapper', 10 );
remove_action( 'woocommerce_after_main_content', 'woocommerce_output_content_wrapper_end', 10 );
remove_action( 'woocommerce_before_main_content', 'woocommerce_breadcrumb', 20 );
remove_action( 'woocommerce_sidebar', 'woocommerce_get_sidebar', 10 );

/**
 * Keep WooCommerce's base form styles (cart/checkout/account) but drop its
 * float-based layout sheets, which fight our grid.
 *
 * @param array $styles Styles.
 */
function adiva_wc_styles( array $styles ): array {
	unset( $styles['woocommerce-layout'], $styles['woocommerce-smallscreen'] );
	return $styles;
}
add_filter( 'woocommerce_enqueue_styles', 'adiva_wc_styles' );

/**
 * Sort dropdown options.
 */
add_filter( 'woocommerce_catalog_orderby', static fn() => adiva_sort_options() );
add_filter( 'woocommerce_default_catalog_orderby_options', static fn() => adiva_sort_options() );

/**
 * Support the extra sort keys (A–Z, Z–A, oldest first).
 *
 * @param array $args Ordering args.
 */
function adiva_catalog_ordering_args( array $args ): array {
	// phpcs:ignore WordPress.Security.NonceVerification.Recommended
	$orderby = isset( $_GET['orderby'] ) ? wc_clean( wp_unslash( $_GET['orderby'] ) ) : '';
	switch ( $orderby ) {
		case 'title-asc':
			return array( 'orderby' => 'title', 'order' => 'ASC', 'meta_key' => '' );
		case 'title-desc':
			return array( 'orderby' => 'title', 'order' => 'DESC', 'meta_key' => '' );
		case 'date-asc':
			return array( 'orderby' => 'date ID', 'order' => 'ASC', 'meta_key' => '' );
	}
	return $args;
}
add_filter( 'woocommerce_get_catalog_ordering_args', 'adiva_catalog_ordering_args' );

/**
 * 12 per page — divides evenly into 2, 3 and 4 columns.
 */
add_filter( 'loop_shop_per_page', static fn() => (int) apply_filters( 'adiva_products_per_page', 12 ), 20 );

/**
 * Live cart count in the header.
 *
 * @param array $fragments Fragments.
 */
function adiva_cart_fragment( array $fragments ): array {
	$count = WC()->cart ? WC()->cart->get_cart_contents_count() : 0;
	$fragments['span.cart-count'] = '<span class="cart-count" data-count="' . esc_attr( (string) $count ) . '">' . esc_html( (string) $count ) . '</span>';
	return $fragments;
}
add_filter( 'woocommerce_add_to_cart_fragments', 'adiva_cart_fragment' );

/**
 * "Add to bag" wording.
 */
add_filter( 'woocommerce_product_single_add_to_cart_text', static fn() => __( 'Add to bag', 'adiva' ) );
add_filter( 'woocommerce_product_add_to_cart_text', static fn( $text, $product = null ) => ( $product && $product->is_type( 'simple' ) && $product->is_purchasable() && $product->is_in_stock() ) ? __( 'Add to bag', 'adiva' ) : $text, 10, 2 );

/**
 * BUY NOW next to ADD TO BAG: adds the item and goes straight to checkout.
 */
function adiva_buy_now_button(): void {
	global $product;
	if ( ! $product || ! $product->is_purchasable() || ! $product->is_in_stock() ) {
		return;
	}
	// Simple products submit their ID via the Add button's own name/value; when
	// Buy Now is the submitter that pair is missing, so add it as a hidden field.
	if ( $product->is_type( 'simple' ) ) {
		echo '<input type="hidden" name="add-to-cart" value="' . esc_attr( (string) $product->get_id() ) . '">';
	}
	echo '<button type="submit" name="adiva_buy_now" value="1" class="button button--outline buy-now">' . esc_html__( 'Buy now', 'adiva' ) . '</button>';
}
add_action( 'woocommerce_after_add_to_cart_button', 'adiva_buy_now_button' );

/**
 * Redirect Buy Now submissions to checkout.
 *
 * @param string $url Redirect URL.
 */
function adiva_buy_now_redirect( $url ) {
	// phpcs:ignore WordPress.Security.NonceVerification -- WooCommerce validates the add-to-cart request itself.
	if ( ! empty( $_REQUEST['adiva_buy_now'] ) ) {
		return wc_get_checkout_url();
	}
	return $url;
}
add_filter( 'woocommerce_add_to_cart_redirect', 'adiva_buy_now_redirect' );

/**
 * Product search results go to the product archive layout.
 *
 * @param WP_Query $query Query.
 */
function adiva_product_search( $query ): void {
	if ( ! is_admin() && $query->is_main_query() && $query->is_search() && empty( $_GET['post_type'] ) ) { // phpcs:ignore WordPress.Security.NonceVerification
		$query->set( 'post_type', array( 'product', 'post', 'page' ) );
	}
}
add_action( 'pre_get_posts', 'adiva_product_search' );

/**
 * Wrap cart/checkout/account pages in our container.
 */
function adiva_is_wc_utility_page(): bool {
	return is_cart() || is_checkout() || is_account_page();
}
