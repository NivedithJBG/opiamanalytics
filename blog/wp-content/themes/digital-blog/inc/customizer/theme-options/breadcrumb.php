<?php
/**
 * Breadcrumb
 *
 * @package Digital Blog
 */

$wp_customize->add_section(
	'digital_blog_breadcrumb',
	array(
		'title' => esc_html__( 'Breadcrumb', 'digital-blog' ),
		'panel' => 'digital_blog_theme_options',
	)
);

// Breadcrumb - Enable Breadcrumb.
$wp_customize->add_setting(
	'digital_blog_enable_breadcrumb',
	array(
		'sanitize_callback' => 'digital_blog_sanitize_switch',
		'default'           => true,
	)
);

$wp_customize->add_control(
	new Digital_Blog_Toggle_Switch_Custom_Control(
		$wp_customize,
		'digital_blog_enable_breadcrumb',
		array(
			'label'   => esc_html__( 'Enable Breadcrumb', 'digital-blog' ),
			'section' => 'digital_blog_breadcrumb',
		)
	)
);

// Breadcrumb - Separator.
$wp_customize->add_setting(
	'digital_blog_breadcrumb_separator',
	array(
		'sanitize_callback' => 'sanitize_text_field',
		'default'           => '/',
	)
);

$wp_customize->add_control(
	'digital_blog_breadcrumb_separator',
	array(
		'label'           => esc_html__( 'Separator', 'digital-blog' ),
		'active_callback' => 'digital_blog_is_breadcrumb_enabled',
		'section'         => 'digital_blog_breadcrumb',
	)
);
