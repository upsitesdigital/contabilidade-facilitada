<?php

// Callback for dwqa-general-settings Option
function dwqa_question_registration_setting_display() {
	global $dwqa_general_settings;
	?>
	<p><input type="checkbox" name="dwqa_options[answer-registration]" value="true" <?php checked( true, isset( $dwqa_general_settings['answer-registration'] ) ? (bool ) $dwqa_general_settings['answer-registration'] : false ); ?> id="dwqa_option_answer_registation">
	<label for="dwqa_option_answer_registation"><span class="description"><?php _e( 'Login required. No anonymous post allowed','dwqa' ); ?></span></label></p>
	<?php
}

function dwqa_pages_settings_display() {
	global $dwqa_general_settings;
	$archive_question_page = isset( $dwqa_general_settings['pages']['archive-question'] ) ? $dwqa_general_settings['pages']['archive-question'] : 0;
	?>
	<p>
		<?php
			wp_dropdown_pages( array(
				'name'              => 'dwqa_options[pages][archive-question]',
				'show_option_none'  => __( 'Select Archive Question Page','dwqa' ),
				'option_none_value' => 0,
				'selected'          => $archive_question_page,
			) );
		?><br><span class="description"><?php _e( 'A page where displays all questions. The <code>[dwqa-list-questions]</code> short code must be on this page.','dwqa' ) ?></span>
	</p>
	<?php
}

function dwqa_question_new_time_frame_display() {
	global  $dwqa_general_settings;
	echo '<p><input type="text" name="dwqa_options[question-new-time-frame]" id="dwqa_options_question_new_time_frame" value="'.( isset( $dwqa_general_settings['question-new-time-frame'] ) ? $dwqa_general_settings['question-new-time-frame'] : 4 ).'" class="small-text" /><span class="description"> '.__( 'hours','dwqa' ).'<span title="'.__( 'A period of time in which new questions are highlighted and marked as New','dwqa' ).'">( ? )</span></span></p>';
}

function dwqa_question_overdue_time_frame_display() {
	global  $dwqa_general_settings;
	echo '<p><input type="text" name="dwqa_options[question-overdue-time-frame]" id="dwqa_options_question_new_time_frame" value="'.( isset( $dwqa_general_settings['question-overdue-time-frame'] ) ? $dwqa_general_settings['question-overdue-time-frame'] : 2 ).'" class="small-text" /><span class="description"> '.__( 'days','dwqa' ).'<span title="'.__( 'A Question will be marked as overdue if it passes this period of time, starting from the time the question was submitted','dwqa' ).'">( ? )</span></span></p>';
}

function dwqa_submit_question_page_display(){
	global  $dwqa_general_settings;
	$submit_question_page = isset( $dwqa_general_settings['pages']['submit-question'] ) ? $dwqa_general_settings['pages']['submit-question'] : 0;
	?>
	<p>
		<?php
			wp_dropdown_pages( array(
				'name'              => 'dwqa_options[pages][submit-question]',
				'show_option_none'  => __( 'Select Submit Question Page','dwqa' ),
				'option_none_value' => 0,
				'selected'          => $submit_question_page,
			) );
		?><br>
		<span class="description"><?php _e( 'A page where users can submit questions. The <code>[dwqa-submit-question-form]</code> short code must be on this page.','dwqa' ) ?></span>
	</p>
	<?php
}

function dwqa_set_register_page_display() {
	global $dwqa_general_settings;

	$register_page = isset( $dwqa_general_settings['pages']['register-page'] ) ? $dwqa_general_settings['pages']['register-page'] : 0;
	?>
	<p>
		<?php
			wp_dropdown_pages( array(
				'name'              => 'dwqa_options[pages][register-page]',
				'show_option_none'  => __( 'Default','dwqa' ),
				'option_none_value' => '0',
				'selected'          => $register_page,
			) );
		?>
	</p>
	<?php
}

function dwqa_set_thank_page_display() {
	global $dwqa_general_settings;

	$thank_page = isset( $dwqa_general_settings['pages']['thank-page'] ) ? $dwqa_general_settings['pages']['thank-page'] : 0;
	?>
	<p>
		<?php
			wp_dropdown_pages( array(
				'name'              => 'dwqa_options[pages][thank-page]',
				'show_option_none'  => __( 'Default','dwqa' ),
				'option_none_value' => '0',
				'selected'          => $thank_page,
			) );
		?><br>
		<span class="description"><?php _e( 'Redirect to a Thank page when user submit a success question.  ','dwqa' ) ?></span>
	</p>
	<?php
}

function dwqa_set_profile_page_display() {
	global $dwqa_general_settings;

	$profile_page = isset( $dwqa_general_settings['pages']['user-profile'] ) ? $dwqa_general_settings['pages']['user-profile'] : 0;
	?>
	<p>
		<?php
			wp_dropdown_pages( array(
				'name'              => 'dwqa_options[pages][user-profile]',
				'show_option_none'  => __( 'Select Profile Page','dwqa' ),
				'option_none_value' => '0',
				'selected'          => $profile_page,
			) );
		?>
	</p>
	<?php
}

function dwqa_404_page_display(){
	global  $dwqa_general_settings;
	$submit_question_page = isset( $dwqa_general_settings['pages']['404'] ) ? $dwqa_general_settings['pages']['404'] : 0;
	?>
	<p>
		<?php
			wp_dropdown_pages( array(
				'name'              => 'dwqa_options[pages][404]',
				'show_option_none'  => __( 'Select 404 DWQA Page','dwqa' ),
				'option_none_value' => 0,
				'selected'          => $submit_question_page,
			) );
		?>
		<span class="description"><?php _e( 'This page will be redirected when users without authority click on a private question. You can customize the message of this page in.If not, a default 404 page will be used.','dwqa' ) ?></span>
	</p>
	<?php
}
function dwqa_email_template_settings_display(){
	global $dwqa_options;
	$editor_content = isset( $dwqa_options['subscribe']['email-template'] ) ? $dwqa_options['subscribe']['email-template'] : '';
	wp_editor( $editor_content, 'dwqa_email_template_editor', array(
		'textarea_name' => 'dwqa_options[subscribe][email-template]'
	) );
}


function dwqa_subscrible_email_logo_display(){
	wp_enqueue_media();
	?>
	<div class="uploader">
		<p><input type="text" name="dwqa_subscrible_email_logo" id="dwqa_subscrible_email_logo" class="regular-text" value="<?php echo  get_option( 'dwqa_subscrible_email_logo' ); ?>" />&nbsp;<input type="button" class="button" name="dwqa_subscrible_email_logo_button" id="dwqa_subscrible_email_logo_button" value="<?php _e( 'Upload','dwqa' ) ?>" /></br><span class="description">&nbsp;<?php _e( 'Upload or choose a logo to be displayed at the top of the email.','dwqa' ) ?></span></p>
	</div>
	<script type="text/javascript">
	jQuery( document ).ready(function($ ){
	  var _custom_media = true,
		  _orig_send_attachment = wp.media.editor.send.attachment;

	  $( '#dwqa_subscrible_email_logo_button' ).click(function(e ) {
		var send_attachment_bkp = wp.media.editor.send.attachment;
		var button = $( this );
		var id = button.attr( 'id' ).replace('_button', '' );
		_custom_media = true;
		wp.media.editor.send.attachment = function( props, attachment ){
		  if ( _custom_media ) {
			$( "#"+id ).val(attachment.url );

			if ( $( "#"+id ).closest( '.uploader' ).find('.logo-preview' ).length > 0 ) {
				$( "#"+id ).closest( '.uploader' ).find('.logo-preview img' ).attr( 'src', attachment.url );
			}else {
				$( "#"+id ).closest( '.uploader' ).append('<p class="logo-preview"><img src="'+attachment.url+'"></p>' )
			}
		  } else {
			return _orig_send_attachment.apply( this, [props, attachment] );
		  };
		}

		wp.media.editor.open( button );
		return false;
	  } );

	  $( '.add_media' ).on('click', function(){
		_custom_media = false;
	  } );
	} );
	</script>
	<?php
}

function dwqa_subscrible_enable_new_question_notification(){
	echo '<th>'.__( 'Enable?','dwqa' ).'</th><td><input type="checkbox" value="1" '.checked( 1, get_option( 'dwqa_subscrible_enable_new_question_notification', 1 ), false ).' name="dwqa_subscrible_enable_new_question_notification" id="dwqa_subscrible_enable_new_question_notification" ><span class="description">'.__( 'Enable notification for new question.', 'dwqa' ).'</span></td>';
}
// New Question - Enable Notification

function dwqa_subscrible_new_question_email_subject_display(){
	echo '<th>'.__( 'Email subject','dwqa' ).'</th><td><input type="text" id="dwqa_subscrible_new_question_email_subject" name="dwqa_subscrible_new_question_email_subject" value="'.get_option( 'dwqa_subscrible_new_question_email_subject' ).'" class="regular-text" /></span></td>';
}
// New Question - Email subject

function dwqa_subscrible_new_question_email_display(){
	echo '<th for="dwqa_subscrible_new_question_email">'.__( 'Email Content','dwqa' ).'</th>';
	echo '<td>';
	$content = dwqa_get_mail_template( 'dwqa_subscrible_new_question_email', 'new-question' );
	wp_editor( $content, 'dwqa_subscrible_new_question_email', array(
		'wpautop'   => false,
		'tinymce' => array( 'content_css' => DWQA_URI . 'assets/css/email-template-editor.css' ),
	) );
	echo '<p><input data-template="new-question.html" type="button" class="button dwqa-reset-email-template" value="Reset Template"></p>';
	echo '<div class="description">
		Enter the email that is sent to Administrator when have new question on your site. HTML is accepted. Available template settings:<br>
		<strong>{site_logo}</strong> - Your site logo. <br />
		<strong>{site_name}</strong> - Your site name. <br />
		<strong>{user_avatar}</strong> - Question Author Avatar. <br />
		<strong>{username}</strong> - Question Author Name. <br />
		<strong>{user_link}</strong> - Question Author Posts Link.<br />
		<strong>{question_title}</strong> - Question Title. <br />
		<strong>{question_link}</strong> - Question Link. <br />
		<strong>{question_content}</strong> - Question Content. <br />
	</div>';
	echo '</td>';
}
// New Question - Email Content


function dwqa_subscrible_enable_new_answer_notification(){
	echo '<th>'.__( 'Enable?','dwqa' ).'</th><td><input type="checkbox" value="1" '.checked( 1, get_option( 'dwqa_subscrible_enable_new_answer_notification', 1 ), false ).' name="dwqa_subscrible_enable_new_answer_notification" id="dwqa_subscrible_enable_new_answer_notification" ><span class="description">'.__( 'Enable notification for new answer.', 'dwqa' ).'</span></td>';
}
// New Answer - Enable Notification

function dwqa_subscrible_new_answer_email_subject_display(){
	echo '<th>'.__( 'Email subject','dwqa' ).'</th><td><input type="text" id="dwqa_subscrible_new_answer_email_subject" name="dwqa_subscrible_new_answer_email_subject" value="'.get_option( 'dwqa_subscrible_new_answer_email_subject' ).'" class="regular-text" /></span></td>';
}
// New Answer - Email Subject

function dwqa_subscrible_new_answer_email_display(){
	echo '<th>'.__( 'Email Content','dwqa' ).'</th>';
	echo '<td>';
	$content = dwqa_get_mail_template( 'dwqa_subscrible_new_answer_email', 'new-answer' );
	wp_editor( $content, 'dwqa_subscrible_new_answer_email', array(
		'wpautop'   => false,
		'tinymce' => array( 'content_css' => DWQA_URI . 'assets/css/email-template-editor.css' ),
	) );
	echo '<p><input data-template="new-answer.html" type="button" class="button dwqa-reset-email-template" value="Reset Template"></p>';
	echo '<div class="description">
		Enter the email that is sent to Administrator when have new answer on your site. HTML is accepted. Available template settings:<br>
		<strong>{site_logo}</strong> - Your site logo. <br />
		<strong>{site_name}</strong> - Your site name. <br />
		<strong>{site_description}</strong> - Your site description. <br />
		<strong>{answer_avatar}</strong> - Answer Author Avatar. <br />
		<strong>{answer_author}</strong> - Answer Author Name. <br />
		<strong>{answer_author_link}</strong> - Answer Author Link. <br />
		<strong>{question_title}</strong> - Question Title. <br />
		<strong>{question_link}</strong> - Question Link. <br />
		<strong>{answer_content}</strong> - Answer Content. <br />

	</div>';
	echo '</td>';
}
// New Answer - Email Content

function dwqa_subscrible_enable_new_answer_followers_notification(){
	echo '<th>'.__( 'Enable?','dwqa' ).'</th><td><input type="checkbox" value="1" '.checked( 1, get_option( 'dwqa_subscrible_enable_new_answer_followers_notification', 1 ), false ).' name="dwqa_subscrible_enable_new_answer_followers_notification" id="dwqa_subscrible_enable_new_answer_followers_notification" ><span class="description">'.__( 'Enable notification for new answer ( to Followers ).', 'dwqa' ).'</span></td>';
}
// New Answer - Follow - Enable Notification

function dwqa_subscrible_new_answer_followers_email_subject_display(){
	echo '<th>'.__( 'Email subject','dwqa' ).'</th><td><input type="text" id="dwqa_subscrible_new_answer_followers_email_subject" name="dwqa_subscrible_new_answer_followers_email_subject" value="'.get_option( 'dwqa_subscrible_new_answer_followers_email_subject', __( '[{site_name}] You have a new answer for your followed question', 'dwqa' ) ).'" class="regular-text" /></span></td>';
}
// New Answer - Follow - Email Subject

function dwqa_subscrible_new_answer_followers_email_display(){
	echo '<th>'.__( 'Email Content','dwqa' ).'</th>';
	echo '<td>';
	$content = dwqa_get_mail_template( 'dwqa_subscrible_new_answer_followers_email', 'new-answer-followers' );
	wp_editor( $content, 'dwqa_subscrible_new_answer_followers_email', array(
		'wpautop'   => false,
		'tinymce' => array( 'content_css' => DWQA_URI . 'assets/css/email-template-editor.css' ),
	) );
	echo '<p><input data-template="new-answer-followers.html" type="button" class="button dwqa-reset-email-template" value="Reset Template"></p>';
	echo '<div class="description">
		Enter the email that is sent to Administrator when have new answer on your site. HTML is accepted. Available template settings:<br>
		<strong>{site_logo}</strong> - Your site logo. <br />
		<strong>{site_name}</strong> - Your site name. <br />
		<strong>{site_description}</strong> - Your site description. <br />
		<strong>{answer_avatar}</strong> - Answer Author Avatar. <br />
		<strong>{answer_author}</strong> - Answer Author Name. <br />
		<strong>{answer_author_link}</strong> - Answer Author Link. <br />
		<strong>{question_title}</strong> - Question Title. <br />
		<strong>{question_link}</strong> - Question Link. <br />
		<strong>{answer_content}</strong> - Answer Content. <br />

	</div>';
	echo '</td>';
}
// New Answer - Follow - Email Content

function dwqa_subscrible_enable_new_comment_question_notification(){
	echo '<th>'.__( 'Enable?','dwqa' ).'</th><td><input type="checkbox" '.checked( 1, get_option( 'dwqa_subscrible_enable_new_comment_question_notification', 1 ), false ).' value="1" name="dwqa_subscrible_enable_new_comment_question_notification" id="dwqa_subscrible_enable_new_comment_question_notification" ><span class="description">'.__( 'Enable notification for new comment of question.', 'dwqa' ).'</span></td>';
}
// New Comment - Question - Enable Notification

function dwqa_subscrible_new_comment_question_email_subject_display(){
	echo '<th>'.__( 'Email subject','dwqa' ).'</th><td><input type="text" id="dwqa_subscrible_new_comment_question_email_subject" name="dwqa_subscrible_new_comment_question_email_subject" value="'.get_option( 'dwqa_subscrible_new_comment_question_email_subject' ).'" class="regular-text" /></td>';
}
// New Comment - Question - Email subject

function dwqa_subscrible_new_comment_question_email_display(){
	echo '<th>'.__( 'Email Content','dwqa' ).'</th><td>';
	$content = dwqa_get_mail_template( 'dwqa_subscrible_new_comment_question_email', 'new-comment-question' );
	wp_editor( $content, 'dwqa_subscrible_new_comment_question_email', array(
		'wpautop'   => false,
		'tinymce' => array( 'content_css' => DWQA_URI . 'assets/css/email-template-editor.css' ),
	) );
	echo '<p><input data-editor="dwqa_subscrible_new_comment_question_email" data-template="new-comment-question.html" type="button" class="button dwqa-reset-email-template" value="Reset Template"></p>';
	echo '<div class="description">
		Enter the email that is sent to Administrator when have new answer on your site. HTML is accepted. Available template settings:<br>
		<strong>{site_logo}</strong> - Your site logo. <br />
		<strong>{site_name}</strong> - Your site name. <br />
		<strong>{site_description}</strong> - Your site description. <br />
		<strong>{question_author}</strong> - Question Author Name. <br />
		<strong>{comment_author}</strong> - Comment Author Name. <br />
		<strong>{comment_author_avatar}</strong> - Comment Author Avatar. <br />
		<strong>{comment_author_link}</strong> - Comment Author Link. <br />
		<strong>{question_title}</strong> - Question Title. <br />
		<strong>{question_link}</strong> - Question Link. <br />
		<strong>{comment_content}</strong> - Comment Content. <br />
	</div>';
	echo '</td>';
}
// New Comment - Question - Email Content

function dwqa_subscrible_enable_new_comment_question_followers_notification(){
	echo '<th>'.__( 'Enable?','dwqa' ).'</th><td><input type="checkbox" '.checked( 1, get_option( 'dwqa_subscrible_enable_new_comment_question_followers_notify', 1 ), false ).' value="1" name="dwqa_subscrible_enable_new_comment_question_followers_notify" id="dwqa_subscrible_enable_new_comment_question_followers_notify" ><span class="description">'.__( 'Enable notification for new comment of question.', 'dwqa' ).'</span></td>';
}
// New Comment - Question - Follow - Enable Notification

function dwqa_subscrible_new_comment_question_followers_email_subject_display(){
	echo '<th>'.__( 'Email subject','dwqa' ).'</th><td><input type="text" id="dwqa_subscrible_new_comment_question_followers_email_subject" name="dwqa_subscrible_new_comment_question_followers_email_subject" value="'.get_option( 'dwqa_subscrible_new_comment_question_followers_email_subject' ).'" class="widefat" /></td>';
}
// New Comment - Question - Follow - Email subject

function dwqa_subscrible_new_comment_question_followers_email_display(){
	echo '<th>'.__( 'Email Content','dwqa' ).'</th><td>';
	$content = dwqa_get_mail_template( 'dwqa_subscrible_new_comment_question_followers_email', 'new-comment-question-followers' );
	wp_editor( $content, 'dwqa_subscrible_new_comment_question_followers_email', array(
		'wpautop'   => false,
		'tinymce' => array( 'content_css' => DWQA_URI . 'assets/css/email-template-editor.css' ),
	) );
	echo '<p><input data-template="new-comment-question-followers.html" type="button" class="button dwqa-reset-email-template" value="Reset Template"></p>';
	echo '<div class="description">
		Enter the email that is sent to Administrator when have new answer on your site. HTML is accepted. Available template settings:<br>
		<strong>{site_logo}</strong> - Your site logo. <br />
		<strong>{site_name}</strong> - Your site name. <br />
		<strong>{site_description}</strong> - Your site description. <br />
		<strong>{question_author}</strong> - Question Author Name. <br />
		<strong>{comment_author}</strong> - Comment Author Name. <br />
		<strong>{comment_author_avatar}</strong> - Comment Author Avatar. <br />
		<strong>{comment_author_link}</strong> - Comment Author Link. <br />
		<strong>{question_title}</strong> - Question Title. <br />
		<strong>{question_link}</strong> - Question Link. <br />
		<strong>{comment_content}</strong> - Comment Content. <br />
	</div>';
	echo '</td>';
}
// New Comment - Question - Follow - Email Content

function dwqa_subscrible_enable_new_comment_answer_notification(){
	echo '<th>'.__( 'Enable?','dwqa' ).'</th><td><input type="checkbox" '.checked( 1, get_option( 'dwqa_subscrible_enable_new_comment_answer_notification', 1 ), false ).' value="1" name="dwqa_subscrible_enable_new_comment_answer_notification" id="dwqa_subscrible_enable_new_comment_answer_notification" ><span class="description">'.__( 'Enable notification for new comment of answer.', 'dwqa' ).'</span></td>';
}
// New Comment - Answer - Enable Notification

function dwqa_subscrible_new_comment_answer_email_subject_display(){
	echo '<th>'.__( 'Email subject','dwqa' ).'</th><td><input type="text" id="dwqa_subscrible_new_comment_answer_email_subject" name="dwqa_subscrible_new_comment_answer_email_subject" value="'.get_option( 'dwqa_subscrible_new_comment_answer_email_subject' ).'" class="regular-text" /></td>';
}
// New Comment - Answer - Email Subject

function dwqa_subscrible_new_comment_answer_email_display(){
	echo '<th>'.__( 'Email Content','dwqa' ).'</th><td>';
	$content = dwqa_get_mail_template( 'dwqa_subscrible_new_comment_answer_email', 'new-comment-answer' );
	wp_editor( $content, 'dwqa_subscrible_new_comment_answer_email', array(
		'wpautop'   => false,
		'tinymce' => array( 'content_css' => DWQA_URI . 'assets/css/email-template-editor.css' ),
	) );
	echo '<p><input data-template="new-comment-answer.html" type="button" class="button dwqa-reset-email-template" value="Reset Template"></p>';
	echo '<div class="description">
		Enter the email that is sent to Administrator when have new answer on your site. HTML is accepted. Available template settings:<br>
		<strong>{site_logo}</strong> - Your site logo. <br />
		<strong>{site_name}</strong> - Your site name. <br />
		<strong>{site_description}</strong> - Your site description. <br />
		<strong>{answer_author}</strong> - Answer Author Name. <br />
		<strong>{comment_author}</strong> - Comment Author Name. <br />
		<strong>{comment_author_avatar}</strong> - Comment Author Avatar. <br />
		<strong>{comment_author_link}</strong> - Comment Author Link. <br />
		<strong>{question_title}</strong> - Question Title. <br />
		<strong>{question_link}</strong> - Question Link. <br />
		<strong>{comment_content}</strong> - Comment Content. <br />
	</div>';
	echo '</td>';
}
// New Comment - Answer - Email Content

function dwqa_subscrible_enable_new_comment_answer_followers_notification(){
	echo '<th>'.__( 'Enable?','dwqa' ).'</th><td><input type="checkbox" '.checked( 1, get_option( 'dwqa_subscrible_enable_new_comment_answer_followers_notification', 1 ), false ).' value="1" name="dwqa_subscrible_enable_new_comment_answer_followers_notification" id="dwqa_subscrible_enable_new_comment_answer_followers_notification" ><span class="description">'.__( 'Enable notification for new comment of answer.', 'dwqa' ).'</span></td>';
}
// New Comment - Answer - Follow - Enable Notification

function dwqa_subscrible_new_comment_answer_followers_email_subject_display(){
	echo '<th>'.__( 'Email subject','dwqa' ).'</th><td><input type="text" id="dwqa_subscrible_new_comment_answer_followers_email_subject" name="dwqa_subscrible_new_comment_answer_followers_email_subject" value="'.get_option( 'dwqa_subscrible_new_comment_answer_followers_email_subject' ).'" class="regular-text" /></td>';
}
// New Comment - Answer - Follow - Email Subject

function dwqa_subscrible_new_comment_answer_followers_email_display(){
	echo '<th>'.__( 'Email Content','dwqa' ).'</th><td>';
	$content = dwqa_get_mail_template( 'dwqa_subscrible_new_comment_answer_followers_email', 'new-comment-answer-followers' );
	wp_editor( $content, 'dwqa_subscrible_new_comment_answer_followers_email', array(
		'wpautop'   => false,
		'tinymce' => array( 'content_css' => DWQA_URI . 'assets/css/email-template-editor.css' ),
	) );
	echo '<p><input data-template="new-comment-answer-followers.html" type="button" class="button dwqa-reset-email-template" value="Reset Template"></p>';
	echo '<div class="description">
		Enter the email that is sent to Administrator when have new answer on your site. HTML is accepted. Available template settings:<br>
		<strong>{site_logo}</strong> - Your site logo. <br />
		<strong>{site_name}</strong> - Your site name. <br />
		<strong>{site_description}</strong> - Your site description. <br />
		<strong>{answer_author}</strong> - Answer Author Name. <br />
		<strong>{comment_author}</strong> - Comment Author Name. <br />
		<strong>{comment_author_avatar}</strong> - Comment Author Avatar. <br />
		<strong>{comment_author_link}</strong> - Comment Author Link. <br />
		<strong>{question_title}</strong> - Question Title. <br />
		<strong>{question_link}</strong> - Question Link. <br />
		<strong>{comment_content}</strong> - Comment Content. <br />
	</div>';
	echo '</td>';
}
// New Comment - Answer - Follow - Email Content

// End email setting html

function dwqa_question_rewrite_display(){
	global  $dwqa_general_settings;
	echo '<p><input type="text" name="dwqa_options[question-rewrite]" id="dwqa_options_question_rewrite" value="'.( isset( $dwqa_general_settings['question-rewrite'] ) ? $dwqa_general_settings['question-rewrite'] : 'question' ).'" class="regular-text" /></p>';
}

function dwqa_question_category_rewrite_display(){
	global  $dwqa_general_settings;
	echo '<p><input type="text" name="dwqa_options[question-category-rewrite]" id="dwqa_options_question_category_rewrite" value="'.( isset( $dwqa_general_settings['question-category-rewrite'] ) ? $dwqa_general_settings['question-category-rewrite'] : 'question-category' ).'" class="regular-text" /></p>';
}

function dwqa_question_tag_rewrite_display(){
	global  $dwqa_general_settings;
	echo '<p><input type="text" name="dwqa_options[question-tag-rewrite]" id="dwqa_options_question_tag_rewrite" value="'.( isset( $dwqa_general_settings['question-tag-rewrite'] ) ? $dwqa_general_settings['question-tag-rewrite'] : 'question-tag' ).'" class="regular-text" /></p>';
}

// function dwqa_question_slug_category_rewrite_display(){
// 	global $dwqa_general_settings;

// 	echo '<p><input type="checkbox" name="dwqa_options[question-slug-category-rewrite]"  id="dwqa_options_question_slug_category_rewrite" value="1" '.checked( 1, (isset($dwqa_general_settings['question-slug-category-rewrite'] ) ? $dwqa_general_settings['question-slug-category-rewrite'] : false ) , false ) .'><span class="description">'.__( 'Enable categories slug first single question link.','dwqa' ).'</span></p>';
// }

function dwqa_question_permalink(){
	global $dwqa_general_settings, $dwqa;
	global $wp_rewrite;



	$array_permalink = $dwqa->question->get_array_permalink();

	$checked = reset($array_permalink);
	if(isset($dwqa_general_settings['question-permalink'])){
		$checked = $dwqa_general_settings['question-permalink'];
	}

	echo '
	<table class="form-table permalink-structure">
		<tbody>';

	foreach($array_permalink as $item_key => $item_value){
		echo '<tr>
				<td><input name="dwqa_options[question-permalink]" type="radio" value="'.$item_key.'" '.($item_key == $checked?'checked':'').'></td>
				<td><code>'.$item_value['sample_permalink'].'</code></td>
			</tr>';

	}

	echo '
		</tbody>
	</table>';
}

function dwqa_permission_display(){
	global $dwqa;
	$perms = $dwqa->permission->perms;
	$roles = get_editable_roles();
	?>
	<div class="dwqa-important-notice"><strong>IMPORTANT NOTE:</strong> In this tab, please uncheck the edit and delete for the normal user roles, just enable for the user role that you want to have permission to edit and delete all the questions, answers, or comments. Usually, it will be enabled for the admin role.</div>
	<input type="hidden" id="reset-permission-nonce" name="reset-permission-nonce" value="<?php echo wp_create_nonce( '_dwqa_reset_permission' ); ?>">
	<h3><?php _e( 'Questions','dwqa' ) ?></h3>
	<table class="table widefat dwqa-permission-settings">
		<thead>
			<tr>
				<th width="20%"></th>
				<th><?php _e( 'Read','dwqa' ) ?></th>
				<th><?php _e( 'Post','dwqa' ) ?></th>
				<th><?php _e( 'Edit','dwqa' ) ?></th>
				<th><?php _e( 'Delete','dwqa' ) ?></th>
				<th><?php _e( 'Manage', 'dwqa' ) ?></th>
			</tr>
		</thead>
		<tbody>
		<?php foreach ( $roles as $key => $role ) : ?>
			<?php if ( $key == 'anonymous' ) continue; ?>
			<tr class="group available">
				<td><?php echo $roles[$key]['name'] ?></td>
				<td><input type="checkbox" <?php checked( true, ( isset( $perms[$key]['question']['read'] ) ? $perms[$key]['question']['read'] : false ) ); ?> name="dwqa_permission[<?php echo $key ?>][question][read]" value="1"></td>
				<td><input type="checkbox" <?php checked( true, ( isset( $perms[$key]['question']['post'] ) ? $perms[$key]['question']['post'] : false ) ); ?> name="dwqa_permission[<?php echo $key ?>][question][post]" value="1"></td>
				<td><input type="checkbox" <?php checked( true, ( isset( $perms[$key]['question']['edit'] ) ? $perms[$key]['question']['edit'] : false ) ); ?> name="dwqa_permission[<?php echo $key ?>][question][edit]" value="1"></td>
				<td><input type="checkbox" <?php checked( true, ( isset( $perms[$key]['question']['delete'] ) ? $perms[$key]['question']['delete'] : false ) ); ?> name="dwqa_permission[<?php echo $key ?>][question][delete]" value="1"></td>
				<td><input type="checkbox" <?php checked( true, ( isset( $perms[$key]['question']['manage'] ) ? $perms[$key]['question']['manage'] : false ) ); ?> name="dwqa_permission[<?php echo $key ?>][question][manage]" value="1"></td>

			</tr>
		<?php endforeach; ?>
			<tr class="group available">
				<td><?php _e( 'Anonymous','dwqa' ) ?></td>

				<td><input type="checkbox" <?php checked( true, ( isset( $perms['anonymous']['question']['read'] ) ? $perms['anonymous']['question']['read'] : false ) ); ?> name="dwqa_permission[<?php echo 'anonymous' ?>][question][read]" value="1"></td>
				<td><input type="checkbox" <?php checked( true, ( isset( $perms['anonymous']['question']['post'] ) ? $perms['anonymous']['question']['post'] : false ) ); ?> name="dwqa_permission[<?php echo 'anonymous' ?>][question][post]" value="1"></td>
				<td><input type="checkbox" <?php checked( true, ( isset( $perms['anonymous']['question']['edit'] ) ? $perms['anonymous']['question']['edit'] : false ) ); ?> name="dwqa_permission[<?php echo 'anonymous' ?>][question][edit]" value="1" disabled="disabled"></td>
				<td><input type="checkbox" <?php checked( true, ( isset( $perms['anonymous']['question']['delete'] ) ? $perms['anonymous']['question']['delete'] : false ) ); ?> name="dwqa_permission[<?php echo 'anonymous' ?>][question][delete]" value="1" disabled="disabled"></td>
				<td><input type="checkbox" <?php checked( true, ( isset( $perms['anonymous']['question']['manage'] ) ? $perms['anonymous']['question']['manage'] : false ) ); ?> name="dwqa_permission[<?php echo 'anonymous' ?>][question][manage]" value="1" disabled="disabled"></td>
			</tr>
		</tbody>
	</table>
	<p class="reset-button-container align-right" style="text-align:right">
		<button data-type="question" class="button reset-permission" name="dwqa-permission-reset" value="question"><?php _e( 'Reset Default', 'dwqa' ); ?></button>
	</p>
	<h3><?php _e( 'Answers', 'dwqa' ); ?></h3>
	<table class="table widefat dwqa-permission-settings">
		<thead>
			<tr>
				<th width="20%"></th>
				<th><?php _e( 'Read', 'dwqa' ) ?></th>
				<th><?php _e( 'Post', 'dwqa' ) ?></th>
				<th><?php _e( 'Edit', 'dqwa' ) ?></th>
				<th><?php _e( 'Delete', 'dwqa' ) ?></th>
				<th><?php _e( 'Manage', 'dwqa' ) ?></th>
			</tr>
		</thead>
		<tbody>
		<?php foreach ( $roles as $key => $role ) : ?>
			<?php if ( $key == 'anonymous' ) continue; ?>
			<tr class="group available">
				<td><?php echo $roles[$key]['name'] ?></td>
				<td><input type="checkbox" <?php checked( true, ( isset( $perms[$key]['answer']['read'] ) ? $perms[$key]['answer']['read'] : false ) ); ?> name="dwqa_permission[<?php echo $key ?>][answer][read]" value="1"></td>
				<td><input type="checkbox" <?php checked( true, ( isset( $perms[$key]['answer']['post'] ) ? $perms[$key]['answer']['post'] : false ) ); ?> name="dwqa_permission[<?php echo $key ?>][answer][post]" value="1"></td>
				<td><input type="checkbox" <?php checked( true, ( isset( $perms[$key]['answer']['edit'] ) ? $perms[$key]['answer']['edit'] : false ) ); ?> name="dwqa_permission[<?php echo $key ?>][answer][edit]" value="1"></td>
				<td><input type="checkbox" <?php checked( true, ( isset( $perms[$key]['answer']['delete'] ) ? $perms[$key]['answer']['delete'] : false ) ); ?> name="dwqa_permission[<?php echo $key ?>][answer][delete]" value="1"></td>
				<td><input type="checkbox" <?php checked( true, ( isset( $perms[$key]['answer']['manage'] ) ? $perms[$key]['answer']['manage'] : false ) ); ?> name="dwqa_permission[<?php echo $key ?>][answer][manage]" value="1"></td>

			</tr>
		<?php endforeach; ?>
			<tr class="group available">
				<td><?php _e( 'Anonymous','dwqa' ) ?></td>

				<td><input type="checkbox" <?php checked( true, ( isset( $perms['anonymous']['answer']['read'] ) ? $perms['anonymous']['answer']['read'] : false ) ); ?> name="dwqa_permission[<?php echo 'anonymous' ?>][answer][read]" value="1"></td>
				<td><input type="checkbox" <?php checked( true, ( isset( $perms['anonymous']['answer']['post'] ) ? $perms['anonymous']['answer']['post'] : false ) ); ?> name="dwqa_permission[<?php echo 'anonymous' ?>][answer][post]" value="1"></td>
				<td><input type="checkbox" <?php checked( true, ( isset( $perms['anonymous']['answer']['edit'] ) ? $perms['anonymous']['answer']['edit'] : false ) ); ?> name="dwqa_permission[<?php echo 'anonymous' ?>][answer][edit]" value="1" disabled="disabled"></td>
				<td><input type="checkbox" <?php checked( true, ( isset( $perms['anonymous']['answer']['delete'] ) ? $perms['anonymous']['answer']['delete'] : false ) ); ?> name="dwqa_permission[<?php echo 'anonymous' ?>][answer][delete]" value="1" disabled="disabled"></td>
				<td><input type="checkbox" <?php checked( true, ( isset( $perms['anonymous']['answer']['manage'] ) ? $perms['anonymous']['answer']['manage'] : false ) ); ?> name="dwqa_permission[<?php echo 'anonymous' ?>][answer][manage]" value="1" disabled="disabled"></td>
			</tr>
		</tbody>
	</table>
	<p class="reset-button-container align-right" style="text-align:right">
		<button data-type="answer" class="button reset-permission" name="dwqa-permission-reset" value="answer"><?php _e( 'Reset Default', 'dwqa' ); ?></button>
	</p>
	<h3><?php _e( 'Comments','dwqa' ) ?></h3>
	<table class="table widefat dwqa-permission-settings">
		<thead>
			<tr>
				<th width="20%"></th>
				<th><?php _e( 'Read', 'dwqa' ) ?></th>
				<th><?php _e( 'Post', 'dqwa' ) ?></th>
				<th><?php _e( 'Edit', 'dwqa' ) ?></th>
				<th><?php _e( 'Delete', 'dwqa' ) ?></th>
				<th><?php _e( 'Manage', 'dwqa' ) ?></th>
			</tr>
		</thead>
		<tbody>
		<?php foreach ( $roles as $key => $role ) : ?>
			<?php if ( $key == 'anonymous' ) continue; ?>
			<tr class="group available">
				<td><?php echo $roles[$key]['name'] ?></td>
				<td><input type="checkbox" <?php checked( true, ( isset( $perms[$key]['comment']['read'] ) ? $perms[$key]['comment']['read'] : false ) ); ?> name="dwqa_permission[<?php echo $key ?>][comment][read]" value="1"></td>
				<td><input type="checkbox" <?php checked( true, ( isset( $perms[$key]['comment']['post'] ) ? $perms[$key]['comment']['post'] : false ) ); ?> name="dwqa_permission[<?php echo $key ?>][comment][post]" value="1"></td>
				<td><input type="checkbox" <?php checked( true, ( isset( $perms[$key]['comment']['edit'] ) ? $perms[$key]['comment']['edit'] : false ) ); ?> name="dwqa_permission[<?php echo $key ?>][comment][edit]" value="1"></td>
				<td><input type="checkbox" <?php checked( true, ( isset( $perms[$key]['comment']['delete'] ) ? $perms[$key]['comment']['delete'] : false ) ); ?> name="dwqa_permission[<?php echo $key ?>][comment][delete]" value="1"></td>
				<td><input type="checkbox" <?php checked( true, ( isset( $perms[$key]['comment']['manage'] ) ? $perms[$key]['comment']['manage'] : false ) ); ?> name="dwqa_permission[<?php echo $key ?>][comment][manage]" value="1"></td>
			</tr>
		<?php endforeach; ?>
			<tr class="group available">
				<td><?php _e( 'Anonymous','dwqa' ) ?></td>
				<td><input type="checkbox" <?php checked( true, ( isset( $perms['anonymous']['comment']['read'] ) ? $perms['anonymous']['comment']['read'] : false ) ); ?> name="dwqa_permission[<?php echo 'anonymous' ?>][comment][read]" value="1"></td>
				<td><input type="checkbox" <?php checked( true, ( isset( $perms['anonymous']['comment']['post'] ) ? $perms['anonymous']['comment']['post'] : false ) ); ?> name="dwqa_permission[<?php echo 'anonymous' ?>][comment][post]" value="1"></td>
				<td><input type="checkbox" <?php checked( true, ( isset( $perms['anonymous']['comment']['edit'] ) ? $perms['anonymous']['comment']['edit'] : false ) ); ?> name="dwqa_permission[<?php echo 'anonymous' ?>][comment][edit]" value="1" disabled="disabled"></td>
				<td><input type="checkbox" <?php checked( true, ( isset( $perms['anonymous']['comment']['delete'] ) ? $perms['anonymous']['comment']['delete'] : false ) ); ?> name="dwqa_permission[<?php echo 'anonymous' ?>][comment][delete]" value="1" disabled="disabled"  ></td>
				<td><input type="checkbox" <?php checked( true, ( isset( $perms['anonymous']['comment']['manage'] ) ? $perms['anonymous']['comment']['manage'] : false ) ); ?> name="dwqa_permission[<?php echo 'anonymous' ?>][comment][manage]" value="1" disabled="disabled"  ></td>
			</tr>
		</tbody>
	</table>

	<p class="reset-button-container align-right" style="text-align:right">
		<button data-type="comment" class="button reset-permission" name="dwqa-permission-reset" value="comment"><?php _e( 'Reset Default', 'dwqa' ); ?></button>
	</p>
	<?php
}

//Captcha
function dwqa_captcha_in_question_display() {
	global $dwqa_general_settings;

	echo '<p><input type="checkbox" name="dwqa_options[captcha-in-question]"  id="dwqa_options_captcha_in_question" value="1" '.checked( 1, (isset($dwqa_general_settings['captcha-in-question'] ) ? $dwqa_general_settings['captcha-in-question'] : false ) , false ) .'><span class="description">'.__( 'Enable captcha on submit question page.','dwqa' ).'</span></p>';
}

function dwqa_captcha_in_single_question_display() {
	global $dwqa_general_settings;

	echo '<p><input type="checkbox" name="dwqa_options[captcha-in-single-question]"  id="dwqa_options_captcha_in_question" value="1" '.checked( 1, (isset($dwqa_general_settings['captcha-in-single-question'] ) ? $dwqa_general_settings['captcha-in-single-question'] : false ) , false ) .'><span class="description">'.__( 'Enable captcha on single question page.','dwqa' ).'</span></p>';
}

function dwqa_captcha_in_comment_single_question_display() {
	global $dwqa_general_settings;

	echo '<p><input type="checkbox" name="dwqa_options[captcha-in-comment-single-question]"  id="dwqa_options_captcha_in_comment_question" value="1" '.checked( 1, (isset($dwqa_general_settings['captcha-in-comment-single-question'] ) ? $dwqa_general_settings['captcha-in-comment-single-question'] : false ) , false ) .'><span class="description">'.__( 'Enable captcha on comment single question page.','dwqa' ).' ('.__( 'Google reCaptcha v2 only','dwqa' ).')</span></p>';
}

function dwqa_captcha_google_pubic_key_display() {
	global $dwqa_general_settings;
	$public_key = isset( $dwqa_general_settings['captcha-google-public-key'] ) ?  $dwqa_general_settings['captcha-google-public-key'] : '';
	echo '<p><input type="text" name="dwqa_options[captcha-google-public-key]" value="'.$public_key.'" class="regular-text"></p>';
}

function dwqa_captcha_google_private_key_display() {
	global $dwqa_general_settings;
	$private_key = isset( $dwqa_general_settings['captcha-google-private-key'] ) ?  $dwqa_general_settings['captcha-google-private-key'] : '';
	echo '<p><input type="text" name="dwqa_options[captcha-google-private-key]" value="'.$private_key.'" class="regular-text"></p>';
}

function dwqa_captcha_select_type_display() {
	global $dwqa_general_settings;

	$types = apply_filters( 'dwqa_captcha_type', array(
		'default' => __( 'Default', 'dwqa' ),
		'google-captcha-v2' => __( 'Google reCaptcha v2', 'dwqa' ),
		// 'funcaptcha' => __( 'FunCaptcha', 'dwqa' )
	) );
	$total = count( $types );
	$type_selected = isset( $dwqa_general_settings['captcha-type'] ) ? $dwqa_general_settings['captcha-type'] : 'default';
	echo '<select name="dwqa_options[captcha-type]">';
	foreach( $types as $key => $name ) {
		echo '<option '.selected( $key, $type_selected, false ).' value="'.$key.'">'.$name.'</option>';
	}
	echo '</select>';
}

function dwqa_current_captcha_selected() {
	global $dwqa_general_settings;

	return isset( $dwqa_general_settings['captcha-type'] ) ? $dwqa_general_settings['captcha-type'] : false;
}

// function dwqa_fc_public_key_display() {
// 	global $dwqa_general_settings;
// 	$public_key = isset( $dwqa_general_settings['funcaptcha-public-key'] ) ?  $dwqa_general_settings['funcaptcha-public-key'] : '';
// 	echo '<p><input type="text" name="dwqa_options[funcaptcha-public-key]" value="'.$public_key.'" class="regular-text"></p>';
// }

// function dwqa_fc_private_key_display() {
// 	global $dwqa_general_settings;
// 	$private_key = isset( $dwqa_general_settings['funcaptcha-private-key'] ) ?  $dwqa_general_settings['funcaptcha-private-key'] : '';
// 	echo '<p><input type="text" name="dwqa_options[funcaptcha-private-key]" value="'.$private_key.'" class="regular-text"></p>';
// }

function dwqa_posts_per_page_display(){
	global $dwqa_general_settings;
	$posts_per_page = isset( $dwqa_general_settings['posts-per-page'] ) ?  $dwqa_general_settings['posts-per-page'] : 15;
	echo '<p><input type="text" name="dwqa_options[posts-per-page]" class="small-text" value="'.$posts_per_page.'" > <span class="description">'.__( 'questions.','dwqa' ).'</span></p>';
}

function dwqa_answer_per_page_display() {
	global $dwqa_general_settings;
	$posts_per_page = isset( $dwqa_general_settings['answer-per-page'] ) ?  $dwqa_general_settings['answer-per-page'] : 5;
	echo '<p><input id="dwqa_setting_answers_per_page" type="text" name="dwqa_options[answer-per-page]" class="small-text" value="'.$posts_per_page.'" > <span class="description">'.__( 'answers.','dwqa' ).'</span></p>';
}

function dwqa_enable_private_question_display() {
	global $dwqa_general_settings;

	echo '<p><label for="dwqa_options_enable_private_question"><input type="checkbox" name="dwqa_options[enable-private-question]"  id="dwqa_options_enable_private_question" value="1" '.checked( 1, (isset($dwqa_general_settings['enable-private-question'] ) ? $dwqa_general_settings['enable-private-question'] : false ) , false ) .'><span class="description">'.__( 'Allow members to post private question.','dwqa' ).'</span></label></p>';
}


function dwqa_enable_review_question_mode() {
	global $dwqa_general_settings;

	echo '<p><label for="dwqa_options_enable_review_question"><input type="checkbox" name="dwqa_options[enable-review-question]"  id="dwqa_options_enable_review_question" value="1" '.checked( 1, (isset($dwqa_general_settings['enable-review-question'] ) ? $dwqa_general_settings['enable-review-question'] : false ) , false ) .'><span class="description">'.__( 'Question must be manually approved.','dwqa' ).'</span></label></p>';
}

function dwqa_disable_question_status() {
	global $dwqa_general_settings;

	echo '<p><label for="dwqa_options_dwqa_disable_question_status"><input type="checkbox" name="dwqa_options[disable-question-status]"  id="dwqa_options_dwqa_disable_question_status" value="1" '.checked( 1, (isset($dwqa_general_settings['disable-question-status'] ) ? $dwqa_general_settings['disable-question-status'] : false ) , false ) .'><span class="description">'.__( 'Disable question status feature.', 'dwqa' ).'</span></label></p>';
}

function dwqa_show_all_answers() {
	global $dwqa_general_settings;

	echo '<p><label for="dwqa_options_dwqa_show_all_answers"><input type="checkbox" name="dwqa_options[show-all-answers-on-single-question-page]"  id="dwqa_options_dwqa_show_all_answers" value="1" '.checked( 1, (isset($dwqa_general_settings['show-all-answers-on-single-question-page'] ) ? $dwqa_general_settings['show-all-answers-on-single-question-page'] : false ) , false ) .'><span class="description">'.__( 'Show all answers on single question page.', 'dwqa' ).'</span></label></p>';
}

function dwqa_single_template_options() {
	global $dwqa_general_settings;
	$selected = isset( $dwqa_general_settings['single-template'] ) ? $dwqa_general_settings['single-template'] : -1;
	$theme_path = trailingslashit( get_template_directory() );
	$files = scandir( $theme_path );
	?>
		<p><label for="dwqa_single_question_template">
				<select name="dwqa_options[single-template]" id="dwqa_single_question_template">
					<option <?php selected( $selected, -1 ); ?> value="-1"><?php _e( 'Select template for Single Quesiton page','dwqa' ) ?></option>
					<?php foreach ( $files as $file ) : ?>
						<?php $ext = pathinfo( $file, PATHINFO_EXTENSION ); ?>
						<?php if ( is_dir( $file ) || strpos( $file, '.' === 0 ) || $ext != 'php' ) continue; ?>
					<option <?php selected( $selected, $file ); ?> value="<?php echo $file; ?>"><?php echo $file ?></option>
					<?php endforeach; ?>
				</select> <span class="description"><?php _e( 'By default, your single.php template file will be used if you do not choose any template', 'dwqa' ) ?></span>
			</label>
		</p>
	<?php

}

function dwqa_profile_avatar_uploaded() {
	global $dwqa_general_settings;

	echo '<p><label for="dwqa_options_enable_private_question"><input type="checkbox" name="dwqa_options[profileAvatar]"  id="dwqa_profileAvatar" value="1" '.checked( 1, (isset($dwqa_general_settings['profileAvatar'] ) ? $dwqa_general_settings['profileAvatar'] : false ) , false ) .'><span class="description">'.__( 'Use user avatar when uploaded from profile page.', 'dwqa' ).'</span></label></p>';
}

function dwqa_markdown_editor_setting_layout() {
	global $dwqa_general_settings;

	$checked = isset( $dwqa_general_settings['markdown-editor'] ) ? true : false;

	?>
	<input class="checkbox" name="dwqa_options[markdown-editor]" id="dwqa_markdown_editor" type="checkbox" <?php checked( $checked, true ) ?>>
	<span class="description"><?php _e( 'Enable', 'dwqa' ) ?></span>
	<?php
}

function dwqa_mention_user_setting_layout() {
	global $dwqa_general_settings;

	$checked = isset( $dwqa_general_settings['mention-user'] ) ? true : false;

	?>
	<input class="checkbox" name="dwqa_options[mention-user]" id="dwqa_mention_user" type="checkbox" <?php checked( $checked, true ) ?>>
	<span class="description"><?php _e( 'Enable', 'dwqa' ) ?></span>
	<?php
}

function dwqa_emoji_setting_layout() {
	global $dwqa_general_settings;

	$checked = isset( $dwqa_general_settings['emoji'] ) ? true : false;

	?>
	<input class="checkbox" name="dwqa_options[emoji]" id="dwqa_emoji" type="checkbox" <?php checked( $checked, true ) ?>>
	<span class="description"><?php _e( 'Enable', 'dwqa' ) ?></span>
	<?php
}

function dwqa_permalink_section_layout() {
	printf( __( 'If you like, you may enter custom structure for your single question, question category and question tag URLs here. For example, using <code>topic</code> as your question base would make your question links like <code>%s</code>. If you leave these blank the default will be used.', 'dwqa' ), home_url( 'topic/question-name/' ) );
}

function dwqa_get_rewrite_slugs() {
	global  $dwqa_general_settings;
	$dwqa_general_settings = get_option( 'dwqa_options' );

	$rewrite_slugs = array();

	$question_rewrite = get_option( 'dwqa-question-rewrite', 'question' );
	$question_rewrite = $question_rewrite ? $question_rewrite : 'question';
	if ( isset( $dwqa_general_settings['question-rewrite'] ) && $dwqa_general_settings['question-rewrite'] && $dwqa_general_settings['question-rewrite'] != $question_rewrite ) {
		$question_rewrite = $dwqa_general_settings['question-rewrite'];
		update_option( 'dwqa-question-rewrite', $question_rewrite );
	}

	$rewrite_slugs['question_rewrite'] = $question_rewrite;

	$question_category_rewrite = $dwqa_general_settings['question-category-rewrite'];
	$question_category_rewrite = $question_category_rewrite ? $question_category_rewrite : 'question-category';
	if ( isset( $dwqa_general_settings['question-category-rewrite'] ) && $dwqa_general_settings['question-category-rewrite'] && $dwqa_general_settings['question-category-rewrite'] != $question_category_rewrite ) {
		$question_category_rewrite = $dwqa_general_settings['question-category-rewrite'];
		update_option( 'dwqa-question-category-rewrite', $question_category_rewrite );
	}

	$rewrite_slugs['question_category_rewrite'] = $question_category_rewrite;

	$question_tag_rewrite = $dwqa_general_settings['question-tag-rewrite'];
	$question_tag_rewrite = $question_tag_rewrite ? $question_tag_rewrite : 'question-tag';
	if ( isset( $dwqa_general_settings['question-tag-rewrite'] ) && $dwqa_general_settings['question-tag-rewrite'] && $dwqa_general_settings['question-tag-rewrite'] != $question_tag_rewrite ) {
		$question_tag_rewrite = $dwqa_general_settings['question-tag-rewrite'];
		update_option( 'dwqa-question-tag-rewrite', $question_tag_rewrite );
	}
	$rewrite_slugs['question_tag_rewrite'] = $question_tag_rewrite;

	return $rewrite_slugs;
}

//luvitas

function dwqa_answer_admin_first() {
	global $dwqa_general_settings;

	echo '<p><label for="dwqa_options_answer_admin_first"><input type="checkbox" name="dwqa_options[answer-admin-first]"  id="dwqa_options_answer_admin_first" value="1" '.checked( 1, (isset($dwqa_general_settings['answer-admin-first'] ) ? $dwqa_general_settings['answer-admin-first'] : false ) , false ) .'><span class="description">'.__( 'Display admin answer first then display another answer.', 'dwqa' ).'</span></label></p>';
}

function dwqa_answer_approve() {
	global $dwqa_general_settings;

	echo '<p><label for="dwqa_options_answer_approve"><input type="checkbox" name="dwqa_options[answer-approve]"  id="dwqa_options_answer_approve" value="1" '.checked( 1, (isset($dwqa_general_settings['answer-approve'] ) ? $dwqa_general_settings['answer-approve'] : false ) , false ) .'><span class="description">'.__( 'Answers must be manually approved.', 'dwqa' ).'</span></label></p>';
}

function dwqa_show_button_upload() {
	global $dwqa_general_settings;

	echo '<p><label for="dwqa_options_show_button_upload"><input type="checkbox" name="dwqa_options[show-button-upload]"  id="dwqa_options_show_button_upload" value="1" '.checked( 1, (isset($dwqa_general_settings['show-button-upload'] ) ? $dwqa_general_settings['show-button-upload'] : false ) , false ) .'><span class="description">'.__( 'Show button upload.', 'dwqa' ).'</span></label></p>';
}

function dwqa_max_size_upload() {
	global $dwqa_general_settings;
	$max_size_upload = isset( $dwqa_general_settings['max-size-upload'] ) ?  $dwqa_general_settings['max-size-upload'] : '';
	echo '<p><input id="dwqa_setting_max_size_upload" type="text" name="dwqa_options[max-size-upload]" class="medium-text" value="'.$max_size_upload.'" > <span class="description">'.__( 'KB.','dwqa' ).'</span></p>';
}

function dwqa_max_files_upload() {
	global $dwqa_general_settings;
	$max_files_upload = isset( $dwqa_general_settings['max-files-upload'] ) ?  $dwqa_general_settings['max-files-upload'] : '';
	echo '<p><input id="dwqa_setting_max_files_upload" type="text" name="dwqa_options[max-files-upload]" class="medium-text" value="'.$max_files_upload.'" > <span class="description">'.__( 'For single answer.','dwqa' ).'</span></p>';
}

function dwqa_accept_upload_extension() {
	global $dwqa_general_settings;
	$accept_upload_extension = isset( $dwqa_general_settings['accept-upload-extension'] ) ?  $dwqa_general_settings['accept-upload-extension'] : '';
	echo '<p><input id="dwqa_setting_accept_upload_extension" type="text" name="dwqa_options[accept-upload-extension]" class="medium-text" value="'.$accept_upload_extension.'" ><br><span class="description">'.__( 'Separate By "|" (txt|jpg|pdf).','dwqa' ).'</span></p>';
}

function dwqa_disallowed_post_extension() {
	global $dwqa_general_settings;
	$disabllowed_post_extension = isset( $dwqa_general_settings['disallowed-post-extension'] ) ?  $dwqa_general_settings['disallowed-post-extension'] : '';
	echo '<p><input id="dwqa_setting_disabllowed_post_extension" type="text" name="dwqa_options[disallowed-post-extension]" class="medium-text" value="'.$disabllowed_post_extension.'" ><br><span class="description">'.__( 'When a answer/question contains any of these words in its content, it will be held in the moderation queue. It will match inside words, so “I love WordPress” will match “WordPress”. Separate By "comma" ("wordpress, stop, hack").','dwqa' ).'</span></p>';
}

function dwqa_allow_anonymous_vote() {
	global $dwqa_general_settings;

	echo '<p><label for="dwqa_options_allow_anonymous_vote"><input type="checkbox" name="dwqa_options[allow-anonymous-vote]"  id="dwqa_options_allow_anonymous_vote" value="1" '.checked( 1, (isset($dwqa_general_settings['allow-anonymous-vote'] ) ? $dwqa_general_settings['allow-anonymous-vote'] : false ) , false ) .'><span class="description">'.__( 'Allow anonymous vote.', 'dwqa' ).'</span></label></p>';
}

function dwqa_use_akismet_antispam() {
	global $dwqa_general_settings;

	echo '<p><label for="dwqa_options_use_akismet_antispam"><input type="checkbox" name="dwqa_options[use-akismet-antispam]"  id="dwqa_options_use_akismet_antispam" value="1" '.checked( 1, (isset($dwqa_general_settings['use-akismet-antispam'] ) ? $dwqa_general_settings['use-akismet-antispam'] : false ) , false ) .'><span class="description">'.__( 'Enable Akismet', 'dwqa' ).'</span></label></p>';
}

function dwqa_akismet_api_key() {
	global $dwqa_general_settings;

	$akismet_api_key = isset( $dwqa_general_settings['akismet-api-key'] ) ?  $dwqa_general_settings['akismet-api-key'] : '';
	echo '<p><input id="dwqa_setting_akismet_api_key" type="text" name="dwqa_options[akismet-api-key]" class="medium-text" value="'.$akismet_api_key.'" ><br><span class="description">'.__( 'Get in', 'dwqa' ).' <a href="https://akismet.com">akismet.com</a>'.'</span></p>';
}

function dwqa_akismet_connection_status() {
	global $dwqa_general_settings;

	$status = __( 'Not Connected', 'dwqa' );

	if(isset($dwqa_general_settings['use-akismet-antispam']) && $dwqa_general_settings['use-akismet-antispam']){
		//enable akismet
		if ( class_exists( 'DWQA_Akismet' ) ){
			if(DWQA_Akismet::akismet_verify_key($dwqa_general_settings['akismet-api-key'])){
				$status = __( 'Connected', 'dwqa' );
			}
		}
	}

	echo '<p>'.$status.'</p>';
}

function dwqa_use_auto_closure() {
	global $dwqa_general_settings;

	echo '<p><label for="dwqa_options_use_auto_closure"><input type="checkbox" name="dwqa_options[use-auto-closure]"  id="dwqa_options_use_auto_closure" value="1" '.checked( 1, (isset($dwqa_general_settings['use-auto-closure'] ) ? $dwqa_general_settings['use-auto-closure'] : false ) , false ) .'><span class="description">'.__( 'Enable Auto Closure', 'dwqa' ).'</span></label></p>';
}
function dwqa_number_day_auto_closure() {
	global $dwqa_general_settings;
	$number_day_auto_closure = isset( $dwqa_general_settings['number-day-auto-closure'] ) ?  $dwqa_general_settings['number-day-auto-closure'] : '';
	echo '<p><input id="dwqa_setting_number_day_auto_closure" type="text" name="dwqa_options[number-day-auto-closure]" class="medium-text" value="'.$number_day_auto_closure.'" > <span class="description">'.__( 'Days.(greater 0)','dwqa' ).'</span></p>';
}

function dwqa_use_user_expiration() {
	global $dwqa_general_settings;

	echo '<p><label for="dwqa_options_use_user_expiration"><input type="checkbox" name="dwqa_options[use-user-expiration]"  id="dwqa_options_use_user_expiration" value="1" '.checked( 1, (isset($dwqa_general_settings['use-user-expiration'] ) ? $dwqa_general_settings['use-user-expiration'] : false ) , false ) .'><span class="description">'.__( 'Enable User Expiration', 'dwqa' ).'</span></label></p>';
}

function dwqa_is_captcha_enable() {
	global $dwqa_general_settings;
	$public_key = isset( $dwqa_general_settings['captcha-google-public-key'] ) ?  $dwqa_general_settings['captcha-google-public-key'] : '';
	$private_key = isset( $dwqa_general_settings['captcha-google-private-key'] ) ?  $dwqa_general_settings['captcha-google-private-key'] : '';

	if ( ! $public_key || ! $private_key ) {
		return false;
	}
	return true;
}

function dwqa_is_captcha_enable_in_submit_question() {
	global $dwqa_general_settings;
	$captcha_in_question = isset( $dwqa_general_settings['captcha-in-question'] ) ? $dwqa_general_settings['captcha-in-question'] : false;

	if ( $captcha_in_question ) {
		return true;
	}
	return false;
}

function dwqa_is_captcha_enable_in_comment() {
	global $dwqa_general_settings;
	$captcha_in_comment = isset( $dwqa_general_settings['captcha-in-comment-single-question'] ) ? $dwqa_general_settings['captcha-in-comment-single-question'] : false;

	if ( $captcha_in_comment ) {
		return true;
	}
	return false;
}

function dwqa_is_captcha_enable_in_single_question() {
	global $dwqa_general_settings;
	$captcha_in_single_question = isset( $dwqa_general_settings['captcha-in-single-question'] ) ? $dwqa_general_settings['captcha-in-single-question'] : false;
	if ( $captcha_in_single_question ) {
		return true;
	}
	return false;
}

function dwqa_is_enable_status() {
	global $dwqa_general_settings;

	if ( !isset( $dwqa_general_settings['disable-question-status'] ) || !$dwqa_general_settings['disable-question-status'] ) {
		return true;
	}

	return false;
}

class DWQA_Settings {
	public function __construct(){
		add_action( 'admin_menu', array( $this, 'admin_menu' ) );
		add_action( 'init', array( $this, 'init_options' ), 9 );
		add_action( 'admin_init', array( $this, 'register_settings' ) );
		add_action( 'updated_option', array( $this, 'update_options' ), 10, 3 );
		add_action( 'wp_loaded', array( $this, 'flush_rules' ) );
		add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_script' ) );
	}

	public function enqueue_script() {
		wp_enqueue_script( 'dwqa-admin-settings-page', DWQA_URI . 'assets/js/admin-settings-page.js', array( 'jquery' ), true );
		if(is_admin() && isset($_REQUEST['post_type']) && $_REQUEST['post_type']=='dwqa-question'){
			wp_enqueue_style( 'dwqa-admin-settings-style', DWQA_URI . 'assets/css/admin-style.css', array(), true );
		}
	}

	public function update_options( $option, $old_value, $value ) {
		if ( $option == 'dwqa_options' ) {
			if ( $old_value['pages']['archive-question'] != $value['pages']['archive-question']  ) {
				$questions_page_content = get_post_field( 'post_content', $value['pages']['archive-question'] );
				if ( strpos( $questions_page_content, '[dwqa-list-questions]' ) === false ) {
					$questions_page_content = str_replace( '[dwqa-submit-question-form]', '', $questions_page_content );
					wp_update_post( array(
						'ID'			=> $value['pages']['archive-question'],
						'post_content'	=> $questions_page_content . '[dwqa-list-questions]',
					) );
				}
			}

			if ( $old_value['pages']['submit-question'] != $value['pages']['submit-question'] ) {
				$submit_question_content = get_post_field( 'post_content', $value['pages']['submit-question'] );
				if ( strpos( $submit_question_content, '[dwqa-submit-question-form]' ) === false ) {
					$submit_question_content = str_replace( '[dwqa-list-questions]', '', $submit_question_content );
					wp_update_post( array(
						'ID'			=> $value['pages']['submit-question'],
						'post_content'	=> $submit_question_content . '[dwqa-submit-question-form]',
					) );
				}
			}

			// Flush rewrite when rewrite rule settings change
			flush_rewrite_rules();
		}
	}

	// Create admin menus for backend
	public function admin_menu(){
		global $dwqa_setting_page;
		$dwqa_setting_page = add_submenu_page( 'edit.php?post_type=dwqa-question', __( 'Plugin Settings','dwqa' ), __( 'Settings','dwqa' ), 'manage_options', 'dwqa-settings', array( $this, 'settings_display' )  );
	}

	public function init_options(){
		global $dwqa_options, $dwqa_general_settings;
		$dwqa_general_settings = $dwqa_options = wp_parse_args( get_option( 'dwqa_options' ), array(
			'pages'     => array(
					'submit-question'   => 0,
					'archive-question'  => 0,
				),
			'question-category-rewrite' => '',
			'question-tag-rewrite' => '',
			'captcha-in-single-question' => false,
			'question-new-time-frame' => 4,
		) );
	}

	public function flush_rules() {
		if ( isset( $_GET['page'] ) && 'dwqa-settings' == $_GET['page'] ) {
			flush_rewrite_rules();
		}
	}

	public function current_email_tab() {
		if ( isset( $_GET['tab'] ) && 'email' == $_GET['tab'] ) {
			return isset( $_GET['section'] ) ? $_GET['section'] : 'general';
		}

		return false;
	}

	public function email_tabs() {
		$section = $this->current_email_tab();
		ob_start();
		?>
		<ul class="subsubsub">
			<li class="<?php echo $section == 'general' ? 'active' : '' ?>"><a href="<?php echo add_query_arg( 'section', 'general', admin_url( 'edit.php?post_type=dwqa-question&page=dwqa-settings&tab=email' ) ) ?>"><?php _e( 'Email Settings', 'dwqa' ) ?></a> &#124; </li>
			<li class="<?php echo $section == 'new-question' ? 'active' : '' ?>"><a href="<?php echo add_query_arg( 'section', 'new-question', admin_url( 'edit.php?post_type=dwqa-question&page=dwqa-settings&tab=email' ) ) ?>"><?php _e( 'New Question Notifications', 'dwqa' ) ?></a> &#124; </li>
			<li class="<?php echo $section == 'new-answer' ? 'active' : '' ?>"><a href="<?php echo add_query_arg( 'section', 'new-answer', admin_url( 'edit.php?post_type=dwqa-question&page=dwqa-settings&tab=email' ) ) ?>"><?php _e( 'New Answer Notifications', 'dwqa' ) ?></a> &#124; </li>
			<li class="<?php echo $section == 'new-comment' ? 'active' : '' ?>"><a href="<?php echo add_query_arg( 'section', 'new-comment', admin_url( 'edit.php?post_type=dwqa-question&page=dwqa-settings&tab=email' ) ) ?>"><?php _e( 'New Comment Notifications', 'dwqa' ) ?></a></li>
		</ul>
		<div class="clear"></div>
		<?php
		return ob_get_clean();
	}

	public function register_settings(){
		global  $dwqa_general_settings;

		//Register Setting Sections
		add_settings_section(
			'dwqa-general-settings',
			__( 'Page Settings', 'dwqa' ),
			null,
			'dwqa-settings'
		);

		add_settings_field(
			'dwqa_options[pages][archive-question]',
			__( 'Question List Page', 'dwqa' ),
			'dwqa_pages_settings_display',
			'dwqa-settings',
			'dwqa-general-settings'
		);

		add_settings_field(
			'dwqa_options[pages][submit-question]',
			__( 'Ask Question Page', 'dwqa' ),
			'dwqa_submit_question_page_display',
			'dwqa-settings',
			'dwqa-general-settings'
		);

		add_settings_field(
			'dwqa_options[pages][register-page]',
			__( 'Register page', 'dwqa' ),
			'dwqa_set_register_page_display',
			'dwqa-settings',
			'dwqa-general-settings'
		);

		add_settings_field(
			'dwqa_options[pages][user-profile]',
			__( 'Profile page', 'dwqa' ),
			'dwqa_set_profile_page_display',
			'dwqa-settings',
			'dwqa-general-settings'
		);

		add_settings_field(
			'dwqa_options[pages][thank-page]',
			__( 'Thanks page', 'dwqa' ),
			'dwqa_set_thank_page_display',
			'dwqa-settings',
			'dwqa-general-settings'
		);
		
		add_settings_field(
			'dwqa_options[disallowed-post-extension]',
			__( 'Disallowed Post', 'dwqa' ),
			'dwqa_disallowed_post_extension',
			'dwqa-settings',
			'dwqa-general-settings'
		);

		// add_settings_field(
		// 	'dwqa_options[single-template]',
		// 	__( 'Single Question Template', 'dwqa' ),
		// 	'dwqa_single_template_options',
		// 	'dwqa-settings',
		// 	'dwqa-general-settings'
		// );

		do_action( 'dwqa_register_setting_section' );

		//Time setting
//		add_settings_section(
//			'dwqa-time-settings',
//			__( 'Time settings','dwqa' ),
//			null,
//			'dwqa-settings'
//		);
//
//		add_settings_field(
//			'dwqa_options[question-new-time-frame]',
//			__( 'New Question Time Frame', 'dwqa' ),
//			'dwqa_question_new_time_frame_display',
//			'dwqa-settings',
//			'dwqa-time-settings'
//		);
//
//		add_settings_field(
//			'dwqa_options[question-overdue-time-frame]',
//			__( 'Question Overdue - Time Frame', 'dwqa' ),
//			'dwqa_question_overdue_time_frame_display',
//			'dwqa-settings',
//			'dwqa-time-settings'
//		);

		// Question Settings
		add_settings_section(
			'dwqa-misc-settings',
			__( 'Question Settings', 'dwqa' ),
			false,
			'dwqa-settings'
		);

		add_settings_field(
			'dwqa_options[posts-per-page]',
			__( 'Archive Page Show At Most','dwqa' ),
			'dwqa_posts_per_page_display',
			'dwqa-settings',
			'dwqa-misc-settings'
		);

		add_settings_field(
			'dwqa_options[enable-review-question]',
			__( 'Before A Question Appears', 'dwqa' ),
			'dwqa_enable_review_question_mode',
			'dwqa-settings',
			'dwqa-misc-settings'
		);

		add_settings_field(
			'dwqa_options[enable-private-question]',
			__( 'Other Question Settings', 'dwqa' ),
			'dwqa_enable_private_question_display',
			'dwqa-settings',
			'dwqa-misc-settings'
		);

		add_settings_field(
			'dwqa_options[profileAvatar]',
			__( 'Use avatar from Profile page', 'dwqa' ),
			'dwqa_profile_avatar_uploaded',
			'dwqa-settings',
			'dwqa-misc-settings'
		);

		add_settings_field(
			'dwqa_options[disable-question-status]',
			'',
			'dwqa_disable_question_status',
			'dwqa-settings',
			'dwqa-misc-settings'
		);

		// Answer Settings
		add_settings_section(
			'dwqa-answer-settings',
			__( 'Answer Settings', 'dwqa' ),
			false,
			'dwqa-settings'
		);

		add_settings_field(
			'dwqa_options[show-all-answers-on-single-question-page]',
			__( 'Answer Listing', 'dwqa' ),
			'dwqa_show_all_answers',
			'dwqa-settings',
			'dwqa-answer-settings'
		);

		add_settings_field(
			'dwqa_options[answer-per-page]',
			false,
			'dwqa_answer_per_page_display',
			'dwqa-settings',
			'dwqa-answer-settings'
		);

		add_settings_field(
			'dwqa_options[answer-admin-first]',
			__( 'Other Answer Setting', 'dwqa' ),
			'dwqa_answer_admin_first',
			'dwqa-settings',
			'dwqa-answer-settings'
		);

		add_settings_field(
			'dwqa_options[answer-approve]',
			'',
			'dwqa_answer_approve',
			'dwqa-settings',
			'dwqa-answer-settings'
		);

		// Upload Settings
		add_settings_section(
			'dwqa-upload-settings',
			__( 'Upload Settings', 'dwqa' ),
			false,
			'dwqa-settings'
		);

		add_settings_field(
			'dwqa_options[show-button-upload]',
			__( 'Show button upload', 'dwqa' ),
			'dwqa_show_button_upload',
			'dwqa-settings',
			'dwqa-upload-settings'
		);

		add_settings_field(
			'dwqa_options[max-size-upload]',
			__( 'Maximum file size', 'dwqa' ),
			'dwqa_max_size_upload',
			'dwqa-settings',
			'dwqa-upload-settings'
		);

		add_settings_field(
			'dwqa_options[max-files-upload]',
			__( 'Maximum files to upload', 'dwqa' ),
			'dwqa_max_files_upload',
			'dwqa-settings',
			'dwqa-upload-settings'
		);

		add_settings_field(
			'dwqa_options[accept-upload-extension]',
			__( 'Accept upload extension', 'dwqa' ),
			'dwqa_accept_upload_extension',
			'dwqa-settings',
			'dwqa-upload-settings'
		);

		// Vote Settings
		add_settings_section(
			'dwqa-vote-settings',
			__( 'Vote Settings', 'dwqa' ),
			false,
			'dwqa-settings'
		);

		add_settings_field(
			'dwqa_options[allow-anonymous-vote]',
			__( 'Allow Anonymous Vote', 'dwqa' ),
			'dwqa_allow_anonymous_vote',
			'dwqa-settings',
			'dwqa-vote-settings'
		);

		// Akismet Settings
		add_settings_section(
			'dwqa-akismet-settings',
			__( 'Akismet Settings', 'dwqa' ),
			false,
			'dwqa-settings'
		);

		add_settings_field(
			'dwqa_options[use-akismet-antispam]',
			__( 'Use Akismet anti-spam', 'dwqa' ),
			'dwqa_use_akismet_antispam',
			'dwqa-settings',
			'dwqa-akismet-settings'
		);
		add_settings_field(
			'dwqa_options[akismet-api-key]',
			__( 'Akismet API key', 'dwqa' ),
			'dwqa_akismet_api_key',
			'dwqa-settings',
			'dwqa-akismet-settings'
		);
		add_settings_field(
			'dwqa_options[akismet-connection-status]',
			__( 'Akismet connection status', 'dwqa' ),
			'dwqa_akismet_connection_status',
			'dwqa-settings',
			'dwqa-akismet-settings'
		);

		//Auto closure Settings
		add_settings_section(
			'dwqa-auto-closure-settings',
			__( 'Auto Closure Settings', 'dwqa' ),
			false,
			'dwqa-settings'
		);

		add_settings_field(
			'dwqa_options[use-auto-closure]',
			__( 'Use Auto Closure', 'dwqa' ),
			'dwqa_use_auto_closure',
			'dwqa-settings',
			'dwqa-auto-closure-settings'
		);
		add_settings_field(
			'dwqa_options[number-day-auto-closure]',
			__( 'Closure after', 'dwqa' ),
			'dwqa_number_day_auto_closure',
			'dwqa-settings',
			'dwqa-auto-closure-settings'
		);

		//DWQA Expiration Settings
		add_settings_section(
			'dwqa-user-expiration-settings',
			__( 'User Expiration Settings (Woo Integration)', 'dwqa' ),
			false,
			'dwqa-settings'
		);

		add_settings_field(
			'dwqa_options[use-user-expiration]',
			__( 'Enable User Expiration', 'dwqa' ),
			'dwqa_use_user_expiration',
			'dwqa-settings',
			'dwqa-user-expiration-settings'
		);

		do_action('dwqa_register_middle_setting_field');

		//Captcha Setting

		add_settings_section(
			'dwqa-captcha-settings',
			__( 'Captcha Settings','dwqa' ),
			null,
			'dwqa-settings'
		);

		add_settings_field(
			'dwqa_options[captcha-type]',
			__( 'Type', 'dwqa' ),
			'dwqa_captcha_select_type_display',
			'dwqa-settings',
			'dwqa-captcha-settings'
		);

		add_settings_field(
			'dwqa_options[captcha-in-question]',
			__( 'Ask Question Page', 'dwqa' ),
			'dwqa_captcha_in_question_display',
			'dwqa-settings',
			'dwqa-captcha-settings'
		);

		add_settings_field(
			'dwqa_options[captcha-in-single-question]',
			__( 'Single Question Page', 'dwqa' ),
			'dwqa_captcha_in_single_question_display',
			'dwqa-settings',
			'dwqa-captcha-settings'
		);

		add_settings_field(
			'dwqa_options[captcha-in-comment-single-question]',
			__( 'Comment Single Question Page', 'dwqa' ),
			'dwqa_captcha_in_comment_single_question_display',
			'dwqa-settings',
			'dwqa-captcha-settings'
		);

		add_settings_field(
			'dwqa_options[captcha-google-public-key]',
			__( 'Google ReCaptcha Public Key', 'dwqa' ),
			'dwqa_captcha_google_pubic_key_display',
			'dwqa-settings',
			'dwqa-captcha-settings'
		);

		add_settings_field(
			'dwqa_options[captcha-google-private-key]',
			__( 'Google ReCaptcha Private Key', 'dwqa' ),
			'dwqa_captcha_google_private_key_display',
			'dwqa-settings',
			'dwqa-captcha-settings'
		);

		// add_settings_field(
		// 	'dwqa_options[funcaptcha-public-key]',
		// 	__( 'Funcaptcha Public Key', 'dwqa' ),
		// 	'dwqa_fc_public_key_display',
		// 	'dwqa-settings',
		// 	'dwqa-captcha-settings'
		// );

		// add_settings_field(
		// 	'dwqa_options[funcaptcha-private-key]',
		// 	__( 'FunCaptcha Private Key', 'dwqa' ),
		// 	'dwqa_fc_private_key_display',
		// 	'dwqa-settings',
		// 	'dwqa-captcha-settings'
		// );

		do_action( 'dwqa_captcha_setting_field' );

		// other settings
		add_settings_section(
			'dwqa-other-settings',
			__( 'Other Settings', 'dwqa' ),
			false,
			'dwqa-settings'
		);

		add_settings_field(
			'dwqa_options[markdown-editor]',
			__( 'Markdown Editor', 'dwqa' ),
			'dwqa_markdown_editor_setting_layout',
			'dwqa-settings',
			'dwqa-other-settings'
		);

		add_settings_field(
			'dwqa_options[mention-user]',
			__( 'Mention User', 'dwqa' ),
			'dwqa_mention_user_setting_layout',
			'dwqa-settings',
			'dwqa-other-settings'
		);

		add_settings_field(
			'dwqa_options[emoji]',
			__( 'Emoji', 'dwqa' ),
			'dwqa_emoji_setting_layout',
			'dwqa-settings',
			'dwqa-other-settings'
		);

		do_action( 'dwqa_after_other_settings' );


		//Permalink
		add_settings_section(
			'dwqa-permalink-settings',
			__( 'Permalink Settings','dwqa' ),
			'dwqa_permalink_section_layout',
			'dwqa-settings'
		);

		add_settings_field(
			'dwqa_options[question-rewrite]',
			__( 'Question Base', 'dwqa' ),
			'dwqa_question_rewrite_display',
			'dwqa-settings',
			'dwqa-permalink-settings'
		);

		add_settings_field(
			'dwqa_options[question-category-rewrite]',
			__( 'Question Category Base', 'dwqa' ),
			'dwqa_question_category_rewrite_display',
			'dwqa-settings',
			'dwqa-permalink-settings'
		);

		add_settings_field(
			'dwqa_options[question-tag-rewrite]',
			__( 'Question Tag Base', 'dwqa' ),
			'dwqa_question_tag_rewrite_display',
			'dwqa-settings',
			'dwqa-permalink-settings'
		);

		// add_settings_field(
		// 	'dwqa_options[question-slug-category-rewrite]',
		// 	__( 'Category Slug', 'dwqa' ),
		// 	'dwqa_question_slug_category_rewrite_display',
		// 	'dwqa-settings',
		// 	'dwqa-permalink-settings'
		// );

		add_settings_field(
			'dwqa_options[question-permalink]',
			__( 'Question Permalink', 'dwqa' ),
			'dwqa_question_permalink',
			'dwqa-settings',
			'dwqa-permalink-settings'
		);


		register_setting( 'dwqa-settings', 'dwqa_options' );

		add_settings_section(
			'dwqa-subscribe-settings',
			false,
			false,
			'dwqa-email'
		);

		add_settings_section(
			'dwqa-subscribe-settings-new-question',
			false,
			false,
			'dwqa-email'
		);

		add_settings_section(
			'dwqa-subscribe-settings-new-answer',
			false,
			false,
			'dwqa-email'
		);

		add_settings_section(
			'dwqa-subscribe-settings-new-comment',
			false,
			false,
			'dwqa-email'
		);

		// Send to address setting
		// add_settings_field(
		//     'dwqa_subscrible_sendto_address',
		//     __( 'Admin Email', 'dwqa' ),
		//     array( $this, 'email_sendto_address_display' ),
		//     'dwqa-email',
		//     'dwqa-subscribe-settings'
		// );
		register_setting( 'dwqa-subscribe-settings', 'dwqa_subscrible_sendto_address' );

		// Cc address setting
		// add_settings_field(
		//     'dwqa_subscrible_cc_address',
		//     __( 'Cc', 'dwqa' ),
		//     array( $this, 'email_cc_address_display' ),
		//     'dwqa-email',
		//     'dwqa-subscribe-settings'
		// );
		register_setting( 'dwqa-subscribe-settings-new-question', 'dwqa_subscrible_cc_address' );

		// Bcc address setting
		// add_settings_field(
		//     'dwqa_subscrible_bcc_address',
		//     __( 'Bcc', 'dwqa' ),
		//     array( $this, 'email_bcc_address_display' ),
		//     'dwqa-email',
		//     'dwqa-subscribe-settings'
		// );
		register_setting( 'dwqa-subscribe-settings-new-question', 'dwqa_subscrible_bcc_address' );

		// Bcc address setting
		add_settings_field(
			'dwqa_subscrible_from_address',
			__( 'From Email', 'dwqa' ),
			array( $this, 'email_from_address_display' ),
			'dwqa-email',
			'dwqa-subscribe-settings'
		);
		register_setting( 'dwqa-subscribe-settings', 'dwqa_subscrible_from_address' );

		add_settings_field(
			'dwqa_subscrible_from_name',
			__( 'From name', 'dwqa' ),
			array( $this, 'email_from_name_display' ),
			'dwqa-email',
			'dwqa-subscribe-settings'
		);
		register_setting( 'dwqa-subscribe-settings', 'dwqa_subscrible_from_name' );

		//add delay email(need to speed up )
		add_settings_field(
			'dwqa_enable_email_delay',
			false,
			array( $this, 'enable_email_delay' ),
			'dwqa-email',
			'dwqa-subscribe-settings'
		);
		register_setting( 'dwqa-subscribe-settings', 'dwqa_enable_email_delay' );


		// Send copy
		add_settings_field(
			'dwqa_subscrible_send_copy_to_admin',
			false,
			array( $this, 'email_send_copy_to_admin' ),
			'dwqa-email',
			'dwqa-subscribe-settings'
		);
		register_setting( 'dwqa-subscribe-settings', 'dwqa_subscrible_send_copy_to_admin' );


		// Logo setting in for email template
		// add_settings_field(
		//     'dwqa_subscrible_email_logo',
		//     __( 'Email Logo', 'dwqa' ),
		//     'dwqa_subscrible_email_logo_display',
		//     'dwqa-email',
		//     'dwqa-subscribe-settings'
		// );
		register_setting( 'dwqa-subscribe-settings', 'dwqa_subscrible_email_logo' );

		//New Question Email Notify
		register_setting( 'dwqa-subscribe-settings-new-question', 'dwqa_subscrible_new_question_email' );
		register_setting( 'dwqa-subscribe-settings-new-question', 'dwqa_subscrible_new_question_email_subject' );
		register_setting( 'dwqa-subscribe-settings-new-question', 'dwqa_subscrible_enable_new_question_notification' );

		// New Answer Email Notify
		register_setting( 'dwqa-subscribe-settings-new-answer', 'dwqa_subscrible_new_answer_email' );
		register_setting( 'dwqa-subscribe-settings-new-answer', 'dwqa_subscrible_new_answer_email_subject' );
		register_setting( 'dwqa-subscribe-settings-new-answer', 'dwqa_subscrible_enable_new_answer_notification' );
		register_setting( 'dwqa-subscribe-settings-new-answer', 'dwqa_subscrible_new_answer_forward' );
		// New Answer to Followers Email Notify
		register_setting( 'dwqa-subscribe-settings-new-answer', 'dwqa_subscrible_new_answer_followers_email' );
		register_setting( 'dwqa-subscribe-settings-new-answer', 'dwqa_subscrible_new_answer_followers_email_subject' );
		register_setting( 'dwqa-subscribe-settings-new-answer', 'dwqa_subscrible_enable_new_answer_followers_notification' );

		// New Comment for Question Notify
		register_setting( 'dwqa-subscribe-settings-new-comment', 'dwqa_subscrible_new_comment_question_email_subject' );
		register_setting( 'dwqa-subscribe-settings-new-comment', 'dwqa_subscrible_new_comment_question_email' );
		register_setting( 'dwqa-subscribe-settings-new-comment', 'dwqa_subscrible_enable_new_comment_question_notification' );

		register_setting( 'dwqa-subscribe-settings-new-comment', 'dwqa_subscrible_new_comment_question_forward' );

		// New Comment for Question to Followers Email Notify
		register_setting( 'dwqa-subscribe-settings-new-comment', 'dwqa_subscrible_new_comment_question_followers_email_subject' );
		register_setting( 'dwqa-subscribe-settings-new-comment', 'dwqa_subscrible_new_comment_question_followers_email' );
		register_setting( 'dwqa-subscribe-settings-new-comment', 'dwqa_subscrible_enable_new_comment_question_followers_notify' );

		// New Comment for Answer Email Notify
		register_setting( 'dwqa-subscribe-settings-new-comment', 'dwqa_subscrible_new_comment_answer_email_subject' );
		register_setting( 'dwqa-subscribe-settings-new-comment', 'dwqa_subscrible_new_comment_answer_email' );
		register_setting( 'dwqa-subscribe-settings-new-comment', 'dwqa_subscrible_enable_new_comment_answer_notification' );
		register_setting( 'dwqa-subscribe-settings-new-comment', 'dwqa_subscrible_new_comment_answer_forward' );

		// New Comment for Answer to Followers Email Notify
		register_setting( 'dwqa-subscribe-settings-new-comment', 'dwqa_subscrible_new_comment_answer_followers_email_subject' );
		register_setting( 'dwqa-subscribe-settings-new-comment', 'dwqa_subscrible_new_comment_answer_followers_email' );
		register_setting( 'dwqa-subscribe-settings-new-comment', 'dwqa_subscrible_enable_new_comment_answer_followers_notification' );


		add_settings_section(
			'dwqa-permission-settings',
			__( 'Group Permission','dwqa' ),
			false,
			'dwqa-permission'
		);

		add_settings_field(
			'dwqa_permission',
			__( 'Group Permission','dwqa' ),
			'dwqa_permission_display',
			'dwqa-permission',
			'dwqa-permission-settings'
		);

		register_setting( 'dwqa-permission-settings', 'dwqa_permission' );
	}

	public function settings_display(){
		global $dwqa_general_settings;
		global $dwqa;
		$email_section = $this->current_email_tab();
		?>
		<style type="text/css">
			ul.subsubsub {
			    float: left;
			}

			ul.subsubsub > li {
			    display: inline-block;
			}

			ul.subsubsub > li.active > a {
			    color: #000;
			    font-weight: bold;
			}
		</style>
		<div class="wrap">
			<div class="dwqa-settings-head">
				<div class="heade-decs">
					<h2><span><?php _e( 'Welcome to DW Q&A', 'dwqa' ) ?></span></h2>
					<p><?php _e('DW Question & Answer Pro is an all-time favourite question & answer plugin for WordPress. Your WordPress site will have a full-featured question & answer section like StackOverflow, Quora or Yahoo Answers.', 'dwqa') ?></p>
					</div>
					<div class="head-thumb dwqa-badge">
						<img src="<?php echo DWQA_URI.'/assets/img/dwqa.png'?>" alt="DWQA Logo" width="100" height="100">
						<ul>
							<li>By <a href="https://www.designwall.com/" target="blank">DesignWall</a></li>
							<li>Version <?php echo $dwqa->version;?></li>
						</ul>
					</div>
			</div>
			<?php settings_errors(); ?>
			<?php $active_tab = isset( $_GET[ 'tab' ] ) ? esc_html( $_GET['tab'] ) : 'general'; ?>
			<h2 class="nav-tab-wrapper">
				<a href="?post_type=dwqa-question&amp;page=dwqa-settings&amp;tab=general" class="nav-tab <?php echo $active_tab == 'general' ? 'nav-tab-active' : ''; ?>"><?php _e( 'General','dwqa' ); ?></a>
				<a href="?post_type=dwqa-question&amp;page=dwqa-settings&amp;tab=email" class="nav-tab <?php echo $active_tab == 'email' ? 'nav-tab-active' : ''; ?>"><?php _e( 'Emails','dwqa' ); ?></a>
				<a href="?post_type=dwqa-question&amp;page=dwqa-settings&amp;tab=permission" class="nav-tab <?php echo $active_tab == 'permission' ? 'nav-tab-active' : ''; ?>"><?php _e( 'Permissions','dwqa' ); ?></a>
				<?php if ( class_exists( 'DWQA_Envato_API' ) ) : ?>
				<a href="?post_type=dwqa-question&amp;page=dwqa-settings&amp;tab=envato" class="nav-tab <?php echo $active_tab == 'envato' ? 'nav-tab-active' : ''; ?>"><?php _e( 'Envato API','dwqa' ); ?></a>
				<?php endif; ?>
			</h2>
			
			<form method="post" action="options.php" class="dwqa-settings-body">
			<?php

			switch ($active_tab) {
				case 'email':

					echo '<div class="dwqa-notification-settings">';
					echo $this->email_tabs();
					// email setup section
					if ( $email_section === 'general' ) :
						settings_fields( 'dwqa-subscribe-settings' );
						echo '<h3>'.__( 'Email settings','dwqa' ).'</h3>';
						echo '<table class="form-table"><tr>';
						echo '<th scope="row">'.__( 'Email Logo','dwqa' ).'</th><td>';
						dwqa_subscrible_email_logo_display();
						echo '<tr>';
						$this->email_sendto_address_display();
						echo '</tr>';
						echo '</td></tr></table>';
						do_settings_sections( 'dwqa-email' );
					endif;

					echo '<div class="dwqa-mail-templates">';
					echo '<div class="progress-bar"><div class="progress-bar-inner"></div></div>';

					echo '<div class="tab-content">';

					if ( $email_section == 'new-question' ) :
						settings_fields( 'dwqa-subscribe-settings-new-question' );
						echo '<div id="new-question" class="tab-pane active">';
						echo '<h3>'.__( 'New Question Notifications (to Admin)','dwqa' ) . '</h3>';
						echo '<table class="form-table">';
						echo '<tr>';
						dwqa_subscrible_enable_new_question_notification();
						echo '</tr>';
						echo '<tr>';
						dwqa_subscrible_new_question_email_subject_display();
						echo '</tr>';
						echo '<tr>';
						dwqa_subscrible_new_question_email_display();
						echo '</tr>';
						echo '</table>';
						echo '</div>'; //End tab for New Question Notification
					endif;

					// new answer section
					if ( $email_section == 'new-answer' ) :

						settings_fields( 'dwqa-subscribe-settings-new-answer' );
						// new answer to follower section
						echo '<div id="new-answer-followers" class="tab-pane">';
						echo '<h3>'.__( 'New Answer Notifications (to Followers)','dwqa' ). '</h3>';
						echo '<table class="form-table">';
						echo '<tr>';
						dwqa_subscrible_enable_new_answer_followers_notification();
						echo '</tr>';
						echo '<tr>';
						dwqa_subscrible_new_answer_followers_email_subject_display();
						echo '</tr>';
						echo '<tr>';
						dwqa_subscrible_new_answer_followers_email_display();
						echo '</tr>';
						echo '</table>';
						echo '<hr>';
						echo '</div>';//End tab for New Answer Notification To Followers

						echo '<div id="new-answer" class="tab-pane">';
						echo '<h3>'.__( 'New Answer Notifications (to Author)','dwqa' ). '</h3>';
						echo '<table class="form-table">';
						echo '<tr>';
						dwqa_subscrible_enable_new_answer_notification();
						echo '<tr>';
						dwqa_subscrible_new_answer_email_subject_display();
						echo '<tr>';
						dwqa_subscrible_new_answer_email_display();
						echo '</tr>';
						echo '</table>';
						echo '</div>';//End tab for New Answer Notification

					endif;

					if ( $email_section == 'new-comment' ) :
						settings_fields( 'dwqa-subscribe-settings-new-comment' );
						echo '<div id="new-comment-question-followers" class="tab-pane">';
						echo '<h3>'.__( 'New Comment to Question Notifications (to Followers)','dwqa' ). '</h3>';
						echo '<table class="form-table">';
						echo '<tr>';
						dwqa_subscrible_enable_new_comment_question_followers_notification();
						echo '</tr>';
						echo '<tr>';
						dwqa_subscrible_new_comment_question_followers_email_subject_display();
						echo '</tr>';
						echo '<tr>';
						dwqa_subscrible_new_comment_question_followers_email_display();
						echo '</tr>';
						echo '</table>';
						echo '<hr>';
						echo '</div>'; //End tab for New Comment to Question Notification


						echo '<div id="new-comment-question" class="tab-pane">';
						echo '<h3>'.__( 'New Comment to Question Notifications (to Author)','dwqa' ). '</h3>';
						echo '<table class="form-table">';
						echo '<tr>';
						dwqa_subscrible_enable_new_comment_question_notification();
						echo '</tr>';
						echo '<tr>';
						dwqa_subscrible_new_comment_question_email_subject_display();
						echo '</tr>';
						echo '<tr>';
						dwqa_subscrible_new_comment_question_email_display();
						echo '</tr>';
						echo '</table>';
						echo '<hr>';
						echo '</div>'; //End tab for New Comment to Question Notification


						echo '<div id="new-comment-answer-followers" class="tab-pane">';
						echo '<h3>'.__( 'New Comment to Answer Notifications (to Followers)','dwqa' ). '</h3>';
						echo '<table class="form-table">';
						echo '<tr>';
						dwqa_subscrible_enable_new_comment_answer_followers_notification();
						echo '</tr>';
						echo '<tr>';
						dwqa_subscrible_new_comment_answer_followers_email_subject_display();
						echo '</tr>';
						echo '<tr>';
						dwqa_subscrible_new_comment_answer_followers_email_display();
						echo '</tr>';
						echo '</table>';
						echo '<hr>';
						echo '</div>'; //End tab for New Comment to Answer Notification


						echo '<div id="new-comment-answer" class="tab-pane">';
						echo '<h3>'.__( 'New Comment to Answer Notifications (to Author)','dwqa' ). '</h3>';
						echo '<table class="form-table">';
						echo '<tr>';
						dwqa_subscrible_enable_new_comment_answer_notification();
						echo '</tr>';
						echo '<tr>';
						dwqa_subscrible_new_comment_answer_email_subject_display();
						echo '</tr>';
						echo '<tr>';
						dwqa_subscrible_new_comment_answer_email_display();
						echo '</tr>';
						echo '</table>';
						echo '</div>'; //End tab for New Comment to Answer Notification
					endif;

					submit_button( __( 'Save all changes','dwqa' ) );
					echo '</div>'; //End wrap mail template settings

					echo '</div>'; //End wrap tab content

					echo '</div>'; //The End
					break;
				case 'permission':
					settings_fields( 'dwqa-permission-settings' );
					dwqa_permission_display();
					submit_button();
					break;
				case 'envato':
					settings_fields( 'dwqa-envato-settings' );
					do_settings_sections( 'dwqa-envato' );
					submit_button();
					break;
				default:
					settings_fields( 'dwqa-settings' );
					do_settings_sections( 'dwqa-settings' );
					submit_button();
					break;
			}

			?>
			</form>
		</div>
		<?php
	}

	public function new_answer_forward() {
		echo '<th>'.__( 'Forward to', 'dwqa' ).'</th>';
		$this->textarea_field( 'dwqa_subscrible_new_answer_forward' );
	}

	public function new_comment_question_forward() {
		echo '<th>'.__( 'Forward to', 'dwqa' ).'</th>';
		$this->textarea_field( 'dwqa_subscrible_new_comment_question_forward' );
	}

	public function new_comment_answer_forward() {
		echo '<th>'.__( 'Forward to', 'dwqa' ).'</th>';
		$this->textarea_field( 'dwqa_subscrible_new_comment_answer_forward' );
	}

	public function email_sendto_address_display(){
		echo '<th>'.__( 'Forward to', 'dwqa' ).'</th>';
		$this->textarea_field( 'dwqa_subscrible_sendto_address' );
	}

	public function email_cc_address_display(){
		echo '<p>'.__( 'Cc', 'dwqa' ).'</p>';
		$this->input_text_field( 'dwqa_subscrible_cc_address' );
	}

	public function email_bcc_address_display(){
		echo '<p>'.__( 'Bcc', 'dwqa' ).'</p>';
		$this->input_text_field( 'dwqa_subscrible_bcc_address' );
	}

	public function email_from_address_display(){
		$this->input_text_field( 'dwqa_subscrible_from_address', get_bloginfo( 'admin_email' ), false, __( 'This address will be used as the sender of the outgoing emails.','dwqa' ) );
	}

	public function email_from_name_display(){
		$this->input_text_field( 'dwqa_subscrible_from_name', get_bloginfo( 'name' ), false, __( 'This address will be used as the sender of the outgoing name.','dwqa' ) );
	}

	public function email_send_copy_to_admin(){
		$this->input_checkbox_field(
			'dwqa_subscrible_send_copy_to_admin',
			__( 'Send a copy of every email to admin.','dwqa' )
		);
	}
	public function enable_email_delay(){
		$this->input_checkbox_field(
			'dwqa_enable_email_delay',
			__( 'Email Delay*','dwqa' )
		);
	}

	public function input_text_field( $option, $default = null, $label = false, $description = false, $class = false ){
		echo '<p><label for="'.$option.'"><input type="text" id="'.$option.'" name="'.$option.'" value="'.get_option( $option, $default ).'" class="regular-text" />';
		if ( $description ) {
			echo '<br><span class="description">'.$description.'</span>';
		}
		echo '</label></p>';
	}

	public function textarea_field( $option, $lable = false, $description = false, $class = false ) {
		echo '<td><textarea type="text" id="'.$option.'" name="'.$option.'" rows="5" class="widefat" >'.get_option( $option ).'</textarea>';
		if ( $description ) {
			echo '<br><span class="description">'.$description.'</span>';
		}
		echo '<td>';
	}

	public function input_checkbox_field( $option, $description = false ){
		echo '</p><label for="'.$option.'"><input id="'.$option.'" name="'.$option.'" type="checkbox" '.checked( true, (bool ) get_option( $option ), false ).' value="true"/>';
		if ( $description ) {
			echo '<span class="description">'.$description.'</span>';
		}
		echo '</label></p>';
	}
}

?>
