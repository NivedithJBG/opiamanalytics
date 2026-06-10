<?php
/**
 * Excerpt
 *
 * @package Digital Blog
 */

$wp_customize->add_section(
	'digital_blog_excerpt_options',
	array(
		'panel' => 'digital_blog_theme_options',
		'title' => esc_html__( 'Excerpt', 'digital-blog' ),
	)
);

// Excerpt - Excerpt Length.
$wp_customize->add_setting(
	'digital_blog_excerpt_length',
	array(
		'default'           => 20,
		'sanitize_callback' => 'digital_blog_sanitize_number_range',
		'validate_callback' => 'digital_blog_validate_excerpt_length',
	)
);

$wp_customize->add_control(
	'digital_blog_excerpt_length',
	array(
		'label'       => esc_html__( 'Excerpt Length (no. of words)', 'digital-blog' ),
		'description' => esc_html__( 'Note: Min 1 & Max 100. Please input the valid number and save. Then refresh the page to see the change.', 'digital-blog' ),
		'section'     => 'digital_blog_excerpt_options',
		'settings'    => 'digital_blog_excerpt_length',
		'type'        => 'number',
		'input_attrs' => array(
			'min'  => 1,
			'max'  => 100,
			'step' => 1,
		),
	)
);
