<?php
/**
 * Sensational Blog Theme Customizer
 *
 * @package Sensational Blog
 */

/**
 * Add postMessage support for site title and description for the Theme Customizer.
 *
 * @param WP_Customize_Manager $wp_customize Theme Customizer object.
 */

function sensational_blog_customize_register( $wp_customize ) {
	$wp_customize->get_setting( 'blogname' )->transport         = 'postMessage';
	$wp_customize->get_setting( 'blogdescription' )->transport  = 'postMessage';
	$wp_customize->get_setting( 'header_textcolor' )->transport = 'postMessage';
	// Register custom section types.
	$wp_customize->register_section_type( 'Sensational_Blog_Customize_Section_Upsell' );
	$wp_customize->add_section(
		new Sensational_Blog_Customize_Section_Upsell(
			$wp_customize,
			'theme_upsell',
			array(
				'title'    => esc_html__( 'Sensational Blog', 'sensational-blog' ),
				'pro_text' => esc_html__( 'Buy Pro', 'sensational-blog' ),
				'pro_url'  => 'http://www.sensationaltheme.com/downloads/sensational-blog-pro/',
				'priority'  => 1,
			)
		)
	);

	$default = sensational_blog_get_default_theme_options();

	//For Menu Option
	$wp_customize->add_section('menu_logo_center_optons', array(    
	'title'       => __('Menu Logo Center', 'sensational-blog'),
	'panel'       => 'nav_menus',
	'active_callback' => 'sensational_blog_header_nine',   
	));

	// Add Single Header Image enable setting and control.
	$wp_customize->add_setting( 'theme_options[enable_center_logo]', array(
		'default'           => $default['enable_center_logo'],
		'sanitize_callback' => 'sensational_blog_sanitize_checkbox',
	) );

	$wp_customize->add_control( 'theme_options[enable_center_logo]', array(
		'label'             => esc_html__( 'Enable Logo Placed in Center', 'sensational-blog' ),
		'section'           => 'menu_logo_center_optons',
		'type'              => 'checkbox',

	) );
	// Add Panel.
	$wp_customize->add_panel( 'theme_option_panel',
		array(
		'title'      => __( 'Theme Options', 'sensational-blog' ),
		'priority'   => 100,
		'capability' => 'edit_theme_options',
		)
	);	

	// Load customize sanitize.
	include get_template_directory() . '/inc/customizer/sanitize.php';

	// Load customize options.
	include get_template_directory() . '/inc/customizer/options.php';

	// Load customize control.
	include get_template_directory() . '/inc/customizer/control.php';

	// Load customize sanitize.
	include get_template_directory() . '/inc/customizer/active-callback.php';

	// Load header sections option.
	include get_template_directory() . '/inc/customizer/theme-option/footer.php';

	// Load header sections option.
	include get_template_directory() . '/inc/customizer/theme-option/general.php';

	// Load header sections option.
	include get_template_directory() . '/inc/customizer/theme-option/header-image.php';

	// Load header sections option.
	include get_template_directory() . '/inc/customizer/theme-option/archives.php';

	// Load Single Post sections option.
	include get_template_directory() . '/inc/customizer/theme-option/single-post.php';

	// Load Single Page sections option.
	include get_template_directory() . '/inc/customizer/theme-option/single-page.php';

	// Load home page sections option.
	include get_template_directory() . '/inc/customizer/home-section.php';


	
}
add_action( 'customize_register', 'sensational_blog_customize_register' );

/**
 * Binds JS handlers to make Theme Customizer preview reload changes asynchronously.
 */
function sensational_blog_customize_preview_js() {
	wp_enqueue_script( 'sensational_blog_customizer', get_template_directory_uri() . '/inc/customizer/js/customizer.js', array( 'customize-preview' ), '20151215', true );
}
add_action( 'customize_preview_init', 'sensational_blog_customize_preview_js' );
/**
 *
 */
function sensational_blog_customize_backend_scripts() {

	wp_enqueue_style( 'sensational-blog-admin-customizer-style', get_template_directory_uri() . '/inc/customizer/css/customizer-style.css' );
	wp_enqueue_script( 'sensational-blog-admin-customizer', get_template_directory_uri() . '/inc/customizer/js/customizer-scipt.js', array( 'jquery', 'customize-controls' ), '20151215', true );
}
add_action( 'customize_controls_enqueue_scripts', 'sensational_blog_customize_backend_scripts', 10 );
