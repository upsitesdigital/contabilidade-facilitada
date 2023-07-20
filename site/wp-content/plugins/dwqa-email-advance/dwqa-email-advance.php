<?php  
/**
 *  Plugin Name: DW Email Advance
 *  Description: Advance Notification Email controller
 *  Author: DesignWall
 *  Author URI: http://www.designwall.com
 *  Version: 1.0.0
 *  Text Domain: dwqa-email
 *  @since 1.0.0
 */

// DW_EMBED plugin dir path
if ( ! defined( 'DW_EMAIL_DIR' ) ) {
	define( 'DW_EMAIL_DIR', plugin_dir_path( __FILE__ ) );
}
// DW_EMBED plguin dir URI
if ( ! defined( 'DW_EMAIL_URI' ) ) {
	define( 'DW_EMAIL_URI', plugin_dir_url( __FILE__ ) );
}

if ( ! defined( 'DWQA_EMAIL_NOTIFICATION_CLASSES' ) ) {
	define( 'DWQA_EMAIL_NOTIFICATION_CLASSES', plugin_dir_path( __FILE__ ).'classes' );
}

class DWQA_ADVANCE_EMAIL_CATEGORY_ADD_ON {

	public function __construct() {
		$this->dir = DW_EMAIL_DIR;
		$this->uri = DW_EMAIL_URI;

		// add_action( 'admin_menu', array( $this, 'admin_menu' ), 15 );
		add_action( 'dwqa-question_category_add_form_fields', array( $this, 'dwqa_category_generate_html_add_form_fields' ) );
		add_action( 'dwqa-question_category_edit_form_fields', array( $this, 'dwqa_category_generate_html_edit_form_fields') );
		add_action( 'create_dwqa-question_category', array( $this, 'dwqa_save_category_option' ) );
		add_action( 'edit_dwqa-question_category', array( $this, 'dwqa_save_category_option') );
		
	}
		function dwqa_category_generate_html_add_form_fields(){
			?>
			<div class="form-field">
				<label for="manager-emails"><?php _e( 'Manager Emails','dwqa-advance-mail' ) ?></label>
				<textarea id="manager-emails" name="manager-emails" rows="5" cols="40"></textarea>
				<p class="description"><?php _e( 'List of manger Emails','dwqa-advance-mail' ); ?></p>
			</div>
		<?php
		}


		function dwqa_category_generate_html_edit_form_fields( $tag ){
			$options = $this->dwqa_get_category_option( $tag->term_id );
			
			$textarea = implode( "\n" , $options['manager_emails']);

			?>
			<tr class="form-field">
				<th scope="row" valign="top">
					<label for="manager-emails">
						<?php _e( 'Manager Emails','dwqa-advance-mail' ) ?>
					</label>
				</th>
				<td>
					<div class="form-field">
						<textarea id="manager-emails" name="manager-emails" rows="5" cols="40"><?php echo esc_attr( $textarea ); ?></textarea>
						<p class="description"><?php _e( 'List of manger Emails','dwqa-advance-mail' ); ?></p>
					</div>
				</td>
			</tr>
			<?php
		}

		function dwqa_save_category_option( $category_id ){
			$category_options = array();

			if ( isset( $_POST['manager-emails'] ) ) {
				$text = trim($_POST['manager-emails']);
				$emails = explode("\n", $text);
				$category_options['manager_emails'] = $emails;
			}
			if ( ! empty( $category_options ) ) {
				update_option( 'dwqa_email_category_option_'.$category_id, $category_options );
			}
		}

		function dwqa_get_category_option( $category_id ){
			return get_option( 'dwqa_email_category_option_'.$category_id, array(
				'manager_emails'         => '',
				) );
		}
}
$GLOBALS['dwqa_advance_email_category_add_on'] = new DWQA_ADVANCE_EMAIL_CATEGORY_ADD_ON();

class DWQA_ADVANCE_EMAIL_NOTIFICATION {

	public function __construct() {
		add_action( 'dwqa_add_question', array( $this, 'new_question_notify' ), 10, 2 );
		add_action( 'dwqa_add_answer', array( $this, 'new_answer_nofity' ) );
		add_action( 'dwqa_update_answer', array( $this, 'new_answer_nofity' ) );
		add_action( 'wp_insert_comment', array( $this, 'new_comment_notify' ), 10, 2 );
	}

	public function new_question_notify( $question_id, $user_id ) {
		// receivers
		$terms = wp_get_post_terms( $question_id, 'dwqa-question_category' );
		$term_id = $terms[0]->term_id;
		$options = DWQA_ADVANCE_EMAIL_CATEGORY_ADD_ON::dwqa_get_category_option( $term_id );
		$manager_emails = $options['manager_emails'];

		$enabled = get_option( 'dwqa_subscrible_enable_new_question_notification', 1 );
		if ( ! $enabled ) {
			return false;
		}
		$question = get_post( $question_id );
		if ( ! $question ) {
			return false;
		}

		$subject = get_option( 'dwqa_subscrible_new_question_email_subject' );
		if ( ! $subject ) {
			$subject = __( 'A new question was posted on {site_name}', 'dwqa' );
		}
		$subject = str_replace( '{site_name}', get_bloginfo( 'name' ), $subject );
		$subject = str_replace( '{question_title}', $question->post_title, $subject );
		$subject = str_replace( '{question_id}', $question->ID, $subject );
		$subject = str_replace( '{username}', get_the_author_meta( 'display_name', $user_id ), $subject );
		// To send HTML mail, the Content-type header must be set
		$headers  = 'MIME-Version: 1.0' . "\r\n";
		$headers .= 'Content-type: text/html; charset=utf-8' . "\r\n";
		//From email 
		$from_email = get_option( 'dwqa_subscrible_from_address' );
		if ( $from_email ) {
			$headers .= 'From: ' . $from_email . "\r\n";
		}

		//Cc email
		$cc_address = get_option( 'dwqa_subscrible_cc_address' );
		if ( $cc_address ) {
			$headers .= 'Cc: ' . $cc_address . "\r\n";
		}
		//Bcc email
		$bcc_address = get_option( 'dwqa_subscrible_bcc_address' );
		if ( $bcc_address ) {
			$headers .= 'Bcc: ' . $bcc_address . "\r\n";
		}
		
		$message = dwqa_get_mail_template( 'dwqa_subscrible_new_question_email', 'new-question' );
		if ( ! $message ) {
			return false;
		}
		// Replacement
		
		foreach ($manager_emails as $email) {
			$user = get_user_by( 'email', $email );
			if ( $user ) {
				$message = str_replace( '{admin}', get_the_author_meta( 'display_name', $user->ID ), $message );
			}
			//sender
			$message = str_replace( '{user_avatar}', get_avatar( $user_id, '60' ), $message );
			$message = str_replace( '{user_link}', get_author_posts_url( $user_id ), $message );
			$message = str_replace( '{username}', get_the_author_meta( 'display_name', $user_id ), $message );
			//question
			$message = str_replace( '{question_link}', get_permalink( $question_id ), $message );
			$message = str_replace( '{question_title}', $question->post_title, $message );
			$message = str_replace( '{question_content}', $question->post_content, $message );
			// Site info
			$logo = get_option( 'dwqa_subscrible_email_logo', '' );
			$logo = $logo ? '<img src="' . $logo . '" alt="' . get_bloginfo( 'name' ) . '" style="max-width: 100%; height: auto;" />' : '';
			$message = str_replace( '{site_logo}', $logo, $message );
			$message = str_replace( '{site_name}', get_bloginfo( 'name' ), $message );
			$message = str_replace( '{site_description}', get_bloginfo( 'description' ), $message );
			$message = str_replace( '{site_url}', site_url(), $message );

			// start send out email
			wp_mail( $email, $subject, $message, $headers );

		}
	}

	public function new_answer_nofity( $answer_id ) {
		$enabled = get_option( 'dwqa_subscrible_enable_new_answer_notification', 1 );
		if ( ! $enabled ) {
			return false;
		}

		//Admin email
		
		$question_id = get_post_meta( $answer_id, '_question', true );
		$terms = wp_get_post_terms( $question_id, 'dwqa-question_category' );
		$term_id = $terms[0]->term_id;
		$options = DWQA_ADVANCE_EMAIL_CATEGORY_ADD_ON::dwqa_get_category_option( $term_id );
		$manager_emails = $options['manager_emails'];
		$question = get_post( $question_id );
		$answer = get_post( $answer_id );
		if ( $answer->post_status != 'publish' && $answer->post_status != 'private' ) {
			return false;
		}

		$subject = get_option( 'dwqa_subscrible_new_answer_email_subject' );
		if ( ! $subject ) {
			$subject = __( 'A new answer for "{question_title}" was posted on {site_name}', 'dwqa' );
		}
		$subject = str_replace( '{site_name}', get_bloginfo( 'name' ), $subject );
		$subject = str_replace( '{question_title}', $question->post_title, $subject );
		$subject = str_replace( '{question_id}', $question->ID, $subject );

		// To send HTML mail, the Content-type header must be set
		$headers  = 'MIME-Version: 1.0' . "\r\n";
		$headers .= 'Content-type: text/html; charset=utf-8' . "\r\n";

		//From email 
		$from_email = get_option( 'dwqa_subscrible_from_address' );
		if ( $from_email ) {
			$headers .= 'From: ' . $from_email . "\r\n";
		}

		$message = dwqa_get_mail_template( 'dwqa_subscrible_new_answer_email', 'new-answer' );
		if ( ! $message ) {
			return false;
		}

		//Receiver
		$message = str_replace( '{question_author}', 'manager', $message );
		//Answer
		$answer  = get_post( $answer_id ); 

		if ( dwqa_is_anonymous( $answer_id ) ) {
			$user_id = 0;
			$display_name = __( 'Anonymous', 'dwqa' );
			$avatar = get_avatar( $user_id, '60' );
			$answer_author = __( 'Anonymous', 'dwqa' );
		} else {
			$user_id = $answer->post_author;
			$display_name = get_the_author_meta( 'display_name', $user_id );
			$avatar = get_avatar( $user_id, '60' );
			$answer_author = '<a href="'.get_author_posts_url( $user_id ).'" >'.get_the_author_meta( 'display_name', $user_id ).'</a>';
		}


		$subject = str_replace( '{username}', $display_name, $subject );
		$subject = str_replace( '{answer_author}', $answer_author, $subject );

		$message = str_replace( '{answer_avatar}', $avatar, $message );
		$message = str_replace( '{answer_author}', $answer_author, $message );
		$message = str_replace( '{question_link}', get_permalink( $question->ID ), $message );
		$message = str_replace( '{answer_link}', get_permalink( $question->ID ) . '#answer-' . $answer_id, $message );
		$message = str_replace( '{question_title}', $question->post_title, $message );
		$message = str_replace( '{answer_content}', $answer->post_content, $message );
		// logo replace
		$logo = get_option( 'dwqa_subscrible_email_logo', '' );
		$logo = $logo ? '<img src="'.$logo.'" alt="'.get_bloginfo( 'name' ).'" style="max-width: 100%; height: auto;" />' : '';
		$message = str_replace( '{site_logo}', $logo, $message );
		$message = str_replace( '{site_name}', get_bloginfo( 'name' ), $message );
		$message = str_replace( '{site_description}', get_bloginfo( 'description' ), $message );
		$message = str_replace( '{site_url}', site_url(), $message );

		$enable_notify = get_option( 'dwqa_subscrible_enable_new_answer_followers_notification', true );
		foreach ($manager_emails as $email) {
			wp_mail( $email, $subject, $message, $headers );
		}
	}

	public function new_comment_notify( $comment_id, $comment ) {
		$parent = get_post_type( $comment->comment_post_ID );
		
		//Admin email
		$admin_email = get_bloginfo( 'admin_email' );
		$enable_send_copy = get_option( 'dwqa_subscrible_send_copy_to_admin' );



		if ( 1 == $comment->comment_approved && ( 'dwqa-question' == $parent || 'dwqa-answer' == $parent ) ) {
			$post_parent = get_post( $comment->comment_post_ID );
			if ( $parent == 'dwqa-question' ) {
				$enabled = get_option( 'dwqa_subscrible_enable_new_comment_question_notification', 1 );
				$terms = wp_get_post_terms( $post_parent->ID, 'dwqa-question_category' );
				$term_id = $terms[0]->term_id;
			} elseif ( $parent == 'dwqa-answer' ) {
				$enabled = get_option( 'dwqa_subscrible_enable_new_comment_answer_notification', 1 );
				$question_id = get_post_meta( $post_parent->ID, '_question', true );
				$terms = wp_get_post_terms( $question_id, 'dwqa-question_category' );
				$term_id = $terms[0]->term_id;
			}
		
			if ( ! $enabled ) {
				return false;
			}

			$options = DWQA_ADVANCE_EMAIL_CATEGORY_ADD_ON::dwqa_get_category_option( $term_id );
			$manager_emails = $options['manager_emails'];

			
			// To send HTML mail, the Content-type header must be set
			$headers  = 'MIME-Version: 1.0' . "\r\n";
			$headers .= 'Content-type: text/html; charset=utf-8' . "\r\n";
			//From email 
			$from_email = get_option( 'dwqa_subscrible_from_address' );
			if ( $from_email ) {
				$headers .= 'From: ' . $from_email . "\r\n";
			}
			
			if ( $parent == 'dwqa-question' ) {
				$message = dwqa_get_mail_template( 'dwqa_subscrible_new_comment_question_email', 'new-comment-question' );    
				$subject = get_option( 'dwqa_subscrible_new_comment_question_email_subject',__( '[{site_name}] You have a new comment for question {question_title}', 'dwqa' ) );
				$message = str_replace( '{question_author}', 'manager', $message );
				$question = $post_parent;
			} else {
				$message = dwqa_get_mail_template( 'dwqa_subscrible_new_comment_answer_email', 'new-comment-answer' );
				$subject = get_option( 'dwqa_subscrible_new_comment_answer_email_subject',__( '[{site_name}] You have a new comment for answer', 'dwqa' ) );
				$message = str_replace( '{answer_author}', 'manager', $message );
				$question_id = get_post_meta( $post_parent->ID, '_question', true );
				$question = get_post( $question_id );
			}
			$subject = str_replace( '{site_name}', get_bloginfo( 'name' ), $subject );
			$subject = str_replace( '{question_title}', $question->post_title, $subject );
			$subject = str_replace( '{question_id}', $question->ID, $subject );
			$subject = str_replace( '{username}',get_the_author_meta( 'display_name', $comment->user_id ), $subject );

			if ( ! $message ) {
				return false;
			}
			// logo replace
			$logo = get_option( 'dwqa_subscrible_email_logo','' );
			$logo = $logo ? '<img src="'.$logo.'" alt="'.get_bloginfo( 'name' ).'" style="max-width: 100%; height: auto;" />' : '';
			$subject = str_replace( '{comment_author}', get_the_author_meta( 'display_name', $comment->user_id ), $subject );
			$message = str_replace( '{site_logo}', $logo, $message );
			$message = str_replace( '{question_link}', get_permalink( $question->ID ), $message );
			$message = str_replace( '{comment_link}', get_permalink( $question->ID ) . '#comment-' . $comment_id, $message );
			$message = str_replace( '{question_title}', $question->post_title, $message );
			$message = str_replace( '{comment_author_avatar}', get_avatar( $comment->user_id, '60' ), $message );
			$message = str_replace( '{comment_author_link}', get_author_posts_url( $comment->user_id ), $message );
			$message = str_replace( '{comment_author}', get_the_author_meta( 'display_name', $comment->user_id ), $message );
			$message = str_replace( '{comment_content}', $comment->comment_content, $message );
			$message = str_replace( '{site_name}', get_bloginfo( 'name' ), $message );
			$message = str_replace( '{site_description}', get_bloginfo( 'description' ), $message );
			$message = str_replace( '{site_url}', site_url(), $message );

			
			foreach ($manager_emails as $email) {
				wp_mail( $email, $subject, $message, $headers );
			}
		}
	}
}
$GLOBALS['dwqa_advance_email_notification'] = new DWQA_ADVANCE_EMAIL_NOTIFICATION();
