<?php
/**
 * Enqueue this page's own stylesheet without touching functions.php.
 */
add_action( 'wp_enqueue_scripts', function () {
	wp_enqueue_style(
		'mytheme-about',
		get_theme_file_uri( 'css/about.css' ),
		array( 'mytheme-base' ),
		filemtime( get_theme_file_path( 'css/about.css' ) )
	);
} );

get_header();
?>

<main id="main-content">

<section class="about-hero">
	<div class="about-hero-content">
		<p class="eyebrow">Our story</p>
		<h1>Makeup made for real, everyday confidence</h1>
		<p class="about-hero-text">Glam is a Danish online beauty shop bringing together cruelty-free makeup from brands we've personally vetted — so you can shop with confidence, without doing the research yourself.</p>
	</div>
</section>

<section class="about-why container">
	<div class="about-why-text">
		<h2>Why we started glam&trade;</h2>
		<p>We wanted a beauty shop that didn't make you choose between a brand you love and the way you want to shop. So we built glam: a curated range picked for quality, plus a dedicated cruelty-free selection for anyone who wants to shop that way — clearly marked, easy to filter, no digging through ingredient lists required.</p>
	</div>
	<div class="about-why-stack" aria-hidden="true">
		<img src="<?php echo get_theme_file_uri( 'assets/glam.webp' ); ?>" alt="" class="stack-card stack-card--pink" loading="lazy">
		<img src="<?php echo get_theme_file_uri( 'assets/glam.webp' ); ?>" alt="" class="stack-card stack-card--maroon" loading="lazy">
		<img src="<?php echo get_theme_file_uri( 'assets/glam.webp' ); ?>" alt="" class="stack-card stack-card--tan" loading="lazy">
	</div>
</section>

<section class="about-values">
	<h2>Our Values</h2>
	<div class="about-values-grid container">
		<article class="value-card">
			<img src="<?php echo get_theme_file_uri( 'assets/about-packaging.webp' ); ?>" alt="" loading="lazy">
			<p class="value-label">Sustainable</p>
			<h3>Packaging that doesn't linger</h3>
			<p>Recyclable or refillable packaging, and carbon-conscious shipping from our Danish warehouse.</p>
		</article>
		<article class="value-card">
			<img src="<?php echo get_theme_file_uri( 'assets/about-curated.webp' ); ?>" alt="" loading="lazy">
			<p class="value-label">Carefully curated</p>
			<h3>We vet every brand we carry</h3>
			<p>Before a brand reaches our shelves, we check its formulas and reviews — and clearly label which ones are cruelty-free.</p>
		</article>
		<article class="value-card">
			<img src="<?php echo get_theme_file_uri( 'assets/about-delivery.webp' ); ?>" alt="" loading="lazy">
			<p class="value-label">Rooted in Denmark</p>
			<h3>Shipped fast, from Denmark</h3>
			<p>Orders ship from our warehouse based in Denmark, with fast delivery across Germany, with a plan to expand in EU.</p>
		</article>
	</div>

	<div class="about-stats container">
		<div class="stat-card">
			<p class="stat-number">15+</p>
			<p class="stat-label">Curated brands, cruelty-free options included</p>
		</div>
		<div class="stat-card">
			<p class="stat-number">100%</p>
			<p class="stat-label">Recycled paper and cardboard packaging</p>
		</div>
		<div class="stat-card">
			<p class="stat-number">Delivery</p>
			<p class="stat-label">Designed and shipped across Denmark and Germany</p>
		</div>
		<div class="stat-card">
			<p class="stat-number">DK</p>
			<p class="stat-label">Denmark based company</p>
		</div>
	</div>
</section>

<section class="about-testimonial container">
	<blockquote>
		<p>&ldquo;The first beauty brand where I didn't have to double-check every ingredient myself. It's such a relief.&rdquo;</p>
		<cite>&mdash; NovaStar89, Make-up Artist Influencer</cite>
	</blockquote>
</section>

<section class="about-cta">
	<div class="about-cta-content">
		<h2>Ready to <span>GLAM UP?</span></h2>
		<a href="<?php echo esc_url( home_url( '/shop/' ) ); ?>" class="btn">Browse our shop</a>
	</div>
</section>

</main>

<?php get_footer(); ?>