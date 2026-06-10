<?php
/**
 * Banner Section
 *
 * @package Digital Blog
 */

$wp_customize->add_section(
	'digital_blog_banner_section',
	array(
		'panel' => 'digital_blog_front_page_options',
		'title' => esc_html__( 'Banner Section', 'digital-blog' ),
	)
);

// Banner Section - Enable Section.
$wp_customize->add_setting(
	'digital_blog_enable_banner_section',
	array(
		'default'           => false,
		'sanitize_callback' => 'digital_blog_sanitize_switch',
	)
);

$wp_customize->add_control(
	new Digital_Blog_Toggle_Switch_Custom_Control(
		$wp_customize,
		'digital_blog_enable_banner_section',
		array(
			'label'    => esc_html__( 'Enable Banner Section', 'digital-blog' ),
			'section'  => 'digital_blog_banner_section',
			'settings' => 'digital_blog_enable_banner_section',
		)
	)
);

if ( isset( $wp_customize->selective_refresh ) ) {
	$wp_customize->selective_refresh->add_partial(
		'digital_blog_enable_banner_section',
		array(
			'selector' => '#digital_blog_banner_section .section-link',
			'settings' => 'digital_blog_enable_banner_section',
		)
	);
}

// Banner Section - Main Banner Posts Content Type.
$wp_customize->add_setting(
	'digital_blog_main_banner_posts_content_type',
	array(
		'default'           => 'post',
		'sanitize_callback' => 'digital_blog_sanitize_select',
	)
);

$wp_customize->add_control(
	'digital_blog_main_banner_posts_content_type',
	array(
		'label'           => esc_html__( 'Select Content Type', 'digital-blog' ),
		'section'         => 'digital_blog_banner_section',
		'settings'        => 'digital_blog_main_banner_posts_content_type',
		'type'            => 'select',
		'active_callback' => 'digital_blog_is_banner_section_enabled',
		'choices'         => array(
			'post'     => esc_html__( 'Post', 'digital-blog' ),
			'category' => esc_html__( 'Category', 'digital-blog' ),
		),
	)
);

for ( $i = 1; $i <= 4; $i++ ) {
	// Banner Section - Select Banner Post.
	$wp_customize->add_setting(
		'digital_blog_main_banner_posts_content_post_' . $i,
		array(
			'sanitize_callback' => 'absint',
		)
	);

	$wp_customize->add_control(
		'digital_blog_main_banner_posts_content_post_' . $i,
		array(
			'label'           => sprintf( esc_html__( 'Select Post %d', 'digital-blog' ), $i ),
			'section'         => 'digital_blog_banner_section',
			'settings'        => 'digital_blog_main_banner_posts_content_post_' . $i,
			'active_callback' => 'digital_blog_is_banner_posts_and_content_type_post_enabled',
			'type'            => 'select',
			'choices'         => digital_blog_get_post_choices(),
		)
	);
}

// Banner Section - Select Banner Category.
$wp_customize->add_setting(
	'digital_blog_main_banner_posts_content_category',
	array(
		'sanitize_callback' => 'digital_blog_sanitize_select',
	)
);

$wp_customize->add_control(
	'digital_blog_main_banner_posts_content_category',
	array(
		'label'           => esc_html__( 'Select Category', 'digital-blog' ),
		'section'         => 'digital_blog_banner_section',
		'settings'        => 'digital_blog_main_banner_posts_content_category',
		'active_callback' => 'digital_blog_is_banner_posts_and_content_type_category_enabled',
		'type'            => 'select',
		'choices'         => digital_blog_get_post_cat_choices(),
	)
);
