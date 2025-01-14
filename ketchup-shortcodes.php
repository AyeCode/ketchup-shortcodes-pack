<?php
/*
 * @wordpress-plugin
 * Plugin Name: Ketchup Shortcodes Pack
 * Description: This plugin enables some nice shortcodes for use with a theme.
 * Version: 0.2.1
 * Requires at least: 3.5.1
 * Tested up to: 6.7
 * Author: AyeCode Ltd
 * Author URI: https://ayecode.io/
 */

DEFINE( 'KETCHUP_SHORTCODES_PLUGIN_PATH', plugin_dir_path( __FILE__ ) );

/*------------------------*/
/*      Shortcodes        */
/*------------------------*/
function ketchup_shortcodes_register(){
	add_shortcode( 'spacer', 'ketchup_spacer_shortcode' );
	add_shortcode( 'fullwidth_background', 'ketchup_fullwidth_background_shortcode' );
	add_shortcode( 'title_and_subtitle', 'ketchup_titles_shortcode' );
	add_shortcode( 'content_block', 'ketchup_content_block_shortcode' );
	add_shortcode( 'blog_post', 'ketchup_blog_post_shortcode' );
}
add_action( 'init', 'ketchup_shortcodes_register' );

function ketchup_spacer_shortcode( $atts ) {
	$defaults = array(
		'margin_top' => '10px',
		'margin_bottom' => '10px'
	);

	$args = shortcode_atts( $defaults, $atts, 'spacer' );

	$margin_top = ketchup_validate_measurements( sanitize_text_field( $args['margin_top'] ) );
	$margin_bottom = ketchup_validate_measurements( sanitize_text_field( $args['margin_bottom'] ) );

	$style = '';

	if ( $margin_top !== '' ) {
		$style .= 'margin-top:' . $margin_top . ';';
	}

	if ( $margin_bottom !== '' ) {
		$style .= 'margin-bottom:' . $margin_bottom . ';';
	}

	$html = '<div class="ketchup_spacer"' . ( $style ? ' style="' . esc_attr( $style ) . '"' : '' ) . '></div>';

	return $html;
}

/*-------------------------*/
/* Background Shortcodes   */
/*-------------------------*/
function ketchup_background_ret( $background_color = null, $background_url = null ) {
	$bg_type = '';

	if ( $background_url != '' ) {
		$bg_type = 'bg';
	} else if ( $background_color != '' ) {
		$bg_type = 'color';
	}

	return $bg_type;
}

function ketchup_fullwidth_background_shortcode( $atts, $content = null ) {
	$defaults = array(
		'background_url' => '',
		'background_color' => ''
	);

	$args = shortcode_atts( $defaults, $atts, 'fullwidth_background' );

	$background_url = sanitize_text_field( $args['background_url'] );
	$background_color = sanitize_text_field( $args['background_color'] );

	$bg_type = ketchup_background_ret( $background_color, $background_url );

	if ( $bg_type == 'color' ) {
		$style = 'background:' . $background_color . ';';
	} else if ( $bg_type == 'bg' ) {
		$style = 'background:url(' . $background_url . ');';
	} else {
		$style ='';
	}

	$html = '<div class="ketchup_fullwidth_bg"' . ( $style ? ' style="' . esc_attr( $style ) . '"' : '' ) . '>';

	$html .='<div class="container">';
	if ( ! empty( $content ) || $content === '0' ) {
		$html .= do_shortcode( $content );
	}
	$html .= '</div>';
	$html .= '</div>';

	return $html;
}

/*--------------------------------*/
/* Title and Subtitle Shortcodes  */
/*--------------------------------*/
function ketchup_titles_shortcode( $atts ) {
	$defaults = array(
		'title' => '',
		'subtitle' => ''
	);

	$args = shortcode_atts( $defaults, $atts, 'title_and_subtitle' );

	$html= '';

	if ( $args['title'] !== '' ) {
		$html .= '<h1 class="ketchup_section_title">' . esc_html( wp_unslash( $args['title'] ) ) . '</h1>';
	}

	if ( $args['subtitle'] !== '' ) {
		$html .= '<h4 class="ketchup_section_subtitle">' . esc_html( wp_unslash( $args['subtitle'] ) ) . '</h4>';
	}

	return $html;
}

/*---------------------------*/
/* Content Block Shortcodes  */
/*---------------------------*/
function ketchup_content_block_shortcode( $atts ) {
	$defaults = array(
		'block_title' => '',
		'block_css_class' => '',
		'block_text' => '',
		'block_text_color' => '#000000',
		'block_button_text' => '',
		'block_button_link' => '',
		'block_button_css' => '',
		'block_button_color' => '',
		'block_image' => ''
	);

	$args = shortcode_atts( $defaults, $atts, 'title_and_subtitle' );

	$html = '<div class="ketchup_block_content ' . esc_attr( $args['block_css_class'] ) . '">';

	if ( $args['block_image'] ) {
		$html .= '<img class="img-responsive" src="' . esc_attr( $args['block_image'] ) . '"/>';
	}

	$html .= '<h2>' . esc_html( $args['block_title'] ).'</h2>';
	$html .= '<p style="color:' . esc_attr( $args['block_text_color'] ) . ';">' . esc_html( $args['block_text'] ).'</p>';
	$html .= '<a href="' . esc_url( $args['block_button_link'] ) . '" class="' . esc_attr( $args['block_button_css'] ) . '" style="color:' . esc_attr( $args['block_button_color'] ) . '">' . esc_html( $args['block_button_text'] ) . '</a>';
	$html .= '</div>';

	return $html;
}

/*-------------------------*/
/* Blog Post Shortcode     */
/*-------------------------*/
function ketchup_blog_post_shortcode( $atts ) {
	global $post;

	$defaults = array(
		'post_id' => '',
		'post_css_class' => '',
		'post_font_color' => '#000000',
		'post_read_more' => ''
	);

	$args = shortcode_atts( $defaults, $atts, 'title_and_subtitle' );

	$post_id = absint( $args['post_id'] );
	$post = $post_id > 0 ? get_post( $post_id ) : array();
	$html = '';

	if ( ! empty( $post ) ) {
		$post_thumbnail_id = (int) get_post_thumbnail_id( $post );
		$image_src = $post_thumbnail_id ? wp_get_attachment_image_src( $post_thumbnail_id, ' ' ) : array();

		$html .= '<div class="ketchup_blog_post ' . esc_attr( $args['post_css_class'] ) . '">';
		if ( ! empty( $image_src ) && ! empty( $image_src[0] ) ) {
			$html .= '<img class="img-responsive" src="' . esc_url_raw( $image_src[0] ) . '"/>';
		}
		$html .= '<h2><a href="' . esc_url( get_the_permalink( $post ) ) . '">' . trim( esc_html( strip_tags( wp_unslash( get_the_title( $post ) ) ) ) ) . '</a></h2>'; 
		$html .= '<p style="color:' . esc_attr( $args['post_font_color'] ) . '">' . get_the_excerpt( $post ) . '</p>';
		$html .='</div>';
	} else {
		$html .='<div class="no-posts">' . esc_html__( 'No posts with this ID found' ) . '</div>';
	}

	return $html;
}

/**
 * Validate measurements.
 *
 * @since 0.2.0
 *
 * @param string $value The value to validate.
 * @return string Validated value.
 */
function ketchup_validate_measurements( $value ) {
	if ( ! is_string( $value ) && ! is_int( $value ) && ! is_float( $value ) ) {
		return '';
	}

	if ( empty( $value ) ) {
		return '';
	}

	if ( is_numeric( $value ) ) {
		$value = $value . 'px';
	}

	$units = implode( '|', array( 'px', 'em', 'rem', 'vh', 'vw', '%' ) );
	$pattern = '/^(\d*\.?\d+)(' . $units . '){1,1}$/';

	preg_match( $pattern, str_replace( ' ', '', trim( $value ) ), $matches );

	if ( ! isset( $matches[1] ) || ! isset( $matches[2] ) ) {
		return '';
	}

	return $matches[1] . $matches[2];
}