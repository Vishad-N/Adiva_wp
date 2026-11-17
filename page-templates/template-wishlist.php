<?php
/**
 * Template Name: Wishlist
 *
 * Built-in wishlist: saved items live in the visitor's browser (no account
 * needed) and are rendered client-side from the same data the product cards
 * carry. If YITH WooCommerce Wishlist is active, its shortcode is used instead.
 *
 * @package adiva
 */

defined( 'ABSPATH' ) || exit;

get_header();
?>
<header class="page-header">
	<div class="container">
		<h1 class="page-title"><?php esc_html_e( 'Your wishlist', 'adiva' ); ?></h1>
		<?php echo adiva_flourish(); // phpcs:ignore ?>
	</div>
</header>
<div class="container">
	<?php if ( shortcode_exists( 'yith_wcwl_wishlist' ) ) : ?>
		<?php echo do_shortcode( '[yith_wcwl_wishlist]' ); ?>
	<?php else : ?>
		<div class="product-grid" data-wishlist-grid></div>
		<div class="empty-state" data-wishlist-empty hidden>
			<?php echo adiva_icon( 'heart', 40 ); // phpcs:ignore ?>
			<p><?php esc_html_e( 'Your wishlist is empty — tap the heart on anything you love.', 'adiva' ); ?></p>
			<a class="button" href="<?php echo esc_url( adiva_shop_url() ); ?>"><?php esc_html_e( 'Start exploring', 'adiva' ); ?></a>
		</div>
	<?php endif; ?>
</div>
<?php
get_footer();
