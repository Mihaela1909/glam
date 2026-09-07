<?php
if ( post_password_required() ) {
	return;
}
?>

<div id="comments" class="single-post-comments">

	<h3 class="comments-title">
		Discussion (<?php echo esc_html( get_comments_number() ); ?>)
	</h3>

	<?php if ( have_comments() ) : ?>
		<ul class="comment-list">
			<?php
			wp_list_comments( array(
				'style'      => 'ul',
				'short_ping' => true,
				'callback'   => 'mytheme_comment_template',
			) );
			?>
		</ul>
	<?php endif; ?>

	<?php
	comment_form( array(
		'title_reply'         => '',
		'comment_field'       => '<textarea id="comment" name="comment" class="comment-input" placeholder="Add a comment..." required></textarea>',
		'comment_notes_before' => '',
		'comment_notes_after'  => '',
		'fields'              => array(
			'author' => '<input id="author" name="author" type="text" placeholder="Your name" required class="comment-input-name">',
			'email'  => '<input id="email" name="email" type="email" placeholder="Your email" required class="comment-input-email">',
		),
		'label_submit'        => 'Post',
		'class_submit'        => 'btn comment-submit-btn',
		'class_form'          => 'comment-form',
	) );
	?>
</div>