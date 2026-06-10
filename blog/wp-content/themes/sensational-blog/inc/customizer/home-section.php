<?php
/**
 * Home Page Options.
 *
 * @package Sensational Blog
 */

$default = sensational_blog_get_default_theme_options();
$homepage_design_layout     = sensational_blog_get_option( 'homepage_design_layout_options' );

// Add Panel.
$wp_customize->add_panel( 'home_page_panel',
	array(
	'title'      => __( 'Front Page Sections', 'sensational-blog' ),
	'priority'   => 100,
	'capability' => 'edit_theme_options',
	)
);

/**
* Section Customizer Options.
*/

if ($homepage_design_layout=='home-corporate') {
	require get_template_directory() . '/inc/customizer/sections/header.php';
	require get_template_directory() . '/inc/customizer/sections/home-layout.php';
	require get_template_directory() . '/inc/customizer/sections/slider.php';
	require get_template_directory() . '/inc/customizer/sections/highlights.php';
	require get_template_directory() . '/inc/customizer/sections/trending.php';
	require get_template_directory() . '/inc/customizer/sections/recent.php';
	require get_template_directory() . '/inc/customizer/sections/mustread.php';
}
