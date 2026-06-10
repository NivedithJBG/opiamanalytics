<?php
/**
 * Custom theme functions.
 *
 * This file contains hook functions attached to theme hooks.
 *
 * @package Sensational Blog
 */

if( ! function_exists( 'sensational_blog_site_branding' ) ) :
	/**
  	 * Site Branding
  	 *
  	 * @since 1.0.0
  	 */
function sensational_blog_site_branding() { ?>
    <?php $enable_header_background = sensational_blog_get_option('disable_header_background_section');
    $header_background_image = sensational_blog_get_option('header_background_image'); 
    $show_social  = sensational_blog_get_option( 'show_header_social_links' ); 
    $show_menu_social  = sensational_blog_get_option( 'show_menu_social_links' );
    $header_social_link =sensational_blog_get_option('header_social_link');
    $show_header_search =sensational_blog_get_option('show_header_search');
    $homelayout     = sensational_blog_get_option( 'homepage_design_layout_options' ); 
    $headerlayout= sensational_blog_get_option('header_layout_options');
    $header_ads_image =sensational_blog_get_option('header_ads_image');
    $header_ads_image_url =sensational_blog_get_option('header_ads_image_url'); 
    $show_header_contact = sensational_blog_get_option( 'enable_header_contact_info' );
    $location_address     = sensational_blog_get_option( 'header_location_address' );
    $email_address        = sensational_blog_get_option( 'header_email' );
    $phone_contact        = sensational_blog_get_option( 'header_phone_contact' );
    $location_text     = sensational_blog_get_option( 'header_location_text' );
    $email_text        = sensational_blog_get_option( 'header_email_text' );
    $phone_contact_text        = sensational_blog_get_option( 'header_phone_text' ); ?>
    
    <?php if ($headerlayout == 'modern-menu') { ?>
        <div class="site-menu" <?php if ($enable_header_background == true ) { ?> style="background-image: url('<?php echo esc_url($header_background_image) ?>');" <?php } ?> >
            <div class="overlay"></div>
            <div class="wrapper">
                <nav id="site-navigation" class="main-navigation" role="navigation" aria-label="<?php echo esc_attr__('Primary Menu', 'sensational-blog'); ?>">
                    <div class="header-menu-wrapper">
                        <button type="button" class="menu-toggle">
                            <span class="icon-bar"></span>
                            <span class="icon-bar close-bar"></span>
                            <span class="icon-bar"></span>
                        </button>

                        <?php
                        wp_nav_menu( array(
                            'theme_location' => 'primary',
                            'menu_id'        => 'primary-menu',
                            'menu_class'     => 'nav-menu',
                            'fallback_cb'    => 'sensational_blog_primary_navigation_fallback',
                        ) );
                        ?>
                    </div>
                </nav><!-- #site-navigation -->
                <div class="header-logo-ads">
                    <div class="site-branding" >
                        <div class="site-logo">
                            <?php if(has_custom_logo()):?>
                                <?php the_custom_logo();?>
                            <?php endif;?>
                        </div><!-- .site-logo -->

                        <div id="site-identity">
                            <h1 class="site-title">
                                <a href="<?php echo esc_url( home_url( '/' ) ); ?>" rel="home">  <?php bloginfo( 'name' ); ?></a>
                            </h1>

                            <?php 
                                $description = get_bloginfo( 'description', 'display' );
                                if ( $description || is_customize_preview() ) : ?>
                                <p class="site-description"><?php echo esc_html($description);?></p>
                            <?php endif; ?>
                        </div><!-- #site-identity -->
                    </div> <!-- .site-branding -->
                </div>
                
                <?php if ( $show_menu_social==true){ ?>
                   <div class="widget widget_social_icons">
                       <ul class="social-icons">
                            <?php 
                            $header_social_links = ! empty( $header_social_link ) ? explode( '|', ( $header_social_link ) ) : array();
                            foreach ($header_social_links as $social_links ) { 
                                if ( isset( $social_links ) ) { 
                                ?>
                                    <li><a href=" <?php echo esc_url($social_links); ?>"></a></li>
                                <?php }  
                            } ?>
                        </ul> 
                    </div><!-- .widget_social_icons -->
                <?php } ?>
            </div>
        </div><!-- .site-menu -->
    <?php } elseif ($headerlayout == 'header-nine') { ?>
        <div class="site-menu" <?php if ($enable_header_background == true ) { ?> style="background-image: url('<?php echo esc_url($header_background_image) ?>');" <?php } ?> >
            <div class="wrapper">
                <div class="header-logo-ads">
                    <div class="site-branding" >
                        <div class="site-logo">
                            <?php if(has_custom_logo()):?>
                                <?php the_custom_logo();?>
                            <?php endif;?>
                        </div><!-- .site-logo -->

                        <div id="site-identity">
                            <h1 class="site-title">
                                <a href="<?php echo esc_url( home_url( '/' ) ); ?>" rel="home">  <?php bloginfo( 'name' ); ?></a>
                            </h1>

                            <?php 
                                $description = get_bloginfo( 'description', 'display' );
                                if ( $description || is_customize_preview() ) : ?>
                                <p class="site-description"><?php echo esc_html($description);?></p>
                            <?php endif; ?>
                        </div><!-- #site-identity -->
                    </div> <!-- .site-branding -->
                </div>
                <nav id="site-navigation" class="main-navigation" role="navigation" aria-label="<?php echo esc_attr__('Primary Menu', 'sensational-blog'); ?>">
                    <div class="header-menu-wrapper">
                        <button type="button" class="menu-toggle">
                            <span class="icon-bar"></span>
                            <span class="icon-bar close-bar"></span>
                            <span class="icon-bar"></span>
                        </button>

                        <?php
                        wp_nav_menu( array(
                            'theme_location' => 'primary',
                            'menu_id'        => 'primary-menu',
                            'menu_class'     => 'nav-menu',
                            'fallback_cb'    => 'sensational_blog_primary_navigation_fallback',
                        ) );
                        ?>
                    </div>
                </nav><!-- #site-navigation -->
            </div>
        </div><!-- .site-menu -->
    <?php } else { ?>
        <div class="site-menu" <?php if ($enable_header_background == true ) { ?> style="background-image: url('<?php echo esc_url($header_background_image) ?>');" <?php } ?> >
            <div class="overlay"></div>
            <div class="wrapper">
                <div class="header-logo-ads">
                    <div class="site-branding" >
                        <div class="site-logo">
                            <?php if(has_custom_logo()):?>
                                <?php the_custom_logo();?>
                            <?php endif;?>
                        </div><!-- .site-logo -->

                        <div id="site-identity">
                            <h1 class="site-title">
                                <a href="<?php echo esc_url( home_url( '/' ) ); ?>" rel="home">  <?php bloginfo( 'name' ); ?></a>
                            </h1>

                            <?php 
                                $description = get_bloginfo( 'description', 'display' );
                                if ( $description || is_customize_preview() ) : ?>
                                <p class="site-description"><?php echo esc_html($description);?></p>
                            <?php endif; ?>
                        </div><!-- #site-identity -->
                    </div> <!-- .site-branding -->
                    <?php if ( !empty($header_ads_image) || ( ( ! empty( $email_address ) || ! empty( $phone_contact ) || ! empty( $location ) ) && $show_header_contact==true ) ) { ?>
                        <?php if (!empty($header_ads_image)): ?>
                            <div class="header_ads">
                               <a class="_blank" href="<?php echo esc_url($header_ads_image_url); ?>"><img src="<?php echo esc_url($header_ads_image) ?>"></a>
                            </div><!-- .widget_ads -->
                        <?php endif ?>
                        <?php if ( ( ! empty( $email_address ) || ! empty( $phone_contact ) || ! empty( $location_address ) ) && $show_header_contact==true ): ?>
                            <div class="widget widget_address_block">
                                <ul> 
                                    <?php if( ! empty( $location_address ) ){ ?>
                                        <li>
                                            <i class="fa fa-map-marker"></i>
                                            <div class="header-contact-info">
                                                <h5><?php echo esc_html( $location_text ); ?></h5>
                                                <span><?php echo esc_html( $location_address ); ?></span>
                                            </div>
                                        </li>
                                    <?php } ?>
                                    <?php if( ! empty( $phone_contact ) ){ ?>
                                        <li>
                                            <i class="fa fa-phone"></i>
                                            <div class="header-contact-info">
                                                <h5><?php echo esc_html( $phone_contact_text ); ?></h5>
                                                <span><a href="tel: <?php echo esc_attr( $phone_contact ) ?>"><?php echo esc_html( $phone_contact ) ?></a></span>
                                            </div>
                                        </li>
                                    <?php } ?>
                                    <?php if( ! empty( $email_address) ){ ?>
                                        <li>
                                            <i class="fa fa-envelope"></i>
                                            <div class="header-contact-info">
                                                <h5><?php echo esc_html( $email_text ); ?></h5>
                                                <span><a href=" <?php echo esc_url('mailto:' . sanitize_email($email_address)) ?>"><?php echo esc_html( $email_address) ?></a></span>
                                            </div>
                                        </li>
                                    <?php } ?>
                                </ul>
                            </div><!-- .widget_address_block -->
                        <?php endif ?>
                    <?php } ?>
                </div>
              <nav id="site-navigation" class="main-navigation" role="navigation" aria-label="<?php echo esc_attr__('Primary Menu', 'sensational-blog'); ?>">
                <style type="text/css">
                    <?php  
                        $no_of_menu_items =sensational_blog_get_option( 'number_of_menu_items'); 
                        for ($i=0; $i <= $no_of_menu_items ; $i++) {
                            $menu_item_color =sensational_blog_get_option( 'menu_item_font_color_'.$i ); ?>
                        .kids-menu .main-navigation ul#primary-menu > li.fa:nth-child(<?php echo esc_attr($i) ?>):after,
                        .kids-menu .main-navigation ul#primary-menu > li.far:nth-child(<?php echo esc_attr($i) ?>):after,
                        .kids-menu .main-navigation ul#primary-menu > li.fab:nth-child(<?php echo esc_attr($i) ?>):after,
                        .kids-menu .main-navigation ul#primary-menu > li.fas:nth-child(<?php echo esc_attr($i) ?>):after {
                            border-color: <?php echo esc_attr($menu_item_color) ?>;
                        }
                        .kids-menu .main-navigation ul > li:nth-child(<?php echo esc_attr($i) ?>) ul li:hover > a,
                        .kids-menu .main-navigation ul > li:nth-child(<?php echo esc_attr($i) ?>) ul li.focus > a{
                            background-color: <?php echo esc_attr($menu_item_color) ?>;
                        }
                        .kids-menu .main-navigation ul#primary-menu > li:nth-child(<?php echo esc_attr($i) ?>):before {
                            background-color: <?php echo esc_attr($menu_item_color) ?>;
                            border-color: <?php echo esc_attr($menu_item_color) ?>;
                        }
                        .kids-menu .main-navigation ul#primary-menu > li:nth-child(<?php echo esc_attr($i) ?>):hover:before,
                        .kids-menu .main-navigation ul#primary-menu > li:nth-child(<?php echo esc_attr($i) ?>):focus:before,
                        .kids-menu .main-navigation ul#primary-menu > li:nth-child(<?php echo esc_attr($i) ?>):hover > a,
                        .kids-menu .main-navigation ul#primary-menu > li:nth-child(<?php echo esc_attr($i) ?>).focus > a {
                            color: <?php echo esc_attr($menu_item_color) ?>;
                        } 
                        .kids-menu .main-navigation ul#primary-menu > li:nth-child(<?php echo esc_attr($i) ?>):hover:before {
                            background-color: transparent;
                        }
                    <?php } ?>
                </style>
                <div class="header-menu-wrapper">
                    <?php if (($homelayout=='home-normal-magazine' || $homelayout=='home-magazine') && ($headerlayout=='header-two'||$headerlayout=='header-three')): ?>
                        <div class="home-icon"><a href="<?php echo esc_url(home_url('/')); ?>" rel="home"><i
                        class="fa fa-home"></i></a></div>
                    <?php endif; ?>
                        <button type="button" class="menu-toggle">
                            <span class="icon-bar"></span>
                            <span class="icon-bar close-bar"></span>
                            <span class="icon-bar"></span>
                        </button>

                        <?php
                        wp_nav_menu( array(
                            'theme_location' => 'primary',
                            'menu_id'        => 'primary-menu',
                            'menu_class'     => 'nav-menu',
                            'fallback_cb'    => 'sensational_blog_primary_navigation_fallback',
                        ) );
                        ?>
                </div>
              </nav><!-- #site-navigation -->
            </div>
        </div><!-- .site-menu -->
    <?php } ?>
<?php }
endif;
add_action( 'sensational_blog_action_header', 'sensational_blog_site_branding', 10 );

if ( ! function_exists( 'sensational_blog_subscription_section' ) ) :

  /**
   * Top  Footer 
   *
   * @since 1.0.0
   */
function sensational_blog_subscription_section() {
    $disable_subscription_section = sensational_blog_get_option( 'disable_subscription_section' );
    $subscription_bg_image = sensational_blog_get_option( 'subscription_background_image' );
    if( true ==$disable_subscription_section): ?>
        <section id="subscription" class="relative page-section">
            <div class="wrapper">
                <?php get_template_part( 'inc/sections/section-subscription' ); ?>
            </div>
        </section>
    <?php endif; 

 }
endif;

add_action( 'sensational_blog_action_subscription', 'sensational_blog_subscription_section', 10 );

if ( ! function_exists( 'sensational_blog_footer_top_section' ) ) :

  /**
   * Top  Footer 
   *
   * @since 1.0.0
   */
  function sensational_blog_footer_top_section() {
      $footer_sidebar_data = sensational_blog_footer_sidebar_class();
      $footer_sidebar    = $footer_sidebar_data['active_sidebar'];
      $footer_class      = $footer_sidebar_data['class'];
      $enable_footer_background   = sensational_blog_get_option( 'enable_footer_background_image' );
      $footer_background     = sensational_blog_get_option( 'footer_background_image' );
      if ( empty( $footer_sidebar ) ) {
        return;
      }      ?>
      <div class="footer-widgets-area page-section <?php echo esc_attr( $footer_class ); ?>" > <!-- widget area starting from here -->
            <div class="wrapper">
                <?php
                  for ( $i = 1; $i <= 4 ; $i++ ) {
                    if ( is_active_sidebar( 'footer-' . $i ) ) {
                    ?>
                      <div class="hentry">
                        <?php dynamic_sidebar( 'footer-' . $i ); ?>
                      </div>
                      <?php
                    }
                  }
                ?>
            </div>
      </div> <!-- widget area starting from here -->
    <?php
 }
endif;
add_action( 'sensational_blog_action_footer', 'sensational_blog_footer_top_section', 10 );

if ( ! function_exists( 'sensational_blog_subscription_section' ) ) :

  /**
   * Top  Footer 
   *
   * @since 1.0.0
   */
  function sensational_blog_subscription_section() {
     $disable_subscription_section = sensational_blog_get_option( 'disable_subscription_section' );
           if( true ==$disable_subscription_section): ?>
              <section id="subscription" class="relative page-section">
                <div class="wrapper">
                    <?php get_template_part( 'inc/sections/section-subscription' ) ; ?>
                </div>
              </section>
      <?php endif; 

 }
endif;

add_action( 'sensational_blog_action_subscription', 'sensational_blog_subscription_section', 10 );

if ( ! function_exists( 'sensational_blog_footer_section' ) ) :

  /**
   * Footer copyright
   *
   * @since 1.0.0
   */

  function sensational_blog_footer_section() { ?>
        <div class="site-info">
            <?php 
                $copyright_footer = sensational_blog_get_option('copyright_text'); 
                $powered_by_footer = sensational_blog_get_option('powered_by_text'); 
                if ( ! empty( $copyright_footer ) ) {
                  $copyright_footer = wp_kses_data( $copyright_footer );
                }
                // Powered by content.
                if ( ! empty( $powered_by_footer ) ) {
                  $powered_by_text = wp_kses_post( $powered_by_footer);
                }
            ?>
            <div class="wrapper">
                <span class="copy-right"><?php echo esc_html($copyright_footer);?><?php echo esc_html( $powered_by_text);?></span>
            </div> 
        </div> <!-- site generator ends here -->
        
    <?php }

endif;
add_action( 'sensational_blog_action_footer', 'sensational_blog_footer_section', 20 );

if ( ! function_exists( 'sensational_blog_footer_sidebar_class' ) ) :
  /**
   * Count the number of footer sidebars to enable dynamic classes for the footer
   *
   * @since Sensational Blog 0.1
   */
  function sensational_blog_footer_sidebar_class() {
    $data = array();
    $active_sidebar = array();
      $count = 0;

      if ( is_active_sidebar( 'footer-1' ) ) {
        $active_sidebar[]   = 'footer-1';
          $count++;
      }

      if ( is_active_sidebar( 'footer-2' ) ){
        $active_sidebar[]   = 'footer-2';
          $count++;
    }

      if ( is_active_sidebar( 'footer-3' ) ){
        $active_sidebar[]   = 'footer-3';
          $count++;
      }

      if ( is_active_sidebar( 'footer-4' ) ){
        $active_sidebar[]   = 'footer-4';
          $count++;
      }

      $class = '';

      switch ( $count ) {
          case '1':
            $class = 'col-1';
            break;
          case '2':
            $class = 'col-2';
            break;
          case '3':
            $class = 'col-3';
            break;
            case '4':
            $class = 'col-4';
            break;
      }

    $data['active_sidebar'] = $active_sidebar;
    $data['class']        = $class;

      return $data;
  }
endif;

if ( ! function_exists( 'sensational_blog_excerpt_length' ) ) :

  /**
   * Implement excerpt length.
   *
   * @since 1.0.0
   *
   * @param int $length The number of words.
   * @return int Excerpt length.
   */
  function sensational_blog_excerpt_length( $length ) {

    if ( is_admin() ) {
      return $length;
    }

    $excerpt_length = sensational_blog_get_option( 'excerpt_length' );

    if ( absint( $excerpt_length ) > 0 ) {
      $length = absint( $excerpt_length );
    }

    return $length;
  }

endif;

add_filter( 'excerpt_length', 'sensational_blog_excerpt_length', 999 );

if( ! function_exists( 'sensational_blog_banner_header
  ' ) ) :
    /**
     * Page Header
    */
    function sensational_blog_banner_header() { 

        if ( is_front_page() && is_home() ){ 
            $header_image = get_header_image();
            $header_image_url = ! empty( $header_image ) ?  $header_image : get_template_directory_uri() . '/assets/images/default-header.jpg';
        }
        elseif( is_front_page() ) {
            return;
        }
        else {
            $header_image_url = sensational_blog_banner_image( $image_url = '' );
        } ?>
        <div id="page-site-header" style="background-image: url('<?php echo esc_url( $header_image_url ); ?>');">
            <div class="overlay"></div>
            <header class='page-header'> 
                <div class="wrapper">
                    <?php sensational_blog_banner_title();?>
                </div><!-- .wrapper -->
            </header>
        </div><!-- #page-site-header -->
        <?php echo '<div class= "page-section">'; ?>
    <?php }
endif;
add_action( 'sensational_blog_banner_header', 'sensational_blog_banner_header', 10 );

if( ! function_exists( 'sensational_blog_banner_title' ) ) :
/**
 * Page Header
*/
function sensational_blog_banner_title(){ 
  $latest_posts_title = sensational_blog_get_option( 'latest_posts_title' );
  $single_post_title = sensational_blog_get_option( 'single_post_header_title_enable' ); 
  $single_page_title = sensational_blog_get_option( 'single_page_header_title_enable' );
  $blog_post_title_enable = sensational_blog_get_option( 'blog_post_header_title_enable' );
  $archive_post_title = sensational_blog_get_option( 'archive_post_header_title_enable' );
    if ( (( is_front_page() && is_home() ) || is_home() ) && !empty($latest_posts_title && $blog_post_title_enable==true) ){ ?>
        <h2 class="page-title"><?php echo esc_html($latest_posts_title); ?></h2><?php
    }

    if( is_single() && $single_post_title==true) {
        the_title( '<h2 class="page-title">', '</h2>' );
    }
    if( is_page() && $single_page_title==true) {
        the_title( '<h2 class="page-title">', '</h2>' );
    }       
    if( is_archive() && $archive_post_title==true ){
        the_archive_description( '<div class="archive-description">', '</div>' );
        the_archive_title( '<h2 class="page-title">', '</h2>' );
    }

    if( is_search() ){ ?>
        <h2 class="page-title"><?php printf( esc_html__( 'Search Results for: %s', 'sensational-blog' ), '<span>' . get_search_query() . '</span>' ); ?></h2>
    <?php }
    
    if( is_404() ) {
        echo '<h2 class="page-title">' . esc_html__( 'Error 404', 'sensational-blog' ) . '</h2>';
    }
}
endif;

if( ! function_exists( 'sensational_blog_banner_image' ) ) :
/**
 * Banner Header Image
*/
function sensational_blog_banner_image( $image_url ){
    global $post;

    $search_header = sensational_blog_get_option( 'search_header_image' );
    $header_404 = sensational_blog_get_option( '404_header_image' );
     $post_header_image_condition = sensational_blog_get_option( 'single_post_header_image_as_header_image_enable' );
    $page_header_image_condition = sensational_blog_get_option( 'single_page_header_image_as_header_image_enable' );

    if ( is_home() && ! is_front_page() ){ 
        $image_url      = get_the_post_thumbnail_url( get_option( 'page_for_posts' ), 'full' );
        $header_image = get_header_image();
        $fallback_image = ! empty( $header_image ) ?  $header_image : get_template_directory_uri() . '/assets/images/default-header.jpg';
        $image_url      = ( ! empty( $image_url) ) ? $image_url : $fallback_image;
    }
    elseif( is_single() ){
        $image_url      = get_the_post_thumbnail_url( $post->ID, 'full' );
        $header_image = get_header_image();
        $fallback_image = ! empty( $header_image ) ?  $header_image : get_template_directory_uri() . '/assets/images/default-header.jpg';
        $image_url      = ( ! empty( $image_url) && $post_header_image_condition==false ) ? $image_url : $fallback_image;
    }
    elseif( is_page() ){
        $image_url      = get_the_post_thumbnail_url( $post->ID, 'full' );
        $header_image = get_header_image();
        $fallback_image = ! empty( $header_image ) ?  $header_image : get_template_directory_uri() . '/assets/images/default-header.jpg';
        $image_url      = ( ! empty( $image_url) && $page_header_image_condition==false ) ? $image_url : $fallback_image;
    }
    elseif( is_archive() ){
         if (function_exists('z_taxonomy_image_url') && !empty(z_taxonomy_image_url())){
              $archive_header = z_taxonomy_image_url(); 
            } else{
                $archive_header = get_header_image();
            }
        $image_url = ( ! empty( $archive_header) ) ? $archive_header : get_template_directory_uri() . '/assets/images/default-header.jpg';
    }
    elseif( is_search() ){ 
        $image_url = ( ! empty( $search_header) ) ? $search_header : get_template_directory_uri() . '/assets/images/default-header.jpg';
    }
    elseif( is_404() ) {
        $image_url = ( ! empty( $header_404) ) ? $header_404 : get_template_directory_uri() . '/assets/images/default-header.jpg';
    }
    return $image_url;
}
endif;

if ( ! function_exists( 'sensational_blog_preloader' ) ) :
    /**
     * Prints HTML with meta information for the current post-date/time and author.
     */
    function sensational_blog_preloader() { 
        $loader_options = sensational_blog_get_option( 'preloader_loader_options' );?>
        <?php if ($loader_options=='loader-1'): ?>
            <div class="middle">
              <div class="bar bar1"></div>
              <div class="bar bar2"></div>
              <div class="bar bar3"></div>
              <div class="bar bar4"></div>
              <div class="bar bar5"></div>
              <div class="bar bar6"></div>
              <div class="bar bar7"></div>
              <div class="bar bar8"></div>
            </div>
        <?php endif; ?>
        <?php if ($loader_options=='loader-2'): ?>
            <div class="loader2">
              <p class="loader2">l</p>
              <p class="loader2">o</p>
              <p class="loader2">a</p>
              <p class="loader2">d</p>
              <p class="loader2">i</p>
              <p class="loader2">n</p>
              <p class="loader2">g</p>
            </div>
        <?php endif; ?>
    <?php }
endif;

if ( ! function_exists( 'sensational_blog_posts_tags' ) ) :
    /**
     * Prints HTML with meta information for the current post-date/time and author.
     */
    function sensational_blog_posts_tags() {
        // Hide category and tag text for pages.
        if ( 'post' === get_post_type() && has_tag() ) { ?>
                <div class="tags-links">

                    <?php /* translators: used between list items, there is a space after the comma */
                    $tags = get_the_tags();
                    if ( $tags ) {

                        foreach ( $tags as $tag ) {
                            echo '<span><a href="' . esc_url( get_tag_link( $tag->term_id ) ) .'">' . esc_html( $tag->name ) . '</a></span>'; // WPCS: XSS OK.
                        }
                    } ?>
                </div><!-- .tags-links -->
        <?php } 
    }
endif;

/**
 * Render social links.
 *
 * @since 1.0
 */
function sensational_blog_render_social_links() {

        $social_link1 = sensational_blog_get_option( 'social_link_1' );
        $social_link2 = sensational_blog_get_option( 'social_link_2' );
        $social_link3 = sensational_blog_get_option( 'social_link_3' );
        
        if ( empty( $social_link1 ) && empty( $social_link2 ) && empty( $social_link3 ) && empty( $social_link4 ) && empty( $social_link5 ) ) {
                return;
        }

        echo '<div class="social-icons">';
        echo '<ul>';
        if ( ! empty( $social_link1 ) ) {
            echo '<li><a href="' . esc_url( $social_link1 ) . '"></a></li>';
        }
        if ( ! empty( $social_link2 ) ) {
            echo '<li><a href="' . esc_url( $social_link2 ) . '"></a></li>';
        }
        if ( ! empty( $social_link3 ) ) {
            echo '<li><a href="' . esc_url( $social_link3 ) . '"></a></li>';
        }
        echo '</ul>';
        echo '</div>';
}
if ( ! function_exists( 'sensational_blog_sortable_sections' ) ) :
    /**
     * List of sections Control options
     * @return array List of Sections control options.
     */
    function sensational_blog_sortable_sections() {
        $sections = array(
            'featured-slider'   =>  esc_html__( 'Slider Section', 'sensational-blog' ),
            'message'   => esc_html__( 'Author info', 'sensational-blog' ),
            'galleryview'   => esc_html__( 'Featured', 'sensational-blog' ),
            'travel'    => esc_html__( 'Two column Section', 'sensational-blog' ),
            'subscription'   =>esc_html__( 'Popular Section', 'sensational-blog' ), 
        );
        return apply_filters( 'sensational_blog_sortable_sections', $sections );
    }
endif;

if ( ! function_exists( 'sensational_blog_pagination' ) ) :
  /**
   * blog/archive pagination.
   *
   * @return pagination type value
   */
  function sensational_blog_pagination() {
    $pagination = sensational_blog_get_option( 'pagination_type' );
    if ( $pagination == 'default' ) :
      the_posts_navigation();
    elseif ( $pagination == 'numeric' ) :
      the_posts_pagination( array(
          'mid_size' => 4,
          // 'prev_text' => ,
          // 'next_text' => ,
      ) );
    endif;
  }
endif;
add_action( 'sensational_blog_pagination_action', 'sensational_blog_pagination', 10 );
