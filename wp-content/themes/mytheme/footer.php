<footer class="site-footer">
	<div class="container footer-grid">
		<div class="logo">
			<img src="<?php echo get_theme_file_uri( 'assets/glam.webp' ); ?>" alt="Glam">
		</div>

		<div class="footer-col footer-col-1">
			<h2>Customer service</h2>
			<ul>
				<li><a href="<?php echo esc_url( home_url( '/delivery' ) ); ?>">Delivery</a></li>
				<li><a href="<?php echo esc_url( home_url( '/returns' ) ); ?>">Returns</a></li>
			</ul>
		</div>

		<div class="footer-col footer-col-2">
			<h2>Information</h2>
			<ul>
				<li><a href="#">About</a></li>
				<li><a href="<?php echo esc_url( get_permalink( get_page_by_path( 'contact-us' ) ) ); ?>">Contact</a></li>
			</ul>
		</div>

		<div class="footer-col footer-col-3">
			<h2>Social Media</h2>
			<ul>
				<li><a href="https://www.instagram.com/glam_denmark?utm_source=ig_web_button_share_sheet&stkn=ZDNlZDc0MzIxNw==">Instagram</a></li>
				<li><a href="https://www.tiktok.com/@glam_denmark?is_from_webapp=1&sender_device=pc">TikTok</a></li>
			</ul>
		</div>

		<div class="footer-divider" aria-hidden="true"></div>
	</div>
</footer>

<?php wp_footer(); ?>
</body>
</html>