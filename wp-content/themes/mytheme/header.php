<!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
	<meta charset="<?php bloginfo( 'charset' ); ?>">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<?php wp_head(); ?>
</head>
<body <?php body_class(); ?>>

<header class="site-header">
	<div class="container">
		<div class="logo">
			<a href="<?php echo esc_url( home_url( '/' ) ); ?>">
		<img src="<?php echo get_theme_file_uri( 'assets/glam.webp' ); ?>" alt="Glam" style="height: 40px;" width="74" height="40">			</a>
		</div>
		<nav>
    <ul>
        <li><a href="<?php echo esc_url( get_post_type_archive_link( 'product' ) ); ?>">Shop</a></li>
        <li><a href="<?php echo esc_url( get_permalink( get_page_by_path( 'front-blog' ) ) ); ?>">Blog</a></li>
        <li><a href="#">About</a></li>
        <li><a href="<?php echo esc_url( get_permalink( get_page_by_path( 'contact-us' ) ) ); ?>">Contact us</a></li>
        <li><a href="https://sustainability.glamweb.dk" target="_blank" rel="noopener">Sustainability</a></li>
    </ul>
</nav>
		<div class="cart">Cart</div>
	</div>
</header>
