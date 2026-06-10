<?php
/**
 * Typography
 *
 * @package Digital Blog
 */

$wp_customize->add_section(
	'digital_blog_typography',
	array(
		'panel' => 'digital_blog_theme_options',
		'title' => esc_html__( 'Typography', 'digital-blog' ),
	)
);

// Typography - Site Title Font.
$wp_customize->add_setting(
	'digital_blog_site_title_font',
	array(
		'default'           => 'Sora',
		'sanitize_callback' => 'digital_blog_sanitize_google_fonts',
	)
);

$wp_customize->add_control(
	'digital_blog_site_title_font',
	array(
		'label'    => esc_html__( 'Site Title Font Family', 'digital-blog' ),
		'section'  => 'digital_blog_typography',
		'settings' => 'digital_blog_site_title_font',
		'type'     => 'select',
		'choices'  => digital_blog_get_all_google_font_families(),
	)
);

// Typography - Site Description Font.
$wp_customize->add_setting(
	'digital_blog_site_description_font',
	array(
		'default'           => 'Comfortaa',
		'sanitize_callback' => 'digital_blog_sanitize_google_fonts',
	)
);

$wp_customize->add_control(
	'digital_blog_site_description_font',
	array(
		'label'    => esc_html__( 'Site Description Font Family', 'digital-blog' ),
		'section'  => 'digital_blog_typography',
		'settings' => 'digital_blog_site_description_font',
		'type'     => 'select',
		'choices'  => digital_blog_get_all_google_font_families(),
	)
);

// Typography - Header Font.
$wp_customize->add_setting(
	'digital_blog_header_font',
	array(
		'default'           => 'Inter',
		'sanitize_callback' => 'digital_blog_sanitize_google_fonts',
	)
);

$wp_customize->add_control(
	'digital_blog_header_font',
	array(
		'label'    => esc_html__( 'Header Font Family', 'digital-blog' ),
		'section'  => 'digital_blog_typography',
		'settings' => 'digital_blog_header_font',
		'type'     => 'select',
		'choices'  => digital_blog_get_all_google_font_families(),
	)
);

// Typography - Body Font.
$wp_customize->add_setting(
	'digital_blog_body_font',
	array(
		'default'           => 'Inter',
		'sanitize_callback' => 'digital_blog_sanitize_google_fonts',
	)
);

$wp_customize->add_control(
	'digital_blog_body_font',
	array(
		'label'    => esc_html__( 'Body Font Family', 'digital-blog' ),
		'section'  => 'digital_blog_typography',
		'settings' => 'digital_blog_body_font',
		'type'     => 'select',
		'choices'  => digital_blog_get_all_google_font_families(),
	)
);
