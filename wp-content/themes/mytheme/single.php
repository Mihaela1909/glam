<?php get_header(); ?>

<main class="single-post">
	<div class="single-post-container">
		<?php if ( have_posts() ) : while ( have_posts() ) : the_post(); ?>

			<h1 class="single-post-title">
				<?php
				$post_title_field = get_field( 'post_title_field' );
				echo esc_html( $post_title_field ? $post_title_field : get_the_title() );
				?>
			</h1>

			<div class="single-post-hero">
				<?php
				$post_image = get_field( 'post_image' );
				if ( $post_image ) :
					?>
					<img src="<?php echo esc_url( $post_image['url'] ); ?>" alt="<?php echo esc_attr( $post_image['alt'] ); ?>">
					<?php
				elseif ( has_post_thumbnail() ) :
					the_post_thumbnail( 'large' );
				endif;
				?>
			</div>

			<?php $tags = get_the_tags(); ?>
			<?php if ( $tags ) : ?>
				<div class="single-post-tags">
					<?php foreach ( $tags as $tag ) : ?>
						<span class="single-post-tag">#<?php echo esc_html( $tag->name ); ?></span>
					<?php endforeach; ?>
				</div>
			<?php endif; ?>

			<div class="single-post-byline">
				<?php
				$author_name   = get_field( 'author_name' );
				$author_role   = get_field( 'author_role' );
				$author_avatar = get_field( 'author_avatar' );
				?>
				<div class="single-post-author">
					<?php if ( $author_avatar ) : ?>
						<img class="single-post-avatar" src="<?php echo esc_url( $author_avatar['url'] ); ?>" alt="">
					<?php else : ?>
						<div class="single-post-avatar single-post-avatar-placeholder"></div>
					<?php endif; ?>
					<div>
						<p class="single-post-author-name"><?php echo esc_html( $author_name ? $author_name : get_the_author() ); ?></p>
						<?php if ( $author_role ) : ?>
							<p class="single-post-author-role"><?php echo esc_html( $author_role ); ?></p>
						<?php endif; ?>
					</div>
				</div>
				<div class="single-post-meta">
					<span class="single-post-date"><?php echo esc_html( get_the_date( 'M j, Y' ) ); ?></span>
					<?php
					$read_time = get_field( 'read_time' );
					if ( $read_time ) :
						?>
						<span class="single-post-readtime"><?php echo esc_html( $read_time ); ?> Mins Read</span>
					<?php endif; ?>
				</div>
			</div>

			<?php
			$post_intro = get_field( 'post_intro' );
			if ( $post_intro ) :
				?>
				<p class="single-post-intro"><?php echo esc_html( $post_intro ); ?></p>
			<?php endif; ?>

			<hr class="single-post-divider">

			<div class="single-post-text">
				<?php the_content(); ?>
			</div>

			<hr class="single-post-divider">

			<?php comments_template(); ?>

		<?php endwhile; endif; ?>

		<p class="back-to-blog">
			<a href="<?php echo esc_url( get_permalink( get_page_by_path( 'front-blog' ) ) ); ?>">&larr; Back to Blog</a>
		</p>
	</div>
</main>

<?php get_footer(); ?>