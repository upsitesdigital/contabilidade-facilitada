<?php
/**
 * The template for displaying answer submit form
 *
 * @package DW Question & Answer
 * @since DW Question & Answer 1.1.8
 */
global $dwqa_general_settings;
?>
<form class="dwqa-user-profile">
	<?php
	$tab = dwqa_profile_tab();
	$user_id = dwqa_profile_displayed_user_id();
	$current_user_id = get_current_user_id();

		/* user commnet count */
		// function dwqa_get_comment_count( $user_ID ) {
		// 	global $wpdb;

		// 	$count = $wpdb->get_var(
		// 		$wpdb->prepare( "SELECT COUNT(comment_ID) FROM {$wpdb->comments} WHERE user_id = %d ", $user_ID )
		// 	);
		// 	return $count;
		// }
	?>
	<div class="dwqa-user-profile-head">
		<div class="dwqa-user-cover-image-wrap">
			<div id="dwqa-user-cover-image">
				<div class="cover-thumb-overlay"></div>
				<img src="<?php echo dwqa_get_cover_image_url($user_id);?>">
			</div>
			<?php if($user_id && $user_id == $current_user_id):?>
			<label for="dwqa-upload-user-cover-image"><?php _e('Edit Cover Image', 'dwqa'); ?></label>
			<input type="file" id="dwqa-upload-user-cover-image">
			<?php endif;?>
			<div style="clear:both;"></div>
		</div>
		<div class="dwqa-user-avatar-group">
			<div class="dwqa-user-avatar-wrap">
				<div id="dwqa-user-avatar">
					<?php if ( isset( $dwqa_general_settings['profileAvatar'] ) && $dwqa_general_settings['profileAvatar'] ) : ?>
						<img src="<?php echo dwqa_get_avatar_url($user_id);?>">
					<?php else :?>
						<?php printf( __( '<span>%1$s</span>', 'dwqa' ), get_avatar( $user_id) ) ?>
					<?php endif; ?>
				</div>
				<?php if($user_id && $user_id == $current_user_id && isset( $dwqa_general_settings['profileAvatar'] ) && $dwqa_general_settings['profileAvatar'] ) :?>
					<label for="dwqa-upload-user-avatar">Update</label>
					<input type="file" id="dwqa-upload-user-avatar">
				<?php endif;?>
			</div>
			<div style="clear:both;"></div>
		</div>
		<div class="dwqa-user-info">
			<h1 class="username"><?php echo dwqa_get_display_name($user_id);?></h1>
		</div>
	</div>
	<div class="dwqa-profile-tabs">
		<a href="<?php echo dwqa_profile_tab_url($user_id, 'questions');?>" class="<?php echo $tab =='questions'?'active':'';?>"><?php echo __('Questions', 'dwqa')?>(<?php echo dwqa_count_user_question($user_id);?>)</a>
		<a href="<?php echo dwqa_profile_tab_url($user_id, 'answers');?>" class="<?php echo $tab =='answers'?'active':'';?>"><?php echo __('Answers', 'dwqa')?>(<?php echo dwqa_count_user_answer($user_id);?>)</a>
		<!-- Custom comment/Blog tabs -->
		<a href="<?php echo dwqa_profile_tab_url($user_id, 'blogpost');?>" class="<?php echo $tab =='blogpost'?'active':'';?>"><?php echo __('Posts', 'dwqa')?>(<?php echo count_user_posts($user_id);?>)</a>
		<a href="<?php echo dwqa_profile_tab_url($user_id, 'comments');?>" class="<?php echo $tab =='comments'?'active':'';?>"><?php echo __('Comments', 'dwqa')?></a>
	</div>

	<div class="dwqa-user-qa">
		<?php
		if( $tab =='questions'){
			dwqa_profile_tab_questions();
		}
		if( $tab =='answers'){
			dwqa_profile_tab_answers();
		}
		// Custom comment/Blog tabs
		if( $tab =='blogpost'){
			dwqa_profile_blogposts();
		}
		if( $tab =='comments'){
			dwqa_profile_comments_post();
		}
		?>
	</div>

	<div class="dwqa-poup">
		<div id="popup-crop">

		</div>
		<div id="avatar-crop" style="width:150px; height: 150px;">

		</div>

		<input type="hidden" id="attachment_file" value="">
		<input type="hidden" id="crop-h" value="">
		<input type="hidden" id="crop-w" value="">
		<input type="hidden" id="crop-x" value="">
		<input type="hidden" id="crop-x2" value="">
		<input type="hidden" id="crop-y" value="">
		<input type="hidden" id="crop-y2" value="">

		<button type="button" id="button-crop"><?php _e('Crop', 'dwqa');?></button>
		<div style="clear:both;"></div>
	</div>



</form>
