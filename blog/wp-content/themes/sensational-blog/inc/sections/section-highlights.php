<?php 
/**
 * Template part for displaying Highlights Section
 *
 *@package Sensational Blog
 */
$highlights_title       = sensational_blog_get_option( 'highlights_title' );
$highlights_subtitle       = sensational_blog_get_option( 'highlights_subtitle' );
$highlights_content_type   = sensational_blog_get_option( 'highlights_content_type' );
$number_of_highlights_items = sensational_blog_get_option( 'number_of_highlights_items' );
$highlights_layout = sensational_blog_get_option( 'highlights_layout_option' );
$highlights_column = sensational_blog_get_option( 'number_of_highlights_column' );
$highlights_category = sensational_blog_get_option( 'highlights_category' );
$enable_content     = sensational_blog_get_option( 'highlights_content_enable' );
$enable_title     = sensational_blog_get_option( 'highlights_title_enable' );
$enable_category     = sensational_blog_get_option( 'highlights_category_enable' );
$enable_posted_on     = sensational_blog_get_option( 'highlights_posted_on_enable' );
$enable_author     = sensational_blog_get_option( 'highlights_author_enable' );
$highlights_speed   = sensational_blog_get_option( 'highlights_speed' );
$highlights_dot   = sensational_blog_get_option( 'highlights_dot' );
$highlights_arrow   = sensational_blog_get_option( 'highlights_arrow_enable' );
$highlights_autoplay  = sensational_blog_get_option( 'highlights_autoplay_enable' );
$highlights_infinite  = sensational_blog_get_option( 'highlights_infinite_enable' );
$highlights_fade  = sensational_blog_get_option( 'highlights_fade_enable' );
$header_font_size = sensational_blog_get_option( 'highlights_font_size');
$highlights_background_color = sensational_blog_get_option( 'highlights_background_color');
$highlights_post_count =sensational_blog_get_option( 'highlights_post_count_enable');
$excerpt_length =sensational_blog_get_option( 'highlights_excerpt_length');
$class ='';
if (true == $highlights_dot) {
   $class = 'true';
} else{
    $class = 'false';
}
for( $i=1; $i<=$number_of_highlights_items; $i++ ) :
    $highlights_page_posts[] = sensational_blog_get_option( 'highlights_page_'.$i );
    $highlights_posts[] = sensational_blog_get_option( 'highlights_post_'.$i );
endfor;
?>
<style>
    <?php if ($header_font_size != 0): ?>
        #highlights .entry-title{
            font-size:<?php echo esc_attr($header_font_size); ?>px;
        }
    <?php endif ?>
</style>
<?php if( !empty($highlights_title) ):?>
    <div class="section-header">
        <?php if (!empty($highlights_title)): ?>
            <h2 class="section-title"><?php echo esc_html($highlights_title);?></h2>
        <?php endif; ?>
        <?php if (!empty($highlights_subtitle)): ?>
            <p class="section-subtitle"><?php echo esc_html($highlights_subtitle);?></p>
        <?php endif; ?>
    </div>       
<?php endif;?> 
<div class="highlights-wrapper" 
    data-slick='{"slidesToShow": <?php echo esc_attr( $highlights_column) ?>,
     "slidesToScroll": 1, 
     "infinite": <?php if( true== $highlights_infinite ){ echo 'true'; } else{ echo 'false'; } ?>, 
     "speed": <?php echo esc_attr( $highlights_speed) ?>, 
     "dots": <?php echo esc_html($class) ?>, 
     "arrows":<?php if( true== $highlights_arrow ){ echo 'true'; } else{ echo 'false'; } ?>, 
     "autoplay": <?php if( true== $highlights_autoplay ){ echo 'true'; } else{ echo 'false'; } ?>, 
     "fade": false }'>
        <?php 
            $args = array (
            
            'post_type'     => 'post',
            'post_per_page' => count( $highlights_posts ),
            'post__in'      => $highlights_posts,
            'orderby'       =>'post__in',
        ); 
        $loop = new WP_Query($args);                        
        if ( $loop->have_posts() ) :
        $i=0;  
            while ($loop->have_posts()) : $loop->the_post(); $i++;?>
                <article class="slick-item" >
                    <?php if ($highlights_post_count==true && !has_post_thumbnail()): ?>
                        <span class="post-num"><?php echo $i; ?></span>
                    <?php endif ?>
                    <div class="highlights-items-wrapper">
                        <?php if (has_post_thumbnail()){ ?>
                            <div class="featured-image" style="background-image: url('<?php the_post_thumbnail_url( 'full');?>');">
                                <a href="<?php the_permalink();?>" class="post-thumbnail-link"></a> 
                                <?php if ($highlights_post_count==true ): ?>
                                    <span class="post-num"><?php echo $i; ?></span>
                                <?php endif ?>   
                            </div><!-- .featured-image -->
                        <?php } ?>
                        <div class="entry-container">
                            <?php if ( ($highlights_content_type != 'highlights_page') && ($enable_category==true)) { ?>
                                <div class="entry-meta">
                                    <?php sensational_blog_entry_meta(); ?>
                                </div><!-- .entry-meta -->
                            <?php } ?>
                            <header class="entry-header">
                                <h2 class="entry-title" ><a href="<?php the_permalink();?>"><?php the_title();?></a></h2>
                            </header>
                            <?php if ( ($enable_content==true)): ?>
                                <div class="entry-content">
                                    <?php
                                        $excerpt = sensational_blog_the_excerpt( $excerpt_length );
                                        echo wp_kses_post( wpautop( $excerpt ) );
                                    ?>
                                </div><!-- .entry-content -->
                            <?php endif; ?>
                            
                            <?php if ( ($enable_posted_on==true) || ($enable_author==true) ) { ?>
                                <div class="entry-meta">   
                                    <?php 
                                        if (($enable_author==true)) {              
                                            sensational_blog_posted_on();
                                        }
                                        if (($enable_author==true)) {
                                            sensational_blog_author();
                                        } 
                                    ?>
                                </div><!-- .entry-meta -->
                            <?php } ?>

                        </div><!-- .entry-container --> 
                    </div>    
                </article><!-- .slick-item -->
            <?php endwhile;?>
        <?php endif;?>
        <?php wp_reset_postdata(); ?>
</div><!-- .highlights-wrapper -->