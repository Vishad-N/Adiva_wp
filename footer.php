<?php
/**
 * Footer: newsletter band, brand column + three link columns, legal bar,
 * then the off-canvas drawers and Quick View dialog.
 *
 * @package adiva
 */

defined( 'ABSPATH' ) || exit;

$adiva_nl_msg = adiva_newsletter_message();
$adiva_social = array_filter(
	array(
		'instagram' => adiva_mod( 'adiva_social_instagram' ),
		'facebook'  => adiva_mod( 'adiva_social_facebook' ),
		'pinterest' => adiva_mod( 'adiva_social_pinterest' ),
		'youtube'   => adiva_mod( 'adiva_social_youtube' ),
	)
);
?>
</main>

<footer class="site-footer">
	<section class="newsletter" id="newsletter" aria-labelledby="newsletter-title">
		<div class="newsletter__inner container">
			<div class="newsletter__copy">
				<p class="eyebrow"><?php esc_html_e( 'Join the tribe', 'adiva' ); ?></p>
				<h2 class="newsletter__title" id="newsletter-title"><?php esc_html_e( 'Stories, new arrivals and first dibs on sales.', 'adiva' ); ?></h2>
			</div>
			<form class="newsletter__form" method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>">
				<input type="hidden" name="action" value="adiva_newsletter">
				<?php wp_nonce_field( 'adiva_newsletter', 'adiva_nl_nonce' ); ?>
				<label class="screen-reader-text" for="nl-email"><?php esc_html_e( 'Email address', 'adiva' ); ?></label>
				<input id="nl-email" type="email" name="email" required autocomplete="email" placeholder="<?php esc_attr_e( 'Your email address', 'adiva' ); ?>">
				<input class="hp-field" type="text" name="adiva_website" tabindex="-1" autocomplete="off" aria-hidden="true">
				<button class="button" type="submit"><?php esc_html_e( 'Subscribe', 'adiva' ); ?></button>
				<?php if ( $adiva_nl_msg ) : ?>
					<p class="newsletter__msg" role="status"><?php echo esc_html( $adiva_nl_msg ); ?></p>
				<?php endif; ?>
			</form>
		</div>
	</section>

	<div class="site-footer__main container">
		<div class="footer-brand">
			<a class="wordmark wordmark--footer" href="<?php echo esc_url( home_url( '/' ) ); ?>">
				<span class="wordmark__name"><span class="wordmark__a">A</span>Diva</span>
				<span class="wordmark__tag"><?php esc_html_e( 'Made in India', 'adiva' ); ?></span>
			</a>
			<p class="footer-brand__text"><?php esc_html_e( 'Handcrafted bags, clothing and accessories for the free-spirited woman. Handmade, sustainably made, cruelty free and fair trade.', 'adiva' ); ?></p>
			<?php if ( $adiva_social ) : ?>
				<ul class="social">
					<?php foreach ( $adiva_social as $net => $url ) : ?>
						<li><a href="<?php echo esc_url( $url ); ?>" rel="noopener" target="_blank"><?php echo adiva_icon( $net, 20 ); // phpcs:ignore ?><span class="screen-reader-text"><?php echo esc_html( ucfirst( $net ) ); ?></span></a></li>
					<?php endforeach; ?>
				</ul>
			<?php endif; ?>
		</div>
		<?php
		adiva_footer_column( 'footer-collections' );
		adiva_footer_column( 'footer-company' );
		adiva_footer_column( 'footer-help' );
		?>
	</div>

	<div class="site-footer__legal">
		<div class="container site-footer__legal-inner">
			<p>&copy; <?php echo esc_html( gmdate( 'Y' ) ); ?> <?php bloginfo( 'name' ); ?>. <?php esc_html_e( 'Made with love in India.', 'adiva' ); ?></p>
			<p class="secure"><?php echo adiva_icon( 'lock', 16 ); // phpcs:ignore ?> <?php esc_html_e( 'Guaranteed secure checkout', 'adiva' ); ?></p>
			<ul class="payments" aria-label="<?php esc_attr_e( 'Accepted payment methods', 'adiva' ); ?>">
				<?php foreach ( array( 'Visa', 'Mastercard', 'Amex', 'PayPal', 'Apple Pay', 'UPI' ) as $adiva_pm ) : ?>
					<li><?php echo esc_html( $adiva_pm ); ?></li>
				<?php endforeach; ?>
			</ul>
		</div>
	</div>
</footer>

<?php get_template_part( 'template-parts/global/overlays' ); ?>

<?php wp_footer(); ?>
</body>
</html>
