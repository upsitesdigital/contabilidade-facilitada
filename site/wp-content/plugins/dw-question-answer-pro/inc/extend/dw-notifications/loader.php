<?php
// Exit if accessed directly
if ( !defined( 'ABSPATH' ) ) exit;

if ( !class_exists( 'DWQA_DW_Notifications' ) ) :

	class DWQA_DW_Notifications{

		public function __construct() {
		// $this->includes();

			add_action( 'dwqa_add_question', array( $this, 'dwqa_notif_add_question' ), 10, 2 );
			add_action( 'dwqa_add_answer', array( $this, 'dwqa_notif_add_answer' ), 10, 2 );
			add_action( 'wp_insert_comment', array( $this, 'dwqa_notif_insert_comment' ), 10, 2 );

		}

		public function includes(){
			require_once(DWQA_DIR . 'inc/extend/dw-notifications/functions.php');
		}

		public function dwqa_notif_add_question( $question_id, $user_id  ) {
		//at moment, only notif to admin

			$admins = get_users('role=Administrator');

			if(!$admins){
				return false;
			}

			if(!$user_id){
				$user_name = __('Anonymous', 'dwqa');
			}else{
				$user_name = get_the_author_meta( 'display_name', $user_id );
			}

			$question = get_post( $question_id );
			if ( ! $question ) {
				return false;
			}

			$title = '<strong>'.$user_name.'</strong> asked <strong>'.$question->post_title.'</strong>.';
			$title = apply_filters("dwqa_dw_notifications_add_question_title", $title, $question_id, $user_id);

			$notif = array('title'=>$title, 'link' => get_permalink($question_id), 'type' => 'asked');

			foreach($admins as $admin){
				dwnotif_add_user_notify($admin->ID, $notif);
			}

		}

		public function dwqa_notif_add_answer( $answer_id, $question_id  ) {
			$answer = get_post( $answer_id );

			if ( ! $answer || $answer->post_type !='dwqa-answer') {
				return false;
			}
			if(dwqa_is_anonymous($answer_id)){
				$user_answer_display_name = get_post_meta( $answer_id, '_dwqa_anonymous_name', true );
				$user_name = $user_answer_display_name ? sanitize_text_field( $user_answer_display_name ) : __( 'Anonymous', 'dwqa' );
			}else{
				$user_name = get_the_author_meta( 'display_name', $answer->post_author );
			}

			$question = get_post( $question_id );
			if ( ! $question || $question->post_type !='dwqa-question') {
				return false;
			}
			
			

			if(isset($_POST['dwqa-mention-submit-form']) && !dwqa_is_anonymous($answer_id)){
				$mentionUsers = dwqa_convert_mention_users($_POST['dwqa-mention-submit-form']);
				$mentionUsers = array_unique($mentionUsers);
				if(!empty($mentionUsers)){
					$title = '<strong>'.$user_name.'</strong> tagged you in <strong>'.$question->post_title.'</strong>.';
					$title = apply_filters("dwqa_dw_notifications_add_answer_mention_title", $title, $answer_id, $question_id);

					$notif = array('title'=>$title, 'link' => get_permalink($question_id). '#answer-' . $answer_id, 'type' => 'tagged' );
					foreach($mentionUsers as $user_id){
						if(!$answer->post_author || $answer->post_author == $user_id) continue;

						dwnotif_add_user_notify($user_id, $notif);
					}
				}
			}

			$title = '<strong>'.$user_name.'</strong> answered in <strong>'.$question->post_title.'</strong>.';
			$title = apply_filters("dwqa_dw_notifications_add_answer_title", $title, $answer_id, $question_id);

			$notif = array('title'=>$title, 'link' => get_permalink($question_id), 'type' => 'answered' );
			
			// get all follower
			$followers = get_post_meta( $question_id, '_dwqa_followers' );
			$followers = array_unique($followers);
			if ( !empty( $followers ) && is_array( $followers ) ) {
				foreach( $followers as $follower ) {
					if ( is_numeric( $follower ) ) {
						//only send to user_id because user email => anonymous

						// prevent send to answer author
						if(!$answer->post_author || $answer->post_author == $follower) continue;

						dwnotif_add_user_notify($follower, $notif);
					}
				}
			}
		}


		public function dwqa_notif_insert_comment( $comment_id, $comment ) {
			$post_parent = get_post( $comment->comment_post_ID );
			// echo '<pre>';
			// print_r($post_parent);
			// echo '</pre>';
			// echo '<pre>';
			// print_r($comment);
			// echo '</pre>';
			// die();
			if(!$post_parent){
				return false;
			}

			if ( 1 == $comment->comment_approved && ( 'dwqa-question' == $post_parent->post_type || 'dwqa-answer' == $post_parent->post_type ) && $comment->user_id > 0 ) {
				// $user_name = get_the_author_meta( 'display_name', $comment->user_id );
				$user_name = $comment->comment_author;


				$question_id = $comment->comment_post_ID;
				if ( 'dwqa-answer' == get_post_type( $question_id ) ) {
					$question_id = dwqa_get_question_from_answer_id( $question_id );
				}
				$question = get_post( $question_id );

				if(isset($_POST['dwqa-mention-submit-form']) && $comment->user_id > 0){
					$mentionUsers = dwqa_convert_mention_users($_POST['dwqa-mention-submit-form']);
					$mentionUsers = array_unique($mentionUsers);
					if(!empty($mentionUsers)){
						$title = '<strong>'.$user_name.'</strong> tagged you in <strong>'.$question->post_title.'</strong>.';
						$title = apply_filters("dwqa_dw_notifications_add_comment_mention_title", $title, $comment_id, $comment);

						$notif = array('title'=>$title, 'link' => get_permalink($question_id). '#comment-' . $comment_id, 'type' => 'replied' );
						foreach($mentionUsers as $user_id){
							if(!$comment->user_id || $comment->user_id == $user_id) continue;

							dwnotif_add_user_notify($user_id, $notif);
						}
					}
				}

				// send to post author
				if( $comment->user_id && $comment->user_id != $post_parent->post_author){
					$title = '<strong>'.$user_name.'</strong> replied in <strong>'.$question->post_title.'</strong>.';
					$title = apply_filters("dwqa_dw_notifications_add_comment_title", $title, $comment_id, $comment);

					$notif = array('title'=>$title, 'link' => get_permalink($question_id). '#comment-' . $comment_id, 'type' => 'replied');

					dwnotif_add_user_notify($post_parent->post_author, $notif);
				}

				//send to comment in post
				$comment_query = new WP_Comment_Query( array( 'post_id' => $comment->comment_post_ID) );
				if($comment_query->comments){
					foreach($comment_query->comments as $comment_value){
						if($comment_value->user_id && $comment_value->user_id != $post_parent->post_author && $comment_value->user_id != $comment->user_id){
							$title = '<strong>'.$user_name.'</strong> replied in <strong>'.$question->post_title.'</strong>.';
							$title = apply_filters("dwqa_dw_notifications_add_comment_title", $title, $comment_id, $comment);

							$notif = array('title'=>$title, 'link' => get_permalink($question_id). '#comment-' . $comment_id, 'type' => 'replied');

							dwnotif_add_user_notify($comment_value->user_id, $notif);
						}
					}
				}
			}
		}

}
endif;
