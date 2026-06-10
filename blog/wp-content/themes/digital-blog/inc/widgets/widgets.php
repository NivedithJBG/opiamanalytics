<?php

// Author Info Widget.
require get_template_directory() . '/inc/widgets/info-author-widget.php';

// Small List Widgets.
require get_template_directory() . '/inc/widgets/small-list-widget.php';

// Social Icons Widget.
require get_template_directory() . '/inc/widgets/social-icons-widget.php';

/**
 * Register Widgets
 */
function digital_blog_register_widgets() {

	register_widget( 'Digital_Blog_Author_Info_Widget' );

	register_widget( 'Digital_Blog_Small_List_Widget' );

	register_widget( 'Digital_Blog_Social_Icons_Widget' );

}
add_action( 'widgets_init', 'digital_blog_register_widgets' );
