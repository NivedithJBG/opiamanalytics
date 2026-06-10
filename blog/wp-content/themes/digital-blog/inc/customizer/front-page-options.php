<?php
/**
 * Front Page Options
 *
 * @package Digital Blog
 */

$wp_customize->add_panel(
	'digital_blog_front_page_options',
	array(
		'title'    => esc_html__( 'Front Page Options', 'digital-blog' ),
		'priority' => 130,
	)
);

// Banner Section.
require get_template_directory() . '/inc/customizer/front-page-options/banner.php';

// Categories Section.
require get_template_directory() . '/inc/customizer/front-page-options/categories.php';

// Post Carousel Section.
require get_template_directory() . '/inc/customizer/front-page-options/post-carousel.php';
