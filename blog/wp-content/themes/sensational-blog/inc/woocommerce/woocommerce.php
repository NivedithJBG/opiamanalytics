<?php
/**
 * WooCommerce Compatibility File
 *
 * @link https://woocommerce.com/
 *
 * @package Sensational Blog
 */

/**
 * WooCommerce setup function.
 *
 * @link https://docs.woocommerce.com/document/third-party-custom-theme-compatibility/
 * @link https://github.com/woocommerce/woocommerce/wiki/Enabling-product-gallery-features-(zoom,-swipe,-lightbox)-in-3.0.0
 *
 * @return void
 */
function sensational_blog_woocommerce_setup() {
	add_theme_support( 'woocommerce' );
	add_theme_support( 'wc-product-gallery-zoom' );
	add_theme_support( 'wc-product-gallery-lightbox' );
	add_theme_support( 'wc-product-gallery-slider' );
}
add_action( 'after_setup_theme', 'sensational_blog_woocommerce_setup' );

/**
 * WooCommerce specific scripts & stylesheets.
 *
 * @return void
 */
function sensational_blog_woocommerce_scripts() {

	$font_path   = WC()->plugin_url() . '/assets/fonts/';
	$inline_font = '@font-face {
			font-family: "star";
			src: url("' . $font_path . 'star.eot");
			src: url("' . $font_path . 'star.eot?#iefix") format("embedded-opentype"),
				url("' . $font_path . 'star.woff") format("woff"),
				url("' . $font_path . 'star.ttf") format("truetype"),
				url("' . $font_path . 'star.svg#star") format("svg");
			font-weight: normal;
			font-style: normal;
		}';

	wp_add_inline_style( 'sensational-blog-woocommerce-style', $inline_font );
}
// add_action( 'wp_enqueue_scripts', 'sensational_blog_woocommerce_scripts' );

/**
 * Add 'woocommerce-active' class to the body tag.
 *
 * @param  array $classes CSS classes applied to the body tag.
 * @return array $classes modified to include 'woocommerce-active' class.
 */
function sensational_blog_woocommerce_active_body_class( $classes ) {
	$classes[] = 'woocommerce woocommerce-active';

	return $classes;
}
add_filter( 'body_class', 'sensational_blog_woocommerce_active_body_class' );

/**
 * Products per page.
 *
 * @return integer number of products.
 */
function sensational_blog_woocommerce_products_per_page() {
	$products_per_page = get_theme_mod( 'sensational_blog_woo_products_per_page', 12 );
	return absint( $products_per_page );
}
add_filter( 'loop_shop_per_page', 'sensational_blog_woocommerce_products_per_page' );

/**
 * Product gallery thumnbail columns.
 *
 * @return integer number of columns.
 */
function sensational_blog_woocommerce_thumbnail_columns() {
	return 5;
}
add_filter( 'woocommerce_product_thumbnails_columns', 'sensational_blog_woocommerce_thumbnail_columns' );

/**
 * Default loop columns on product archives.
 *
 * @return integer products per row.
 */
function sensational_blog_woocommerce_loop_columns() {
	return 5;
}
add_filter( 'loop_shop_columns', 'sensational_blog_woocommerce_loop_columns' );

/**
 * Related Products Args.
 *
 * @param array $args related products args.
 * @return array $args related products args.
 */
function sensational_blog_woocommerce_related_products_args( $args ) {
	$defaults = array(
		'posts_per_page' => 5,
		'columns'        => 5,
	);

	$args = wp_parse_args( $defaults, $args );

	return $args;
}
add_filter( 'woocommerce_output_related_products_args', 'sensational_blog_woocommerce_related_products_args' );

/**
 * Remove default WooCommerce wrapper.
 */
remove_action( 'woocommerce_before_main_content', 'woocommerce_output_content_wrapper', 10 );
remove_action( 'woocommerce_after_main_content', 'woocommerce_output_content_wrapper_end', 10 );
remove_action( 'woocommerce_before_main_content', 'woocommerce_breadcrumb', 20 );
// add_filter( 'woocommerce_show_page_title', '__return_false' );

if ( ! function_exists( 'sensational_blog_woocommerce_wrapper_before' ) ) {
	/**
	 * Before Content.
	 *
	 * Wraps all WooCommerce content in wrappers which match the theme markup.
	 *
	 * @return void
	 */
	function sensational_blog_woocommerce_wrapper_before() {
		?>
                <div id="primary" class="content-area">
                    <main id="main" class="site-main" role="main"> 
                    	<div class="wrapper">
						<?php
	}
}
add_action( 'woocommerce_before_main_content', 'sensational_blog_woocommerce_wrapper_before', 40 );

if ( ! function_exists( 'sensational_blog_woocommerce_wrapper_after' ) ) {
	/**
	 * After Content.
	 *
	 * Closes the wrapping divs.
	 *
	 * @return void
	 */
	function sensational_blog_woocommerce_wrapper_after() {
				?>
				</div>
			</main><!-- #main -->
		</div><!-- #primary -->
		<?php
	}
}
add_action( 'woocommerce_after_main_content', 'sensational_blog_woocommerce_wrapper_after' );

if ( ! function_exists( 'sensational_blog_woocommerce_wrapper_after_sidebar' ) ) {
	/**
	 * After Content.
	 *
	 * Closes the wrapping divs.
	 *
	 * @return void
	 */
	function sensational_blog_woocommerce_wrapper_after_sidebar() {
			?>
		<?php
	}
}
add_action( 'woocommerce_sidebar', 'sensational_blog_woocommerce_wrapper_after_sidebar' );

if ( ! function_exists( 'sensational_blog_woocommerce_cart_link_fragment' ) ) {
	/**
	 * Cart Fragments.
	 *
	 * Ensure cart contents update when products are added to the cart via AJAX.
	 *
	 * @param array $fragments Fragments to refresh via AJAX.
	 * @return array Fragments to refresh via AJAX.
	 */
	function sensational_blog_woocommerce_cart_link_fragment( $fragments ) {
		ob_start();
		sensational_blog_woocommerce_cart_link();
		$fragments['a.cart-contents'] = ob_get_clean();

		return $fragments;
	}
}
add_filter( 'woocommerce_add_to_cart_fragments', 'sensational_blog_woocommerce_cart_link_fragment' );

if ( ! function_exists( 'sensational_blog_woocommerce_cart_link' ) ) {
	/**
	 * Cart Link.
	 *
	 * Displayed a link to the cart including the number of items present and the cart total.
	 *
	 * @return void
	 */
	function sensational_blog_woocommerce_cart_link() {
		?>
		<a class="cart-contents" href="<?php echo esc_url( wc_get_cart_url() ); ?>" title="<?php esc_attr_e( 'View your shopping cart', 'sensational-blog' ); ?>">
			<?php
			$item_count_text = sprintf(
				/* translators: number of items in the mini cart. */
				_n( '%d item', '%d items', WC()->cart->get_cart_contents_count(), 'sensational-blog' ),
				WC()->cart->get_cart_contents_count()
			);
			?>
			<span class="amount"><?php echo wp_kses_data( WC()->cart->get_cart_subtotal() ); ?></span> <span class="count"><?php echo esc_html( $item_count_text ); ?></span>
		</a>
		<?php
	}
}

if ( ! function_exists( 'sensational_blog_woocommerce_before_shop_loop_item' ) ) {
	/**
	 * Before shop loop item
	 *
	 * Open the wrapping div.
	 *
	 * @return void
	 */
	function sensational_blog_woocommerce_before_shop_loop_item() {
			?>
      	<div class="post-thumbnail">
		<?php
	}
}
add_action( 'woocommerce_before_shop_loop_item', 'sensational_blog_woocommerce_before_shop_loop_item', 5 );

if ( ! function_exists( 'sensational_blog_woocommerce_after_shop_loop_item' ) ) {
	/**
	 * After shop loop item
	 *
	 * Close the wrapping div.
	 *
	 * @return void
	 */
	function sensational_blog_woocommerce_after_shop_loop_item() {
			?>
      	</div><!-- post-thumbnail -->
		<?php
	}
}
add_action( 'woocommerce_after_shop_loop_item', 'sensational_blog_woocommerce_after_shop_loop_item', 6 );

remove_action( 'woocommerce_shop_loop_item_title', 'woocommerce_template_loop_product_title', 10 );
remove_action( 'woocommerce_after_shop_loop_item_title', 'woocommerce_template_loop_rating', 5 );
remove_action( 'woocommerce_after_shop_loop_item_title', 'woocommerce_template_loop_price', 10 );

if ( ! function_exists( 'sensational_blog_woocommerce_meta_wrapper_start' ) ) {
	/**
	 * Starting wrap the meta values.
	 *
	 * @return void
	 */
	function sensational_blog_woocommerce_meta_wrapper_start() {
		?>
      	<div class="entry-container">
		<?php
	}
}
add_action( 'woocommerce_after_shop_loop_item', 'sensational_blog_woocommerce_meta_wrapper_start', 7 );

if ( ! function_exists( 'sensational_blog_woocommerce_meta_wrapper_end' ) ) {
	/**
	 * Closing Wrap the meta values
	 *
	 * @return void
	 */
	function sensational_blog_woocommerce_meta_wrapper_end() {
		?>
      	</div><!-- entry-container -->
		<?php
	}
}
add_action( 'woocommerce_after_shop_loop_item', 'sensational_blog_woocommerce_meta_wrapper_end', 20 );


function sensational_blog_woocommerce_cat_meta() {
	global $product;
	echo '<div class="product_meta">';
	echo wc_get_product_category_list( $product->get_id(), ', ', '<span class="posted_in">',  '</span>' );
	echo '</div>';
}
add_action( 'woocommerce_after_shop_loop_item', 'sensational_blog_woocommerce_cat_meta', 8 );

if ( ! function_exists( 'sensational_blog_woocommerce_product_title' ) ) {
	/**
	 * Product title
	 *
	 * @return void
	 */
	function sensational_blog_woocommerce_product_title() {
		?>
		<div class="sensational-blogduct-title">
      		<a href="<?php the_permalink();?>"><?php woocommerce_template_loop_product_title(); ?></a>
      	</div>
		<?php
	}
}
add_action( 'woocommerce_after_shop_loop_item', 'sensational_blog_woocommerce_product_title', 9 );

add_action( 'woocommerce_after_shop_loop_item', 'woocommerce_template_loop_rating', 10 );
add_action( 'woocommerce_after_shop_loop_item', 'woocommerce_template_loop_add_to_cart', 10 );
add_action( 'woocommerce_after_shop_loop_item', 'woocommerce_template_loop_price', 9 );
remove_action('woocommerce_sidebar', 'woocommerce_get_sidebar', 10);