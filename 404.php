<?php
/**
 * Error 404 page template
 *
 * @package Wolmart WordPress Framework
 * @since 1.0
 */
defined( 'ABSPATH' ) || die;

get_header();
do_action( 'wolmart_before_content' );
?>

<div class="page-content">

	<?php
	do_action( 'wolmart_print_before_page_layout' );

	global $wolmart_layout;

	if ( ! empty( $wolmart_layout['error_block'] ) && 'hide' != $wolmart_layout['error_block'] ) {

		wolmart_print_template( $wolmart_layout['error_block'] );

	} elseif ( empty( $wolmart_layout['error_block'] ) || 'hide' != $wolmart_layout['error_block'] ) {

		?>
		<div class="area_404">
			<div class="img-area ml-auto mr-auto"></div>
			<h1 class="mt-8 mb-2 pt-3 pl-4 pr-4 ls-normal font-weight-bold text-capitalize">
				<span class="text-secondary"><?php echo esc_html_e( 'Oops!!!', 'wolmart' ); ?></span>
				<?php esc_html_e( 'Something Went Wrong Here', 'wolmart' ); ?>
			</h1>
			<p class="ls-normal mb-7 pl-4 pr-4"><?php esc_html_e( 'There may be a misspelling in the URL entered, or the page you are looking for may no longer exist', 'wolmart' ); ?></p>
			<a href="<?php echo esc_url( home_url() ); ?>" class="btn btn-dark btn-rounded btn-icon-right mb-3"><?php esc_html_e( 'Go Back Home', 'wolmart' ); ?><i class="w-icon-long-arrow-right"></i></a>
		</div>
		<?php
	}

	do_action( 'wolmart_print_after_page_layout' );

	?>

</div>

<?php
do_action( 'wolmart_after_content' );
get_footer();
