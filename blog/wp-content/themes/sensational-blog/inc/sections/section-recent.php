<?php
    $recent_title       = sensational_blog_get_option( 'recent_title' );
    $recent_subtitle       = sensational_blog_get_option( 'recent_subtitle' );
    $recent_content_type     = sensational_blog_get_option( 'recent_content_type' );
    $enable_category     = sensational_blog_get_option( 'recent_category_enable' );
    $enable_content     = sensational_blog_get_option( 'recent_content_enable' );
    $enable_author     = sensational_blog_get_option( 'recent_author_enable' );
    $enable_posted_on     = sensational_blog_get_option( 'recent_posted_on_enable' );
    $number_of_recent_items  = sensational_blog_get_option( 'number_of_recent_items' );
    $recent_category = sensational_blog_get_option( 'recent_category' );
    $header_font_size = sensational_blog_get_option( 'recent_font_size');
    $no_of_recent_column = sensational_blog_get_option('number_of_recent_column');
    $content_align = sensational_blog_get_option('recent_content_align');
    $excerpt_length =sensational_blog_get_option( 'recent_excerpt_length');
    $recent_post_count =sensational_blog_get_option( 'recent_post_count_enable');

    $see_more_txt     = sensational_blog_get_option( 'recent_see_all_txt' );
    $see_more_url     = sensational_blog_get_option( 'recent_see_all_url' );

    for( $i=1; $i<=$number_of_recent_items; $i++ ) :
        $recent_page_posts[] = absint(sensational_blog_get_option( 'recent_page_'.$i ) );
        $recent_post_posts[] = absint(sensational_blog_get_option( 'recent_post_'.$i ) );
    endfor;

?>  
<style>
    <?php if ($header_font_size != 0): ?>
        #recent .section-title{
            font-size:<?php echo esc_attr($header_font_size); ?>px;
        }
    <?php endif ?>
</style>
<?php if( !empty($recent_title) ):?>
    <div class="section-header">
        <?php if (!empty($recent_title)): ?>
            <h2 class="section-title"><?php echo esc_html($recent_title);?></h2>
        <?php endif; ?>
        <?php if (!empty($recent_subtitle)): ?>
            <p class="section-subtitle"><?php echo esc_html($recent_subtitle);?></p>
        <?php endif; ?>
    </div>       
<?php endif;?> 
<div class="must-read-wrapper col-<?php echo esc_attr($no_of_recent_column) ?>">
    <?php 
            $args = array (
                'post_type'     => 'post',
                'post_per_page' => count( $recent_post_posts ),
                'post__in'      => $recent_post_posts,
                'orderby'       =>'post__in', 
                'ignore_sticky_posts' => true, 
            ); 
        
        $loop = new WP_Query($args);                        
        if ( $loop->have_posts() ) :
            $i=0;  
            while ($loop->have_posts()) : $loop->the_post(); $i++;?>      
            <?php $col_class='';
            if ($i==1) {
                 $col_class='full-width';
             } else{
                $col_class='half-width';
             } ?>  
                <article class="<?php echo has_post_thumbnail() ? 'has-post-thumbnail' : 'no-post-thumbnail' ; ?>">
                    <?php if ($recent_post_count==true && !has_post_thumbnail()): ?>
                        <span class="post-num"><?php echo $i; ?></span>
                    <?php endif ?>
                    <div class="recent-item-wrapper">
                        <?php if (has_post_thumbnail()) : ?>
                            <div class="post-featured-image">
                                <div class="featured-image" style="background-image: url('<?php the_post_thumbnail_url();?>');">
                                    <a href="<?php the_permalink();?>" class="post-thumbnail-link"></a>
                                    <?php $homepage_video_url = get_post_meta( get_the_ID(), 'sensational-blog-video-url', true ); ?>
                                    <?php if (!empty($homepage_video_url)): ?>
                                       <a href="<?php the_permalink();?>"> <div class="homepage-video-icon"><i class="fa fa-play"></i></div></a>
                                    <?php endif ?>
                                    <?php if ($recent_post_count==true): ?>
                                        <span class="post-num"><?php echo $i; ?></span>
                                    <?php endif ?>
                                </div><!-- .featured-image -->
                            </div>
                        <?php endif; ?>
                        <div class="entry-container <?php echo esc_attr($content_align); ?>">
                            <?php if ( ($recent_content_type !== 'recent_page') && ($enable_category==true) ) : ?>
                                <div class="entry-meta">
                                    <?php sensational_blog_entry_meta(); ?>
                                </div><!-- .entry-meta -->
                            <?php endif; ?>
                            <header class="entry-header">
                                <h2 class="entry-title"><a href="<?php the_permalink();?>"><?php the_title();?></a></h2>
                            </header>
                            <?php if ((($enable_posted_on==true) || ($enable_author==true))) : ?>
                                <div class="entry-meta">
                                    <?php 
                                        if (($enable_posted_on==true)) {
                                            sensational_blog_posted_on();
                                        } 
                                        if (($enable_author==true)) {
                                            sensational_blog_author();
                                        }
                                     ?>
                                </div><!-- .entry-meta -->
                            <?php endif; ?>
                            <?php if (($enable_content==true)) : ?>
                                <div class="entry-content">
                                    <?php
                                        $excerpt = sensational_blog_the_excerpt( $excerpt_length );
                                        echo wp_kses_post( wpautop( $excerpt ) );
                                    ?>
                                </div><!-- .entry-content -->
                            <?php endif; ?>  
                        </div><!-- .entry-container -->
                    </div>
                </article>

            <?php endwhile;?>
        <?php endif; ?>
        <?php wp_reset_postdata(); ?>
</div>