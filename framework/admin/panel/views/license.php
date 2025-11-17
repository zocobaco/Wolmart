<?php
defined( 'ABSPATH' ) || die;

require_once WOLMART_ADMIN . '/importer/importer-api.php';

$importer_api = new Wolmart_Importer_API();
?>
<div class="wolmart-admin-panel-header">
	<h1><?php esc_html_e( 'Welcome to Wolmart', 'wolmart' ); ?></h1>
	<?php if ( Wolmart_Admin::get_instance()->is_registered() ) : ?>
		<p><?php esc_html_e( 'Congratulations! Your product is registered now.', 'wolmart' ); ?></p>
	<?php else : ?>
		<p><?php esc_html_e( 'Thank you for choosing Wolmart theme from ThemeForest. Please register your purchase and make sure that you have fulfilled all of the requirements.', 'wolmart' ); ?></p>
	<?php endif; ?>
</div>
<div class="wolmart-admin-panel-view">
	<div class="wolmart-admin-panel-row">
		<div class="wolmart-admin-panel-side">
			<div class="wolmart-card-box wolmart-version">
				<img class="logo" src="<?php echo WOLMART_URI; ?>/assets/images/admin/license.svg" alt="<?php esc_attr_e( 'Wolmart Setup Wizard', 'wolmart' ); ?>" width="258" height="258" />
				<?php /* translators: %1$s represents opening bold tag, %2$s represents closing bold tag and br tag, %3$s represents theme version. */ ?>
				<h3><?php printf( esc_html__( '%1$sWolmart eCommerce%2$sVersion %3$s', 'wolmart' ), '<b>', '</b><br><a href="' . esc_url( $importer_api->get_url( 'changelog' ) ) . '" title="' . esc_html__( 'View Changelog', 'wolmart' ) . '" target="_blank">', WOLMART_VERSION . '</a>' ); ?></h3>
			</div>
		</div>
		<div class="wolmart-admin-panel-content">
			<div class="wolmart-admin-panel-body welcome wolmart-card-box">
				<div class="wolmart-important-notice registration-form-container">
					<div class="wolmart-registration-form">
						<?php if ( ! Wolmart_Admin::get_instance()->is_registered() ) : ?>
							<p class="about-description"><?php esc_html_e( 'Please enter your Purchase Code to complete registration.', 'wolmart' ); ?></p>
						<?php endif; ?>

						<h4><?php esc_html_e( 'Where can I find my purchase code?', 'wolmart' ); ?></h4>
						<ul>
							<?php /* translators: $1: opening A tag which has link to the Themeforest downloads page $2: closing A tag */ ?>
							<li><i class="circle w-icon-check2"></i><?php printf( esc_html__( 'Please go to %1$sThemeForest.net/downloads%2$s', 'wolmart' ), '<a target="_blank" href="https://themeforest.net/downloads">', '</a>' ); ?></li>
							<li><i class="circle w-icon-check2"></i><?php printf( esc_html__( 'Click the Download button in Wolmart row', 'wolmart' ), '<strong>', '</strong>' ); ?></li>
							<li><i class="circle w-icon-check2"></i><?php printf( esc_html__( 'Select License Certificate & Purchase code', 'wolmart' ), '<strong>', '</strong>' ); ?></li>
							<li><i class="circle w-icon-check2"></i><?php printf( esc_html__( 'Copy Item Purchase Code', 'wolmart' ), '<strong>', '</strong>' ); ?></li>
						</ul>

						<?php
							$disable_field = '';
							$errors        = get_option( 'wolmart_register_error_msg' );
							update_option( 'wolmart_register_error_msg', '' );
							$purchase_code = Wolmart_Admin::get_instance()->get_purchase_code_asterisk();
						if ( ! empty( $errors ) ) {
							echo '<div class="notice-error notice-alt notice-large">' . esc_html( $errors ) . '</div>';
						}

						if ( ! empty( $purchase_code ) ) {
							if ( ! empty( $errors ) ) {
								echo '<div class="notice-warning notice-alt notice-large">' . esc_html__( 'Purchase code not updated. We will keep the existing one.', 'wolmart' ) . '</div>';
							} else {
								/* translators: $1 and $2 opening and closing strong tags respectively */
								echo '<div class="notice-success notice-alt notice-large">' . sprintf( esc_html__( 'Your %1$spurchase code is valid%2$s. Thank you! Enjoy Wolmart Theme and automatic updates.', 'wolmart' ), '<strong>', '</strong>' ) . '</div>';
							}
						}
						?>
						<form id="wolmart_registration" method="post">
							<?php
							if ( $purchase_code && ! empty( $purchase_code ) && Wolmart_Admin::get_instance()->is_registered() ) {
								$disable_field = ' disabled=true';
							}
							?>
							<input type="hidden" name="wolmart_registration" />
							<?php if ( Wolmart_Admin::get_instance()->is_envato_hosted() ) : ?>
								<p class="confirm unregister">
									<?php esc_html_e( 'You are using Envato Hosted, this subscription code can not be deregistered.', 'wolmart' ); ?>
								</p>
							<?php else : ?>
								<input type="text" id="wolmart_purchase_code" name="code" class="regular-text wolmart-input" value="<?php echo esc_attr( $purchase_code ); ?>" placeholder="<?php esc_attr_e( 'Purchase Code', 'wolmart' ); ?>" <?php echo wolmart_escaped( $disable_field ); ?> />
								<?php if ( Wolmart_Admin::get_instance()->is_registered() ) : ?>
									<input type="hidden" name="action" value="unregister" />
									<?php submit_button( esc_html__( 'Deactivate', 'wolmart' ), array( 'button-dark', 'large', 'wolmart-large-button' ), '', true ); ?>
									<?php if ( empty( get_option( 'wolmart_demo_history', array() ) ) ) : ?>
									<a href="<?php echo esc_url( admin_url( 'admin.php?page=wolmart-setup-wizard' ) ); ?>" class="button button-light button-large"><?php esc_html_e( 'Run Setup Wizard', 'wolmart' ); ?></a>
									<?php endif; ?>
								<?php else : ?>
									<input type="hidden" name="action" value="register" />
									<?php submit_button( esc_html__( 'Activate', 'wolmart' ), array( 'primary', 'large', 'wolmart-large-button' ), '', true ); ?>
								<?php endif; ?>
							<?php endif; ?>
							<?php wp_nonce_field( 'wolmart-setup-wizard' ); ?>
						</form>
					</div>
				</div>
				<p class="about-description">
					<?php /* translators: $1: opening A tag which has link to the Wolmart documentation $2: closing A tag */ ?>
					<?php printf( esc_html__( 'Before you get started, please be sure to always check out %1$sthis documentation%2$s. We outline all kinds of good information, and provide you with all the details you need to use Wolmart.', 'wolmart' ), '<a href="https://d-themes.com/wordpress/wolmart/documentation" target="_blank">', '</a>' ); ?>
				</p>
				<p class="about-description">
					<?php /* translators: $1: opening A tag which has link to the Wolmart support $2: closing A tag */ ?>
					<?php printf( esc_html__( 'If you are unable to find your answer in our documentation, we encourage you to contact us through %1$ssupport page%2$s with your site CPanel (or FTP) and WordPress admin details.', 'wolmart' ), '<a href="https://d-themes.com/wordpress/wolmart/support" target="_blank">', '</a>' ); ?>
					<br>
					<?php esc_html_e( 'We are very happy to help you and you will get reply from us faster than you expected.', 'wolmart' ); ?>
				</p>
			</div>
		</div>
	</div>
</div>
