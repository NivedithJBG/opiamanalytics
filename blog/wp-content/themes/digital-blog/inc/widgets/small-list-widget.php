<?php
if ( ! class_exists( 'Digital_Blog_Small_List_Widget' ) ) {
	/**
	 * Adds Digital_Blog_Small_List_Widget Widget.
	 */
	class Digital_Blog_Small_List_Widget extends WP_Widget {


		/**
		 * Register widget with WordPress.
		 */
		public function __construct() {
			$digital_blog_small_list_posts_widget_ops = array(
				'classname'   => 'ascendoor-widget digital-blog-small-list-section',
				'description' => __( 'Retrive Small List Widgets', 'digital-blog' ),
			);
			parent::__construct(
				'digital_blog_small_list_widget',
				__( 'Ascendoor Small List Widget', 'digital-blog' ),
				$digital_blog_small_list_posts_widget_ops
			);
		}

		/**
		 * Front-end display of widget.
		 *
		 * @see WP_Widget::widget()
		 *
		 * @param array $args     Widget arguments.
		 * @param array $instance Saved values from database.
		 */
		public function widget( $args, $instance ) {
			if ( ! isset( $args['widget_id'] ) ) {
				$args['widget_id'] = $this->id;
			}
			$small_list_title    = ( ! empty( $instance['title'] ) ) ? ( $instance['title'] ) : '';
			$small_list_title    = apply_filters( 'widget_title', $small_list_title, $instance, $this->id_base );
			$small_list_offset   = isset( $instance['offset'] ) ? absint( $instance['offset'] ) : '';
			$small_list_category = isset( $instance['category'] ) ? absint( $instance['category'] ) : '';

			echo $args['before_widget'];
			?>
			<?php if ( ! empty( $small_list_title ) ) { ?>
				<div class="section-header">
					<?php
					echo $args['before_title'] . esc_html( $small_list_title ) . $args['after_title'];
					?>
				</div>
			<?php } ?>
			<div class="digital-blog-section-body">
				<div class="small-list-section-wrapper">
					<?php
					$small_list_widgets_args = array(
						'post_type'      => 'post',
						'posts_per_page' => absint( 4 ),
						'offset'         => absint( $small_list_offset ),
						'cat'            => absint( $small_list_category ),
					);

					$query = new WP_Query( $small_list_widgets_args );
					if ( $query->have_posts() ) :
						while ( $query->have_posts() ) :
							$query->the_post();
							?>
							<div class="blog-post-single <?php echo esc_attr( has_post_thumbnail() ? 'has-post-thumbnail' : '' ); ?> small-list-design">
								<?php if ( has_post_thumbnail() ) { ?>
									<div class="blog-img">
										<a class="post-thumbnail" href="<?php the_permalink(); ?>">
											<?php the_post_thumbnail(); ?>
										</a>
									</div>
									<div class="blog-detail">
									<?php } ?>	
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
									</div>
									<?php if ( has_post_thumbnail() ) { ?>	
									</div>
								<?php } ?>
							</div>
							<?php
						endwhile;
						wp_reset_postdata();
					endif;
					?>
				</div>
			</div>
			<?php
			echo $args['after_widget'];
		}

		/**
		 * Back-end widget form.
		 *
		 * @see WP_Widget::form()
		 *
		 * @param array $instance Previously saved values from database.
		 */
		public function form( $instance ) {
			$small_list_title    = isset( $instance['title'] ) ? ( $instance['title'] ) : '';
			$small_list_offset   = isset( $instance['offset'] ) ? absint( $instance['offset'] ) : '';
			$small_list_category = isset( $instance['category'] ) ? absint( $instance['category'] ) : '';
			?>
			<p>
				<label for="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>"><?php esc_html_e( 'Section Title:', 'digital-blog' ); ?></label>
				<input class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'title' ) ); ?>" type="text" value="<?php echo esc_attr( $small_list_title ); ?>" />
			</p>
			<p>
				<label for="<?php echo esc_attr( $this->get_field_id( 'offset' ) ); ?>"><?php esc_html_e( 'Number of posts to displace or pass over:', 'digital-blog' ); ?></label>
				<input class="tiny-text" id="<?php echo esc_attr( $this->get_field_id( 'offset' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'offset' ) ); ?>" type="number" step="1" min="0" value="<?php echo absint( $small_list_offset ); ?>" size="3" />
			</p>
			<p>
				<label for="<?php echo esc_attr( $this->get_field_id( 'category' ) ); ?>"><?php esc_html_e( 'Select the category to show posts:', 'digital-blog' ); ?></label>
				<select id="<?php echo esc_attr( $this->get_field_id( 'category' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'category' ) ); ?>" class="widefat" style="width:100%;">
					<?php
					$categories = digital_blog_get_post_cat_choices();
					foreach ( $categories as $category => $value ) {
						?>
						<option value="<?php echo absint( $category ); ?>" <?php selected( $small_list_category, $category ); ?>><?php echo esc_html( $value ); ?></option>
						<?php
					}
					?>
				</select>
			</p>
			<?php
		}

		/**
		 * Sanitize widget form values as they are saved.
		 *
		 * @see WP_Widget::update()
		 *
		 * @param array $new_instance Values just sent to be saved.
		 * @param array $old_instance Previously saved values from database.
		 *
		 * @return array Updated safe values to be saved.
		 */
		public function update( $new_instance, $old_instance ) {
			$instance             = $old_instance;
			$instance['title']    = sanitize_text_field( $new_instance['title'] );
			$instance['offset']   = (int) $new_instance['offset'];
			$instance['category'] = (int) $new_instance['category'];
			return $instance;
		}
	}
}
