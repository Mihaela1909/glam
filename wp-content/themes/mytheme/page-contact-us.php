<?php
/* Template Name: Contact Us */
get_header();
?>

<section class="contact-hero" style="background-image: url('<?php echo get_theme_file_uri( 'assets/contact-hero.webp' ); ?>');">
	<div class="contact-hero-content">
		<h1>Have any questions, recommendations or concerns? Get in touch with us through here:</h1>
	</div>
</section>

<?php if ( isset( $_GET['contact_status'] ) && $_GET['contact_status'] === 'success' ) : ?>
	<div class="success-message">Thank you for your message! We'll get back to you soon.</div>
<?php endif; ?>

<div class="contact-form-wrap">
	<form class="contact-form" action="<?= esc_url( admin_url( 'admin-post.php' ) ) ?>" method="post">
		<?php wp_nonce_field( 'submit_contact_form', 'contact_nonce' ); ?>
		<input type="hidden" name="action" value="submit_contact_form">
		<input type="hidden" name="redirect_to" value="<?= esc_url( get_permalink() ) ?>">

		<p>
			<label for="name">Name</label>
			<input type="text" name="name" id="name" required>
		</p>
		<p>
			<label for="email">E-mail</label>
			<input type="email" name="email" id="email" required>
		</p>
		<p>
			<label for="message">Message</label>
			<textarea name="message" id="message" rows="6" required></textarea>
		</p>
		<p class="contact-form-submit">
			<button type="submit" class="btn">Send</button>
		</p>
	</form>
</div>

<div class="contact-signoff">
	<p>Sincerely, the glam team.</p>
	<div class="contact-icons">
		<a href="mailto:hello@glam.com" aria-label="Email">
			<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
				<rect x="2" y="4" width="20" height="16" rx="2"></rect>
				<path d="m22 7-8.97 5.7a1.94 1.94 0 0 1-2.06 0L2 7"></path>
			</svg>
		</a>
		<a href="#" aria-label="Instagram">
			<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
				<rect x="2" y="2" width="20" height="20" rx="5" ry="5"></rect>
				<path d="M16 11.37A4 4 0 1 1 12.63 8 4 4 0 0 1 16 11.37z"></path>
				<line x1="17.5" y1="6.5" x2="17.51" y2="6.5"></line>
			</svg>
		</a>
		<a href="#" aria-label="TikTok">
			<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="currentColor">
				<path d="M16.5 3c.3 2.1 1.7 3.6 3.9 3.9v2.6c-1.4 0-2.8-.4-3.9-1.2v6.2c0 3.2-2.6 5.5-5.6 5.5-1.4 0-2.7-.5-3.7-1.5-1.6-1.6-1.9-4.2-.6-6.1C7.6 11 9.1 10.1 10.7 10v2.7c-.5-.1-1.1 0-1.6.3-.9.5-1.4 1.6-1.1 2.6.3.9 1.2 1.6 2.2 1.6 1.4 0 2.5-1.1 2.5-2.5V3h3.8z"></path>
			</svg>
		</a>
	</div>
	<img class="sparkle" src="<?php echo get_theme_file_uri( 'assets/star.png' ); ?>" alt="" loading="lazy" width="48" height="48"></div>

<?php get_footer(); ?>