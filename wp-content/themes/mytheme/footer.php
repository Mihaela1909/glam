<footer class="site-footer">
		<div class="container footer-grid">
			<div class="logo">
<img src="<?php echo get_theme_file_uri( 'assets/glam.webp' ); ?>" alt="Glam" loading="lazy" width="364" height="196">			</div>
			<h4>Customer service</h4>
			<h4>Information</h4>
			<h4>Social Media</h4>

			<div class="footer-divider"></div>

			<div></div>
			<ul>
				<li><a href="#">Delivery</a></li>
				<li><a href="#">Returns</a></li>
			</ul>
			<ul>
				<li><a href="#">About</a></li>
				<li><a href="<?php echo esc_url( get_permalink( get_page_by_path( 'contact-us' ) ) ); ?>">Contact</a></li>
			</ul>
			<ul>
				<li><a href="#">Instagram</a></li>
				<li><a href="#">TikTok</a></li>
			</ul>
		</div>
	</footer>

	<?php wp_footer(); ?>
</body>
</html>