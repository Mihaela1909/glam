<?php

function mytheme_setup() {
	add_theme_support( 'title-tag' );
	add_theme_support( 'post-thumbnails' );
	register_nav_menu( 'primary', 'Primary Menu' );
}
add_action( 'after_setup_theme', 'mytheme_setup' );

function mytheme_enqueue_assets() {
	wp_enqueue_style( 'mytheme-base', get_theme_file_uri( 'css/base.css' ), array(), '1.0' );

	if ( is_front_page() ) {
		wp_enqueue_style( 'mytheme-front-page', get_theme_file_uri( 'css/front-page.css' ), array( 'mytheme-base' ), '1.0' );
	}

	if ( is_page( 'contact-us' ) ) {
		wp_enqueue_style( 'mytheme-contact', get_theme_file_uri( 'css/contact.css' ), array( 'mytheme-base' ), '1.0' );
	}

	if ( is_page( 'front-blog' ) ) {
		wp_enqueue_style( 'mytheme-blog-listing', get_theme_file_uri( 'css/blog-listing.css' ), array( 'mytheme-base' ), '1.0' );
	}

	if ( is_single() ) {
		wp_enqueue_style( 'mytheme-single-post', get_theme_file_uri( 'css/single-post.css' ), array( 'mytheme-base' ), '1.0' );
	}
}
add_action( 'wp_enqueue_scripts', 'mytheme_enqueue_assets' );

/**
 * ===== SEO: Custom title format =====
 * Appends brand + keyword context to every page title.
 */
function mytheme_custom_title( $title_parts ) {
	if ( is_front_page() ) {
		$title_parts['title'] = 'glam — Cruelty-Free Beauty & Skincare, Denmark';
		unset( $title_parts['site'] );
	} elseif ( is_page( 'contact-us' ) ) {
		$title_parts['title'] = 'Contact Us — Cruelty-Free Beauty Support';
		$title_parts['site'] = 'glam';
	} elseif ( is_page( 'front-blog' ) ) {
		$title_parts['title'] = 'Beauty Blog — Tips, Tutorials & Sustainable Makeup';
		$title_parts['site'] = 'glam';
	} else {
		$title_parts['site'] = 'glam';
	}
	return $title_parts;
}
add_filter( 'document_title_parts', 'mytheme_custom_title' );

/**
 * ===== SEO: Meta description per page type =====
 */
function mytheme_meta_description() {
	if ( is_front_page() ) {
		$desc = 'glam is a curated online beauty shop in Denmark offering cruelty-free, sustainable makeup and skincare from trusted, hand-picked brands.';
	} elseif ( is_page( 'contact-us' ) ) {
		$desc = 'Get in touch with the glam team — questions, recommendations or feedback about our cruelty-free beauty products. We would love to hear from you.';
	} elseif ( is_page( 'front-blog' ) ) {
		$desc = 'Explore beauty tips, tutorials and sustainable makeup guides on the glam blog — everything for your everyday glam routine.';
	} elseif ( is_singular( 'post' ) ) {
		$desc = has_excerpt() ? wp_strip_all_tags( get_the_excerpt() ) : wp_trim_words( wp_strip_all_tags( get_the_content() ), 30 );
	} elseif ( is_singular( 'product' ) ) {
		$desc = wp_trim_words( wp_strip_all_tags( get_the_content() ), 30 );
	} else {
		$desc = get_bloginfo( 'description' );
	}
	echo '<meta name="description" content="' . esc_attr( $desc ) . '">' . "\n";
}
add_action( 'wp_head', 'mytheme_meta_description', 1 );

/**
 * ===== SEO: Organization schema (site-wide) =====
 */
function mytheme_organization_schema() {
	$schema = array(
		'@context' => 'https://schema.org',
		'@type'    => 'Organization',
		'name'     => 'glam',
		'url'      => home_url( '/' ),
		'logo'     => get_theme_file_uri( 'assets/glam.webp' ),
		'sameAs'   => array(
			'https://www.instagram.com/glam_denmark',
			'https://www.tiktok.com/@glam_denmark',
		),
	);
	echo '<script type="application/ld+json">' . wp_json_encode( $schema ) . '</script>' . "\n";
}
add_action( 'wp_head', 'mytheme_organization_schema' );

/**
 * ===== SEO: Article schema on single blog posts =====
 */
function mytheme_article_schema() {
	if ( ! is_singular( 'post' ) ) {
		return;
	}
	$schema = array(
		'@context'      => 'https://schema.org',
		'@type'         => 'Article',
		'headline'      => get_the_title(),
		'datePublished' => get_the_date( 'c' ),
		'dateModified'  => get_the_modified_date( 'c' ),
		'author'        => array(
			'@type' => 'Person',
			'name'  => get_the_author(),
		),
	);
	echo '<script type="application/ld+json">' . wp_json_encode( $schema ) . '</script>' . "\n";
}
add_action( 'wp_head', 'mytheme_article_schema' );

/**
 * ===== SEO: Product schema on single products =====
 */
function mytheme_product_schema() {
	if ( ! is_singular( 'product' ) ) {
		return;
	}
	$price = get_field( 'price' );
	$schema = array(
		'@context'    => 'https://schema.org',
		'@type'       => 'Product',
		'name'        => get_the_title(),
		'description' => wp_strip_all_tags( get_the_excerpt() ?: get_the_content() ),
		'image'       => get_the_post_thumbnail_url( get_the_ID(), 'large' ),
		'offers'      => array(
			'@type'         => 'Offer',
			'priceCurrency' => 'DKK',
			'price'         => $price ?: '',
			'availability'  => 'https://schema.org/InStock',
		),
	);
	echo '<script type="application/ld+json">' . wp_json_encode( $schema ) . '</script>' . "\n";
}
add_action( 'wp_head', 'mytheme_product_schema' );

/**
 * Testimonial CPT — "What users think" section.
 */
function mytheme_register_testimonial_cpt() {
	register_post_type( 'testimonial', array(
		'labels' => array(
			'name'          => 'Testimonials',
			'singular_name' => 'Testimonial',
			'add_new_item'  => 'Add New Testimonial',
		),
		'public'       => false,
		'show_ui'      => true,
		'show_in_menu' => true,
		'menu_icon'    => 'dashicons-format-quote',
		'supports'     => array( 'title', 'thumbnail' ),
	) );
}
add_action( 'init', 'mytheme_register_testimonial_cpt' );

/**
 * Site Content CPT — a single entry admin edits to manage
 */
function mytheme_register_site_content_cpt() {
	register_post_type( 'site_content', array(
		'labels' => array(
			'name'          => 'Homepage Content',
			'singular_name' => 'Homepage Content',
		),
		'public'       => false,
		'show_ui'      => true,
		'show_in_menu' => true,
		'menu_icon'    => 'dashicons-admin-home',
		'supports'     => array( 'title' ),
	) );
}
add_action( 'init', 'mytheme_register_site_content_cpt' );


/**
 * Product CPT — for the Shop / Bestsellers section.
 */
function mytheme_register_product_cpt() {
	register_post_type( 'product', array(
		'labels' => array(
			'name'          => 'Products',
			'singular_name' => 'Product',
			'add_new_item'  => 'Add New Product',
		),
		'public'       => true, // true so products can have their own pages/archive
		'show_ui'      => true,
		'show_in_menu' => true,
		'menu_icon'    => 'dashicons-cart',
		'has_archive'  => true,
		'supports'     => array( 'title', 'thumbnail', 'editor' ),
	) );
}
add_action( 'init', 'mytheme_register_product_cpt' );


/**
 * Contact Message CPT — stores submissions from the Contact Us page.
 * post_title = sender's name, meta = email + message.
 */
function mytheme_register_contact_message_cpt() {
	register_post_type( 'contact_message', array(
		'labels' => array(
			'name'          => 'Contact Messages',
			'singular_name' => 'Contact Message',
		),
		'public'       => false,
		'show_ui'      => true,
		'show_in_menu' => true,
		'menu_icon'    => 'dashicons-email',
'supports'     => array( 'title', 'custom-fields' ),	) );
}
add_action( 'init', 'mytheme_register_contact_message_cpt' );


/**
 * Handle the contact form submission.
 */
function playground_handle_contact_form() {
	if ( ! isset( $_POST['contact_nonce'] ) || ! wp_verify_nonce( $_POST['contact_nonce'], 'submit_contact_form' ) ) {
		wp_die( 'Security check failed. Please go back and try again.' );
	}

	$name    = sanitize_text_field( $_POST['name'] );
	$email   = sanitize_email( $_POST['email'] );
	$message = sanitize_textarea_field( $_POST['message'] );

	if ( empty( $name ) || empty( $message ) || ! is_email( $email ) ) {
		wp_die( 'Please fill in all fields with a valid email address.' );
	}

	$postData = [
		"post_title"  => $name,
		"post_status" => "publish",
		"post_type"   => "contact_message",
	];
	$postId = wp_insert_post( $postData, true );

	if ( ! is_wp_error( $postId ) ) {
		update_post_meta( $postId, 'email', $email );
		update_post_meta( $postId, 'message', $message );
	}

	$subject = 'New contact form message from ' . $name;
	$body    = "Name: $name\nEmail: $email\n\nMessage:\n$message";
	wp_mail( get_option( 'admin_email' ), $subject, $body );

	$redirectUrl = add_query_arg( 'contact_status', 'success', $_POST['redirect_to'] );
	wp_safe_redirect( $redirectUrl );
	exit;
}

add_action( 'admin_post_submit_contact_form', 'playground_handle_contact_form' );
add_action( 'admin_post_nopriv_submit_contact_form', 'playground_handle_contact_form' );

/**
 * AJAX: Contact Form Submission 
 */
function mytheme_handle_contact_form_ajax() {
	if ( ! isset( $_POST['contact_nonce'] ) || ! wp_verify_nonce( $_POST['contact_nonce'], 'submit_contact_form' ) ) {
		wp_send_json_error( array( 'message' => 'Security check failed. Please refresh and try again.' ) );
	}

	$name    = sanitize_text_field( $_POST['name'] );
	$email   = sanitize_email( $_POST['email'] );
	$message = sanitize_textarea_field( $_POST['message'] );

	if ( empty( $name ) || empty( $message ) || ! is_email( $email ) ) {
		wp_send_json_error( array( 'message' => 'Please fill in all fields with a valid email address.' ) );
	}

	$postData = array(
		'post_title'  => $name,
		'post_status' => 'publish',
		'post_type'   => 'contact_message',
	);
	$postId = wp_insert_post( $postData, true );

	if ( is_wp_error( $postId ) ) {
		wp_send_json_error( array( 'message' => 'Something went wrong. Please try again.' ) );
	}

	update_post_meta( $postId, 'email', $email );
	update_post_meta( $postId, 'message', $message );

	$subject = 'New contact form message from ' . $name;
	$body    = "Name: $name\nEmail: $email\n\nMessage:\n$message";
	wp_mail( get_option( 'admin_email' ), $subject, $body );

	wp_send_json_success( array( 'message' => "Thank you for your message! We'll get back to you soon." ) );
}
add_action( 'wp_ajax_submit_contact_form_ajax', 'mytheme_handle_contact_form_ajax' );
add_action( 'wp_ajax_nopriv_submit_contact_form_ajax', 'mytheme_handle_contact_form_ajax' );

/**
 * Add Email and Message columns to the Contact Messages admin list.
 */
function mytheme_contact_message_columns( $columns ) {
	$columns['email']   = 'Email';
	$columns['message'] = 'Message';
	return $columns;
}
add_filter( 'manage_contact_message_posts_columns', 'mytheme_contact_message_columns' );

function mytheme_contact_message_column_content( $column, $post_id ) {
	if ( $column === 'email' ) {
		echo esc_html( get_post_meta( $post_id, 'email', true ) );
	}
	if ( $column === 'message' ) {
		$message = get_post_meta( $post_id, 'message', true );
		echo nl2br( esc_html( $message ) );
	}

}
add_action( 'manage_contact_message_posts_custom_column', 'mytheme_contact_message_column_content', 10, 2 );
/**
 * Custom comment display for single blog posts (avatar + name + time + text).
 */
function mytheme_comment_template( $comment, $args, $depth ) {
	?>
	<li <?php comment_class( 'single-comment' ); ?> id="comment-<?php comment_ID(); ?>">
		<div class="single-comment-avatar">
			<?php echo get_avatar( $comment, 40 ); ?>
		</div>
		<div class="single-comment-body">
			<p class="single-comment-meta">
				<span class="single-comment-author"><?php comment_author(); ?></span>
				<span class="single-comment-time"><?php echo esc_html( human_time_diff( get_comment_time( 'U' ), current_time( 'timestamp' ) ) ); ?> ago</span>
			</p>
			<p class="single-comment-text"><?php comment_text(); ?></p>
			<?php
			comment_reply_link( array_merge( $args, array(
				'depth'     => $depth,
				'max_depth' => $args['max_depth'],
				'reply_text' => 'Reply',
				'before'    => '<p class="single-comment-reply">',
				'after'     => '</p>',
			) ) );
			?>
		</div>
	</li>
	<?php
}
/**
 * AJAX handler: load more blog cards without leaving the page.
 */
function mytheme_load_more_posts() {
	check_ajax_referer( 'mytheme_load_more_posts', 'nonce' );

	$paged       = isset( $_POST['page'] ) ? absint( $_POST['page'] ) : 1;
	$current_cat = isset( $_POST['cat'] ) ? absint( $_POST['cat'] ) : 0;

	$query_args = array(
		'post_type'      => 'post',
		'posts_per_page' => 6,
		'paged'          => $paged,
	);
	if ( $current_cat > 0 ) {
		$query_args['cat'] = $current_cat;
	}

	$blog_query = new WP_Query( $query_args );

	ob_start();
	if ( $blog_query->have_posts() ) {
		while ( $blog_query->have_posts() ) {
			$blog_query->the_post();
			get_template_part( 'template-parts/blog-card' );
		}
		wp_reset_postdata();
	}
	$html = ob_get_clean();

	wp_send_json_success( array(
		'html'     => $html,
		'has_more' => $paged < $blog_query->max_num_pages,
	) );
}
add_action( 'wp_ajax_mytheme_load_more_posts', 'mytheme_load_more_posts' );
add_action( 'wp_ajax_nopriv_mytheme_load_more_posts', 'mytheme_load_more_posts' );