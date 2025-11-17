<?php
defined( 'ABSPATH' ) || die;

// Elements Group Mapping
$used_mapping  = [];
$used_by_group = [];
if ( defined( 'WPB_VC_VERSION' ) ) {
	$used_mapping['shortcode'] = array(
		'title' => esc_html__( 'WPBakery Shortcodes', 'wolmart' ),
	);
}

// Initialize grouped array.
foreach ( $used_mapping as $group => $data ) {
	$used_by_group[ $group ] = array();
}

foreach ( $wolmart_used_elements as $element => $used ) {
	$find = false;
	foreach ( $used_mapping as $group => $data ) {
		if ( isset( $data['prefix'] ) ) {
			foreach ( $data['prefix'] as $prefix ) {
				if ( substr( $element, 0, strlen( $prefix ) ) == $prefix ) {
					$used_by_group[ $group ][] = $element;
					$find                      = true;
					break;
				}
			}
		}
		if ( $find ) {
			break;
		}
	}
}

if ( defined( 'WPB_VC_VERSION' ) ) {
	$used_by_group['shortcode'] = $this->get_all_shortcodes();
}

foreach ( $used_by_group as $group => $elements ) {
	ksort( $used_by_group[ $group ] );
}

foreach ( $used_by_group as $group => $elements ) {
	// WPB Shortcodes
	if ( 'shortcode' == $group && defined( 'WPB_VC_VERSION' ) ) {
		?>
		<div class="wolmart-card <?php echo esc_attr( $group ); ?>">
			<div class="wolmart-card-header">
				<h3><?php echo esc_html( $used_mapping[ $group ]['title'] ); ?></h3>
				<label class="checkbox checkbox-inline checkbox-toggle">
				<?php esc_html_e( 'Toggle All', 'wolmart' ); ?>
					<span type="checkbox" class="toggle"></span>
				</label>
			</div>
			<div class="wolmart-card-list">
				<?php
				foreach ( $elements as $element ) {
					?>
					<label class="checkbox checkbox-inline">
						<input type="checkbox" name="used_shortcode[<?php echo esc_attr( $element ); ?>]" 
							<?php
							disabled( in_array( $element, $used_shortcodes ) );
							checked( in_array( $element, $checked_shortcodes ) );
							?>
						class="element">
							<?php echo esc_html( $element ); ?>
					</label>
					<?php
				}
				?>
			</div>
		</div>
		<?php
	} else {
		?>
		<div class="wolmart-card <?php echo esc_attr( $group ); ?>">
			<div class="wolmart-card-header">
				<h3><?php echo esc_html( $used_mapping[ $group ]['title'] ); ?></h3>
				<label class="checkbox checkbox-inline checkbox-toggle">
				<?php esc_html_e( 'Toggle All', 'wolmart' ); ?>
					<span type="checkbox" class="toggle"></span>
				</label>
			</div>
			<div class="wolmart-card-list">
				<?php
				foreach ( $elements as $element ) {
					if ( 'helper' != $group || ! in_array( $element, $helper_classes ) ) {
						?>
						<label class="checkbox checkbox-inline">
							<input type="checkbox" name="used[<?php echo esc_attr( $element ); ?>]" 
								<?php
								disabled( true === $wolmart_used_elements[ $element ] );
								checked( $wolmart_used_elements[ $element ] );
								?>
							class="element">
								<?php echo esc_html( $element ); ?>
						</label>
						<?php
					}
				}
				?>
			</div>
		</div>
		<?php
	}
}
