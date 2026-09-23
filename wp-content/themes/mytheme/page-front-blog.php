<?php
/**
 * Template Name: FrontBlog Page
 */
get_header();

$current_cat = isset( $_GET['blog_cat'] ) ? absint( $_GET['blog_cat'] ) : 0;
?>

<main class="page-front-blog">
	<div class="front-blog-page-header container">
		<h1>Hottest Trends</h1>

		<!-- ===== Category filter links (now AJAX targets, no more full-page reload) ===== -->
<div class="blog-filter">
	<button id="filter-toggle" class="btn filter-btn">Filter &gt;</button>
	<ul id="filter-list" class="filter-dropdown" hidden>
		<li><a href="#" data-cat="0" class="filter-link <?php echo $current_cat === 0 ? 'active-filter' : ''; ?>">All</a></li>
		<?php
		$categories = get_categories( array( 'hide_empty' => true ) );
		foreach ( $categories as $cat ) :
			?>
			<li><a href="#" data-cat="<?php echo esc_attr( $cat->term_id ); ?>" class="filter-link <?php echo $current_cat === $cat->term_id ? 'active-filter' : ''; ?>"><?php echo esc_html( $cat->name ); ?></a></li>
			<?php
		endforeach;
		?>
	</ul>
</div>

	</div>

	<div id="front-blog-page-grid" class="front-blog-page-grid container">
		<?php
		$query_args = array(
			'post_type'      => 'post',
			'posts_per_page' => 6,
			'paged'          => 1,
		);
		if ( $current_cat > 0 ) {
			$query_args['cat'] = $current_cat;
		}
		$blog_query = new WP_Query( $query_args );

		if ( $blog_query->have_posts() ) :
			while ( $blog_query->have_posts() ) : $blog_query->the_post();
				get_template_part( 'template-parts/blog-card' );
			endwhile;
			wp_reset_postdata();
		else :
			echo '<p>No blog posts found in this category.</p>';
		endif;
		?>
	</div>

	<?php if ( $blog_query->max_num_pages > 1 ) : ?>
		<p class="load-more-wrap">
			<button id="load-more-btn" class="btn load-more-btn"
				data-page="1"
				data-cat="<?php echo esc_attr( $current_cat ); ?>">
				Load more
			</button>
		</p>
	<?php endif; ?>
</main>

<!-- ===== AJAX: Category filter (replaces grid) + Load more (appends to grid) ===== -->
<script>
document.addEventListener('DOMContentLoaded', function () {
	const toggle = document.getElementById('filter-toggle');
	const list = document.getElementById('filter-list');
	const grid = document.getElementById('front-blog-page-grid');
	const loadMoreBtn = document.getElementById('load-more-btn');
	const ajaxUrl = '<?php echo esc_url( admin_url( 'admin-ajax.php' ) ); ?>';

	if (toggle && list) {
		toggle.addEventListener('click', function () {
			list.hidden = !list.hidden;
		});
		document.addEventListener('click', function (e) {
			if (!toggle.contains(e.target) && !list.contains(e.target)) {
				list.hidden = true;
			}
		});
	}

	// Shared fetch function — used by BOTH filter clicks and Load More clicks
	function fetchPosts(page, cat, replace) {
		const formData = new FormData();
		formData.append('action', 'mytheme_load_more_posts');
		formData.append('page', page);
		formData.append('cat', cat);

		return fetch(ajaxUrl, { method: 'POST', body: formData })
			.then(function (res) { return res.json(); })
			.then(function (response) {
				if (!response.success) return;
				if (replace) {
					grid.innerHTML = response.data.html || '<p>No blog posts found in this category.</p>';
				} else {
					grid.insertAdjacentHTML('beforeend', response.data.html);
				}
				if (loadMoreBtn) {
					loadMoreBtn.dataset.page = page;
					loadMoreBtn.dataset.cat = cat;
					loadMoreBtn.style.display = response.data.has_more ? '' : 'none';
					loadMoreBtn.textContent = 'Load more';
				}
			});
	}

	// Category filter clicks — REPLACES grid content
	document.querySelectorAll('.filter-link').forEach(function (link) {
		link.addEventListener('click', function (e) {
			e.preventDefault();
			const cat = this.dataset.cat;

			document.querySelectorAll('.filter-link').forEach(function (l) { l.classList.remove('active-filter'); });
			this.classList.add('active-filter');
			list.hidden = true;

			grid.style.opacity = '0.5';
			fetchPosts(1, cat, true).then(function () {
				grid.style.opacity = '1';
			});
		});
	});

	// Load more button — APPENDS to grid content
	if (loadMoreBtn) {
		loadMoreBtn.addEventListener('click', function () {
			const nextPage = parseInt(loadMoreBtn.dataset.page) + 1;
			const cat = loadMoreBtn.dataset.cat;
			loadMoreBtn.textContent = 'Loading...';
			fetchPosts(nextPage, cat, false);
		});
	}
});
</script>

<?php get_footer(); ?>