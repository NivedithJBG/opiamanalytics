<?php
/**
 * Home Page Options.
 *
 * @package Sensational Blog
 */

$default = sensational_blog_get_default_theme_options();

// Latest Latest Posts Section
$wp_customize->add_section( 'section_home_latest_posts',
	array(
		'title'      => __( 'Blog And Archive', 'sensational-blog' ),
		'priority'   => 110,
		'capability' => 'edit_theme_options',
		'panel'      => 'theme_option_panel',
		)
);
$wp_customize->add_setting('theme_options[blog_layout_content_type]', 
	array(
	'default' 			=> $default['blog_layout_content_type'],
	'type'              => 'theme_mod',
	'capability'        => 'edit_theme_options',	
	'sanitize_callback' => 'sensational_blog_sanitize_select'
	)
);

$wp_customize->add_control('theme_options[blog_layout_content_type]', 
	array(
	'label'       => __('Blog And Archive page layout Content Type', 'sensational-blog'),
	'section'     => 'section_home_latest_posts',   
	'settings'    => 'theme_options[blog_layout_content_type]',		
	'type'        => 'select',
	'choices'	  => array(
			'blog-one'	  => __('Design One','sensational-blog'),
			'blog-two'	  => __('Design Two','sensational-blog'),
			'blog-three'	  => __('Design Three','sensational-blog'),
		),
	)
);
// Latest Posts title
$wp_customize->add_setting('theme_options[latest_posts_title]', 
	array(
	'default'           => $default['latest_posts_title'],
	'type'              => 'theme_mod',
	'capability'        => 'edit_theme_options',	
	'sanitize_callback' => 'sanitize_text_field'
	)
);

$wp_customize->add_control('theme_options[latest_posts_title]', 
	array(
	'label'       => __('Static Blog Page Header Title', 'sensational-blog'),
	'section'     => 'section_home_latest_posts',   
	'settings'    => 'theme_options[latest_posts_title]',		
	'type'        => 'text'
	)
);

// Latest Posts title
$wp_customize->add_setting('theme_options[latest_section_posts_title]', 
	array(
	'default'           => $default['latest_section_posts_title'],
	'type'              => 'theme_mod',
	'capability'        => 'edit_theme_options',	
	'sanitize_callback' => 'sanitize_text_field'
	)
);

$wp_customize->add_control('theme_options[latest_section_posts_title]', 
	array(
	'label'       => __('Blog Page Header Title', 'sensational-blog'),
	'description' => __('This Setting works on the Latest posts option chosen as the Homepage Setting.', 'sensational-blog'),
	'section'     => 'section_home_latest_posts',   
	'settings'    => 'theme_options[latest_section_posts_title]',		
	'type'        => 'text'
	)
);
// Number of Blog Posts
$wp_customize->add_setting('theme_options[number_of_latest_posts_column]', 
	array(
	'default' 			=> $default['number_of_latest_posts_column'],
	'type'              => 'theme_mod',
	'capability'        => 'edit_theme_options',	
	'sanitize_callback' => 'sensational_blog_sanitize_number_range'
	)
);

$wp_customize->add_control('theme_options[number_of_latest_posts_column]', 
	array(
	'label'       => __('Column Per Row', 'sensational-blog'),
	'description' => __('Save & Refresh the customizer to see its effect. Maximum is 3', 'sensational-blog'),
	'section'     => 'section_home_latest_posts',   
	'settings'    => 'theme_options[number_of_latest_posts_column]',		
	'type'        => 'number',
	'input_attrs' => array(
			'min'	=> 1,
			'max'	=> 3,
			'step'	=> 1,
		),
	)
);


// Setting excerpt_length.
$wp_customize->add_setting( 'theme_options[excerpt_length]', array(
	'default'           => $default['excerpt_length'],
	'sanitize_callback' => 'sensational_blog_sanitize_positive_integer',
) );
$wp_customize->add_control( 'theme_options[excerpt_length]', array(
	'label'       => esc_html__( 'Excerpt Length', 'sensational-blog' ),
	'description' => esc_html__( 'in words', 'sensational-blog' ),
	'section'     => 'section_home_latest_posts',
	'type'        => 'number',
	'input_attrs' => array( 'min' => 1, 'max' => 200, 'style' => 'width: 55px;' ),
) );

// Add arrow enable setting and control.
$wp_customize->add_setting( 'theme_options[archive_content_align]', array(
	'default'           => $default['archive_content_align'],
	'sanitize_callback' => 'sensational_blog_sanitize_select',
) );

$wp_customize->add_control( 'theme_options[archive_content_align]', array(
	'label'             => esc_html__( 'Choose Content Align', 'sensational-blog' ),
	'section'           => 'section_home_latest_posts',
	'type'              => 'radio',
	'choices'				=> array( 
		'content-right'     => esc_html__( 'Right Side', 'sensational-blog' ), 
		'content-center'     => esc_html__( 'Center Side', 'sensational-blog' ), 
		'content-left'     => esc_html__( 'Left Side', 'sensational-blog' ), 
		'content-justify'     => esc_html__( 'Justify', 'sensational-blog' ),
		)
) );

// Add Single Header Image enable setting and control.
$wp_customize->add_setting( 'theme_options[archive_post_header_title_enable]', array(
	'default'           => $default['archive_post_header_title_enable'],
	'sanitize_callback' => 'sensational_blog_sanitize_checkbox',
) );

$wp_customize->add_control( 'theme_options[archive_post_header_title_enable]', array(
	'label'             => esc_html__( 'Enable Archive Header Title', 'sensational-blog' ),
	'section'           => 'section_home_latest_posts',
	'type'              => 'checkbox',

) );

// Add Single Header Image enable setting and control.
$wp_customize->add_setting( 'theme_options[blog_post_header_image_enable]', array(
	'default'           => $default['blog_post_header_image_enable'],
	'sanitize_callback' => 'sensational_blog_sanitize_checkbox',
) );

$wp_customize->add_control( 'theme_options[blog_post_header_image_enable]', array(
	'label'             => esc_html__( 'Enable Blog Page Header Image', 'sensational-blog' ),
	'section'           => 'section_home_latest_posts',
	'type'              => 'checkbox',

) );


// Add Single Header Image enable setting and control.
$wp_customize->add_setting( 'theme_options[archive_post_header_image_enable]', array(
	'default'           => $default['archive_post_header_image_enable'],
	'sanitize_callback' => 'sensational_blog_sanitize_checkbox',
) );

$wp_customize->add_control( 'theme_options[archive_post_header_image_enable]', array(
	'label'             => esc_html__( 'Enable Archive Header Image', 'sensational-blog' ),
	'section'           => 'section_home_latest_posts',
	'type'              => 'checkbox',

) );

// Add Single Header Image enable setting and control.
$wp_customize->add_setting( 'theme_options[blog_post_header_title_enable]', array(
	'default'           => $default['blog_post_header_title_enable'],
	'sanitize_callback' => 'sensational_blog_sanitize_checkbox',
) );

$wp_customize->add_control( 'theme_options[blog_post_header_title_enable]', array(
	'label'             => esc_html__( 'Enable Blog Header Title', 'sensational-blog' ),
	'section'           => 'section_home_latest_posts',
	'type'              => 'checkbox',

) );

// Add category enable setting and control.
$wp_customize->add_setting( 'theme_options[background_image_enable]', array(
	'default'           => $default['background_image_enable'],
	'sanitize_callback' => 'sensational_blog_sanitize_checkbox',
) );

$wp_customize->add_control( 'theme_options[background_image_enable]', array(
	'label'             => esc_html__( 'Enable background Image', 'sensational-blog' ),
	'section'           => 'section_home_latest_posts',
	'active_callback' => 'sensational_blog_blog_four_design_enable',
	'type'              => 'checkbox',

) );

// Add category enable setting and control.
$wp_customize->add_setting( 'theme_options[latest_category_enable]', array(
	'default'           => $default['latest_category_enable'],
	'sanitize_callback' => 'sensational_blog_sanitize_checkbox',
) );

$wp_customize->add_control( 'theme_options[latest_category_enable]', array(
	'label'             => esc_html__( 'Enable Category', 'sensational-blog' ),
	'section'           => 'section_home_latest_posts',
	'type'              => 'checkbox',

) );
// Add category enable setting and control.
$wp_customize->add_setting( 'theme_options[latest_author_enable]', array(
	'default'           => $default['latest_author_enable'],
	'sanitize_callback' => 'sensational_blog_sanitize_checkbox',
) );

$wp_customize->add_control( 'theme_options[latest_author_enable]', array(
	'label'             => esc_html__( 'Enable Author', 'sensational-blog' ),
	'section'           => 'section_home_latest_posts',
	'type'              => 'checkbox',

) );
// Add category enable setting and control.
$wp_customize->add_setting( 'theme_options[latest_comment_enable]', array(
	'default'           => $default['latest_comment_enable'],
	'sanitize_callback' => 'sensational_blog_sanitize_checkbox',
) );

$wp_customize->add_control( 'theme_options[latest_comment_enable]', array(
	'label'             => esc_html__( 'Enable Comment', 'sensational-blog' ),
	'section'           => 'section_home_latest_posts',
	'type'              => 'checkbox',

) );
// Add category enable setting and control.
$wp_customize->add_setting( 'theme_options[latest_read_more_text_enable]', array(
	'default'           => $default['latest_read_more_text_enable'],
	'sanitize_callback' => 'sensational_blog_sanitize_checkbox',
) );

$wp_customize->add_control( 'theme_options[latest_read_more_text_enable]', array(
	'label'             => esc_html__( 'Enable Read More Text', 'sensational-blog' ),
	'description' => __('Enable read more text inside content and disable read more button.', 'sensational-blog'),
	'section'           => 'section_home_latest_posts',
	'type'              => 'checkbox',

) );

// Add posted on enable setting and control.
$wp_customize->add_setting( 'theme_options[latest_posted_on_enable]', array(
	'default'           => $default['latest_posted_on_enable'],
	'sanitize_callback' => 'sensational_blog_sanitize_checkbox',
) );

$wp_customize->add_control( 'theme_options[latest_posted_on_enable]', array(
	'label'             => esc_html__( 'Enable Date', 'sensational-blog' ),
	'section'           => 'section_home_latest_posts',
	'type'              => 'checkbox',

) );

// Add category enable setting and control.
$wp_customize->add_setting( 'theme_options[latest_video_enable]', array(
	'default'           => $default['latest_video_enable'],
	'sanitize_callback' => 'sensational_blog_sanitize_checkbox',
) );

$wp_customize->add_control( 'theme_options[latest_video_enable]', array(
	'label'             => esc_html__( 'Enable Featured Video', 'sensational-blog' ),
	'section'           => 'section_home_latest_posts',
	'type'              => 'checkbox',

) );

// Header Font Size
$wp_customize->add_setting('theme_options[latest_font_size]', 
	array(
	'sanitize_callback' => 'absint',
	'default'			=> 30,
	)
);

$wp_customize->add_control(new Sensational_Blog_Range_Value_Control($wp_customize,'theme_options[latest_font_size]',
	array(
		'type'     => 'range-value',
		'section'  => 'section_home_latest_posts',
	
		'label'    => esc_html__( 'Header Font Size(px)', 'sensational-blog' ),
		'description' => __(' Default:30px. Max:45px Min:16px .', 'sensational-blog'),
		'input_attrs' => array(
			'min'    => 16,
			'max'    => 45,
			'step'   => 1,
	)
)));

$wp_customize->add_setting('theme_options[latest_readmore_text]', 
	array(
	'default'           => $default['latest_readmore_text'],
	'type'              => 'theme_mod',
	'capability'        => 'edit_theme_options',	
	'sanitize_callback' => 'sanitize_text_field'
	)
);

$wp_customize->add_control('theme_options[latest_readmore_text]', 
	array(
	'label'       => __('Button Label', 'sensational-blog'),
	'section'     => 'section_home_latest_posts',   
	'settings'    => 'theme_options[latest_readmore_text]',	
	'type'        => 'text'
	)
);

$wp_customize->add_setting('theme_options[pagination_type]', 
	array(
	'default' 			=> $default['pagination_type'],
	'type'              => 'theme_mod',
	'capability'        => 'edit_theme_options',	
	'sanitize_callback' => 'sensational_blog_sanitize_select'
	)
);

$wp_customize->add_control('theme_options[pagination_type]', 
	array(
	'label'       => __('Pagination Type', 'sensational-blog'),
	'section'     => 'section_home_latest_posts',   
	'settings'    => 'theme_options[pagination_type]',		
	'type'        => 'select',
	'choices'	  => array(
		'default' 		=> esc_html__( 'Default', 'sensational-blog' ),
		'numeric' 		=> esc_html__( 'Numeric', 'sensational-blog' ),
		),
	)
);