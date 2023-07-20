<?php

if( !class_exists( 'TGM_Plugin_Activation' ) ) {
	// load our custom updater
	include( DWQA_DIR . '/lib/class-tgm-plugin-activation.php' );
}

class DWQA_Updater {
	public function __construct() {
		add_action('dwqa_after_other_settings', array($this,'dwqa_updater_settings'));

		add_action('init', array($this, 'init_update'));
	}

	public function init_update(){
		global $dwqa_general_settings, $dwqa;

		if( isset($dwqa_general_settings['use-auto-update-from-evanto']) && $dwqa_general_settings['use-auto-update-from-evanto']){

			if (version_compare(DWQA_Updater::get_last_version(), $dwqa->version, ">")) {
				add_action( 'tgmpa_register', array($this, 'dwqa_update_required_plugins' ));
			}
		}
	}

	public function dwqa_updater_settings(){
		// Auto update from Evanto Settings
		add_settings_section(
			'dwqa-auto-update-settings',
			__( 'Update Settings', 'dwqa' ),
			false,
			'dwqa-settings'
		);

		add_settings_field(
			'dwqa_options[use-auto-update-from-evanto]',
			__( 'Use Update', 'dwqa' ),
			array($this, 'dwqa_use_auto_update_from_evanto'),
			'dwqa-settings',
			'dwqa-auto-update-settings'
		);

		add_settings_field(
			'dwqa_options[evanto-token]',
			__( 'Evanto Token', 'dwqa' ),
			array($this, 'dwqa_evanto_token'),
			'dwqa-settings',
			'dwqa-auto-update-settings'
		);

		add_settings_field(
			'dwqa_options[evanto-connection-status]',
			__( 'Evanto connection status', 'dwqa' ),
			array($this, 'dwqa_evanto_connection_status'),
			'dwqa-settings',
			'dwqa-auto-update-settings'
		);
	}
	public function dwqa_use_auto_update_from_evanto() {
		global $dwqa_general_settings;

		echo '<p><label for="dwqa_options_use_auto_update_from_evanto"><input type="checkbox" name="dwqa_options[use-auto-update-from-evanto]"  id="dwqa_options_use_auto_update_from_evanto" value="1" '.checked( 1, (isset($dwqa_general_settings['use-auto-update-from-evanto'] ) ? $dwqa_general_settings['use-auto-update-from-evanto'] : false ) , false ) .'><span class="description">'.__( 'Enable Auto Update', 'dwqa' ).'</span></label></p>';
	}
	public function dwqa_evanto_token() {
		global $dwqa_general_settings;

		$dwqa_evanto_token = isset( $dwqa_general_settings['evanto-token'] ) ?  $dwqa_general_settings['evanto-token'] : '';
		echo '<p><input id="dwqa_setting_evanto_token" type="text" name="dwqa_options[evanto-token]" class="medium-text" value="'.$dwqa_evanto_token.'" ><br><span class="description">'.__( 'Create your token', 'dwqa' ).' <a href="https://build.envato.com/create-token/">'.__( 'here', 'dwqa' ).'</a>'.'</span></p>';
	}
	public function dwqa_evanto_connection_status() {
		global $dwqa_general_settings;

		$status = __( 'Not Connected', 'dwqa' );

		if(isset($dwqa_general_settings['use-auto-update-from-evanto']) && $dwqa_general_settings['use-auto-update-from-evanto'] && isset($dwqa_general_settings['evanto-token'])){
			//enable akismet
			if ( class_exists( 'DWQA_Updater' ) ){
				$version = DWQA_Updater::evanto_check_version();
				if($version){
					$status = __( 'Connected', 'dwqa' ).' ('.__( 'lastest version ', 'dwqa' ).$version.')';
				}
			}
		}
		echo '<p>'.$status.'</p>';
	}


	public static function evanto_check_version($token = ''){
		if(!$token || $token==''){
			global $dwqa_general_settings;
			$token = $dwqa_general_settings['evanto-token'];
		}
		if(!$token || $token==''){
			return false;
		}
		$url = 'https://api.envato.com/v3/market/catalog/item-version?id=15057949';
		$args = array(
			'headers' => array(
				'Authorization' => 'Bearer ' . $token
			)
		);
		$response = wp_remote_get( $url, $args);

		if(!is_wp_error($response) && $response && isset($response['response']) && $response['response']['code']=='200' && $response['response']['message']=='OK'){
			$result = json_decode($response['body'], true);
			return $result['wordpress_plugin_latest_version'];
		}

		return false;
	}

	public static function evanto_get_download($token = ''){
		if(!$token || $token==''){
			global $dwqa_general_settings;
			$token = $dwqa_general_settings['evanto-token'];
		}
		if(!$token || $token==''){
			return false;
		}
		$url = 'https://api.envato.com/v3/market/buyer/download?item_id=15057949';
		$args = array(
			'headers' => array(
				'Authorization' => 'Bearer ' . $token
			)
		);
		$response = wp_remote_get( $url, $args);

		if($response && isset($response['response']) && $response['response']['code']=='200' && $response['response']['message']=='OK'){
			$result = json_decode($response['body'], true);
			return $result['wordpress_plugin'];
		}

		return false;
	}




	/**
	 * Register the required plugins for this theme.
	 *
	 *  <snip />
	 *
	 * This function is hooked into tgmpa_init, which is fired within the
	 * TGM_Plugin_Activation class constructor.
	 */
	function dwqa_update_required_plugins() {
		/*
		 * Array of plugin arrays. Required keys are name and slug.
		 * If the source is NOT from the .org repo, then source is also required.
		 */

		$version = DWQA_Updater::evanto_check_version();
		if(!$version){
			return false;
		}
		$source = DWQA_Updater::get_download_url();
		if(!$source){
			return false;
		}

		$plugins = array(
			// This is an example of how to include a plugin bundled with a theme.
			array(
				'name'               => 'DW Question Answer Pro', // The plugin name.
				'slug'               => 'dw-question-answer-pro', // The plugin slug (typically the folder name).
				'source'             => $source, // The plugin source.
				'required'           => true, // If false, the plugin is only 'recommended' instead of required.
				'version'            => $version, // E.g. 1.0.0. If set, the active plugin must be this version or higher. If the plugin version is higher than the plugin version installed, the user will be notified to update the plugin.
				'force_activation'   => false, // If true, plugin is activated upon theme activation and cannot be deactivated until theme switch.
				'force_deactivation' => false, // If true, plugin is deactivated upon theme switch, useful for theme-specific plugins.
				'external_url'       => '', // If set, overrides default API URL and points to an external URL.
				'is_callable'        => '', // If set, this callable will be be checked for availability to determine if a plugin is active.
			)
		);

		tgmpa( $plugins);

	}

	public static function get_last_version(){
		$last_check = get_option('dwqa_pro_version_last_check');
		if($last_check && ($last_check + 86400) > time()){
			$version = get_option('dwqa_pro_version');

			if($version){
				return $version;
			}
		}

		$version = DWQA_Updater::evanto_check_version();
		if(!$version){
			return false;
		}


		update_option('dwqa_pro_version', $version);
		update_option('dwqa_pro_version_last_check', time());
		return $source;
	}

	public static function get_download_url(){

		if(isset($_GET['plugin']) && $_GET['plugin']=='dw-question-answer-pro' && isset($_GET['tgmpa-update']) && $_GET['tgmpa-update']=='update-plugin' ){

			$source = DWQA_Updater::evanto_get_download();
			if(!$source){
				return false;
			}

			return $source;
		}
		return '#';
	}
}
