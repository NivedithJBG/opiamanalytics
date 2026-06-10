<?php
/**
 * Template part for displaying results in search pages
 *
 * @link https://developer.wordpress.org/themes/basics/template-hierarchy/
 *
 * @package Digital Blog
 */
$archive_classes = 'blog-post-single grid-design grid-style-1';
?>

<article id="post-<?php the_ID(); ?>" <?php post_class( esc_attr( $archive_classes ) ); ?>>
	<?php digital_blog_post_thumbnail(); ?>
	<div class="mag-post-category">
		<?php digital_blog_categories_list(); ?>
	</div>
	<?php
	if ( is_singular() ) :
		the_title( '<h1 class="entry-title mag-post-title">', '</h1>' );
	else :
		the_title( '<h2 class="entry-title mag-post-title"><a href="' . esc_url( get_permalink() ) . '" rel="bookmark">', '</a></h2>' );
	endif;
	?>
	<div class="mag-post-meta">
		<?php
		digital_blog_posted_by();
		digital_blog_posted_on();
		?>
		<span class="post-views">
			<i class="far fa-eye"></i>
			<?php echo getPostViews( get_the_ID() ); ?>
		</span>
	</div>
	<div class="mag-post-excerpt">
		<?php the_excerpt(); ?>
	</div>
</article><!-- #post-<?php the_ID(); ?> -->
