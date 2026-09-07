<?php

function mytheme_setup() {
	add_theme_support( 'title-tag' );
	add_theme_support( 'post-thumbnails' );
	register_nav_menu( 'primary', 'Primary Menu' );
}
add_action( 'after_setup_theme', 'mytheme_setup' );

function mytheme_enqueue_assets() {
	wp_enqueue_style( 'mytheme-style', get_stylesheet_uri() );
}
add_action( 'wp_enqueue_scripts', 'mytheme_enqueue_assets' );

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
		</div>
	</li>
	<?php
}