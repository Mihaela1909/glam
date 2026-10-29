<?php

function mytheme_setup()
{
	add_theme_support('title-tag');
	add_theme_support('post-thumbnails');
	register_nav_menu('primary', 'Primary Menu');
}
add_action('after_setup_theme', 'mytheme_setup');

function mytheme_enable_woocommerce()
{
	add_theme_support('woocommerce');
	add_theme_support('wc-product-gallery-slider');
	add_theme_support('wc-product-gallery-lightbox');
}
add_action('after_setup_theme', 'mytheme_enable_woocommerce');
add_filter('woocommerce_show_page_title', '__return_false');

/**
 * Shop toolbar: search + sort in one row, result count underneath.
 */
function mytheme_shop_toolbar_open()
{
	?>
	<div class="shop-toolbar">
		<form class="shop-search" action="<?php echo esc_url(home_url('/')); ?>">
			<img src="<?php echo esc_url(get_theme_file_uri('assets/search.svg')); ?>" alt="">
			<input type="search" name="s" placeholder="Search..." value="<?php echo esc_attr(get_search_query()); ?>">
			<input type="hidden" name="post_type" value="product">
		</form>
		<?php
}

function mytheme_shop_toolbar_close()
{
	echo '</div>';
}
// Reposition the shop toolbar and result count to match the design.
add_action('woocommerce_before_shop_loop', 'mytheme_shop_toolbar_open', 15);
add_action('woocommerce_before_shop_loop', 'mytheme_shop_toolbar_close', 31);
remove_action('woocommerce_before_shop_loop', 'woocommerce_result_count', 20);
add_action('woocommerce_before_shop_loop', 'woocommerce_result_count', 35);

/**
 * Single product page: badges, accordion, quantity buttons, related products.
 */

// Product tags (e.g. "Cruelty-free", "Vegan") as pills above the title.
function mytheme_product_badges()
{
	$tags = get_the_terms(get_the_ID(), 'product_tag');
	if (empty($tags) || is_wp_error($tags)) {
		return;
	}
	echo '<div class="product-badges">';
	foreach ($tags as $tag) {
		echo '<span class="product-badge product-badge--' . esc_attr($tag->slug) . '">' . esc_html($tag->name) . '</span>';
	}
	echo '</div>';
}
add_action('woocommerce_single_product_summary', 'mytheme_product_badges', 4);

// Product tags as small labels above the name on shop/related/bestseller cards.
function mytheme_card_badges() {
	$tags = get_the_terms( get_the_ID(), 'product_tag' );
	if ( empty( $tags ) || is_wp_error( $tags ) ) {
		return;
	}
	echo '<div class="card-badges">';
	foreach ( $tags as $tag ) {
		echo '<span class="card-badge card-badge--' . esc_attr( $tag->slug ) . '">' . esc_html( $tag->name ) . '</span>';
	}
	echo '</div>';
}
add_action( 'woocommerce_shop_loop_item_title', 'mytheme_card_badges', 5 );

// Replace WooCommerce's tabs and SKU/category line with the accordion from the design.
remove_action('woocommerce_single_product_summary', 'woocommerce_template_single_meta', 40);
remove_action('woocommerce_after_single_product_summary', 'woocommerce_output_product_data_tabs', 10);

function mytheme_product_accordion()
{
	global $product;
	$ingredients = $product->get_attribute('ingredients');
	?>
		<div class="product-accordion">
			<details open>
				<summary>Full description</summary>
				<?php the_content(); ?>
			</details>
			<?php if ($ingredients): ?>
				<details>
					<summary>Ingredients</summary>
					<p><?php echo esc_html($ingredients); ?></p>
				</details>
			<?php endif; ?>
			<details>
				<summary>Shipping &amp; returns</summary>
				<p>Ships from Denmark in 1-3 working days. Free returns within 30 days.</p>
			</details>
		</div>
		<?php
}
add_action('woocommerce_single_product_summary', 'mytheme_product_accordion', 45);

// − and + buttons around the quantity field (also used on the cart page).
function mytheme_qty_minus()
{
	echo '<button type="button" class="qty-btn" data-step="-1" aria-label="Decrease quantity">−</button>';
}
function mytheme_qty_plus()
{
	echo '<button type="button" class="qty-btn" data-step="1" aria-label="Increase quantity">+</button>';
}
add_action('woocommerce_before_quantity_input_field', 'mytheme_qty_minus');
add_action('woocommerce_after_quantity_input_field', 'mytheme_qty_plus');

function mytheme_qty_script()
{
	if (!function_exists('is_woocommerce') || !(is_product() || is_cart())) {
		return;
	}
	?>
		<script>
			document.addEventListener('click', function (e) {
				var btn = e.target.closest('.qty-btn');
				if (!btn) {
					return;
				}
				var input = btn.parentElement.querySelector('.qty');
				var min = parseFloat(input.min) || 1;
				var max = parseFloat(input.max) || Infinity;
				var value = (parseFloat(input.value) || min) + Number(btn.dataset.step);
				input.value = Math.min(max, Math.max(min, value));
				input.dispatchEvent(new Event('change', { bubbles: true }));
			});
		</script>
		<?php
}
add_action('wp_footer', 'mytheme_qty_script');

// "You may also like": 4 products in one row.
function mytheme_related_products_args($args)
{
	$args['posts_per_page'] = 4;
	$args['columns'] = 4;
	return $args;
}
add_filter('woocommerce_output_related_products_args', 'mytheme_related_products_args');

function mytheme_related_heading()
{
	return 'You may also like';
}
add_filter('woocommerce_product_related_products_heading', 'mytheme_related_heading');

function mytheme_enqueue_assets()
{
	wp_enqueue_style('mytheme-fonts', 'https://fonts.googleapis.com/css2?family=Stack+Sans+Text:wght@200..700&display=swap', array(), null);
	wp_enqueue_style('mytheme-base', get_theme_file_uri('css/base.css'), array('mytheme-fonts'), filemtime(get_theme_file_path('css/base.css')));

	if (is_front_page()) {
		wp_enqueue_style('mytheme-front-page', get_theme_file_uri('css/front-page.css'), array('mytheme-base'), filemtime(get_theme_file_path('css/front-page.css')));
	}

	if (is_page('contact-us')) {
		wp_enqueue_style('mytheme-contact', get_theme_file_uri('css/contact.css'), array('mytheme-base'), filemtime(get_theme_file_path('css/contact.css')));
	}

	if (is_page('front-blog')) {
		wp_enqueue_style('mytheme-blog-listing', get_theme_file_uri('css/blog-listing.css'), array('mytheme-base'), filemtime(get_theme_file_path('css/blog-listing.css')));
	}

	if (function_exists('is_cart') && is_cart()) {
		wp_enqueue_style('mytheme-cart', get_theme_file_uri('css/cart.css'), array('mytheme-shop'), filemtime(get_theme_file_path('css/cart.css')));
	}

	if (function_exists('is_account_page') && is_account_page()) {
		wp_enqueue_style('mytheme-account', get_theme_file_uri('css/account.css'), array('mytheme-shop'), filemtime(get_theme_file_path('css/account.css')));
	}

	if (function_exists('is_checkout') && is_checkout()) {
		wp_enqueue_style('mytheme-checkout', get_theme_file_uri('css/checkout.css'), array('mytheme-shop'), filemtime(get_theme_file_path('css/checkout.css')));
	}

	// Shop styles also load on the front page for the Bestsellers product cards.
	if (is_front_page() || (function_exists('is_woocommerce') && (is_woocommerce() || is_cart() || is_checkout() || is_account_page()))) {
		wp_enqueue_style('mytheme-shop', get_theme_file_uri('css/shop.css'), array('mytheme-base'), filemtime(get_theme_file_path('css/shop.css')));
	}

	if (is_single()) {
		wp_enqueue_style('mytheme-single-post', get_theme_file_uri('css/single-post.css'), array('mytheme-base'), filemtime(get_theme_file_path('css/single-post.css')));
	}
}
add_action('wp_enqueue_scripts', 'mytheme_enqueue_assets');

/**
 * ===== SEO: Custom title format =====
 * Appends brand + keyword context to every page title.
 */
function mytheme_custom_title($title_parts)
{
	if (is_front_page()) {
		$title_parts['title'] = 'glam — Cruelty-Free Makeup, Denmark';
		unset($title_parts['site']);
	} elseif (is_page('contact-us')) {
		$title_parts['title'] = 'Contact Us — Cruelty-Free Beauty Support';
		$title_parts['site'] = 'glam';
	} elseif (is_page('front-blog')) {
		$title_parts['title'] = 'Beauty Blog — Tips, Tutorials & Sustainable Makeup';
		$title_parts['site'] = 'glam';
	} else {
		$title_parts['site'] = 'glam';
	}
	return $title_parts;
}
add_filter('document_title_parts', 'mytheme_custom_title');

/**
 * ===== SEO: Meta description per page type =====
 */
function mytheme_meta_description()
{
	if (is_front_page()) {
		$desc = 'glam is a curated online beauty shop in Denmark offering cruelty-free, sustainable makeup and beauty tools from trusted, hand-picked brands.';
	} elseif (is_page('contact-us')) {
		$desc = 'Get in touch with the glam team — questions, recommendations or feedback about our cruelty-free beauty products. We would love to hear from you.';
	} elseif (is_page('front-blog')) {
		$desc = 'Explore beauty tips, tutorials and sustainable makeup guides on the glam blog — everything for your everyday glam routine.';
	} elseif (is_page('about')) {
		$desc = 'Meet glam: a Danish online beauty shop with a curated, cruelty-free range of makeup from brands we have personally vetted.';
	} elseif (function_exists('is_shop') && is_shop()) {
		$desc = 'Shop cruelty-free and vegan makeup, foundation, mascara and lip products at glam. Ships from Denmark — not sure where to start? Take our quick quiz.';
	} elseif (function_exists('is_product_category') && is_product_category()) {
		$term = get_queried_object();
		$desc = $term->description ? wp_strip_all_tags($term->description) : 'Shop ' . $term->name . ' at glam — curated, cruelty-free beauty that ships from Denmark.';
	} elseif (is_singular('post')) {
		$intro = get_field('post_intro');
		if ($intro) {
			$desc = wp_trim_words($intro, 30);
		} elseif (has_excerpt()) {
			$desc = wp_strip_all_tags(get_the_excerpt());
		} else {
			$desc = wp_trim_words(wp_strip_all_tags(mytheme_get_post_body(false)), 30);
		}
	} elseif (is_singular('product')) {
		$desc = wp_trim_words(wp_strip_all_tags(get_the_content()), 30);
	} else {
		$desc = get_bloginfo('description');
	}
	if (!$desc) {
		$desc = 'glam is a curated online beauty shop in Denmark offering cruelty-free, sustainable makeup and beauty tools from trusted, hand-picked brands.';
	}
	echo '<meta name="description" content="' . esc_attr($desc) . '">' . "\n";
}
add_action('wp_head', 'mytheme_meta_description', 1);

/**
 * ===== SEO: Organization schema (site-wide) =====
 */
function mytheme_organization_schema()
{
	$schema = array(
		'@context' => 'https://schema.org',
		'@type' => 'Organization',
		'name' => 'glam',
		'url' => home_url('/'),
		'logo' => get_theme_file_uri('assets/glam.webp'),
		'sameAs' => array(
			'https://www.instagram.com/glam_denmark',
			'https://www.tiktok.com/@glam_denmark',
		),
	);
	echo '<script type="application/ld+json">' . wp_json_encode($schema) . '</script>' . "\n";
}
add_action('wp_head', 'mytheme_organization_schema');

/**
 * ===== SEO: Article schema on single blog posts =====
 */
function mytheme_article_schema()
{
	if (!is_singular('post')) {
		return;
	}
	$schema = array(
		'@context' => 'https://schema.org',
		'@type' => 'Article',
		'headline' => get_the_title(),
		'datePublished' => get_the_date('c'),
		'dateModified' => get_the_modified_date('c'),
		'author' => array(
			'@type' => 'Person',
			// get_the_author() is empty in wp_head because it runs outside the loop.
			'name' => get_field('author_name') ?: get_the_author_meta('display_name', get_post_field('post_author')),
		),
		'publisher' => array(
			'@type' => 'Organization',
			'name' => 'glam',
		),
	);
	$post_image = get_field('post_image');
	$image_url = $post_image ? $post_image['url'] : get_the_post_thumbnail_url(null, 'large');
	if ($image_url) {
		$schema['image'] = $image_url;
	}
	echo '<script type="application/ld+json">' . wp_json_encode($schema) . '</script>' . "\n";
}
add_action('wp_head', 'mytheme_article_schema');

/**
 * ===== SEO: Product schema on single products =====
 */
function mytheme_product_schema()
{
	if (!is_singular('product')) {
		return;
	}
	$product = wc_get_product(get_the_ID());
	$schema = array(
		'@context' => 'https://schema.org',
		'@type' => 'Product',
		'name' => get_the_title(),
		'description' => wp_strip_all_tags(get_the_excerpt() ?: get_the_content()),
		'image' => get_the_post_thumbnail_url(get_the_ID(), 'large'),
		'offers' => array(
			'@type' => 'Offer',
			'priceCurrency' => 'DKK',
			'price' => $product ? $product->get_price() : '',
			'availability' => ($product && $product->is_in_stock()) ? 'https://schema.org/InStock' : 'https://schema.org/OutOfStock',
			'url' => get_permalink(),
		),
	);
	echo '<script type="application/ld+json">' . wp_json_encode($schema) . '</script>' . "\n";
}
add_action('wp_head', 'mytheme_product_schema');
// WooCommerce prints its own Product schema too; keep only the theme's so each product page has one.
add_filter('woocommerce_structured_data_product', '__return_empty_array');

/**
 * Testimonial CPT — "What users think" section.
 */
function mytheme_register_testimonial_cpt()
{
	register_post_type('testimonial', array(
		'labels' => array(
			'name' => 'Testimonials',
			'singular_name' => 'Testimonial',
			'add_new_item' => 'Add New Testimonial',
		),
		'public' => false,
		'show_ui' => true,
		'show_in_menu' => true,
		'menu_icon' => 'dashicons-format-quote',
		'supports' => array('title', 'thumbnail'),
		'capability_type' => array('testimonial', 'testimonials'),
		'map_meta_cap' => true,
	));
}
add_action('init', 'mytheme_register_testimonial_cpt');

/**
 * Site Content CPT — a single entry admin edits to manage
 */
function mytheme_register_site_content_cpt()
{
	register_post_type('site_content', array(
		'labels' => array(
			'name' => 'Homepage Content',
			'singular_name' => 'Homepage Content',
		),
		'public' => false,
		'show_ui' => true,
		'show_in_menu' => true,
		'menu_icon' => 'dashicons-admin-home',
		'supports' => array('title'),
		// Only administrators can add or delete entries; the Communication Officer can edit them.
		'capability_type' => array('site_content', 'site_contents'),
		'capabilities' => array('create_posts' => 'create_site_contents'),
		'map_meta_cap' => true,
	));
}
add_action('init', 'mytheme_register_site_content_cpt');


/**
 * Product CPT — for the Shop / Bestsellers section.
 */
function mytheme_register_product_cpt()
{
	register_post_type('product', array(
		'labels' => array(
			'name' => 'Products',
			'singular_name' => 'Product',
			'add_new_item' => 'Add New Product',
		),
		'public' => true, // true so products can have their own pages/archive
		'show_ui' => true,
		'show_in_menu' => true,
		'menu_icon' => 'dashicons-cart',
		'has_archive' => true,
		'supports' => array('title', 'thumbnail', 'editor'),
		// Same capabilities WooCommerce uses for products (edit_products, publish_products, ...).
		'capability_type' => 'product',
		'map_meta_cap' => true,
	));
}
add_action('init', 'mytheme_register_product_cpt');


/**
 * Contact Message CPT — stores submissions from the Contact Us page.
 * post_title = sender's name, meta = email + message.
 */
function mytheme_register_contact_message_cpt()
{
	register_post_type('contact_message', array(
		'labels' => array(
			'name' => 'Contact Messages',
			'singular_name' => 'Contact Message',
		),
		'public' => false,
		'show_ui' => true,
		'show_in_menu' => true,
		'menu_icon' => 'dashicons-email',
		'supports' => array('title', 'custom-fields'),
		// Personal data: own capabilities so only administrators can see it (see mytheme_setup_roles).
		'capability_type' => array('contact_message', 'contact_messages'),
		'capabilities' => array('create_posts' => 'create_contact_messages'), // only the public form creates them
		'map_meta_cap' => true,
	));
}
add_action('init', 'mytheme_register_contact_message_cpt');


/**
 * Handle the contact form submission.
 */
function playground_handle_contact_form()
{
	if (!isset($_POST['contact_nonce']) || !wp_verify_nonce($_POST['contact_nonce'], 'submit_contact_form')) {
		wp_die('Security check failed. Please go back and try again.');
	}

	$name = sanitize_text_field($_POST['name']);
	$email = sanitize_email($_POST['email']);
	$message = sanitize_textarea_field($_POST['message']);

	if (empty($name) || empty($message) || !is_email($email)) {
		wp_die('Please fill in all fields with a valid email address.');
	}

	$postData = [
		"post_title" => $name,
		"post_status" => "publish",
		"post_type" => "contact_message",
	];
	$postId = wp_insert_post($postData, true);

	if (!is_wp_error($postId)) {
		update_post_meta($postId, 'email', $email);
		update_post_meta($postId, 'message', $message);
	}

	$subject = 'New contact form message from ' . $name;
	$body = "Name: $name\nEmail: $email\n\nMessage:\n$message";
	wp_mail(get_option('admin_email'), $subject, $body);

	$redirectUrl = add_query_arg('contact_status', 'success', $_POST['redirect_to']);
	wp_safe_redirect($redirectUrl);
	exit;
}

add_action('admin_post_submit_contact_form', 'playground_handle_contact_form');
add_action('admin_post_nopriv_submit_contact_form', 'playground_handle_contact_form');

/**
 * AJAX: Contact Form Submission 
 */
function mytheme_handle_contact_form_ajax()
{
	if (!isset($_POST['contact_nonce']) || !wp_verify_nonce($_POST['contact_nonce'], 'submit_contact_form')) {
		wp_send_json_error(array('message' => 'Security check failed. Please refresh and try again.'));
	}

	$name = sanitize_text_field($_POST['name']);
	$email = sanitize_email($_POST['email']);
	$message = sanitize_textarea_field($_POST['message']);

	if (empty($name) || empty($message) || !is_email($email)) {
		wp_send_json_error(array('message' => 'Please fill in all fields with a valid email address.'));
	}

	$postData = array(
		'post_title' => $name,
		'post_status' => 'publish',
		'post_type' => 'contact_message',
	);
	$postId = wp_insert_post($postData, true);

	if (is_wp_error($postId)) {
		wp_send_json_error(array('message' => 'Something went wrong. Please try again.'));
	}

	update_post_meta($postId, 'email', $email);
	update_post_meta($postId, 'message', $message);

	$subject = 'New contact form message from ' . $name;
	$body = "Name: $name\nEmail: $email\n\nMessage:\n$message";
	wp_mail(get_option('admin_email'), $subject, $body);

	wp_send_json_success(array('message' => "Thank you for your message! We'll get back to you soon."));
}
add_action('wp_ajax_submit_contact_form_ajax', 'mytheme_handle_contact_form_ajax');
add_action('wp_ajax_nopriv_submit_contact_form_ajax', 'mytheme_handle_contact_form_ajax');

/**
 * Add Email and Message columns to the Contact Messages admin list.
 */
function mytheme_contact_message_columns($columns)
{
	$columns['email'] = 'Email';
	$columns['message'] = 'Message';
	return $columns;
}
add_filter('manage_contact_message_posts_columns', 'mytheme_contact_message_columns');

function mytheme_contact_message_column_content($column, $post_id)
{
	if ($column === 'email') {
		echo esc_html(get_post_meta($post_id, 'email', true));
	}
	if ($column === 'message') {
		$message = get_post_meta($post_id, 'message', true);
		echo nl2br(esc_html($message));
	}

}
add_action('manage_contact_message_posts_custom_column', 'mytheme_contact_message_column_content', 10, 2);
/**
 * Custom comment display for single blog posts (avatar + name + time + text).
 */
function mytheme_comment_template($comment, $args, $depth)
{
	?>
		<li <?php comment_class('single-comment'); ?> id="comment-<?php comment_ID(); ?>">
			<div class="single-comment-avatar">
				<?php echo get_avatar($comment, 40); ?>
			</div>
			<div class="single-comment-body">
				<p class="single-comment-meta">
					<span class="single-comment-author"><?php comment_author(); ?></span>
					<span
						class="single-comment-time"><?php echo esc_html(human_time_diff(get_comment_time('U'), current_time('timestamp'))); ?>
						ago</span>
				</p>
				<p class="single-comment-text"><?php comment_text(); ?></p>
				<?php
				comment_reply_link(array_merge($args, array(
					'depth' => $depth,
					'max_depth' => $args['max_depth'],
					'reply_text' => 'Reply',
					'before' => '<p class="single-comment-reply">',
					'after' => '</p>',
				)));
				?>
			</div>
		</li>
		<?php
}
/**
 * AJAX handler: load more blog cards without leaving the page.
 */
function mytheme_load_more_posts()
{
	check_ajax_referer('mytheme_load_more_posts', 'nonce');

	$paged = isset($_POST['page']) ? absint($_POST['page']) : 1;
	$current_cat = isset($_POST['cat']) ? absint($_POST['cat']) : 0;

	$query_args = array(
		'post_type' => 'post',
		'posts_per_page' => 6,
		'paged' => $paged,
	);
	if ($current_cat > 0) {
		$query_args['cat'] = $current_cat;
	}

	$blog_query = new WP_Query($query_args);

	ob_start();
	if ($blog_query->have_posts()) {
		while ($blog_query->have_posts()) {
			$blog_query->the_post();
			get_template_part('template-parts/blog-card');
		}
		wp_reset_postdata();
	}
	$html = ob_get_clean();

	wp_send_json_success(array(
		'html' => $html,
		'has_more' => $paged < $blog_query->max_num_pages,
	));
}
add_action('wp_ajax_mytheme_load_more_posts', 'mytheme_load_more_posts');
add_action('wp_ajax_nopriv_mytheme_load_more_posts', 'mytheme_load_more_posts');

/**
 * Shorten the "Default sorting" label in the shop sort dropdown.
 */
function mytheme_sort_label($options)
{
	$options['menu_order'] = 'Sort';
	return $options;
}
add_filter('woocommerce_catalog_orderby', 'mytheme_sort_label');

/**
 * Wrap shop/related product images in a box for the gradient frame.
 */
function mytheme_loop_product_thumbnail()
{
	echo '<div class="shop-thumb">' . woocommerce_get_product_thumbnail() . '</div>';
}
remove_action('woocommerce_before_shop_loop_item_title', 'woocommerce_template_loop_product_thumbnail', 10);
add_action('woocommerce_before_shop_loop_item_title', 'mytheme_loop_product_thumbnail', 10);

/**
 * Cart: show the coupon field at the top of the cart totals card.
 */
function mytheme_cart_totals_coupon()
{
	if (!wc_coupons_enabled()) {
		return;
	}
	?>
		<h2 class="order-summary-title">Order summary</h2>
		<form class="cart-coupon" method="post" action="<?php echo esc_url(wc_get_cart_url()); ?>">
			<input type="text" name="coupon_code" placeholder="Coupon code">
			<button type="submit" name="apply_coupon" value="Apply coupon">Apply</button>
			<?php wp_nonce_field('woocommerce-cart', 'woocommerce-cart-nonce'); ?>
		</form>
		<?php
}
add_action('woocommerce_before_cart_totals', 'mytheme_cart_totals_coupon');

/**
 * Cart: info text under the "Proceed to checkout" button.
 */
function mytheme_checkout_info()
{
	echo '<p class="checkout-info">Shipping is estimated for Denmark and calculated at checkout.</p>';
}
add_action('woocommerce_proceed_to_checkout', 'mytheme_checkout_info', 30);

/**
 * Shop loop: show "Add" on every product card button.
 * Out-of-stock products keep WooCommerce's "Read more" label.
 */
function mytheme_loop_add_to_cart_text($text, $product)
{
	if ($product->is_purchasable() && $product->is_in_stock()) {
		return 'Add';
	}
	return $text;
}
add_filter('woocommerce_product_add_to_cart_text', 'mytheme_loop_add_to_cart_text', 10, 2);

/**
 * Screen reader label for the "Add" button must start with the visible word "Add"
 * (WCAG label-in-name). WooCommerce's default for variable products is "Select options for …".
 */
function mytheme_loop_add_to_cart_description($description, $product)
{
	if (!$product->is_purchasable() || !$product->is_in_stock()) {
		return $description;
	}
	/* translators: %s: product name */
	$label = $product->is_type('simple') ? 'Add “%s” to your cart' : 'Add “%s”: choose options';
	return sprintf($label, wp_strip_all_tags($product->get_name()));
}
add_filter('woocommerce_product_add_to_cart_description', 'mytheme_loop_add_to_cart_description', 10, 2);

/**
 * Checkout: wrap the "Your order" heading, order table and payment in one container.
 */
function mytheme_checkout_summary_open()
{
	echo '<div class="checkout-summary">';
}
add_action('woocommerce_checkout_before_order_review_heading', 'mytheme_checkout_summary_open');

function mytheme_checkout_summary_close()
{
	echo '</div>';
}
add_action('woocommerce_checkout_after_order_review', 'mytheme_checkout_summary_close');


/**
 * Blog post editor: help text under the ACF fields, and hide fields the theme never shows.
 * Hidden fields keep their saved values.
 */
function mytheme_blog_field_instructions($field)
{
	$instructions = array(
		'read_time' => 'Number of minutes, e.g. 5. Shown as "5 Mins Read" on the card and the post.',
		'post_title_field' => 'Optional. Overrides the normal title on the card and the post. Leave empty to use the normal title.',
		'post_text' => 'Optional. Full article text, used when it is longer than the text in the main editor. Also used for the card preview.',
		'post_image' => 'Optional. Overrides the Featured image on the card and the post.',
		'post_intro' => 'Optional. Short intro paragraph shown above the article.',
		'author_name' => 'Optional. Leave empty to show the WordPress author.',
		'author_role' => 'Optional. Shown under the author name, e.g. "Make-up Artist".',
		'author_avatar' => 'Optional. Small round photo next to the author name.',
	);
	if (isset($instructions[$field['name']])) {
		$field['instructions'] = $instructions[$field['name']];
	}
	return $field;
}
foreach (array('read_time', 'post_title_field', 'post_text', 'post_image', 'post_intro', 'author_name', 'author_role', 'author_avatar') as $mytheme_field_name) {
	add_filter('acf/prepare_field/name=' . $mytheme_field_name, 'mytheme_blog_field_instructions');
}

// Subtitle and Badge tag are not shown anywhere (tags come from WordPress Tags).
add_filter('acf/prepare_field/name=subtitle', '__return_false');
add_filter('acf/prepare_field/name=badge_tag', '__return_false');

/**
 * Article HTML for a single blog post.
 * Some posts keep the full article in the ACF "Post text" field and only a teaser in the editor,
 * so "Post text" is used when the editor has less than half as many words.
 */
function mytheme_get_post_body($format = true)
{
	$post_text = get_field('post_text');
	// Passing the post lets this also work outside the loop (in wp_head for the meta description).
	$content = get_the_content(null, false, get_post());
	$word_count = function ($html) {
		return str_word_count(html_entity_decode(wp_strip_all_tags($html)));
	};
	if ($post_text && $word_count($post_text) > 2 * $word_count($content)) {
		return wp_kses_post($post_text);
	}
	return $format ? apply_filters('the_content', $content) : $content;
}

/**
 * Newsletter Subscriber CPT — stores emails from the newsletter pop-up.
 * post_title = email address.
 */
function mytheme_register_newsletter_cpt()
{
	register_post_type('newsletter_signup', array(
		'labels' => array(
			'name' => 'Newsletter Subscribers',
			'singular_name' => 'Newsletter Subscriber',
		),
		'public' => false,
		'show_ui' => true,
		'show_in_menu' => true,
		'menu_icon' => 'dashicons-email-alt',
		'supports' => array('title'),
		'capability_type' => array('newsletter_signup', 'newsletter_signups'),
		'capabilities' => array('create_posts' => 'create_newsletter_signups'), // only the pop-up and quiz create them
		'map_meta_cap' => true,
	));
}
add_action('init', 'mytheme_register_newsletter_cpt');

/**
 * Newsletter pop-up: skip cart/checkout so it never interrupts a purchase.
 */
function mytheme_show_newsletter_popup()
{
	return !is_admin() && !(function_exists('is_cart') && (is_cart() || is_checkout()));
}

function mytheme_enqueue_newsletter()
{
	if (!mytheme_show_newsletter_popup()) {
		return;
	}
	wp_enqueue_style('mytheme-newsletter', get_theme_file_uri('css/newsletter.css'), array('mytheme-base'), filemtime(get_theme_file_path('css/newsletter.css')));
}
add_action('wp_enqueue_scripts', 'mytheme_enqueue_newsletter');

function mytheme_newsletter_popup()
{
	if (!mytheme_show_newsletter_popup()) {
		return;
	}
	get_template_part('template-parts/newsletter-popup');
	?>
		<script>
			document.addEventListener('DOMContentLoaded', function () {
				var popup = document.getElementById('newsletter-popup');
				if (!popup || typeof popup.showModal !== 'function') {
					return;
				}
				var form = popup.querySelector('.newsletter-form');
				var input = popup.querySelector('.newsletter-input');
				var submit = popup.querySelector('.newsletter-submit');
				var status = popup.querySelector('.newsletter-status');
				var success = popup.querySelector('.newsletter-success');
				var reopen = document.querySelector('.newsletter-reopen');
				var footer = document.querySelector('.site-footer');

				function store(key) {
					try { localStorage.setItem(key, '1'); } catch (e) {}
				}
				function stored(key) {
					try { return localStorage.getItem(key) === '1'; } catch (e) { return false; }
				}
				// Subscribers see their discount code instead of the form when they reopen it.
				function showSuccess() {
					form.hidden = true;
					success.hidden = false;
				}
				if (stored('glamNewsletterSubscribed')) {
					showSuccess();
				}

				// Show once per visitor, shortly after the page loads.
				if (!stored('glamNewsletterSeen')) {
					setTimeout(function () { popup.showModal(); }, 1500);
				}

				// Closing in any way (X, Esc, click outside) counts as seen.
				popup.addEventListener('close', function () {
					store('glamNewsletterSeen');
				});
				popup.querySelector('.newsletter-close').addEventListener('click', function () {
					popup.close();
				});
				popup.addEventListener('click', function (e) {
					if (e.target === popup) {
						var r = popup.getBoundingClientRect();
						var inside = e.clientX >= r.left && e.clientX <= r.right && e.clientY >= r.top && e.clientY <= r.bottom;
						if (!inside) {
							popup.close();
						}
					}
				});

				// Star button: reopens the pop-up any time.
				reopen.addEventListener('click', function () {
					status.textContent = '';
					popup.showModal();
				});

				// Keep the star button above the footer instead of covering it.
				function placeReopen() {
					var overlap = footer ? window.innerHeight - footer.getBoundingClientRect().top : 0;
					reopen.style.transform = overlap > 0 ? 'translateY(' + -overlap + 'px)' : '';
				}
				window.addEventListener('scroll', placeReopen, { passive: true });
				window.addEventListener('resize', placeReopen);
				placeReopen();

				form.addEventListener('submit', function (e) {
					e.preventDefault();
					if (!input.checkValidity()) {
						input.setAttribute('aria-invalid', 'true');
						status.textContent = 'Please enter a valid email address.';
						input.focus();
						return;
					}
					input.removeAttribute('aria-invalid');
					submit.disabled = true;
					status.textContent = 'Subscribing...';

					var data = new FormData(form);
					data.append('action', 'mytheme_newsletter_subscribe');

					fetch('<?php echo esc_url(admin_url('admin-ajax.php')); ?>', { method: 'POST', body: data })
						.then(function (res) { return res.json(); })
						.then(function (response) {
							if (response.success) {
								status.textContent = '';
								showSuccess();
								store('glamNewsletterSubscribed');
								store('glamNewsletterSeen');
							} else {
								status.textContent = response.data.message;
							}
						})
						.catch(function () {
							status.textContent = 'Something went wrong. Please try again.';
						})
						.finally(function () {
							submit.disabled = false;
						});
				});
			});
		</script>
		<?php
}
add_action('wp_footer', 'mytheme_newsletter_popup');

/**
 * Save an email as a newsletter sign-up, unless it is already on the list.
 * Used by the newsletter pop-up and the product quiz.
 */
function mytheme_save_newsletter_signup($email)
{
	$existing = get_posts(array(
		'post_type' => 'newsletter_signup',
		'title' => $email,
		'post_status' => 'any',
		'numberposts' => 1,
		'fields' => 'ids',
	));
	if (!empty($existing)) {
		return $existing[0];
	}
	return wp_insert_post(array(
		'post_title' => $email,
		'post_status' => 'publish',
		'post_type' => 'newsletter_signup',
	), true);
}

/**
 * AJAX: save a newsletter sign-up (ignores duplicates).
 */
function mytheme_newsletter_subscribe()
{
	if (!isset($_POST['newsletter_nonce']) || !wp_verify_nonce($_POST['newsletter_nonce'], 'mytheme_newsletter_subscribe')) {
		wp_send_json_error(array('message' => 'Security check failed. Please refresh and try again.'));
	}

	$email = isset($_POST['email']) ? sanitize_email(wp_unslash($_POST['email'])) : '';
	if (!is_email($email)) {
		wp_send_json_error(array('message' => 'Please enter a valid email address.'));
	}

	if (is_wp_error(mytheme_save_newsletter_signup($email))) {
		wp_send_json_error(array('message' => 'Something went wrong. Please try again.'));
	}

	wp_send_json_success(array('message' => 'Thank you for subscribing!'));
}
add_action('wp_ajax_mytheme_newsletter_subscribe', 'mytheme_newsletter_subscribe');
add_action('wp_ajax_nopriv_mytheme_newsletter_subscribe', 'mytheme_newsletter_subscribe');

/**
 * ===== Product quiz (shop page) =====
 * Five chat-style questions → a 3-product starter kit (base, eyes or cheeks, lips) + a matching tutorial.
 */
function mytheme_quiz_questions()
{
	return array(
		array(
			'key' => 'skin',
			'text' => "Hey! Let's find your perfect everyday routine. What's your skin type?",
			'options' => array('dry' => 'Dry & flaky', 'oily' => 'Oily & shiny', 'combination' => 'Combination', 'normal' => 'Normal / not sure'),
		),
		array(
			'key' => 'time',
			'text' => 'Got it. How much time do you usually spend on makeup?',
			'options' => array('quick' => 'Under 5 min', 'medium' => '5–15 min', 'full' => 'I love a full glam'),
		),
		array(
			'key' => 'goal',
			'text' => "Nice! What's your main goal?",
			'options' => array('glow' => 'A fresh, natural glow', 'coverage' => 'Coverage that lasts all day', 'learn' => 'Learn new techniques', 'bold' => 'A bold statement look'),
		),
		array(
			'key' => 'ethics',
			'text' => 'Do your products need to be cruelty-free or vegan?',
			'options' => array('cruelty-free' => 'Cruelty-free only', 'vegan' => 'Vegan only', 'any' => "Doesn't matter"),
		),
		array(
			'key' => 'tone',
			'text' => "Last one — what's your skin tone, so we can match your shade?",
			'options' => array('light' => 'Fair / light', 'medium' => 'Medium', 'deep' => 'Tan / deep'),
		),
	);
}

/**
 * Read and validate quiz answers from the request. Returns false if any answer is missing or unknown.
 */
function mytheme_quiz_answers_from_request()
{
	$answers = array();
	foreach (mytheme_quiz_questions() as $question) {
		$value = isset($_POST[$question['key']]) ? sanitize_key(wp_unslash($_POST[$question['key']])) : '';
		if (!isset($question['options'][$value])) {
			return false;
		}
		$answers[$question['key']] = $value;
	}
	return $answers;
}

/**
 * Score one product against the answers, using words from its name and short description.
 */
function mytheme_quiz_score($product, $answers)
{
	$text = strtolower($product->get_name() . ' ' . wp_strip_all_tags($product->get_short_description()));
	$has = function ($words) use ($text) {
		$hits = 0;
		foreach ($words as $word) {
			if (strpos($text, $word) !== false) {
				$hits++;
			}
		}
		return $hits;
	};
	$cats = wp_get_post_terms($product->get_id(), 'product_cat', array('fields' => 'slugs'));
	$score = 0;

	switch ($answers['skin']) {
		case 'dry':
			$score += 2 * $has(array('hydrat', 'glow', 'radian', 'nourish', 'cream', 'lotion', 'satin')) - 2 * $has(array('matte', 'powder'));
			break;
		case 'oily':
			$score += 2 * $has(array('matte', 'longwear', 'long wear', 'powder', 'grip', 'setting')) - $has(array('glow', 'radian', 'oil'));
			break;
		case 'combination':
			$score += $has(array('matte', 'lightweight', 'grip', 'buildable', 'soft-focus'));
			break;
	}

	switch ($answers['time']) {
		case 'quick':
			// Multi-use products and easy formulas save time; baking powder does not.
			$score += (count(array_intersect($cats, array('foundation', 'concealer', 'highlighter', 'primer'))) >= 2 ? 2 : 0)
				+ $has(array('lightweight', 'lotion', 'oil', 'gloss', 'washable', 'pen'))
				- 2 * $has(array('baking'));
			break;
		case 'full':
			$score += $has(array('full coverage', 'longwear', 'baking', 'setting', 'intense', 'volum'));
			break;
	}

	switch ($answers['goal']) {
		case 'glow':
			$score += 2 * $has(array('glow', 'radian', 'gloss', 'shine', 'illuminat', 'booster'));
			break;
		case 'coverage':
			$score += 2 * $has(array('longwear', 'long wear', 'coverage', 'grip', 'setting', 'matte'));
			break;
		case 'learn':
			$score += 2 * $has(array('buildable', 'baking', 'hourglass brush', 'brush', 'pen', 'blush'));
			break;
		case 'bold':
			$score += 2 * $has(array('intense', 'matte', 'rich colour', 'volum', 'false-lash'));
			break;
	}

	return $score;
}

/**
 * Pick a shade for foundation/concealer products: shades are listed light → deep.
 */
function mytheme_quiz_shade($product, $answers)
{
	$cats = wp_get_post_terms($product->get_id(), 'product_cat', array('fields' => 'slugs'));
	$attributes = $product->get_attributes();
	if (!array_intersect($cats, array('foundation', 'concealer')) || !isset($attributes['pa_shade'])) {
		return null;
	}
	$shades = $attributes['pa_shade']->get_terms();
	if (empty($shades)) {
		return null;
	}
	$position = array('light' => 0.15, 'medium' => 0.5, 'deep' => 1)[$answers['tone']];
	return $shades[min(count($shades) - 1, (int) floor($position * count($shades)))];
}

/**
 * Build the starter kit for a set of answers.
 * Returns array('items' => [...], 'tutorial' => WP_Post|null, 'note' => string).
 */
function mytheme_quiz_recommend($answers)
{
	$slots = array(
		'base' => $answers['time'] === 'full' ? array('foundation') : array('foundation', 'concealer', 'primer', 'powder'),
		'extra' => $answers['goal'] === 'glow' ? array('blush', 'highlighter') : array('mascara'),
		'lips' => $answers['goal'] === 'bold' ? array('lipstick') : array('lipstick', 'lipgloss'),
	);
	// Strictest first; fall back when a slot has no product that matches.
	$ethics = array('vegan' => array('vegan', 'cruelty-free', ''), 'cruelty-free' => array('cruelty-free', ''), 'any' => array(''));

	$products = wc_get_products(array('limit' => -1, 'status' => 'publish', 'stock_status' => 'instock'));
	$items = array();
	$used = array();
	$note = '';

	foreach ($slots as $slot => $slot_cats) {
		foreach ($ethics[$answers['ethics']] as $required_tag) {
			$best = null;
			$best_score = PHP_INT_MIN;
			foreach ($products as $product) {
				$id = $product->get_id();
				if (in_array($id, $used, true) || !has_term($slot_cats, 'product_cat', $id)) {
					continue;
				}
				// The cheek step should add colour, not a second complexion product.
				if ($slot === 'extra' && has_term('foundation', 'product_cat', $id)) {
					continue;
				}
				if ($required_tag && !has_term($required_tag, 'product_tag', $id)) {
					continue;
				}
				$score = mytheme_quiz_score($product, $answers);
				// On a tie, the cheaper product makes a friendlier starter kit.
				if ($score > $best_score || ($score === $best_score && (float) $product->get_price() < (float) $best->get_price())) {
					$best = $product;
					$best_score = $score;
				}
			}
			if ($best) {
				if ($required_tag !== $ethics[$answers['ethics']][0]) {
					$note = $answers['ethics'] === 'vegan'
						? "We don't have a vegan option for every step yet, so we picked the closest cruelty-free or classic match."
						: "We don't have a cruelty-free option for every step yet, so we picked the closest classic match.";
				}
				$used[] = $best->get_id();
				$items[] = array('product' => $best, 'shade' => mytheme_quiz_shade($best, $answers));
				break;
			}
		}
	}

	// Tutorial to match the goal (blog post IDs).
	$tutorials = array(
		'glow' => 108,     // How to not fall victim to blush blindness
		'coverage' => 133, // Long-wear base that survives a 12-hour shoot
		'learn' => $answers['time'] === 'quick' ? 478 : 106, // Winged liner for beginners / Eyeshadow night-out tutorial
		'bold' => 102,     // The DO's and DONT's of lip-lining
	);
	$tutorial = get_post($tutorials[$answers['goal']]);

	return array(
		'items' => $items,
		'tutorial' => ($tutorial && $tutorial->post_status === 'publish') ? $tutorial : null,
		'note' => $note,
	);
}

/**
 * Product page link with the suggested shade preselected, plus a direct add-to-cart link when the shade is the only choice.
 */
function mytheme_quiz_links($product, $shade)
{
	$links = array('view' => get_permalink($product->get_id()), 'add' => '');
	if (!$shade) {
		return $links;
	}
	$links['view'] = add_query_arg('attribute_pa_shade', $shade->slug, $links['view']);
	$variation_id = (new WC_Product_Data_Store_CPT())->find_matching_product_variation($product, array('attribute_pa_shade' => $shade->slug));
	if ($variation_id && count($product->get_variation_attributes()) === 1) {
		$links['add'] = add_query_arg(array(
			'add-to-cart' => $product->get_id(),
			'variation_id' => $variation_id,
			'attribute_pa_shade' => $shade->slug,
		), wc_get_cart_url());
	}
	return $links;
}

function mytheme_quiz_results_html($result)
{
	ob_start();
	?>
		<div class="quiz-kit">
			<?php foreach ($result['items'] as $item):
				$product = $item['product'];
				$links = mytheme_quiz_links($product, $item['shade']);
				?>
				<article class="quiz-product">
					<a class="quiz-product-image" href="<?php echo esc_url($links['view']); ?>" tabindex="-1" aria-hidden="true">
						<?php echo $product->get_image('woocommerce_thumbnail', array('loading' => 'lazy')); ?>
					</a>
					<h3 class="quiz-product-name"><a href="<?php echo esc_url($links['view']); ?>"><?php echo esc_html($product->get_name()); ?></a></h3>
					<p class="quiz-product-price"><?php echo wp_kses_post($product->get_price_html()); ?></p>
					<?php if ($item['shade']): ?>
						<p class="quiz-product-shade">Your shade: <strong><?php echo esc_html($item['shade']->name); ?></strong></p>
					<?php endif; ?>
					<div class="quiz-product-actions">
						<?php if ($links['add']): ?>
							<a class="btn quiz-product-add" href="<?php echo esc_url($links['add']); ?>">Add to cart</a>
						<?php endif; ?>
						<a class="quiz-product-view" href="<?php echo esc_url($links['view']); ?>">View product</a>
					</div>
				</article>
			<?php endforeach; ?>
		</div>
		<?php if ($result['note']): ?>
			<p class="quiz-note"><?php echo esc_html($result['note']); ?></p>
		<?php endif; ?>
		<?php if ($result['tutorial']): ?>
			<a class="quiz-tutorial" href="<?php echo esc_url(get_permalink($result['tutorial'])); ?>">
				<span class="quiz-tutorial-label">Your tutorial</span>
				<span class="quiz-tutorial-title"><?php echo esc_html(get_the_title($result['tutorial'])); ?> &rarr;</span>
			</a>
		<?php endif; ?>
		<?php
	return ob_get_clean();
}

/**
 * AJAX: quiz answers → result cards.
 */
function mytheme_quiz_results()
{
	check_ajax_referer('mytheme_quiz', 'nonce');
	$answers = mytheme_quiz_answers_from_request();
	if (!$answers) {
		wp_send_json_error(array('message' => 'Something went wrong. Please start the quiz again.'));
	}
	wp_send_json_success(array('html' => mytheme_quiz_results_html(mytheme_quiz_recommend($answers))));
}
add_action('wp_ajax_mytheme_quiz_results', 'mytheme_quiz_results');
add_action('wp_ajax_nopriv_mytheme_quiz_results', 'mytheme_quiz_results');

/**
 * AJAX: email the quiz results and add the address to the newsletter list.
 */
function mytheme_quiz_email()
{
	check_ajax_referer('mytheme_quiz', 'nonce');
	$answers = mytheme_quiz_answers_from_request();
	$email = isset($_POST['email']) ? sanitize_email(wp_unslash($_POST['email'])) : '';
	if (!$answers) {
		wp_send_json_error(array('message' => 'Something went wrong. Please start the quiz again.'));
	}
	if (!is_email($email)) {
		wp_send_json_error(array('message' => 'Please enter a valid email address.'));
	}

	$result = mytheme_quiz_recommend($answers);
	$lines = array();
	foreach ($result['items'] as $item) {
		$links = mytheme_quiz_links($item['product'], $item['shade']);
		$shade = $item['shade'] ? ' (your shade: ' . esc_html($item['shade']->name) . ')' : '';
		$lines[] = '<li><a href="' . esc_url($links['view']) . '">' . esc_html($item['product']->get_name()) . '</a>' . $shade . '</li>';
	}
	$body = '<h2>Your glam starter kit</h2><ul>' . implode('', $lines) . '</ul>';
	if ($result['tutorial']) {
		$body .= '<p>Your tutorial: <a href="' . esc_url(get_permalink($result['tutorial'])) . '">' . esc_html(get_the_title($result['tutorial'])) . '</a></p>';
	}
	$body .= '<p>Welcome to the glam newsletter! Use code <strong>GLAM10</strong> for 10% off your first order.</p>';

	mytheme_save_newsletter_signup($email);
	$sent = wp_mail($email, 'Your glam starter kit', $body, array('Content-Type: text/html; charset=UTF-8'));

	if (!$sent) {
		wp_send_json_error(array('message' => "We couldn't send the email right now. Please try again later."));
	}
	wp_send_json_success(array('message' => 'Sent! Check your inbox — your code GLAM10 is in there too.'));
}
add_action('wp_ajax_mytheme_quiz_email', 'mytheme_quiz_email');
add_action('wp_ajax_nopriv_mytheme_quiz_email', 'mytheme_quiz_email');

function mytheme_enqueue_quiz()
{
	if (!function_exists('is_shop') || !(is_shop() || is_product_taxonomy())) {
		return;
	}
	wp_enqueue_style('mytheme-quiz', get_theme_file_uri('css/quiz.css'), array('mytheme-shop'), filemtime(get_theme_file_path('css/quiz.css')));
	wp_enqueue_script('mytheme-quiz', get_theme_file_uri('js/quiz.js'), array(), filemtime(get_theme_file_path('js/quiz.js')), true);

	$questions = array();
	foreach (mytheme_quiz_questions() as $question) {
		$options = array();
		foreach ($question['options'] as $value => $label) {
			$options[] = array('value' => $value, 'label' => $label);
		}
		$questions[] = array('key' => $question['key'], 'text' => $question['text'], 'options' => $options);
	}
	wp_localize_script('mytheme-quiz', 'glamQuiz', array(
		'ajaxUrl' => admin_url('admin-ajax.php'),
		'nonce' => wp_create_nonce('mytheme_quiz'),
		'questions' => $questions,
		'finalText' => "Here's your personalised starter kit + a tutorial to match:",
	));
}
add_action('wp_enqueue_scripts', 'mytheme_enqueue_quiz');

/**
 * ===== Roles (CRUD by role, as documented in the report) =====
 *
 * Object            | Administrator | Communication Officer | Intern            | Visitor
 * Pages             | CRUD          | RU                    | R                 | R
 * Blog posts        | CRUD          | CRUD                  | CRU (own drafts)  | R
 * Site content      | CRUD          | RU                    | R                 | R
 * Testimonials      | CRUD          | CRUD                  | R                 | R
 * Products          | CRUD          | CRUD                  | R                 | R
 * Contact messages  | CRUD          | R                     | -                 | C (form)
 * Newsletter signups| CRUD          | R                     | -                 | C (form)
 * Comments          | CRUD          | RUD                   | R                 | C, R
 * Users / settings  | CRUD          | -                     | -                 | -
 *
 * Every custom post type has its own capabilities (see the register_post_type calls).
 * Bump the version to re-apply after changing capabilities.
 */
function mytheme_roles_caps($type, $actions)
{
	$caps = array();
	$map = array(
		'C' => array('create_%s', 'edit_%s', 'publish_%s'),
		'R' => array('read_private_%s'),
		'U' => array('edit_%s', 'edit_others_%s', 'edit_published_%s', 'edit_private_%s'),
		'D' => array('delete_%s', 'delete_others_%s', 'delete_published_%s', 'delete_private_%s'),
	);
	foreach (str_split($actions) as $action) {
		foreach ($map[$action] as $cap) {
			$caps[sprintf($cap, $type)] = true;
		}
	}
	return $caps;
}

function mytheme_setup_roles()
{
	if (get_option('mytheme_roles_version') === '2') {
		return;
	}

	// Administrator: full CRUD on the theme's own post types (WordPress/WooCommerce caps are already there).
	$admin = get_role('administrator');
	foreach (array('testimonials', 'site_contents', 'contact_messages', 'newsletter_signups', 'products') as $type) {
		foreach (array_keys(mytheme_roles_caps($type, 'CRUD')) as $cap) {
			$admin->add_cap($cap);
		}
	}

	remove_role('communication_officer');
	add_role('communication_officer', 'Communication Officer', array_merge(
		array(
			'read' => true,
			'upload_files' => true,
			'moderate_comments' => true, // comments: read, update, delete
			'manage_categories' => true,
			// Blog posts: CRUD (WordPress core capability names)
			'edit_posts' => true,
			'edit_others_posts' => true,
			'edit_published_posts' => true,
			'edit_private_posts' => true,
			'read_private_posts' => true,
			'publish_posts' => true,
			'delete_posts' => true,
			'delete_others_posts' => true,
			'delete_published_posts' => true,
			'delete_private_posts' => true,
			// Pages: read + update
			'edit_pages' => true,
			'edit_others_pages' => true,
			'edit_published_pages' => true,
			'read_private_pages' => true,
			// Product categories and tags
			'manage_product_terms' => true,
			'edit_product_terms' => true,
			'delete_product_terms' => true,
			'assign_product_terms' => true,
			// Contact messages and newsletter sign-ups: read the list only (no opening, editing or deleting)
			'edit_contact_messages' => true,
			'edit_newsletter_signups' => true,
		),
		mytheme_roles_caps('testimonials', 'CRUD'),
		mytheme_roles_caps('products', 'CRUD'),
		array(
			// Site content: read + update the existing entries
			'edit_site_contents' => true,
			'edit_others_site_contents' => true,
			'edit_published_site_contents' => true,
			'read_private_site_contents' => true,
		)
	));

	remove_role('intern');
	add_role('intern', 'Intern', array(
		'read' => true,
		'edit_posts' => true, // blog posts: create + edit own drafts; cannot publish, delete or edit others' work
	));

	update_option('mytheme_roles_version', '2');
}
add_action('init', 'mytheme_setup_roles');

/**
 * ===== Performance =====
 */

// Connect to Google Fonts early, before the stylesheet asks for the font files.
function mytheme_font_resource_hints($urls, $relation_type)
{
	if ($relation_type === 'preconnect') {
		$urls[] = 'https://fonts.googleapis.com';
		$urls[] = array('href' => 'https://fonts.gstatic.com', 'crossorigin');
	}
	return $urls;
}
add_filter('wp_resource_hints', 'mytheme_font_resource_hints', 10, 2);

// The homepage hero is a CSS background, which the browser only finds after the CSS loads.
// Preloading the first slide makes it the first image to download (it is the LCP element).
function mytheme_preload_hero_image()
{
	if (!is_front_page()) {
		return;
	}
	$site_content = get_posts(array('post_type' => 'site_content', 'posts_per_page' => 1, 'fields' => 'ids'));
	if (empty($site_content)) {
		return;
	}
	$slides = get_field('hero_slides', $site_content[0]);
	$image = $slides ? $slides[0]['slide_image'] : get_field('hero_image', $site_content[0]);
	if (empty($image)) {
		return;
	}
	echo '<link rel="preload" as="image" href="' . esc_url($image['sizes']['large'] ?? $image['url']) . '" fetchpriority="high">' . "\n";
}
add_action('wp_head', 'mytheme_preload_hero_image', 1);

// On the homepage every image sits below the full-screen hero: lazy-load them all,
// and stop WordPress from giving the first bestseller "high" priority over the hero.
function mytheme_front_page_lazy_images($attr)
{
	if (is_front_page()) {
		$attr['loading'] = 'lazy';
		unset($attr['fetchpriority']);
	}
	return $attr;
}
add_filter('wp_get_attachment_image_attributes', 'mytheme_front_page_lazy_images');
