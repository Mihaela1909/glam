<a class="blog-card" href="<?php the_permalink(); ?>">
	<div class="blog-card-thumb">
		<?php
		$post_image = get_field( 'post_image' );
		if ( $post_image ) :
			?>
			<img src="<?php echo esc_url( $post_image['url'] ); ?>" alt="<?php echo esc_attr( $post_image['alt'] ); ?>">
			<?php
		elseif ( has_post_thumbnail() ) :
			the_post_thumbnail( 'medium_large' );
		else :
			?>
			<img src="<?php echo esc_url( get_theme_file_uri( 'assets/placeholder.png' ) ); ?>" alt="">
			<?php
		endif;
		?>
	</div>
	<div class="blog-card-body">
		<h3>
			<?php
			$post_title_field = get_field( 'post_title_field' );
			echo esc_html( $post_title_field ? $post_title_field : get_the_title() );
			?>
		</h3>
		<p class="blog-card-excerpt">
			<?php
			$post_text = get_field( 'post_text' );
			echo esc_html( wp_trim_words( $post_text ? $post_text : get_the_excerpt(), 14 ) );
			?>
		</p>
		<div class="blog-card-footer">
			<p class="blog-card-date"><?php echo esc_html( get_the_date( 'j.n.Y' ) ); ?></p>
			<?php $read_time = get_field( 'read_time' ); ?>
			<?php if ( $read_time ) : ?>
    		<p class="blog-card-readtime"><?php echo esc_html( $read_time ); ?> Mins Read</p>
			<?php endif; ?>
		</div>
	</div>
</a>