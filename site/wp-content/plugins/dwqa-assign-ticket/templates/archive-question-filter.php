<?php
/**
 * The template for displaying answers
 *
 * @package DW Question & Answer
 * @since DW Question & Answer 1.0.1
 */

global $dwqa_general_settings;
$sort = isset( $_GET['sort'] ) ? $_GET['sort'] : '';
$filter = isset( $_GET['filter'] ) ? $_GET['filter'] : 'all';
?>
<div class="dwqa-question-filter">
	<span><?php _e( 'Filter:', 'dwqa-at' ); ?></span>
	<?php if ( !isset( $_GET['user'] ) ) : ?>
		<a href="<?php echo esc_url( add_query_arg( array( 'filter' => 'all' ) ) ) ?>" class="<?php echo 'all' == $filter ? 'active' : '' ?>"><?php _e( 'All', 'dwqa-at' ); ?></a>
		<?php if ( dwqa_is_enable_status() ) : ?>
			<a href="<?php echo esc_url( add_query_arg( array( 'filter' => 'open' ) ) ) ?>" class="<?php echo 'open' == $filter ? 'active' : '' ?>"><?php _e( 'Open', 'dwqa-at' ); ?></a>
			<a href="<?php echo esc_url( add_query_arg( array( 'filter' => 'resolved' ) ) ) ?>" class="<?php echo 'resolved' == $filter ? 'active' : '' ?>"><?php _e( 'Resolved', 'dwqa-at' ); ?></a>
			<a href="<?php echo esc_url( add_query_arg( array( 'filter' => 'closed' ) ) ) ?>" class="<?php echo 'closed' == $filter ? 'active' : '' ?>"><?php _e( 'Closed', 'dwqa-at' ); ?></a>
		<?php endif; ?>
		<a href="<?php echo esc_url( add_query_arg( array( 'filter' => 'unanswered' ) ) ) ?>" class="<?php echo 'unanswered' == $filter ? 'active' : '' ?>"><?php _e( 'Unanswered', 'dqwa-at' ); ?></a>
		<?php if ( is_user_logged_in() ) : ?>
			<a href="<?php echo esc_url( add_query_arg( array( 'filter' => 'my-questions' ) ) ) ?>" class="<?php echo 'my-questions' == $filter ? 'active' : '' ?>"><?php _e( 'My questions', 'dqwa-at' ); ?></a>
			<a href="<?php echo esc_url( add_query_arg( array( 'filter' => 'my-subscribes' ) ) ) ?>" class="<?php echo 'my-subscribes' == $filter ? 'active' : '' ?>"><?php _e( 'My subscribes', 'dqwa-at' ); ?></a>
		<?php endif; ?>
	<?php else : ?>
		<a href="<?php echo esc_url( add_query_arg( array( 'filter' => 'all' ) ) ) ?>" class="<?php echo 'all' == $filter ? 'active' : '' ?>"><?php _e( 'Questions', 'dqwa-at' ); ?></a>
		<a href="<?php echo esc_url( add_query_arg( array( 'filter' => 'subscribes' ) ) ) ?>" class="<?php echo 'subscribes' == $filter ? 'active' : '' ?>"><?php _e( 'Subscribes', 'dqwa-at' ); ?></a>
	<?php endif; ?>
	<?php if ( dwqa_current_user_can( 'manage_question' ) ) : ?>
		<a href="<?php echo esc_url( add_query_arg( array( 'filter' => 'my-ticket' ) ) ) ?>" class="<?php echo 'my-ticket' == $filter ? 'active' : '' ?>">My Ticket</a>
	<?php endif; ?>
	<select id="dwqa-sort-by" class="dwqa-sort-by" onchange="this.options[this.selectedIndex].value && (window.location = this.options[this.selectedIndex].value);">
		<option selected disabled><?php _e( 'Sort by', 'dwqa-at' ); ?></option>
		<option <?php selected( $sort, 'views' ) ?> value="<?php echo esc_url( add_query_arg( array( 'sort' => 'views' ) ) ) ?>"><?php _e( 'Views', 'dwqa-at' ) ?></option>
		<option <?php selected( $sort, 'answers' ) ?> value="<?php echo esc_url( add_query_arg( array( 'sort' => 'answers' ) ) ) ?>"><?php _e( 'Answers', 'dwqa-at' ); ?></option>
		<option <?php selected( $sort, 'votes' ) ?> value="<?php echo esc_url( add_query_arg( array( 'sort' => 'votes' ) ) ) ?>"><?php _e( 'Votes', 'dwqa-at' ) ?></option>
	</select>
</div>