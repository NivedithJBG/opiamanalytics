<?php
/**
 * Sidebar Position
 *
 * @package Digital Blog
 */

$wp_customize->add_section(
	'digital_blog_sidebar_position',
	array(
		'title' => esc_html__( 'Sidebar Position', 'digital-blog' ),
		'panel' => 'digital_blog_theme_options',
	)
);

// Sidebar Position - Global Sidebar Position.
$wp_customize->add_setting(
	'digital_blog_sidebar_position',
	array(
		'sanitize_callback' => 'digital_blog_sanitize_select',
		'default'           => 'right-sidebar',
	)
);

$wp_customize->add_control(
	'digital_blog_sidebar_position',
	array(
		'label'   => esc_html__( 'Global Sidebar Position', 'digital-blog' ),
		'section' => 'digital_blog_sidebar_position',
		'type'    => 'select',
		'choices' => array(
			'right-sidebar' => esc_html__( 'Right Sidebar', 'digital-blog' ),
			'no-sidebar'    => esc_html__( 'No Sidebar', 'digital-blog' ),
		),
	)
);

// Sidebar Position - Post Sidebar Position.
$wp_customize->add_setting(
	'digital_blog_post_sidebar_position',
	array(
		'sanitize_callback' => 'digital_blog_sanitize_select',
		'default'           => 'right-sidebar',
	)
);

$wp_customize->add_control(
	'digital_blog_post_sidebar_position',
	array(
		'label'   => esc_html__( 'Post Sidebar Position', 'digital-blog' ),
		'section' => 'digital_blog_sidebar_position',
		'type'    => 'select',
		'choices' => array(
			'right-sidebar' => esc_html__( 'Right Sidebar', 'digital-blog' ),
			'no-sidebar'    => esc_html__( 'No Sidebar', 'digital-blog' ),
		),
	)
);

// Sidebar Position - Page Sidebar Position.
$wp_customize->add_setting(
	'digital_blog_page_sidebar_position',
	array(
		'sanitize_callback' => 'digital_blog_sanitize_select',
		'default'           => 'right-sidebar',
	)
);

$wp_customize->add_control(
	'digital_blog_page_sidebar_position',
	array(
		'label'   => esc_html__( 'Page Sidebar Position', 'digital-blog' ),
		'section' => 'digital_blog_sidebar_position',
		'type'    => 'select',
		'choices' => array(
			'right-sidebar' => esc_html__( 'Right Sidebar', 'digital-blog' ),
			'no-sidebar'    => esc_html__( 'No Sidebar', 'digital-blog' ),
		),
	)
);
