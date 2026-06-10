<?php
if ( ! get_theme_mod( 'digital_blog_enable_post_carousel_section', false ) ) {
	return;
}

$content_ids  = array();
$content_type = get_theme_mod( 'digital_blog_post_carousel_content_type', 'post' );

if ( $content_type === 'post' ) {

	for ( $i = 1; $i <= 4; $i++ ) {
		$content_ids[] = get_theme_mod( 'digital_blog_post_carousel_content_post_' . $i );
	}

	$args = array(
		'post_type'           => 'post',
		'posts_per_page'      => absint( 4 ),
		'ignore_sticky_posts' => true,
	);
	if ( ! empty( array_filter( $content_ids ) ) ) {
		$args['post__in'] = array_filter( $content_ids );
		$args['orderby']  = 'post__in';
	} else {
		$args['orderby'] = 'date';
	}
} else {
	$cat_content_id = get_theme_mod( 'digital_blog_post_carousel_content_category' );
	$args           = array(
		'cat'            => $cat_content_id,
		'posts_per_page' => absint( 4 ),
	);
}

$args = apply_filters( 'digital_blog_post_carousel_section_args', $args );

digital_blog_render_post_carousel_section( $args );

/**
 * Render Post Carousel Section.
 */
function digital_blog_render_post_carousel_section( $args ) {
	$query = new WP_Query( $args );
	if ( $query->have_posts() ) :
		$section_title    = get_theme_mod( 'digital_blog_post_carousel_title', __( 'Post Carousel', 'digital-blog' ) );
		$section_subtitle = get_theme_mod( 'digital_blog_post_carousel_subtitle', '' );
		?>
		<section id="digital_blog_post_carousel_section" class="digital-blog-frontpage-section digital-blog-post-carousel-section">
			<?php
			if ( is_customize_preview() ) :
				digital_blog_section_link( 'digital_blog_post_carousel_section' );
			endif;
			?>
			<div class="ascendoor-wrapper">
				<?php if ( ! empty( $section_title || $section_subtitle ) ) { ?>
					<div class="section-header">
						<h3 class="section-title"><?php echo esc_html( $section_title ); ?></h3>
						<p class="section-subtitle"><?php echo esc_html( $section_subtitle ); ?></p>
					</div>
				<?php } ?>
				<div class="digital-blog-section-body">
					<div class="digital-blog-post-carousel-section-wrapper post-carousel digital-blog-carousel-slider-navigation">
						<?php
						while ( $query->have_posts() ) :
							$query->the_post();
							$thumbnail_class = has_post_thumbnail() ? 'has-post-thumbnail' : '';
							?>
							<div class="carousel-item">
								<div class="blog-post-single tile-design <?php echo esc_attr( $thumbnail_class ); ?>">
									<a class="post-thumbnail" href="<?php the_permalink(); ?>"><?php the_post_thumbnail(); ?></a>

									<div class="mag-post-category with-background">
										<?php digital_blog_categories_list(); ?>
									</div>
									<h3 class="mag-post-title">
										<a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
									</h3>
									<div class="mag-post-meta">
										<span class="post-author">
											<a href="<?php echo esc_url( get_author_posts_url( get_the_author_meta( 'ID' ) ) ); ?>"><i class="fas fa-user"></i><?php echo esc_html( get_the_author() ); ?></a>
										</span>
										<span class="post-date">
											<a href="<?php the_permalink(); ?>"><i class="far fa-clock"></i><?php echo esc_html( get_the_date() ); ?></a>
										</span>
										<span class="post-views">
											<i class="far fa-eye"></i>
											<?php echo getPostViews( get_the_ID() ); ?>
										</span>
									</div>
									<?php if ( ! has_post_thumbnail() ) { ?>
										<div class="mag-post-excerpt">
											<p><?php the_excerpt(); ?></p>
										</div>
									<?php } ?>
								</div>
							</div>
							<?php
						endwhile;
						wp_reset_postdata();
						?>
					</div>
				</div>
			</div>
		</section>
		<?php
	endif;
}
