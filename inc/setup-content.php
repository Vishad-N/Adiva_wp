<?php
/**
 * Starter page content for the setup tool (and the local preview).
 *
 * All copy is original ADiva brand voice. Policy pages are deliberately marked
 * as drafts to review — they are starting points, not legal advice.
 *
 * @package adiva
 */

defined( 'ABSPATH' ) || exit;

/**
 * Pages the setup tool can create: slug => [title, template, html].
 *
 * @return array<string, array{0: string, 1: string, 2: string}>
 */
function adiva_starter_pages(): array {
	$story    = 'page-templates/template-story.php';
	$review   = '<p class="notice-draft">' . esc_html__( 'Starter text — please review and adapt with your own policies and legal advice before launch.', 'adiva' ) . '</p>';
	$ship_min = (int) adiva_mod( 'adiva_free_shipping' );

	return array(
		'about-us'                => array(
			__( 'About Us', 'adiva' ),
			$story,
			'<section class="story-hero"><p class="eyebrow">' . esc_html__( 'Our story', 'adiva' ) . '</p><h2>' . esc_html__( 'Step into our story. Come join the ADiva tribe.', 'adiva' ) . '</h2><p>' . esc_html__( 'Handcrafted for the free-spirited woman — the one who packs light, wanders far and always comes home with a story. ADiva marries India’s living craft traditions with easy Western silhouettes, so every piece feels made for the life you actually live.', 'adiva' ) . '</p></section>'
			. '<section class="story-split"><div><h2>' . esc_html__( 'Made with love in India', 'adiva' ) . '</h2><p>' . esc_html__( 'Every ADiva bag, dress and accessory begins in a small workshop in India, in the hands of artisans whose skills have been passed down through families for generations. Zardozi, hand-block printing, mirror work and beading — techniques no machine can imitate.', 'adiva' ) . '</p></div><div><h2>' . esc_html__( 'What we believe', 'adiva' ) . '</h2><ul class="pillars"><li>' . esc_html__( 'Handmade', 'adiva' ) . '</li><li>' . esc_html__( 'Sustainably made', 'adiva' ) . '</li><li>' . esc_html__( 'Cruelty free', 'adiva' ) . '</li><li>' . esc_html__( 'Fair trade', 'adiva' ) . '</li><li>' . esc_html__( 'Ethically made', 'adiva' ) . '</li></ul></div></section>'
			. '<section class="story-quote"><blockquote><p>' . esc_html__( 'Our inspiration is the global nomad — comfortable, confident, and drawn to things with a soul.', 'adiva' ) . '</p></blockquote></section>'
			. '<section class="story-split"><div><h2>' . esc_html__( 'Our artisans, our heart', 'adiva' ) . '</h2><p>' . esc_html__( 'We build long-term relationships rather than one-off orders. That means steady work, fair pay, safe and comfortable workshops, and the dignity of being credited for the craft. When you choose ADiva, you help keep these traditions alive.', 'adiva' ) . '</p></div><div><h2>' . esc_html__( 'ADiva today', 'adiva' ) . '</h2><p>' . esc_html__( 'From a single embroidered tote to a growing collection of bags, clothing and accessories, ADiva now travels with women around the world — and every piece still carries the touch of the hands that made it.', 'adiva' ) . '</p></div></section>',
		),
		'our-story'               => array(
			__( 'Our Story', 'adiva' ),
			$story,
			'<section class="story-hero"><p class="eyebrow">' . esc_html__( 'Our story', 'adiva' ) . '</p><h2>' . esc_html__( 'Slow fashion, long before it had a name.', 'adiva' ) . '</h2><p>' . esc_html__( 'ADiva was born from a love of the bazaars, workshops and colour of India — and a wish to carry that beauty into everyday life without losing the hand that made it.', 'adiva' ) . '</p></section>'
			. '<section class="story-split"><div><h2>' . esc_html__( 'The wanderer', 'adiva' ) . '</h2><p>' . esc_html__( 'She is curious, unhurried and confident. She chooses pieces that feel good, travel well and tell a story. Everything we make is designed with her in mind.', 'adiva' ) . '</p></div><div><h2>' . esc_html__( 'The craft', 'adiva' ) . '</h2><p>' . esc_html__( 'Each design starts as a sketch and passes through many skilled hands before it reaches yours. Because it is made by hand, no two pieces are ever exactly the same — and that is the point.', 'adiva' ) . '</p></div></section>',
		),
		'behind-the-product'      => array(
			__( 'Behind the Product', 'adiva' ),
			$story,
			'<section class="story-hero"><p class="eyebrow">' . esc_html__( 'Behind the product', 'adiva' ) . '</p><h2>' . esc_html__( 'How a handmade ADiva bag comes to life', 'adiva' ) . '</h2><p>' . esc_html__( 'A single embroidered bag can take days of patient work. Here is the journey, step by step.', 'adiva' ) . '</p></section>'
			. '<ol class="process-steps">'
			. '<li><h3>' . esc_html__( 'Sketch', 'adiva' ) . '</h3><p>' . esc_html__( 'Our designers draw every motif by hand, balancing tradition with a modern eye.', 'adiva' ) . '</p></li>'
			. '<li><h3>' . esc_html__( 'Transfer', 'adiva' ) . '</h3><p>' . esc_html__( 'The design is pricked onto tracing paper and transferred to fabric with a fine chalk dust.', 'adiva' ) . '</p></li>'
			. '<li><h3>' . esc_html__( 'Hand stamp', 'adiva' ) . '</h3><p>' . esc_html__( 'Hand-carved wooden blocks press colour and outlines onto the cloth, one impression at a time.', 'adiva' ) . '</p></li>'
			. '<li><h3>' . esc_html__( 'Embroidery', 'adiva' ) . '</h3><p>' . esc_html__( 'Artisans fill the outlines with thread-work on a wooden frame — the slowest, most loving step.', 'adiva' ) . '</p></li>'
			. '<li><h3>' . esc_html__( 'Beads & zardozi', 'adiva' ) . '</h3><p>' . esc_html__( 'Beads, sequins and metallic zardozi and karchobi threads are couched on by hand for that signature shimmer.', 'adiva' ) . '</p></li>'
			. '<li><h3>' . esc_html__( 'Hand-sewn', 'adiva' ) . '</h3><p>' . esc_html__( 'Finally the panels are lined, stitched and finished, then checked piece by piece before they travel to you.', 'adiva' ) . '</p></li>'
			. '</ol>'
			. '<section class="story-quote"><blockquote><p>' . esc_html__( 'A machine makes a thousand identical bags. An artisan makes one that is yours alone.', 'adiva' ) . '</p></blockquote></section>',
		),
		'sustainability'          => array(
			__( 'Sustainability', 'adiva' ),
			$story,
			'<section class="story-hero"><p class="eyebrow">' . esc_html__( 'Sustainability', 'adiva' ) . '</p><h2>' . esc_html__( 'Made by hand. Made to matter.', 'adiva' ) . '</h2><p>' . esc_html__( 'Handmade is, by nature, a gentler way to make things. We work every day to make it gentler still.', 'adiva' ) . '</p></section>'
			. '<section class="story-split"><div><h2>' . esc_html__( 'People', 'adiva' ) . '</h2><p>' . esc_html__( 'Behind every piece is an artisan in India. Fair wages, safe workshops and long-term partnerships are non-negotiable for us.', 'adiva' ) . '</p></div><div><h2>' . esc_html__( 'Planet', 'adiva' ) . '</h2><p>' . esc_html__( 'Small-batch production means very little waste. Where we can, we choose certified organic and recycled fibres and low-impact dyes, and we reuse off-cuts in smaller pieces.', 'adiva' ) . '</p></div></section>'
			. '<section class="story-split"><div><h2>' . esc_html__( 'A cleaner supply chain', 'adiva' ) . '</h2><p>' . esc_html__( 'We keep our supply chain short and visible, working closely with the workshops we know and visit. List your certifications (e.g. GOTS, GRS, ISO) here once confirmed.', 'adiva' ) . '</p></div><div><h2>' . esc_html__( 'Made to last', 'adiva' ) . '</h2><p>' . esc_html__( 'The most sustainable piece is the one you keep for years. We design for longevity, not trends.', 'adiva' ) . '</p></div></section>',
		),
		'gift-guide'              => array( __( 'Gift Guide', 'adiva' ), '', '<p class="lead">' . esc_html__( 'Handmade gifts for the women who shine brightest — wrapped with care and made by artisan hands.', 'adiva' ) . '</p>' ),
		'faq'                     => array( __( 'FAQ', 'adiva' ), '', $review . '<h2>' . esc_html__( 'Are your products really handmade?', 'adiva' ) . '</h2><p>' . esc_html__( 'Yes. Every ADiva piece is made by hand in India, so small variations are part of its charm.', 'adiva' ) . '</p><h2>' . esc_html__( 'How long does shipping take?', 'adiva' ) . '</h2><p>' . esc_html__( 'Orders ship within 1–2 business days and usually arrive in 5–12 business days.', 'adiva' ) . '</p><h2>' . esc_html__( 'Can I return an item?', 'adiva' ) . '</h2><p>' . esc_html__( 'Unused items with tags attached can be returned within 30 days. See our Return Policy for details.', 'adiva' ) . '</p>' ),
		/* translators: %d: free shipping threshold */
		'shipping-info'           => array( __( 'Shipping Info', 'adiva' ), '', $review . '<p>' . esc_html( sprintf( __( 'Free standard shipping on orders over $%d.', 'adiva' ), $ship_min ) ) . '</p><p>' . esc_html__( 'Orders are dispatched within 1–2 business days. Standard delivery usually takes 5–12 business days depending on destination.', 'adiva' ) . '</p>' ),
		'return-policy'           => array( __( 'Return Policy', 'adiva' ), '', $review . '<p>' . esc_html__( 'We accept returns within 30 days of delivery for unused items with original tags attached. A small processing fee may apply to non-defective returns. Final-sale items cannot be returned or exchanged.', 'adiva' ) . '</p><p>' . esc_html__( 'To start a return or exchange, contact us with your order number.', 'adiva' ) . '</p>' ),
		'privacy-policy'          => array( __( 'Privacy Policy', 'adiva' ), '', $review ),
		'terms-and-conditions'    => array( __( 'Terms & Conditions', 'adiva' ), '', $review ),
		'contact-us'              => array( __( 'Contact Us', 'adiva' ), '', '<p class="lead">' . esc_html__( 'We’d love to hear from you. Write to us and a real person will reply within one business day.', 'adiva' ) . '</p><p>' . esc_html__( 'Add your email, phone and a contact form block here.', 'adiva' ) . '</p>' ),
		'wholesale'               => array( __( 'Wholesale', 'adiva' ), '', '<p class="lead">' . esc_html__( 'Bring handmade ADiva pieces to your boutique. Tell us about your store and we’ll send our wholesale lookbook.', 'adiva' ) . '</p>' ),
		'corporate-gifting'       => array( __( 'Corporate Gifting', 'adiva' ), '', '<p class="lead">' . esc_html__( 'Thoughtful, handmade gifts for teams and clients — with custom wrapping and notes.', 'adiva' ) . '</p>' ),
		'brand-ambassador'        => array( __( 'Brand Ambassador', 'adiva' ), '', '<p class="lead">' . esc_html__( 'Love ADiva? Join our ambassador circle for early access, gifts and commission.', 'adiva' ) . '</p>' ),
		'careers'                 => array( __( 'Careers', 'adiva' ), '', '<p class="lead">' . esc_html__( 'We’re a small team with a big love for craft. Open roles will be listed here.', 'adiva' ) . '</p>' ),
		'accessibility-statement' => array( __( 'Accessibility Statement', 'adiva' ), '', '<p>' . esc_html__( 'We want everyone to enjoy shopping with ADiva. This site aims to meet WCAG 2.2 AA. If anything is hard to use, please contact us and we will help.', 'adiva' ) . '</p>' ),
		'data-privacy-opt-out'    => array( __( 'Do Not Sell or Share My Personal Information', 'adiva' ), '', $review . '<p>' . esc_html__( 'Use this page to explain how customers can opt out of the sale or sharing of personal information, and link a request form.', 'adiva' ) . '</p>' ),
		'wishlist'                => array( __( 'Wishlist', 'adiva' ), 'page-templates/template-wishlist.php', '' ),
	);
}
