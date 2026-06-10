<?php
/**
 * Must Read Post options.
 *
 * @package Sensational Blog
 */

$default = sensational_blog_get_default_theme_options();

// Must Read Post Section
$wp_customize->add_section( 'section_home_mustread',
	array(
		'title'      => __( 'Must Read Posts', 'sensational-blog' ),
		'capability' => 'edit_theme_options',
		'panel'      => 'home_page_panel',
		'active_callback' => 'sensational_blog_mustread_design_enable',
		)
);

$wp_customize->add_setting( 'theme_options[disable_mustread_section]',
	array(
		'default'           => $default['disable_mustread_section'],
		'type'              => 'theme_mod',
		'capability'        => 'edit_theme_options',
		'sanitize_callback' => 'sensational_blog_sanitize_switch_control',
	)
);
$wp_customize->add_control( new Sensational_Blog_Switch_Control( $wp_customize, 'theme_options[disable_mustread_section]',
    array(
		'label' 			=> __('Enable/Disable Must Read Post Section', 'sensational-blog'),
		'section'    		=> 'section_home_mustread',
		 'settings'  		=> 'theme_options[disable_mustread_section]',
		'on_off_label' 		=> sensational_blog_switch_options(),
    )
) );


$wp_customize->add_setting( 'theme_options[mustread_background_color]', array(
    'sanitize_callback' => 'sanitize_hex_color', // The hue is stored as a positive integer.
    'default' 			=> $default['mustread_background_color'],
    
) );
$wp_customize->add_control( new WP_Customize_Color_Control( $wp_customize, 'theme_options[mustread_background_color]', array(
    'label'    => esc_html__( 'Background Color', 'sensational-blog' ),
    'section'  => 'section_home_mustread',
    'active_callback' => 'sensational_blog_mustread_active',
) ) );

// Add arrow enable setting and control.
$wp_customize->add_setting( 'theme_options[mustread_content_align]', array(
	'default'           => $default['mustread_content_align'],
	'sanitize_callback' => 'sensational_blog_sanitize_select',
) );

$wp_customize->add_control( 'theme_options[mustread_content_align]', array(
	'label'             => esc_html__( 'Choose Content Align', 'sensational-blog' ),
	'section'           => 'section_home_mustread',
	'type'              => 'radio',
	'active_callback' => 'sensational_blog_mustread_active',
	'choices'				=> array( 
		'content-right'     => esc_html__( 'Right Side', 'sensational-blog' ), 
		'content-center'     => esc_html__( 'Center Side', 'sensational-blog' ), 
		'content-left'     => esc_html__( 'Left Side', 'sensational-blog' ), 
		'content-justify'     => esc_html__( 'Justify', 'sensational-blog' )
		)
) );

//Must Read Post Section title
$wp_customize->add_setting('theme_options[mustread_title]', 
	array(
	'default'           => $default['mustread_title'],
	'type'              => 'theme_mod',
	'capability'        => 'edit_theme_options',	
	'sanitize_callback' => 'sanitize_text_field'
	)
);

$wp_customize->add_control('theme_options[mustread_title]', 
	array(
	'label'       => __('Section Title', 'sensational-blog'),
	'section'     => 'section_home_mustread',   
	'settings'    => 'theme_options[mustread_title]',
	'active_callback' => 'sensational_blog_mustread_active',		
	'type'        => 'text'
	)
);

//Popular Section title
$wp_customize->add_setting('theme_options[mustread_subtitle]', 
	array(
	'default'           => $default['mustread_subtitle'],
	'type'              => 'theme_mod',
	'capability'        => 'edit_theme_options',	
	'sanitize_callback' => 'sanitize_text_field'
	)
);

$wp_customize->add_control('theme_options[mustread_subtitle]', 
	array(
	'label'       => __('Section Sub Title', 'sensational-blog'),
	'section'     => 'section_home_mustread',   
	'settings'    => 'theme_options[mustread_subtitle]',
	'active_callback' => 'sensational_blog_mustread_active',		
	'type'        => 'text'
	)
);

// Add posted on enable setting and control.
$wp_customize->add_setting( 'theme_options[mustread_posted_on_enable]', array(
	'default'           => $default['mustread_posted_on_enable'],
	'sanitize_callback' => 'sensational_blog_sanitize_checkbox',
) );

$wp_customize->add_control( 'theme_options[mustread_posted_on_enable]', array(
	'label'             => esc_html__( 'Enable Date', 'sensational-blog' ),
	'section'           => 'section_home_mustread',
	'type'              => 'checkbox',
	'active_callback' => 'sensational_blog_mustread_active',
) );

// Add posted on enable setting and control.
$wp_customize->add_setting( 'theme_options[mustread_author_enable]', array(
	'default'           => $default['mustread_author_enable'],
	'sanitize_callback' => 'sensational_blog_sanitize_checkbox',
) );

$wp_customize->add_control( 'theme_options[mustread_author_enable]', array(
	'label'             => esc_html__( 'Enable Author', 'sensational-blog' ),
	'section'           => 'section_home_mustread',
	'type'              => 'checkbox',
	'active_callback' => 'sensational_blog_mustread_active',
) );


// Setting  Team Category.
$wp_customize->add_setting( 'theme_options[mustread_category]',
	array(

	'capability'        => 'edit_theme_options',
	'sanitize_callback' => 'absint',
	)
);
$wp_customize->add_control(
	new Sensational_Blog_Dropdown_Taxonomies_Control( $wp_customize, 'theme_options[mustread_category]',
		array(
		'label'    => __( 'Select Category', 'sensational-blog' ),
		'section'  => 'section_home_mustread',
		'settings' => 'theme_options[mustread_category]',	
		'active_callback' => 'sensational_blog_mustread_active',		
		)
	)
);
