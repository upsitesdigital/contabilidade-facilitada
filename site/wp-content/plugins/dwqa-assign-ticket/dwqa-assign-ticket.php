<?php
/**
 * Plugin Name: DWQA Assign Ticket
 * PLugin Description: A WordPress extension of DW Question & Answer Pro, which helps you admins to assign unresolved tickets to other admins or moderators.
 * Author: DesignWall
 * AUthor URI: https://www.designwall.com
 * Version: 1.0.2
 * Text Domain: dwqa-at
 */

add_action( 'plugins_loaded', 'dwqa_assign_ticket_load_textdomain' );
function dwqa_assign_ticket_load_textdomain() {
	load_plugin_textdomain( 'dwqa-at', false, basename( dirname( __FILE__ ) )  . '/languages' );
}

function dwqa_assign_ticket_load_template( $name, $extend = '', $include = false ) {
	if ( !empty( $extend ) ) {
		$name = $name . '-' . $extend;
	}

	$temp_paths = array(
		trailingslashit( get_stylesheet_directory() ) . dwqa()->template->get_template_dir(),
		trailingslashit( get_template_directory() ) . dwqa()->template->get_template_dir(),
		trailingslashit( plugin_dir_path( __FILE__ ) ) . 'templates/'
	);

	$template = false;
	foreach( $temp_paths as $temp ) {
		if ( file_exists( $temp . $name . '.php' ) ) {
			$template = $temp . $name . '.php';
			break;
		}
	}

	if ( file_exists( $template ) ) {
		if ( $include ) {
			include( $template );
			return;
		}
	}

	return $template;
}

add_filter( 'dwqa-load-template', 'dwqa_assign_ticket_replace_template', 10, 2 );
function dwqa_assign_ticket_replace_template( $template, $name ) {
	if ( 'content-question' == $name ) {
		$template = dwqa_assign_ticket_load_template( 'content', 'question' );
	}

	if ( 'content-single-question' == $name ) {
		$template = dwqa_assign_ticket_load_template( 'content', 'single-question' );
	}

	if ( 'archive-question-filter' == $name ) {
		$template = dwqa_assign_ticket_load_template( 'archive', 'question-filter' );
	}


	return $template;
}

add_filter( 'dwqa_prepare_archive_posts', 'dwqa_assign_ticket_filter_question', 10 );
function dwqa_assign_ticket_filter_question( $args ) {
	if ( isset( $_GET['filter'] ) && sanitize_text_field( $_GET['filter'] ) === 'my-ticket' ) {
		$query = array();
		$query['meta_query']['relation'] = 'AND';
		$query['meta_query'][] = array(
			'key' => '_dwqa_assign_to',
			'value' => get_current_user_id(),
			'compare' => '='
		);
		$query['meta_query'][] = array(
			'key' => '_dwqa_status',
			'value' => array( 'open', 're-open', 'answered' ),
			'compare' => 'IN'
		);

		$query = apply_filters( 'dwqa_assign_ticket_filter_question', $query, $args );
		$args = wp_parse_args( $args, $query );
	}

	return $args;
}

function dwqa_get_assignee_id( $question_id = 0 ) {
	if ( !$question_id ) {
		$question_id = get_the_ID();
	}

	if ( 'dwqa-question' !== get_post_type( $question_id ) ) {
		return false;
	}

	return get_post_meta( $question_id, '_dwqa_assign_to', true );
}

function dwqa_get_assignee_name( $question_id = 0 ) {
	if ( !$question_id ) {
		$question_id = get_the_ID();
	}

	if ( 'dwqa-question' !== get_post_type( $question_id ) ) {
		return false;
	}

	$user_id = dwqa_get_assignee_id( $question_id );

	if ( !$user_id ) {
		return '';
	}

	$user = get_user_by( 'id', $user_id );

	return apply_filters( 'dwqa_get_assignee_name', $user->display_name, $user_id, $user );
}

function dwqa_get_role_can_assign_ticket() {
	$dwqa_perm = get_option( 'dwqa_permission', array() );

	$roles = array();
	foreach( $dwqa_perm as $role => $post_type ) {
		if ( isset( $post_type['question'] ) && isset( $post_type['question']['manage'] ) && $post_type['question']['manage'] ) {
			$roles[] = $role;
		}
	}

	return $roles;
}

function dwqa_get_assign_able_lists() {
	$users = get_users( array(
		'role__in' => dwqa_get_role_can_assign_ticket()
	) );

	return $users;
}

function dwqa_get_assignee_template( $question_id = 0, $echo = true ) {
	if ( $question_id ) {
		$question_id = get_the_ID();
	}

	$users = dwqa_get_assign_able_lists();
	$assign_to = dwqa_get_assignee_id( $question_id );
	$html = '';
	if ( $users ) {
		$html .= '<span class="dwqa-question-assign-to">';
		$html .= __( 'Assign To:', 'dwqa-at' );
		$html .= '<select id="dwqa_assign_to" data-id="'.absint( $question_id ).'" data-nonce="'.wp_create_nonce( 'dwqa_assign_ticket_ajax' ).'">';
		$html .= '<option value="0">'.__( 'None', 'dwqa-at' ).'</option>';
		foreach( $users as $user ) {
			$html .= sprintf( '<option value="%s" %s>%s</option>', absint( $user->data->ID ), selected( $user->data->ID, $assign_to, false ) ? 'selected="selected"' : '', esc_html( $user->data->display_name ) );
		}
		$html .= '</select>';
		$html .= dwqa_assign_ticket_script();
		$html .= '</span>';
	}

	if ( $echo ) {
		echo $html;
	} else {
		return $html;
	}
}

function dwqa_assign_ticket_script( $echo = false ) {
	ob_start();
	?>
	<script type="text/javascript">
		jQuery(document).ready(function($){
			var current_selected = $('#dwqa_assign_to').val();
			$('#dwqa_assign_to').on('change',function(e){
				e.preventDefault();
				var t = $(this);
				console.log(t.val());
				if ( current_selected !== t.val() ) {
					var data = {
						action: 'dwqa_assign_ticket',
						post_id: parseInt( t.data('id') ),
						user_id: parseInt( t.val() ),
						_wpnonce: t.data('nonce')
					};

					$.ajax({
						url: '<?php echo admin_url( 'admin-ajax.php' ) ?>',
						data: data,
						type: 'POST',
						dataType: 'json',
						success: function(resp) {
							if ( resp.success ) {
								window.location.reload();
							}
						}
					})
				}
			})
		});
	</script>
	<?php
	$content = ob_get_clean();

	if ( $echo ) {
		echo $content;
	} else {
		return $content;
	}
}

add_action( 'wp_ajax_dwqa_assign_ticket', 'dwqa_assign_ticket_ajax', 10 );
function dwqa_assign_ticket_ajax() {
	if ( isset( $_POST['_wpnonce'] ) && wp_verify_nonce( esc_attr( $_POST['_wpnonce'] ), 'dwqa_assign_ticket_ajax' ) ) {
		$user_id = (int) isset( $_POST['user_id'] ) ? absint( $_POST['user_id'] ) : 0;
		$post_id = (int) isset( $_POST['post_id'] ) ? absint( $_POST['post_id'] ) : 0;

		// make sure post_id variable is ID of post type dwqa-question
		if ( 'dwqa-question' !== get_post_type( $post_id ) ) {
			// return false;
			wp_send_json_error();
			exit();
		}

		// make sure current user can assign ticket
		if ( !dwqa_current_user_can( 'manage_question' ) ) {
			// return false;
			wp_send_json_error();
			exit();
		}

		//check if user is none (user_id = 0)
		if($user_id == 0){
			update_post_meta( $post_id, '_dwqa_assign_to', 0 );
			wp_send_json_success();
			exit();
		}

		// make sure user was assign is can assign ticket
		if ( !dwqa_user_can( $user_id, 'manage_question' ) ) {
			// return false;
			wp_send_json_error();
			exit();
		}

		update_post_meta( $post_id, '_dwqa_assign_to', $user_id );

		do_action( 'dwqa_assign_ticket', $post_id, $user_id, get_current_user_id() );

		wp_send_json_success();
		exit();
	}
}

add_action( 'dwqa_assign_ticket', 'dwqa_assign_ticket_notification', 10, 3 );
function dwqa_assign_ticket_notification( $question_id, $assign_to, $user_assign ) {
	$to = sanitize_email( get_the_author_meta( 'user_email', $assign_to ) );
	$subject = sprintf( __( '%s assigned a question to you', 'dwqa-at' ), get_the_author_meta( 'display_name', $user_assign ) );
	$subject = apply_filters( 'dwqa_assign_ticket_notification_message', $subject, $question_id, $assign_to, $user_assign );
	$message = sprintf( __( '
		Hi %1$s,<br><br>
		%2$s assigned a question to you.<br>
		<a href="%3$s">View Question.</a>
	', 'dwqa-at' ),
		get_the_author_meta( 'display_name', $assign_to ),
		get_the_author_meta( 'display_name', $user_assign ),
		esc_html( get_permalink( $question_id ) ) 
	);
	$message = apply_filters( 'dwqa_assign_ticket_notification_message', $message, $question_id, $assign_to, $user_assign );

	do_action( 'dwqa_assign_ticket_notification', $question_id, $assign_to );

	dwqa()->notifications->send( $to, $subject, $message );
}