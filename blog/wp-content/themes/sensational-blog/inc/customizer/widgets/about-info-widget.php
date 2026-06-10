<?php
/**
 * About Author.
 *
 * @package kuza
 */

function sensational_blog_about_info() {
	register_widget( 'Party_Planner_Bell_About_Info_Widget' );
}
add_action( 'widgets_init', 'sensational_blog_about_info' );

class Party_Planner_Bell_About_Info_Widget extends WP_Widget{ 

	function __construct() {
		global $control_ops;
		$sensational_blog_author_info_widget = array(
		  'classname'   => 'widget-about-author',
		  'description' => esc_html__( 'Add Widget to Display About Author.', 'sensational-blog' )
		);
		parent::__construct( 'Party_Planner_Bell_About_Info_Widget',esc_html__( 'ST: About Author', 'sensational-blog' ), $sensational_blog_author_info_widget, $control_ops );
	}

function form( $instance ) {
	$section_title			= isset( $instance['title'] ) ? $instance['title'] : '';
	$author_name			= isset( $instance['name'] ) ? $instance['name'] : '';
	$author_description		= isset( $instance['description'] ) ? $instance['description'] : '';
	$author_image_url		= isset( $instance['author_image_url'] ) ? $instance['author_image_url'] : '';
	?>

	<p>
		<label for="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>"><?php esc_html_e( 'Section Title:', 'sensational-blog' ); ?></label>
		<input class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'title' ) ); ?>" type="text" value="<?php echo esc_attr( $section_title ); ?>" />
	</p>
	<p>
		<label for="<?php echo esc_attr( $this->get_field_id( 'name' ) ); ?>"><?php esc_html_e( 'Author Name:', 'sensational-blog' ); ?></label>
		<input class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'name' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'name' ) ); ?>" type="text" value="<?php echo esc_attr( $author_name ); ?>" />
	</p>
	<p>
		<label for="<?php echo esc_attr( $this->get_field_id( 'description' ) ); ?>"><?php esc_html_e( 'Description :', 'sensational-blog' ); ?></label>
		<textarea class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'description' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'description' ) ); ?>" value="<?php echo esc_attr( $author_description ); ?>"><?php echo wp_kses_post( $author_description ); ?></textarea>
	</p>

	<p>
		<label for="<?php echo esc_attr( $this->get_field_id( 'author_image_url' ) ); ?>"><?php esc_html_e( 'Author Image URL', 'sensational-blog' ); ?></label>:<br />
		<input type="url" class="img widefat" name="<?php echo esc_attr( $this->get_field_name( 'author_image_url' ) ); ?>" id="<?php echo esc_attr( $this->get_field_id( 'author_image_url' ) ); ?>" value="<?php echo esc_url( $author_image_url ); ?>" /><br />
		<input type="button" class="select-img button button-primary" value="<?php esc_attr_e( 'Upload', 'sensational-blog' ); ?>" data-uploader_title="<?php esc_attr_e( 'Select Image', 'sensational-blog' ); ?>" data-uploader_button_text="<?php esc_attr_e( 'Choose Image', 'sensational-blog' ); ?>" style="margin-top:5px;" /><br/>
	</p>
	<?php
}

function update( $new_instance, $old_instance ) {

	$instance						= $old_instance;
	$instance['title']				= sanitize_text_field( $new_instance['title'] );
	$instance['name']				= sanitize_text_field( $new_instance['name'] );
	$instance['description']		= wp_kses_post( $new_instance['description'] );
	$instance['author_image_url']	= esc_url_raw( $new_instance['author_image_url'] );
	return $instance;
}

		/**
		* Front-end display of widget.
		**/
		function widget( $args, $instance ) {
			if ( ! isset( $args['widget_id'] ) ) {
				$args['widget_id'] = $this->id;
			}
			$section_title     			= isset( $instance['title'] ) ? esc_html( $instance['title'] ) : esc_html__( 'About Author', 'sensational-blog' );
    	$section_title 				= apply_filters( 'widget_title', $section_title, $instance, $this->id_base );
			$author_name			= ! empty( $instance['name'] ) ? $instance['name'] : '';
			$author_description		= ! empty( $instance['description'] ) ? $instance['description'] : '';
			$author_image_url		= ! empty( $instance['author_image_url'] ) ? $instance['author_image_url'] : '';
			echo $args['before_widget'];
			?>

			<article class="has-post-thumbnail">
				<?php if ( ! empty( $section_title ) ) { ?>
					<div class="widget-header">
						<?php echo $args['before_title'] . esc_html( $section_title ) . $args['after_title']; ?>
					</div>
				<?php } ?>
				
				<div class="entry-container">
					<?php if ( !empty( $author_image_url ) ) { ?>
					<div class="featured-image">
						<?php
						echo  '<img src="' . esc_url( $author_image_url ) . '" alt="' . esc_attr( $author_name  ) . '"  />';
						?>
					</div>
				<?php } ?>
					<h2 class="entry-title"><?php echo esc_html( $author_name ); ?></h2>
					<div class="widget-entry-content">
						<p><?php echo wp_kses_post( $author_description ); ?></p>
					</div>
				</div>
			</article>

		<?php
		echo $args['after_widget'];
		}
}
/**
* Enqueue admin scripts for Image Widget
* @since Sensational Blog 1.0
**/
function sensational_blog_author_image_upload_enqueue( $hook ) {

if( 'widgets.php' !== $hook )
return;

wp_enqueue_media();
wp_enqueue_script( 'author-info-widget-image-upload-script', get_template_directory_uri() . '/assets/js/upload.js', array( 'jquery' ), true );
}

add_action( 'admin_enqueue_scripts', 'sensational_blog_author_image_upload_enqueue' );