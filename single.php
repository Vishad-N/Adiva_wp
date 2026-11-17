<?php
/**
 * Single journal post.
 *
 * @package adiva
 */

defined( 'ABSPATH' ) || exit;

get_header();

while ( have_posts() ) :
	the_post();
	?>
	<article id="post-<?php the_ID(); ?>" <?php post_class( 'single-post' ); ?>>
		<header class="page-header">
			<div class="container container--narrow">
				<p class="eyebrow"><?php echo esc_html( get_the_date() ); ?></p>
				<h1 class="page-title"><?php the_title(); ?></h1>
				<?php echo adiva_flourish(); // phpcs:ignore ?>
			</div>
		</header>
		<?php if ( has_post_thumbnail() ) : ?>
			<div class="container container--narrow single-post__media"><?php the_post_thumbnail( 'large' ); ?></div>
		<?php endif; ?>
		<div class="container container--narrow">
			<div class="entry-content"><?php the_content(); ?></div>
			<?php
			the_post_navigation(
				array(
					'prev_text' => '<span class="eyebrow">' . esc_html__( 'Previous', 'adiva' ) . '</span> %title',
					'next_text' => '<span class="eyebrow">' . esc_html__( 'Next', 'adiva' ) . '</span> %title',
				)
			);
			if ( comments_open() || get_comments_number() ) {
				comments_template();
			}
			?>
		</div>
	</article>
	<?php
endwhile;

get_footer();
