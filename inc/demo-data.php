<?php
/**
 * Demo catalogue.
 *
 * Shown only when WooCommerce is inactive or has no published products, so a
 * fresh install previews the full design. As soon as real products exist the
 * theme uses them instead (see adiva_get_products()). Ratings here are demo
 * values only; live products display their real WooCommerce review average.
 *
 * Disable entirely with: add_filter( 'adiva_use_demo_products', '__return_false' );
 *
 * @package adiva
 */

defined( 'ABSPATH' ) || exit;

/**
 * Full demo catalogue as normalised product arrays.
 *
 * @return array<int, array>
 */
function adiva_demo_products(): array {
	static $products = null;
	if ( null !== $products ) {
		return $products;
	}

	// slug, title, art, bg, price, regular, cats, rating, votes, badge.
	$rows = array(
		array( 'marigold-zardozi-tote', 'Marigold Zardozi Tote', 'tote', '#F6D9A8', 128, 178, 'handbags,totes,new,best-sellers', 4.9, 289, 'New' ),
		array( 'rani-beaded-clutch', 'Rani Beaded Evening Clutch', 'clutch', '#F3C6CF', 89, 0, 'handbags,clutches,new,special-occasion', 4.9, 164, '' ),
		array( 'jaipur-sun-round-bag', 'Jaipur Sun Woven Round Bag', 'round', '#EFD8B4', 74, 98, 'handbags,shoulder-bags,raffia-collection,new,best-sellers', 4.8, 412, 'Bestseller' ),
		array( 'kesar-embroidered-shoulder-bag', 'Kesar Embroidered Shoulder Bag', 'shoulder', '#F2C9B5', 96, 0, 'handbags,shoulder-bags,medium-bags,new', 4.9, 97, 'New' ),
		array( 'goa-shoreline-beach-bag', 'Goa Shoreline Beach Bag', 'beach', '#CFE3DF', 68, 85, 'handbags,beach-bags,vacation-shop,new', 4.8, 203, '' ),
		array( 'mehndi-mirror-pouch', 'Mehndi Mirror-Work Pouch', 'pouch', '#E9D3E8', 38, 0, 'handbags,pouches,accessories,new', 4.9, 151, '' ),
		array( 'saffron-bloom-weekender', 'Saffron Bloom Weekender', 'weekender', '#F5D0A4', 158, 198, 'handbags,weekenders,getaway-edit', 4.9, 76, '' ),
		array( 'lotus-sequin-clutch', 'Lotus Sequin Clutch', 'clutch', '#F8D5C2', 79, 0, 'handbags,clutches,special-occasion,new', 4.7, 58, 'New' ),
		array( 'evil-eye-beaded-bag', 'Nazar Evil Eye Beaded Bag', 'shoulder', '#C9D9EE', 112, 0, 'handbags,evil-eye-collection,best-sellers', 4.9, 333, 'Bestseller' ),
		array( 'jaguar-jacquard-tote', 'Jaguar Jacquard Tote', 'tote', '#E7D2B2', 118, 148, 'handbags,totes,jaguar-collection,animal-print,best-sellers', 4.8, 188, '' ),
		array( 'daphne-raffia-bucket', 'Daphne Raffia Bucket Bag', 'beach', '#EADCC0', 86, 0, 'handbags,daphne-bag-collection,raffia-collection', 4.8, 64, '' ),
		array( 'sienna-tassel-crossbody', 'Sienna Tassel Crossbody', 'shoulder', '#F0C3A6', 92, 115, 'handbags,sienna-bag-collection,medium-bags', 4.9, 121, '' ),
		array( 'gulmohar-maxi-dress', 'Gulmohar Hand-Block Maxi Dress', 'dress', '#F6C9C4', 148, 0, 'clothing,dresses,new,floral-print', 4.9, 142, 'New' ),
		array( 'chandni-kaftan', 'Chandni Embroidered Kaftan', 'kaftan', '#E8DDF0', 118, 158, 'clothing,cover-ups,vacation-shop,new', 4.8, 96, '' ),
		array( 'sunehri-wrap-top', 'Sunehri Wrap Top', 'top', '#F7DFB0', 64, 0, 'clothing,tops,new,solids', 4.7, 54, '' ),
		array( 'leela-coord-set', 'Leela Printed Co-ord Set', 'coord', '#D9E6CF', 136, 168, 'clothing,co-ord-sets,new,floral-print', 4.9, 87, 'New' ),
		array( 'ruby-tiered-midi', 'Ruby Tiered Midi Dress', 'dress', '#F4B8B5', 128, 0, 'clothing,dresses,special-occasion,best-sellers', 4.9, 260, 'Bestseller' ),
		array( 'indigo-dabu-dress', 'Indigo Dabu Shirt Dress', 'dress', '#C8D3EA', 112, 140, 'clothing,dresses,solids', 4.8, 73, '' ),
		array( 'pushkar-palazzo', 'Pushkar Palazzo Pants', 'coord', '#EBD9C3', 78, 0, 'clothing,bottoms', 4.8, 49, '' ),
		array( 'jaguar-silk-shirt', 'Jaguar Print Silk Shirt', 'top', '#E9CFA6', 98, 122, 'clothing,tops,animal-print,jaguar-collection', 4.9, 66, '' ),
		array( 'jhumka-drop-earrings', 'Jhumka Bead Drop Earrings', 'earrings', '#F3D3A5', 42, 0, 'accessories,jewelry,new', 4.9, 218, 'New' ),
		array( 'kolhapuri-sandals', 'Kolhapuri Embroidered Sandals', 'sandals', '#EBCDB0', 72, 88, 'accessories,sandals,vacation-shop', 4.8, 130, '' ),
		array( 'tassel-bag-charm', 'Silk Tassel Bag Charm', 'charm', '#F6C6D8', 24, 0, 'accessories,bag-charms,tassels', 4.9, 92, '' ),
		array( 'beaded-bag-strap', 'Beaded Statement Bag Strap', 'strap', '#E6D4F0', 48, 60, 'accessories,bag-straps', 4.8, 61, '' ),
	);

	$sizes_by_art = array(
		'dress' => array( 'XS', 'S', 'M', 'L', 'XL' ),
		'kaftan' => array( 'S/M', 'L/XL' ),
		'top' => array( 'XS', 'S', 'M', 'L', 'XL' ),
		'coord' => array( 'XS', 'S', 'M', 'L', 'XL' ),
		'sandals' => array( '36', '37', '38', '39', '40', '41' ),
	);

	$products = array();
	foreach ( $rows as $i => $r ) {
		list( $slug, $title, $art, $bg, $price, $regular, $cats, $rating, $votes, $badge ) = $r;
		$img      = adiva_asset( 'images/demo/' . $art . '.svg' );
		$is_cloth = isset( $sizes_by_art[ $art ] );

		$products[] = array(
			'id'            => 9000 + $i,
			'slug'          => $slug,
			'title'         => $title,
			'url'           => home_url( '/product/' . $slug . '/' ),
			'price'         => (float) $price,
			'regular_price' => $regular ? (float) $regular : 0.0,
			'on_sale'       => $regular > 0,
			'price_html'    => '',
			'rating'        => (float) $rating,
			'rating_count'  => (int) $votes,
			'image'         => $img,
			'image_alt'     => $title,
			'bg'            => $bg,
			'gallery'       => array( $img, $img, $img, $img, $img, $img ),
			'gallery_bgs'   => adiva_demo_tints( $bg ),
			'categories'    => explode( ',', $cats ),
			'badge'         => $badge,
			'type'          => $is_cloth ? 'variable' : 'simple',
			'sizes'         => $is_cloth ? $sizes_by_art[ $art ] : array(),
			'in_stock'      => true,
			'short'         => adiva_demo_short( $art ),
			'description'   => adiva_demo_description( $art, $title ),
			'add_to_cart'   => '',
			'date'          => 1000 - $i,
			'sales'         => $votes,
		);
	}
	return $products;
}

/**
 * Six gallery backdrops derived from a base tint, so demo galleries feel varied.
 */
function adiva_demo_tints( string $bg ): array {
	return array( $bg, '#FCEED3', $bg, '#F7E3C4', $bg, '#FBE8DA' );
}

/**
 * One-line demo summaries.
 */
function adiva_demo_short( string $art ): string {
	$map = array(
		'tote'      => __( 'A roomy everyday tote, hand-embroidered in metallic zardozi thread on cotton canvas.', 'adiva' ),
		'clutch'    => __( 'Glass beads and sequins hand-set one by one — an evening clutch that catches every light.', 'adiva' ),
		'round'     => __( 'Sun-bleached jute, hand-coiled into a perfect circle with a soft leather strap.', 'adiva' ),
		'shoulder'  => __( 'A structured shoulder bag with hand-stamped motifs and tonal thread embroidery.', 'adiva' ),
		'beach'     => __( 'Hand-woven raffia with cotton tassels, sized for towel, book and sunscreen.', 'adiva' ),
		'pouch'     => __( 'Tiny mirrors stitched in by hand — the pouch for lipstick, keys and little treasures.', 'adiva' ),
		'weekender' => __( 'A generous carry-all for two days away, block-printed and lined in soft cotton.', 'adiva' ),
		'dress'     => __( 'Hand-block printed in Rajasthan on breathable cotton voile, cut to move with you.', 'adiva' ),
		'kaftan'    => __( 'A floaty kaftan with thread-work neckline — from beach to dinner without a change.', 'adiva' ),
		'top'       => __( 'An easy, flattering top with hand-finished seams and mother-of-pearl buttons.', 'adiva' ),
		'coord'     => __( 'A relaxed matching set in hand-printed cotton, wear together or apart.', 'adiva' ),
		'earrings'  => __( 'Featherlight bead jhumkas strung by hand in a family-run Jaipur workshop.', 'adiva' ),
		'sandals'   => __( 'Hand-stitched leather sandals with thread embroidery on a cushioned sole.', 'adiva' ),
		'charm'     => __( 'Silk tassel charm to clip on any bag — a little swing of colour.', 'adiva' ),
		'strap'     => __( 'Swap-on beaded strap, adjustable and hand-strung, to refresh any favourite bag.', 'adiva' ),
	);
	return $map[ $art ] ?? '';
}

/**
 * Demo long description in the brand voice: poetic, then practical.
 */
function adiva_demo_description( string $art, string $title ): string {
	$short = adiva_demo_short( $art );
	return '<p>' . esc_html( $short ) . ' ' . esc_html__( 'Every piece passes through several pairs of hands — a sketch, a hand-carved stamp, hours of needlework — so no two are ever exactly alike.', 'adiva' ) . '</p>'
		. '<ul>'
		. '<li>' . esc_html__( 'Handmade in India by ADiva artisans', 'adiva' ) . '</li>'
		. '<li>' . esc_html__( 'Natural and recycled fibres, azo-free dyes', 'adiva' ) . '</li>'
		. '<li>' . esc_html__( 'Spot clean gently; store in the cotton dust bag provided', 'adiva' ) . '</li>'
		. '<li>' . esc_html__( 'Small variations in colour and stitch are the mark of hand craft', 'adiva' ) . '</li>'
		. '</ul>';
}
