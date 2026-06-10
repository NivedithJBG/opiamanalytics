<?php 
/**
 * Theme Options.
 *
 * @package Sensational Blog
 */

$default = sensational_blog_get_default_theme_options();

// Single Page Setting Section starts
$wp_customize->add_section('section_single_page', 
	array(    
	'title'       => __('Single Page Option', 'sensational-blog'),
	'panel'       => 'theme_option_panel'    
	)
);

// Add Single Header Image enable setting and control.
$wp_customize->add_setting( 'theme_options[single_page_header_image_as_header_image_enable]', array(
	'default'           => $default['single_page_header_image_as_header_image_enable'],
	'sanitize_callback' => 'sensational_blog_sanitize_checkbox',
) );

$wp_customize->add_control( 'theme_options[single_page_header_image_as_header_image_enable]', array(
	'label'             => esc_html__( 'Enable Header Image As Header Image', 'sensational-blog' ),
	'description' => __('If this option is Enable then Display Header Image as Header Image Otherwise display Featured Image as Header Image  ', 'sensational-blog'),
	'section'           => 'section_single_page',
	'type'              => 'checkbox',

) );
