<?php
/**
 * Collection / category / shop archive.
 *
 * Title → toolbar (Filter + Sort) → responsive grid (2–4 cols) → Load More
 * (progressive enhancement over real pagination links) → Recently viewed.
 *
 * Overrides woocommerce/templates/archive-product.php.
 *
 * @package adiva
 * @version 8.6.0
 */

defined( 'ABSPATH' ) || exit;

get_header();

if ( function_exists( 'woocommerce_output_all_notices' ) ) {
	echo '<div class="container">';
	woocommerce_output_all_notices();
	echo '</div>';
}

$adiva_result  = adiva_archive_products();
$adiva_items   = $adiva_result['items'];
$adiva_title   = adiva_archive_title();
$adiva_desc    = is_tax() ? term_description() : '';
$adiva_orderby = isset( $_GET['orderby'] ) ? sanitize_key( wp_unslash( $_GET['orderby'] ) ) : ( is_search() ? 'relevance' : 'menu_order' ); // phpcs:ignore WordPress.Security.NonceVerification
$adiva_next    = $adiva_result['current'] < $adiva_result['pages'] ? get_pagenum_link( $adiva_result['current'] + 1 ) : '';
$adiva_shown   = min( $adiva_result['total'], ( $adiva_result['current'] - 1 ) * (int) apply_filters( 'adiva_products_per_page', 12 ) + count( $adiva_items ) );
$adiva_ranges  = array(
	array( __( 'Under $50', 'adiva' ), 0, 50 ),
	array( __( '$50 – $100', 'adiva' ), 50, 100 ),
	array( __( '$100 – $150', 'adiva' ), 100, 150 ),
	array( __( '$150 & above', 'adiva' ), 150, 0 ),
);
// phpcs:disable WordPress.Security.NonceVerification.Recommended
$adiva_min = isset( $_GET['min_price'] ) ? (float) $_GET['min_price'] : 0;
$adiva_max = isset( $_GET['max_price'] ) ? (float) $_GET['max_price'] : 0;
// phpcs:enable
$adiva_base = remove_query_arg( array( 'min_price', 'max_price', 'paged', 'product-page' ) );
?>
<header class="page-header collection-header">
	<div class="container">
		<?php if ( function_exists( 'woocommerce_breadcrumb' ) && ! adiva_using_demo() ) : ?>
			<?php woocommerce_breadcrumb( array( 'wrap_before' => '<nav class="breadcrumb" aria-label="' . esc_attr__( 'Breadcrumb', 'adiva' ) . '">', 'wrap_after' => '</nav>', 'delimiter' => '<span aria-hidden="true"> / </span>' ) ); ?>
		<?php else : ?>
			<nav class="breadcrumb" aria-label="<?php esc_attr_e( 'Breadcrumb', 'adiva' ); ?>"><a href="<?php echo esc_url( home_url( '/' ) ); ?>"><?php esc_html_e( 'Home', 'adiva' ); ?></a> <span aria-hidden="true">/</span> <span aria-current="page"><?php echo esc_html( $adiva_title ); ?></span></nav>
		<?php endif; ?>
		<h1 class="page-title collection-title"><?php echo esc_html( $adiva_title ); ?></h1>
		<?php echo adiva_flourish(); // phpcs:ignore ?>
		<?php if ( $adiva_desc ) : ?>
			<div class="collection-desc"><?php echo wp_kses_post( $adiva_desc ); ?></div>
		<?php endif; ?>
	</div>
</header>

<div class="container collection">
	<div class="toolbar">
		<button class="button button--ghost toolbar__filter" type="button" data-open="filter-drawer" aria-controls="filter-drawer" aria-expanded="false">
			<?php echo adiva_icon( 'filter', 18 ); // phpcs:ignore ?> <?php esc_html_e( 'Filter', 'adiva' ); ?>
			<?php if ( $adiva_min || $adiva_max ) : ?><span class="toolbar__dot" aria-label="<?php esc_attr_e( 'filters active', 'adiva' ); ?>"></span><?php endif; ?>
		</button>
		<p class="toolbar__count" aria-live="polite">
			<?php /* translators: 1: shown, 2: total */ printf( esc_html( _n( '%2$d product', 'Showing %1$d of %2$d products', $adiva_result['total'], 'adiva' ) ), (int) $adiva_shown, (int) $adiva_result['total'] ); ?>
		</p>
		<form class="toolbar__sort" method="get" action="">
			<label for="orderby"><?php esc_html_e( 'Sort by', 'adiva' ); ?></label>
			<div class="select">
				<select name="orderby" id="orderby" data-autosubmit>
					<?php foreach ( adiva_sort_options() as $adiva_key => $adiva_label ) : ?>
						<option value="<?php echo esc_attr( $adiva_key ); ?>" <?php selected( $adiva_orderby, $adiva_key ); ?>><?php echo esc_html( $adiva_label ); ?></option>
					<?php endforeach; ?>
				</select>
				<?php echo adiva_icon( 'down', 14 ); // phpcs:ignore ?>
			</div>
			<?php
			// Keep other query args (search, price filter) when re-sorting.
			foreach ( $_GET as $adiva_k => $adiva_v ) { // phpcs:ignore WordPress.Security.NonceVerification
				if ( in_array( $adiva_k, array( 'orderby', 'paged', 'product-page' ), true ) || is_array( $adiva_v ) ) {
					continue;
				}
				echo '<input type="hidden" name="' . esc_attr( sanitize_key( $adiva_k ) ) . '" value="' . esc_attr( sanitize_text_field( wp_unslash( $adiva_v ) ) ) . '">';
			}
			?>
			<noscript><button class="button button--ghost" type="submit"><?php esc_html_e( 'Apply', 'adiva' ); ?></button></noscript>
		</form>
	</div>

	<?php if ( $adiva_items ) : ?>
		<ul class="product-grid" role="list" data-grid>
			<?php foreach ( $adiva_items as $adiva_i => $adiva_p ) : ?>
				<li class="product-grid__item">
					<?php get_template_part( 'template-parts/product/card', null, array( 'product' => $adiva_p, 'eager' => $adiva_i < 4 ) ); ?>
				</li>
			<?php endforeach; ?>
		</ul>

		<div class="load-more">
			<?php if ( $adiva_next ) : ?>
				<a class="button button--outline load-more__btn" href="<?php echo esc_url( $adiva_next ); ?>" data-load-more><?php esc_html_e( 'Load more', 'adiva' ); ?></a>
			<?php endif; ?>
			<?php if ( $adiva_result['pages'] > 1 ) : ?>
				<nav class="pagination" aria-label="<?php esc_attr_e( 'Product pages', 'adiva' ); ?>" data-pagination>
					<?php for ( $adiva_n = 1; $adiva_n <= $adiva_result['pages']; $adiva_n++ ) : ?>
						<?php if ( $adiva_n === $adiva_result['current'] ) : ?>
							<span aria-current="page"><?php echo (int) $adiva_n; ?></span>
						<?php else : ?>
							<a href="<?php echo esc_url( get_pagenum_link( $adiva_n ) ); ?>"><?php echo (int) $adiva_n; ?></a>
						<?php endif; ?>
					<?php endfor; ?>
				</nav>
			<?php endif; ?>
		</div>
	<?php else : ?>
		<div class="empty-state">
			<?php echo adiva_icon( 'bag', 40 ); // phpcs:ignore ?>
			<p><?php esc_html_e( 'Nothing here just yet — our artisans are busy. Explore the full collection in the meantime.', 'adiva' ); ?></p>
			<a class="button" href="<?php echo esc_url( adiva_shop_url() ); ?>"><?php esc_html_e( 'Shop all', 'adiva' ); ?></a>
		</div>
	<?php endif; ?>
</div>

<?php get_template_part( 'template-parts/product/recently-viewed' ); ?>

<aside class="drawer drawer--left filter-drawer" id="filter-drawer" role="dialog" aria-modal="true" aria-label="<?php esc_attr_e( 'Filters', 'adiva' ); ?>" hidden>
	<div class="drawer__head">
		<span class="drawer__title"><?php esc_html_e( 'Filter', 'adiva' ); ?></span>
		<button class="icon-btn" type="button" data-close><?php echo adiva_icon( 'close' ); // phpcs:ignore ?><span class="screen-reader-text"><?php esc_html_e( 'Close filters', 'adiva' ); ?></span></button>
	</div>
	<div class="drawer__body">
		<div class="filter-group">
			<h2 class="filter-group__title"><?php esc_html_e( 'Price', 'adiva' ); ?></h2>
			<ul class="filter-list">
				<?php foreach ( $adiva_ranges as $adiva_r ) :
					$adiva_url    = add_query_arg( array_filter( array( 'min_price' => $adiva_r[1], 'max_price' => $adiva_r[2] ) ), $adiva_base );
					$adiva_active = ( (float) $adiva_r[1] === $adiva_min && (float) $adiva_r[2] === $adiva_max );
					?>
					<li><a href="<?php echo esc_url( $adiva_url ); ?>" class="<?php echo $adiva_active ? 'is-active' : ''; ?>"<?php echo $adiva_active ? ' aria-current="true"' : ''; ?>><?php echo esc_html( $adiva_r[0] ); ?></a></li>
				<?php endforeach; ?>
			</ul>
			<?php if ( $adiva_min || $adiva_max ) : ?>
				<a class="link-underline" href="<?php echo esc_url( $adiva_base ); ?>"><?php esc_html_e( 'Clear price filter', 'adiva' ); ?></a>
			<?php endif; ?>
		</div>
		<div class="filter-group">
			<h2 class="filter-group__title"><?php esc_html_e( 'Category', 'adiva' ); ?></h2>
			<ul class="filter-list filter-list--cats">
				<?php foreach ( adiva_filter_categories() as $adiva_g ) : ?>
					<li>
						<a href="<?php echo esc_url( $adiva_g[1] ); ?>" class="filter-list__parent<?php echo $adiva_g[2] ? ' is-active' : ''; ?>"><?php echo esc_html( $adiva_g[0] ); ?> <span class="filter-list__count"><?php echo (int) $adiva_g[4]; ?></span></a>
						<?php if ( $adiva_g[3] ) : ?>
							<ul>
								<?php foreach ( $adiva_g[3] as $adiva_c ) : ?>
									<li><a href="<?php echo esc_url( $adiva_c[1] ); ?>" class="<?php echo $adiva_c[2] ? 'is-active' : ''; ?>"><?php echo esc_html( $adiva_c[0] ); ?> <span class="filter-list__count"><?php echo (int) $adiva_c[3]; ?></span></a></li>
								<?php endforeach; ?>
							</ul>
						<?php endif; ?>
					</li>
				<?php endforeach; ?>
			</ul>
		</div>
	</div>
</aside>
<?php
get_footer();
