<?php
/**
 * Theme SCSS Builder
 *
 * @package Wolmart WordPress Framework
 * @since 1.0
 */
defined( 'ABSPATH' ) || die;

function wolmart_customizer_background( $bg ) {
	$res = '';

	if ( is_array( $bg ) ) {
		if ( isset( $bg['background-color'] ) && $bg['background-color'] ) {
			$res .= 'background-color: ' . $bg['background-color'] . ',' . PHP_EOL;
		} else {
			$res .= 'background-color: transparent,' . PHP_EOL;
		}

		if ( isset( $bg['background-image'] ) && $bg['background-image'] ) {
			$res .= 'background-image: url(' . $bg['background-image'] . '),' . PHP_EOL;

			if ( isset( $bg['background-repeat'] ) && $bg['background-repeat'] ) {
				$res .= 'background-repeat: ' . $bg['background-repeat'] . ',' . PHP_EOL;
			}
			if ( isset( $bg['background-position'] ) && $bg['background-position'] ) {
				$res .= 'background-position: ' . $bg['background-position'] . ',' . PHP_EOL;
			}
			if ( isset( $bg['background-size'] ) && $bg['background-size'] ) {
				$res .= 'background-size: ' . $bg['background-size'] . ',' . PHP_EOL;
			}
			if ( isset( $bg['background-attachment'] ) && $bg['background-attachment'] ) {
				$res .= 'background-attachment: ' . $bg['background-attachment'] . ',' . PHP_EOL;
			}
		} else {
			$res .= 'background-image: none,' . PHP_EOL;
		}
	}

	return $res;
}

function wolmart_customizer_typography( $typo, $allow_inherit = false ) {
	$res = '';

	if ( is_array( $typo ) ) {
		if ( isset( $typo['font-family'] ) && 'inherit' != $typo['font-family'] ) {
			$res .= 'font-family: "' . "'" . sanitize_text_field( $typo['font-family'] ) . "'" . ', sans-serif",' . PHP_EOL;

			if ( isset( $typo['variant'] ) && $typo['variant'] ) {
				$res .= 'font-weight: ' . ( 'regular' == $typo['variant'] ? 400 : (int) $typo['variant'] ) . ',' . PHP_EOL;
			}
		} elseif ( $allow_inherit ) {
			$res .= 'font-family: inherit,' . PHP_EOL;
		}
		if ( isset( $typo['font-size'] ) && '' != $typo['font-size'] ) {
			$res .= 'font-size: ' . ( is_numeric( $typo['font-size'] ) ? ( (int) $typo['font-size'] . 'px' ) : esc_attr( $typo['font-size'] ) ) . ',' . PHP_EOL;
		}
		if ( isset( $typo['line-height'] ) && '' != $typo['line-height'] ) {
			$res .= 'line-height: ' . esc_attr( $typo['line-height'] ) . ',' . PHP_EOL;
		}
		if ( isset( $typo['letter-spacing'] ) && '' != $typo['letter-spacing'] ) {
			$res .= 'letter-spacing: ' . esc_attr( $typo['letter-spacing'] ) . ',' . PHP_EOL;
		}
		if ( isset( $typo['text-transform'] ) && '' != $typo['text-transform'] ) {
			$res .= 'text-transform: ' . esc_attr( $typo['text-transform'] ) . ',' . PHP_EOL;
		}
		if ( isset( $typo['color'] ) && '' != $typo['color'] ) {
			$res .= 'color: ' . esc_attr( $typo['color'] ) . ',' . PHP_EOL;
		}
	}

	return $res;
}

function wolmart_dynamic_vars_bg( $id, $bg ) {
	$style = '';

	if ( isset( $bg['background-color'] ) && $bg['background-color'] ) {
		$style .= '--wolmart-' . $id . '-bg-color: ' . $bg['background-color'] . ';' . PHP_EOL;
	}
	if ( isset( $bg['background-image'] ) && $bg['background-image'] ) {
		$style .= '--wolmart-' . $id . '-bg-image: url("' . $bg['background-image'] . '");' . PHP_EOL;

		if ( isset( $bg['background-repeat'] ) && $bg['background-repeat'] ) {
			$style .= '--wolmart-' . $id . '-bg-repeat: ' . $bg['background-repeat'] . ';' . PHP_EOL;
		}
		if ( isset( $bg['background-position'] ) && $bg['background-position'] ) {
			$style .= '--wolmart-' . $id . '-bg-position: ' . $bg['background-position'] . ';' . PHP_EOL;
		}
		if ( isset( $bg['background-size'] ) && $bg['background-size'] ) {
			$style .= '--wolmart-' . $id . '-bg-size: ' . $bg['background-size'] . ';' . PHP_EOL;
		}
		if ( isset( $bg['background-attachment'] ) && $bg['background-attachment'] ) {
			$style .= '--wolmart-' . $id . '-bg-attachment: ' . $bg['background-attachment'] . ';' . PHP_EOL;
		}
	}

	return $style;
}

function wolmart_dynamic_vars_typo( $id, $typo, $default = array() ) {
	$style = '';

	if ( isset( $typo['font-family'] ) && 'inherit' != $typo['font-family'] ) {
		$style .= '--wolmart-' . $id . '-font-family: ' . "'" . $typo['font-family'] . "';" . PHP_EOL;

		if ( ! isset( $typo['variant'] ) ) {
			$typo['variant'] = 400;
		}
	} else {
		if ( isset( $default['font-weight'] ) ) {
			$typo['variant'] = $default['font-weight'];
		}
	}

	if ( isset( $typo['variant'] ) && $typo['variant'] ) {
		$style .= '--wolmart-' . $id . '-font-weight: ' . ( 'regular' == $typo['variant'] ? 400 : $typo['variant'] ) . ';' . PHP_EOL;
	}

	if ( isset( $typo['font-size'] ) && '' != $typo['font-size'] ) {
		$style .= '--wolmart-' . $id . '-font-size: ' . ( is_int( $typo['font-size'] ) ? ( $typo['font-size'] . 'px' ) : $typo['font-size'] ) . ';' . PHP_EOL;
	}

	if ( isset( $typo['line-height'] ) && '' != $typo['line-height'] ) {
		$style .= '--wolmart-' . $id . '-line-height: ' . $typo['line-height'] . ';' . PHP_EOL;
	}

	if ( isset( $typo['letter-spacing'] ) && '' != $typo['letter-spacing'] ) {
		$style .= '--wolmart-' . $id . '-letter-spacing: ' . $typo['letter-spacing'] . ';' . PHP_EOL;
	}

	if ( isset( $typo['text-transform'] ) && '' != $typo['text-transform'] ) {
		$style .= '--wolmart-' . $id . '-text-transform: ' . $typo['text-transform'] . ';' . PHP_EOL;
	}

	if ( isset( $typo['color'] ) && '' != $typo['color'] ) {
		$style .= '--wolmart-' . $id . '-color: ' . $typo['color'] . ';' . PHP_EOL;
	}

	return $style;
}
