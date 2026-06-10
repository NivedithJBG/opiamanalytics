<?php
/**
 * Slider options.
 *
 * @package Sensational Blog
 */

$default = sensational_blog_get_default_theme_options();

// Featured Slider Section
$wp_customize->add_section( 'section_featured_slider',
	array(
		'title'      => __( 'Banner Section', 'sensational-blog' ),
		'capability' => 'edit_theme_options',
		'panel'      => 'home_page_panel',
		'active_callback' => 'sensational_blog_slider_design_enable',
		)
);

$wp_customize->add_setting( 'theme_options[disable_featured-slider_section]',
	array(
		'default'           => $default['disable_featured-slider_section'],
		'type'              => 'theme_mod',
		'capability'        => 'edit_theme_options',
		'sanitize_callback' => 'sensational_blog_sanitize_switch_control',
	)
);
$wp_customize->add_control( new Sensational_Blog_Switch_Control( $wp_customize, 'theme_options[disable_featured-slider_section]',
    array(
		'label' 	=> __('Disable Slider Section', 'sensational-blog'),
		'section'    			=> 'section_featured_slider',
		'on_off_label' 		=> sensational_blog_switch_options(),
    )
) );

// Number of items
$wp_customize->add_setting('theme_options[slider_excerpt_length]', 
	array(
	'default' 			=> $default['slider_excerpt_length'],
	'type'              => 'theme_mod',
	'capability'        => 'edit_theme_options',	
	'sanitize_callback' => 'sensational_blog_sanitize_number_range'
	)
);

$wp_customize->add_control('theme_options[slider_excerpt_length]', 
	array(
	'label'       => __('Excerpt Length', 'sensational-blog'),
	'description' => __('Save & Refresh the customizer to see its effect. Maximum is 1000.', 'sensational-blog'),
	'section'     => 'section_featured_slider',   
	'settings'    => 'theme_options[slider_excerpt_length]',		
	'type'        => 'number',
	'active_callback' => 'sensational_blog_slider_active',
	'input_attrs' => array(
			'min'	=> 0,
			'max'	=> 1000,
			'step'	=> 1,
		),
	)
);

// Add arrow enable setting and control.
$wp_customize->add_setting( 'theme_options[slider_content_position_option]', array(
	'default'           => $default['slider_content_position_option'],
	'sanitize_callback' => 'sensational_blog_sanitize_select',
) );

$wp_customize->add_control( 'theme_options[slider_content_position_option]', array(
	'label'             => esc_html__( 'Choose Slider Contion position', 'sensational-blog' ),
	'section'           => 'section_featured_slider',
	'type'              => 'radio',
	'active_callback' => 'sensational_blog_slider_active',
	'choices'				=> array( 
		'default-position'     => esc_html__( 'Center', 'sensational-blog' ), 
		'left-position'     => esc_html__( 'Left Side', 'sensational-blog' ), 
		'right-position'     => esc_html__( 'Right Side', 'sensational-blog' ),
		)
) );


// Add arrow enable setting and control.
$wp_customize->add_setting( 'theme_options[slider_arrow_enable]', array(
	'default'           => $default['slider_arrow_enable'],
	'sanitize_callback' => 'sensational_blog_sanitize_checkbox',
) );

$wp_customize->add_control( 'theme_options[slider_arrow_enable]', array(
	'label'             => esc_html__( 'Enable Slider Arrow', 'sensational-blog' ),
	'section'           => 'section_featured_slider',
	'type'              => 'checkbox',
	'active_callback' => 'sensational_blog_slider_active',
) );


// Add autoplay enable setting and control.
$wp_customize->add_setting( 'theme_options[slider_fade_enable]', array(
	'default'           => $default['slider_fade_enable'],
	'sanitize_callback' => 'sensational_blog_sanitize_checkbox',
) );

$wp_customize->add_control( 'theme_options[slider_fade_enable]', array(
	'label'             => esc_html__( 'Enable Slider Fade Effect', 'sensational-blog' ),
	'section'           => 'section_featured_slider',
	'type'              => 'checkbox',
	'active_callback' => 'sensational_blog_slider_active',
) );


// Add posted on enable setting and control.
$wp_customize->add_setting( 'theme_options[slider_author_enable]', array(
	'default'           => $default['slider_author_enable'],
	'sanitize_callback' => 'sensational_blog_sanitize_checkbox',
) );

$wp_customize->add_control( 'theme_options[slider_author_enable]', array(
	'label'             => esc_html__( 'Enable Author', 'sensational-blog' ),
	'section'           => 'section_featured_slider',
	'type'              => 'checkbox',
	'active_callback' => 'sensational_blog_slider_active',
) );
// Add posted on enable setting and control.
$wp_customize->add_setting( 'theme_options[slider_posted_on_enable]', array(
	'default'           => $default['slider_posted_on_enable'],
	'sanitize_callback' => 'sensational_blog_sanitize_checkbox',
) );

$wp_customize->add_control( 'theme_options[slider_posted_on_enable]', array(
	'label'             => esc_html__( 'Enable Posted Date', 'sensational-blog' ),
	'section'           => 'section_featured_slider',
	'type'              => 'checkbox',
	'active_callback' => 'sensational_blog_slider_active',
) );

// Number of items
$wp_customize->add_setting('theme_options[slider_speed]', 
	array(
	'default' 			=> $default['slider_speed'],
	'type'              => 'theme_mod',
	'capability'        => 'edit_theme_options',	
	'sanitize_callback' => 'sensational_blog_sanitize_number_range'
	)
);

$wp_customize->add_control('theme_options[slider_speed]', 
	array(
	'label'       => __('Slider Speed', 'sensational-blog'),
	'description' => __('Slider Speed Default speed 800', 'sensational-blog'),
	'section'     => 'section_featured_slider',   
	'settings'    => 'theme_options[slider_speed]',		
	'type'        => 'number',
	'active_callback' => 'sensational_blog_slider_active',
	)
);

$wp_customize->add_setting( 'theme_options[slider_dot]',
	array(

		'type'              => 'theme_mod',
		'capability'        => 'edit_theme_options',
		'sanitize_callback' => 'sensational_blog_sanitize_switch_control',
	)
);
$wp_customize->add_control( new Sensational_Blog_Switch_Control( $wp_customize, 'theme_options[slider_dot]',
    array(
		'label' 	=> __('Disable Slider Dots', 'sensational-blog'),
		'section'    			=> 'section_featured_slider',
		'on_off_label' 		=> sensational_blog_switch_options(),
		'active_callback' => 'sensational_blog_slider_active',
    )
) );

$number_of_sr_items = sensational_blog_get_option( 'number_of_sr_items' );
for( $i=1; $i<=$number_of_sr_items; $i++ ){

	// Additional Information First Post
	$wp_customize->add_setting('theme_options[slider_post_'.$i.']', 
		array(
		'type'              => 'theme_mod',
		'capability'        => 'edit_theme_options',	
		'sanitize_callback' => 'sensational_blog_dropdown_posts'
		)
	);
	$wp_customize->add_control( new Sensational_Blog_Dropdown_Chooser( $wp_customize,'theme_options[slider_post_'.$i.']', 
		array(
		'label'       => sprintf( __('Select Post #%1$s', 'sensational-blog'), $i),
		'section'     => 'section_featured_slider',  
		'settings'    => 'theme_options[slider_post_'.$i.']',	
		'choices'	=> sensational_blog_post_choices(),	
		'type'        => 'dropdown-posts',
		'active_callback' => 'sensational_blog_slider_active',
		)
	));

	// Cta Button Text
	$wp_customize->add_setting('theme_options[slider_custom_btn_text_' . $i . ']', 
		array(

		'type'              => 'theme_mod',
		'capability'        => 'edit_theme_options',	
		'sanitize_callback' => 'sanitize_text_field'
		)
	);

	$wp_customize->add_control('theme_options[slider_custom_btn_text_' . $i . ']', 
		array(
		'label'       => sprintf( __('Button Label %d', 'sensational-blog'),$i ),
		'section'     => 'section_featured_slider',   
		'settings'    => 'theme_options[slider_custom_btn_text_' . $i . ']',	
		'active_callback' => 'sensational_blog_slider_active',	
		'type'        => 'text',
		)
	);

	// slider hr setting and control
	$wp_customize->add_setting( 'theme_options[slider_hr_'. $i .']', array(
		'sanitize_callback' => 'sanitize_text_field'
	) );

	$wp_customize->add_control( new Sensational_Blog_Customize_Horizontal_Line( $wp_customize, 'theme_options[slider_hr_'. $i .']',
		array(
			'section'         => 'section_featured_slider',
			'active_callback' => 'sensational_blog_slider_active',
			'type'			  => 'hr',
	) ) );
}
// Slider Button Text
$wp_customize->add_setting('theme_options[slider_alt_custom_btn_text]', 
	array(

	'type'              => 'theme_mod',
	'capability'        => 'edit_theme_options',	
	'sanitize_callback' => 'sanitize_text_field'
	)
);

$wp_customize->add_control('theme_options[slider_alt_custom_btn_text]', 
	array(
	'label'       => __('Alternative Button Label', 'sensational-blog'),
	'section'     => 'section_featured_slider',   
	'settings'    => 'theme_options[slider_alt_custom_btn_text]',	
	'active_callback' => 'sensational_blog_slider_active',	
	'type'        => 'text',
	)
);

	// Slider Button Url
$wp_customize->add_setting('theme_options[slider_alt_custom_btn_url]', 
	array(

	'type'              => 'theme_mod',
	'capability'        => 'edit_theme_options',	
	'sanitize_callback' => 'esc_url_raw',
	)
);

$wp_customize->add_control('theme_options[slider_alt_custom_btn_url]', 
	array(
	'label'       => __('Alternative Button Url', 'sensational-blog'),
	'section'     => 'section_featured_slider',   
	'settings'    => 'theme_options[slider_alt_custom_btn_url]',	
	'active_callback' => 'sensational_blog_slider_active',	
	'type'        => 'url',
	)
);
