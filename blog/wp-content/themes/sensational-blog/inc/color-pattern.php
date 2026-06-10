<?php
/**
 * Sensational Blog: Color Patterns
 *
 * @package WordPress
 * @subpackage Sensational Blog
 * @since 1.0
 */

/**
 * Generate the CSS for the current custom color scheme.
 */
function sensational_blog_custom_colors_css() {

	$color_value = sensational_blog_get_option('colorscheme_hue');
	$medi_text_color = sensational_blog_get_option('medi_text_color');
	$medi_secondary_color = sensational_blog_get_option('medi_secondary_color');
	$footer_wgt_bg_color = sensational_blog_get_option('footer_widget_background_color');
	$footer_wgt_font_color = sensational_blog_get_option('footer_widget_font_color');
	$footer_cr_bg_color = sensational_blog_get_option('footer_copyright_background_color');
	$footer_cr_font_color = sensational_blog_get_option('footer_copyright_font_color');
	$menu_bg_color = sensational_blog_get_option('menu_background_color');
	$menu_font_color = sensational_blog_get_option('menu_font_color');

	$slider_background_color = sensational_blog_get_option( 'slider_background_color');
	$services_background_color = sensational_blog_get_option( 'services_background_color');
	$message_background_color = sensational_blog_get_option('message_background_color');
	$features_background_color = sensational_blog_get_option('features_background_color');
	$admissionprocess_background_color = sensational_blog_get_option('admissionprocess_background_color');
	$team_background_color = sensational_blog_get_option('team_background_color');
	$instagram_section_background_color = sensational_blog_get_option('instagram_section_background_color');
	$popular_background_color = sensational_blog_get_option('popular_background_color');
	$shopproduct_background_color = sensational_blog_get_option('shopproduct_background_color');
	$event_background_color = sensational_blog_get_option('event_background_color');
	$mustread_background_color = sensational_blog_get_option('mustread_background_color');
	$client_background_color = sensational_blog_get_option('client_background_color');
	$counter_background_color = sensational_blog_get_option('counter_background_color');
	$project_background_color = sensational_blog_get_option('project_background_color');

	$header_text_decoration = sensational_blog_get_option('header_text_decoration_options');
	$header_text_transform = sensational_blog_get_option('header_text_transform_options');
	$header_font_style = sensational_blog_get_option('header_font_style_options');
	$header_padding = sensational_blog_get_option('header_top_buttom_padding');
	
	$css = '
	:root{
		--primary-color: ' . esc_attr($color_value) . ';
		--medi-secondary-color: ' . esc_attr($medi_secondary_color) . ';
		--color-text: ' . esc_attr($medi_text_color) . ';
	}
	.header-one .site-menu,
	.header-two .site-menu .header-logo-ads,
	.header-three .site-menu .header-logo-ads,
	.header-five .site-menu .header-logo-ads,
	.header-six .site-menu .wrapper,
	.modern-menu .site-menu
	{
		padding-top: ' . esc_attr($header_padding) . 'px !important;
		padding-bottom: ' . esc_attr($header_padding) . 'px !important;
	}
	
	#colophon{
		background-color: '. esc_attr($footer_wgt_bg_color) . ';
	}
	.footer-widgets-area .cloud-top svg{
		fill: '. esc_attr($footer_wgt_bg_color) . ';
	}
	#colophon .widget-title, 
	#colophon .widgettitle,
	#colophon .widget li::before,
	#colophon .widget li a,
	#colophon p,
	#colophon .widget_block .wp-block-heading{
		color: '. esc_attr($footer_wgt_font_color) . ';
	}
	.site-info{
		background-color: '. esc_attr($footer_cr_bg_color) . ';
	}
	.site-info span.copy-right{
		color: '. esc_attr($footer_cr_font_color) . ';
	}
	.header-one #masthead,
	.header-three .site-menu nav, 
	.header-two .site-menu nav,
	body:not(.header-four) .main-navigation ul ul li a,
	.header-three .site-menu nav,.header-three.home-magazine .site-menu nav, 
	.header-two.home-magazine .site-menu nav, 
	.header-three.home-normal-magazine .site-menu nav, 
	.header-two.home-normal-magazine .site-menu nav,
	.header-five .site-menu nav,
	.header-six #masthead .wrapper,
	.kids-menu #masthead{
		background-color: '. esc_attr($menu_bg_color) . ';
	}
	.cloud-header-bottom svg,
	.cloud-down.slider-cloud svg{
		fill: '. esc_attr($menu_bg_color) . ';
	}
	.kids-menu .main-navigation ul ul li a{
		background-color:#fff;
	}
	@media screen and (max-width: 1023px) {
		#masthead.site-header .main-navigation ul.nav-menu{
			background-color: '. esc_attr($menu_bg_color) . ';
		}
		.header-three .site-menu nav,
		.header-two .site-menu nav,
		.header-five .site-menu nav{
			background-color:transparent;
		}
		.modern-menu .site-menu{
			padding:0;
		}
	}
	.header-five .main-navigation ul .sub-menu li a,
    .header-three .main-navigation ul .sub-menu li a,
    .header-two .main-navigation ul .sub-menu li a,
	.main-navigation ul.nav-menu > li > a,
	.main-navigation ul ul li a,
	.menu-hover-3 .main-navigation ul.nav-menu > li.menu-item-has-children:after{
		color: '. esc_attr($menu_font_color) . ';
	}
	/* Section Background Color */
	#services {
	    background-color: ' . esc_attr($services_background_color) . ';
	}
	#featured-slider .cloud-up.slider-cloud svg,
	#services .cloud-bottom svg{
	    fill: ' . esc_attr($services_background_color) . ';
	}

	#message {
	    background-color: ' . esc_attr($message_background_color) . ';
	}
	#message .cloud-bottom svg{
	    fill: ' . esc_attr($message_background_color) . ';
	}

	#project {
	    background-color: ' . esc_attr($project_background_color) . ';
	}
	#project .cloud-bottom svg{
	    fill: ' . esc_attr($project_background_color) . ';
	}

	#admissionprocess{
	    background-color: ' . esc_attr($admissionprocess_background_color) . ';
	}
	#admissionprocess .cloud-bottom svg{
	    fill: ' . esc_attr($admissionprocess_background_color) . ';
	} 

	#features{
	    background-color: ' . esc_attr($features_background_color) . ';
	}

	#team{
	    background-color: ' . esc_attr($team_background_color) . ';
	}
	#team .cloud-top svg{
	    fill: ' . esc_attr($team_background_color) . ';
	} 

	#shopproduct{
	    background-color: ' . esc_attr($shopproduct_background_color) . ';
	}
	#shopproduct .cloud-top svg{
	    fill: ' . esc_attr($shopproduct_background_color) . ';
	} 

	#event{
	    background-color: ' . esc_attr($event_background_color) . ';
	}
	#event .cloud-bottom svg,
	#event .cloud-top svg{
	    fill: ' . esc_attr($event_background_color) . ';
	}

	#client{
	    background-color: ' . esc_attr($client_background_color) . ';
	}
	#client .cloud-top svg{
	    fill: ' . esc_attr($client_background_color) . ';
	} 
	
	#mustread{
	    background-color: ' . esc_attr($mustread_background_color) . ';
	}
	#mustread .cloud-top svg{
	    fill: ' . esc_attr($mustread_background_color) . ';
	}
	#counter{
	    background-color: ' . esc_attr($counter_background_color) . ';
	}
	.half-image-slider #featured-slider {
	    background-color: ' . esc_attr($slider_background_color) . ';
	}
	
	

	.loader-container svg,
	.main-navigation ul.nav-menu > li > a.search:hover svg.icon-search,
	.main-navigation ul.nav-menu > li > a.search:focus svg.icon-search,
	.single-post-wrapper span.posted-on svg,
	.single-post-wrapper span.cat-links svg {
		fill:#110e0e;
	}
	
	.main-navigation ul.nav-menu > li:not(:last-child):after {
		background-color: #fff;
	}
	.post-navigation a, 
	.posts-navigation a,
	.widget_popular_post h3 a:hover,
	.widget_popular_post h3 a:focus,
	.widget_popular_post a:hover time,
	.widget_popular_post a:focus time,
	.widget_latest_post h3 a:hover,
	.widget_latest_post h3 a:focus,
	.widget_latest_post a:hover time,
	.widget_latest_post a:focus time,
	.widget_featured_courses .course-wrapper h5 a:hover,
	.widget_featured_courses .course-wrapper h5 a:focus,
	.widget_featured_courses .tp-course-price,
	.widget_popular_courses .course-wrapper h5 a:hover,
	.widget_popular_courses .course-wrapper h5 a:focus,
	.widget_popular_courses .tp-course-price,
	.page-header small,
	.single-post-wrapper span.tags-links a:hover,
	.single-post-wrapper span.tags-links a:focus,
	ul.post-categories li a:hover,
	ul.post-categories li a:focus,
	.icon-container i,
	#special-offer .price ins,
	#special-menu article:hover .woocommerce-Price-amount.amount,
	#special-offer .woocommerce-Price-amount.amount {
		color: #110e0e;
	}
	#special-menu .woocommerce-Price-amount.amount,
	#special-menu .section-subtitle,
	#special-menu .entry-content, 
	#special-menu .entry-content p{
		color: #fff;
	}';

	$css .= '
	@keyframes preloader {
	    0% {height:5px;transform:translateY(0px);background: ' . esc_attr($color_value) . ';}
	    25% {height:30px;transform:translateY(15px);background: ' . esc_attr($color_value) . ';}
	    50% {height:5px;transform:translateY(0px);background: ' . esc_attr($color_value) . ';}
	    100% {height:5px;transform:translateY(0px);background: ' . esc_attr($color_value) . ';}
	}';

	$css .= '
	.page .page-title,
	.single .page-title,
	.single .entry-title,
	.entry-title{
	    text-transform: ' . esc_attr($header_text_transform) . ';
	}
	.page .page-title,
	.single .page-title,
	.single .entry-title,
	.entry-title{
	    text-decoration: ' . esc_attr($header_text_decoration) . ';
	}
	.page .page-title,
	.single .page-title,
	.single .entry-title,
	.entry-title{
	    font-style: ' . esc_attr($header_font_style) . ';
	}';
	/**
	 * Filters Sensational Blog custom colors CSS.
	 *
	 * @since Sensational Blog 1.0.0
	 *
	 * @param string $css        Base theme colors CSS.
	 */
	return apply_filters( 'sensational_blog_custom_colors_css', $css );
}
