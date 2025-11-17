<?php
/**
 * This template is used to display featured video in single product page.
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

global $product;
wp_enqueue_script( 'ywcfav_video' );

$aspect_ratio      = '_' . get_option( 'ywcfav_aspectratio', '4_3' );
$gallery_thumbnail = wc_get_image_size( 'gallery_thumbnail' );
$thumbnail_size    = apply_filters( 'woocommerce_gallery_thumbnail_size', array( $gallery_thumbnail['width'], $gallery_thumbnail['height'] ) );
$thumbnail_url     = wp_get_attachment_image_src( $thumbnail_id, $thumbnail_size );
$thumbnail_url     = isset( $thumbnail_url[0] ) ? $thumbnail_url[0] : '';

if ( 'youtube' === $host ) {
	$video_class = 'youtube';
	$url         = 'https://www.youtube.com/embed/' . $video_id . '/?enablejsapi=1&origin=' . get_site_url();
} elseif ( 'vimeo' === $host ) {
	$video_class = 'vimeo';
	$url         = '//player.vimeo.com/video/' . $video_id;
} else {
	$video_class = 'other';
	$url         = 'https://' . $host . $video_id;
}

$gallery_item_class = ywcfav_get_gallery_item_class()
?>
<div class="<?php echo esc_attr( $gallery_item_class ); ?> yith_featured_content" data-thumb="<?php echo esc_attr( $thumbnail_url ); ?>">
	<div class="ywcfav-video-content <?php echo esc_attr( $video_class . ' ' . $aspect_ratio ); ?>">
		<?php if ( ! 'other' === $video_class ) : ?>
			<iframe id="video_<?php echo esc_attr( $product->get_id() ); ?>" src="<?php echo esc_url( $url ); ?>" type="text/html" frameborder="0" allowfullscreen></iframe>
		<?php else : ?>
			<video id="video_<?php echo esc_attr( $product->get_id() ); ?>" src="<?php echo esc_url( $url ); ?>" controls></video>
		<?php endif; ?>
	</div>
</div>
