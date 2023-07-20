<?php
/**
 * The template for displaying single questions
 *
 * @package DW Question & Answer
 * @since DW Question & Answer 1.0.1
 */
?>

<?php do_action( 'dwqa_before_single_question_content' ); ?>
<div class="dwqa-question-item">
	<div class="dwqa-question-vote" data-nonce="<?php echo wp_create_nonce( '_dwqa_question_vote_nonce' ) ?>" data-post="<?php the_ID(); ?>">
		<span class="dwqa-vote-count"><?php echo dwqa_vote_count() ?></span>
		<a class="dwqa-vote dwqa-vote-up" href="#"><?php _e( 'Vote Up', 'dwqa-at' ); ?></a>
		<a class="dwqa-vote dwqa-vote-down" href="#"><?php _e( 'Vote Down', 'dwqa-at' ); ?></a>
	</div>
	<div class="dwqa-question-meta">
		<?php $user_id = get_post_field( 'post_author', get_the_ID() ) ? get_post_field( 'post_author', get_the_ID() ) : false ?>
		<?php printf( __( '<span><a href="%1$s">%2$s%3$s</a> %4$s asked %5$s ago</span>', 'dwqa' ), dwqa_get_author_link( $user_id ), get_avatar( $user_id, 48 ), get_the_author(),  dwqa_print_user_badge( $user_id ), human_time_diff( get_post_time( 'U', true ) ) ) ?>
		<span class="dwqa-question-actions"><?php dwqa_question_button_action() ?></span>
	</div>
	<div class="dwqa-question-content"><?php the_content(); ?></div>
	<footer class="dwqa-question-footer">
		<div class="dwqa-question-meta">
			<?php echo get_the_term_list( get_the_ID(), 'dwqa-question_tag', '<span class="dwqa-question-tag">' . __( 'Question Tags: ', 'dwqa-at' ), ', ', '</span>' ); ?>
			<?php if ( dwqa_current_user_can( 'manage_question' ) && function_exists( 'dwqa_get_assignee_template' )  ) : ?>
				<?php dwqa_get_assignee_template( get_the_ID() ); ?>
			<?php endif; ?>
			<?php if ( dwqa_current_user_can( 'edit_question', get_the_ID() ) || dwqa_current_user_can( 'manage_question' ) ) : ?>
				<?php if ( dwqa_is_enable_status() ) : ?>
				<span class="dwqa-question-status">
					<?php _e( 'This question is:', 'dwqa-at' ) ?>
					<select id="dwqa-question-status" data-nonce="<?php echo wp_create_nonce( '_dwqa_update_privacy_nonce' ) ?>" data-post="<?php the_ID(); ?>">
						<optgroup label="<?php _e( 'Status', 'dwqa-at' ); ?>">
							<option <?php selected( dwqa_question_status(), 'open' ) ?> value="open"><?php _e( 'Open', 'dwqa-at' ) ?></option>
							<option <?php selected( dwqa_question_status(), 'closed' ) ?> value="closed"><?php _e( 'Closed', 'dwqa-at' ) ?></option>
							<option <?php selected( dwqa_question_status(), 'resolved' ) ?> value="resolved"><?php _e( 'Resolved', 'dwqa-at' ) ?></option>
						</optgroup>
					</select>
					</span>
				<?php endif; ?>
			<?php endif; ?>
		</div>
	</footer>
	<?php comments_template(); ?>
</div>
<?php do_action( 'dwqa_after_single_question_content' ); ?>
