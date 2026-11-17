<?php
/**
 * Normalised product layer.
 *
 * Templates never talk to WC_Product directly; they receive plain arrays from
 * the functions below. That keeps card/quick-view/wishlist markup identical
 * whether the data comes from WooCommerce or from the demo catalogue, and lets
 * the same array be JSON-encoded for the Quick View modal.
 *
 * Normalised shape:
 *   id, slug, title, url, price, regular_price, on_sale, price_html, rating,
 *   rating_count, image, image_alt, bg, gallery[], gallery_bgs[], categories[],
 *   badge, type, sizes[], in_stock, short, description, add_to_cart
 *
 * @package adiva
 */

defined( 'ABSPATH' ) || exit;

/**
 * Is WooCommerce loaded?
 */
function adiva_wc_active(): bool {
	return class_exists( 'WooCommerce' );
}

/**
 * Whether the store has at least one published product. Cached per request.
 */
function adiva_has_real_products(): bool {
	static $has = null;
	if ( null === $has ) {
		$has = adiva_wc_active() && function_exists( 'wc_get_products' )
			&& ! empty( wc_get_products( array( 'status' => 'publish', 'limit' => 1, 'return' => 'ids' ) ) );
	}
	return $has;
}

/**
 * Should the demo catalogue be used?
 */
function adiva_using_demo(): bool {
	return ! adiva_has_real_products() && apply_filters( 'adiva_use_demo_products', true );
}

/**
 * Convert a WC_Product into the normalised array.
 *
 * @param WC_Product $product Product.
 * @param bool       $full    Include gallery + long description (single product).
 */
function adiva_normalize_product( $product, bool $full = false ): array {
	$id       = $product->get_id();
	$image_id = $product->get_image_id();
	$image    = $image_id ? wp_get_attachment_image_url( $image_id, 'adiva-card' ) : wc_placeholder_img_src( 'adiva-card' );

	$gallery = array();
	if ( $full ) {
		$ids = array_filter( array_merge( array( $image_id ), $product->get_gallery_image_ids() ) );
		foreach ( $ids as $gid ) {
			$gallery[] = wp_get_attachment_image_url( $gid, 'large' );
		}
	}
	if ( ! $gallery ) {
		$gallery = array( $image );
	}

	if ( $product->is_type( 'variable' ) ) {
		$price   = (float) $product->get_variation_price( 'min' );
		$regular = (float) $product->get_variation_regular_price( 'min' );
	} else {
		$price   = (float) $product->get_price();
		$regular = (float) $product->get_regular_price();
	}
	$on_sale = $product->is_on_sale() && $regular > $price;

	$cats  = get_the_terms( $id, 'product_cat' );
	$slugs = ( $cats && ! is_wp_error( $cats ) ) ? wp_list_pluck( $cats, 'slug' ) : array();

	$created = $product->get_date_created();
	$is_new  = $created && ( time() - $created->getTimestamp() ) < 30 * DAY_IN_SECONDS;

	$sizes = array();
	if ( $product->is_type( 'variable' ) ) {
		foreach ( $product->get_variation_attributes() as $values ) {
			$sizes = array_values( (array) $values );
			break;
		}
	}

	$simple_buyable = $product->is_type( 'simple' ) && $product->is_purchasable() && $product->is_in_stock();

	return array(
		'id'            => $id,
		'slug'          => $product->get_slug(),
		'title'         => $product->get_name(),
		'url'           => $product->get_permalink(),
		'price'         => $price,
		'regular_price' => $on_sale ? $regular : 0.0,
		'on_sale'       => $on_sale,
		// Ranges ("$40 – $60") keep WooCommerce's own formatting.
		'price_html'    => ( $product->is_type( 'variable' ) && $product->get_variation_price( 'min' ) !== $product->get_variation_price( 'max' ) ) ? $product->get_price_html() : '',
		'rating'        => (float) $product->get_average_rating(),
		'rating_count'  => (int) $product->get_rating_count(),
		'image'         => $image,
		'image_alt'     => $image_id ? (string) get_post_meta( $image_id, '_wp_attachment_image_alt', true ) : $product->get_name(),
		'bg'            => '',
		'gallery'       => $gallery,
		'gallery_bgs'   => array(),
		'categories'    => $slugs,
		'badge'         => $product->is_featured() ? __( 'Bestseller', 'adiva' ) : ( $is_new ? __( 'New', 'adiva' ) : '' ),
		'type'          => $product->get_type(),
		'sizes'         => $sizes,
		'in_stock'      => $product->is_in_stock(),
		'short'         => wp_trim_words( wp_strip_all_tags( $product->get_short_description() ), 30 ),
		'description'   => $full ? apply_filters( 'the_content', $product->get_description() ) : '',
		'add_to_cart'   => $simple_buyable ? (string) $id : '',
	);
}

/**
 * Query products for carousels and grids.
 *
 * @param array $args {
 *   @type string $category Product category slug ('' = all).
 *   @type int    $limit    Max products.
 *   @type string $orderby  date | popularity | rating | rand.
 *   @type bool   $on_sale  Only sale items.
 *   @type int[]  $exclude  IDs to skip.
 * }
 * @return array<int, array>
 */
function adiva_get_products( array $args = array() ): array {
	$args = wp_parse_args(
		$args,
		array( 'category' => '', 'limit' => 12, 'orderby' => 'date', 'on_sale' => false, 'exclude' => array() )
	);

	if ( adiva_using_demo() ) {
		$result = adiva_demo_query( $args['category'], $args['orderby'], 1, (int) $args['limit'], $args['exclude'] );
		return $result['items'];
	}
	if ( ! adiva_wc_active() ) {
		return array();
	}

	$query = array(
		'status'     => 'publish',
		'limit'      => (int) $args['limit'],
		'visibility' => 'catalog',
		'exclude'    => array_map( 'intval', (array) $args['exclude'] ),
	);
	if ( $args['category'] ) {
		$query['category'] = array( $args['category'] );
	}
	switch ( $args['orderby'] ) {
		case 'popularity':
			$query['orderby']  = 'meta_value_num';
			$query['meta_key'] = 'total_sales'; // phpcs:ignore WordPress.DB.SlowDBQuery
			$query['order']    = 'DESC';
			break;
		case 'rand':
			$query['orderby'] = 'rand';
			break;
		default:
			$query['orderby'] = 'date';
			$query['order']   = 'DESC';
	}
	if ( $args['on_sale'] ) {
		$query['include'] = wc_get_product_ids_on_sale();
	}

	return array_map( 'adiva_normalize_product', wc_get_products( $query ) );
}

/**
 * Filter, sort and page the demo catalogue.
 *
 * @return array{items: array, total: int, pages: int, current: int}
 */
function adiva_demo_query( string $category = '', string $orderby = 'menu_order', int $paged = 1, int $per_page = 12, array $exclude = array(), string $search = '', float $min = 0, float $max = 0 ): array {
	$items = array_values(
		array_filter(
			adiva_demo_products(),
			static function ( $p ) use ( $category, $exclude, $search, $min, $max ) {
				if ( 'sale' === $category ) {
					if ( empty( $p['on_sale'] ) ) {
						return false;
					}
				} elseif ( $category && ! in_array( $category, $p['categories'], true ) ) {
					return false;
				}
				if ( ( $min && $p['price'] < $min ) || ( $max && $p['price'] > $max ) ) {
					return false;
				}
				if ( $search && false === stripos( $p['title'] . ' ' . $p['short'], $search ) ) {
					return false;
				}
				return ! in_array( $p['id'], $exclude, true );
			}
		)
	);

	$sorters = array(
		'popularity' => static fn( $a, $b ) => $b['sales'] <=> $a['sales'],
		'rating'     => static fn( $a, $b ) => $b['rating'] <=> $a['rating'],
		'date'       => static fn( $a, $b ) => $b['date'] <=> $a['date'],
		'date-asc'   => static fn( $a, $b ) => $a['date'] <=> $b['date'],
		'price'      => static fn( $a, $b ) => $a['price'] <=> $b['price'],
		'price-desc' => static fn( $a, $b ) => $b['price'] <=> $a['price'],
		'title-asc'  => static fn( $a, $b ) => strcmp( $a['title'], $b['title'] ),
		'title-desc' => static fn( $a, $b ) => strcmp( $b['title'], $a['title'] ),
	);
	if ( isset( $sorters[ $orderby ] ) ) {
		usort( $items, $sorters[ $orderby ] );
	}

	$total = count( $items );
	$pages = max( 1, (int) ceil( $total / max( 1, $per_page ) ) );
	$paged = max( 1, min( $paged, $pages ) );

	return array(
		'items'   => array_slice( $items, ( $paged - 1 ) * $per_page, $per_page ),
		'total'   => $total,
		'pages'   => $pages,
		'current' => $paged,
	);
}

/**
 * Products for the current archive request (shop, category, tag, search).
 *
 * With real products this simply walks the main query WooCommerce prepared
 * (so its ordering, price filter and pagination all apply). With the demo
 * catalogue it reads the same query vars.
 *
 * @return array{items: array, total: int, pages: int, current: int}
 */
function adiva_archive_products(): array {
	global $wp_query;

	if ( ! adiva_using_demo() ) {
		$items = array();
		if ( have_posts() ) {
			while ( have_posts() ) {
				the_post();
				$product = wc_get_product( get_the_ID() );
				if ( $product && $product->is_visible() ) {
					$items[] = adiva_normalize_product( $product );
				}
			}
			wp_reset_postdata();
		}
		return array(
			'items'   => $items,
			'total'   => (int) $wp_query->found_posts,
			'pages'   => (int) $wp_query->max_num_pages,
			'current' => max( 1, (int) get_query_var( 'paged' ) ),
		);
	}

	// phpcs:disable WordPress.Security.NonceVerification.Recommended -- read-only catalogue view.
	$orderby = isset( $_GET['orderby'] ) ? sanitize_key( wp_unslash( $_GET['orderby'] ) ) : 'menu_order';
	$search  = isset( $_GET['s'] ) ? sanitize_text_field( wp_unslash( $_GET['s'] ) ) : '';
	$min     = isset( $_GET['min_price'] ) ? (float) $_GET['min_price'] : 0;
	$max     = isset( $_GET['max_price'] ) ? (float) $_GET['max_price'] : 0;
	// phpcs:enable
	$cat = (string) get_query_var( 'product_cat' );

	return adiva_demo_query( $cat, $orderby, max( 1, (int) get_query_var( 'paged' ) ), (int) apply_filters( 'adiva_products_per_page', 12 ), array(), $search, $min, $max );
}

/**
 * Heading for the current product archive.
 */
function adiva_archive_title(): string {
	if ( is_search() ) {
		/* translators: %s: search query */
		return sprintf( __( 'Results for “%s”', 'adiva' ), get_search_query() );
	}
	if ( ! adiva_using_demo() && function_exists( 'woocommerce_page_title' ) ) {
		return (string) woocommerce_page_title( false );
	}
	$cat = (string) get_query_var( 'product_cat' );
	if ( $cat ) {
		foreach ( adiva_default_menu() as $node ) {
			if ( ( $node['cat'] ?? '' ) === $cat ) {
				return $node['label'];
			}
			foreach ( $node['children'] as $child ) {
				if ( ( $child['cat'] ?? '' ) === $cat ) {
					return $child['label'];
				}
			}
		}
		return ucwords( str_replace( '-', ' ', $cat ) );
	}
	return __( 'Shop all', 'adiva' );
}

/**
 * Category groups for the filter drawer: [ [label, url, current, children[]], … ].
 */
function adiva_filter_categories(): array {
	$current = (string) get_query_var( 'product_cat' );
	$groups  = array();

	if ( ! adiva_using_demo() && taxonomy_exists( 'product_cat' ) ) {
		$parents = get_terms( array( 'taxonomy' => 'product_cat', 'parent' => 0, 'hide_empty' => true ) );
		if ( is_wp_error( $parents ) ) {
			return array();
		}
		foreach ( $parents as $term ) {
			if ( 'uncategorized' === $term->slug ) {
				continue;
			}
			$kids     = get_terms( array( 'taxonomy' => 'product_cat', 'parent' => $term->term_id, 'hide_empty' => true ) );
			$children = array();
			foreach ( is_wp_error( $kids ) ? array() : $kids as $kid ) {
				$children[] = array( $kid->name, get_term_link( $kid ), $kid->slug === $current, $kid->count );
			}
			$groups[] = array( $term->name, get_term_link( $term ), $term->slug === $current, $children, $term->count );
		}
		return $groups;
	}

	foreach ( adiva_default_menu() as $node ) {
		if ( empty( $node['cat'] ) || empty( $node['children'] ) ) {
			continue;
		}
		$children = array();
		foreach ( $node['children'] as $child ) {
			if ( empty( $child['cat'] ) || $child['cat'] === $node['cat'] ) {
				continue;
			}
			$count = adiva_demo_query( $child['cat'] )['total'];
			if ( $count ) {
				$children[] = array( $child['label'], adiva_category_url( $child['cat'] ), $child['cat'] === $current, $count );
			}
		}
		$groups[] = array( $node['label'], adiva_category_url( $node['cat'] ), $node['cat'] === $current, $children, adiva_demo_query( $node['cat'] )['total'] );
	}
	return $groups;
}

/**
 * The product being viewed on a single product page.
 */
function adiva_current_product(): ?array {
	if ( ! adiva_using_demo() ) {
		$product = function_exists( 'wc_get_product' ) ? wc_get_product( get_the_ID() ) : null;
		return $product ? adiva_normalize_product( $product, true ) : null;
	}
	$slug = (string) get_query_var( 'product' );
	foreach ( adiva_demo_products() as $p ) {
		if ( $p['slug'] === $slug ) {
			return $p;
		}
	}
	return null;
}

/**
 * "You may also like" products.
 */
function adiva_related_products( array $p, int $limit = 10 ): array {
	if ( ! adiva_using_demo() && function_exists( 'wc_get_related_products' ) ) {
		$ids = wc_get_related_products( $p['id'], $limit );
		return array_values( array_filter( array_map( static function ( $id ) {
			$product = wc_get_product( $id );
			return $product ? adiva_normalize_product( $product ) : null;
		}, $ids ) ) );
	}
	$cat = $p['categories'][1] ?? ( $p['categories'][0] ?? '' );
	$out = adiva_get_products( array( 'category' => $cat, 'limit' => $limit, 'exclude' => array( $p['id'] ) ) );
	if ( count( $out ) < 4 ) {
		$out = adiva_get_products( array( 'limit' => $limit, 'exclude' => array( $p['id'] ) ) );
	}
	return $out;
}

/**
 * The subset of a product that Quick View, wishlist and recently-viewed need,
 * JSON-encoded for a data attribute. Long description is deliberately left out.
 */
function adiva_product_json( array $p ): string {
	$data = array(
		'id'       => $p['id'],
		'title'    => $p['title'],
		'url'      => $p['url'],
		'image'    => $p['image'],
		'alt'      => $p['image_alt'],
		'bg'       => $p['bg'],
		'gallery'  => array_slice( $p['gallery'], 0, 4 ),
		'gbgs'     => array_slice( $p['gallery_bgs'], 0, 4 ),
		'price'    => adiva_price_markup( $p ),
		'rating'   => adiva_rating( $p['rating'], $p['rating_count'] ),
		'short'    => $p['short'],
		'sizes'    => $p['sizes'],
		'cart'     => $p['add_to_cart'],
		'type'     => $p['type'],
		'inStock'  => $p['in_stock'],
	);
	return (string) wp_json_encode( $data );
}
