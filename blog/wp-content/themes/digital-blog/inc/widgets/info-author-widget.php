<?php
if ( ! class_exists( 'Digital_Blog_Author_Info_Widget' ) ) {
	/**
	 * Adds Digital_Blog_Author_Info_Widget Widget.
	 */
	class Digital_Blog_Author_Info_Widget extends WP_Widget {

		/**
		 * Register widget with WordPress.
		 */
		public function __construct() {
			$digital_blog_author_info_widget_ops = array(
				'classname'   => 'ascendoor-widget author-info',
				'description' => __( 'Retrive Author Information Widget', 'digital-blog' ),
			);
			parent::__construct(
				'digital_blog_author_info_widget',
				__( 'Ascendoor Author Info Widget', 'digital-blog' ),
				$digital_blog_author_info_widget_ops
			);
		}

		/**
		 * Front-end display of widget.

		 * @see WP_Widget::widget()

		 * @param array $args     Widget arguments.
		 * @param array $instance Saved values from database.
		 */
		public function widget( $args, $instance ) {
			if ( ! isset( $args['widget_id'] ) ) {
				$args['widget_id'] = $this->id;
			}
			$section_title      = ! empty( $instance['title'] ) ? $instance['title'] : '';
			$section_title      = apply_filters( 'widget_title', $section_title, $instance, $this->id_base );
			$author_name        = ! empty( $instance['name'] ) ? $instance['name'] : '';
			$author_description = ! empty( $instance['description'] ) ? $instance['description'] : '';
			$author_image_url   = ! empty( $instance['author_image_url'] ) ? $instance['author_image_url'] : '';
			$open_link_new_tab  = ! empty( $instance['open_link_new_tab'] ) ? true : false;
			$target             = empty( $open_link_new_tab ) ? '' : 'target="_blank"';

			echo $args['before_widget'];
			if ( ! empty( $section_title ) ) {
				?>
				<div class="section-header">
						<?php echo $args['before_title'] . esc_html( $section_title ) . $args['after_title']; ?>
				</div>
				<?php
			}
			if ( ! empty( $author_image_url ) ) {
				$hasthumbnail = 'has-image';
			} else {
				$hasthumbnail = 'has-no-image';
			}
			?>
			<div class="digital-blog-section-body <?php echo esc_attr( $hasthumbnail ); ?>">
				<div class="author-img">
					<?php
					if ( ! empty( $author_image_url ) ) {
						$sizes = array();
						echo '<img src="' . esc_url( $author_image_url ) . '" alt="' . esc_attr( $author_name ) . '"  />';
					}
					?>
				</div>
				<div class="author-details">
					<h3 class="author-name"><?php echo esc_html( $author_name ); ?></h3>
					<div class="author-social-contacts">
						<?php
						for ( $i = 1; $i <= 4; $i++ ) {
							$link = ( ! empty( $instance[ 'link' . '-' . $i ] ) ) ? $instance[ 'link' . '-' . $i ] : '';
							if ( ! empty( $link ) ) :
								?>
							<a href="<?php echo esc_url( $link ) . '" ' . esc_attr( $target ); ?>"></a>
								<?php
						endif;
						}
						?>
					</div>
					<p class="author-description"><?php echo wp_kses_post( $author_description ); ?></p>
				</div>
			</div>
			<?php
			echo $args['after_widget'];
		}

		/**
		 * Back-end widget form.

		 * @see WP_Widget::form()

		 * @param array $instance Previously saved values from database.
		 */
		public function form( $instance ) {
			$section_title      = isset( $instance['title'] ) ? $instance['title'] : '';
			$author_name        = isset( $instance['name'] ) ? $instance['name'] : '';
			$author_description = isset( $instance['description'] ) ? $instance['description'] : '';
			$author_image_url   = isset( $instance['author_image_url'] ) ? $instance['author_image_url'] : '';
			$open_link_new_tab  = isset( $instance['open_link_new_tab'] ) ? $instance['open_link_new_tab'] : false;
			?>
			<p>
				<label for="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>"><?php esc_html_e( 'Section Title:', 'digital-blog' ); ?></label>
				<input class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'title' ) ); ?>" type="text" value="<?php echo esc_attr( $section_title ); ?>" />
			</p>
			<p>
				<label for="<?php echo esc_attr( $this->get_field_id( 'name' ) ); ?>"><?php esc_html_e( 'Author Name:', 'digital-blog' ); ?></label>
				<input class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'name' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'name' ) ); ?>" type="text" value="<?php echo esc_attr( $author_name ); ?>" />
			</p>
			<p>
				<label for="<?php echo esc_attr( $this->get_field_id( 'description' ) ); ?>"><?php esc_html_e( 'Description :', 'digital-blog' ); ?></label>
				<textarea class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'description' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'description' ) ); ?>" value="<?php echo esc_attr( $author_description ); ?>"><?php echo wp_kses_post( $author_description ); ?></textarea>
			</p>
			<p>
				<label for="<?php echo esc_attr( $this->get_field_id( 'author_image_url' ) ); ?>"><?php esc_html_e( 'Author Image URL', 'digital-blog' ); ?></label>:<br />
				<input type="url" class="img widefat" name="<?php echo esc_attr( $this->get_field_name( 'author_image_url' ) ); ?>" id="<?php echo esc_attr( $this->get_field_id( 'author_image_url' ) ); ?>" value="<?php echo esc_url( $author_image_url ); ?>" /><br />
				<input type="button" class="select-img" value="<?php esc_attr_e( 'Upload', 'digital-blog' ); ?>" data-uploader_title="<?php esc_attr_e( 'Select Image', 'digital-blog' ); ?>" data-uploader_button_text="<?php esc_attr_e( 'Choose Image', 'digital-blog' ); ?>" style="margin-top:5px;" /><br/>
			</p>
			<p>
				<label for="<?php echo esc_attr( $this->get_field_id( 'open_link_new_tab' ) ); ?>"><?php esc_html_e( 'Open Social link in New Tab', 'digital-blog' ); ?>:</label>
				<input type="checkbox" id="<?php echo esc_attr( $this->get_field_id( 'open_link_new_tab' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'open_link_new_tab' ), 'digital-blog' ); ?>"  <?php checked( $open_link_new_tab, true ); ?> />
			</p>
			<?php
			for ( $i = 1; $i <= 4; $i++ ) {
				$link = isset( $instance[ 'link' . '-' . $i ] ) ? $instance[ 'link' . '-' . $i ] : '';
				?>
			<p>
				<label for="<?php echo esc_attr( $this->get_field_id( 'link' . '-' . $i ) ); ?>"><?php sprintf( esc_html__( 'Link %s :', 'digital-blog' ), $i ); ?></label>
				<input type="url" class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'link' . '-' . $i ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'link' . '-' . $i ) ); ?>" value="<?php echo esc_url( $link ); ?>"/>
			</p>
			<?php } ?>

			<?php
		}

		/**
		 * Sanitize widget form values as they are saved.

		 * @see WP_Widget::update()

		 * @param array $new_instance Values just sent to be saved.
		 * @param array $old_instance Previously saved values from database.

		 * @return array Updated safe values to be saved.
		 */
		public function update( $new_instance, $old_instance ) {

			$instance                      = $old_instance;
			$instance['title']             = sanitize_text_field( $new_instance['title'] );
			$instance['name']              = sanitize_text_field( $new_instance['name'] );
			$instance['description']       = wp_kses_post( $new_instance['description'] );
			$instance['author_image_url']  = esc_url_raw( $new_instance['author_image_url'] );
			$instance['open_link_new_tab'] = digital_blog_sanitize_checkbox( $new_instance['open_link_new_tab'] );
			for ( $i = 1; $i <= 4; $i++ ) {
				$instance[ 'link' . '-' . $i ] = esc_url_raw( $new_instance[ 'link' . '-' . $i ] );
			}

			return $instance;
		}

	}
}

/**
 * Enqueue admin scripts for Image Widget
 *
 * @since Digital Blog 1.0
 **/
function digital_blog_author_info_widget_image_upload_enqueue( $hook ) {

	if ( 'widgets.php' !== $hook ) {
		return;
	}

	wp_enqueue_media();
	wp_enqueue_script( 'author-info-widget-image-upload-script', get_template_directory_uri() . '/assets/js/upload.min.js', array( 'jquery' ), DIGITAL_BLOG_VERSION, true );
}

add_action( 'admin_enqueue_scripts', 'digital_blog_author_info_widget_image_upload_enqueue' );
