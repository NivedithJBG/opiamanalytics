<?php
/**
 * Recent Post options.
 *
 * @package Sensational Blog
 */

$default = sensational_blog_get_default_theme_options();

// Blog & News Section
$wp_customize->add_section( 'section_home_recent',
	array(
		'title'      => __( 'Recent Posts', 'sensational-blog' ),
		'capability' => 'edit_theme_options',
		'panel'      => 'home_page_panel',
		)
);

$wp_customize->add_setting( 'theme_options[disable_recent_section]',
	array(
		'default'           => $default['disable_recent_section'],
		'type'              => 'theme_mod',
		'capability'        => 'edit_theme_options',
		'sanitize_callback' => 'sensational_blog_sanitize_switch_control',
	)
);
$wp_customize->add_control( new Sensational_Blog_Switch_Control( $wp_customize, 'theme_options[disable_recent_section]',
    array(
		'label' 			=> __('Enable/Disable Recent Posts Section', 'sensational-blog'),
		'section'    		=> 'section_home_recent',
		 'settings'  		=> 'theme_options[disable_recent_section]',
		'on_off_label' 		=> sensational_blog_switch_options(),
    )
) );

// Lite/Dark Setting.
$wp_customize->add_setting( 'theme_options[recent_lite_dark_background]', array(
	'default'           => $default['recent_lite_dark_background'],
	'sanitize_callback' => 'sensational_blog_sanitize_select',
) );

$wp_customize->add_control( 'theme_options[recent_lite_dark_background]', array(
	'label'             => esc_html__( 'Choose Dark/Lite layout', 'sensational-blog' ),
	'section'           => 'section_home_recent',
	'type'              => 'radio',
	'active_callback' => 'sensational_blog_recent_active',
	'choices'				=> array( 
		'dark-bg'     => esc_html__( 'Dark Background', 'sensational-blog' ), 
		'lite-bg'     => esc_html__( 'Lite Background', 'sensational-blog' )
		)
) );

// Add arrow enable setting and control.
$wp_customize->add_setting( 'theme_options[recent_content_align]', array(
	'default'           => $default['recent_content_align'],
	'sanitize_callback' => 'sensational_blog_sanitize_select',
) );

$wp_customize->add_control( 'theme_options[recent_content_align]', array(
	'label'             => esc_html__( 'Choose Content Align', 'sensational-blog' ),
	'section'           => 'section_home_recent',
	'type'              => 'radio',
	'active_callback' => 'sensational_blog_recent_active',
	'choices'				=> array( 
		'content-right'     => esc_html__( 'Right Side', 'sensational-blog' ), 
		'content-center'     => esc_html__( 'Center Side', 'sensational-blog' ), 
		'content-left'     => esc_html__( 'Left Side', 'sensational-blog' ), 
		'content-justify'     => esc_html__( 'Justify', 'sensational-blog' )
		)
) );


//Blog & News Section title
$wp_customize->add_setting('theme_options[recent_title]', 
	array(
	'default'           => $default['recent_title'],
	'type'              => 'theme_mod',
	'capability'        => 'edit_theme_options',	
	'sanitize_callback' => 'sanitize_text_field'
	)
);

$wp_customize->add_control('theme_options[recent_title]', 
	array(
	'label'       => __('Section Title', 'sensational-blog'),
	'section'     => 'section_home_recent',   
	'settings'    => 'theme_options[recent_title]',
	'active_callback' => 'sensational_blog_recent_active',		
	'type'        => 'text'
	)
);

//Recent Section title
$wp_customize->add_setting('theme_options[recent_subtitle]', 
	array(
	'default'           => $default['recent_subtitle'],
	'type'              => 'theme_mod',
	'capability'        => 'edit_theme_options',	
	'sanitize_callback' => 'sanitize_text_field'
	)
);

$wp_customize->add_control('theme_options[recent_subtitle]', 
	array(
	'label'       => __('Section Sub Title', 'sensational-blog'),
	'section'     => 'section_home_recent',   
	'settings'    => 'theme_options[recent_subtitle]',
	'active_callback' => 'sensational_blog_recent_active',		
	'type'        => 'text'
	)
);

// Add posted on enable setting and control.
$wp_customize->add_setting( 'theme_options[recent_posted_on_enable]', array(
	'default'           => $default['recent_posted_on_enable'],
	'sanitize_callback' => 'sensational_blog_sanitize_checkbox',
) );

$wp_customize->add_control( 'theme_options[recent_posted_on_enable]', array(
	'label'             => esc_html__( 'Enable Date', 'sensational-blog' ),
	'section'           => 'section_home_recent',
	'type'              => 'checkbox',
	'active_callback' => 'sensational_blog_recent_active',
) );

// Add posted on enable setting and control.
$wp_customize->add_setting( 'theme_options[recent_author_enable]', array(
	'default'           => $default['recent_author_enable'],
	'sanitize_callback' => 'sensational_blog_sanitize_checkbox',
) );

$wp_customize->add_control( 'theme_options[recent_author_enable]', array(
	'label'             => esc_html__( 'Enable Author', 'sensational-blog' ),
	'section'           => 'section_home_recent',
	'type'              => 'checkbox',
	'active_callback' => 'sensational_blog_recent_active',
) );

// Add posted on enable setting and control.
$wp_customize->add_setting( 'theme_options[recent_post_count_enable]', array(
	'default'           => $default['recent_post_count_enable'],
	'sanitize_callback' => 'sensational_blog_sanitize_checkbox',
) );

$wp_customize->add_control( 'theme_options[recent_post_count_enable]', array(
	'label'             => esc_html__( 'Enable Post Number', 'sensational-blog' ),
	'section'           => 'section_home_recent',
	'type'              => 'checkbox',
	'active_callback' => 'sensational_blog_recent_active',
) );

$number_of_recent_items = sensational_blog_get_option( 'number_of_recent_items' );

for( $i=1; $i<=$number_of_recent_items; $i++ ){

	// Posts
	$wp_customize->add_setting('theme_options[recent_post_'.$i.']', 
		array(
		'type'              => 'theme_mod',
		'capability'        => 'edit_theme_options',	
		'sanitize_callback' => 'sensational_blog_dropdown_pages'
		)
	);

	$wp_customize->add_control('theme_options[recent_post_'.$i.']', 
		array(
		'label'       => sprintf( __('Select Post #%1$s', 'sensational-blog'), $i),
		'section'     => 'section_home_recent',   
		'settings'    => 'theme_options[recent_post_'.$i.']',		
		'type'        => 'select',
		'choices'	  => sensational_blog_post_choices(),
		'active_callback' => 'sensational_blog_recent_active',
		)
	);
}