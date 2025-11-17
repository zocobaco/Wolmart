<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

global $wp_filesystem;
// Initialize the WordPress filesystem, no more using file_put_contents function
if ( empty( $wp_filesystem ) ) {
	require_once ABSPATH . '/wp-admin/includes/file.php';
	WP_Filesystem();
}

$data = array(
	'wp_uploads'     => wp_get_upload_dir(),
	'memory_limit'   => wp_convert_hr_to_bytes( @ini_get( 'memory_limit' ) ),
	'time_limit'     => ini_get( 'max_execution_time' ),
	'max_input_vars' => ini_get( 'max_input_vars' ),
);

$status = array(
	'uploads'        => wp_is_writable( $data['wp_uploads']['basedir'] ),
	'fs'             => ( $wp_filesystem || WP_Filesystem() ) ? true : false,
	'zip'            => class_exists( 'ZipArchive' ),
	'suhosin'        => extension_loaded( 'suhosin' ),
	'memory_limit'   => $data['memory_limit'] >= 268435456,
	'time_limit'     => ( ( $data['time_limit'] >= 600 ) || ( 0 == $data['time_limit'] ) ) ? true : false,
	'max_input_vars' => $data['max_input_vars'] >= 2000,
);
?>
<h2><?php esc_html_e( 'System Status', 'wolmart' ); ?></h2>
<p><?php esc_html_e( 'Check your current server performance.', 'wolmart' ); ?></p>

<div class="system-status">
	<ul class="system-status">
		<li>
			<?php if ( $status['uploads'] ) : ?>
				<i class="circle w-icon-check2"></i>
			<?php else : ?>
				<i class="circle w-icon-times-solid"></i>
			<?php endif; ?>

			<span class="label"><?php esc_html_e( 'Uploads folder writable', 'wolmart' ); ?></span>

			<?php if ( ! $status['uploads'] ) : ?>
			<p class="status-notice status-error"><?php esc_html_e( 'Uploads folder must be writable. Please set write permission to your wp-content/uploads folder.', 'wolmart' ); ?></p>
			<?php endif; ?>
		</li>

		<li>
			<?php if ( $status['fs'] ) : ?>
				<i class="circle w-icon-check2"></i>
			<?php else : ?>
				<i class="circle w-icon-times-solid"></i>
			<?php endif; ?>

			<span class="label"><?php esc_html_e( 'WP File System', 'wolmart' ); ?></span>

			<?php if ( ! $status['fs'] ) : ?>
				<p class="status-notice status-error"><?php esc_html_e( 'File System access is required for pre-built websites and plugins installation. Please contact your hosting provider.', 'wolmart' ); ?></p>
			<?php endif; ?>

		</li>

		<li>
			<?php if ( $status['zip'] ) : ?>
				<i class="circle w-icon-check2"></i>
			<?php else : ?>
				<i class="circle w-icon-times-solid"></i>
			<?php endif; ?>

			<span class="label"><?php esc_html_e( 'ZipArchive', 'wolmart' ); ?></span>

			<?php if ( ! $status['zip'] ) : ?>
				<p class="status-notice status-error"><?php esc_html_e( 'ZipArchive is required for pre-built websites and plugins installation. Please contact your hosting provider.', 'wolmart' ); ?></p>
			<?php endif; ?>
		</li>

		<?php if ( $status['suhosin'] ) : ?>

			<li>
				<span class="status step-id status-info"></span>
				<span class="label"><?php esc_html_e( 'SUHOSIN Installed', 'wolmart' ); ?></span>
				<p class="status-notice"><?php esc_html_e( 'Suhosin may need to be configured to increase its data submission limits.', 'wolmart' ); ?></p>
			</li>

		<?php else : ?>

			<li>
				<?php if ( $status['memory_limit'] ) : ?>
					<i class="circle w-icon-check2"></i>
				<?php else : ?>
					<?php if ( $data['memory_limit'] < 134217728 ) : ?>
						<i class="circle w-icon-times-solid"></i>
					<?php else : ?>
						<span class="status step-id status-info"></span>
					<?php endif; ?>
				<?php endif; ?>

				<span class="label"><?php esc_html_e( 'PHP Memory Limit', 'wolmart' ); ?></span>

				<?php if ( $status['memory_limit'] ) : ?>
					<span class="desc">(<?php echo size_format( $data['memory_limit'] ); ?>)</span>
				<?php else : ?>
					<?php if ( $data['memory_limit'] < 134217728 ) : ?>
						<span class="desc">(<?php echo size_format( $data['memory_limit'] ); ?>)</span>
						<?php /* translators: opening and closing strong tag */ ?>
						<p class="status-notice status-error"><?php printf( esc_html__( 'Minimum %1$s128 MB%2$s is required, %1$s256 MB%2$s is recommended.', 'wolmart' ), '<strong>', '</strong>' ); ?></p>
					<?php else : ?>
						<span class="desc">(<?php echo size_format( $data['memory_limit'] ); ?>)</span>
						<?php /* translators: opening and closing strong tag */ ?>
						<p class="status-notice status-error"><?php printf( esc_html__( 'Current memory limit is OK, however %1$s256 MB%2$s is recommended.', 'wolmart' ), '<strong>', '</strong>' ); ?></p>
					<?php endif; ?>
				<?php endif; ?>

			</li>

			<li>
				<?php if ( $status['time_limit'] ) : ?>
					<i class="circle w-icon-check2"></i>
				<?php else : ?>
					<?php if ( $data['time_limit'] < 300 ) : ?>
						<i class="circle w-icon-times-solid"></i>
					<?php else : ?>
						<i class="circle w-icon-info"></i>
					<?php endif; ?>
				<?php endif; ?>

				<span class="label"><?php esc_html_e( 'PHP max_execution_time', 'wolmart' ); ?></span>

				<?php if ( $status['time_limit'] ) : ?>
					<span class="desc">(<?php echo esc_html( $data['time_limit'] ); ?>)</span>
				<?php else : ?>
					<?php if ( $data['time_limit'] < 300 ) : ?>
						<span class="desc">(<?php echo esc_html( $data['time_limit'] ); ?>)</span>
						<?php /* translators: opening and closing strong tag */ ?>
						<p class="status-notice status-error"><?php printf( esc_html__( 'Minimum %1$s300%2$s is required, %1$s600%2$s is recommended.', 'wolmart' ), '<strong>', '</strong>' ); ?></p>
					<?php else : ?>
						<span class="desc">(<?php echo esc_html( $data['time_limit'] ); ?>)</span>
						<?php /* translators: opening and closing strong tag */ ?>
						<p class="status-notice status-error"><?php printf( esc_html__( 'Current time limit is OK, however %1$s600%2$s is recommended.', 'wolmart' ), '<strong>', '</strong>' ); ?></p>
					<?php endif; ?>
				<?php endif; ?>

			</li>

			<li>
				<?php if ( $status['max_input_vars'] ) : ?>
					<i class="circle w-icon-check2"></i>
				<?php else : ?>
					<i class="circle w-icon-times-solid"></i>
				<?php endif; ?>

				<span class="label"><?php esc_html_e( 'PHP max_input_vars', 'wolmart' ); ?></span>

				<?php if ( $status['max_input_vars'] ) : ?>
					<span class="desc">(<?php echo esc_html( $data['max_input_vars'] ); ?>)</span>
				<?php else : ?>
					<span class="desc">(<?php echo esc_html( $data['max_input_vars'] ); ?>)</span>
					<p class="status-notice status-error"><?php esc_html_e( 'Minimum 2000 is required', 'wolmart' ); ?></p>
				<?php endif; ?>
			</li>
			<li>
				<p class="info-qt"><?php esc_html_e( 'Do not worry if you are unable to update your server configuration due to hosting limit, you can use "Alternative Import" method in Demo Content import page.', 'wolmart' ); ?></p>
				<?php
				printf(
					'<p class="info">%1$s <a target="_blank" href="http://php.net/manual/en/function.phpinfo.php">%2$s</a></p>',
					esc_html__( 'php.ini values are shown above. Real values may vary, please check your limits using', 'wolmart' ),
					esc_html( 'php_info()' )
				);
				?>
			</li>

		<?php endif; ?>

	</ul>

</div><p class="wolmart-admin-panel-actions">
	<a href="<?php echo esc_url( $this->get_next_step_link() ); ?>" class="button button-dark button-large button-next" role="button"><?php esc_html_e( 'Continue', 'wolmart' ); ?></a>
</p>
