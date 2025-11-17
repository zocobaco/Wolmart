<?php
defined( 'ABSPATH' ) || die;
update_option( 'wolmart_setup_complete', time() );
?>
<h2><?php esc_html_e( 'Your Website is Ready!', 'wolmart' ); ?></h2>

<p><?php esc_html_e( 'Congratulations! The theme has been activated and your website is ready. Please go to your WordPress dashboard to make changes and modify the content for your needs.', 'wolmart' ); ?></p>

<p><?php esc_html_e( 'This theme comes with 6 months item support from purchase date (with the option to extend this period). This license allows you to use this theme on a single website. Please purchase an additional license to use this theme on another website.', 'wolmart' ); ?></p>
<div class="wolmart-admin-panel-row">
	<div class="wolmart-support">
		<?php /* translators: $1 and $2 opening and closing strong tags respectively */ ?>
		<h4 class="success system-status"><i class="circle w-icon-check2"></i> <?php printf( esc_html__( 'Item Support %1$sDOES%2$s Include:', 'wolmart' ), '<strong class="success">', '</strong>' ); ?></h4>

		<ul class="list">
			<li><?php esc_html_e( 'Availability of the author to answer questions', 'wolmart' ); ?></li>
			<li><?php esc_html_e( 'Answering technical questions about item features', 'wolmart' ); ?></li>
			<li><?php esc_html_e( 'Assistance with reported bugs and issues', 'wolmart' ); ?></li>
			<li><?php esc_html_e( 'Help with bundled 3rd party plugins', 'wolmart' ); ?></li>
		</ul>
	</div>
	<div class="wolmart-support">
		<?php /* translators: $1 and $2 opening and closing strong tags respectively */ ?>
		<h4 class="error system-status"><i class="circle w-icon-ban"></i> <?php printf( esc_html__( 'Item Support %1$sDOES NOT%2$s Include:', 'wolmart' ), '<strong class="error">', '</strong>' ); ?></h4>
		<ul class="list">
			<li><?php printf( esc_html__( 'Customization services (this is available through %1$sptheme.customize@gmail.com%2$s)', 'wolmart' ), '<a href="mailto:ptheme.customize@gmail.com">', '</a>' ); ?></li>
			<li><?php printf( esc_html__( 'Installation services (this is available through %1$sptheme.customize@gmail.com%2$s)', 'wolmart' ), '<a href="mailto:ptheme.customize@gmail.com">', '</a>' ); ?></li>
			<li><?php esc_html_e( 'Help and Support for non-bundled 3rd party plugins (i.e. plugins you install yourself later on)', 'wolmart' ); ?></li>
		</ul>
	</div>
</div>
<?php /* translators: $1 and $2 opening and closing anchor tags respectively */ ?>
<p class="info-qt light-info"><?php printf( esc_html__( 'More details about item support can be found in the ThemeForest %1$sItem Support Policy%2$s.', 'wolmart' ), '<a href="http://themeforest.net/page/item_support_policy" target="_blank">', '</a>' ); ?></p>
<br>
<div class="wolmart-setup-next-steps">
	<div class="wolmart-setup-next-steps-first">
		<h4><?php esc_html_e( 'More Resources', 'wolmart' ); ?></h4>
		<ul style="margin-bottom:40px;">
			<li class="documentation"><a href="https://d-themes.com/wordpress/wolmart/documentation"><?php esc_html_e( 'Wolmart Documentation', 'wolmart' ); ?></a></li>
			<li class="woocommerce documentation"><a href="https://docs.woocommerce.com/document/woocommerce-101-video-series/"><?php esc_html_e( 'Learn how to use WooCommerce', 'wolmart' ); ?></a></li>
			<li class="howto" style="font-style: normal;"><a href="https://wordpress.org/support/"><?php esc_html_e( 'Learn how to use WordPress', 'wolmart' ); ?></a></li>
			<li class="rating"><a href="http://themeforest.net/downloads"><?php esc_html_e( 'Leave an Item Rating', 'wolmart' ); ?></a></li>
		</ul>
		<?php /* translators: $1 and $2 opening and closing anchor tags respectively */ ?>
		<p class="info-qt light-info"><?php printf( esc_html__( 'Please come back and leave a %1$s5-star rating%2$s if you are happy with this theme. Thanks!', 'wolmart' ), '<a href="http://themeforest.net/downloads" target="_blank">', '</a>' ); ?></p>
	</div>
	<div class="wolmart-admin-panel-actions">
		<a class="button button-large button-dark button-next" href="<?php echo esc_url( home_url() ); ?>"><?php esc_html_e( 'View your new website!', 'wolmart' ); ?></a>
	</div>
</div>
