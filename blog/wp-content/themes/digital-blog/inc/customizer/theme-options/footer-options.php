<?php
/**
 * Footer Options
 *
 * @package Digital Blog
 */

$wp_customize->add_section(
	'digital_blog_footer_options',
	array(
		'panel' => 'digital_blog_theme_options',
		'title' => esc_html__( 'Footer Options', 'digital-blog' ),
	)
);

// Footer Options - Copyright Text.
/* translators: 1: Year, 2: Site Title with home URL. */
$copyright_default = sprintf( esc_html_x( 'Copyright &copy; %1$s %2$s', '1: Year, 2: Site Title with home URL', 'digital-blog' ), '[the-year]', '[site-link]' );
$wp_customize->add_setting(
	'digital_blog_footer_copyright_text',
	array(
		'default'           => $copyright_default,
		'sanitize_callback' => 'wp_kses_post',
		'transport'         => 'refresh',
	)
);

$wp_customize->add_control(
	'digital_blog_footer_copyright_text',
	array(
		'label'    => esc_html__( 'Copyright Text', 'digital-blog' ),
		'section'  => 'digital_blog_footer_options',
		'settings' => 'digital_blog_footer_copyright_text',
		'type'     => 'textarea',
	)
);
