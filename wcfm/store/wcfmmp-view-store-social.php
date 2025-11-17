<?php
/**
 * The Template for displaying all store social
 *
 * @package WCfM Markeplace Views Store Social
 *
 * For edit coping this to yourtheme/wcfm/store
 *
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

global $WCFM, $WCFMmp;
?>

<ul class="social-icons">
	<?php do_action( 'wcfmmp_store_before_social', $store_user->get_id() ); ?>

	<?php if ( isset( $store_info['social']['fb'] ) && ! empty( $store_info['social']['fb'] ) ) { ?>
		<li><a class="social-icon stacked social-facebook" href="<?php echo wcfmmp_generate_social_url( $store_info['social']['fb'], 'facebook' ); ?>" target="_blank" aria-label="<?php esc_attr_e( 'facebook', 'wolmart' ); ?>"><i class="w-icon-facebook" aria-hidden="true"></i></a></li>
	<?php } ?>
	<?php if ( isset( $store_info['social']['twitter'] ) && ! empty( $store_info['social']['twitter'] ) ) { ?>
		<li><a class="social-icon stacked social-twitter" href="<?php echo wcfmmp_generate_social_url( $store_info['social']['twitter'], 'twitter' ); ?>" target="_blank" aria-label="<?php esc_attr_e( 'twitter', 'wolmart' ); ?>"><i class="w-icon-twitter" aria-hidden="true"></i></a></li>
	<?php } ?>
	<?php if ( isset( $store_info['social']['linkedin'] ) && ! empty( $store_info['social']['linkedin'] ) ) { ?>
		<li><a class="social-icon stacked social-linkedin" href="<?php echo wcfmmp_generate_social_url( $store_info['social']['linkedin'], 'linkedin' ); ?>" target="_blank" aria-label="<?php esc_attr_e( 'linkedin', 'wolmart' ); ?>"><i class="w-icon-linkedin-in" aria-hidden="true"></i></a></li>
	<?php } ?>
	<?php if ( isset( $store_info['social']['instagram'] ) && ! empty( $store_info['social']['instagram'] ) ) { ?>
		<li><a class="social-icon stacked social-instagram" href="<?php echo wcfmmp_generate_social_url( $store_info['social']['instagram'], 'instagram' ); ?>" target="_blank" aria-label="<?php esc_attr_e( 'instagram', 'wolmart' ); ?>"><i class="w-icon-instagram" aria-hidden="true"></i></a></li>
	<?php } ?>
	<?php if ( isset( $store_info['social']['pinterest'] ) && ! empty( $store_info['social']['pinterest'] ) ) { ?>
		<li><a class="social-icon stacked social-pinterest" href="<?php echo wcfmmp_generate_social_url( $store_info['social']['pinterest'], 'pinterest' ); ?>" target="_blank" aria-label="<?php esc_attr_e( 'pinterest', 'wolmart' ); ?>"><i class="w-icon-pinterest" aria-hidden="true"></i></a></li>
	<?php } ?>
	<?php if ( isset( $store_info['social']['youtube'] ) && ! empty( $store_info['social']['youtube'] ) ) { ?>
		<li><a class="social-icon stacked social-youtube" href="<?php echo wcfmmp_generate_social_url( $store_info['social']['youtube'], 'youtube' ); ?>" target="_blank" aria-label="<?php esc_attr_e( 'youtube', 'wolmart' ); ?>"><i class="w-icon-youtube-solid" aria-hidden="true"></i></a></li>
	<?php } ?>
	<?php if ( isset( $store_info['social']['snapchat'] ) && ! empty( $store_info['social']['snapchat'] ) ) { ?>
		<li><a class="social-icon stacked social-snapchat" href="<?php echo wcfmmp_generate_social_url( $store_info['social']['snapchat'], 'snapchat' ); ?>" target="_blank" aria-label="<?php esc_attr_e( 'snapchat', 'wolmart' ); ?>"><i class="w-icon-snapchat" aria-hidden="true"></i></a></li>
	<?php } ?>

	<?php do_action( 'wcfmmp_store_after_social', $store_user->get_id() ); ?>
</ul>

