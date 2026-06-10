<?php
/**
 * Trending Post options.
 *
 * @package Sensational Blog
 */

$default = sensational_blog_get_default_theme_options();

// Blog & News Section
$wp_customize->add_section( 'section_home_trending',
	array(
		'title'      => __( 'Trending Posts', 'sensational-blog' ),
		'capability' => 'edit_theme_options',
		'panel'      => 'home_page_panel',
		)
);

$wp_customize->add_setting( 'theme_options[disable_trending_section]',
	array(
		'default'           => $default['disable_trending_section'],
		'type'              => 'theme_mod',
		'capability'        => 'edit_theme_options',
		'sanitize_callback' => 'sensational_blog_sanitize_switch_control',
	)
);
$wp_customize->add_control( new Sensational_Blog_Switch_Control( $wp_customize, 'theme_options[disable_trending_section]',
    array(
		'label' 			=> __('Enable/Disable Trending Posts Section', 'sensational-blog'),
		'section'    		=> 'section_home_trending',
		 'settings'  		=> 'theme_options[disable_trending_section]',
		'on_off_label' 		=> sensational_blog_switch_options(),
    )
) );


// Add arrow enable setting and control.
$wp_customize->add_setting( 'theme_options[trending_content_align]', array(
	'default'           => $default['trending_content_align'],
	'sanitize_callback' => 'sensational_blog_sanitize_select',
) );

$wp_customize->add_control( 'theme_options[trending_content_align]', array(
	'label'             => esc_html__( 'Choose Content Align', 'sensational-blog' ),
	'section'           => 'section_home_trending',
	'type'              => 'radio',
	'active_callback' => 'sensational_blog_trending_active',
	'choices'				=> array( 
		'content-right'     => esc_html__( 'Right Side', 'sensational-blog' ), 
		'content-center'     => esc_html__( 'Center Side', 'sensational-blog' ), 
		'content-left'     => esc_html__( 'Left Side', 'sensational-blog' ), 
		'content-justify'     => esc_html__( 'Justify', 'sensational-blog' )
		)
) );

//Blog & News Section title
$wp_customize->add_setting('theme_options[trending_title]', 
	array(
	'default'           => $default['trending_title'],
	'type'              => 'theme_mod',
	'capability'        => 'edit_theme_options',	
	'sanitize_callback' => 'sanitize_text_field'
	)
);

$wp_customize->add_control('theme_options[trending_title]', 
	array(
	'label'       => __('Section Title', 'sensational-blog'),
	'section'     => 'section_home_trending',   
	'settings'    => 'theme_options[trending_title]',
	'active_callback' => 'sensational_blog_trending_active',		
	'type'        => 'text'
	)
);

//Trending Section title
$wp_customize->add_setting('theme_options[trending_subtitle]', 
	array(
	'default'           => $default['trending_subtitle'],
	'type'              => 'theme_mod',
	'capability'        => 'edit_theme_options',	
	'sanitize_callback' => 'sanitize_text_field'
	)
);

$wp_customize->add_control('theme_options[trending_subtitle]', 
	array(
	'label'       => __('Section Sub Title', 'sensational-blog'),
	'section'     => 'section_home_trending',   
	'settings'    => 'theme_options[trending_subtitle]',
	'active_callback' => 'sensational_blog_trending_active',		
	'type'        => 'text'
	)
);

// Add posted on enable setting and control.
$wp_customize->add_setting( 'theme_options[trending_posted_on_enable]', array(
	'default'           => $default['trending_posted_on_enable'],
	'sanitize_callback' => 'sensational_blog_sanitize_checkbox',
) );

$wp_customize->add_control( 'theme_options[trending_posted_on_enable]', array(
	'label'             => esc_html__( 'Enable Date', 'sensational-blog' ),
	'section'           => 'section_home_trending',
	'type'              => 'checkbox',
	'active_callback' => 'sensational_blog_trending_active',
) );

// Add posted on enable setting and control.
$wp_customize->add_setting( 'theme_options[trending_author_enable]', array(
	'default'           => $default['trending_author_enable'],
	'sanitize_callback' => 'sensational_blog_sanitize_checkbox',
) );

$wp_customize->add_control( 'theme_options[trending_author_enable]', array(
	'label'             => esc_html__( 'Enable Author', 'sensational-blog' ),
	'section'           => 'section_home_trending',
	'type'              => 'checkbox',
	'active_callback' => 'sensational_blog_trending_active',
) );

// Add posted on enable setting and control.
$wp_customize->add_setting( 'theme_options[trending_post_count_enable]', array(
	'default'           => $default['trending_post_count_enable'],
	'sanitize_callback' => 'sensational_blog_sanitize_checkbox',
) );

$wp_customize->add_control( 'theme_options[trending_post_count_enable]', array(
	'label'             => esc_html__( 'Enable Post Number', 'sensational-blog' ),
	'section'           => 'section_home_trending',
	'type'              => 'checkbox',
	'active_callback' => 'sensational_blog_trending_active',
) );

$number_of_trending_items = sensational_blog_get_option( 'number_of_trending_items' );

for( $i=1; $i<=$number_of_trending_items; $i++ ){

	// Posts
	$wp_customize->add_setting('theme_options[trending_post_'.$i.']', 
		array(
		'type'              => 'theme_mod',
		'capability'        => 'edit_theme_options',	
		'sanitize_callback' => 'sensational_blog_dropdown_pages'
		)
	);

	$wp_customize->add_control('theme_options[trending_post_'.$i.']', 
		array(
		'label'       => sprintf( __('Select Post #%1$s', 'sensational-blog'), $i),
		'section'     => 'section_home_trending',   
		'settings'    => 'theme_options[trending_post_'.$i.']',		
		'type'        => 'select',
		'choices'	  => sensational_blog_post_choices(),
		'active_callback' => 'sensational_blog_trending_active',
		)
	);
}