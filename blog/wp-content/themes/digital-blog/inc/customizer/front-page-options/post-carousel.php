<?php
/**
 * Post Carousel Section
 *
 * @package Digital Blog
 */

$wp_customize->add_section(
	'digital_blog_post_carousel_section',
	array(
		'panel' => 'digital_blog_front_page_options',
		'title' => esc_html__( 'Post Carousel Section', 'digital-blog' ),
	)
);

// Post Carousel Section - Enable Section.
$wp_customize->add_setting(
	'digital_blog_enable_post_carousel_section',
	array(
		'default'           => false,
		'sanitize_callback' => 'digital_blog_sanitize_switch',
	)
);

$wp_customize->add_control(
	new Digital_Blog_Toggle_Switch_Custom_Control(
		$wp_customize,
		'digital_blog_enable_post_carousel_section',
		array(
			'label'    => esc_html__( 'Enable Post Carousel Section', 'digital-blog' ),
			'section'  => 'digital_blog_post_carousel_section',
			'settings' => 'digital_blog_enable_post_carousel_section',
		)
	)
);

if ( isset( $wp_customize->selective_refresh ) ) {
	$wp_customize->selective_refresh->add_partial(
		'digital_blog_enable_post_carousel_section',
		array(
			'selector' => '#digital_blog_post_carousel_section .section-link',
			'settings' => 'digital_blog_enable_post_carousel_section',
		)
	);
}

// Post Carousel Section - Section Title.
$wp_customize->add_setting(
	'digital_blog_post_carousel_title',
	array(
		'default'           => __( 'Post Carousel', 'digital-blog' ),
		'sanitize_callback' => 'sanitize_text_field',
	)
);

$wp_customize->add_control(
	'digital_blog_post_carousel_title',
	array(
		'label'           => esc_html__( 'Section Title', 'digital-blog' ),
		'section'         => 'digital_blog_post_carousel_section',
		'settings'        => 'digital_blog_post_carousel_title',
		'type'            => 'text',
		'active_callback' => 'digital_blog_is_post_carousel_section_enabled',
	)
);

// Post Carousel Section - Section Subtitle.
$wp_customize->add_setting(
	'digital_blog_post_carousel_subtitle',
	array(
		'default'           => '',
		'sanitize_callback' => 'sanitize_text_field',
	)
);

$wp_customize->add_control(
	'digital_blog_post_carousel_subtitle',
	array(
		'label'           => esc_html__( 'Section Subtitle', 'digital-blog' ),
		'section'         => 'digital_blog_post_carousel_section',
		'settings'        => 'digital_blog_post_carousel_subtitle',
		'type'            => 'text',
		'active_callback' => 'digital_blog_is_post_carousel_section_enabled',
	)
);

// Post Carousel Section - Content Type.
$wp_customize->add_setting(
	'digital_blog_post_carousel_content_type',
	array(
		'default'           => 'post',
		'sanitize_callback' => 'digital_blog_sanitize_select',
	)
);

$wp_customize->add_control(
	'digital_blog_post_carousel_content_type',
	array(
		'label'           => esc_html__( 'Select Content Type', 'digital-blog' ),
		'section'         => 'digital_blog_post_carousel_section',
		'settings'        => 'digital_blog_post_carousel_content_type',
		'type'            => 'select',
		'active_callback' => 'digital_blog_is_post_carousel_section_enabled',
		'choices'         => array(
			'post'     => esc_html__( 'Post', 'digital-blog' ),
			'category' => esc_html__( 'Category', 'digital-blog' ),
		),
	)
);

for ( $i = 1; $i <= 4; $i++ ) {
	// Post Carousel Section - Select Post.
	$wp_customize->add_setting(
		'digital_blog_post_carousel_content_post_' . $i,
		array(
			'sanitize_callback' => 'absint',
		)
	);

	$wp_customize->add_control(
		'digital_blog_post_carousel_content_post_' . $i,
		array(
			'label'           => sprintf( esc_html__( 'Select Post %d', 'digital-blog' ), $i ),
			'section'         => 'digital_blog_post_carousel_section',
			'settings'        => 'digital_blog_post_carousel_content_post_' . $i,
			'active_callback' => 'digital_blog_is_post_carousel_section_and_content_type_post_enabled',
			'type'            => 'select',
			'choices'         => digital_blog_get_post_choices(),
		)
	);

}

// Post Carousel Section - Select Category.
$wp_customize->add_setting(
	'digital_blog_post_carousel_content_category',
	array(
		'sanitize_callback' => 'digital_blog_sanitize_select',
	)
);

$wp_customize->add_control(
	'digital_blog_post_carousel_content_category',
	array(
		'label'           => esc_html__( 'Select Category', 'digital-blog' ),
		'section'         => 'digital_blog_post_carousel_section',
		'settings'        => 'digital_blog_post_carousel_content_category',
		'active_callback' => 'digital_blog_is_post_carousel_section_and_content_type_category_enabled',
		'type'            => 'select',
		'choices'         => digital_blog_get_post_cat_choices(),
	)
);
