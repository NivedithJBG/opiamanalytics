<?php

/**
 * The template for displaying all single posts
 *
 * @link https://developer.wordpress.org/themes/basics/template-hierarchy/#single-post
 *
 * @package Digital Blog
 */

get_header();
?>
<main id="primary" class="site-main">

	<?php
	while ( have_posts() ) :
		the_post();

		setPostViews( get_the_ID() );

		get_template_part( 'template-parts/content', 'single' );

		do_action( 'digital_blog_post_navigation' );

		if ( is_singular( 'post' ) ) {

			$related_posts_label = get_theme_mod( 'digital_blog_post_related_post_label', __( 'Related Posts', 'digital-blog' ) );
			$args                = array(
				'posts_per_page' => 3,
				'post__not_in'   => array( $post->ID ),
				'orderby'        => 'rand',
			);

			$cat_content_id = get_the_category( $post->ID );
			if ( ! empty( $cat_content_id ) ) {
				$args['cat'] = $cat_content_id[0]->term_id;
			}

			$query = new WP_Query( $args );

			if ( $query->have_posts() ) :
				?>
				<div class="related-posts">
					<h2><?php echo esc_html( $related_posts_label ); ?></h2>
					<div class="row">
						<?php
						while ( $query->have_posts() ) :
							$query->the_post();
							?>
							<article id="post-<?php the_ID(); ?>" <?php post_class( 'blog-post-single grid-design grid-style-1' ); ?>>
								<a class="post-thumbnail" href="<?php the_permalink(); ?>">
									<?php the_post_thumbnail( 'post-thumbnail' ); ?>
								</a>
								<?php the_title( '<h5 class="entry-title mag-post-title"><a href="' . esc_url( get_permalink() ) . '" rel="bookmark">', '</a></h5>' ); ?>
								<div class="mag-post-excerpt">
									<?php the_excerpt(); ?>
								</div><!-- .entry-content -->
							</article>
							<?php
						endwhile;
						wp_reset_postdata();
						?>
					</div>
				</div>
				<?php
				endif;
		}

		// If comments are open or we have at least one comment, load up the comment template.
		if ( comments_open() || get_comments_number() ) :
			comments_template();
	endif;

	endwhile; // End of the loop.
	?>

</main><!-- #main -->
<?php
if ( digital_blog_is_sidebar_enabled() ) {
	get_sidebar();
}
get_footer();
