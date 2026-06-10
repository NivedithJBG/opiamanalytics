<?php 
/**
 * List of posts for post choices.
 * @return Array Array of post ids and name.
 */
function sensational_blog_post_choices() {
    $posts = get_posts( array( 'numberposts' => -1 ) );
    $choices = array();
    $choices[0] = esc_html__( '--Select--', 'sensational-blog' );
    foreach ( $posts as $post ) {
        $choices[ $post->ID ] = $post->post_title;
    }
    return  $choices;
}

if ( ! function_exists( 'sensational_blog_switch_options' ) ) :
    /**
     * List of custom Switch Control options
     * @return array List of switch control options.
     */
    function sensational_blog_switch_options() {
        $arr = array(
            'on'        => esc_html__( 'Enable', 'sensational-blog' ),
            'off'       => esc_html__( 'Disable', 'sensational-blog' )
        );
        return apply_filters( 'sensational_blog_switch_options', $arr );
    }
endif;

/**
 * List of category for category choices.
 * @return Array Array of post ids and name.
 */
function sensational_blog_category_choices() {
    $tax_args = array(
        'hierarchical' => 0,
        'taxonomy'     => 'category',
    );
    $taxonomies = get_categories( $tax_args );
    $choices = array();
    $choices[0] = esc_html__( '--Select--', 'sensational-blog' );
    foreach ( $taxonomies as $tax ) {
        $choices[ $tax->term_id ] = $tax->name;
    }
    return  $choices;
}
if ( ! function_exists( 'sensational_blog_get_woo_product' ) ) {
    /**
     * Get product.
     */
    function sensational_blog_get_woo_product() {
        $args = array(
            'posts_per_page' => -1,
        );
         
        $choices = array( '' => esc_html__( '--Select--', 'sensational-blog' ) );
        $products = wc_get_products( $args );
        foreach ( $products as $product ) {
            $id = $product->get_id();
            $title = $product->get_name();
            $choices[ $id ] = $title;
        }
        return $choices;
    }
}




 /**
 * Get an array of google fonts.
 * 
 */
function sensational_blog_font_choices() {
    $font_family_arr = array();
    $font_family_arr[''] = esc_html__( '--Default--', 'sensational-blog' );

    // Make the request
    $request = wp_remote_get( get_theme_file_uri( 'assets/fonts/webfonts.json' ) );

    if( is_wp_error( $request ) ) {
        return false; // Bail early
    }
    // Retrieve the data
    $body = wp_remote_retrieve_body( $request );
    $data = json_decode( $body );
    if ( ! empty( $data ) ) {
        foreach ( $data->items as $items => $fonts ) {
            $family_str_arr = explode( ' ', $fonts->family );
            $family_value = implode( '-', array_map( 'strtolower', $family_str_arr ) );
            $font_family_arr[ $family_value ] = $fonts->family;
        }
    }

    return apply_filters( 'sensational_blog_font_choices', $font_family_arr );
}

if ( ! function_exists( 'sensational_blog_typography_options' ) ) :
    /**
     * Returns list of typography
     * @return array font styles
     */
    function sensational_blog_typography_options(){
        $choices = array(
            'default'         => esc_html__( 'Default', 'sensational-blog' ),
            'header-font-1'   => esc_html__( 'Raleway', 'sensational-blog' ),
            'header-font-2'   => esc_html__( 'Poppins', 'sensational-blog' ),
            'header-font-3'   => esc_html__( 'Montserrat', 'sensational-blog' ),
            'header-font-4'   => esc_html__( 'Open Sans', 'sensational-blog' ),
            'header-font-5'   => esc_html__( 'Lato', 'sensational-blog' ),
            'header-font-6'   => esc_html__( 'Ubuntu', 'sensational-blog' ),
            'header-font-7'   => esc_html__( 'Playfair Display', 'sensational-blog' ),
            'header-font-8'   => esc_html__( 'Lora', 'sensational-blog' ),
            'header-font-9'   => esc_html__( 'Titillium Web', 'sensational-blog' ),
            'header-font-10'   => esc_html__( 'Muli', 'sensational-blog' ),
            'header-font-11'   => esc_html__( 'Oxygen', 'sensational-blog' ),
            'header-font-12'   => esc_html__( 'Nunito Sans', 'sensational-blog' ),
            'header-font-13'   => esc_html__( 'Maven Pro', 'sensational-blog' ),
            'header-font-14'   => esc_html__( 'Cairo', 'sensational-blog' ),
            'header-font-15'   => esc_html__( 'Philosopher', 'sensational-blog' ),
            'header-font-16'   => esc_html__( 'Quicksand', 'sensational-blog' ),
            'header-font-17'   => esc_html__( 'Henny Penny', 'sensational-blog' ),
            'header-font-18'   => esc_html__( 'Orbitron' , 'sensational-blog' ),
            'header-font-19'   => esc_html__( 'Marck Script', 'sensational-blog' ),
            'header-font-20'   => esc_html__( 'Kaushan Script', 'sensational-blog' ),
            'header-font-21'   => esc_html__( 'Courgette', 'sensational-blog' ),
            'header-font-22'   => esc_html__( 'Rajdhani', 'sensational-blog' ),
            'header-font-23'   => esc_html__( 'Bad Script', 'sensational-blog' ),
            'header-font-24'   => esc_html__( 'Righteous', 'sensational-blog' ),
            'header-font-25'   => esc_html__( 'Dosis', 'sensational-blog' ),
            'header-font-26'   => esc_html__( 'Cinzel Decorative', 'sensational-blog' ),
            'header-font-27'   => esc_html__( 'Faster one', 'sensational-blog' ),
            'header-font-28'   => esc_html__( 'Tangerine', 'sensational-blog' ),
            'header-font-29'   => esc_html__( 'Fredericka the Great', 'sensational-blog' ),
            'header-font-30'   => esc_html__( 'Shadows Into Light', 'sensational-blog' ),
            'header-font-31'   => esc_html__( 'Gloria Hallelujah', 'sensational-blog' ),
        );

        $output = apply_filters( 'sensational_blog_typography_options', $choices );
        if ( ! empty( $output ) ) {
            ksort( $output );
        }

        return $output;
    }
endif;

if ( ! function_exists( 'sensational_blog_body_typography_options' ) ) :
    /**
     * Returns list of typography
     * @return array font styles
     */
    function sensational_blog_body_typography_options(){
        $choices = array(
            'default'         => esc_html__( 'Default', 'sensational-blog' ),
            'body-font-1'     => esc_html__( 'Raleway', 'sensational-blog' ),
            'body-font-2'     => esc_html__( 'Poppins', 'sensational-blog' ),
            'body-font-3'     => esc_html__( 'Roboto', 'sensational-blog' ),
            'body-font-4'     => esc_html__( 'Open Sans', 'sensational-blog' ),
            'body-font-5'     => esc_html__( 'Lato', 'sensational-blog' ),
            'body-font-6'   => esc_html__( 'Ubuntu', 'sensational-blog' ),
            'body-font-7'   => esc_html__( 'Playfair Display', 'sensational-blog' ),
            'body-font-8'   => esc_html__( 'Lora', 'sensational-blog' ),
            'body-font-9'   => esc_html__( 'Titillium Web', 'sensational-blog' ),
            'body-font-10'   => esc_html__( 'Muli', 'sensational-blog' ),
            'body-font-11'   => esc_html__( 'Oxygen', 'sensational-blog' ),
            'body-font-12'   => esc_html__( 'Nunito Sans', 'sensational-blog' ),
            'body-font-13'   => esc_html__( 'Maven Pro', 'sensational-blog' ),
            'body-font-14'   => esc_html__( 'Cairo', 'sensational-blog' ),
            'body-font-15'   => esc_html__( 'Philosopher', 'sensational-blog' ),
            'body-font-16'   => esc_html__( 'Quicksand', 'sensational-blog' ),
            'body-font-17'   => esc_html__( 'Dancing Script ', 'sensational-blog' ),
            'body-font-18'   => esc_html__( 'Rajdhani', 'sensational-blog' ),
        );

        $output = apply_filters( 'sensational_blog_body_typography_options', $choices );
        if ( ! empty( $output ) ) {
            ksort( $output );
        }

        return $output;
    }
endif;


if ( ! function_exists( 'sensational_blog_archive_typography_options' ) ) :
    /**
     * Returns list of typography
     * @return array font styles
     */
    function sensational_blog_archive_typography_options(){
        $choices = array(
            'default'         => esc_html__( 'Default', 'sensational-blog' ),
            'header-font-1'   => esc_html__( 'Raleway', 'sensational-blog' ),
            'header-font-2'   => esc_html__( 'Poppins', 'sensational-blog' ),
            'header-font-3'   => esc_html__( 'Montserrat', 'sensational-blog' ),
            'header-font-4'   => esc_html__( 'Open Sans', 'sensational-blog' ),
            'header-font-5'   => esc_html__( 'Lato', 'sensational-blog' ),
            'header-font-6'   => esc_html__( 'Ubuntu', 'sensational-blog' ),
            'header-font-7'   => esc_html__( 'Playfair Display', 'sensational-blog' ),
            'header-font-8'   => esc_html__( 'Lora', 'sensational-blog' ),
            'header-font-9'   => esc_html__( 'Titillium Web', 'sensational-blog' ),
            'header-font-10'   => esc_html__( 'Muli', 'sensational-blog' ),
            'header-font-11'   => esc_html__( 'Oxygen', 'sensational-blog' ),
            'header-font-12'   => esc_html__( 'Nunito Sans', 'sensational-blog' ),
            'header-font-13'   => esc_html__( 'Maven Pro', 'sensational-blog' ),
            'header-font-14'   => esc_html__( 'Cairo', 'sensational-blog' ),
            'header-font-15'   => esc_html__( 'Philosopher', 'sensational-blog' ),
            'header-font-16'   => esc_html__( 'Quicksand', 'sensational-blog' ),
            'header-font-17'   => esc_html__( 'Henny Penny', 'sensational-blog' ),
            'header-font-18'   => esc_html__( 'Orbitron' , 'sensational-blog' ),
            'header-font-19'   => esc_html__( 'Marck Script', 'sensational-blog' ),
            'header-font-20'   => esc_html__( 'Kaushan Script', 'sensational-blog' ),
            'header-font-21'   => esc_html__( 'Courgette', 'sensational-blog' ),
            'header-font-22'   => esc_html__( 'Rajdhani', 'sensational-blog' ),
            'header-font-23'   => esc_html__( 'Bad Script', 'sensational-blog' ),
            'header-font-24'   => esc_html__( 'Righteous', 'sensational-blog' ),
            'header-font-25'   => esc_html__( 'Dosis', 'sensational-blog' ),
            'header-font-26'   => esc_html__( 'Cinzel Decorative', 'sensational-blog' ),
            'header-font-27'   => esc_html__( 'Faster one', 'sensational-blog' ),
            'header-font-28'   => esc_html__( 'Tangerine', 'sensational-blog' ),
            'header-font-29'   => esc_html__( 'Fredericka the Great', 'sensational-blog' ),
            'header-font-30'   => esc_html__( 'Shadows Into Light', 'sensational-blog' ),
            'header-font-31'   => esc_html__( 'Gloria Hallelujah', 'sensational-blog' ),
        );

        $output = apply_filters( 'sensational_blog_archive_typography_options', $choices );
        if ( ! empty( $output ) ) {
            ksort( $output );
        }

        return $output;
    }
endif;

if ( ! function_exists( 'sensational_blog_archive_body_typography_options' ) ) :
    /**
     * Returns list of typography
     * @return array font styles
     */
    function sensational_blog_archive_body_typography_options(){
        $choices = array(
            'default'         => esc_html__( 'Default', 'sensational-blog' ),
            'body-font-1'     => esc_html__( 'Raleway', 'sensational-blog' ),
            'body-font-2'     => esc_html__( 'Poppins', 'sensational-blog' ),
            'body-font-3'     => esc_html__( 'Roboto', 'sensational-blog' ),
            'body-font-4'     => esc_html__( 'Open Sans', 'sensational-blog' ),
            'body-font-5'     => esc_html__( 'Lato', 'sensational-blog' ),
            'body-font-6'   => esc_html__( 'Ubuntu', 'sensational-blog' ),
            'body-font-7'   => esc_html__( 'Playfair Display', 'sensational-blog' ),
            'body-font-8'   => esc_html__( 'Lora', 'sensational-blog' ),
            'body-font-9'   => esc_html__( 'Titillium Web', 'sensational-blog' ),
            'body-font-10'   => esc_html__( 'Muli', 'sensational-blog' ),
            'body-font-11'   => esc_html__( 'Oxygen', 'sensational-blog' ),
            'body-font-12'   => esc_html__( 'Nunito Sans', 'sensational-blog' ),
            'body-font-13'   => esc_html__( 'Maven Pro', 'sensational-blog' ),
            'body-font-14'   => esc_html__( 'Cairo', 'sensational-blog' ),
            'body-font-15'   => esc_html__( 'Philosopher', 'sensational-blog' ),
            'body-font-16'   => esc_html__( 'Quicksand', 'sensational-blog' ),
            'body-font-17'   => esc_html__( 'Dancing Script ', 'sensational-blog' ),
            'body-font-18'   => esc_html__( 'Rajdhani', 'sensational-blog' ),
        );

        $output = apply_filters( 'sensational_blog_archive_body_typography_options', $choices );
        if ( ! empty( $output ) ) {
            ksort( $output );
        }

        return $output;
    }
endif;

if ( ! function_exists( 'sensational_blog_page_typography_options' ) ) :
    /**
     * Returns list of typography
     * @return array font styles
     */
    function sensational_blog_page_typography_options(){
        $choices = array(
            'default'         => esc_html__( 'Default', 'sensational-blog' ),
            'header-font-1'   => esc_html__( 'Raleway', 'sensational-blog' ),
            'header-font-2'   => esc_html__( 'Poppins', 'sensational-blog' ),
            'header-font-3'   => esc_html__( 'Montserrat', 'sensational-blog' ),
            'header-font-4'   => esc_html__( 'Open Sans', 'sensational-blog' ),
            'header-font-5'   => esc_html__( 'Lato', 'sensational-blog' ),
            'header-font-6'   => esc_html__( 'Ubuntu', 'sensational-blog' ),
            'header-font-7'   => esc_html__( 'Playfair Display', 'sensational-blog' ),
            'header-font-8'   => esc_html__( 'Lora', 'sensational-blog' ),
            'header-font-9'   => esc_html__( 'Titillium Web', 'sensational-blog' ),
            'header-font-10'   => esc_html__( 'Muli', 'sensational-blog' ),
            'header-font-11'   => esc_html__( 'Oxygen', 'sensational-blog' ),
            'header-font-12'   => esc_html__( 'Nunito Sans', 'sensational-blog' ),
            'header-font-13'   => esc_html__( 'Maven Pro', 'sensational-blog' ),
            'header-font-14'   => esc_html__( 'Cairo', 'sensational-blog' ),
            'header-font-15'   => esc_html__( 'Philosopher', 'sensational-blog' ),
            'header-font-16'   => esc_html__( 'Quicksand', 'sensational-blog' ),
            'header-font-17'   => esc_html__( 'Henny Penny', 'sensational-blog' ),
            'header-font-18'   => esc_html__( 'Orbitron' , 'sensational-blog' ),
            'header-font-19'   => esc_html__( 'Marck Script', 'sensational-blog' ),
            'header-font-20'   => esc_html__( 'Kaushan Script', 'sensational-blog' ),
            'header-font-21'   => esc_html__( 'Courgette', 'sensational-blog' ),
            'header-font-22'   => esc_html__( 'Rajdhani', 'sensational-blog' ),
            'header-font-23'   => esc_html__( 'Bad Script', 'sensational-blog' ),
            'header-font-24'   => esc_html__( 'Righteous', 'sensational-blog' ),
            'header-font-25'   => esc_html__( 'Dosis', 'sensational-blog' ),
            'header-font-26'   => esc_html__( 'Cinzel Decorative', 'sensational-blog' ),
            'header-font-27'   => esc_html__( 'Faster one', 'sensational-blog' ),
            'header-font-28'   => esc_html__( 'Tangerine', 'sensational-blog' ),
            'header-font-29'   => esc_html__( 'Fredericka the Great', 'sensational-blog' ),
            'header-font-30'   => esc_html__( 'Shadows Into Light', 'sensational-blog' ),
            'header-font-31'   => esc_html__( 'Gloria Hallelujah', 'sensational-blog' ),
        );

        $output = apply_filters( 'sensational_blog_page_typography_options', $choices );
        if ( ! empty( $output ) ) {
            ksort( $output );
        }

        return $output;
    }
endif;

if ( ! function_exists( 'sensational_blog_page_body_typography_options' ) ) :
    /**
     * Returns list of typography
     * @return array font styles
     */
    function sensational_blog_page_body_typography_options(){
        $choices = array(
            'default'         => esc_html__( 'Default', 'sensational-blog' ),
            'body-font-1'     => esc_html__( 'Raleway', 'sensational-blog' ),
            'body-font-2'     => esc_html__( 'Poppins', 'sensational-blog' ),
            'body-font-3'     => esc_html__( 'Roboto', 'sensational-blog' ),
            'body-font-4'     => esc_html__( 'Open Sans', 'sensational-blog' ),
            'body-font-5'     => esc_html__( 'Lato', 'sensational-blog' ),
            'body-font-6'   => esc_html__( 'Ubuntu', 'sensational-blog' ),
            'body-font-7'   => esc_html__( 'Playfair Display', 'sensational-blog' ),
            'body-font-8'   => esc_html__( 'Lora', 'sensational-blog' ),
            'body-font-9'   => esc_html__( 'Titillium Web', 'sensational-blog' ),
            'body-font-10'   => esc_html__( 'Muli', 'sensational-blog' ),
            'body-font-11'   => esc_html__( 'Oxygen', 'sensational-blog' ),
            'body-font-12'   => esc_html__( 'Nunito Sans', 'sensational-blog' ),
            'body-font-13'   => esc_html__( 'Maven Pro', 'sensational-blog' ),
            'body-font-14'   => esc_html__( 'Cairo', 'sensational-blog' ),
            'body-font-15'   => esc_html__( 'Philosopher', 'sensational-blog' ),
            'body-font-16'   => esc_html__( 'Quicksand', 'sensational-blog' ),
            'body-font-17'   => esc_html__( 'Dancing Script ', 'sensational-blog' ),
            'body-font-18'   => esc_html__( 'Rajdhani', 'sensational-blog' ),
        );

        $output = apply_filters( 'sensational_blog_page_body_typography_options', $choices );
        if ( ! empty( $output ) ) {
            ksort( $output );
        }

        return $output;
    }
endif;

if ( ! function_exists( 'sensational_blog_post_typography_options' ) ) :
    /**
     * Returns list of typography
     * @return array font styles
     */
    function sensational_blog_post_typography_options(){
        $choices = array(
            'default'         => esc_html__( 'Default', 'sensational-blog' ),
            'header-font-1'   => esc_html__( 'Raleway', 'sensational-blog' ),
            'header-font-2'   => esc_html__( 'Poppins', 'sensational-blog' ),
            'header-font-3'   => esc_html__( 'Montserrat', 'sensational-blog' ),
            'header-font-4'   => esc_html__( 'Open Sans', 'sensational-blog' ),
            'header-font-5'   => esc_html__( 'Lato', 'sensational-blog' ),
            'header-font-6'   => esc_html__( 'Ubuntu', 'sensational-blog' ),
            'header-font-7'   => esc_html__( 'Playfair Display', 'sensational-blog' ),
            'header-font-8'   => esc_html__( 'Lora', 'sensational-blog' ),
            'header-font-9'   => esc_html__( 'Titillium Web', 'sensational-blog' ),
            'header-font-10'   => esc_html__( 'Muli', 'sensational-blog' ),
            'header-font-11'   => esc_html__( 'Oxygen', 'sensational-blog' ),
            'header-font-12'   => esc_html__( 'Nunito Sans', 'sensational-blog' ),
            'header-font-13'   => esc_html__( 'Maven Pro', 'sensational-blog' ),
            'header-font-14'   => esc_html__( 'Cairo', 'sensational-blog' ),
            'header-font-15'   => esc_html__( 'Philosopher', 'sensational-blog' ),
            'header-font-16'   => esc_html__( 'Quicksand', 'sensational-blog' ),
            'header-font-17'   => esc_html__( 'Henny Penny', 'sensational-blog' ),
            'header-font-18'   => esc_html__( 'Orbitron' , 'sensational-blog' ),
            'header-font-19'   => esc_html__( 'Marck Script', 'sensational-blog' ),
            'header-font-20'   => esc_html__( 'Kaushan Script', 'sensational-blog' ),
            'header-font-21'   => esc_html__( 'Courgette', 'sensational-blog' ),
            'header-font-22'   => esc_html__( 'Rajdhani', 'sensational-blog' ),
            'header-font-23'   => esc_html__( 'Bad Script', 'sensational-blog' ),
            'header-font-24'   => esc_html__( 'Righteous', 'sensational-blog' ),
            'header-font-25'   => esc_html__( 'Dosis', 'sensational-blog' ),
            'header-font-26'   => esc_html__( 'Cinzel Decorative', 'sensational-blog' ),
            'header-font-27'   => esc_html__( 'Faster one', 'sensational-blog' ),
            'header-font-28'   => esc_html__( 'Tangerine', 'sensational-blog' ),
            'header-font-29'   => esc_html__( 'Fredericka the Great', 'sensational-blog' ),
            'header-font-30'   => esc_html__( 'Shadows Into Light', 'sensational-blog' ),
            'header-font-31'   => esc_html__( 'Gloria Hallelujah', 'sensational-blog' ),
        );

        $output = apply_filters( 'sensational_blog_post_typography_options', $choices );
        if ( ! empty( $output ) ) {
            ksort( $output );
        }

        return $output;
    }
endif;

if ( ! function_exists( 'sensational_blog_post_body_typography_options' ) ) :
    /**
     * Returns list of typography
     * @return array font styles
     */
    function sensational_blog_post_body_typography_options(){
        $choices = array(
            'default'         => esc_html__( 'Default', 'sensational-blog' ),
            'body-font-1'     => esc_html__( 'Raleway', 'sensational-blog' ),
            'body-font-2'     => esc_html__( 'Poppins', 'sensational-blog' ),
            'body-font-3'     => esc_html__( 'Roboto', 'sensational-blog' ),
            'body-font-4'     => esc_html__( 'Open Sans', 'sensational-blog' ),
            'body-font-5'     => esc_html__( 'Lato', 'sensational-blog' ),
            'body-font-6'   => esc_html__( 'Ubuntu', 'sensational-blog' ),
            'body-font-7'   => esc_html__( 'Playfair Display', 'sensational-blog' ),
            'body-font-8'   => esc_html__( 'Lora', 'sensational-blog' ),
            'body-font-9'   => esc_html__( 'Titillium Web', 'sensational-blog' ),
            'body-font-10'   => esc_html__( 'Muli', 'sensational-blog' ),
            'body-font-11'   => esc_html__( 'Oxygen', 'sensational-blog' ),
            'body-font-12'   => esc_html__( 'Nunito Sans', 'sensational-blog' ),
            'body-font-13'   => esc_html__( 'Maven Pro', 'sensational-blog' ),
            'body-font-14'   => esc_html__( 'Cairo', 'sensational-blog' ),
            'body-font-15'   => esc_html__( 'Philosopher', 'sensational-blog' ),
            'body-font-16'   => esc_html__( 'Quicksand', 'sensational-blog' ),
            'body-font-17'   => esc_html__( 'Dancing Script ', 'sensational-blog' ),
            'body-font-18'   => esc_html__( 'Rajdhani', 'sensational-blog' ),
        );

        $output = apply_filters( 'sensational_blog_post_body_typography_options', $choices );
        if ( ! empty( $output ) ) {
            ksort( $output );
        }

        return $output;
    }
endif;


if ( ! function_exists( 'sensational_blog_site_title_typography_options' ) ) :
    /**
     * Returns list of typography
     * @return array font styles
     */
    function sensational_blog_site_title_typography_options(){
        $choices = array(
            'default'         => esc_html__( 'Default', 'sensational-blog' ),
            'site-font-1'   => esc_html__( 'Raleway', 'sensational-blog' ),
            'site-font-2'   => esc_html__( 'Poppins', 'sensational-blog' ),
            'site-font-3'   => esc_html__( 'Montserrat', 'sensational-blog' ),
            'site-font-4'   => esc_html__( 'Open Sans', 'sensational-blog' ),
            'site-font-5'   => esc_html__( 'Lato', 'sensational-blog' ),
            'site-font-6'   => esc_html__( 'Ubuntu', 'sensational-blog' ),
            'site-font-7'   => esc_html__( 'Playfair Display', 'sensational-blog' ),
            'site-font-8'   => esc_html__( 'Lora', 'sensational-blog' ),
            'site-font-9'   => esc_html__( 'Titillium Web', 'sensational-blog' ),
            'site-font-10'   => esc_html__( 'Muli', 'sensational-blog' ),
            'site-font-11'   => esc_html__( 'Oxygen', 'sensational-blog' ),
            'site-font-12'   => esc_html__( 'Nunito Sans', 'sensational-blog' ),
            'site-font-13'   => esc_html__( 'Maven Pro', 'sensational-blog' ),
            'site-font-14'   => esc_html__( 'Cairo', 'sensational-blog' ),
            'site-font-15'   => esc_html__( 'Philosopher', 'sensational-blog' ),
            'site-font-16'   => esc_html__( 'Quicksand', 'sensational-blog' ),
            'site-font-17'   => esc_html__( 'Henny Penny', 'sensational-blog' ),
            'site-font-18'   => esc_html__( 'Orbitron' , 'sensational-blog' ),
            'site-font-19'   => esc_html__( 'Marck Script', 'sensational-blog' ),
            'site-font-20'   => esc_html__( 'Kaushan Script', 'sensational-blog' ),
            'site-font-21'   => esc_html__( 'Courgette', 'sensational-blog' ),
            'site-font-22'   => esc_html__( 'Rajdhani', 'sensational-blog' ),
            'site-font-23'   => esc_html__( 'Bad Script', 'sensational-blog' ),
            'site-font-24'   => esc_html__( 'Righteous', 'sensational-blog' ),
            'site-font-25'   => esc_html__( 'Dosis', 'sensational-blog' ),
            'site-font-26'   => esc_html__( 'Cinzel Decorative', 'sensational-blog' ),
            'site-font-27'   => esc_html__( 'Faster one', 'sensational-blog' ),
            'site-font-28'   => esc_html__( 'Tangerine', 'sensational-blog' ),
            'site-font-29'   => esc_html__( 'Fredericka the Great', 'sensational-blog' ),
            'site-font-30'   => esc_html__( 'Shadows Into Light', 'sensational-blog' ),
            'site-font-31'   => esc_html__( 'Gloria Hallelujah', 'sensational-blog' ),
        );

        $output = apply_filters( 'sensational_blog_site_title_typography_options', $choices );
        if ( ! empty( $output ) ) {
            ksort( $output );
        }

        return $output;
    }
endif;

if ( ! function_exists( 'sensational_blog_site_tagline_typography_options' ) ) :
    /**
     * Returns list of typography
     * @return array font styles
     */
    function sensational_blog_site_tagline_typography_options(){
        $choices = array(
            'default'         => esc_html__( 'Default', 'sensational-blog' ),
            'tagline-font-1'     => esc_html__( 'Raleway', 'sensational-blog' ),
            'tagline-font-2'     => esc_html__( 'Poppins', 'sensational-blog' ),
            'tagline-font-3'     => esc_html__( 'Roboto', 'sensational-blog' ),
            'tagline-font-4'     => esc_html__( 'Open Sans', 'sensational-blog' ),
            'tagline-font-5'     => esc_html__( 'Lato', 'sensational-blog' ),
            'tagline-font-6'   => esc_html__( 'Ubuntu', 'sensational-blog' ),
            'tagline-font-7'   => esc_html__( 'Playfair Display', 'sensational-blog' ),
            'tagline-font-8'   => esc_html__( 'Lora', 'sensational-blog' ),
            'tagline-font-9'   => esc_html__( 'Titillium Web', 'sensational-blog' ),
            'tagline-font-10'   => esc_html__( 'Muli', 'sensational-blog' ),
            'tagline-font-11'   => esc_html__( 'Oxygen', 'sensational-blog' ),
            'tagline-font-12'   => esc_html__( 'Nunito Sans', 'sensational-blog' ),
            'tagline-font-13'   => esc_html__( 'Maven Pro', 'sensational-blog' ),
            'tagline-font-14'   => esc_html__( 'Cairo', 'sensational-blog' ),
            'tagline-font-15'   => esc_html__( 'Philosopher', 'sensational-blog' ),
            'tagline-font-16'   => esc_html__( 'Quicksand', 'sensational-blog' ),
            'tagline-font-17'   => esc_html__( 'Dancing Script ', 'sensational-blog' ),
            'tagline-font-18'   => esc_html__( 'Rajdhani', 'sensational-blog' ),
        );

        $output = apply_filters( 'sensational_blog_site_tagline_typography_options', $choices );
        if ( ! empty( $output ) ) {
            ksort( $output );
        }

        return $output;
    }
endif;

 ?>