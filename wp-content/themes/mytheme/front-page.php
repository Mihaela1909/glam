<?php get_header(); ?>

<?php
$site_content = get_posts( array(
	'post_type'      => 'site_content',
	'posts_per_page' => 1,
) );
$content_id = ! empty( $site_content ) ? $site_content[0]->ID : null;
?>

<!-- ============ HERO CAROUSEL ============ -->
<section class="hero-carousel">
	<?php
	$slides = $content_id ? get_field( 'hero_slides', $content_id ) : false;
	if ( $slides ) :
		$i = 0;
		foreach ( $slides as $slide ) :
			$i++;
			?>
			<div class="hero-slide <?php echo $i === 1 ? 'active' : ''; ?>"
style="background-image: url('<?php echo esc_url( $slide['slide_image']['sizes']['large'] ?? $slide['slide_image']['url'] ); ?>');">				<div class="hero-content hero-content-<?php echo esc_attr( $slide['text_position'] ?: 'center' ); ?>">
					<h1><?php echo nl2br( esc_html( $slide['slide_heading'] ) ); ?></h1>
					<div class="hero-buttons">
						<?php if ( ! empty( $slide['slide_button_text'] ) ) : ?>
							<a class="btn" href="<?php echo esc_url( $slide['slide_button_link'] ); ?>">
								<?php echo esc_html( $slide['slide_button_text'] ); ?>
							</a>
						<?php endif; ?>
						<?php if ( ! empty( $slide['slide_button_2_text'] ) ) : ?>
							<a class="btn" href="<?php echo esc_url( $slide['slide_button_2_link'] ); ?>">
								<?php echo esc_html( $slide['slide_button_2_text'] ); ?>
							</a>
						<?php endif; ?>
					</div>
				</div>
			</div>
			<?php
		endforeach;
	else :
		// Fallback: single static hero if no slides are set up yet
		?>
		<div class="hero-slide active" <?php if ( $content_id && get_field( 'hero_image', $content_id ) ) : ?>
<?php $hero_img = get_field( 'hero_image', $content_id ); ?>
style="background-image: url('<?php echo esc_url( $hero_img['sizes']['large'] ?? $hero_img['url'] ); ?>');"		<?php endif; ?>>
			<div class="hero-content">
				<h1><?php echo $content_id ? esc_html( get_field( 'hero_headline', $content_id ) ) : 'Everyday glam, made simple'; ?></h1>
				<div class="hero-buttons">
					<a class="btn" href="<?php echo $content_id ? esc_url( get_field( 'shop_button_link', $content_id ) ) : '#'; ?>">
						<?php echo $content_id ? esc_html( get_field( 'shop_button_text', $content_id ) ) : 'Shop now'; ?>
					</a>
					<a class="btn" href="<?php echo $content_id ? esc_url( get_field( 'blog_button_link', $content_id ) ) : '#'; ?>">
						<?php echo $content_id ? esc_html( get_field( 'blog_button_text', $content_id ) ) : 'Read blog'; ?>
					</a>
				</div>
			</div>
		</div>
	<?php endif; ?>

	<?php if ( $slides && count( $slides ) > 1 ) : ?>
		<div class="hero-dots">
			<?php for ( $d = 0; $d < count( $slides ); $d++ ) : ?>
				<span class="hero-dot <?php echo $d === 0 ? 'active' : ''; ?>" data-index="<?php echo $d; ?>"></span>
			<?php endfor; ?>
		</div>
	<?php endif; ?>
</section>

<script>
document.addEventListener('DOMContentLoaded', function () {
	const slides = document.querySelectorAll('.hero-slide');
	const dots = document.querySelectorAll('.hero-dot');
	if (slides.length <= 1) return;

	let current = 0;
	function showSlide(index) {
		slides.forEach(function (s) { s.classList.remove('active'); });
		dots.forEach(function (d) { d.classList.remove('active'); });
		slides[index].classList.add('active');
		if (dots[index]) dots[index].classList.add('active');
		current = index;
	}
	function nextSlide() {
		showSlide((current + 1) % slides.length);
	}
	let timer = setInterval(nextSlide, 5000);

	dots.forEach(function (dot) {
		dot.addEventListener('click', function () {
			clearInterval(timer);
			showSlide(parseInt(dot.dataset.index));
			timer = setInterval(nextSlide, 5000);
		});
	});
});
</script>

<!-- ============ BRAND MESSAGE ============ -->
<section class="brand-message">
	<img class="sparkle sparkle-left" src="<?php echo get_theme_file_uri( 'assets/star.webp' ); ?>" alt="" loading="lazy" width="40" height="40">
	<p><?php echo $content_id ? nl2br( esc_html( get_field( 'brand_message', $content_id ) ) ) : 'At glam, you can find anything.<br>Whether you are a beginner or a pro, clean or messy.'; ?></p>
	<img class="sparkle sparkle-right" src="<?php echo get_theme_file_uri( 'assets/star.webp' ); ?>" alt="">
</section>

<!-- ============ BESTSELLERS ============ -->
<section class="bestsellers">
	<h2>Bestsellers</h2>
	<div class="product-grid">
		<?php
		$bestsellers = new WP_Query( array(
			'post_type'      => 'product',
			'posts_per_page' => 4,
			'orderby'        => 'date',
		) );
		if ( $bestsellers->have_posts() ) :
			while ( $bestsellers->have_posts() ) : $bestsellers->the_post();
				?>
				<a class="product-card" href="<?php the_permalink(); ?>">
					<div class="product-thumb"><?php the_post_thumbnail( 'medium' ); ?></div>
					<p class="product-name"><?php the_title(); ?></p>
					<p class="product-price"><?php echo esc_html( get_field( 'price' ) ); ?> kr</p>
				</a>
				<?php
			endwhile;
			wp_reset_postdata();
		else :
			echo '<p>No products yet.</p>';
		endif;
		?>
	</div>
	<p class="see-more"><a href="<?php echo esc_url( get_post_type_archive_link( 'product' ) ); ?>">see more &gt;</a></p>
</section>

<div class="section-divider"></div>

<!-- ============ TESTIMONIALS ============ -->
<section class="testimonials">
	<h2>What users think</h2>
	<div class="testimonial-carousel">
		<div class="testimonial-carousel-track" id="testimonial-track">
			<?php
			// Render the loop twice in a row so the animation can loop seamlessly.
			for ( $pass = 0; $pass < 2; $pass++ ) :
				$testimonials = new WP_Query( array(
					'post_type'      => 'testimonial',
					'posts_per_page' => -1,
					'orderby'        => 'date',
				) );
				if ( $testimonials->have_posts() ) :
					while ( $testimonials->have_posts() ) : $testimonials->the_post();
						$photo = get_field( 'photo' );
						?>
						<div class="testimonial-card">
							<div class="testimonial-header">
								<?php if ( has_post_thumbnail() ) : ?>
									<div class="avatar"><?php the_post_thumbnail( 'thumbnail' ); ?></div>
								<?php else : ?>
									<div class="avatar avatar-placeholder"></div>
								<?php endif; ?>
								<span class="sender-name"><?php the_title(); ?></span>
							</div>
							<p class="quote"><?php echo esc_html( get_field( 'quote' ) ); ?></p>
							<?php if ( $photo ) : ?>
								<div class="testimonial-photo">
									<img src="<?php echo esc_url( $photo['sizes']['medium'] ?? $photo['url'] ); ?>" alt="" loading="lazy" width="400" height="200">
								</div>
							<?php endif; ?>
						</div>
						<?php
					endwhile;
					wp_reset_postdata();
				endif;
			endfor;
			?>
		</div>
	</div>
</section>

<!-- ============ FROM OUR BLOG ============ -->
<section class="blog-teaser">
	<h2>From our blog</h2>
	<div class="blog-carousel">
		<button class="blog-carousel-arrow blog-carousel-prev" aria-label="Previous posts">&lt;</button>
<div class="blog-carousel-track">
				<?php
			$latest_posts = new WP_Query( array(
				'post_type'      => 'post',
				'posts_per_page' => 6,
				'orderby'        => 'date',
				'order'          => 'DESC',
			) );
			if ( $latest_posts->have_posts() ) :
				while ( $latest_posts->have_posts() ) : $latest_posts->the_post();
					get_template_part( 'template-parts/blog-card' );
				endwhile;
				wp_reset_postdata();
			else :
				echo '<p>No blog posts yet.</p>';
			endif;
			?>
		</div>
		<button class="blog-carousel-arrow blog-carousel-next" aria-label="Next posts">&gt;</button>
	</div>
</section>

<script>
document.addEventListener('DOMContentLoaded', function () {
	const track = document.querySelector('.blog-carousel-track');
	const prevBtn = document.querySelector('.blog-carousel-prev');
	const nextBtn = document.querySelector('.blog-carousel-next');
	if (!track || !prevBtn || !nextBtn) return;

	function scrollAmount() {
		const card = track.querySelector('.blog-card');
		return card ? card.offsetWidth + 24 : 300;
	}
	prevBtn.addEventListener('click', function () {
		track.scrollBy({ left: -scrollAmount(), behavior: 'smooth' });
	});
	nextBtn.addEventListener('click', function () {
		track.scrollBy({ left: scrollAmount(), behavior: 'smooth' });
	});
});
</script>

<?php get_footer(); ?>