<dialog id="newsletter-popup" class="newsletter-popup" aria-labelledby="newsletter-title" aria-describedby="newsletter-desc">
	<button type="button" class="newsletter-close" aria-label="Close newsletter sign-up">
		<svg viewBox="0 0 24 24" width="24" height="24" aria-hidden="true"><path d="M5 5l14 14M19 5L5 19" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/></svg>
	</button>

	<div class="newsletter-content">
		<div class="newsletter-heading">
			<img class="newsletter-sparkle" src="<?php echo esc_url( get_theme_file_uri( 'assets/Star.webp' ) ); ?>" alt="" width="75" height="81">
			<h2 id="newsletter-title">Join our newsletter</h2>
			<img class="newsletter-sparkle" src="<?php echo esc_url( get_theme_file_uri( 'assets/Star.webp' ) ); ?>" alt="" width="75" height="81">
		</div>
		<p id="newsletter-desc" class="newsletter-text">Get exclusive news about new launches, sales and content, plus <strong>10% off</strong> your first order.</p>

		<form class="newsletter-form" novalidate>
			<label for="newsletter-email" class="screen-reader-text">Email address</label>
			<input type="email" id="newsletter-email" name="email" class="newsletter-input" placeholder="janedoe@gmail.com" autocomplete="email" required autofocus>
			<?php wp_nonce_field( 'mytheme_newsletter_subscribe', 'newsletter_nonce' ); ?>
			<button type="submit" class="btn newsletter-submit">Subscribe</button>
			<p class="newsletter-consent">By subscribing you agree to receive emails from glam. Unsubscribe anytime.</p>
		</form>

		<div class="newsletter-success" hidden>
			<p class="newsletter-success-title">Thank you for subscribing!</p>
			<p>Use this code at checkout for 10% off your first order:</p>
			<p class="newsletter-code">GLAM10</p>
		</div>

		<p class="newsletter-status" role="status" aria-live="polite"></p>
	</div>
</dialog>

<button type="button" class="newsletter-reopen" aria-label="Open newsletter sign-up, get 10% off" aria-haspopup="dialog" aria-controls="newsletter-popup">
	<img src="<?php echo esc_url( get_theme_file_uri( 'assets/Star.webp' ) ); ?>" alt="" width="75" height="81">
</button>
