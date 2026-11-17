<?php
/**
 * Announcement bar. One message is static; several auto-rotate with
 * prev/next controls (rotation pauses on hover/focus and for reduced motion).
 *
 * @package adiva
 */

defined( 'ABSPATH' ) || exit;

if ( ! adiva_mod( 'adiva_announcement_enable' ) ) {
	return;
}
$adiva_msgs = adiva_announcements();
if ( ! $adiva_msgs ) {
	return;
}
$adiva_multi = count( $adiva_msgs ) > 1;
?>
<div class="announcement" data-announcement aria-roledescription="carousel" aria-label="<?php esc_attr_e( 'Announcements', 'adiva' ); ?>">
	<div class="announcement__inner container">
		<?php if ( $adiva_multi ) : ?>
			<button class="announcement__nav" type="button" data-dir="-1"><?php echo adiva_icon( 'left', 14 ); // phpcs:ignore ?><span class="screen-reader-text"><?php esc_html_e( 'Previous announcement', 'adiva' ); ?></span></button>
		<?php endif; ?>
		<div class="announcement__track" aria-live="polite">
			<?php foreach ( $adiva_msgs as $i => $msg ) : ?>
				<p class="announcement__msg<?php echo 0 === $i ? ' is-active' : ''; ?>"<?php echo 0 === $i ? '' : ' hidden'; ?>>
					<?php if ( $msg[1] ) : ?>
						<a href="<?php echo esc_url( $msg[1] ); ?>"><?php echo esc_html( $msg[0] ); ?></a>
					<?php else : ?>
						<?php echo esc_html( $msg[0] ); ?>
					<?php endif; ?>
				</p>
			<?php endforeach; ?>
		</div>
		<?php if ( $adiva_multi ) : ?>
			<button class="announcement__nav" type="button" data-dir="1"><?php echo adiva_icon( 'right', 14 ); // phpcs:ignore ?><span class="screen-reader-text"><?php esc_html_e( 'Next announcement', 'adiva' ); ?></span></button>
		<?php endif; ?>
	</div>
</div>
