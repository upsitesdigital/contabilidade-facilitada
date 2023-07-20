<?php

class DWQA_Admin_Upgrade {
	private $start_version = '1.0.6';
	public function __construct() {
		add_action( 'init', array( $this, 'add_notice' ) );
		add_action( 'admin_menu', array( $this, 'add_admin_page' ) );
		add_action( 'wp_ajax_dwqa-upgrades', array( $this, 'ajax_upgrades' ) );
	}

	public function add_admin_page() {
		add_submenu_page( null, __( 'DWQA Upgrades', 'dwqa' ), __( 'DWQA Upgrades', 'dwqa' ), 'manage_options', 'dwqa-upgrades', array( $this, 'screen' ) );
	}

	public function screen() {
	?>

		<div class="wrap">
			<h2><?php echo get_admin_page_title(); ?></h2>
			<p><?php _e('The upgrade process has started, please be patient. This could take several minutes. You will be automatically redirected when the upgrade is finished...','dw-question-answer') ?></p>
			<span class="spinner" style="visibility: visible; float: none;"></span>
			<script type="text/javascript">
			jQuery(document).ready(function($) {
				function dwqaUpgradeSendRequest( restart ) {

					$.ajax({
						url: '<?php echo admin_url( 'admin-ajax.php' ); ?>',
						type: 'POST',
						dataType: 'json',
						data: {
							action: 'dwqa-upgrades',
							restart: restart,
						},
					})
					.done(function( resp ) {
						if ( resp.success ) {
							if ( resp.data.finish ) {
								document.location.href = '<?php echo admin_url('edit.php?post_type=dwqa-question'); ?>';
							} else {
								dwqaUpgradeSendRequest( 0 );
							}
						} else {
							console.log( resp.message );
						}
					});
				}

				dwqaUpgradeSendRequest( 1 );

			});
			</script>
		</div>
		<?php
	}

	public function add_notice() {
		//only admin
		if(!current_user_can('manage_options')){
			return false;
		}

		$current_db_version = get_option( 'dwqa-db-version', $this->start_version );
		$db_version = dwqa()->db_version;
		if ( version_compare( $current_db_version, $db_version, '<' ) ) {
			dwqa()->admin_notices->add_notice( 'update', __CLASS__ . '::print_notice' );
		}
	}

	public static function print_notice() {
		?>
		<div id="message" class="updated dwqa-message dwqa-connect">
			<p><?php _e( '<strong>DW Question & Answer Data Update Required</strong> &#8211; We just need to update your install to the latest version', 'dwqa' ); ?></p>
			<p class="submit"><a href="<?php echo esc_url( add_query_arg( 'dwqa-upgrade', 'true', admin_url( 'admin.php?page=dwqa-upgrades' ) ) ); ?>" class="dwqa-update-now button-primary"><?php _e( 'Run the updater', 'dwqa' ); ?></a></p>
		</div>
		<script type="text/javascript">
			jQuery( '.dwqa-update-now' ).click( 'click', function() {
				return window.confirm( '<?php echo esc_js( __( 'It is strongly recommended that you backup your database before proceeding. Are you sure you wish to run the updater now?', 'dwqa' ) ); ?>' );
			});
		</script>
		<?php
	}


	public static function upgrade_question_answer_relationship() {
		global $wpdb;
		$cursor = get_option( 'dwqa_upgrades_step', 0 );
		$step = 100;
		$length = $wpdb->get_var( "SELECT count(*) FROM $wpdb->posts p JOIN $wpdb->postmeta pm ON p.ID = pm.post_id WHERE 1=1 AND post_type = 'dwqa-answer' AND pm.meta_key = '_question'" );
		if( $cursor <= $length ) {
			$answers = $wpdb->get_results( $wpdb->prepare( "SELECT ID, meta_value as parent FROM $wpdb->posts p JOIN $wpdb->postmeta pm ON p.ID = pm.post_id WHERE 1=1 AND post_type = 'dwqa-answer' AND pm.meta_key = '_question' LIMIT %d, %d ", $cursor, $step ) );

			if ( ! empty( $answers ) ) {
				foreach ( $answers as $answer ) {
					$update = wp_update_post( array( 'ID' => $answer->ID, 'post_parent' => $answer->parent ), true );
				}
				$cursor += $step;
				update_option( 'dwqa_upgrades_step', $cursor );
				return $cursor;
			} else {
				delete_option( 'dwqa_upgrades_step' );
				return 0;
			}
		} else {
			delete_option( 'dwqa_upgrades_step' );
			return 0;
		}
	}

	public static function ajax_upgrades() {
		if ( ! current_user_can( 'manage_options' ) ) {
			wp_send_json_error( array( 'message' => __( 'You do not have permission to do this task', 'dw-question-answer' ) ) );
		}

		if ( isset( $_POST['restart'] ) && intval( $_POST['restart'] ) ) {
			delete_option( 'dwqa_upgrades_start' );
			$start = 0;
		} else {
			$start = get_option( 'dwqa_upgrades_start', 0 );
		}

		switch ( $start ) {
			case 0:
				$start += 1;
				update_option( 'dwqa_upgrades_start', $start );
				wp_send_json_success( array(
					'start' => $start,
					'finish' => 0,
					'message' => __( 'Just do it..', 'dw-question-answer' )
				) );
				break;
			case 1:
				$do_next = self::upgrade_question_answer_relationship();
				if ( ! $do_next ) {
					$start += 1;
					update_option( 'dwqa_upgrades_start', $start );
					$message = sprintf( __( 'Move to next step %d', 'dw-question-answer' ), $start );
				} else {
					$message = $do_next;
				}
				wp_send_json_success( array(
					'start' => $start,
					'finish' => 0,
					'message' => $message
				) );
				break;

			default:
				delete_option( 'dwqa_upgrades_start' );
				update_option( 'dwqa-db-version', dwqa()->db_version );
				dwqa()->admin_notices->remove_notice( 'update' );
				wp_send_json_success( array(
					'start' => $start,
					'finish' => 1,
					'message' => __('Upgrade process is done','dw-question-answer')
				) );
				break;
		}
	}

}