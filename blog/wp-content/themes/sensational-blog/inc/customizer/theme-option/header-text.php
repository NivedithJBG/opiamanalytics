<?php 

/**
 * Theme Options.
 *
 * @package Sensational Blog
 */

$default = sensational_blog_get_default_theme_options();
//For Header Text Option
$wp_customize->add_section('section_header_text', array(    
'title'       => __('Post Title Options', 'sensational-blog'),
'panel'       => 'theme_option_panel'    
));

// Add arrow enable setting and control.
$wp_customize->add_setting( 'theme_options[header_text_hover]', array(
	'default'           => $default['header_text_hover'],
	'sanitize_callback' => 'sensational_blog_sanitize_select',
) );

$wp_customize->add_control( 'theme_options[header_text_hover]', array(
	'label'             => esc_html__( 'Title Text Hover Option', 'sensational-blog' ),
	'section'           => 'section_header_text',
	'type'              => 'radio',
	'choices'			=> array( 
		'title-hover-none' => __('Default','sensational-blog'),
		'title-hover-1'	  => __('Hover 1','sensational-blog'),
		'title-hover-2'	  => __('Hover 2','sensational-blog'),
	)
) );

// Add arrow enable setting and control.
$wp_customize->add_setting( 'theme_options[header_text_transform_options]', array(
	'default'           => $default['header_text_transform_options'],
	'sanitize_callback' => 'sensational_blog_sanitize_select',
) );

$wp_customize->add_control( 'theme_options[header_text_transform_options]', array(
	'label'             => esc_html__( 'Title Text Transform Option', 'sensational-blog' ),
	'section'           => 'section_header_text',
	'type'              => 'radio',
	'choices'				=> array( 
		'none'	  => __('Default','sensational-blog'),
		'uppercase'	  => __('Uppercase','sensational-blog'),
		'lowercase'	  => __('Lowercase','sensational-blog'),
		'capitalize'  => __('Capitalize', 'sensational-blog'),
	)
) );

// Add arrow enable setting and control.
$wp_customize->add_setting( 'theme_options[header_text_decoration_options]', array(
	'default'           => $default['header_text_decoration_options'],
	'sanitize_callback' => 'sensational_blog_sanitize_select',
) );

$wp_customize->add_control( 'theme_options[header_text_decoration_options]', array(
	'label'             => esc_html__( 'Title Text Decoration Option', 'sensational-blog' ),
	'section'           => 'section_header_text',
	'type'              => 'radio',
	'choices'				=> array( 
		'none'	  => __('Default','sensational-blog'),
		'overline'	  => __('Overline','sensational-blog'),
		'underline'	  => __('Underline','sensational-blog'),
		'line-through'  => __('Line Through', 'sensational-blog'),
	)
) );

// Add arrow enable setting and control.
$wp_customize->add_setting( 'theme_options[header_font_style_options]', array(
	'default'           => $default['header_font_style_options'],
	'sanitize_callback' => 'sensational_blog_sanitize_select',
) );

$wp_customize->add_control( 'theme_options[header_font_style_options]', array(
	'label'             => esc_html__( 'Title Font Style Options', 'sensational-blog' ),
	'section'           => 'section_header_text',
	'type'              => 'radio',
	'choices'				=> array( 
		'none'	  => __('Default','sensational-blog'),
		'italic'	  => __('Italic','sensational-blog'),
		'oblique'	  => __('Oblique','sensational-blog'),
	)
) );

