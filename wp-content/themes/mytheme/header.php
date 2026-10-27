<!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
	<meta charset="<?php bloginfo( 'charset' ); ?>">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<?php wp_head(); ?>
</head>
<body <?php body_class(); ?>>

<a class="skip-link" href="#main-content">Skip to main content</a>

<header class="site-header">
	<div class="container">
		<div class="logo">
			<a href="<?php echo esc_url( home_url( '/' ) ); ?>">
				<img src="<?php echo get_theme_file_uri( 'assets/glam.webp' ); ?>" alt="Glam" style="height: 40px;" width="74" height="40">
			</a>
		</div>
		<nav aria-label="Primary navigation">
			<ul>
				<li><a href="<?php echo esc_url( home_url( '/shop/' ) ); ?>">Shop</a></li>
				<li><a href="<?php echo esc_url( get_permalink( get_page_by_path( 'front-blog' ) ) ); ?>">Blog</a></li>
                <li><a href="<?php echo esc_url( get_permalink( get_page_by_path( 'about' ) ) ); ?>">About</a></li>
				<li><a href="<?php echo esc_url( get_permalink( get_page_by_path( 'contact-us' ) ) ); ?>">Contact us</a></li>
				<li><a href="https://sustainability.glamweb.dk" target="_blank" rel="noopener">Sustainability</a></li>
				<li><a href="<?php echo esc_url( wc_get_account_endpoint_url('dashboard') ); ?>">Account</a></li>
			</ul>
		</nav>
		<a href="<?php echo esc_url( wc_get_cart_url() ); ?>" class="cart" style="text-decoration: none;">Cart</a>
		<div class="header-lang">
			<a href="#">DK</a> /
			<a href="#">DE</a>
		</div>
	</div>
</header>
