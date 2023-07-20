<?php  

function dwqa_get_following_user( $question_id = false ) {
	if ( ! $question_id ) {
		$question_id = get_the_ID();
	}
	$followers = get_post_meta( $question_id, '_dwqa_followers' );
	
	if ( empty( $followers ) ) {
		return false;
	}
	
	return $followers;
}
/** 
 * Did user flag this post ?
 */
function dwqa_is_user_flag( $post_id, $user_id = null ) {
	if ( ! $user_id ) {
		global $current_user;
		if ( $current_user->ID > 0 ) {
			$user_id = $current_user->ID;
		} else {
			return false;
		}
	}
	$flag = get_post_meta( $post_id, '_flag', true );
	if ( ! $flag ) {
		return false;
	}
	$flag = unserialize( $flag );
	if ( ! is_array( $flag ) ) {
		return false;
	}
	if ( ! array_key_exists( $user_id, $flag ) ) {
		return false;
	}
	if ( $flag[$user_id] == 1 ) {
		return true;
	}
	return false;
}


function dwqa_user_post_count( $user_id, $post_type = 'post' ) {
	$posts = new WP_Query( array(
		'author' => $user_id,
		'post_status'		=> array( 'publish', 'private' ),
		'post_type'			=> $post_type,
		'fields' => 'ids',
	) );
	return $posts->found_posts;
}

function dwqa_user_question_count( $user_id ) {
	return dwqa_user_post_count( $user_id, 'dwqa-question' );
}

function dwqa_user_answer_count( $user_id ) {
	return dwqa_user_post_count( $user_id, 'dwqa-answer' );
}

function dwqa_user_comment_count( $user_id ) {
	global $wpdb;

	$query = "SELECT `{$wpdb->prefix}comments`.user_id, count(*) as number_comment FROM `{$wpdb->prefix}comments` JOIN `{$wpdb->prefix}posts` ON `{$wpdb->prefix}comments`.comment_post_ID = `{$wpdb->prefix}posts`.ID WHERE  1 = 1 AND  ( `{$wpdb->prefix}posts`.post_type = 'dwqa-question' OR `{$wpdb->prefix}posts`.post_type = 'dwqa-answer' ) AND  `{$wpdb->prefix}comments`.comment_approved = 1 GROUP BY `{$wpdb->prefix}comments`.user_id";

	$results = wp_cache_get( 'dwqa-user-comment-count' );
	if ( false == $results ) {
		$results = $wpdb->get_results( $query, ARRAY_A );
		wp_cache_set( 'dwqa-user-comment-count', $results );
	}

	$users_comment_count = array_filter( $results, create_function( '$a', 'return $a["user_id"] == '.$user_id.';' ) ); 
	if ( ! empty( $users_comment_count ) ) {
		$user_comment_count = array_shift( $users_comment_count );
		return $user_comment_count['number_comment'];
	}
	return false;
}

function dwqa_user_most_answer( $number = 10, $from = false, $to = false ) {
	global $wpdb;
	
	$query = "SELECT post_author, count( * ) as `answer_count` 
				FROM `{$wpdb->prefix}posts` 
				WHERE post_type = 'dwqa-answer' 
					AND post_status = 'publish'
					AND post_author <> 0";
	if ( $from ) {
		$from = date( 'Y-m-d h:i:s', $from );
		$query .= " AND `{$wpdb->prefix}posts`.post_date > '{$from}'";
	}
	if ( $to ) {
		$to = date( 'Y-m-d h:i:s', $to );
		$query .= " AND `{$wpdb->prefix}posts`.post_date < '{$to}'";
	}

	$prefix = '-all';
	if ( $from && $to ) {
		$prefix = '-' . ( $form - $to );
	}

	$query .= " GROUP BY post_author 
				ORDER BY `answer_count` DESC LIMIT 0,{$number}";
	$users = wp_cache_get( 'dwqa-most-answered' . $prefix );
	if ( false == $users ) {
		$users = $wpdb->get_results( $query, ARRAY_A  );
		wp_cache_set( 'dwqa-most-answered', $users );
	}
	return $users;            
}

function dwqa_user_most_answer_this_month( $number = 10 ) {
	$from = strtotime( 'first day of this month' );
	$to = strtotime( 'last day of this month' );
	return dwqa_user_most_answer( $number, $from, $to );
}

function dwqa_user_most_answer_last_month( $number = 10 ) {
	$from = strtotime( 'first day of last month' );
	$to = strtotime( 'last day of last month' );
	return dwqa_user_most_answer( $number, $from, $to );
}

function dwqa_is_followed( $post_id = false, $user_id = false ) {
	if ( ! $post_id ) {
		$post_id = get_the_ID();
	}

	if ( ! $user_id ) {
		$user = wp_get_current_user();
		$user_id = $user->ID;
	}

	if ( in_array( $user_id, get_post_meta( $post_id, '_dwqa_followers', false ) ) ) {
		return true;
	}
	return false;
}

/**
* Get username
*
* @param int $post_id
* @return string
* @since 1.4.0
*/
function dwqa_the_author( $display_name ) {
	global $post;

	if ( isset( $post->ID ) && ( 'dwqa-answer' == $post->post_type || 'dwqa-question' == $post->post_type ) ) {
		if ( dwqa_is_anonymous( $post->ID ) ) {
			$anonymous_name = get_post_meta( $post->ID, '_dwqa_anonymous_name', true );
			$display_name = $anonymous_name ? $anonymous_name : __( 'Anonymous', 'dwqa' );
		}
	}
	$display_name = sanitize_text_field( $display_name );
	$display_name = wp_filter_kses( $display_name );
	$display_name = _wp_specialchars( $display_name );
	return $display_name;
}
add_filter( 'the_author', 'dwqa_the_author', 99 );

/**
* Get user's profile link
*
* @param int $user_id
* @return string
* @since 1.4.0
*/
function dwqa_get_author_link( $user_id = false ) {
	if ( ! $user_id ) {
		return false;
	}
	$user = get_user_by( 'id', $user_id );
	if(!$user){
		return false;
	}

	global $dwqa_general_settings;
	
	$question_link = isset( $dwqa_general_settings['pages']['archive-question'] ) ? get_permalink( $dwqa_general_settings['pages']['archive-question'] ) : false;
	$url = get_the_author_link( $user_id );
	if ( $question_link ) {
		$url = add_query_arg( array( 'user' => urlencode( $user->user_login ) ), $question_link );
	}

	return apply_filters( 'dwqa_get_author_link', $url, $user_id, $user );
}


/**
* Get question ids user is subscribing
*
* @param int $user_id
* @return array
* @since 1.4.0
*/
function dwqa_get_user_question_subscribes( $user_id = false, $posts_per_page = 5, $page = 1 ) {
	if ( !$user_id ) {
		return array();
	}

	$args = array(
		'post_type' 				=> 'dwqa-question',
		'posts_per_page'			=> $posts_per_page,
		'paged'						=> $page,
		'fields' 					=> 'ids',
		'update_post_term_cache' 	=> false,
		'update_post_meta_cache' 	=> false,
		'no_found_rows' 			=> true,
		'meta_query'				=> array(
			'key'					=> '_dwqa_followers',
			'value'					=> $user_id,
			'compare'				=> '='
		)
	);

	$question_id = wp_cache_get( '_dwqa_user_'. $user_id .'_question_subscribes' );

	if ( ! $question_id ) {
		$question_id = get_posts( $args );
		wp_cache_set( '_dwqa_user_'. $user_id .'_question_subscribes', $question_id, false, 450 );
	}

	return $question_id;
}

function dwqa_get_user_badge( $user_id = false ) {
	if ( !$user_id ) {
		return;
	}

	$badges = array();
	if ( dwqa_user_can( $user_id, 'manage_question' ) && dwqa_user_can( $user_id, 'manage_answer' ) && dwqa_user_can( $user_id, 'manage_comment' ) ) {
		$badges['staff'] = __( 'Staff', 'dwqa' );
	}

	return apply_filters( 'dwqa_get_user_badge', $badges, $user_id );
}

function dwqa_print_user_badge( $user_id = false, $echo = false ) {
	if ( !$user_id ) {
		return;
	}

	$badges = dwqa_get_user_badge( $user_id );
	$result = '';
	if ( $badges && !empty( $badges ) ) {
		foreach( $badges as $k => $badge ) {
			$k = str_replace( ' ', '-', $k );
			$result .= '<span class="dwqa-label dwqa-'. strtolower( $k ) .'">'.$badge.'</span>';
		}
	}

	if ( $echo ) {
		echo $result;
	}

	return $result;
}

function dwqa_get_avatar_url($user_id = false){
	$dwqa_user = DWQA_User::getInstance();
	
	return $dwqa_user->getAvatarUrl($user_id);
}

function dwqa_get_cover_image_url($user_id = false){
	$dwqa_user = DWQA_User::getInstance();
	
	return $dwqa_user->getCoverImageUrl($user_id);
}
function dwqa_get_display_name($user_id = false){
	if(!$user_id){
		if(is_user_logged_in()){
			$user = wp_get_current_user();
			return $user->display_name;
		}
		return false;
	}
	$user = get_user_by('id', $user_id);
	return $user->display_name;
}
function dwqa_count_user_question($user_id = false){
	$dwqa_user = DWQA_User::getInstance();
	return $dwqa_user->countQuestion($user_id);
}
function dwqa_count_user_answer($user_id = false){
	$dwqa_user = DWQA_User::getInstance();
	return $dwqa_user->countAnswer($user_id);
}

function dwqa_profile_displayed_user_id(){
	$user_param = get_query_var( 'user' );
	if ( $user_param ) {
		$user = get_user_by( 'login', $user_param );
		if ( isset( $user->ID ) ) {
			
			$user_id = $user->ID;
		}else{
			$user_id = 0;
		}
	}else{
		$user_id = get_current_user_id();
	}
	return $user_id;
}


// Update avatar to backend
global $dwqa_general_settings;
if ( isset( $dwqa_general_settings['profileAvatar'] ) && $dwqa_general_settings['profileAvatar'] ) {
add_filter( 'get_avatar' , 'dwqa_profile_get_avatar' , 1 , 6 );
function dwqa_profile_get_avatar( $avatar, $id_or_email, $size, $default, $alt, $args ) {
		$user = false;

		if ( is_numeric( $id_or_email ) ) {
			$id = (int) $id_or_email;
			$user = get_user_by( 'id' , $id );
		} elseif ( is_object( $id_or_email ) ) {
			if ( ! empty( $id_or_email->user_id ) ) {
				$id = (int) $id_or_email->user_id;
				$user = get_user_by( 'id' , $id );
			}
		} else {
			$user = get_user_by( 'email', $id_or_email );   
		}

		if ( $user && is_object( $user ) ) {
			//get upload dir data
			$upload_dir = wp_upload_dir();
			//get user data
			$user_id = $user->ID;
			$user_info = get_userdata( $user_id );
			//using the username for this example
			$username = $user_info->user_login;
			//construct src
			$src = dwqa_get_avatar_url($user_id);
			$avatar = "<img alt='{$alt}' src='{$src}' class='avatar avatar-{$size} photo' height='{$size}' width='{$size}' />";
		}
		return $avatar;
	}
}


function dwqa_profile_tab(){
	$tab = get_query_var( 'tab' );
	return $tab?$tab:'questions';
}
function dwqa_profile_tab_url($user_id = false, $tab = 'questions'){
	return trailingslashit(dwqa_get_author_link($user_id)). $tab;
}


function dwqa_profile_tab_questions($user_id = false){
	add_filter('dwqa_prepare_archive_posts', 'dwqa_profile_question_filter_query',12);
	remove_action( 'dwqa_before_questions_archive', 'dwqa_archive_question_filter_layout', 12 );
	remove_action( 'dwqa_before_questions_archive', 'dwqa_search_form', 11 );

	global $dwqa;
	$dwqa->template->load_template('bp-archive', 'question');
}

function dwqa_profile_question_filter_query($query){
	$displayed_user_id = dwqa_profile_displayed_user_id();
	$query['author'] = $displayed_user_id;
	return $query;
}

function dwqa_profile_tab_answers($user_id = false){
	add_filter('dwqa_prepare_archive_posts', 'dwqa_profile_answer_filter_query',12);
	remove_action( 'dwqa_before_questions_archive', 'dwqa_archive_question_filter_layout', 12 );
	remove_action( 'dwqa_before_questions_archive', 'dwqa_search_form', 11 );

	global $dwqa;
	$dwqa->template->load_template('bp-archive', 'question');
}

function dwqa_profile_answer_filter_query($query){
	$displayed_user_id = dwqa_profile_displayed_user_id();
	$post__in = array();
	
	$array = $query;
	$array['post_type'] = 'dwqa-answer';
	$array['author'] = $displayed_user_id;
	$array['posts_per_page'] = -1;

	$results = new WP_Query( $array );
	if($results->post_count > 0){
		foreach($results->posts as $result){
			if(!in_array($result->post_parent, $post__in)){
				$post__in[] = $result->post_parent;
			}
			
		}
	}

	if(empty($post__in)){
		$post__in = array(0);
	}
	$query['post__in'] = $post__in;
	$query['orderby'] = 'post__in';
	return $query;
}

function dwqa_profile_blogposts() {
	global $post;
	global $dwqa_general_settings;
	$user_id = dwqa_profile_displayed_user_id();
	$author_name = dwqa_get_display_name($user_id);
	$args = array( 'posts_per_page' => 12, 'post_type'=> 'post', 'author_name' => $author_name );
	$myposts = get_posts( $args );
	$selectedCol = $dwqa_general_settings['choose-blog-col'];

	if ( $myposts ) {
		echo '<div class="profile-blog dwqa-'.$selectedCol.'">';
			foreach ( $myposts as $post ) :
				setup_postdata( $post ); ?>
				<div class="dwqa-post">
					<?php if ( has_post_thumbnail() ) : ?>
						<div class="dwqa-thumb"><?php the_post_thumbnail( array(230, 130) ); ?></div>
					<?php endif; ?>
					<div class="dwqa-inner">
						<h2 class="dwqa-post-title"><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h2>
						<div class="dwqa-post-meta">
							<span>
								<?php printf( __( '%s ago', 'dwqa' ), human_time_diff( get_the_time( 'U', $post->ID ), current_time( 'timestamp' ) ) ); ?>
							</span>

							<span>
								<?php _e( 'in', 'dwqa' ); ?>: <?php the_category( ', ', '', $post->ID ); ?>
							</span>
						</div> 
					</div>
				</div>
			<?php
			endforeach; 
			wp_reset_postdata();
			
			if( count_user_posts($user_id) > 12 ) {
				echo '<a class="btnshowall" href="'.home_url('/author/').''.$author_name.'">show all</a>';
			}
		echo '</div>';

	} else {
		// Display message if no post  are found.
		echo '<p class="dwqa-alert dwqa-alert-info">' . __( 'The author didn\'t add any post.', 'dwqa' ) . '</p>';
	}
}

function dwqa_profile_comments_post() {
	// Get the global `$wp_query` object...
	global $wp_query;
	
	// ...and use it to get post author's id.
	$post_author_id = dwqa_profile_displayed_user_id();

	// Setup arguments.
	$args = array (
		'user_id' => $post_author_id,
		'orderby' => 'comment_ID'
	);
	
	// Custom comment query.
	$my_comment_query = new WP_Comment_Query;
	$comments = $my_comment_query->query( $args );
	
	// Check for comments.
	if ( $comments ) {
	
		// Start listing comments.
		echo '<ul class="author-comments">';
	
			// Loop over comments.
			foreach( $comments as $comment ) {
				$comment_title = get_the_title( $comment->comment_post_ID );
				$link = get_permalink( $comment->comment_post_ID );

				echo '<div class="dwqa-cmt-item">';
					echo '<div class="dwqa-cmt-link">';
						echo '<i class="fa fa-comments"></i>';
						echo '<a href="'.get_comment_link($comment->comment_ID).'">'.$comment->comment_content.'</a>';
					echo '</div>';
					echo '<div class="dwqa-cmt-meta">';
						echo '<span>';
							echo 'On <a href="'.$link.'">'.$comment_title.'</a>';
						echo '</span>';
					echo '</div>';
				echo '</div>';
			}
	
		// Stop listing comments.
		echo '</ul>';
	
	} else {
		// Display message if no comments are found.
		echo '<p class="dwqa-alert dwqa-alert-info">' . __( 'The author didn\'t post any comments.', 'dwqa' ) . '</p>';
	}
}

class DWQA_User { 
	private static $instance = null;
	private $avatar_dir = 'avatar';
    private $avatar_meta_name = 'dwqa_avatar_name';
    private $cover_dir = 'cover';
	private $cover_meta_name = 'dwqa_cover_name';

	public function __construct() {
		// Do something about user roles, permission login, profile setting
		add_action( 'wp_ajax_dwqa-follow-question', array( $this, 'follow_question' ) );

		add_action('wp_ajax_dwqa_upload_avatar', array($this, 'uploadAvatar'));
		add_action('wp_ajax_dwqa_crop_avatar', array($this, 'cropAvatar'));

		add_action('wp_ajax_dwqa_upload_cover_image', array($this, 'uploadCoverImage'));

		add_action('init', array($this, 'addRewriteRule'), 10);

		add_filter('dwqa_get_author_link', array($this, 'changeAuthorLink'), 10, 3 );


		global $dwqa_general_settings;
		if(isset($dwqa_general_settings['use-user-expiration']) && $dwqa_general_settings['use-user-expiration']){
			//admin setting expiration
			add_action( 'show_user_profile', array($this, 'extraUserProfileInfo'), 10, 1 );
			add_action( 'edit_user_profile', array($this, 'extraUserProfileInfo'), 10, 1 );

			//save
			add_action( 'personal_options_update', array($this, 'saveExtraUserProfileInfo' ), 10, 1);
			add_action( 'edit_user_profile_update', array($this, 'saveExtraUserProfileInfo' ), 10, 1);

			//add permission
			add_filter( 'dwqa_user_can', array($this, 'filterPermission'), 10 , 4);

			//addscript
			add_action( 'admin_enqueue_scripts', array($this, 'addAdminScripts' ) );
		}
	}

	public function addAdminScripts() {
		wp_enqueue_script( 'jquery-datetime-picker-scripts', DWQA_URI . 'assets/js/jquery.datetimepicker.full.min.js', false, '1.0.0' );
    	wp_enqueue_style( 'jquery-datetime-picker-style', DWQA_URI . 'assets/css/jquery.datetimepicker.min.css', array(), '1.0.0' );

	}


	public function filterPermission($can, $perm, $user_id, $post_id){
		if(!$can){
			//only check if not permission before

			//check $perm
			$perm_array = array(
				'read_question', 'post_question', 'read_answer', 'post_answer', 'read_comment', 'post_comment'
			);
			$perm_array = apply_filters('dwqa_expiration_perm_filter', $perm_array);
			$dwqa_expiration = get_user_meta($user_id, 'dwqa_expiration', true);

			if(in_array($perm, $perm_array) && $dwqa_expiration && $dwqa_expiration >= current_time('timestamp')){
				$can = true;
			}
		}
		
		return $can;
	}

	

	public function saveExtraUserProfileInfo( $user_id ) {
	    if ( !current_user_can('administrator') ) { 
	        return false; 
	    }

	    if(isset($_POST['dwqa-expiration'])){
	    	$expiration_time = strtotime($_POST['dwqa-expiration']);
	    	update_user_meta( $user_id, 'dwqa_expiration', $expiration_time );
	    }else{
	    	update_user_meta( $user_id, 'dwqa_expiration', 0 );
	    }
	}

	public function extraUserProfileInfo($user){
	    $user_id = 0;
	    $user_email = '';
	    $user_meta = array();
	    if(is_object($user)){
	        $user_id = $user->ID;
	        $user_email = $user->user_email;
	    }

	    $expiration_time = get_user_meta($user_id, 'dwqa_expiration', true);
	    ?>
	    <h2>DWQA Information</h2>
	    <table class="form-table">
	        <tr>
	            <th>
	                <label>DWQA Expiration</label>
	            </th>
	            <td>
	            	<input type="text" id="dwqa-expiration" name="dwqa-expiration" value="<?php echo $expiration_time?date("Y-m-d H:i:s", $expiration_time):'';?>">
	            </td>
	        </tr>
	    </table>
	    <script>
	    	jQuery('document').ready(function($){
	    		if($('#dwqa-expiration').length){
					$('#dwqa-expiration').datetimepicker({
						format:'Y-m-d H:i:00'
					});
				}
	    		
	    	});
	    </script>
	    <?php
	}

	function addRewriteRule(){
		global $dwqa_general_settings;
		
		if(isset( $dwqa_general_settings['pages']['user-profile'] )){
			$page_user_profile = get_post($dwqa_general_settings['pages']['user-profile']);

			if($page_user_profile){
				add_rewrite_tag('%user%', '([^&]+)');
				add_rewrite_tag('%tab%', '([^&]+)');
				
				add_rewrite_rule('^'.$page_user_profile->post_name.'/([^/]*)/([^/]*)/page/([^/]*)/?','index.php?page_id='.$dwqa_general_settings['pages']['user-profile'].'&user=$matches[1]&tab=$matches[2]&paged=$matches[3]','top');
				add_rewrite_rule('^'.$page_user_profile->post_name.'/([^/]*)/([^/]*)/?','index.php?page_id='.$dwqa_general_settings['pages']['user-profile'].'&user=$matches[1]&tab=$matches[2]','top');
				add_rewrite_rule('^'.$page_user_profile->post_name.'/([^/]*)/?','index.php?page_id='.$dwqa_general_settings['pages']['user-profile'].'&user=$matches[1]','top');
			}
			
		}
		
	}

	function changeAuthorLink($url, $user_id, $user){
		global $dwqa_general_settings;
		
		if(isset( $dwqa_general_settings['pages']['user-profile'] ) && $dwqa_general_settings['pages']['user-profile'] && isset($user->user_login) && $user->user_login){
			$page_user_profile_url = get_permalink($dwqa_general_settings['pages']['user-profile']);
			if ( $page_user_profile_url ) {
				return trailingslashit($page_user_profile_url). $user->user_login;
			}
		}
		return $url;
	}

	function follow_question() {
		check_ajax_referer( '_dwqa_follow_question', 'nonce' );
		if ( ! isset( $_POST['post'] ) ) {
			wp_send_json_error( array( 'message' => __( 'Invalid Post', 'dwqa' ) ) );
		}
		$question = get_post( intval( $_POST['post'] ) );
		if ( is_user_logged_in() ) {
			global $current_user;
			if(!$current_user){
				$current_user = get_current_user();
			}
			if ( ! dwqa_is_followed( $question->ID, $current_user->ID )  ) {
				do_action( 'dwqa_follow_question', $question->ID, $current_user->ID );
				add_post_meta( $question->ID, '_dwqa_followers', $current_user->ID );
				wp_send_json_success( array( 'code' => 'followed', 'text' => 'Unsubscribe' ) );
			} else {
				do_action( 'dwqa_unfollow_question', $question->ID, $current_user->ID );
				delete_post_meta( $question->ID, '_dwqa_followers', $current_user->ID );
				wp_send_json_success( array( 'code' => 'unfollowed', 'text' => 'Subscribe' ) );
			}
		} else {
			wp_send_json_error( array( 'code' => 'not-logged-in' ) );
		}

	}

	public function cropAvatar(){
		check_ajax_referer( 'dwqa-user-profile', 'nonce' );

		$src = $_POST['attachment_file'];
		$src_x = $_POST['x'];
		$src_y = $_POST['y'];
		$src_w = $_POST['w'];
		$src_h = $_POST['h'];
		$ui_w = $_POST['ui_w'];
		$ui_h = $_POST['ui_h'];
		$dst_w = 150;
		$dst_h = 150;
		$dst_file = '';

		if(!$user_id = $this->checkUserID($user_id)){
 			return false;
 		}
		$avatar_dir = $this->uploadAvatarFilter(wp_upload_dir());

		$dst_file = $avatar_dir['path'] . '/avatar-' . basename( $src );
		$dst_url = $avatar_dir['url'] . '/avatar-' . basename( $src );

		wp_mkdir_p( dirname( $dst_file ) );

		$editor = wp_get_image_editor( $src );
		if ( !is_wp_error( $editor ) ) {
			$resized = $editor->resize( $_POST['dw'], $_POST['dh'], false );
			$editor->crop( $src_x, $src_y, $src_w, $src_h, $dst_w, $dst_h );
			$editor->save( $dst_file );

			$avatar_cropped = get_user_meta( $user_id, 'dwqa_user_avatar_cropped', true);
			if($avatar_cropped){
				//delete old
				if(file_exists($avatar_cropped['file'])){
					unlink($avatar_cropped['file']);
				}
			}

			$avatar_cropped = array(
				'file' => $dst_file,
				'url' => $dst_url,
				'type' => wp_check_filetype($dst_file)['type'],
			);
			update_user_meta( $user_id, 'dwqa_user_avatar_cropped', $avatar_cropped);

			wp_send_json_success( array('cropped' => $avatar_cropped) );
		}
		wp_send_json_error( __( 'Something went wrong', 'dwqa' ) );
		die;
	}

	public function uploadAvatarFilter($upload_dir = array()) {
		if(!$user_id = $this->checkUserID($user_id)){
 			return false;
 		}
		$upload_dir['subdir'] = '/dwqa/'.$user_id . '/avatar';

		//fix for window
		$upload_dir['basedir'] = str_replace('\\', '/', $upload_dir['basedir']);

		$upload_dir['path'] = $upload_dir['basedir'] . $upload_dir['subdir'];
		$upload_dir['url'] = $upload_dir['baseurl'] . $upload_dir['subdir'];
		return apply_filters( 'dwqa_avatar_upload_dir', $upload_dir );
	}

	public function uploadCoverImageFilter($upload_dir = array()) {
		if(!$user_id = $this->checkUserID($user_id)){
 			return false;
 		}
		$upload_dir['subdir'] = '/dwqa/'.$user_id . '/cover';

		//fix for window
		$upload_dir['basedir'] = str_replace('\\', '/', $upload_dir['basedir']);

		$upload_dir['path'] = $upload_dir['basedir'] . $upload_dir['subdir'];
		$upload_dir['url'] = $upload_dir['baseurl'] . $upload_dir['subdir'];
		return apply_filters( 'dwqa_avatar_upload_dir', $upload_dir );
	}

	public function uploadAvatar(){
 		check_ajax_referer( 'dwqa-user-profile', 'nonce' );

 		if(!$user_id = $this->checkUserID($user_id)){
 			return false;
 		}

 		if (!empty($_FILES) && !empty($_FILES['file'])) {
			require_once(ABSPATH.'wp-admin/includes/file.php');
			$args_accepted = $this->avatarAllowedTypes();
			$max_size = $this->avatarMaxsize();
			
			$overrides = array('test_form' => false, 'upload_error_handler' => 'dwqa_attachment_handle_upload_error');


			$upload_dir_filter = array($this, 'uploadAvatarFilter');
			// Make sure the file will be uploaded in the attachment directory.
			if ( ! empty( $upload_dir_filter ) ) {
				add_filter( 'upload_dir', $upload_dir_filter, 10);
			}

			$error = $_FILES['file']['error'];
			$file_name = $_FILES['file']['name'];
			$file_size = $_FILES['file']['size'];
			$ext = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));
			$uploads = array();
			if ($error == UPLOAD_ERR_OK) {

				if(in_array($ext, $args_accepted)){
					if($file_size > 0){
						if($file_size <= $max_size){
							$file = array('name' => $file_name,
								'type' => $_FILES['file']['type'],
								'size' => $_FILES['file']['size'],
								'tmp_name' => $_FILES['file']['tmp_name'],
								'error' => $_FILES['file']['error']
							);

							$upload = wp_handle_upload($file, $overrides);

							if (!is_wp_error($upload)) {
								$uploads[] = $upload;
							} else {

								wp_send_json_error( __( 'FileUpload: '.$upload->errors['wp_upload_error'][0], 'dwqa' ) );
							}
						}else{
							wp_send_json_error( __( 'FileUpload: Upload file size exceeds maximum file size allowed', 'dwqa' ) );
						}
					}else{
						wp_send_json_error( __( 'FileUpload('.$file_name.'): Upload file is empty.', 'dwqa' ) );
					}
					
				}else{
					wp_send_json_error( __( 'FileUpload: Upload file type not allowed', 'dwqa' ) );
				}
			}
			// Restore WordPress Uploads data.
			if ( ! empty( $upload_dir_filter ) ) {
				remove_filter( 'upload_dir', $upload_dir_filter, 10 );
			}

			if (!empty($uploads)) {
				foreach ($uploads as $upload) {
					$upload['file'] = str_replace('\\', '/', $upload['file']);

					//resize image
					$editor = wp_get_image_editor( $upload['file'] );
					if ( !is_wp_error( $editor ) ) {
						$resized = $editor->resize( 600, 600, false );
						$editor->save( $upload['file'] );
					}

					$avatar_temp = get_user_meta( $user_id, 'dwqa_user_avatar_temp', true);
					if($avatar_temp){
						//delete dwqa_user_avatar_temp
						if(file_exists($avatar_temp['file'])){
							unlink($avatar_temp['file']);
						}
						
					}
					update_user_meta( $user_id, 'dwqa_user_avatar_temp', $upload);
					wp_send_json_success( array('upload' => $upload) );
				}
			}
			
		}
		wp_send_json_error( __( 'Something went wrong', 'dwqa' ) );
 		die;
 	}

 	public function getDefaultAvatarUrl(){
 		$avatar_url = 'http://1.gravatar.com/avatar/d66c53c349efdf280357eece851dde8c?s=150&d=mm&r=g';
 		return apply_filters('dwqa_user_get_default_avatar_url',  $avatar_url);
 	}

 	public function getAvatarUrl($user_id = false){
 		if(!$user_id = $this->checkUserID($user_id)){
 			return false;
 		}
 		$avatar_cropped = get_user_meta( $user_id, 'dwqa_user_avatar_cropped', true);
 		if(!$avatar_cropped){
 			return $this->getDefaultAvatarUrl();
 		}
 		return $avatar_cropped['url'];
 		
 	}

 	//cover image

 	public function getDefaultCoverImageUrl(){
		$cover_url = 'http://placehold.jp/2560x678.png';
 		return apply_filters('dwqa_user_get_default_cover_image_url',  $cover_url);
 	}
 	public function getCoverImageUrl($user_id = false){
 		if(!$user_id = $this->checkUserID($user_id)){
 			return false;
 		}
 		$cover_image = get_user_meta( $user_id, 'dwqa_user_cover_image_temp', true);
 		if(!$cover_image){
 			return $this->getDefaultCoverImageUrl();
 		}
 		return $cover_image['url'];
 		
 	}

 	public function uploadCoverImage(){
 		check_ajax_referer( 'dwqa-user-profile', 'nonce' );

 		if(!$user_id = $this->checkUserID($user_id)){
 			return false;
 		}

 		if (!empty($_FILES) && !empty($_FILES['file'])) {
			require_once(ABSPATH.'wp-admin/includes/file.php');
			$args_accepted = $this->coverImageAllowedTypes();
			$max_size = $this->coverImageMaxsize();
			
			$overrides = array('test_form' => false, 'upload_error_handler' => 'dwqa_attachment_handle_upload_error');


			$upload_dir_filter = array($this, 'uploadCoverImageFilter');
			// Make sure the file will be uploaded in the attachment directory.
			if ( ! empty( $upload_dir_filter ) ) {
				add_filter( 'upload_dir', $upload_dir_filter, 10);
			}

			$error = $_FILES['file']['error'];
			$file_name = $_FILES['file']['name'];
			$file_size = $_FILES['file']['size'];
			$ext = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));
			$uploads = array();
			if ($error == UPLOAD_ERR_OK) {

				if(in_array($ext, $args_accepted)){
					if($file_size > 0){
						if($file_size <= $max_size){
							$file = array('name' => $file_name,
								'type' => $_FILES['file']['type'],
								'size' => $_FILES['file']['size'],
								'tmp_name' => $_FILES['file']['tmp_name'],
								'error' => $_FILES['file']['error']
							);

							$upload = wp_handle_upload($file, $overrides);

							if (!is_wp_error($upload)) {
								$uploads[] = $upload;
							} else {

								wp_send_json_error( __( 'FileUpload: '.$upload->errors['wp_upload_error'][0], 'dwqa' ) );
							}
						}else{
							wp_send_json_error( __( 'FileUpload: Upload file size exceeds maximum file size allowed', 'dwqa' ) );
						}
					}else{
						wp_send_json_error( __( 'FileUpload('.$file_name.'): Upload file is empty.', 'dwqa' ) );
					}
					
				}else{
					wp_send_json_error( __( 'FileUpload: Upload file type not allowed', 'dwqa' ) );
				}
			}
			// Restore WordPress Uploads data.
			if ( ! empty( $upload_dir_filter ) ) {
				remove_filter( 'upload_dir', $upload_dir_filter, 10 );
			}

			if (!empty($uploads)) {
				foreach ($uploads as $upload) {
					$upload['file'] = str_replace('\\', '/', $upload['file']);

					//resize image
					$editor = wp_get_image_editor( $upload['file'] );
					if ( !is_wp_error( $editor ) ) {
						$resized = $editor->resize( 900, 900, false );
						$editor->save( $upload['file'] );
					}

					$cover_temp = get_user_meta( $user_id, 'dwqa_user_cover_image_temp', true);
					if($cover_temp){
						//delete dwqa_user_cover_temp
						if(file_exists($cover_temp['file'])){
							unlink($cover_temp['file']);
						}
						
					}
					update_user_meta( $user_id, 'dwqa_user_cover_image_temp', $upload);
					wp_send_json_success( array('upload' => $upload) );
				}
			}
			
		}
		wp_send_json_error( __( 'Something went wrong', 'dwqa' ) );
 		die;
 	}

 	public function avatarMaxsize(){
 		$size = 512*1024;
 		return apply_filters('dwqa_avatar_max_size',  $size);
 	}

	public function coverImageMaxsize(){
 		$size = 2*1024*1024;
 		return apply_filters('dwqa_cover_image_max_size',  $size);
 	}

 	public function coverImageAllowedTypes(){
 		$exts = array( 'jpg', 'jpeg', 'gif', 'png' );
 		return apply_filters('dwqa_cover_image_allowed_types',  $exts);
 	}


 	public function avatarAllowedTypes(){
 		$exts = array( 'jpg', 'jpeg', 'gif', 'png' );
 		return apply_filters('dwqa_avatar_allowed_types',  $exts);
 	}

 	public function countQuestion($user_id = false){
 		if(!$user_id = $this->checkUserID($user_id)){
 			return false;
 		}
 		global $wpdb;
 		$user_count = $wpdb->get_var( "SELECT COUNT(1) AS count FROM $wpdb->posts WHERE post_author = {$user_id} AND post_type = 'dwqa-question' AND post_status = 'publish'" );
 		return $user_count?$user_count:0;
 	}

 	public function countAnswer($user_id = false){
 		if(!$user_id = $this->checkUserID($user_id)){
 			return false;
 		}
 		global $wpdb;
 		$user_count = $wpdb->get_var( "SELECT COUNT(1) AS count FROM $wpdb->posts WHERE post_author = {$user_id} AND post_type = 'dwqa-answer' AND post_status = 'publish'" );
 		return $user_count?$user_count:0;
 	}


 	public function checkUserID($user_id = false){
 		if(!$user_id){
 			$user_id = get_current_user_id();
 		}
 		if(!$user_id){
 			return false;
 		}
	 	return $user_id;
 	}

 	public static function getInstance() {
        if (!isset(self::$instance)) {
            self::$instance = new self();
        }
        return self::$instance;
    }
}
?>