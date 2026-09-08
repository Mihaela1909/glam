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

		<div class="blog-filter">
			<button id="filter-toggle" class="btn filter-btn">Filter &gt;</button>
			<ul id="filter-list" class="filter-dropdown" hidden>
				<li><a href="<?php echo esc_url( remove_query_arg( 'blog_cat' ) ); ?>" class="<?php echo $current_cat === 0 ? 'active-filter' : ''; ?>">All</a></li>
				<?php
				$categories = get_categories( array( 'hide_empty' => true ) );
				foreach ( $categories as $cat ) :
					$filter_url = add_query_arg( 'blog_cat', $cat->term_id );
					?>
					<li><a href="<?php echo esc_url( $filter_url ); ?>" class="<?php echo $current_cat === $cat->term_id ? 'active-filter' : ''; ?>"><?php echo esc_html( $cat->name ); ?></a></li>
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

<script>
document.addEventListener('DOMContentLoaded', function () {
	const toggle = document.getElementById('filter-toggle');
	const list = document.getElementById('filter-list');
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

	const loadMoreBtn = document.getElementById('load-more-btn');
	const grid = document.getElementById('front-blog-page-grid');
	if (!loadMoreBtn || !grid) return;

	loadMoreBtn.addEventListener('click', function () {
		const nextPage = parseInt(loadMoreBtn.dataset.page) + 1;
		const cat = loadMoreBtn.dataset.cat;

		loadMoreBtn.textContent = 'Loading...';

		const formData = new FormData();
		formData.append('action', 'mytheme_load_more_posts');
		formData.append('page', nextPage);
		formData.append('cat', cat);

		fetch('<?php echo esc_url( admin_url( 'admin-ajax.php' ) ); ?>', {
			method: 'POST',
			body: formData
		})
			.then(function (res) { return res.json(); })
			.then(function (response) {
				if (response.success) {
					grid.insertAdjacentHTML('beforeend', response.data.html);
					loadMoreBtn.dataset.page = nextPage;
					loadMoreBtn.textContent = 'Load more';
					if (!response.data.has_more) {
						loadMoreBtn.style.display = 'none';
					}
				}
			});
	});
});
</script>

<?php get_footer(); ?>