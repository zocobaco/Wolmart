<?php
defined( 'ABSPATH' ) || die;
?>
<?php /* translators: %s: Theme name */ ?>
<h2><?php printf( esc_html__( 'Welcome to the optimize wizard for %s.', 'wolmart' ), wp_get_theme() ); ?></h2>

<?php
if ( get_option( 'wolmart_optimize_complete', false ) ) {
	?>
	<p class="lead success"><?php esc_html_e( 'It looks like you have already optimized your site.', 'wolmart' ); ?></p>

	<p class="wolmart-admin-panel-actions">
		<a href="<?php echo esc_url( $this->get_next_step_link() ); ?>" class="button-primary button button-next button-large"><?php esc_html_e( 'Run Optimize Wizard Again', 'wolmart' ); ?></a>
		<a href="<?php echo esc_url( admin_url( 'admin.php?page=wolmart' ) ); ?>" class="button button-large"><?php esc_html_e( 'Exit to Wolmart Panel', 'wolmart' ); ?></a>
	</p>
	<?php
} else {
	?>
	<?php /* translators: %s: Theme name */ ?>
	<p><?php printf( esc_html__( 'This Optimize Wizard is introduced to optimize all resources that are unnecessary for your site. Every step has enough description about how it works. Some options may produce some conflicts if your site is still in development progress, so we advise you to enable all options once site development is completed.', 'wolmart' ), wp_get_theme() ); ?></p>
	<p><span class="info-qt"><?php esc_html_e( 'No time right now? ', 'wolmart' ); ?></span><?php esc_html_e( 'If you don\'t want to go through the wizard, you can skip and return to the WordPress dashboard. Come back anytime you want!', 'wolmart' ); ?></p>
	<p class="wolmart-admin-panel-actions">
		<a href="<?php echo esc_url( wp_get_referer() && ! strpos( wp_get_referer(), 'update.php' ) ? wp_get_referer() : admin_url( '' ) ); ?>" class="button button-large button-dark"><?php esc_html_e( 'Not right now', 'wolmart' ); ?></a>
		<a href="<?php echo esc_url( $this->get_next_step_link() ); ?>" class="button-dark button button-large button-next"><?php esc_html_e( "Let's Go", 'wolmart' ); ?></a>
	</p>
	<?php
}
