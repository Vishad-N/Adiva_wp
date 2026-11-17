<?php
/**
 * Small, dependency-free template helpers: icons, ratings, prices, headings.
 *
 * @package adiva
 */

defined( 'ABSPATH' ) || exit;

/**
 * Inline SVG icon. Icons inherit currentColor and are hidden from assistive
 * tech — every icon-only control must carry its own aria-label.
 *
 * @param string $name Icon key.
 * @param int    $size Pixel size.
 */
function adiva_icon( string $name, int $size = 22 ): string {
	static $paths = array(
		'search'   => '<circle cx="11" cy="11" r="7"/><path d="m20 20-3.6-3.6"/>',
		'user'     => '<circle cx="12" cy="8" r="4"/><path d="M4 21c1.6-4 4.5-6 8-6s6.4 2 8 6"/>',
		'heart'    => '<path d="M12 20s-7.5-4.6-9.2-9.3C1.7 7.6 3.6 4.5 6.9 4.5c2 0 3.5 1.1 4.3 2.6.8-1.5 2.3-2.6 4.3-2.6 3.3 0 5.2 3.1 4.1 6.2C19.5 15.4 12 20 12 20Z"/>',
		'bag'      => '<path d="M5 8h14l-1.2 12.2a1 1 0 0 1-1 .8H7.2a1 1 0 0 1-1-.8L5 8Z"/><path d="M9 10V6.5a3 3 0 0 1 6 0V10"/>',
		'menu'     => '<path d="M3 6h18M3 12h18M3 18h18"/>',
		'close'    => '<path d="M6 6l12 12M18 6 6 18"/>',
		'left'     => '<path d="m15 5-7 7 7 7"/>',
		'right'    => '<path d="m9 5 7 7-7 7"/>',
		'down'     => '<path d="m5 9 7 7 7-7"/>',
		'plus'     => '<path d="M12 5v14M5 12h14"/>',
		'minus'    => '<path d="M5 12h14"/>',
		'eye'      => '<path d="M2 12s3.6-7 10-7 10 7 10 7-3.6 7-10 7S2 12 2 12Z"/><circle cx="12" cy="12" r="3"/>',
		'filter'   => '<path d="M4 6h16M7 12h10M10 18h4"/>',
		'truck'    => '<path d="M2 6h12v10H2zM14 10h4l3 3v3h-7"/><circle cx="6" cy="18" r="2"/><circle cx="17" cy="18" r="2"/>',
		'lock'     => '<rect x="4" y="10" width="16" height="11" rx="2"/><path d="M8 10V7a4 4 0 0 1 8 0v3"/>',
		'return'   => '<path d="M9 14 4 9l5-5"/><path d="M4 9h11a5 5 0 0 1 0 10h-3"/>',
		'award'    => '<circle cx="12" cy="9" r="6"/><path d="m8.5 14-1.5 7 5-3 5 3-1.5-7"/>',
		'hand'     => '<path d="M7 11V5.5a1.5 1.5 0 0 1 3 0V11M10 10V4a1.5 1.5 0 0 1 3 0v6M13 10V5a1.5 1.5 0 0 1 3 0v7"/><path d="M16 9.5a1.5 1.5 0 0 1 3 0V14a7 7 0 0 1-7 7h-.5a6 6 0 0 1-5-2.7L4 14.5a1.6 1.6 0 0 1 2.6-1.8L7 13.5"/>',
		'leaf'     => '<path d="M5 19c0-8 5-14 15-15-1 10-7 15-15 15Z"/><path d="M5 19 13 11"/>',
		'rabbit'   => '<path d="M9 9C8 5 8.5 2 10 2s2 4 1.5 7M13 9c.5-4 1.5-7 3-6.5S17 6 15 9.5"/><path d="M6 15a6 5.5 0 0 1 12 0c0 3-2.5 5-6 5s-6-2-6-5Z"/><circle cx="10" cy="14" r=".6" fill="currentColor"/><circle cx="14" cy="14" r=".6" fill="currentColor"/>',
		'globe'    => '<circle cx="12" cy="12" r="9"/><path d="M3 12h18M12 3c2.5 2.7 3.8 5.7 3.8 9S14.5 18.3 12 21c-2.5-2.7-3.8-5.7-3.8-9S9.5 5.7 12 3Z"/>',
		'star'     => '<path d="m12 3 2.7 5.6 6.1.9-4.4 4.3 1 6.1L12 17l-5.4 2.9 1-6.1-4.4-4.3 6.1-.9Z"/>',
		'instagram' => '<rect x="3" y="3" width="18" height="18" rx="5"/><circle cx="12" cy="12" r="4"/><circle cx="17.5" cy="6.5" r=".8" fill="currentColor"/>',
		'facebook' => '<path d="M14 8h3V4h-3a4 4 0 0 0-4 4v3H7v4h3v6h4v-6h3l1-4h-4V8.5a.5.5 0 0 1 .5-.5Z"/>',
		'pinterest' => '<circle cx="12" cy="12" r="9"/><path d="M11 8.5c3-1 5.5.8 5 3.6-.4 2.4-2.7 3.4-4 2.4M11.5 11 9 21"/>',
		'youtube'  => '<rect x="2.5" y="5.5" width="19" height="13" rx="4"/><path d="m10 9 5 3-5 3Z"/>',
		'arrow'    => '<path d="M4 12h15M13 6l6 6-6 6"/>',
	);
	if ( ! isset( $paths[ $name ] ) ) {
		return '';
	}
	return sprintf(
		'<svg class="icon icon-%1$s" width="%2$d" height="%2$d" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true" focusable="false">%3$s</svg>',
		esc_attr( $name ),
		$size,
		$paths[ $name ]
	);
}

/**
 * Star rating with an accessible label, e.g. "★★★★★ 4.9 · 289 votes".
 * Nothing is printed when a product has no reviews — ratings are never invented.
 */
function adiva_rating( float $rating, int $count = 0, bool $show_count = true ): string {
	if ( $rating <= 0 ) {
		return '';
	}
	$pct   = max( 0, min( 100, ( $rating / 5 ) * 100 ) );
	$label = $count
		/* translators: 1: average rating, 2: number of votes */
		? sprintf( __( 'Rated %1$s out of 5 from %2$d votes', 'adiva' ), number_format_i18n( $rating, 1 ), $count )
		/* translators: %s: average rating */
		: sprintf( __( 'Rated %s out of 5', 'adiva' ), number_format_i18n( $rating, 1 ) );

	$out  = '<div class="rating" role="img" aria-label="' . esc_attr( $label ) . '">';
	$out .= '<span class="rating__stars" style="--pct:' . esc_attr( (string) round( $pct, 1 ) ) . '%" aria-hidden="true"></span>';
	$out .= '<span class="rating__value" aria-hidden="true">' . esc_html( number_format_i18n( $rating, 1 ) );
	if ( $show_count && $count ) {
		/* translators: %d: number of votes */
		$out .= ' <span class="rating__count">· ' . esc_html( sprintf( _n( '%d vote', '%d votes', $count, 'adiva' ), $count ) ) . '</span>';
	}
	$out .= '</span></div>';
	return $out;
}

/**
 * Format a plain number as a price. Uses WooCommerce when present so the
 * store currency settings are respected.
 */
function adiva_format_price( float $amount ): string {
	if ( function_exists( 'wc_price' ) ) {
		return wp_strip_all_tags( wc_price( $amount ) );
	}
	$symbol = get_theme_mod( 'adiva_demo_currency', '$' );
	return $symbol . number_format( $amount, ( floor( $amount ) == $amount ) ? 0 : 2 );
}

/**
 * Sale-aware price markup for a normalised product (see inc/products.php).
 */
function adiva_price_markup( array $p ): string {
	if ( ! empty( $p['price_html'] ) ) {
		return '<div class="price">' . wp_kses_post( $p['price_html'] ) . '</div>';
	}
	$out = '<div class="price">';
	if ( ! empty( $p['on_sale'] ) && ! empty( $p['regular_price'] ) ) {
		$out .= '<span class="price__sale"><span class="screen-reader-text">' . esc_html__( 'Sale price', 'adiva' ) . '</span>' . esc_html( adiva_format_price( (float) $p['price'] ) ) . '</span>';
		$out .= '<del class="price__regular"><span class="screen-reader-text">' . esc_html__( 'Regular price', 'adiva' ) . '</span>' . esc_html( adiva_format_price( (float) $p['regular_price'] ) ) . '</del>';
	} else {
		$out .= '<span class="price__current">' . esc_html( adiva_format_price( (float) $p['price'] ) ) . '</span>';
	}
	return $out . '</div>';
}

/**
 * Percentage saved, for sale badges. Returns 0 when not on sale.
 */
function adiva_discount_pct( array $p ): int {
	if ( empty( $p['on_sale'] ) || empty( $p['regular_price'] ) || (float) $p['regular_price'] <= 0 ) {
		return 0;
	}
	return (int) round( ( 1 - ( (float) $p['price'] / (float) $p['regular_price'] ) ) * 100 );
}

/**
 * Gold flourish divider echoing the line under the ADiva wordmark.
 */
function adiva_flourish( string $class = '' ): string {
	return '<span class="flourish ' . esc_attr( $class ) . '" aria-hidden="true"><svg viewBox="0 0 240 14" preserveAspectRatio="none"><path d="M0 7 H96 Q104 3 108 7" fill="none" stroke="currentColor" stroke-width="1"/><path d="M240 7 H144 Q136 11 132 7" fill="none" stroke="currentColor" stroke-width="1"/><path d="M120 1 126 7 120 13 114 7Z" fill="currentColor"/><circle cx="108.5" cy="7" r="1.6" fill="currentColor"/><circle cx="131.5" cy="7" r="1.6" fill="currentColor"/></svg></span>';
}

/**
 * Section heading block used across the homepage and product page.
 *
 * @param string $title    Heading text.
 * @param string $eyebrow  Optional small label above.
 * @param string $link     Optional "View all" URL.
 * @param string $id       Heading id, for aria-labelledby.
 */
function adiva_section_heading( string $title, string $eyebrow = '', string $link = '', string $id = '' ): void {
	?>
	<div class="section-head">
		<div class="section-head__text">
			<?php if ( $eyebrow ) : ?>
				<p class="eyebrow"><?php echo esc_html( $eyebrow ); ?></p>
			<?php endif; ?>
			<h2 class="section-title"<?php echo $id ? ' id="' . esc_attr( $id ) . '"' : ''; ?>><?php echo esc_html( $title ); ?></h2>
			<?php echo adiva_flourish( 'section-head__flourish' ); // phpcs:ignore WordPress.Security.EscapeOutput -- static SVG. ?>
		</div>
		<?php if ( $link ) : ?>
			<a class="link-arrow" href="<?php echo esc_url( $link ); ?>"><?php esc_html_e( 'View all', 'adiva' ); ?> <?php echo adiva_icon( 'arrow', 16 ); // phpcs:ignore ?></a>
		<?php endif; ?>
	</div>
	<?php
}

/**
 * URL for a product category slug, with a safe fallback when the term
 * doesn't exist yet (fresh install before the setup tool has run).
 */
function adiva_category_url( string $slug ): string {
	if ( '' === $slug ) {
		return adiva_shop_url();
	}
	if ( taxonomy_exists( 'product_cat' ) ) {
		$link = get_term_link( $slug, 'product_cat' );
		if ( ! is_wp_error( $link ) ) {
			return $link;
		}
	}
	return home_url( '/product-category/' . rawurlencode( $slug ) . '/' );
}

/**
 * Shop page URL with a fallback.
 */
function adiva_shop_url(): string {
	if ( function_exists( 'wc_get_page_permalink' ) ) {
		return wc_get_page_permalink( 'shop' );
	}
	return home_url( '/shop/' );
}

/**
 * Link to a page by slug, falling back to a pretty URL.
 */
function adiva_page_url( string $slug ): string {
	$page = function_exists( 'get_page_by_path' ) ? get_page_by_path( $slug ) : null;
	return $page ? get_permalink( $page ) : home_url( '/' . $slug . '/' );
}

/**
 * Asset URL helper.
 */
function adiva_asset( string $path ): string {
	return ADIVA_URI . '/assets/' . ltrim( $path, '/' );
}
