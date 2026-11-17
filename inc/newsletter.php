<?php
/**
 * Footer newsletter: stores sign-ups in an option list (exportable from
 * Appearance → ADiva Setup). Swap for Mailchimp/Klaviyo by hooking
 * 'adiva_newsletter_signup' and returning early.
 *
 * @package adiva
 */

defined( 'ABSPATH' ) || exit;

/**
 * Handle the footer form (logged-in and guest).
 */
function adiva_newsletter_handle(): void {
	$back = wp_get_referer() ? wp_get_referer() : home_url( '/' );

	if ( ! isset( $_POST['adiva_nl_nonce'] ) || ! wp_verify_nonce( sanitize_key( wp_unslash( $_POST['adiva_nl_nonce'] ) ), 'adiva_newsletter' ) ) {
		wp_safe_redirect( add_query_arg( 'subscribed', 'error', $back ) . '#newsletter' );
		exit;
	}
	// Honeypot: real people never fill this hidden field.
	if ( ! empty( $_POST['adiva_website'] ) ) {
		wp_safe_redirect( add_query_arg( 'subscribed', '1', $back ) . '#newsletter' );
		exit;
	}

	$email = isset( $_POST['email'] ) ? sanitize_email( wp_unslash( $_POST['email'] ) ) : '';
	if ( ! is_email( $email ) ) {
		wp_safe_redirect( add_query_arg( 'subscribed', 'invalid', $back ) . '#newsletter' );
		exit;
	}

	do_action( 'adiva_newsletter_signup', $email );

	$list = (array) get_option( 'adiva_newsletter', array() );
	if ( ! isset( $list[ $email ] ) ) {
		$list[ $email ] = gmdate( 'c' );
		update_option( 'adiva_newsletter', $list, false );
	}

	wp_safe_redirect( add_query_arg( 'subscribed', '1', $back ) . '#newsletter' );
	exit;
}
add_action( 'admin_post_nopriv_adiva_newsletter', 'adiva_newsletter_handle' );
add_action( 'admin_post_adiva_newsletter', 'adiva_newsletter_handle' );

/**
 * Status message after redirect.
 */
function adiva_newsletter_message(): string {
	// phpcs:ignore WordPress.Security.NonceVerification.Recommended -- display only.
	$state = isset( $_GET['subscribed'] ) ? sanitize_key( wp_unslash( $_GET['subscribed'] ) ) : '';
	$map   = array(
		'1'       => __( 'Thank you — you’re on the list. Welcome to the ADiva tribe.', 'adiva' ),
		'invalid' => __( 'That email doesn’t look quite right. Please try again.', 'adiva' ),
		'error'   => __( 'Your session expired. Please try again.', 'adiva' ),
	);
	return $map[ $state ] ?? '';
}
