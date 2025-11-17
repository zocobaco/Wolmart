<?php
/**
 * Wolmart Studio Blocks Wrapper Template
 *
 * @package Wolmart WordPress Framework
 * @subpackage Wolmart Add-Ons
 * @since 1.0
 */
defined( 'ABSPATH' ) || die;

$is_ajax = wolmart_doing_ajax();

if ( isset( $_REQUEST['active-document'] ) ) {
	$post_id = intval( $_REQUEST['active-document'] );
} elseif ( isset( $_REQUEST['post'] ) ) {
	$post_id = intval( $_REQUEST['post'] );
}
$template_type = get_post_meta( $post_id, 'wolmart_template_type', true );
?>
<script type="text/template" id="wolmart_studio_blocks_wrapper_template">
	<div class="blocks-overlay closed"></div>
	<div class="blocks-wrapper closed">
		<button title="<?php esc_attr_e( 'Close (Esc)', 'wolmart' ); ?>" aria-label="<?php esc_attr_e( 'Close (Esc)', 'wolmart' ); ?>" type="button" class="mfp-close">&times;</button>
		<div class="category-list">
			<h3><img src="<?php echo WOLMART_URI; ?>/assets/images/logo-studio.png" alt="<?php esc_attr_e( 'Wolmart Studio', 'wolmart' ); ?>" width="206" height="73" /></h3>
			<ul>
				<li class="filtered"><a href="#" data-filter-by="0" data-total-page="<?php echo (int) $args['total_pages']; ?>" aria-label="<?php esc_attr_e( 'Default Category', 'wolmart' ); ?>"></a></li>
				<li>
					<a href="#" class="all active">
						<img src="<?php echo WOLMART_URI; ?>/assets/images/studio/icon-all.svg">
						<?php esc_html_e( 'All', 'wolmart' ); ?>
						<span>(<?php echo (int) $args['total_count']; ?>)</span>
					</a>
				</li>
				<li class="category-has-children">
					<a href="#" class="block-category-blocks" data-filter-by="blocks" data-total-page="<?php echo (int) $args['blocks_pages']; ?>">
						<img src="<?php echo WOLMART_URI; ?>/assets/images/studio/icon-block.svg">
						<?php esc_html_e( 'Blocks', 'wolmart' ); ?><i class="w-icon-chevron-down"></i>
					</a>
					<ul>
						<?php
						foreach ( $args['block_categories'] as $category ) :
							if ( ! in_array( $category['title'], $args['big_categories'] ) && $category['count'] > 0 ) :
								?>
								<li>
									<a href="#" data-filter-by="<?php echo (int) $category['id']; ?>" data-total-page="<?php echo (int) ( $category['total'] ); ?>">
										<?php echo esc_html( $args['studio']->get_category_title( $category['title'] ) ); ?>
									</a>
								</li>
								<?php
							endif;
						endforeach;
						?>
					</ul>
				</li>
				<?php
				foreach ( $args['big_categories'] as $big_category ) :
					$not_found = true;
					foreach ( $args['block_categories'] as $category ) :
						if ( $category['title'] == $big_category ) :
							?>
							<li>
								<a href="#" class="block-category-<?php echo esc_attr( $category['title'] ); ?>" data-filter-by="<?php echo esc_attr( $category['id'] ); ?>" data-total-page="<?php echo (int) ( $category['total'] ); ?>">
									<img src="<?php echo WOLMART_URI; ?>/assets/images/studio/icon-<?php echo esc_attr( $big_category ); ?>.svg">
									<?php
									echo esc_html( $args['studio']->get_category_title( $category['title'] ) );
									if ( 'favourites' == $big_category || 'my-templates' == $big_category ) {
										echo '<span>(' . (int) $category['count'] . ')</span>';
									}
									?>
								</a>
							</li>
							<?php
							$not_found = false;
							break;
						endif;
					endforeach;
					if ( $not_found ) :
						?>
						<li>
							<a href="#" class="block-category-<?php echo esc_attr( $big_category ); ?>">
								<img src="<?php echo WOLMART_URI; ?>/assets/images/studio/icon-<?php echo esc_attr( $big_category ); ?>.svg">
								<?php echo esc_html( $args['studio']->get_category_title( $big_category ) ); ?>
							</a>
						</li>
						<?php
					endif;
				endforeach;
				?>
			</ul>
		</div>
		<div class="blocks-section">
			<div class="blocks-section-inner">
				<div class="blocks-row">
					<div class="blocks-title">
						<h3><?php esc_html_e( 'All in One Library', 'wolmart' ); ?></h3>
						<p><?php esc_html_e( 'Choose any type of template from our library.', 'wolmart' ); ?></p>
					</div>
					<div class="demo-filter">
						<div class="layout-buttons">
							<button class="layout-2" data-column="2" aria-label="<?php esc_attr_e( 'Columns', 'wolmart' ); ?>"></button>
							<button class="layout-3 active" data-column="3" aria-label="<?php esc_attr_e( 'Columns', 'wolmart' ); ?>"></button>
							<button class="layout-4" data-column="4" aria-label="<?php esc_attr_e( 'Columns', 'wolmart' ); ?>"></button>
						</div>
						<?php
						if ( ! class_exists( 'Wolmart_Setup_Wizard' ) ) {
							require_once WOLMART_ADMIN . '/wizard/setup_wizard/setup_wizard.php';
						}
						$instance = Wolmart_Setup_Wizard::get_instance();
						$filters  = $instance->wolmart_demo_types();
						?>
						<div class="custom-select">
							<select class="filter-select">
								<option value=""><?php esc_html_e( 'Select Demo', 'wolmart' ); ?></option>
								<?php foreach ( $filters as $name => $value ) : ?>
									<?php
									if ( ! empty( $value['editors'] ) && (
										( 'v' == $args['page_type'] && in_array( 'visualcomposer', $value['editors'] ) ) ||
										( 'e' == $args['page_type'] && in_array( 'elementor', $value['editors'] ) ) ||
										( 'w' == $args['page_type'] && in_array( 'js_composer', $value['editors'] ) ) ) ) :
										?>
										<option value="<?php echo esc_attr( $name ); ?>" data-filter="<?php echo esc_attr( $value['filter'] ); ?>"><?php echo esc_html( $value['alt'] ); ?></option>
									<?php endif; ?>
								<?php endforeach; ?>
							</select>
						</div>
						<button class="btn btn-primary" disabled="disabled"><?php esc_html_e( 'Filter', 'wolmart' ); ?></button>
					</div>
				</div>
					<?php if ( ! $is_ajax ) : ?>
					<div class="block-categories">
						<a href="#" class="block-category" data-category="blocks">
							<h4><?php esc_html_e( 'Blocks', 'wolmart' ); ?></h4>
							<img src="<?php echo WOLMART_URI; ?>/assets/images/studio/block.svg">
						</a>
						<?php
						foreach ( $args['big_categories'] as $big_category ) {
							?>
							<a href="#" class="block-category" data-category="<?php echo esc_attr( $big_category ); ?>">
								<h4><?php echo esc_html( $args['studio']->get_category_title( $big_category ) ); ?></h4>
								<img src="<?php echo WOLMART_URI; ?>/assets/images/studio/<?php echo esc_attr( $big_category ); ?>.svg">
							</a>
							<?php
						}
						?>
					</div>
				<?php endif; ?>
				<div class="blocks-list column-4"></div>
				<div class="wolmart-loading"><i></i></div>
			</div>
		</div>
		<div class="wolmart-loading"><i></i></div>
	</div>
	<?php if ( ! isset( $template_type ) || 'type' != $template_type ) : ?>
	<div class="layout-builder closed">
		<button title="<?php esc_attr_e( 'Close (Esc)', 'wolmart' ); ?>" aria-label="<?php esc_attr_e( 'Close (Esc)', 'wolmart' ); ?>" type="button" class="mfp-close">&times;</button>
		<?php
		if ( isset( $template_type ) ) {
			if ( 'product_layout' == $template_type ) {
				$template_type = 'single_product';
			} elseif ( 'shop_layout' == $template_type ) {
				$template_type = 'archive_product';
			}
		}
		?>
		<iframe src="<?php echo esc_url( admin_url( 'admin.php?page=wolmart-layout-builder&is_elementor_preview=true&noheader=true' . ( isset( $post_id ) ? ( '&post=' . $post_id ) : '' ) . ( ! empty( $template_type ) ? ( '&layout=' . $template_type ) : '' ) ) ); ?>"></iframe>
	</div>
	<?php endif; ?>
</script>
