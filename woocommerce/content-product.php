<?php
/**
 * Product inside WooCommerce loops ([products] shortcode, blocks using the
 * classic loop, cross-sells). Renders the same ADiva card as everywhere else.
 *
 * Overrides woocommerce/templates/content-product.php.
 *
 * @package adiva
 * @version 9.4.0
 */

defined( 'ABSPATH' ) || exit;

global $product;

if ( empty( $product ) || ! $product->is_visible() ) {
	return;
}
?>
<li <?php wc_product_class( 'product-grid__item', $product ); ?>>
	<?php get_template_part( 'template-parts/product/card', null, array( 'product' => adiva_normalize_product( $product ) ) ); ?>
</li>
