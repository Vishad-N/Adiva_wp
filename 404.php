<?php
/**
 * 404.
 *
 * @package adiva
 */

defined( 'ABSPATH' ) || exit;

get_header();
?>
<section class="section error-404">
	<div class="container container--narrow">
		<img class="error-404__art" src="<?php echo esc_url( adiva_asset( 'images/bag-outline.svg' ) ); ?>" alt="" width="160" height="160">
		<h1 class="page-title"><?php esc_html_e( 'This page wandered off.', 'adiva' ); ?></h1>
		<?php echo adiva_flourish(); // phpcs:ignore ?>
		<p><?php esc_html_e( 'Like any good traveller, it may turn up somewhere unexpected. Try a search, or start again from the shop.', 'adiva' ); ?></p>
		<?php get_search_form(); ?>
		<p><a class="button" href="<?php echo esc_url( adiva_shop_url() ); ?>"><?php esc_html_e( 'Back to the shop', 'adiva' ); ?></a></p>
	</div>
</section>
<?php
get_footer();
