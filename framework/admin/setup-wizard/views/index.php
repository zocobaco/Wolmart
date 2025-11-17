<?php
defined( 'ABSPATH' ) || die;

$step_number  = 1;
$output_steps = $this->steps;
$images_url   = array(
	'status'          => WOLMART_ADMIN_URI . '/setup-wizard/images/wizard_status.svg',
	'customize'       => WOLMART_ADMIN_URI . '/setup-wizard/images/wizard_child.svg',
	'page_builder'    => WOLMART_ADMIN_URI . '/setup-wizard/images/wizard_builder.svg',
	'default_plugins' => WOLMART_ADMIN_URI . '/setup-wizard/images/wizard_plugins.svg',
	'demo_content'    => WOLMART_ADMIN_URI . '/setup-wizard/images/wizard_import.svg',
	'ready'           => WOLMART_ADMIN_URI . '/setup-wizard/images/wizard_ready.svg',
);
?>
<div class="wolmart-admin-panel-header">
	<h1><?php esc_html_e( 'Setup Wizard', 'wolmart' ); ?></h1>
	<p><?php esc_html_e( 'This quick setup wizard will help you configure your new website. This wizard will install the required WordPress plugins, import demo.', 'wolmart' ); ?></p>
</div>
<div class="wolmart-admin-panel-view">
	<div class="wolmart-admin-panel-row">	
		<div class="wolmart-admin-panel-side">
			<nav class="wolmart-card-box">
				<ul class="wolmart-admin-panel-steps">
					<?php foreach ( $output_steps as $step_key => $step ) : ?>
						<?php
						$li_class_escaped = '';
						if ( $step_key === $this->step ) {
							$li_class_escaped = 'active';
						} elseif ( array_search( $this->step, array_keys( $this->steps ) ) > array_search( $step_key, array_keys( $this->steps ) ) ) {
							$li_class_escaped = 'done';
						}
						?>
						<li class="<?php echo esc_attr( $li_class_escaped ); ?>">
							<?php
								echo '<a href="' . esc_url( $this->get_step_link( $step_key ) ) . '">' . '<img src="' . esc_url( $images_url[ $step_key ] ) . '">' . esc_html( $step['name'] ) . '</a>';
							?>
						</li>
					<?php endforeach; ?>
				</ul>
			</nav>
		</div>
		<div class="wolmart-admin-panel-content">
			<div class="wolmart-admin-panel-body wolmart-card-box wolmart-setup-<?php echo esc_attr( str_replace( '_', '-', $this->step ) ); ?>">
				<?php $this->view_step(); ?>
			</div>
		</div>
	</div>
</div>
