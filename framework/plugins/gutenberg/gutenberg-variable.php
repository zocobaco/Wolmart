<?php
if ( ! function_exists( 'wolmart_selective_typography' ) ) {
	function wolmart_selective_typography( $id, $typo ) {
		$style = '';
		if ( isset( $typo['font-family'] ) && 'inherit' != $typo['font-family'] ) {
			$style .= ' --' . $id . '-font-family: ' . '"' . $typo['font-family'] . '", sans-serif;';
		}
		if ( isset( $typo['variant'] ) ) {
			$style .= ' --' . $id . '-font-weight: ' . ( 'regular' == $typo['variant'] ? 400 : $typo['variant'] ) . ';';
		}
		if ( isset( $typo['font-size'] ) && '' != $typo['font-size'] ) {
			$style .= ' --' . $id . '-font-size: ' . $typo['font-size'] . ';';
		}
		if ( isset( $typo['line-height'] ) && '' != $typo['line-height'] ) {
			$style .= ' --' . $id . '-line-height: ' . $typo['line-height'] . ';';
		}
		if ( isset( $typo['letter-spacing'] ) && '' != $typo['letter-spacing'] ) {
			$style .= ' --' . $id . '-letter-spacing: ' . $typo['letter-spacing'] . ';';
		}
		if ( isset( $typo['text-transform'] ) && '' != $typo['text-transform'] ) {
			$style .= ' --' . $id . '-text-transform: ' . $typo['text-transform'] . ';';
		}
		if ( isset( $typo['color'] ) && '' != $typo['color'] ) {
			$style .= ' --' . $id . '-color: ' . $typo['color'] . ';';
		}
		return $style;
	}
}

if ( ! function_exists( 'wolmart_selective_bg' ) ) {
	function wolmart_selective_bg( $id, $bg ) {
		$style = '';
		if ( isset( $bg['background-color'] ) ) {
			$style .= ' --' . $id . '-background-color: ' . '"' . $bg['background-color'] . '";';
		}
		if ( isset( $bg['background-image'] ) ) {
			$style .= ' --' . $id . '-background-image: ' . '"' . $bg['background-image'] . '";';

			if ( isset( $bg['backgorund-repeat'] ) ) {
				$style .= ' --' . $id . '-background-repeat: ' . '"' . $bg['background-repeat'] . '";';
			}

			if ( isset( $bg['backgorund-position'] ) ) {
				$style .= ' --' . $id . '-background-position: ' . '"' . $bg['background-position'] . '";';
			}

			if ( isset( $bg['backgorund-size'] ) ) {
				$style .= ' --' . $id . '-background-size: ' . '"' . $bg['background-size'] . '";';
			}

			if ( isset( $bg['backgorund-attachment'] ) ) {
				$style .= ' --' . $id . '-background-attachment: ' . '"' . $bg['background-attachment'] . '";';
			}
		}
		return $style;
	}
}

if ( ! function_exists( 'wolmart_selective_styles' ) ) {
	function wolmart_selective_styles() {
		$dyna_vars = array(
			'primary-color'   => 'primary_color',
			'secondary-color' => 'secondary_color',
			'dark-color'      => 'dark_color',
			'light-color'     => 'light_color',
		);
		$style     = '';
		foreach ( $dyna_vars as $key => $item ) {
			$style .= '--' . $key . ': ' . wolmart_get_option( $item ) . ';';
		}
		return $style;
	}
}

echo 'html {
	--container-width: ' . wolmart_get_option( 'container' ) . 'px;
	--container-fluid-width: ' . wolmart_get_option( 'container_fluid' ) . 'px;
	' . wolmart_selective_typography( 'heading', wolmart_get_option( 'typo_heading' ) ) . '
	' . wolmart_selective_typography( 'body', wolmart_get_option( 'typo_default' ) ) . wolmart_selective_styles() . wolmart_selective_bg( 'page-wrapper', wolmart_get_option( 'content_bg' ) ) . '
}';


global $wolmart_layout;
if ( ! $wolmart_layout['wrap'] || 'container' == $wolmart_layout['wrap'] ) {
	echo '.edit-post-visual-editor {
		max-width: ' . wolmart_get_option( 'container' ) . 'px;
		width: 100%;
		margin: 0 auto;
		padding: 0 2rem;
		background: none;
	}
	.edit-post-visual-editor .is-root-container > [data-align="wide"].wp-block {
		max-width: 100%;
		padding-left: 0;
		padding-right: 0;
	}';
} elseif ( 'container_fluid' == $wolmart_layout['wrap'] ) {
	echo '.edit-post-visual-editor {
		max-width: ' . wolmart_get_option( 'container_fluid' ) . 'px;
		width: 100%;
		padding: 0 2rem;
		margin: 0 auto;
	}
	.edit-post-visual-editor .is-root-container > [data-align="wide"].wp-block {
		max-width: 100%;
		padding-left: 0;
		padding-right: 0;
	}';
}
