<?php
/**
 * The template for displaying home page.
 * @package Sensational Blog
 */

if ( 'posts' == get_option( 'show_on_front' )  || 'posts' != get_option( 'show_on_front' )){ 
    get_header(); ?>
    <?php 
    $enabled_sections = sensational_blog_get_sections();
    $homepage_design_layout     = sensational_blog_get_option( 'homepage_design_layout_options' );
    $decoration_image     = sensational_blog_get_option( 'decoration_side_enable' );
    if( is_array( $enabled_sections ) &&  $homepage_design_layout== 'home-corporate') { 
        foreach( $enabled_sections as $section ) { ?>
            <?php if( ( $section['id'] == 'featured-slider' ) ){ ?>
                <?php $disable_featured_slider = sensational_blog_get_option( 'disable_featured-slider_section' );
                if( true == $disable_featured_slider): ?>
                    <section id="<?php echo esc_attr( $section['id'] ); ?>">
                        
                        <?php $slider_layout = sensational_blog_get_option( 'slider_layout_option'); ?>
                        <?php if ($slider_layout=='default-slider'){ ?>
                            <div class="wrapper">
                                <?php get_template_part( 'inc/sections/section', esc_attr( $section['id'] ) ); ?>
                            </div>
                        <?php } else {
                            get_template_part( 'inc/sections/section', esc_attr( $section['id'] ) ); 
                        } ?>                       
                    </section>
            <?php endif; ?>
            <?php } elseif( $section['id'] == 'highlights' ) { ?>
                <?php $disable_highlights_section = sensational_blog_get_option( 'disable_highlights_section' );
                $highlights_lite_dark_bg = sensational_blog_get_option( 'highlights_lite_dark_background' );
                if( true ==$disable_highlights_section): ?>
                    <section id="<?php echo esc_attr( $section['id'] ); ?>" class="relative page-section <?php echo esc_attr($highlights_lite_dark_bg) ?>">
                        <div class="wrapper">
                            <?php get_template_part( 'inc/sections/section', esc_attr( $section['id'] ) ); ?>
                        </div>
                    </section> 
            <?php endif; ?>
            <?php } elseif( $section['id'] == 'recent' ) { ?>
                <?php $disable_recent_section = sensational_blog_get_option( 'disable_recent_section' ); 
                $recent_lite_dark_background = sensational_blog_get_option( 'recent_lite_dark_background' );
                if( true ==$disable_recent_section): ?>
                    <section id="<?php echo esc_attr( $section['id'] ); ?>" class="relative page-section <?php echo esc_attr($recent_lite_dark_background) ?>">
                        <div class="wrapper">
                            <?php get_template_part( 'inc/sections/section', esc_attr( $section['id'] ) ); ?>
                        </div>
                    </section> 
            <?php endif; ?>
            <?php } elseif( $section['id'] == 'trending' ) { ?>
                <?php $disable_trending_section = sensational_blog_get_option( 'disable_trending_section' );
                if( true ==$disable_trending_section): ?>
                    <section id="<?php echo esc_attr( $section['id'] ); ?>" class="relative page-section">
                        <div class="wrapper">
                            <?php get_template_part( 'inc/sections/section', esc_attr( $section['id'] ) ); ?>
                        </div>
                    </section> 
            <?php endif; ?> 
            <?php } elseif( $section['id'] == 'mustread' ) { ?>
                <?php $disable_mustread_section = sensational_blog_get_option( 'disable_mustread_section' );
                if( true ==$disable_mustread_section): 
                     $background_contact_section = sensational_blog_get_option( 'background_contact_section' );?>
                    <section id="<?php echo esc_attr( $section['id'] );  ?>" class="relative page-section">
                        <div class="wrapper">
                            <?php get_template_part( 'inc/sections/section', esc_attr( $section['id'] ) ); ?>
                        </div>
                    </section>
            <?php endif; ?> 
            <?php } ?>
        <?php } ?>
    <?php } ?>
    <?php $disable_homepage_content_section = sensational_blog_get_option( 'disable_homepage_content_section' );
    if('posts' == get_option( 'show_on_front' )){ ?>
       <?php include( get_home_template() ); ?>
    <?php } elseif(($disable_homepage_content_section == true ) && ('posts' != get_option( 'show_on_front' ))) { ?>
        <div class="wrapper">
           <?php include( get_page_template() ); ?>
        </div>
     <?php  }
    get_footer();
} ?>