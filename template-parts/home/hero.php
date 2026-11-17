<?php
/**
 * Homepage hero. A large, faded copy of the ADiva illustration sits behind
 * the content as a watermark; the full-colour illustration anchors the right.
 *
 * @package adiva
 */

defined( 'ABSPATH' ) || exit;

$adiva_logo_art = adiva_asset( 'images/adiva-logo-illustration.webp' );
?>
<section class="hero" aria-labelledby="hero-title">
	<div class="hero__watermark" aria-hidden="true" style="background-image:url('<?php echo esc_url( $adiva_logo_art ); ?>')"></div>
	<img class="hero__bag-art" src="<?php echo esc_url( adiva_asset( 'images/bag-outline.svg' ) ); ?>" alt="" width="220" height="220" aria-hidden="true">
	<div class="hero__inner container">
		<div class="hero__copy">
			<p class="eyebrow"><?php echo esc_html( adiva_mod( 'adiva_hero_eyebrow' ) ); ?></p>
			<h1 class="hero__title" id="hero-title"><?php echo esc_html( adiva_mod( 'adiva_hero_title' ) ); ?></h1>
			<?php echo adiva_flourish( 'hero__flourish' ); // phpcs:ignore ?>
			<p class="hero__text"><?php echo esc_html( adiva_mod( 'adiva_hero_text' ) ); ?></p>
			<div class="hero__ctas">
				<a class="button" href="<?php echo esc_url( adiva_category_url( 'new' ) ); ?>"><?php esc_html_e( 'Shop new arrivals', 'adiva' ); ?></a>
				<a class="button button--outline" href="<?php echo esc_url( adiva_page_url( 'our-story' ) ); ?>"><?php esc_html_e( 'Our story', 'adiva' ); ?></a>
			</div>
			<ul class="hero__pillars">
				<li><?php echo adiva_icon( 'hand', 18 ); // phpcs:ignore ?> <?php esc_html_e( 'Handmade', 'adiva' ); ?></li>
				<li><?php echo adiva_icon( 'leaf', 18 ); // phpcs:ignore ?> <?php esc_html_e( 'Sustainable', 'adiva' ); ?></li>
				<li><?php echo adiva_icon( 'globe', 18 ); // phpcs:ignore ?> <?php esc_html_e( 'Fair trade', 'adiva' ); ?></li>
			</ul>
		</div>
		<div class="hero__art">
			<img src="<?php echo esc_url( $adiva_logo_art ); ?>" alt="<?php esc_attr_e( 'Illustration of a woman in a red dress carrying a woven round bag and shopping bags — the ADiva emblem', 'adiva' ); ?>" width="1254" height="1254" fetchpriority="high" decoding="async">
		</div>
	</div>
</section>
