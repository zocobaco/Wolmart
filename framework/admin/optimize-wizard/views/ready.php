<?php
defined( 'ABSPATH' ) || die;
update_option( 'wolmart_setup_complete', time() );
?>
<h2><?php esc_html_e( 'Your Website is now optimized much better than before!', 'wolmart' ); ?></h2>
<p class="lead success"><?php esc_html_e( 'Congratulations! The Site is now much faster, better and fully optimized. Please visit your new site to notice how its performance changed.', 'wolmart' ); ?></p>
<p style="margin: 0 0 50px;"><a target="_blank" href="<?php echo esc_url( home_url() ); ?>"><?php esc_html_e( 'View your optimized website!', 'wolmart' ); ?></a></p>
<?php /* translators: opening and closing a tag */ ?>
<p class="info-qt light-info" style="margin-top: 20px;"><?php printf( esc_html__( 'Please leave a %1$s5-star rating%2$s if you are satisfied with this theme. Thanks!', 'wolmart' ), '<a href="http://themeforest.net/downloads" target="_blank">', '</a>' ); ?></p>
