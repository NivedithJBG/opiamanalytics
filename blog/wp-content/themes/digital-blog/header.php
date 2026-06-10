<?php

/**
 * The header for our theme
 *
 * This is the template that displays all of the <head> section and everything up until <div id="content">
 *
 * @link https://developer.wordpress.org/themes/basics/template-files/#template-partials
 *
 * @package Digital Blog
 */

?>
<!doctype html>
<html <?php language_attributes(); ?>>

<head>
	<meta charset="<?php bloginfo( 'charset' ); ?>">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<link rel="profile" href="https://gmpg.org/xfn/11">
	<?php wp_head(); ?>
</head>

<body <?php body_class(); ?>>
	<?php wp_body_open(); ?>
	<div id="page" class="site ascendoor-site-wrapper">
		<a class="skip-link screen-reader-text" href="#primary"><?php esc_html_e( 'Skip to content', 'digital-blog' ); ?></a>

		<header id="masthead" class="site-header <?php echo esc_attr( ! empty( get_header_image() ) ? 'ascendoor-header-image' : '' ); ?>">
			<?php if ( ! empty( get_header_image() ) ) : ?>
				<div class="header-background-image">
					<img src="<?php echo esc_url( get_header_image() ); ?>" alt="<?php esc_attr_e( 'Header Image', 'digital-blog' ); ?>">
				</div>	
			<?php endif; ?>
			<div class="ascendoor-wrapper">
				<div class="header-wrapper">
					<div class="site-branding">
						<?php if ( has_custom_logo() ) { ?>
							<div class="site-logo">
								<?php the_custom_logo(); ?>
							</div>
						<?php } ?>
						<div class="site-identity">
							<?php if ( is_front_page() && is_home() ) : ?>
								<h1 class="site-title"><a href="<?php echo esc_url( home_url( '/' ) ); ?>" rel="home"><?php bloginfo( 'name' ); ?></a></h1>
							<?php else : ?>
								<p class="site-title">
									<a href="<?php echo esc_url( home_url( '/' ) ); ?>" rel="home"><?php bloginfo( 'name' ); ?></a>
								</p>
								<?php
							endif;
							$digital_blog_description = get_bloginfo( 'description', 'display' );
							if ( $digital_blog_description || is_customize_preview() ) :
								?>
							<p class="site-description"><?php echo esc_html( $digital_blog_description ); ?></p>
							<?php endif; ?>
						</div>
					</div><!-- .site-branding -->

					<div class="offcanv-search">
						<div class="header-search">
							<div class="header-search-wrap">
								<a href="#" title="Search" class="header-search-icon">
									<i class="fa fa-search"></i>
								</a>
								<div class="header-search-form">
									<?php get_search_form(); ?>
								</div>
							</div>
						</div>
					</div>
					<div class="navigation-part">
						<nav id="site-navigation" class="main-navigation">
							<button class="menu-toggle" aria-controls="primary-menu" aria-expanded="false">
								<div class="btn-span">
									<span></span>
									<span></span>
									<span></span>
								</div>
								<div class="btn-label"><?php esc_html_e( 'MENU', 'digital-blog' ); ?></div>
							</button>
							<div class="main-navigation-links">
								<?php
								if ( has_nav_menu( 'primary' ) ) {
									wp_nav_menu(
										array(
											'theme_location' => 'primary',
										)
									);
								}
								?>
							</div>
						</nav><!-- #site-navigation -->
					</div>
					<?php if ( has_nav_menu( 'social' ) ) { ?>
						<div class="social-icons">
							<?php
							wp_nav_menu(
								array(
									'menu_class'     => 'menu social-links',
									'link_before'    => '<span class="screen-reader-text">',
									'link_after'     => '</span>',
									'theme_location' => 'social',
								)
							);
							?>
						</div>
					<?php } ?>
				</div>
			</div>
		</header><!-- #masthead -->

		<?php

		if ( ! is_front_page() || is_home() ) {

			if ( is_front_page() ) {

				require get_template_directory() . '/sections/banner.php';
				require get_template_directory() . '/sections/sections.php';
			}

			?>

			<div id="content" class="site-content">
				<div class="ascendoor-wrapper">
					<div class="ascendoor-page">
					<?php } ?>
