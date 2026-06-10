<?php
/**
 * Custom functions that act independently of the theme templates
 *
 * Eventually, some of the functionality here could be replaced by core features.
 *
 * @package Sensational Blog
 */

/**
 * Adds custom classes to the array of body classes.
 *
 * @param array $classes Classes for the body element.
 * @return array
 */
function sensational_blog_body_classes( $classes ) {
	// Adds a class of group-blog to blogs with more than 1 published author.
	if ( is_multi_author() ) {
		$classes[] = 'group-blog';
	}

	// Adds a class of hfeed to non-singular pages.
	if ( ! is_singular() ) {
		$classes[] = 'hfeed';
	}
	if (false == sensational_blog_get_option('show_topbar')) {
		$classes[] = 'disable-topbar';
	}
	
	if (true == sensational_blog_get_option('enable_center_logo')) {
		$classes[] = 'align-logo-center';
	}
	$loader_options = sensational_blog_get_option('preloader_loader_options');
	$classes[] = esc_attr( $loader_options );
	
	$homepage_layout_options = sensational_blog_get_option('homepage_layout_options');
	$classes[] = esc_attr( $homepage_layout_options );

	if (true == sensational_blog_get_option('corporate_curve_shape_enable')) {
		$classes[] = 'enable-corporate-curve';
	}

	$header_layout_options = sensational_blog_get_option('header_layout_options');
	$classes[] = esc_attr( $header_layout_options );

	$homepage_design_layout_options = sensational_blog_get_option('homepage_design_layout_options');
	$classes[] = esc_attr( $homepage_design_layout_options );

	$header_text_hover = sensational_blog_get_option('header_text_hover');
	$classes[] = esc_attr( $header_text_hover );

	$menu_text_hover = sensational_blog_get_option('menu_text_hover');
	$classes[] = esc_attr( $menu_text_hover );

	if (true == sensational_blog_get_option('menu_sticky')) {
		$classes[] = 'menu-sticky';
	}

	if ((is_front_page() || is_home()) && (true == sensational_blog_get_option('decoration_side_enable'))) {
		$classes[] = 'disable-decoration-image';
	}

	if (is_front_page() && (false == sensational_blog_get_option('disable_blog_banner_section')  || true == sensational_blog_get_option('disable_featured-slider_section'))){
		$classes[] = 'blog-banner-disable';
	}

	if ( is_front_page() || is_home() ) {
		$slider_content_position_option = sensational_blog_get_option('slider_content_position_option');
		$classes[] = esc_attr( $slider_content_position_option );
	}

	if ( is_front_page() || is_home() ) {
		$homepage_sidebar_position = sensational_blog_get_option('homepage_sidebar_position');
		$classes[] = esc_attr( $homepage_sidebar_position );
	}

	if ( is_front_page() || is_home() ) {
		$slider_layout = sensational_blog_get_option('slider_layout_option');
		$classes[] = esc_attr( $slider_layout );
	} 

	if (is_single() && false == sensational_blog_get_option( 'single_post_header_image_enable' )){
		$classes[] = 'disable-single-post-header';
	}

	if (is_page() && false == sensational_blog_get_option( 'single_page_header_image_enable' )){
		$classes[] = 'disable-single-page-header';
	}

	if ( is_home() ) {
		$sidebar_layout_blog = sensational_blog_get_option('layout_options_blog'); 
		$classes[] = esc_attr( $sidebar_layout_blog );
		if (false == sensational_blog_get_option( 'blog_post_header_title_enable' )) {
			$classes[] = 'disable-blog-post-header-title';
		}
	}

	if( is_archive() ) {
		$sidebar_layout_archive = sensational_blog_get_option('layout_options_archive'); 
		$classes[] = esc_attr( $sidebar_layout_archive );
		if (false == sensational_blog_get_option( 'archive_post_header_title_enable' )) {
			$classes[] = 'disable-archive-post-header-title';
		}
	}

	if( is_search() ) {
		$sidebar_layout_archive = sensational_blog_get_option('layout_options_archive'); 
		$classes[] = esc_attr( $sidebar_layout_archive );
	}

	if( is_page() ) {
		$sidebar_layout_page = sensational_blog_get_option('layout_options_page'); 
		$classes[] = esc_attr( $sidebar_layout_page );
		if (false == sensational_blog_get_option( 'single_page_header_title_enable' )) {
			$classes[] = 'disable-single-page-header-title';
		}
	}

	if( is_single() ) {
		$sidebar_layout_single = sensational_blog_get_option('layout_options_single'); 
		$classes[] = esc_attr( $sidebar_layout_single );
		if (false == sensational_blog_get_option( 'single_post_header_title_enable' )) {
			$classes[] = 'disable-single-post-header-title';
		}
	}

	if( is_archive() || is_search() || is_home() ) {
		$blog_layout_content_type = sensational_blog_get_option('blog_layout_content_type'); 
		$classes[] = esc_attr( $blog_layout_content_type );
	}

	if( is_home() ) {
		$blog_header_image_condition = sensational_blog_get_option( 'blog_post_header_image_enable' );
		if ($blog_header_image_condition==false) {
			$classes[] = 'disable-blog-header-image';
		}
	}

	if( is_archive() ) {
		$archive_header_image_condition = sensational_blog_get_option( 'archive_post_header_image_enable' );
		if ($archive_header_image_condition==false) {
			$classes[] = 'disable-archive-header-image';
		}
	}

	if ( is_front_page()) :
		// Add a class for typography
		$typography = (  sensational_blog_get_option('theme_typography') == 'default' ) ? '' :  sensational_blog_get_option('theme_typography');
		$classes[] = esc_attr( $typography );

		$body_typography = (  sensational_blog_get_option('body_theme_typography') == 'default' ) ? '' :  sensational_blog_get_option('body_theme_typography');
		$classes[] = esc_attr( $body_typography );
	endif;

	if ( is_home() || is_archive() || is_search()) :
		// Add a class for typography
		$archive_typography = (  sensational_blog_get_option('archive_typography') == 'default' ) ? '' :  sensational_blog_get_option('archive_typography');
		$classes[] = esc_attr( $archive_typography );

		$body_archive_typography = (  sensational_blog_get_option('body_archive_typography') == 'default' ) ? '' :  sensational_blog_get_option('body_archive_typography');
		$classes[] = esc_attr( $body_archive_typography );
	endif;

	if ( is_single()) :
		// Add a class for typography
		$post_typography = (  sensational_blog_get_option('post_typography') == 'default' ) ? '' :  sensational_blog_get_option('post_typography');
		$classes[] = esc_attr( $post_typography );

		$body_post_typography = (  sensational_blog_get_option('body_post_typography') == 'default' ) ? '' :  sensational_blog_get_option('body_post_typography');
		$classes[] = esc_attr( $body_post_typography );
	endif;

	if ( is_page()) :
		// Add a class for typography
		$page_typography = (  sensational_blog_get_option('page_typography') == 'default' ) ? '' :  sensational_blog_get_option('page_typography');
		$classes[] = esc_attr( $page_typography );

		$body_page_typography = (  sensational_blog_get_option('body_page_typography') == 'default' ) ? '' :  sensational_blog_get_option('body_page_typography');
		$classes[] = esc_attr( $body_page_typography );
	endif;

		// Add a class for typography
		$site_typography = (  sensational_blog_get_option('site_title_typography') == 'default' ) ? '' :  sensational_blog_get_option('site_title_typography');
		$classes[] = esc_attr( $site_typography );

		$tagline_typography = (  sensational_blog_get_option('site_tagline_typography') == 'default' ) ? '' :  sensational_blog_get_option('site_tagline_typography');
		$classes[] = esc_attr( $tagline_typography );

	return $classes;
}
add_filter( 'body_class', 'sensational_blog_body_classes' );

/**
 * Add a pingback url auto-discovery header for singularly identifiable articles.
 */
function sensational_blog_pingback_header() {
	if ( is_singular() && pings_open() ) {
		echo '<link rel="pingback" href="', esc_url( get_bloginfo( 'pingback_url' ) ), '">';
	}
}
add_action( 'wp_head', 'sensational_blog_pingback_header' );

/**
 * Function to get Sections 
 */
function sensational_blog_get_sections() {
	$homepage_design_layout     = sensational_blog_get_option( 'homepage_design_layout_options' );

    if ($homepage_design_layout=='home-corporate') {
	    $sections = array( 'featured-slider', 'highlights', 'trending', 'recent', 'mustread');
	}
    if ($homepage_design_layout=='home-grid') {
	    $sections = array( );
	}

    if ($homepage_design_layout=='home-normalblog') {

			$sorted_sections= sensational_blog_get_option( 'sensational_blog_homepage_sortable_sections' );
			$sections = array();
			$sections = explode( ',' , sensational_blog_get_option( 'sensational_blog_homepage_sortable_sections' ));
			
		}
	

	    $enabled_section = array();
	    foreach ( $sections as $section ){
	    	
	        if (true == sensational_blog_get_option('disable_'.$section.'_section')){
	            $enabled_section[] = array(
	                'id' => $section,
	                'menu_text' => esc_html( sensational_blog_get_option('' . $section . '_menu_title','') ),
	            );
	        }
	    }
	    return $enabled_section;
	
    
}

if ( ! function_exists( 'sensational_blog_the_excerpt' ) ) :

	/**
	 * Generate excerpt.
	 *
	 * @since 1.0.0
	 *
	 * @param int     $length Excerpt length in words.
	 * @param WP_Post $post_obj WP_Post instance (Optional).
	 * @return string Excerpt.
	 */
	function sensational_blog_the_excerpt( $length = 0, $post_obj = null ) {

		global $post;

		if ( is_null( $post_obj ) ) {
			$post_obj = $post;
		}

		$length = absint( $length );

		if ( 0 === $length ) {
			return;
		}

		$source_content = $post_obj->post_content;

		if ( ! empty( $post_obj->post_excerpt ) ) {
			$source_content = $post_obj->post_excerpt;
		}

		$source_content = preg_replace( '`\[[^\]]*\]`', '', $source_content );
		$trimmed_content = wp_trim_words( $source_content, $length, '&hellip;' );
		return $trimmed_content;

	}

endif;

//  Customizer Control
if (class_exists('WP_Customize_Control') && ! class_exists( 'Sensational_Blog_Image_Radio_Control' ) ) {
	/**
 	* Customize sidebar layout control.
 	*/
	class Sensational_Blog_Image_Radio_Control extends WP_Customize_Control {

		public function render_content() {

			if (empty($this->choices))
				return;

			$name = '_customize-radio-' . $this->id;
			?>
			<span class="customize-control-title"><?php echo esc_html($this->label); ?></span>
			<ul class="controls" id='sensational-blog-img-container'>
				<?php
				foreach ($this->choices as $value => $label) :
					$class = ($this->value() == $value) ? 'sensational-blog-radio-img-selected sensational-blog-radio-img-img' : 'sensational-blog-radio-img-img';
					?>
					<li style="display: inline;">
						<label>
							<input <?php $this->link(); ?>style = 'display:none' type="radio" value="<?php echo esc_attr($value); ?>" name="<?php echo esc_attr($name); ?>" <?php
								$this->link();
								checked($this->value(), $value);
								?> />
							<img src='<?php echo esc_url($label); ?>' class='<?php echo esc_attr($class); ?>' />
						</label>
					</li>
					<?php
				endforeach;
				?>
			</ul>
			<?php
		}

	}
}	
