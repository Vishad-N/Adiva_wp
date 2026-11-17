<?php
/**
 * Default page. Cart, checkout and account pages get a wider canvas.
 *
 * @package adiva
 */

defined( 'ABSPATH' ) || exit;

get_header();

$adiva_wide = function_exists( 'adiva_is_wc_utility_page' ) && adiva_is_wc_utility_page();

while ( have_posts() ) :
	the_post();
	?>
	<article id="post-<?php the_ID(); ?>" <?php post_class( 'page-article' ); ?>>
		<header class="page-header">
			<div class="container">
				<h1 class="page-title"><?php the_title(); ?></h1>
				<?php echo adiva_flourish(); // phpcs:ignore ?>
			</div>
		</header>
		<div class="container <?php echo $adiva_wide ? 'container--wc' : 'container--narrow'; ?>">
			<div class="entry-content">
				<?php
				the_content();
				wp_link_pages( array( 'before' => '<nav class="page-links">', 'after' => '</nav>' ) );
				?>
			</div>
		</div>
	</article>
	<?php
endwhile;

get_footer();
