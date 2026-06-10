<?php

/**
 * Active Callbacks
 *
 * @package Digital Blog
 */

// Theme Options.
function digital_blog_is_pagination_enabled( $control ) {
	return ( $control->manager->get_setting( 'digital_blog_enable_pagination' )->value() );
}
function digital_blog_is_breadcrumb_enabled( $control ) {
	return ( $control->manager->get_setting( 'digital_blog_enable_breadcrumb' )->value() );
}

// Check if static home page is enabled.
function digital_blog_is_static_homepage_enabled( $control ) {
	return ( 'page' === $control->manager->get_setting( 'show_on_front' )->value() );
}

// Banner Slider Section.
function digital_blog_is_banner_section_enabled( $control ) {
	return ( $control->manager->get_setting( 'digital_blog_enable_banner_section' )->value() );
}
function digital_blog_is_banner_posts_and_content_type_post_enabled( $control ) {
	$content_type = $control->manager->get_setting( 'digital_blog_main_banner_posts_content_type' )->value();
	return ( digital_blog_is_banner_section_enabled( $control ) && ( 'post' === $content_type ) );
}
function digital_blog_is_banner_posts_and_content_type_category_enabled( $control ) {
	$content_type = $control->manager->get_setting( 'digital_blog_main_banner_posts_content_type' )->value();
	return ( digital_blog_is_banner_section_enabled( $control ) && ( 'category' === $content_type ) );
}

// Categories Section.
function digital_blog_is_categories_section_enabled( $control ) {
	return ( $control->manager->get_setting( 'digital_blog_enable_categories_section' )->value() );
}

// Post Carousel Section.
function digital_blog_is_post_carousel_section_enabled( $control ) {
	return ( $control->manager->get_setting( 'digital_blog_enable_post_carousel_section' )->value() );
}
function digital_blog_is_post_carousel_section_and_content_type_post_enabled( $control ) {
	$content_type = $control->manager->get_setting( 'digital_blog_post_carousel_content_type' )->value();
	return ( digital_blog_is_post_carousel_section_enabled( $control ) && ( 'post' === $content_type ) );
}
function digital_blog_is_post_carousel_section_and_content_type_category_enabled( $control ) {
	$content_type = $control->manager->get_setting( 'digital_blog_post_carousel_content_type' )->value();
	return ( digital_blog_is_post_carousel_section_enabled( $control ) && ( 'category' === $content_type ) );
}
