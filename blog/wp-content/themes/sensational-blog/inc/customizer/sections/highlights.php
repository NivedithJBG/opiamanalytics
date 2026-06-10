<?php
/**
 * Highlights options.
 *
 * @package Sensational Blog
 */

$default = sensational_blog_get_default_theme_options();

// Highlights Section
$wp_customize->add_section( 'section_home_highlights',
	array(
		'title'      => __( 'Highlights Section', 'sensational-blog' ),
		'capability' => 'edit_theme_options',
		'panel'      => 'home_page_panel',
		)
);

$wp_customize->add_setting( 'theme_options[disable_highlights_section]',
	array(
		'default'           => $default['disable_highlights_section'],
		'type'              => 'theme_mod',
		'capability'        => 'edit_theme_options',
		'sanitize_callback' => 'sensational_blog_sanitize_switch_control',
	)
);
$wp_customize->add_control( new Sensational_Blog_Switch_Control( $wp_customize, 'theme_options[disable_highlights_section]',
    array(
		'label' 	=> __('Disable Highlights Section', 'sensational-blog'),
		'section'    			=> 'section_home_highlights',
		'on_off_label' 		=> sensational_blog_switch_options(),
    )
) );
//Blog & News Section title
$wp_customize->add_setting('theme_options[highlights_title]', 
	array(
	'default'           => $default['highlights_title'],
	'type'              => 'theme_mod',
	'capability'        => 'edit_theme_options',	
	'sanitize_callback' => 'sanitize_text_field'
	)
);

$wp_customize->add_control('theme_options[highlights_title]', 
	array(
	'label'       => __('Section Title', 'sensational-blog'),
	'section'     => 'section_home_highlights',   
	'settings'    => 'theme_options[highlights_title]',
	'active_callback' => 'sensational_blog_highlights_active',		
	'type'        => 'text'
	)
);


// Lite/Dark Setting.
$wp_customize->add_setting( 'theme_options[highlights_lite_dark_background]', array(
	'default'           => $default['highlights_lite_dark_background'],
	'sanitize_callback' => 'sensational_blog_sanitize_select',
) );

$wp_customize->add_control( 'theme_options[highlights_lite_dark_background]', array(
	'label'             => esc_html__( 'Choose Dark/Lite layout', 'sensational-blog' ),
	'section'           => 'section_home_highlights',
	'type'              => 'radio',
	'active_callback' => 'sensational_blog_highlights_active',
	'choices'				=> array( 
		'dark-bg'     => esc_html__( 'Dark Background', 'sensational-blog' ), 
		'lite-bg'     => esc_html__( 'Lite Background', 'sensational-blog' )
		)
) );


//Popular Section title
$wp_customize->add_setting('theme_options[highlights_subtitle]', 
	array(
	'default'           => $default['highlights_subtitle'],
	'type'              => 'theme_mod',
	'capability'        => 'edit_theme_options',	
	'sanitize_callback' => 'sanitize_text_field'
	)
);

$wp_customize->add_control('theme_options[highlights_subtitle]', 
	array(
	'label'       => __('Section Sub Title', 'sensational-blog'),
	'section'     => 'section_home_highlights',   
	'settings'    => 'theme_options[highlights_subtitle]',
	'active_callback' => 'sensational_blog_highlights_active',		
	'type'        => 'text'
	)
);

// Add arrow enable setting and control.
$wp_customize->add_setting( 'theme_options[highlights_content_align]', array(
	'default'           => $default['highlights_content_align'],
	'sanitize_callback' => 'sensational_blog_sanitize_select',
) );

$wp_customize->add_control( 'theme_options[highlights_content_align]', array(
	'label'             => esc_html__( 'Choose Content Align', 'sensational-blog' ),
	'section'           => 'section_home_highlights',
	'type'              => 'radio',
	'active_callback' => 'sensational_blog_highlights_active',
	'choices'				=> array( 
		'content-right'     => esc_html__( 'Right Side', 'sensational-blog' ), 
		'content-center'     => esc_html__( 'Center Side', 'sensational-blog' ), 
		'content-left'     => esc_html__( 'Left Side', 'sensational-blog' ), 
		'content-justify'     => esc_html__( 'Justify', 'sensational-blog' )
		)
) );


// Add arrow enable setting and control.
$wp_customize->add_setting( 'theme_options[highlights_arrow_enable]', array(
	'default'           => $default['highlights_arrow_enable'],
	'sanitize_callback' => 'sensational_blog_sanitize_checkbox',
) );

$wp_customize->add_control( 'theme_options[highlights_arrow_enable]', array(
	'label'             => esc_html__( 'Enable Highlights Arrow', 'sensational-blog' ),
	'section'           => 'section_home_highlights',
	'type'              => 'checkbox',
	'active_callback' => 'sensational_blog_highlights_active',
) );

// Add content enable setting and control.
$wp_customize->add_setting( 'theme_options[highlights_content_enable]', array(
	'default'           => $default['highlights_content_enable'],
	'sanitize_callback' => 'sensational_blog_sanitize_checkbox',
) );

$wp_customize->add_control( 'theme_options[highlights_content_enable]', array(
	'label'             => esc_html__( 'Enable Content', 'sensational-blog' ),
	'section'           => 'section_home_highlights',
	'type'              => 'checkbox',
	'active_callback' => 'sensational_blog_highlights_active',
) );

// Add posted on enable setting and control.
$wp_customize->add_setting( 'theme_options[highlights_posted_on_enable]', array(
	'default'           => $default['highlights_posted_on_enable'],
	'sanitize_callback' => 'sensational_blog_sanitize_checkbox',
) );

$wp_customize->add_control( 'theme_options[highlights_posted_on_enable]', array(
	'label'             => esc_html__( 'Enable Date', 'sensational-blog' ),
	'section'           => 'section_home_highlights',
	'type'              => 'checkbox',
	'active_callback' => 'sensational_blog_highlights_active',
) );
// Add posted on enable setting and control.
$wp_customize->add_setting( 'theme_options[highlights_author_enable]', array(
	'default'           => $default['highlights_author_enable'],
	'sanitize_callback' => 'sensational_blog_sanitize_checkbox',
) );

$wp_customize->add_control( 'theme_options[highlights_author_enable]', array(
	'label'             => esc_html__( 'Enable Author', 'sensational-blog' ),
	'section'           => 'section_featured_highlights',
	'type'              => 'checkbox',
	'active_callback' => 'sensational_blog_highlights_active',
) );

// Add posted on enable setting and control.
$wp_customize->add_setting( 'theme_options[highlights_post_count_enable]', array(
	'default'           => $default['highlights_post_count_enable'],
	'sanitize_callback' => 'sensational_blog_sanitize_checkbox',
) );

$wp_customize->add_control( 'theme_options[highlights_post_count_enable]', array(
	'label'             => esc_html__( 'Enable Post Number', 'sensational-blog' ),
	'section'           => 'section_home_highlights',
	'type'              => 'checkbox',
	'active_callback' => 'sensational_blog_highlights_active',
) );

// Speed of highlights
$wp_customize->add_setting('theme_options[highlights_speed]', 
	array(
	'default' 			=> $default['highlights_speed'],
	'type'              => 'theme_mod',
	'capability'        => 'edit_theme_options',	
	'sanitize_callback' => 'sensational_blog_sanitize_number_range'
	)
);

$wp_customize->add_control('theme_options[highlights_speed]', 
	array(
	'label'       => __('Highlights Speed', 'sensational-blog'),
	'description' => __('Highlights Speed Default speed 800', 'sensational-blog'),
	'section'     => 'section_home_highlights',   
	'settings'    => 'theme_options[highlights_speed]',		
	'type'        => 'number',
	'active_callback' => 'sensational_blog_highlights_active',
	)
);

// Enable dot
$wp_customize->add_setting( 'theme_options[highlights_dot]',
	array(
		'default' 			=> $default['highlights_dot'],
		'type'              => 'theme_mod',
		'capability'        => 'edit_theme_options',
		'sanitize_callback' => 'sensational_blog_sanitize_switch_control',
	)
);
$wp_customize->add_control( new Sensational_Blog_Switch_Control( $wp_customize, 'theme_options[highlights_dot]',
    array(
		'label' 	=> __('Disable Highlights Dots', 'sensational-blog'),
		'section'    			=> 'section_home_highlights',
		'on_off_label' 		=> sensational_blog_switch_options(),
		'active_callback' => 'sensational_blog_highlights_active',
    )
) );

$number_of_highlights_items = sensational_blog_get_option( 'number_of_highlights_items' );
for( $i=1; $i<=$number_of_highlights_items; $i++ ){
	// Additional Information First Post
	$wp_customize->add_setting('theme_options[highlights_post_'.$i.']', 
		array(
		'type'              => 'theme_mod',
		'capability'        => 'edit_theme_options',	
		'sanitize_callback' => 'sensational_blog_dropdown_posts'
		)
	);
	$wp_customize->add_control( new Sensational_Blog_Dropdown_Chooser( $wp_customize,'theme_options[highlights_post_'.$i.']', 
		array(
		'label'       => sprintf( __('Select Post #%1$s', 'sensational-blog'), $i),
		'section'     => 'section_home_highlights',  
		'settings'    => 'theme_options[highlights_post_'.$i.']',	
		'choices'	=> sensational_blog_post_choices(),	
		'type'        => 'dropdown-posts',
		'active_callback' => 'sensational_blog_highlights_active',
		)
	));
}
