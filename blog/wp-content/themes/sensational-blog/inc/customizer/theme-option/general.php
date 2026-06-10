<?php 

/**
 * Theme Options.
 *
 * @package Sensational Blog
 */

$default = sensational_blog_get_default_theme_options();
//For General Option
$wp_customize->add_section('section_general', array(    
'title'       => __('Sidebar Setting', 'sensational-blog'),
'panel'       => 'theme_option_panel'    
));

$wp_customize->add_setting( 'theme_options[disable_homepage_content_section]',
	array(
			'default'           => $default['disable_homepage_content_section'],
			'type'              => 'theme_mod',
			'capability'        => 'edit_theme_options',
			'sanitize_callback' => 'sensational_blog_sanitize_switch_control',
		)
	);
$wp_customize->add_control( new Sensational_Blog_Switch_Control( $wp_customize, 'theme_options[disable_homepage_content_section]',
    array(
		'label' 			=> __('Enable/Disable Static Homepage Content Section', 'sensational-blog'),
		'description' => __('This option is only work on Static HomePage ', 'sensational-blog'),
		'section'    		=> 'static_front_page',
		 'settings'  		=> 'theme_options[disable_homepage_content_section]',
		'on_off_label' 		=> sensational_blog_switch_options(),
    )
) );

//Layout Options for Blog
$wp_customize->add_setting('theme_options[layout_options_blog]', 
	array(
	'default' 			=> $default['layout_options_blog'],
	'type'              => 'theme_mod',
	'capability'        => 'edit_theme_options',
	'sanitize_callback' => 'sensational_blog_sanitize_select'
	)
);

$wp_customize->add_control(new Sensational_Blog_Image_Radio_Control($wp_customize, 'theme_options[layout_options_blog]', 
	array(		
	'label' 	=> __('Layout Option For Blog', 'sensational-blog'),
	'section' 	=> 'section_general',
	'settings'  => 'theme_options[layout_options_blog]',
	'type' 		=> 'radio-image',
	'choices' 	=> array(		
		'left-sidebar' 	=> get_template_directory_uri() . '/assets/images/left-sidebar.png',						
		'right-sidebar' => get_template_directory_uri() . '/assets/images/right-sidebar.png',
		'no-sidebar' 	=> get_template_directory_uri() . '/assets/images/no-sidebar.png',
		),	
	))
);

//Layout Options for Archive
$wp_customize->add_setting('theme_options[layout_options_archive]', 
	array(
	'default' 			=> $default['layout_options_archive'],
	'type'              => 'theme_mod',
	'capability'        => 'edit_theme_options',
	'sanitize_callback' => 'sensational_blog_sanitize_select'
	)
);

$wp_customize->add_control(new Sensational_Blog_Image_Radio_Control($wp_customize, 'theme_options[layout_options_archive]', 
	array(		
	'label' 	=> __('Layout Option For Archive', 'sensational-blog'),
	'section' 	=> 'section_general',
	'settings'  => 'theme_options[layout_options_archive]',
	'type' 		=> 'radio-image',
	'choices' 	=> array(		
		'left-sidebar' 	=> get_template_directory_uri() . '/assets/images/left-sidebar.png',						
		'right-sidebar' => get_template_directory_uri() . '/assets/images/right-sidebar.png',
		'no-sidebar' 	=> get_template_directory_uri() . '/assets/images/no-sidebar.png',
		),	
	))
);


//Layout Options for Pages
$wp_customize->add_setting('theme_options[layout_options_page]', 
	array(
	'default' 			=> $default['layout_options_page'],
	'type'              => 'theme_mod',
	'capability'        => 'edit_theme_options',
	'sanitize_callback' => 'sensational_blog_sanitize_select'
	)
);

$wp_customize->add_control(new Sensational_Blog_Image_Radio_Control($wp_customize, 'theme_options[layout_options_page]', 
	array(		
	'label' 	=> __('Layout Option For Pages', 'sensational-blog'),
	'section' 	=> 'section_general',
	'settings'  => 'theme_options[layout_options_page]',
	'type' 		=> 'radio-image',
	'choices' 	=> array(		
		'left-sidebar' 	=> get_template_directory_uri() . '/assets/images/left-sidebar.png',						
		'right-sidebar' => get_template_directory_uri() . '/assets/images/right-sidebar.png',
		'no-sidebar' 	=> get_template_directory_uri() . '/assets/images/no-sidebar.png',
		),	
	))
);

//Layout Options for Single Post
$wp_customize->add_setting('theme_options[layout_options_single]', 
	array(
	'default' 			=> $default['layout_options_single'],
	'type'              => 'theme_mod',
	'capability'        => 'edit_theme_options',
	'sanitize_callback' => 'sensational_blog_sanitize_select'
	)
);

$wp_customize->add_control(new Sensational_Blog_Image_Radio_Control($wp_customize, 'theme_options[layout_options_single]', 
	array(		
	'label' 	=> __('Layout Option For Single Posts', 'sensational-blog'),
	'section' 	=> 'section_general',
	'settings'  => 'theme_options[layout_options_single]',
	'type' 		=> 'radio-image',
	'choices' 	=> array(		
		'left-sidebar' 	=> get_template_directory_uri() . '/assets/images/left-sidebar.png',						
		'right-sidebar' => get_template_directory_uri() . '/assets/images/right-sidebar.png',
		'no-sidebar' 	=> get_template_directory_uri() . '/assets/images/no-sidebar.png',
		),	
	))
);

 ?>