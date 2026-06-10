<?php
/**
 * Default theme options.
 *
 * @package Sensational Blog
 */


if ( ! function_exists( 'sensational_blog_get_default_theme_options' ) ) :

	/**
	 * Get default theme options.
	 *
	 * @since 1.0.0
	 *
	 * @return array Default theme options.
	 */
	
	function sensational_blog_get_default_theme_options() {

		$theme_data = wp_get_theme();
		$defaults = array();

		$defaults['show_header_contact_info'] 	= true;
		$defaults['disable_homepage_content_section'] 			= false;
		$defaults['show_topbar'] 			= false;
		$defaults['topbar_layout_option'] 			= 'contact-info-option';
	    $defaults['header_email']             	= __( 'info@sensationaltheme.com','sensational-blog' );
	    $defaults['header_phone' ]            	= __( '+1-541-754-3010','sensational-blog' );
	    $defaults['header_location' ]           = __( 'London, UK','sensational-blog' );
	    $defaults['enable_header_contact_info'] 	= true;
	    $defaults['header_email_text']             	= __( 'Email ID','sensational-blog' );
	    $defaults['header_phone_text' ]            	= __( 'Free Call','sensational-blog' );
	    $defaults['header_location_text' ]           = __( 'Visit Us','sensational-blog' );
	    $defaults['header_email_address']             	= __( 'info@sensationaltheme.com','sensational-blog' );
	    $defaults['header_phone_contact' ]            	= __( '+1-541-754-3010','sensational-blog' );
	    $defaults['header_location_address' ]           = __( 'London, UK','sensational-blog' );
	    $defaults['show_header_social_links'] 	= true;
	    $defaults['show_menu_social_links'] 	= true;
	    $defaults['header_social_links']		= array();
	    $defaults['disable_header_background_section'] = false;
	    $defaults['show_header_search'] 	= true;
	    $defaults['show_current_date'] 	= true;
	    $defaults['top_login_text'] 	= __( 'Login','sensational-blog' );
	    $defaults['top_login_url'] 	='#';
	    $defaults['top_register_text'] 	= __( 'Register','sensational-blog' );
	    $defaults['top_register_url'] 	= '#';
	    $defaults['search_login_layout'] 	= 'search-form-option';
	    $defaults['colorscheme_hue'] 	= '#25a0ed';
	    $defaults['medi_text_color'] 	= '#191B1D';
	    $defaults['medi_secondary_color'] 	= '#14457B';
	    $defaults['topbar_background_color'] 	= '#000';
	    $defaults['topbar_color'] 	= '#ffffff';
	    $defaults['enable_center_logo'] 	= false;


	    $defaults['menu_background_color'] 	= '#fff';
	    $defaults['menu_text_hover'] 	= 'menu-hover-none';
	    $defaults['header_text_hover'] 	= 'title-hover-none';
	    $defaults['number_of_menu_items'] 	= 6;
	    $defaults['preloader_loader_enable'] 	= false;
	    $defaults['preloader_loader_options'] 	= 'loader-1';
	    $defaults['header_text_transform_options'] 	= 'none';
	    $defaults['header_text_decoration_options'] 	= 'none';
	    $defaults['header_font_style_options'] 	= 'none';
	    $defaults['header_text_design'] 	= false;
	    $defaults['homepage_layout_options']			= 'lite-layout';
	    $defaults['header_layout_options']			= 'header-one';
	    $defaults['homepage_design_layout_options']			= 'home-corporate';
	    $defaults['homepage_sidebar_position']			= 'home-right-sidebar';
	    $defaults['header_top_buttom_padding']			= 10;

		// Featured Slider Section
		$defaults['disable_featured-slider_section']	= false;
		$defaults['number_of_sr_items']			= 4;
		$defaults['number_of_sr_column']		= 1;
		$defaults['slider_layout_option']			= 'half-image-slider';
		$defaults['slider_content_position_option']			= 'default-position';
		$defaults['sr_content_type']			= 'sr_category';
		$defaults['slider_speed']				= 800;
		$defaults['slider_excerpt_length']			= 40;
		$defaults['disable_white_overlay']		= false;
		$defaults['slider_arrow_enable']		= true;
		$defaults['slider_fade_enable']		 	= true;
		$defaults['slider_autoplay_enable']		= true;
		$defaults['slider_infinite_enable']		= true;
		$defaults['slider_title_enable']		= true;
		$defaults['slider_category_enable']		= true;
		$defaults['slider_content_enable']		= true;
		$defaults['slider_author_enable']		= true;
		$defaults['slider_posted_on_enable']		= true;
		$defaults['disable_blog_banner_section']		= false;
		$defaults['slider_social_title_text']	   		= esc_html__( 'Follow Me:', 'sensational-blog' );

		// Highlights Section
		$defaults['disable_highlights_section']	= false;
		$defaults['highlights_title']	   	 		= esc_html__( 'Highlights Posts', 'sensational-blog' );
		$defaults['highlights_subtitle']	   	 	= esc_html__( 'Every sunrise brings a new opportunity to rewrite your story, to chase your dreams, and to embrace the magic of possibilities.', 'sensational-blog' );
		$defaults['number_of_highlights_items']			= 5;
		$defaults['number_of_highlights_column']		= 3;
		$defaults['highlights_content_type']			= 'highlights_category';
		$defaults['highlights_speed']				= 500;
		$defaults['highlights_excerpt_length']			= 5;
		$defaults['highlights_arrow_enable']		= false;
		$defaults['highlights_fade_enable']		 	= false;
		$defaults['highlights_dot']					= true;
		$defaults['highlights_autoplay_enable']		= true;
		$defaults['highlights_infinite_enable']		= true;
		$defaults['highlights_category_enable']		= false;
		$defaults['highlights_content_enable']		= false;
		$defaults['highlights_posted_on_enable']		= true;
		$defaults['highlights_author_enable']		= true;
		$defaults['highlights_post_count_enable']		= true;
		$defaults['highlights_lite_dark_background']	= 'dark-bg';
		$defaults['highlights_content_align']		= 'content-left';

		// Featured Post Section
		$defaults['disable_featuredpost_section']	= true;
		$defaults['featuredpost_title']	   	 	= esc_html__( 'Featured of the Week', 'sensational-blog' );
		$defaults['featuredpost_subtitle']	   	 	= esc_html__( 'Every sunrise brings a new opportunity to rewrite your story, to chase your dreams, and to embrace the magic of possibilities.', 'sensational-blog' );
		$defaults['featuredpost_right_title']	   	 	= esc_html__( 'Featured Posts', 'sensational-blog' );
		$defaults['number_of_featuredpost_items']			= 6;
		$defaults['number_of_featuredpost_right_items']			= 6;
		$defaults['featuredpost_layout_option']			= 'default-featuredpost';
		$defaults['featuredpost_content_type']			= 'featuredpost_post';
		$defaults['featuredpost_lite_dark_background']	= 'dark-bg';
		$defaults['featuredpost_category_enable']		= true;
		$defaults['featuredpost_posted_on_enable']		= true;
		$defaults['featuredpost_content_enable']		= true;
		$defaults['featuredpost_author_enable']		= true;
		$defaults['featuredpost_post_count_enable']		= true;
		$defaults['featuredpost_see_all_txt']			= esc_html__( 'See All', 'sensational-blog' );

		//Must Read Section
		$defaults['disable_mustread_section']	= false;
		$defaults['mustread_title']	   	 		= esc_html__( 'Must Read Posts', 'sensational-blog' );
		$defaults['mustread_subtitle']	   	 	= esc_html__( 'Every sunrise brings a new opportunity to rewrite your story, to chase your dreams, and to embrace the magic of possibilities.', 'sensational-blog' );
		$defaults['number_of_mustread_items']			= 3;
		$defaults['number_of_mustread_column']			= 3;
		$defaults['mustread_excerpt_length']			= 20;
		$defaults['mustread_content_type']			= 'mustread_category';
		$defaults['mustread_background_color']	   	= '#slider_fade_effect';
		$defaults['mustread_content_align']			= 'content-center';
		$defaults['mustread_background_color']			= '#fff';
		$defaults['mustread_category_enable']		= true;
		$defaults['mustread_posted_on_enable']		= true;
		$defaults['mustread_author_enable']		= true;
		$defaults['mustread_content_enable']		= true;
		$defaults['mustread_see_all_txt']			= esc_html__( 'See All', 'sensational-blog' );

		//Popular Section
		$defaults['disable_popular_section']	= false;
		$defaults['popular_title']	   	 		= esc_html__( 'Popular Posts', 'sensational-blog' );
		$defaults['popular_subtitle']	   	 	= esc_html__( 'Every sunrise brings a new opportunity to rewrite your story, to chase your dreams, and to embrace the magic of possibilities.', 'sensational-blog' );
		$defaults['number_of_popular_items']			= 3;
		$defaults['number_of_popular_column']			= 3;
		$defaults['popular_excerpt_length']			= 20;
		$defaults['popular_content_type']			= 'popular_category';
		$defaults['popular_content_align']			= 'content-center';
		$defaults['popular_background_color']			= '#fff';
		$defaults['popular_category_enable']		= true;
		$defaults['popular_posted_on_enable']		= true;
		$defaults['popular_author_enable']		= true;
		$defaults['popular_content_enable']		= true;
		$defaults['popular_post_count_enable']		= true;
		$defaults['popular_see_all_txt']			= esc_html__( 'See All', 'sensational-blog' );

		//Recent Section
		$defaults['disable_recent_section']	= false;
		$defaults['recent_title']	   	 		= esc_html__( 'Recent Posts', 'sensational-blog' );
		$defaults['recent_subtitle']	   	 	= esc_html__( 'Every sunrise brings a new opportunity to rewrite your story, to chase your dreams, and to embrace the magic of possibilities.', 'sensational-blog' );
		$defaults['number_of_recent_items']			= 4;
		$defaults['number_of_recent_column']			= 4;
		$defaults['recent_excerpt_length']			= 20;
		$defaults['recent_content_type']			= 'recent_category';
		$defaults['recent_background_color']	   	= '#fff';
		$defaults['recent_content_align']			= 'content-center';
		$defaults['recent_category_enable']		= true;
		$defaults['recent_lite_dark_background']		= 'dark-bg';
		$defaults['recent_posted_on_enable']		= false;
		$defaults['recent_author_enable']		= false;
		$defaults['recent_content_enable']		= false;
		$defaults['recent_post_count_enable']		= true;

		//Trending Section
		$defaults['disable_trending_section']	= false;
		$defaults['trending_title']	   	 		= esc_html__( 'Trending Posts', 'sensational-blog' );
		$defaults['trending_subtitle']	   	 	= esc_html__( 'Every sunrise brings a new opportunity to rewrite your story, to chase your dreams, and to embrace the magic of possibilities.', 'sensational-blog' );
		$defaults['number_of_trending_items']			= 8;
		$defaults['number_of_trending_column']			= 3;
		$defaults['trending_excerpt_length']			= 20;
		$defaults['trending_content_type']			= 'trending_category';
		$defaults['trending_content_align']			= 'content-center';
		$defaults['trending_background_color']			= '#fff';
		$defaults['trending_category_enable']		= true;
		$defaults['trending_posted_on_enable']		= true;
		$defaults['trending_author_enable']		= true;
		$defaults['trending_content_enable']		= true;
		$defaults['trending_post_count_enable']		= true;
		$defaults['trending_see_all_txt']			= esc_html__( 'See All', 'sensational-blog' );


		// Latest Posts Section
		$defaults['latest_posts_title']	   	 	= esc_html__( 'My Latest Stories', 'sensational-blog' );
		$defaults['latest_section_posts_title']	   	 	= esc_html__( 'I love natural beauty, and I think it’s your best look, but I think makeup as an artist is so transformative.', 'sensational-blog' );
		$defaults['number_of_latest_posts_column']	= 1;
		$defaults['pagination_type']		= 'numeric';
		$defaults['latest_category_enable']		= true;
		$defaults['latest_author_enable']		= true;
		$defaults['latest_comment_enable']		= true;
		$defaults['latest_read_more_text_enable']		= true;
		$defaults['latest_posted_on_enable']		= true;
		$defaults['latest_video_enable']		= false;
		$defaults['blog_layout_content_type']		= 'blog-three';
		$defaults['archive_content_align']		= 'content-left';
		$defaults['archive_post_header_title_enable']		= true;
		$defaults['archive_post_header_image_enable']		= true;
		$defaults['blog_post_header_image_enable']		= true;
		$defaults['blog_post_header_title_enable']		= true;
		$defaults['background_image_enable']		= true;
		
		// Decoration Option
		$defaults['decoration_side_enable']		= true;

		// Single Post Option
		$defaults['single_post_category_enable']		= true;
		$defaults['single_post_posted_on_enable']		= true;
		$defaults['single_post_video_enable']		= true;
		$defaults['single_post_comment_enable']		= true;
		$defaults['single_post_author_enable']		= true;
		$defaults['single_post_pagination_enable']		= true;
		$defaults['single_post_image_enable']		= true;
		$defaults['single_post_header_image_enable']		= true;
		$defaults['single_post_header_title_enable']		= true;
		$defaults['single_post_header_image_as_header_image_enable']		= true;


		// Single Post Option
		$defaults['single_page_video_enable']		= true;
		$defaults['single_page_image_enable']		= true;
		$defaults['single_page_header_image_enable']		= true;
		$defaults['single_page_header_title_enable']		= true;
		$defaults['single_page_header_image_as_header_image_enable']		= true;
		
		$defaults['theme_typography']			=  'default';
		$defaults['body_theme_typography']		=  'default';		
		$defaults['archive_typography']			=  'default';
		$defaults['body_archive_typography']		=  'default';		
		$defaults['page_typography']			=  'default';
		$defaults['body_page_typography']		=  'default';		
		$defaults['post_typography']			=  'default';
		$defaults['body_post_typography']		=  'default';		
		$defaults['site_title_typography']			=  'default';
		$defaults['site_tagline_typography']		=  'default';

		// Curve Option
		$defaults['corporate_curve_shape_enable']		= true;

		//General Section
		$defaults['latest_readmore_text']			= esc_html__('Read More','sensational-blog');
		$defaults['excerpt_length']					= 25;
		$defaults['layout_options_blog']			= 'right-sidebar';
		$defaults['layout_options_archive']			= 'right-sidebar';
		$defaults['layout_options_page']			= 'right-sidebar';	
		$defaults['layout_options_single']			= 'right-sidebar';	

		//Footer section 
		$defaults['scroll_top_visible']		= true;		
		$defaults['copyright_text']				= esc_html__( 'Copyright &copy; All rights reserved.', 'sensational-blog' );
		$defaults['powered_by_text']			= esc_html__( 'Sensational Blog by Sensational Theme', 'sensational-blog' );
		$defaults['enable_footer_background_image'] 	= true;
		$defaults['footer_copyright_font_color'] 	= '#fff';
		$defaults['footer_copyright_background_color'] 	= '#000';

		// Pass through filter.
		$defaults = apply_filters( 'sensational_blog_filter_default_theme_options', $defaults );
		return $defaults;
	}

endif;


/**
*  Get theme options
*/
if ( ! function_exists( 'sensational_blog_get_option' ) ) :

	/**
	 * Get theme option
	 *
	 * @since 1.0.0
	 *
	 * @param string $key Option key.
	 * @return mixed Option value.
	 */
	function sensational_blog_get_option( $key ) {

			$default_options = sensational_blog_get_default_theme_options();
		
		if ( empty( $key ) ) {
			return;
		}

		$theme_options = (array)get_theme_mod( 'theme_options' );
		$theme_options = wp_parse_args( $theme_options, $default_options );

		$value = null;

		if ( isset( $theme_options[ $key ] ) ) {
			$value = $theme_options[ $key ];
		}

		return $value;

	}

endif;