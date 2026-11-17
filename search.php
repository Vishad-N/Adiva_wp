<?php
/**
 * General search results. Product-only searches (post_type=product) are
 * routed by WooCommerce to woocommerce/archive-product.php instead.
 *
 * @package adiva
 */

defined( 'ABSPATH' ) || exit;

get_header();
?>
<header class="page-header">
	<div class="container">
		<h1 class="page-title"><?php /* translators: %s: search query */ printf( esc_html__( 'Results for “%s”', 'adiva' ), esc_html( get_search_query() ) ); ?></h1>
		<?php echo adiva_flourish(); // phpcs:ignore ?>
	</div>
</header>
<div class="container">
	<?php if ( have_posts() ) : ?>
		<div class="product-grid">
			<?php
			while ( have_posts() ) :
				the_post();
				if ( 'product' === get_post_type() && function_exists( 'wc_get_product' ) ) {
					$adiva_product = wc_get_product( get_the_ID() );
					if ( $adiva_product ) {
						get_template_part( 'template-parts/product/card', null, array( 'product' => adiva_normalize_product( $adiva_product ) ) );
					}
				} else {
					get_template_part( 'template-parts/global/post-card' );
				}
			endwhile;
			?>
		</div>
		<?php the_posts_pagination( array( 'class' => 'pagination' ) ); ?>
	<?php else : ?>
		<div class="empty-state">
			<p><?php esc_html_e( 'No matches yet. Try another word — or browse the newest pieces.', 'adiva' ); ?></p>
			<?php get_search_form(); ?>
		</div>
	<?php endif; ?>
</div>
<?php
get_footer();
