<?php
/**
 * Search form — searches products when WooCommerce is active.
 *
 * @package adiva
 */

defined( 'ABSPATH' ) || exit;

$adiva_sid = wp_unique_id( 'search-' );
?>
<form role="search" method="get" class="search-form" action="<?php echo esc_url( home_url( '/' ) ); ?>">
	<label class="screen-reader-text" for="<?php echo esc_attr( $adiva_sid ); ?>"><?php esc_html_e( 'Search for:', 'adiva' ); ?></label>
	<?php echo adiva_icon( 'search', 20 ); // phpcs:ignore ?>
	<input type="search" id="<?php echo esc_attr( $adiva_sid ); ?>" class="search-form__input" placeholder="<?php esc_attr_e( 'Search bags, dresses, jewelry…', 'adiva' ); ?>" value="<?php echo esc_attr( get_search_query() ); ?>" name="s" autocomplete="off">
	<?php if ( adiva_wc_active() || adiva_using_demo() ) : ?>
		<input type="hidden" name="post_type" value="product">
	<?php endif; ?>
	<button type="submit" class="button search-form__submit"><?php esc_html_e( 'Search', 'adiva' ); ?></button>
</form>
