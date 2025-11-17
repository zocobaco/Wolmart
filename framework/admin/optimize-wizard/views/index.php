<?php
defined( 'ABSPATH' ) || die;

$step_number  = 1;
$output_steps = $this->steps;
$images_url   = array(
	'resources'   => WOLMART_ADMIN_URI . '/optimize-wizard/images/wizard_resources.svg',
	'lazyload'    => WOLMART_ADMIN_URI . '/optimize-wizard/images/wizard_lazyload.svg',
	'performance' => WOLMART_ADMIN_URI . '/optimize-wizard/images/wizard_performance.svg',
	'plugins'     => WOLMART_ADMIN_URI . '/optimize-wizard/images/wizard_plugins.svg',
	'ready'       => WOLMART_ADMIN_URI . '/optimize-wizard/images/wizard_ready.svg',
);
?>
<div class="wolmart-admin-panel-header">
	<h1><?php esc_html_e( 'Optimize Wizard', 'wolmart' ); ?></h1>
	<p><?php esc_html_e( 'Wolmart optimize wizard will help you configure proper website with optimum resources and peak efficiency.', 'wolmart' ); ?></p>
</div>
<div class="wolmart-admin-panel-view">
	<div class="wolmart-admin-panel-row wolmart-optimize-panel">
		<div class="wolmart-admin-panel-side">
			<div class="wolmart-card-box">
				<ul class="wolmart-admin-panel-steps">
					<?php
					$index = 1;
					foreach ( $output_steps as $step_key => $step ) :
						$li_class_escaped = '';
						if ( $step_key === $this->step ) {
							$li_class_escaped = 'active';
						} elseif ( array_search( $this->step, array_keys( $this->steps ) ) > array_search( $step_key, array_keys( $this->steps ) ) ) {
							$li_class_escaped = 'done';
						}
						?>
						<li class="step <?php echo esc_attr( $li_class_escaped ); ?>">
							<?php
								echo '<a href="' . esc_url( $this->get_step_link( $step_key ) ) . '">' . '<img src="' . esc_url( $images_url[ $step_key ] ) . '">' . esc_html( $step['name'] ) . '</a>';
							?>
						</li>
						<?php
						$index ++;
						endforeach;
					?>
				</ul>
			</div>
		</div>
		<div class="wolmart-admin-panel-content">
			<div class="wolmart-admin-panel-body wolmart-card-box wolmart-optimize-<?php echo esc_attr( str_replace( '_', '-', $this->step ) ); ?>">
				<?php $this->view_step(); ?>
			</div>
		</div>
	</div>
</div>
