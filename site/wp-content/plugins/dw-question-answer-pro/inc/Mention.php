<?php
if ( !defined( 'ABSPATH' ) ) exit;

class DWQA_Mention {

	public function __construct() {
		global $dwqa_general_settings;
		if(isset($dwqa_general_settings['mention-user']) && $dwqa_general_settings['mention-user']){
			add_action( 'wp_enqueue_scripts', array($this,'dwqa_mention_script' ));

			add_action('wp_ajax_dwqa_mention_user', array($this, 'dwqa_mention_user'));
			add_action('wp_ajax_nopriv_dwqa_mention_user', array($this, 'dwqa_mention_user'));

			//custom tinymce
			add_filter("mce_external_plugins", array( $this, 'add_tinymce_plugin' ) );
			add_filter('dwqa_array_setting_wp_editor', array($this, 'dwqa_array_setting_wp_editor'));

			// add_action( 'dwqa_before_question_submit_button', array($this,'dwqa_add_hidden_input_mention'), 10);
			add_action( 'dwqa_before_answer_submit_button', array($this,'dwqa_add_hidden_input_mention'), 10);
			add_action( 'dwqa_before_edit_submit_button', array($this,'dwqa_add_hidden_input_mention'), 10);
			add_action( 'comment_form', array($this,'dwqa_comment_add_hidden_input_mention'), 10, 1);

			// add_action('dwqa_add_question', array($this,'dwqa_add_question_subscribe_mention'), 11, 2);
			//question not available
			add_action('dwqa_add_answer', array($this,'dwqa_add_answer_subscribe_mention'), 11, 2);
			add_action( 'wp_insert_comment', array( $this, 'dwqa_add_comment_subscribe_mention' ), 11, 2 );

			//
			add_filter('dwqa_insert_answer_args', array($this, 'dwqa_add_answer_args_mention'), 10, 1);
			add_filter('dwqa_insert_comment_args', array($this, 'dwqa_add_comment_args_mention'), 10, 1);
		}
	}

	public function dwqa_add_answer_args_mention($args){
		if(isset($_POST['dwqa-mention-submit-form-highlight']) && isset($args['post_content']) && $args['post_content']!='' && is_user_logged_in()){
			
			$mentionUsers = json_decode((str_replace("\\", "", $_POST['dwqa-mention-submit-form-highlight'])), true);
			
			if(!empty($mentionUsers)){
				// @name => <strong>@name</strong>

				$content = $args['post_content'];
				foreach($mentionUsers as $user_name => $user_id){
					$content = str_replace('@'.$user_name, '<a href="'.dwqa_get_author_link($user_id).'" class="dwqa-mention-user">@'.$user_name.'</a>', $content);
				}
				$args['post_content'] = $content;
			}
		}
		return $args;
	}

	public function dwqa_add_comment_args_mention($args){
		if(isset($_POST['dwqa-mention-comment-highlight']) && isset($args['comment_content']) && $args['comment_content']!='' && is_user_logged_in()){
			
			$mentionUsers = json_decode((str_replace("\\", "", $_POST['dwqa-mention-comment-highlight'])), true);
			
			if(!empty($mentionUsers)){
				// @name => <strong>@name</strong>

				$content = $args['comment_content'];
				foreach($mentionUsers as $user_name => $user_id){
					$content = str_replace('@'.$user_name, '<a href="'.dwqa_get_author_link($user_id).'" class="dwqa-mention-user">@'.$user_name.'</a>', $content);
				}
				$args['comment_content'] = $content;
			}
		}
		return $args;
	}

	public function dwqa_add_answer_subscribe_mention($answer_id, $question_id ){
		if(isset($_POST['dwqa-mention-submit-form']) && is_user_logged_in()){
			$mentionUsers = dwqa_convert_mention_users($_POST['dwqa-mention-submit-form']);
			if(!empty($mentionUsers)){
				foreach($mentionUsers as $user_id){
					if ( !dwqa_is_followed( $question_id, $user_id ) ) {
						add_post_meta( $question_id, '_dwqa_followers', $user_id );
					}
				}
				do_action('dwqa_add_answer_subscribe_mention', $mentionUsers, $answer_id, $question_id);
			}
		}
	}

	public function dwqa_add_comment_subscribe_mention( $comment_id, $comment ){
		if(isset($_POST['dwqa-mention-comment']) && is_user_logged_in()){
			$mentionUsers = dwqa_convert_mention_users($_POST['dwqa-mention-comment']);
			if(!empty($mentionUsers)){
				$question_id = $comment->comment_post_ID;
				if ( 'dwqa-answer' == get_post_type( $question_id ) ) {
					$question_id = dwqa_get_question_from_answer_id( $question_id );
				}
				foreach($mentionUsers as $user_id){
					if ( !dwqa_is_followed( $question_id, $user_id ) ) {
						add_post_meta( $question_id, '_dwqa_followers', $user_id );
					}
				}
				do_action('dwqa_add_comment_subscribe_mention', $mentionUsers, $comment_id, $comment);
			}
		}
	}

	public function dwqa_array_setting_wp_editor($settings){
		if(isset($settings['tinymce']) ){
			if(is_singular('dwqa-question') || is_singular('dwqa-answer')){

				$settings['tinymce']['plugins'] = 'mention';
				$settings['tinymce']['mentions'] = "{
						source: function (query, process, delimiter) {

							temp_this = this;
							var self = this;
						    // Do your ajax call
						    if (delimiter === '@' && query.length>2) {

						       	if ( self.xhr ) {
			        				self.xhr.abort();
			        			}
			        			self.xhr = jQuery.ajax({
			        				url: dwqa_mention.dwqa_mention_ajax_url,
			        				data: {
			        					action: 'dwqa_mention_user',
			        					nonce: dwqa_mention.dwqa_mention_nonce,
			        					name: query
			        				},
			        				type: 'POST',
			        				dataType: 'json',
			        				success: function(data) {
			        					if(typeof data !== 'undefined' && data.success && data.result != null && data.result.length>0) {
			        						
			        						process(jQuery.map(data.result, function(item) {
			        							return {
			        								name: item.name,
			        								value: item.id
			        							}
			        						}));
			        						
			        					}
			                        }
			                    });
						    }
							
						},
						delay: 1000,
						insert: function(item) {
							
							var insert_value = '@' + item.name;

							temp_this.id_map[item.name] = item.value;
							temp_this.updateHidden(insert_value);

						    return insert_value + '&nbsp;';
						},
						temp_this: {},
						id_map: {},
						updateHidden: function(insert_value) {

				        	var trigger = '@';
				        	var contents = tinymce.activeEditor.getContent({format : 'text'}) + insert_value;

				        	for(var key in temp_this.id_map) {
				        		var find = trigger+key;
				        		find = find.replace(/[^a-zA-Z 0-9@]+/g,'\\$&');
				        		regex = new RegExp(find, 'g');
				        		contents = contents.replace(regex, trigger+'['+temp_this.id_map[key]+']');
				        	}
				        	jQuery('#dwqa-mention-submit-form').val(contents);
				        	jQuery('#dwqa-mention-submit-form-highlight').val(JSON.stringify(temp_this.id_map));
				        }

					}";
			}
		}
		return $settings;
	}

	public function add_tinymce_plugin($plugins){
		// $plugins[] = 'advlink';
		// if ( is_singular( 'dwqa-question' ) || ( $dwqa_options['pages']['submit-question'] && is_page( $dwqa_options['pages']['submit-question'] ) ) ) {
		if ( is_singular( 'dwqa-question' )) {
			if(!isset($plugins['mention'])){
				$plugins['mention'] = DWQA_URI . 'assets/js/tinymce-plugin-mention.js';
			}
		}
		return $plugins;
	}

	public function dwqa_add_hidden_input_mention(){
		echo '<input type="hidden" id="dwqa-mention-submit-form" name="dwqa-mention-submit-form" value=""/>';
		echo '<input type="hidden" id="dwqa-mention-submit-form-highlight" name="dwqa-mention-submit-form-highlight" value=""/>';
	}

	public function dwqa_comment_add_hidden_input_mention(){
		echo '<input type="hidden" class="dwqa-mention-comment" name="dwqa-mention-comment" value=""/>';
		echo '<input type="hidden" class="dwqa-mention-comment-highlight" name="dwqa-mention-comment-highlight" value=""/>';
	}

	public function dwqa_mention_script(){

		$dwqa_mention_script_vars = apply_filters('dwnotif_script_vars', array(
			'dwqa_mention_nonce' => wp_create_nonce('dwqa_mention'),
			'dwqa_mention_ajax_url' => admin_url('admin-ajax.php')
		));

		wp_enqueue_script( 'dwqa-mention-user-script', DWQA_URI.'assets/js/dwqa-mention-user.js', array( 'jquery-ui-autocomplete', 'jquery' ));
		wp_localize_script( 'dwqa-mention-user-script', 'dwqa_mention', $dwqa_mention_script_vars );
		// wp_enqueue_style( 'dwqa-mention-user-style', DWQA_URI.'assets/css/dwqa-mention-user.css');
	}

	public function dwqa_mention_user(){
		check_ajax_referer( 'dwqa_mention', 'nonce' );

		if(!isset($_POST['name']) || $_POST['name']==''){
			echo json_encode(array('success'=>false));
			die();
		}

		$users = get_users(
			array(
				'search'=> '*'.esc_attr( $_POST['name'] ).'*',
				'search_columns' => array( 'display_name'),
				'number' => 10
			)
		);

		if(!$users || empty($users)){
			echo json_encode(array('success'=>false));
			die();
		}

		$result = array();
		foreach($users as $user){
			$result[] = array(
				'id' => $user->ID,
				'name' => $user->display_name,
			);
		}

		echo json_encode(array('success'=>true, 'result'=>$result));
		die();
	}

}
