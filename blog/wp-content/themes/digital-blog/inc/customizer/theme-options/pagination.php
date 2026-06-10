<?php
/**
 * Pagination
 *
 * @package Digital Blog
 */

$wp_customize->add_section(
	'digital_blog_pagination',
	array(
		'panel' => 'digital_blog_theme_options',
		'title' => esc_html__( 'Pagination', 'digital-blog' ),
	)
);

// Pagination - Enable Pagination.
$wp_customize->add_setting(
	'digital_blog_enable_pagination',
	array(
		'default'           => true,
		'sanitize_callback' => 'digital_blog_sanitize_switch',
	)
);

$wp_customize->add_control(
	new Digital_Blog_Toggle_Switch_Custom_Control(
		$wp_customize,
		'digital_blog_enable_pagination',
		array(
			'label'    => esc_html__( 'Enable Pagination', 'digital-blog' ),
			'section'  => 'digital_blog_pagination',
			'settings' => 'digital_blog_enable_pagination',
			'type'     => 'checkbox',
		)
	)
);

// Pagination - Pagination Type.
$wp_customize->add_setting(
	'digital_blog_pagination_type',
	array(
		'default'           => 'numeric',
		'sanitize_callback' => 'digital_blog_sanitize_select',
	)
);

$wp_customize->add_control(
	'digital_blog_pagination_type',
	array(
		'label'           => esc_html__( 'Pagination Type', 'digital-blog' ),
		'section'         => 'digital_blog_pagination',
		'settings'        => 'digital_blog_pagination_type',
		'active_callback' => 'digital_blog_is_pagination_enabled',
		'type'            => 'select',
		'choices'         => array(
			'default' => __( 'Default (Older/Newer)', 'digital-blog' ),
			'numeric' => __( 'Numeric', 'digital-blog' ),
		),
	)
);
