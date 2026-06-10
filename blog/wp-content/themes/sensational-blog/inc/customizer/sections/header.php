<?php
/**
 * Header options.
 *
 * @package Sensational Blog
 */

$default = sensational_blog_get_default_theme_options();

// Header Author Section
$wp_customize->add_section( 'section_home_header', 
	array(
		'title'      => __( 'Header Options', 'sensational-blog' ),
		'capability' => 'edit_theme_options',
		'panel'      => 'home_page_panel',
		)
);
// Add arrow enable setting and control.
$wp_customize->add_setting( 'theme_options[header_layout_options]', array(
	'default'           => $default['header_layout_options'],
	'sanitize_callback' => 'sensational_blog_sanitize_select',
	'type'				=> 'theme_mod',
) );

$wp_customize->add_control( 'theme_options[header_layout_options]', array(
	'label'             => esc_html__( 'Choose Header Layout', 'sensational-blog' ),
	'section'           => 'section_home_header',
	'type'              => 'radio',
	'choices'				=> array( 
		'header-one'     => esc_html__( 'Header First(Normal)', 'sensational-blog' ), 
		'header-two'     => esc_html__( 'Header Second(Blog Style)', 'sensational-blog' ), 
		)
) );

