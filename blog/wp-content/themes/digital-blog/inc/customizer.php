<?php
/**
 * Digital Blog Theme Customizer
 *
 * @package Digital Blog
 */

// Sanitize callback.
require get_template_directory() . '/inc/customizer/sanitize-callback.php';

// Active Callback.
require get_template_directory() . '/inc/customizer/active-callback.php';

// Custom Controls.
require get_template_directory() . '/inc/customizer/custom-controls/custom-controls.php';

/**
 * Add postMessage support for site title and description for the Theme Customizer.
 *
 * @param WP_Customize_Manager $wp_customize Theme Customizer object.
 */
function digital_blog_customize_register( $wp_customize ) {
	$wp_customize->get_setting( 'blogname' )->transport        = 'postMessage';
	$wp_customize->get_setting( 'blogdescription' )->transport = 'postMessage';

	if ( isset( $wp_customize->selective_refresh ) ) {
		$wp_customize->selective_refresh->add_partial(
			'blogname',
			array(
				'selector'        => '.site-title a',
				'render_callback' => 'digital_blog_customize_partial_blogname',
			)
		);
		$wp_customize->selective_refresh->add_partial(
			'blogdescription',
			array(
				'selector'        => '.site-description',
				'render_callback' => 'digital_blog_customize_partial_blogdescription',
			)
		);
	}

	// Latest posts title setting.
	$wp_customize->add_setting(
		'digital_blog_latest_posts_title',
		array(
			'sanitize_callback' => 'sanitize_text_field',
			'default'           => '',
		)
	);

	$wp_customize->add_control(
		'digital_blog_latest_posts_title',
		array(
			'section'         => 'static_front_page',
			'label'           => esc_html__( 'Latest Posts Title:', 'digital-blog' ),
			'active_callback' => function( $control ) {
				return ( is_front_page() && is_home() );
			},
		)
	);

	// Latest posts subtitle setting.
	$wp_customize->add_setting(
		'digital_blog_latest_posts_subtitle',
		array(
			'sanitize_callback' => 'sanitize_text_field',
			'default'           => '',
		)
	);

	$wp_customize->add_control(
		'digital_blog_latest_posts_subtitle',
		array(
			'section'         => 'static_front_page',
			'label'           => esc_html__( 'Latest Posts Subtitle:', 'digital-blog' ),
			'active_callback' => function( $control ) {
				return ( is_front_page() && is_home() );
			},
		)
	);

	// Upsell Section.
	$wp_customize->add_section(
		new Digital_Blog_Upsell_Section(
			$wp_customize,
			'upsell_section',
			array(
				'title'            => __( 'Digital Blog Pro', 'digital-blog' ),
				'button_text'      => __( 'Buy Pro', 'digital-blog' ),
				'url'              => 'https://ascendoor.com/themes/digital-blog-pro/',
				'background_color' => '#dd3e54',
				'text_color'       => '#fff',
				'priority'         => 0,
			)
		)
	);

	// Homepage Settings - Enable Homepage Content.
	$wp_customize->add_setting(
		'digital_blog_enable_frontpage_content',
		array(
			'default'           => false,
			'sanitize_callback' => 'digital_blog_sanitize_checkbox',
		)
	);

	$wp_customize->add_control(
		'digital_blog_enable_frontpage_content',
		array(
			'label'           => esc_html__( 'Enable Homepage Content', 'digital-blog' ),
			'description'     => esc_html__( 'Check to enable content on static homepage.', 'digital-blog' ),
			'section'         => 'static_front_page',
			'type'            => 'checkbox',
			'active_callback' => 'digital_blog_is_static_homepage_enabled',
		)
	);

	// Front Page Options.
	require get_template_directory() . '/inc/customizer/front-page-options.php';

	// Theme Options.
	require get_template_directory() . '/inc/customizer/theme-options.php';

}
add_action( 'customize_register', 'digital_blog_customize_register' );

/**
 * Render the site title for the selective refresh partial.
 *
 * @return void
 */
function digital_blog_customize_partial_blogname() {
	bloginfo( 'name' );
}

/**
 * Render the site tagline for the selective refresh partial.
 *
 * @return void
 */
function digital_blog_customize_partial_blogdescription() {
	bloginfo( 'description' );
}

/**
 * Binds JS handlers to make Theme Customizer preview reload changes asynchronously.
 */
function digital_blog_customize_preview_js() {
	// Append .min if SCRIPT_DEBUG is false.
	$min = ( defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ) ? '' : '.min';
	wp_enqueue_script( 'digital-blog-customizer', get_template_directory_uri() . '/assets/js/customizer' . $min . '.js', array( 'customize-preview' ), DIGITAL_BLOG_VERSION, true );
}
add_action( 'customize_preview_init', 'digital_blog_customize_preview_js' );

/**
 * Enqueue script for custom customize control.
 */
function digital_blog_custom_control_scripts() {
	// Append .min if SCRIPT_DEBUG is false.
	$min = ( defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ) ? '' : '.min';
	wp_enqueue_style( 'digital-blog-custom-controls-css', get_template_directory_uri() . '/assets/css/custom-controls' . $min . '.css', array(), '1.0', 'all' );
	wp_enqueue_script( 'digital-blog-custom-controls-js', get_template_directory_uri() . '/assets/js/custom-controls' . $min . '.js', array( 'jquery', 'jquery-ui-core', 'jquery-ui-sortable' ), '1.0', true );
}
add_action( 'customize_controls_enqueue_scripts', 'digital_blog_custom_control_scripts' );
