<?php
/**
 * Sensational Blog functions and definitions
 *
 * @link https://developer.wordpress.org/themes/basics/theme-functions/
 *
 * @package Sensational Blog
 */


if ( ! function_exists( 'sensational_blog_setup' ) ) :
/**
 * Sets up theme defaults and registers support for various WordPress features.
 *
 * Note that this function is hooked into the after_setup_theme hook, which
 * runs before the init hook. The init hook is too late for some features, such
 * as indicating support for post thumbnails.
 */
function sensational_blog_setup() {
	/*
	 * Make theme available for translation.
	 * Translations can be filed in the /languages/ directory.
	 * If you're building a theme based on BlogMelody, use a find and replace
	 * to change 'sensational-blog' to the name of your theme in all the template files.
	 */
	load_theme_textdomain( 'sensational-blog', get_template_directory() . '/languages' );

	// Add default posts and comments RSS feed links to head.
	add_theme_support( 'automatic-feed-links' );

	/*
	 * Let WordPress manage the document title.
	 * By adding theme support, we declare that this theme does not use a
	 * hard-coded <title> tag in the document head, and expect WordPress to
	 * provide it for us.
	 */
	add_theme_support( 'title-tag' );

	/*
	 * Enable support for Post Thumbnails on posts and pages.
	 *
	 * @link https://developer.wordpress.org/themes/functionality/featured-images-post-thumbnails/
	 */
	add_theme_support( 'post-thumbnails' );
	add_image_size( 'sensational-blog-gallery', 500, 500, true);
	add_image_size( 'sensational-blog-team', 400, 450, true);

	// This theme uses wp_nav_menu() in one location.
	register_nav_menus( array(
		'primary' => esc_html__( 'Primary', 'sensational-blog' ),
	) );

	// Enable support for custom logo.
	add_theme_support( 'custom-logo' , array(
		'height'		=>45,	
		'width'			=>45,	
		'flex-height'	=>true,	
		'flex-width'	=>true,
	));

	/*
	 * Switch default core markup for search form, comment form, and comments
	 * to output valid HTML5.
	 */
	add_theme_support( 'html5', array(
		'comment-form',
		'comment-list',
		'gallery',
		'caption',
	) );

	// Set up the WordPress core custom background feature.
	add_theme_support( 'custom-background', apply_filters( 'sensational_blog_custom_background_args', array(
		'default-color' => 'ffffff',
		'default-image' => '',
	) ) );

	// Add theme support for selective refresh for widgets.
	add_theme_support( 'customize-selective-refresh-widgets' );

	// Add theme support for selective refresh for widgets.
	add_theme_support( 'customize-selective-refresh-widgets' );

	/*
	* This theme styles the visual editor to resemble the theme style,
	* specifically font, colors, icons, and column width.
	*/
	$min = defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ? '' : '.min';
	add_editor_style( array( '/assets/css/editor-style' . $min . '.css', sensational_blog_fonts_url() ) );

	// Gutenberg support
	add_theme_support( 'editor-color-palette', array(
       	array(
			'name' => esc_html__( 'Tan', 'sensational-blog' ),
			'slug' => 'tan',
			'color' => '#E6DBAD',
       	),
       	array(
           	'name' => esc_html__( 'Yellow', 'sensational-blog' ),
           	'slug' => 'yellow',
           	'color' => '#FDE64B',
       	),
       	array(
           	'name' => esc_html__( 'Orange', 'sensational-blog' ),
           	'slug' => 'orange',
           	'color' => '#ED7014',
       	),
       	array(
           	'name' => esc_html__( 'Red', 'sensational-blog' ),
           	'slug' => 'red',
           	'color' => '#D0312D',
       	),
       	array(
           	'name' => esc_html__( 'Pink', 'sensational-blog' ),
           	'slug' => 'pink',
           	'color' => '#b565a7',
       	),
       	array(
           	'name' => esc_html__( 'Purple', 'sensational-blog' ),
           	'slug' => 'purple',
           	'color' => '#A32CC4',
       	),
       	array(
           	'name' => esc_html__( 'Blue', 'sensational-blog' ),
           	'slug' => 'blue',
           	'color' => '#3A43BA',
       	),
       	array(
           	'name' => esc_html__( 'Green', 'sensational-blog' ),
           	'slug' => 'green',
           	'color' => '#3BB143',
       	),
       	array(
           	'name' => esc_html__( 'Brown', 'sensational-blog' ),
           	'slug' => 'brown',
           	'color' => '#231709',
       	),
       	array(
           	'name' => esc_html__( 'Grey', 'sensational-blog' ),
           	'slug' => 'grey',
           	'color' => '#6C626D',
       	),
       	array(
           	'name' => esc_html__( 'Black', 'sensational-blog' ),
           	'slug' => 'black',
           	'color' => '#000000',
       	),
   	));

	add_theme_support( 'align-wide' );
	add_theme_support( 'editor-font-sizes', array(
	   	array(
	       	'name' => esc_html__( 'small', 'sensational-blog' ),
	       	'shortName' => esc_html__( 'S', 'sensational-blog' ),
	       	'size' => 12,
	       	'slug' => 'small'
	   	),
	   	array(
	       	'name' => esc_html__( 'regular', 'sensational-blog' ),
	       	'shortName' => esc_html__( 'M', 'sensational-blog' ),
	       	'size' => 16,
	       	'slug' => 'regular'
	   	),
	   	array(
	       	'name' => esc_html__( 'larger', 'sensational-blog' ),
	       	'shortName' => esc_html__( 'L', 'sensational-blog' ),
	       	'size' => 36,
	       	'slug' => 'larger'
	   	),
	   	array(
	       	'name' => esc_html__( 'huge', 'sensational-blog' ),
	       	'shortName' => esc_html__( 'XL', 'sensational-blog' ),
	       	'size' => 48,
	       	'slug' => 'huge'
	   	)
	));
	add_theme_support('editor-styles');
	add_theme_support( 'wp-block-styles' );
}
endif;
add_action( 'after_setup_theme', 'sensational_blog_setup' );

/**
 * Set the content width in pixels, based on the theme's design and stylesheet.
 *
 * Priority 0 to make it available to lower priority callbacks.
 *
 * @global int $content_width
 */
function sensational_blog_content_width() {
	$GLOBALS['content_width'] = apply_filters( 'sensational_blog_content_width', 640 );
}
add_action( 'after_setup_theme', 'sensational_blog_content_width', 0 );

/**
 * Register widget area.
 *
 * @link https://developer.wordpress.org/themes/functionality/sidebars/#registering-a-sidebar
 */
function sensational_blog_widgets_init() {
	register_sidebar( array(
		'name'          => esc_html__( 'Sidebar', 'sensational-blog' ),
		'id'            => 'sidebar-1',
		'description'   => esc_html__( 'Add widgets here.', 'sensational-blog' ),
		'before_widget' => '<aside id="%1$s" class="widget %2$s">',
		'after_widget'  => '</aside>',
		'before_title'  => '<h2 class="widget-title">',
		'after_title'   => '</h2>',
	) );
		register_sidebar( array(
		'name'          => sprintf( esc_html__( 'Footer %d', 'sensational-blog' ), 1 ),
		'id'            => 'footer-1',
		'before_widget' => '<aside id="%1$s" class="widget %2$s">',
		'after_widget'  => '</aside>',
		'before_title'  => '<h2 class="widget-title">',
		'after_title'   => '</h2>',
	) );
	register_sidebar( array(
		'name'          => sprintf( esc_html__( 'Footer %d', 'sensational-blog' ), 2 ),
		'id'            => 'footer-2',
		'before_widget' => '<aside id="%1$s" class="widget %2$s">',
		'after_widget'  => '</aside>',
		'before_title'  => '<h2 class="widget-title">',
		'after_title'   => '</h2>',
	) );
	register_sidebar( array(
		'name'          => sprintf( esc_html__( 'Footer %d', 'sensational-blog' ), 3 ),
		'id'            => 'footer-3',
		'before_widget' => '<aside id="%1$s" class="widget %2$s">',
		'after_widget'  => '</aside>',
		'before_title'  => '<h2 class="widget-title">',
		'after_title'   => '</h2>',
	) );
	register_sidebar( array(
		'name'          => sprintf( esc_html__( 'Footer %d', 'sensational-blog' ), 4 ),
		'id'            => 'footer-4',
		'before_widget' => '<aside id="%1$s" class="widget %2$s">',
		'after_widget'  => '</aside>',
		'before_title'  => '<h2 class="widget-title">',
		'after_title'   => '</h2>',
	) );
}
add_action( 'widgets_init', 'sensational_blog_widgets_init' );

/**
 * Register custom fonts.
 */
function sensational_blog_fonts_url() {
	$fonts_url = '';
	$fonts     = array();
	$subsets   = 'latin,latin-ext';

	/* translators: If there are characters in your language that are not supported by Rajdhani, translate this to 'off'. Do not translate into your own language. */
	if ( 'off' !== _x( 'on', 'Rajdhani font: on or off', 'sensational-blog' ) ) {
		$fonts[] = 'Rajdhani';
	}
	
	/* translators: If there are characters in your language that are not supported by Bad Script, translate this to 'off'. Do not translate into your own language. */
	if ( 'off' !== _x( 'on', 'Bad Script font: on or off', 'sensational-blog' ) ) {
		$fonts[] = 'Bad Script';
	}
	

	/* translators: If there are characters in your language that are not supported by Righteous, translate this to 'off'. Do not translate into your own language. */
	if ( 'off' !== _x( 'on', 'Righteous font: on or off', 'sensational-blog' ) ) {
		$fonts[] = 'Righteous';
	}

	/* translators: If there are characters in your language that are not supported by Dosis, translate this to 'off'. Do not translate into your own language. */
	if ( 'off' !== _x( 'on', 'Dosis font: on or off', 'sensational-blog' ) ) {
		$fonts[] = 'Dosis';
	}

	/* translators: If there are characters in your language that are not supported by Orbitron, translate this to 'off'. Do not translate into your own language. */
	if ( 'off' !== _x( 'on', 'Orbitron font: on or off', 'sensational-blog' ) ) {
		$fonts[] = 'Orbitron';
	}

	/* translators: If there are characters in your language that are not supported by Gloria Hallelujah, translate this to 'off'. Do not translate into your own language. */
	if ( 'off' !== _x( 'on', 'Gloria Hallelujah font: on or off', 'sensational-blog' ) ) {
		$fonts[] = 'Gloria Hallelujah';
	}

	/* translators: If there are characters in your language that are not supported by Dancing Script, translate this to 'off'. Do not translate into your own language. */
	if ( 'off' !== _x( 'on', 'Dancing Script font: on or off', 'sensational-blog' ) ) {
		$fonts[] = 'Dancing Script';
	}
	/* translators: If there are characters in your language that are not supported by Cinzel Decorative, translate this to 'off'. Do not translate into your own language. */
	if ( 'off' !== _x( 'on', 'Cinzel Decorative font: on or off', 'sensational-blog' ) ) {
		$fonts[] = 'Cinzel Decorative';
	}
	/* translators: If there are characters in your language that are not supported by Faster one, translate this to 'off'. Do not translate into your own language. */
	if ( 'off' !== _x( 'on', 'Faster one font: on or off', 'sensational-blog' ) ) {
		$fonts[] = 'Faster One';
	}
	/* translators: If there are characters in your language that are not supported by Courgette, translate this to 'off'. Do not translate into your own language. */
	if ( 'off' !== _x( 'on', 'Courgette font: on or off', 'sensational-blog' ) ) {
		$fonts[] = 'Courgette';
	}

	/* translators: If there are characters in your language that are not supported by Montserrat, translate this to 'off'. Do not translate into your own language. */
	if ( 'off' !== _x( 'on', 'Tangerine font: on or off', 'sensational-blog' ) ) {
		$fonts[] = 'Tangerine';

	}
	/* translators: If there are characters in your language that are not supported by Henny Penny, translate this to 'off'. Do not translate into your own language. */
	if ( 'off' !== _x( 'on', 'Henny Penny font: on or off', 'sensational-blog' ) ) {
		$fonts[] = 'Henny Penny';

	}
	/* translators: If there are characters in your language that are not supported by Lumanosimo, translate this to 'off'. Do not translate into your own language. */
	if ( 'off' !== _x( 'on', 'Lumanosimo font: on or off', 'sensational-blog' ) ) {
		$fonts[] = 'Lumanosimo';
	}
	
	/* translators: If there are characters in your language that are not supported by Montserrat, translate this to 'off'. Do not translate into your own language. */
	if ( 'off' !== _x( 'on', 'Kaushan Script font: on or off', 'sensational-blog' ) ) {
		$fonts[] = 'Kaushan Script';
	}
	/* translators: If there are characters in your language that are not supported by Montserrat, translate this to 'off'. Do not translate into your own language. */
	if ( 'off' !== _x( 'on', 'Marck Script font: on or off', 'sensational-blog' ) ) {
		$fonts[] = 'Marck Script';
	}

	/* translators: If there are characters in your language that are not supported by Fredericka the Great, translate this to 'off'. Do not translate into your own language. */
	if ( 'off' !== _x( 'on', 'Fredericka the Great font: on or off', 'sensational-blog' ) ) {
		$fonts[] = 'Fredericka the Great';
	
	}
	/* translators: If there are characters in your language that are not supported by Roboto Slab, translate this to 'off'. Do not translate into your own language. */
	if ( 'off' !== _x( 'on', 'Raleway: on or off', 'sensational-blog' ) ) {
		$fonts[] = 'Raleway:300,400,500,600,700';
	}
	
	/* translators: If there are characters in your language that are not supported by Roboto Slab, translate this to 'off'. Do not translate into your own language. */
	if ( 'off' !== _x( 'on', 'Jost: on or off', 'sensational-blog' ) ) {
		$fonts[] = 'Jost:300,400,500,600,700';
	}
	
	/* translators: If there are characters in your language that are not supported by Roboto Slab, translate this to 'off'. Do not translate into your own language. */
	if ( 'off' !== _x( 'on', 'Gilda Display: on or off', 'sensational-blog' ) ) {
		$fonts[] = 'Gilda Display:300,400,500,600,700';
	}

	// Header Options

	/* translators: If there are characters in your language that are not supported by Poppins, translate this to 'off'. Do not translate into your own language. */
	if ( 'off' !== _x( 'on', 'Poppins font: on or off', 'sensational-blog' ) ) {
		$fonts[] = 'Poppins';
	}

	/* translators: If there are characters in your language that are not supported by Montserrat, translate this to 'off'. Do not translate into your own language. */
	if ( 'off' !== _x( 'on', 'Montserrat font: on or off', 'sensational-blog' ) ) {
		$fonts[] = 'Montserrat:300,400,500,600,700';
	}

	/* translators: If there are characters in your language that are not supported by Quicksand, translate this to 'off'. Do not translate into your own language. */
	if ( 'off' !== _x( 'on', 'Quicksand font: on or off', 'sensational-blog' ) ) {
		$fonts[] = 'Quicksand:300,400,500,600,700';
	}

	/* translators: If there are characters in your language that are not supported by Open Sans, translate this to 'off'. Do not translate into your own language. */
	if ( 'off' !== _x( 'on', 'Open Sans font: on or off', 'sensational-blog' ) ) {
		$fonts[] = 'Open Sans';
	}

	/* translators: If there are characters in your language that are not supported by Lato, translate this to 'off'. Do not translate into your own language. */
	if ( 'off' !== _x( 'on', 'Lato font: on or off', 'sensational-blog' ) ) {
		$fonts[] = 'Lato:300,400,500,600,700';
	}

	/* translators: If there are characters in your language that are not supported by Ubuntu, translate this to 'off'. Do not translate into your own language. */
	if ( 'off' !== _x( 'on', 'Ubuntu font: on or off', 'sensational-blog' ) ) {
		$fonts[] = 'Ubuntu';
	}

	// Body Options
	
	/* translators: If there are characters in your language that are not supported by |Playfair Display, translate this to 'off'. Do not translate into your own language. */
	if ( 'off' !== _x( 'on', 'Playfair Display font: on or off', 'sensational-blog' ) ) {
		$fonts[] = 'Playfair Display';
	}

	/* translators: If there are characters in your language that are not supported by Lora, translate this to 'off'. Do not translate into your own language. */
	if ( 'off' !== _x( 'on', 'Lora font: on or off', 'sensational-blog' ) ) {
		$fonts[] = 'Lora';
	}

	/* translators: If there are characters in your language that are not supported by Titillium Web, translate this to 'off'. Do not translate into your own language. */
	if ( 'off' !== _x( 'on', 'Titillium Web font: on or off', 'sensational-blog' ) ) {
		$fonts[] = 'Titillium Web';
	}

	/* translators: If there are characters in your language that are not supported by Muli, translate this to 'off'. Do not translate into your own language. */
	if ( 'off' !== _x( 'on', 'Muli font: on or off', 'sensational-blog' ) ) {
		$fonts[] = 'Muli';
	}

	/* translators: If there are characters in your language that are not supported by Nunito Sans, translate this to 'off'. Do not translate into your own language. */
	if ( 'off' !== _x( 'on', 'Nunito Sans font: on or off', 'sensational-blog' ) ) {
		$fonts[] = 'Nunito Sans';
	}

	/* translators: If there are characters in your language that are not supported by Maven Pro, translate this to 'off'. Do not translate into your own language. */
	if ( 'off' !== _x( 'on', 'Maven Pro font: on or off', 'sensational-blog' ) ) {
		$fonts[] = 'Maven Pro';
	}

	/* translators: If there are characters in your language that are not supported by Cairo, translate this to 'off'. Do not translate into your own language. */
	if ( 'off' !== _x( 'on', 'Cairo font: on or off', 'sensational-blog' ) ) {
		$fonts[] = 'Cairo';
	}

	/* translators: If there are characters in your language that are not supported by Cormorant, translate this to 'off'. Do not translate into your own language. */
	if ( 'off' !== _x( 'on', 'Cormorant font: on or off', 'sensational-blog' ) ) {
		$fonts[] = 'Cormorant';
	}

	/* translators: If there are characters in your language that are not supported by Philosopher, translate this to 'off'. Do not translate into your own language. */
	if ( 'off' !== _x( 'on', 'Philosopher font: on or off', 'sensational-blog' ) ) {
		$fonts[] = 'Philosopher';
	}

	/* translators: If there are characters in your language that are not supported by Roboto, translate this to 'off'. Do not translate into your own language. */
	if ( 'off' !== _x( 'on', 'Roboto font: on or off', 'sensational-blog' ) ) {
		$fonts[] = 'Roboto';
	}
	/* translators: If there are characters in your language that are not supported by Roboto, translate this to 'off'. Do not translate into your own language. */
	if ( 'off' !== _x( 'on', 'Shadows Into Light font: on or off', 'sensational-blog' ) ) {
		$fonts[] = 'Shadows Into Light';
	}

	/* translators: If there are characters in your language that are not supported by Oxygen, translate this to 'off'. Do not translate into your own language. */
	if ( 'off' !== _x( 'on', 'Oxygen font: on or off', 'sensational-blog' ) ) {
		$fonts[] = 'Oxygen';
	}

	if ( $fonts ) {
		$fonts_url = add_query_arg( array(
			'family' => urlencode( implode( '|', $fonts ) ),
			'subset' => urlencode( $subsets ),
		), 'https://fonts.googleapis.com/css' );
	}

	return esc_url_raw( $fonts_url );
}



/**
 * Enqueue scripts and styles.
 */
function sensational_blog_scripts() {
	$min = defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ? '' : '.min';
	$fonts_url = sensational_blog_fonts_url();
	$primary_color = sensational_blog_get_option( 'primary_color' );
	
		wp_enqueue_style( 'sensational-blog-google-fonts', $fonts_url, array(), null );
	
	wp_enqueue_style( 'fontawesome-all', get_template_directory_uri() . '/assets/css/all' . $min . '.css', '', '4.7.0' );

	wp_enqueue_style( 'font-awesome', get_template_directory_uri() . '/assets/css/font-awesome' . $min . '.css', '', '4.7.0' );

	wp_enqueue_style( 'slick-theme-css', get_template_directory_uri() .'/assets/css/slick-theme' . $min . '.css', '', 'v2.2.0');

	wp_enqueue_style( 'slick-css', get_template_directory_uri() .'/assets/css/slick' . $min . '.css', '', 'v1.8.0');

	wp_enqueue_style( 'sensational-blog-blocks', get_template_directory_uri() . '/assets/css/blocks' . $min . '.css' );

	wp_enqueue_style( 'sensational-blog-style', get_stylesheet_uri() );

	wp_enqueue_script( 'jquery-slick', get_template_directory_uri() . '/assets/js/slick' . $min . '.js', array('jquery'), '2017417', true );

	wp_enqueue_script( 'jquery-match-height', get_template_directory_uri() . '/assets/js/jquery.matchHeight' . $min . '.js', array('jquery'), '2017417', true );

	wp_enqueue_script( 'imagesloaded' );
	
	wp_enqueue_script( 'jquery-packery', get_template_directory_uri() . '/assets/js/packery.pkgd' . $min . '.js', array('jquery'), '2017417', true );

	wp_enqueue_script( 'sensational-blog-navigation', get_template_directory_uri() . '/assets/js/navigation' . $min . '.js', array(), '20151215', true );

	wp_enqueue_script( 'sensational-blog-skip-link-focus-fix', get_template_directory_uri() . '/assets/js/skip-link-focus-fix' . $min . '.js', array(), '20151215', true );
	wp_enqueue_script( 'sensational-blog-custom-js', get_template_directory_uri() . '/assets/js/custom' . $min . '.js', array('jquery'), '20151215', true );  
	if ( is_singular() && comments_open() && get_option( 'thread_comments' ) ) {
		wp_enqueue_script( 'comment-reply' );
	}
}
add_action( 'wp_enqueue_scripts', 'sensational_blog_scripts' );

/**
 * Enqueue editor styles for Gutenberg
 *
 * @since Safar Lite 1.0.0
 */
function sensational_blog_block_editor_styles() {
	$min = defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ? '' : '.min';
	// Block styles.
	wp_enqueue_style( 'sensational-blog-block-editor-style', get_theme_file_uri( '/assets/css/editor-blocks' . $min . '.css' ) );
	// Add custom fonts.
	wp_enqueue_style( 'sensational-blog-fonts', sensational_blog_fonts_url(), array(), null );
}
add_action( 'enqueue_block_editor_assets', 'sensational_blog_block_editor_styles' );

/**
 * Webfont Loader.
 */
require get_template_directory() . '/inc/wptt-webfont-loader.php';

/**
 * Load init.
 */
require_once get_template_directory() . '/inc/init.php';

require get_template_directory() . '/inc/customizer/widgets/widgets.php';

 if ( class_exists( 'WooCommerce' ) ) {
	// Load home page sections option.
	include get_template_directory() . '/inc/woocommerce/template-functions.php';

	// Load home page sections option.
	include get_template_directory() . '/inc/woocommerce/woocommerce.php';
}
