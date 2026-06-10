<?php
if ( ! get_theme_mod( 'digital_blog_enable_banner_section', false ) ) {
	return;
}

$main_banner_content_ids  = array();
$main_banner_content_type = get_theme_mod( 'digital_blog_main_banner_posts_content_type', 'post' );

if ( $main_banner_content_type === 'post' ) {
	for ( $i = 1; $i <= 4; $i++ ) {
		$main_banner_content_ids[] = get_theme_mod( 'digital_blog_main_banner_posts_content_post_' . $i );
	}
	$main_banner_args = array(
		'post_type'           => 'post',
		'posts_per_page'      => absint( 4 ),
		'ignore_sticky_posts' => true,
	);
	if ( ! empty( array_filter( $main_banner_content_ids ) ) ) {
		$main_banner_args['post__in'] = array_filter( $main_banner_content_ids );
		$main_banner_args['orderby']  = 'post__in';
	} else {
		$main_banner_args['orderby'] = 'date';
	}
} else {
	$cat_content_id   = get_theme_mod( 'digital_blog_main_banner_posts_content_category' );
	$main_banner_args = array(
		'cat'            => $cat_content_id,
		'posts_per_page' => absint( 4 ),
	);
}
$main_banner_args = apply_filters( 'digital_blog_banner_section_args', $main_banner_args );

digital_blog_render_banner_section( $main_banner_args );

/**
 * Render Banner Section.
 */
function digital_blog_render_banner_section( $main_banner_args ) {

	$banner_query = new WP_Query( $main_banner_args );
	if ( $banner_query->have_posts() ) :
		?>
		<section id="digital_blog_banner_section" class="banner-section banner-style-2 dark-theme">
			<?php
			if ( is_customize_preview() ) :
				digital_blog_section_link( 'digital_blog_banner_section' );
			endif;
			?>
			<div class="ascendoor-wrapper">
				<div class="banner-section-wrapper">
					<div class="digital-blog-slider-wrapper banner-slider digital-blog-carousel-slider-navigation">
						<?php
						while ( $banner_query->have_posts() ) :
							$banner_query->the_post();
							?>
							<div class="carousel-item">
								<div class="blog-post-single tile-design <?php echo esc_attr( has_post_thumbnail() ? 'has-post-thumbnail' : '' ); ?>">
									<a class="post-thumbnail" href="<?php the_permalink(); ?>">
										<?php the_post_thumbnail( 'full' ); ?>
									</a>
									<div class="ascendoor-wrapper">
										<div class="blog-detail">
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
										</div>
									</div>
								</div>
							</div>
							<?php
						endwhile;
						wp_reset_postdata();
						?>
					</div>
				</div>
				<div class="ascendoor-wrapper">
					<div class="banner-section-thumb-wrapper">
						<div class="digital-blog-thumb-slider-wrapper">
							<?php
							while ( $banner_query->have_posts() ) :
								$banner_query->the_post();
								?>
								<div class="carousel-thumb-item">
									<div class="blog-post-single list-design">
										<h3 class="mag-post-title">
											<?php the_title(); ?>
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
									</div>
								</div>
								<?php
							endwhile;
							wp_reset_postdata();
							?>
						</div>
					</div>
				</div>
			</div>
		</section>
		<?php
	endif;
}
