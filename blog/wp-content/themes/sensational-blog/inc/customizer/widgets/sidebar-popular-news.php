<?php
/**
 * Trending Posts.
 *
 * @package Sensational Blog
 */

function sensational_blog_sidebar_popular_news() {
	register_widget( 'Sensational_Blog_Sidebar_Popular_News' );
}
add_action( 'widgets_init', 'sensational_blog_sidebar_popular_news' );

class Sensational_Blog_Sidebar_Popular_News extends WP_Widget{ 

	function __construct() {
		global $control_ops;
		$widget_sidebar_popular = array(
		  'classname'   => 'sidebar-popular-news',
		  'description' => esc_html__( 'Add Widget to Display Trending Posts.', 'sensational-blog' )
		);
		parent::__construct( 'Sensational_Blog_Sidebar_Popular_News',esc_html__( 'ST: Sidebar Trending Posts', 'sensational-blog' ), $widget_sidebar_popular, $control_ops );
	}

	function form( $instance ) {
		$instance = wp_parse_args( (array) $instance, 
			array( 
			  'title'			=> esc_html__( 'Trending Posts', 'sensational-blog' ),		
			  'category'       	=> '', 
			  'number'          => 6, 
			  'show_category'	=> true,	
			) 
		);
		$title     			= isset( $instance['title'] ) ? esc_html( $instance['title'] ) : esc_html__( 'Trending Posts', 'sensational-blog' );
		$category 			= isset( $instance['category'] ) ? absint( $instance['category'] ) : 0;
		$number    			= isset( $instance['number'] ) ? absint( $instance['number'] ) : 6;   
		$show_category 		= isset( $instance['show_category'] ) ? (bool) $instance['show_category'] : true; 
	?>
	    <p>
	    	<label for="<?php echo esc_attr($this->get_field_id( 'title' )); ?>"><?php echo esc_html__( 'Title:', 'sensational-blog' ); ?></label>
	    	<input class="widefat" id="<?php echo esc_attr($this->get_field_id( 'title' )); ?>" name="<?php echo esc_attr($this->get_field_name( 'title' )); ?>" type="text" value="<?php echo esc_attr($title); ?>" />
		</p>	
		<p>
			<label for="<?php echo esc_attr( $this->get_field_id( 'category' ) ); ?>">
				<?php esc_html_e( 'Select Category:', 'sensational-blog' ); ?>			
			</label>

			<?php
				wp_dropdown_categories(array(
					'show_option_none' => '',
					'class' 		  => 'widefat',
					'show_option_all'  => esc_html__('Trending Posts','sensational-blog'),
					'name'             => esc_attr($this->get_field_name( 'category' )),
					'selected'         => absint( $category ),          
				) );
			?>
		</p>

	    <p>
	    	<label for="<?php echo esc_attr($this->get_field_id( 'number' )); ?>">
	    		<?php echo esc_html__( 'Choose Number (Max: 6)', 'sensational-blog' );?>    		
	    	</label>

	    	<input class="widefat" id="<?php echo esc_attr($this->get_field_id( 'number' )); ?>" name="<?php echo esc_attr($this->get_field_name( 'number' )); ?>" type="number" step="1" min="1" value="<?php echo esc_attr($number); ?>" max="6" />
	    </p>	
  
    <?php
    }

	function update( $new_instance, $old_instance ) {
		$instance = $old_instance;
		$instance['title'] 				= sanitize_text_field( $new_instance['title'] );
		$instance['category'] 			= absint( $new_instance['category'] );		
		$instance['number'] 			= (int) $new_instance['number'];
		$instance['show_category'] 		= (bool) $new_instance['show_category'];  	   
		return $instance;
	}

    function widget( $args, $instance ) {

    	extract( $args ); 
		$title     			= isset( $instance['title'] ) ? esc_html( $instance['title'] ) : esc_html__( 'Trending Posts', 'sensational-blog' );
    	$title 				= apply_filters( 'widget_title', $title, $instance, $this->id_base );
    	
        $category  			= isset( $instance[ 'category' ] ) ? $instance[ 'category' ] : 0;
        $featured_category  = isset( $instance[ 'featured_category' ] ) ? $instance[ 'featured_category' ] : 0;
        $number 			= ( ! empty( $instance['number'] ) ) ? absint( $instance['number'] ) : 6; 
        $show_category		= isset( $instance['show_category'] ) ? $instance['show_category'] : true;
        echo $before_widget;
        ?>   		    
	        
        <?php $recent_args = array(
            'posts_per_page' => absint( $number ),
            'post_type' => 'post',
            'post_status' => 'publish',
            'post__not_in' => get_option( 'sticky_posts' ),      
        );

        if ( absint( $category ) > 0 ) {
          $recent_args['cat'] = absint( $category );
        }

        $recent_loop = new WP_Query( $recent_args ); 
         ?>		            
			<div class="section-header">
            	<?php if ( !empty( $title ) ): ?>
		           <?php echo $args['before_title'] . esc_html($title) . $args['after_title']; ?>
		        <?php endif; ?>
		    </div>     
 			<ul>
    			<?php if ($recent_loop->have_posts()) : 
    				$count= 0;
        		 while ( $recent_loop->have_posts() ) : $recent_loop->the_post(); 
        		 	if( has_post_thumbnail() ){
					        $image_class = 'has-post-thumbnail'; 
					    } else {
					        $image_class = 'no-post-thumbnail'; 
					    }
					    ?>
                    <li class="<?php echo esc_attr( $image_class ); ?>">
				        <?php if( has_post_thumbnail() ) : ?>
				        	<div class="post-featured-image">
					        	<div class="featured-image" style="background-image: url('<?php the_post_thumbnail_url();?>');" > 
					            <a href="<?php the_permalink();?>" class="post-thumbnail-link"></a>
					           </div>
				           </div>
				        <?php endif; ?>
				        <div class="entry-container">   
					        <header class="entry-header">
	                            <h3 class="entry-title"><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h3>
	                        </header>
	                        <div class="entry-meta">  
	                        	<?php sensational_blog_posted_on();?>
	                        </div><!-- .entry-meta -->
	                    </div>
				    </li>
        		<?php endwhile; wp_reset_postdata(); endif;?>
        		</ul>	    
        <?php echo $after_widget;

    } 

}