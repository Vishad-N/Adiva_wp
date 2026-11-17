<?php
/**
 * Journal post card.
 *
 * @package adiva
 */

defined( 'ABSPATH' ) || exit;
?>
<article id="post-<?php the_ID(); ?>" <?php post_class( 'post-card' ); ?>>
	<a class="post-card__media" href="<?php the_permalink(); ?>" tabindex="-1" aria-hidden="true">
		<?php if ( has_post_thumbnail() ) : ?>
			<?php the_post_thumbnail( 'medium_large', array( 'loading' => 'lazy' ) ); ?>
		<?php else : ?>
			<img src="<?php echo esc_url( adiva_asset( 'images/bag-outline.svg' ) ); ?>" alt="" width="200" height="200" loading="lazy">
		<?php endif; ?>
	</a>
	<div class="post-card__body">
		<p class="eyebrow"><?php echo esc_html( get_the_date() ); ?></p>
		<h2 class="post-card__title"><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h2>
		<p><?php echo esc_html( wp_trim_words( get_the_excerpt(), 22 ) ); ?></p>
	</div>
</article>
