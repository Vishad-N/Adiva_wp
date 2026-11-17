<?php
/**
 * Comments for journal posts (product reviews use WooCommerce's template).
 *
 * @package adiva
 */

defined( 'ABSPATH' ) || exit;

if ( post_password_required() ) {
	return;
}
?>
<section id="comments" class="comments">
	<?php if ( have_comments() ) : ?>
		<h2 class="comments__title">
			<?php /* translators: %d: comment count */ printf( esc_html( _n( '%d comment', '%d comments', get_comments_number(), 'adiva' ) ), (int) get_comments_number() ); ?>
		</h2>
		<ol class="comments__list">
			<?php wp_list_comments( array( 'style' => 'ol', 'short_ping' => true, 'avatar_size' => 40 ) ); ?>
		</ol>
		<?php the_comments_navigation(); ?>
	<?php endif; ?>
	<?php comment_form(); ?>
</section>
