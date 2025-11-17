<?php
/**
 * Wolmart Product Image Swatch Tab for Admin
 */
defined( 'ABSPATH' ) || die;

if ( ! class_exists( 'Wolmart_Image_Swatch_Tab' ) ) {
	class Wolmart_Image_Swatch_Tab {

		public function __construct() {
			add_filter( 'woocommerce_product_data_tabs', array( $this, 'add_product_data_tab' ), 99 );
			add_action( 'woocommerce_product_data_panels', array( $this, 'add_product_data_panel' ), 99 );
			add_action( 'woocommerce_process_product_meta', array( $this, 'save_product_meta' ), 1, 2 );

			add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_scripts' ), 1001 );
		}

		public function add_product_data_tab( $tabs ) {
			$tabs['swatch'] = array(
				'label'    => esc_html__( 'Image Change & Swatch', 'wolmart' ),
				'target'   => 'swatch_product_options',
				'class'    => array( 'show_if_variable' ),
				'priority' => 80,
			);
			return $tabs;
		}

		public function add_product_data_panel() {
			global $product_object;
			$attributes     = array_filter(
				$product_object->get_attributes(),
				function( $attr ) {
					return true === $attr->get_variation();
				}
			);
			$swatch_options = wc_get_product( $product_object->get_Id() )->get_meta( 'swatch_options', true );
			if ( $swatch_options ) {
				$swatch_options = json_decode( $swatch_options, true );
			}
			?>
			<div id="swatch_product_options" class="panel wc-metaboxes-wrapper woocommerce_options_panel hidden">
				<div class="wc-metaboxes">
				<?php
				if ( ! count( $attributes ) ) :
					?>

					<div id="message" class="inline notice wolmart-wc-message">
						<p><?php printf( esc_html__( 'Before you can add image swatch you need to add some %1$slist%2$s variations on the %1$sVariations%2$s tab.', 'wolmart' ), '<strong>', '</strong>' ); ?></p>
						<p><a class="button-primary" href="<?php echo esc_url( apply_filters( 'woocommerce_docs_url', 'https://docs.woocommerce.com/document/variable-product/', 'product-variations' ) ); ?>" target="_blank"><?php esc_html_e( 'Learn more', 'wolmart' ); ?></a></p>
					</div>

					<?php
				else :
					?>
					<div class="inline notice woocommerce-message"><p><?php esc_html_e( 'This will replace product image with following uploaded image when attribute button is clicked.', 'wolmart' ); ?></p></div>
					<?php
					foreach ( $attributes as $attribute ) :
						$attribute_obj = $attribute->get_taxonomy_object();
						$metabox_class = array();
						if ( $attribute->is_taxonomy() ) {
							$metabox_class[] = 'taxonomy';
							$metabox_class[] = $attribute->get_name();
						}

						$swatch_type = $swatch_options && isset( $swatch_options[ $attribute->get_name() ] ) ? $swatch_options[ $attribute->get_name() ]['type'] : 'image';
						?>
							<div data-taxonomy="<?php echo esc_attr( $attribute->get_taxonomy() ); ?>" class="woocommerce_attribute wc-metabox closed <?php echo esc_attr( implode( ' ', $metabox_class ) ); ?>" rel="<?php echo esc_attr( $attribute->get_position() ); ?>">
								<h3>
									<strong>
										<?php echo wc_attribute_label( $attribute->get_name() ); ?>
									</strong>
								</h3>
								<div class="woocommerce_attribute_data wc-metabox-content hidden">
									<p class="form-field">
										<label><?php esc_html_e( 'Button Type', 'wolmart' ); ?> </label>
										<select class="swatch-type" id="swatch_options[<?php echo esc_attr( $attribute->get_name() ); ?>][type]" name="swatch_options[<?php echo esc_attr( $attribute->get_name() ); ?>][type]">
											<option value="label" <?php selected( $swatch_type, 'label' ); ?>><?php esc_html_e( 'Default', 'wolmart' ); ?></option>
											<option value="image" <?php selected( $swatch_type, 'image' ); ?>><?php esc_html_e( 'Image', 'wolmart' ); ?></option>
										</select>
										<span class="woocommerce-help-tip" data-tip="<?php esc_attr_e( 'Select button type as image to show image on button.', 'wolmart' ); ?>"></span>
									</p>
									<table class="product_custom_swatches">
										<thead>
											<th><?php esc_html_e( 'Attribute', 'wolmart' ); ?></th>
											<th><?php esc_html_e( 'Image', 'wolmart' ); ?></th>
										</thead>

										<tbody>
										<?php
										foreach ( $attribute->get_options() as $option ) {
											$term = get_term( $option );
											if ( $term ) {
												$attr_label = $term->name;
											} else {
												$attr_label = $option;
												$option     = preg_replace( '/\s+/', '_', $option );
											}
											$src    = wc_placeholder_img_src();
											$src_id = $swatch_options && isset( $swatch_options[ $attribute->get_name() ] ) && isset( $swatch_options[ $attribute->get_name() ][ $option ] ) ? $swatch_options[ $attribute->get_name() ][ $option ] : '';
											if ( $src_id ) {
												$src = wp_get_attachment_image_src( $src_id )[0];
											}
											?>
												<tr>
													<td><?php echo esc_html( $attr_label ); ?></td>
													<td>
														<img src="<?php echo esc_url( $src ); ?>" alt="<?php esc_attr_e( 'Thumbnail Preview', 'wolmart' ); ?>" width="32" height="32">
														<input class="upload_image_url" type="hidden" name="swatch_options[<?php echo esc_attr( $attribute->get_name() ); ?>][<?php echo esc_attr( $option ); ?>]" value="<?php echo esc_attr( $src_id ); ?>" />
														<button class="button_upload_image button"><?php esc_html_e( 'Upload/Add image', 'wolmart' ); ?></button>
														<button class="button_remove_image button"><?php esc_html_e( 'Remove image', 'wolmart' ); ?></button>
													</td>
												</tr>
												<?php
										}
										?>
										</tbody>
									</table>
								</div>
							</div>
						<?php
					endforeach;
					?>
					<div class="toolbar">
						<span class="expand-close"><a href="#" class="expand_all" role="button"><?php esc_html_e( 'Expand', 'wolmart' ); ?></a> / <a href="#" class="close_all" role="button"><?php esc_html_e( 'Close', 'wolmart' ); ?></a></span>
						<button type="submit" class="button-primary wolmart-admin-save-changes" disabled="disabled"><?php esc_html_e( 'Save changes', 'wolmart' ); ?></button>
						<button type="reset" class="button wolmart-admin-cancel-changes" disabled="disabled"><?php esc_html_e( 'Cancel', 'wolmart' ); ?></button>
					</div>
					<?php
				endif;
				?>
				</div>
			</div>
			<?php
		}

		public function enqueue_scripts() {
			wp_enqueue_media();

			wp_enqueue_script( 'wolmart-swatch-admin', WOLMART_ADDONS_URI . '/product-image-swatch/product-image-swatch-admin' . ( defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ? '.js' : '.min.js' ), array(), WOLMART_VERSION, true );

			wp_localize_script(
				'wolmart-swatch-admin',
				'lib_swatch_admin',
				array(
					'placeholder'      => esc_js( wc_placeholder_img_src() ),
					'file_frame_title' => __( 'Choose an image', 'wolmart' ),
					'file_frame_btn'   => __( 'Use image', 'wolmart' ),
				)
			);
		}

		public function save_product_meta( $post_id, $post ) {

			if ( 'variable' != $_POST['product-type'] ) {
				return;
			}

			$product = wc_get_product( $post_id );

			$swatch_options = isset( $_POST['swatch_options'] ) ? $_POST['swatch_options'] : false;

			if ( $swatch_options && is_array( $swatch_options ) ) {
				$product->update_meta_data( 'swatch_options', json_encode( $swatch_options ) );
			} else {
				$product->delete_meta_data( 'swatch_options' );
			}

			$product->save_meta_data();
		}
	}
}

new Wolmart_Image_Swatch_Tab;
