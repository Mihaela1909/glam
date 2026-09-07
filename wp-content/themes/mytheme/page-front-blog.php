<?php
/**
 * Template Name: FrontBlog Page
 */
get_header();

$current_cat = isset( $_GET['blog_cat'] ) ? absint( $_GET['blog_cat'] ) : 0;
$paged       = isset( $_GET['blog_page'] ) ? absint( $_GET['blog_page'] ) : 1;
?>

<main class="page-front-blog">
	<div class="front-blog-page-header container">
		<h1>Hottest Trends</h1>

		<div class="blog-filter">
			<button id="filter-toggle" class="btn filter-btn">Filter &gt;</button>
			<ul id="filter-list" class="filter-dropdown" hidden>
				<li><a href="<?php echo esc_url( remove_query_arg( array( 'blog_cat', 'blog_page' ) ) ); ?>" class="<?php echo $current_cat === 0 ? 'active-filter' : ''; ?>">All</a></li>
				<?php
				$categories = get_categories( array( 'hide_empty' => true ) );
				foreach ( $categories as $cat ) :
					$filter_url = add_query_arg( 'blog_cat', $cat->term_id, remove_query_arg( 'blog_page' ) );
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
			'paged'          => $paged,
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
			<?php if ( $paged < $blog_query->max_num_pages ) : ?>
				<?php
				$next_url = add_query_arg( array(
					'blog_page' => $paged + 1,
					'blog_cat'  => $current_cat,
				) );
				?>
				<a class="btn load-more-btn" href="<?php echo esc_url( $next_url ); ?>">Load more</a>
			<?php endif; ?>
		</p>
	<?php endif; ?>
</main>

<script>
document.addEventListener('DOMContentLoaded', function () {
	const toggle = document.getElementById('filter-toggle');
	const list = document.getElementById('filter-list');
	if (!toggle || !list) return;

	toggle.addEventListener('click', function () {
		list.hidden = !list.hidden;
	});

	document.addEventListener('click', function (e) {
		if (!toggle.contains(e.target) && !list.contains(e.target)) {
			list.hidden = true;
		}
	});
});
</script>

<?php get_footer(); ?>