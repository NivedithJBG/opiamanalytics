<?php
/**
 * Template part for displaying page content in page.php
 *
 * @link https://codex.wordpress.org/Template_Hierarchy
 *
 * @package Sensational Blog
 */

?>
<?php 
$enable_video = sensational_blog_get_option( 'single_page_video_enable' );
$enable_image = sensational_blog_get_option( 'single_page_image_enable' );
$enable_header_image     = sensational_blog_get_option( 'single_page_header_image_enable' ); ?>
<article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
	<?php if ($enable_header_image==false): ?>
		<header class="entry-header">
	        <?php the_title( '<h1 class="entry-title">', '</h1>' ); ?>
	    </header>
	<?php endif; ?>
	<?php
		if ( $enable_image ) { ?>
			<div class="post-featured-image">
				<div class="featured-image">
			        <?php the_post_thumbnail( 'full', array( 'alt' => the_title_attribute( 'echo=0' ) ) ); ?>
				</div><!-- .featured-post-image -->
			</div>
		<?php } ?>
	<div class="entry-content">
		<?php
			
			the_content();
			$video_url = get_post_meta( get_the_ID(), 'sensational-blog-video-url', true );
            if ( ! empty( $video_url ) && ($enable_video==true) ) { ?>
				<div class="featured-video">
		            <?php echo do_shortcode( '[video src="' . esc_url( $video_url ) . '"]' );?>
		        </div><!-- .featured-image -->
		    <?php } 
			wp_link_pages( array(
				'before' => '<div class="page-links">' . esc_html__( 'Pages:', 'sensational-blog' ),
				'after'  => '</div>',
			) );
		?>

	</div><!-- .entry-content -->

	<?php if ( get_edit_post_link() ) : ?> 
		<footer class="entry-footer">
			<?php
				edit_post_link(
					sprintf(
						/* translators: %s: Name of current post */
						esc_html__( 'Edit %s', 'sensational-blog' ),
						the_title( '<span class="screen-reader-text">"', '"</span>', false )
					),
					'<span class="edit-link">',
					'</span>'
				);
			?>
		</footer><!-- .entry-footer -->
	<?php endif; ?>
</article><!-- #post-## -->
