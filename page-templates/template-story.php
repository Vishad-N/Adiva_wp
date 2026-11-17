<?php
/**
 * Template Name: Brand Story
 *
 * Editorial layout for About Us, Our Story, Sustainability and Behind the
 * Product: watermark illustration, generous measure, and styled story blocks
 * (.story-hero, .story-split, .story-quote, .process-steps) in the content.
 *
 * @package adiva
 */

defined( 'ABSPATH' ) || exit;

get_header();

while ( have_posts() ) :
	the_post();
	?>
	<article id="post-<?php the_ID(); ?>" <?php post_class( 'story' ); ?>>
		<div class="story__watermark" aria-hidden="true" style="background-image:url('<?php echo esc_url( adiva_asset( 'images/adiva-logo-illustration.webp' ) ); ?>')"></div>
		<header class="page-header page-header--story">
			<div class="container">
				<h1 class="page-title"><?php the_title(); ?></h1>
				<?php echo adiva_flourish(); // phpcs:ignore ?>
			</div>
		</header>
		<div class="container container--story">
			<div class="entry-content story__content">
				<?php the_content(); ?>
			</div>
		</div>
		<div class="container">
			<div class="story__cta">
				<a class="button" href="<?php echo esc_url( adiva_shop_url() ); ?>"><?php esc_html_e( 'Shop the collection', 'adiva' ); ?></a>
			</div>
		</div>
	</article>
	<?php
endwhile;

get_footer();
