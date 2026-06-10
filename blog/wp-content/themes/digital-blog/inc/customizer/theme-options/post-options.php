<?php
/**
 * Post Options
 *
 * @package Digital Blog
 */

$wp_customize->add_section(
	'digital_blog_post_options',
	array(
		'title' => esc_html__( 'Post Options', 'digital-blog' ),
		'panel' => 'digital_blog_theme_options',
	)
);

// Post Options - Hide Date.
$wp_customize->add_setting(
	'digital_blog_post_hide_date',
	array(
		'default'           => false,
		'sanitize_callback' => 'digital_blog_sanitize_switch',
	)
);

$wp_customize->add_control(
	new Digital_Blog_Toggle_Switch_Custom_Control(
		$wp_customize,
		'digital_blog_post_hide_date',
		array(
			'label'   => esc_html__( 'Hide Date', 'digital-blog' ),
			'section' => 'digital_blog_post_options',
		)
	)
);

// Post Options - Hide Author.
$wp_customize->add_setting(
	'digital_blog_post_hide_author',
	array(
		'default'           => false,
		'sanitize_callback' => 'digital_blog_sanitize_switch',
	)
);

$wp_customize->add_control(
	new Digital_Blog_Toggle_Switch_Custom_Control(
		$wp_customize,
		'digital_blog_post_hide_author',
		array(
			'label'   => esc_html__( 'Hide Author', 'digital-blog' ),
			'section' => 'digital_blog_post_options',
		)
	)
);

// Post Options - Hide Category.
$wp_customize->add_setting(
	'digital_blog_post_hide_category',
	array(
		'default'           => false,
		'sanitize_callback' => 'digital_blog_sanitize_switch',
	)
);

$wp_customize->add_control(
	new Digital_Blog_Toggle_Switch_Custom_Control(
		$wp_customize,
		'digital_blog_post_hide_category',
		array(
			'label'   => esc_html__( 'Hide Category', 'digital-blog' ),
			'section' => 'digital_blog_post_options',
		)
	)
);

// Post Options - Hide Tag.
$wp_customize->add_setting(
	'digital_blog_post_hide_tags',
	array(
		'default'           => false,
		'sanitize_callback' => 'digital_blog_sanitize_switch',
	)
);

$wp_customize->add_control(
	new Digital_Blog_Toggle_Switch_Custom_Control(
		$wp_customize,
		'digital_blog_post_hide_tags',
		array(
			'label'   => esc_html__( 'Hide Tag', 'digital-blog' ),
			'section' => 'digital_blog_post_options',
		)
	)
);

// Post Options - Related Post Label.
$wp_customize->add_setting(
	'digital_blog_post_related_post_label',
	array(
		'default'           => __( 'Related Posts', 'digital-blog' ),
		'sanitize_callback' => 'sanitize_text_field',
	)
);

$wp_customize->add_control(
	'digital_blog_post_related_post_label',
	array(
		'label'    => esc_html__( 'Related Posts Label', 'digital-blog' ),
		'section'  => 'digital_blog_post_options',
		'settings' => 'digital_blog_post_related_post_label',
		'type'     => 'text',
	)
);
