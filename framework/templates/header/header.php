<?php
/**
 * Header content template
 *
 * @package Wolmart WordPress Framework
 * @since 1.0
 */

defined( 'ABSPATH' ) || die;

global $wolmart_layout;

if ( 'wolmart_template' == get_post_type() && 'header' == get_post_meta( get_the_ID(), 'wolmart_template_type', true ) && 'publish' == get_post_status() ) {
	/**
	 * View Header Template
	 */
	echo '<header class="header custom-header header-' . get_the_ID() . '" id="header">';

	if ( have_posts() ) :
		the_post();
			the_content();
		wp_reset_postdata();
	endif;

	echo '</header>';

} elseif ( ! empty( $wolmart_layout['header'] ) && 'elementor_pro' == $wolmart_layout['header'] ) {

	/**
	 * Elementor Pro Header
	 */
	do_action( 'wolmart_elementor_pro_header_location' );

} elseif ( ! empty( $wolmart_layout['header'] ) && 'hide' == $wolmart_layout['header'] ) {

	// Hide

} elseif ( ! empty( $wolmart_layout['header'] ) && 'publish' == get_post_status( intval( $wolmart_layout['header'] ) ) ) {

	/**
	 * Custom Block Header
	 */
	echo '<header class="header custom-header header-' . intval( $wolmart_layout['header'] ) . '" id="header">';
	wolmart_print_template( $wolmart_layout['header'] );
	echo '</header>';

} else {
	/**
	 * Default Header
	 */
	?>
	<header class="header pt-5 pb-5 default-header" id="header">
		<div class="container d-flex align-items-center">
			<a href="<?php echo esc_url( home_url() ); ?>" class="<?php echo is_rtl() ? 'ml-4' : 'mr-4'; ?>" aria-label="<?php esc_attr_e( 'Site Logo', 'wolmart' ); ?>">
				<?php if ( wolmart_get_option( 'custom_logo' ) ) : ?>
					<img class="logo" src="<?php echo esc_url( str_replace( array( 'http:', 'https:' ), '', wp_get_attachment_url( wolmart_get_option( 'custom_logo' ) ) ) ); ?>" alt="<?php echo esc_attr( get_bloginfo( 'name', 'display' ) ); ?>">
				<?php else : ?>
					<img class="logo" src="<?php echo WOLMART_ASSETS . '/images/logo.png'; ?>" width="144" height="45" alt="<?php echo esc_attr__( 'Logo', 'wolmart' ); ?>"/>
				<?php endif; ?>
			</a>
			<?php
			if ( has_nav_menu( 'main-menu' ) ) {
				ob_start();
				wp_nav_menu(
					array(
						'theme_location'  => 'main-menu',
						'container'       => 'nav',
						'container_class' => 'main-menu d-none d-lg-block ml-auto',
						'items_wrap'      => '<ul id="%1$s" class="menu menu-main-menu default-menu">%3$s</ul>',
						'walker'          => new Wolmart_Walker_Nav_Menu(),
					)
				);
				$nav_html_escaped = ob_get_clean();
				if ( $nav_html_escaped ) {
					if ( wolmart_get_option( 'mobile_menu_items' ) ) {
						echo '<a href="#" class="mobile-menu-toggle d-show-mob" aria-label="' . esc_html__( 'Mobile Menu Toggle', 'wolmart' ) . '" role="button
						"><i class="w-icon-hamburger"></i></a>';
					}

					echo wolmart_escaped( $nav_html_escaped );
				}
			}
			?>
		</div>
	</header>
	<?php
}

