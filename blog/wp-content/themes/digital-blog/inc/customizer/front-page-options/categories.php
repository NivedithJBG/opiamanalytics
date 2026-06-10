<?php
/**
 * Categories Section
 *
 * @package Digital Blog
 */

$wp_customize->add_section(
	'digital_blog_categories_section',
	array(
		'panel' => 'digital_blog_front_page_options',
		'title' => esc_html__( 'Categories Section', 'digital-blog' ),
	)
);

// Categories Section - Enable Section.
$wp_customize->add_setting(
	'digital_blog_enable_categories_section',
	array(
		'default'           => false,
		'sanitize_callback' => 'digital_blog_sanitize_switch',
	)
);

$wp_customize->add_control(
	new Digital_Blog_Toggle_Switch_Custom_Control(
		$wp_customize,
		'digital_blog_enable_categories_section',
		array(
			'label'    => esc_html__( 'Enable Categories Section', 'digital-blog' ),
			'section'  => 'digital_blog_categories_section',
			'settings' => 'digital_blog_enable_categories_section',
		)
	)
);

if ( isset( $wp_customize->selective_refresh ) ) {
	$wp_customize->selective_refresh->add_partial(
		'digital_blog_enable_categories_section',
		array(
			'selector' => '#digital_blog_categories_section .section-link',
			'settings' => 'digital_blog_enable_categories_section',
		)
	);
}

// Categories Section - Section Title.
$wp_customize->add_setting(
	'digital_blog_categories_title',
	array(
		'default'           => __( 'Categories', 'digital-blog' ),
		'sanitize_callback' => 'sanitize_text_field',
	)
);

$wp_customize->add_control(
	'digital_blog_categories_title',
	array(
		'label'           => esc_html__( 'Section Title', 'digital-blog' ),
		'section'         => 'digital_blog_categories_section',
		'settings'        => 'digital_blog_categories_title',
		'type'            => 'text',
		'active_callback' => 'digital_blog_is_categories_section_enabled',
	)
);

// Categories Section - Section Subtitle.
$wp_customize->add_setting(
	'digital_blog_categories_subtitle',
	array(
		'default'           => '',
		'sanitize_callback' => 'sanitize_text_field',
	)
);

$wp_customize->add_control(
	'digital_blog_categories_subtitle',
	array(
		'label'           => esc_html__( 'Section Subtitle', 'digital-blog' ),
		'section'         => 'digital_blog_categories_section',
		'settings'        => 'digital_blog_categories_subtitle',
		'type'            => 'text',
		'active_callback' => 'digital_blog_is_categories_section_enabled',
	)
);

for ( $i = 1; $i <= 4; $i++ ) {

	// Categories Section - Select Category.
	$wp_customize->add_setting(
		'digital_blog_categories_content_category_' . $i,
		array(
			'sanitize_callback' => 'digital_blog_sanitize_select',
		)
	);

	$wp_customize->add_control(
		'digital_blog_categories_content_category_' . $i,
		array(
			'label'           => sprintf( esc_html__( 'Select Category %d', 'digital-blog' ), $i ),
			'section'         => 'digital_blog_categories_section',
			'settings'        => 'digital_blog_categories_content_category_' . $i,
			'active_callback' => 'digital_blog_is_categories_section_enabled',
			'type'            => 'select',
			'choices'         => digital_blog_get_post_cat_choices(),
		)
	);

	// Categories Section - Category Image.
	$wp_customize->add_setting(
		'digital_blog_category_category_image_' . $i,
		array(
			'sanitize_callback' => 'digital_blog_sanitize_image',
		)
	);

	$wp_customize->add_control(
		new WP_Customize_Image_Control(
			$wp_customize,
			'digital_blog_category_category_image_' . $i,
			array(
				'label'           => sprintf( esc_html__( 'Category Image %d', 'digital-blog' ), $i ),
				'section'         => 'digital_blog_categories_section',
				'settings'        => 'digital_blog_category_category_image_' . $i,
				'active_callback' => 'digital_blog_is_categories_section_enabled',
			)
		)
	);

	// Categories Section - Horizontal Line.
	$wp_customize->add_setting(
		'digital_blog_categories_horizontal_line_' . $i,
		array(
			'sanitize_callback' => 'sanitize_text_field',
		)
	);

	$wp_customize->add_control(
		new Digital_Blog_Customize_Horizontal_Line(
			$wp_customize,
			'digital_blog_categories_horizontal_line_' . $i,
			array(
				'section'         => 'digital_blog_categories_section',
				'settings'        => 'digital_blog_categories_horizontal_line_' . $i,
				'active_callback' => 'digital_blog_is_categories_section_enabled',
				'type'            => 'hr',
			)
		)
	);

}
