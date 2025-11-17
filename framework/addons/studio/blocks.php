<?php
/**
 * Wolmart Studio Blocks List Template
 *
 * @package Wolmart WordPress Framework
 * @subpackage Wolmart Add-Ons
 * @since 1.0
 */
defined( 'ABSPATH' ) || die;

foreach ( $args['blocks'] as $block ) :
	if ( $block instanceof WP_Post ) :
		$template_type = get_post_meta( $block->ID, 'wolmart_template_type', true );
		?>
		<div class="block block-template">
			<img src="<?php echo WOLMART_URI; ?>/assets/images/studio/<?php echo esc_attr( $template_type ); ?>.svg">
			<h5 class="block-title"><?php echo esc_html( $block->post_title ); ?></h5>
			<div class="block-actions" data-id="<?php echo esc_attr( $block->ID ); ?>" data-category="<?php echo esc_attr( $template_type ); ?>">
				<button class="btn btn-primary <?php echo boolval( $args['studio']->new_template_mode ) ? 'select' : 'import'; ?>">
					<i class="w-icon-download3"></i>
					<?php $args['studio']->new_template_mode ? esc_html_e( 'Select', 'wolmart' ) : esc_html_e( 'Import', 'wolmart' ); ?>
				</button>
			</div>
		</div>
		<?php
	else :
		$class = 'block block-online';
		// if ( isset( $block['w'] ) && 800 == (int) $block['w'] ) {
		// 	$class .= ' width-2';
		// }
		// if ( isset( $block['h'] ) && ( 80 >= (int) $block['h'] || 500 <= (int) $block['h'] ) ) {
		// 	$class .= ' center';
		// }
		if ( isset( $args['favourites_map'][ $block['block_id'] ] ) ) {
			$class .= ' favour';
		}
		?>
		<div class="<?php echo esc_attr( $class ); ?>">
			<img src="<?php echo esc_url( 'https://d-themes.com/wordpress/wolmart/dummy/images/studio/' . intval( isset( $block['s'] ) ? $block['s'] : $block['block_id'] ) . '.jpg' ); ?>" alt="<?php echo esc_attr( $block['t'] ); ?>"<?php echo isset( $block['w'] ) && $block['w'] ? ' width="' . intval( $block['w'] ) . '"' : '', isset( $block['h'] ) && $block['h'] ? ' height="' . intval( $block['h'] ) . '"' : ''; ?>>
			<h5 class="block-title"><?php echo esc_html( $block['t'] ); ?></h5>
			<div class="block-actions" data-id="<?php echo esc_attr( $block['block_id'] ); ?>" data-category="<?php echo esc_attr( $block['c'] ); ?>">
				<button class="btn btn-dark favourite"><i class="w-icon-heart3"></i><?php esc_html_e( 'Favourite', 'wolmart' ); ?></button>
				<?php if ( ( function_exists( 'Wolmart' ) && Wolmart()->is_registered() || get_option( 'wolmart_registered' ) ) ) : ?>
					<button class="btn btn-primary <?php echo boolval( $args['studio']->new_template_mode ) ? 'select' : 'import'; ?>">
						<i class="w-icon-download3"></i>
						<?php $args['studio']->new_template_mode ? esc_html_e( 'Select', 'wolmart' ) : esc_html_e( 'Import', 'wolmart' ); ?>
					</button>
				<?php endif; ?>
			</div>
		</div>
		<?php
	endif;
endforeach;
