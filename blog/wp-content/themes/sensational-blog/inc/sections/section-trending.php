<?php
    $trending_title       = sensational_blog_get_option( 'trending_title' );
    $trending_subtitle       = sensational_blog_get_option( 'trending_subtitle' );
    $trending_content_type     = sensational_blog_get_option( 'trending_content_type' );
    $enable_category     = sensational_blog_get_option( 'trending_category_enable' );
    $enable_content     = sensational_blog_get_option( 'trending_content_enable' );
    $enable_author     = sensational_blog_get_option( 'trending_author_enable' );
    $enable_posted_on     = sensational_blog_get_option( 'trending_posted_on_enable' );
    $number_of_trending_items  = sensational_blog_get_option( 'number_of_trending_items' );
    $trending_category = sensational_blog_get_option( 'trending_category' );
    $header_font_size = sensational_blog_get_option( 'trending_font_size');
    $number_of_trending_column = sensational_blog_get_option('number_of_trending_column');
    $content_align = sensational_blog_get_option('trending_content_align');
    $excerpt_length =sensational_blog_get_option( 'trending_excerpt_length');
    $trending_post_count =sensational_blog_get_option( 'trending_post_count_enable');

    $see_more_txt     = sensational_blog_get_option( 'trending_see_all_txt' );
    $see_more_url     = sensational_blog_get_option( 'trending_see_all_url' );

    for( $i=1; $i<=$number_of_trending_items; $i++ ) :
        $trending_page_posts[] = absint(sensational_blog_get_option( 'trending_page_'.$i ) );
        $trending_post_posts[] = absint(sensational_blog_get_option( 'trending_post_'.$i ) );
    endfor;

?>  
<style>
    <?php if ($header_font_size != 0): ?>
        #trending .section-title{
            font-size:<?php echo esc_attr($header_font_size); ?>px;
        }
    <?php endif ?>
</style>
<?php if( !empty($trending_title) ):?>
    <div class="section-header">
        <?php if (!empty($trending_title)): ?>
            <h2 class="section-title"><?php echo esc_html($trending_title);?></h2>
        <?php endif; ?>
        <?php if (!empty($trending_subtitle)): ?>
            <p class="section-subtitle"><?php echo esc_html($trending_subtitle);?></p>
        <?php endif; ?>
    </div>       
<?php endif;?> 
<div class="trending-wrapper col-3">
    <?php 
        $args = array (
            'post_type'     => 'post',
            'post_per_page' => count( $trending_post_posts ),
            'post__in'      => $trending_post_posts,
            'orderby'       =>'post__in', 
            'ignore_sticky_posts' => true, 
        ); 
        $loop = new WP_Query($args);                        
        if ( $loop->have_posts() ) :
            $i=0;  
            while ($loop->have_posts()) : $loop->the_post(); $i++;?>      
            <?php $col_class='';
            if ($i%4==1) {
                 $col_class='full-width';
             } else{
                $col_class='half-width';
             } ?>  
                <article class="<?php echo $col_class; ?> <?php echo has_post_thumbnail() ? 'has-post-thumbnail' : 'no-post-thumbnail' ; ?>">
                    <?php if ($trending_post_count==true && !has_post_thumbnail()): ?>
                        <span class="post-num"><?php echo $i; ?></span>
                    <?php endif ?>
                    <div class="trending-item-wrapper">
                        <?php if (has_post_thumbnail()) : ?>
                            <div class="post-featured-image">
                                <div class="featured-image" style="background-image: url('<?php the_post_thumbnail_url();?>');">
                                    <a href="<?php the_permalink();?>" class="post-thumbnail-link"></a>
                                    <?php $homepage_video_url = get_post_meta( get_the_ID(), 'sensational-blog-video-url', true ); ?>
                                    <?php if (!empty($homepage_video_url)): ?>
                                       <a href="<?php the_permalink();?>"> <div class="homepage-video-icon"><i class="fa fa-play"></i></div></a>
                                    <?php endif ?>
                                    <?php if ($i==1): ?>
                                        <div class="overlay"></div>
                                    <?php endif ?>
                                    <?php if ($trending_post_count==true): ?>
                                        <span class="post-num"><?php echo $i; ?></span>
                                    <?php endif ?>
                                </div><!-- .featured-image -->
                            </div>
                        <?php endif; ?>
                        <div class="entry-container <?php if ($i != 1) { echo esc_attr($content_align); } else{ echo 'content-center'; } ?>">
                            <?php if ( ($trending_content_type !== 'trending_page') && ($enable_category==true)) : ?>
                                <div class="entry-meta">
                                    <?php sensational_blog_entry_meta(); ?>
                                </div><!-- .entry-meta -->
                            <?php endif; ?>
                            <header class="entry-header">
                                <h2 class="entry-title"><a href="<?php the_permalink();?>"><?php the_title();?></a></h2>
                            </header>
                            <?php if ((($enable_posted_on==true) || ($enable_author==true)) && ($i==1)) : ?>
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
                            <?php if ((($enable_posted_on==true) || ($enable_author==true)) && ($i!=1)) : ?>
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
                        </div><!-- .entry-container -->
                    </div>
                </article>
            <?php endwhile;?>
        <?php endif; ?>
        <?php wp_reset_postdata(); ?>
</div>