<?php
/**
 * Fallback template: journal / blog listing.
 *
 * @package adiva
 */

defined( 'ABSPATH' ) || exit;

get_header();
?>
<header class="page-header">
	<div class="container">
		<h1 class="page-title">
			<?php
			if ( is_home() && ! is_front_page() ) {
				single_post_title();
			} elseif ( is_archive() ) {
				the_archive_title();
			} else {
				esc_html_e( 'The Journal', 'adiva' );
			}
			?>
		</h1>
		<?php echo adiva_flourish(); // phpcs:ignore ?>
		<?php the_archive_description( '<div class="archive-description">', '</div>' ); ?>
	</div>
</header>
<div class="container">
	<?php if ( have_posts() ) : ?>
		<div class="post-grid">
			<?php
			while ( have_posts() ) :
				the_post();
				get_template_part( 'template-parts/global/post-card' );
			endwhile;
			?>
		</div>
		<?php the_posts_pagination( array( 'class' => 'pagination', 'prev_text' => __( 'Previous', 'adiva' ), 'next_text' => __( 'Next', 'adiva' ) ) ); ?>
	<?php else : ?>
		<div class="empty-state"><p><?php esc_html_e( 'Nothing here yet — new stories are on their way.', 'adiva' ); ?></p></div>
	<?php endif; ?>
</div>
<?php
get_footer();
