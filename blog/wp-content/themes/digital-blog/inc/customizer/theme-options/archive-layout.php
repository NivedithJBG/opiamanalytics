<?php
/**
 * Archive Layout
 *
 * @package Digital Blog
 */

$wp_customize->add_section(
	'digital_blog_archive_layout',
	array(
		'title' => esc_html__( 'Archive Layout', 'digital-blog' ),
		'panel' => 'digital_blog_theme_options',
	)
);

// Archive Layout - Column Layout.
$wp_customize->add_setting(
	'digital_blog_archive_column_layout',
	array(
		'default'           => 'column-2',
		'sanitize_callback' => 'digital_blog_sanitize_select',
	)
);

$wp_customize->add_control(
	'digital_blog_archive_column_layout',
	array(
		'label'   => esc_html__( 'Select Column Layout', 'digital-blog' ),
		'section' => 'digital_blog_archive_layout',
		'type'    => 'select',
		'choices' => array(
			'column-2' => __( 'Column 2', 'digital-blog' ),
			'column-3' => __( 'Column 3', 'digital-blog' ),
		),
	)
);
