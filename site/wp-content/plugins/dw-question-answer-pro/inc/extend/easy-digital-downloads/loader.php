<?php
// Exit if accessed directly
if ( !defined( 'ABSPATH' ) ) exit;

if ( !class_exists( 'DWQA_EDD' ) ) :

class DWQA_EDD{

	public function __construct() {

		add_action('dwqa_register_middle_setting_field', array($this, 'addSetting'));

		global $dwqa_general_settings;
		if(isset($dwqa_general_settings['use-user-expiration-edd']) && $dwqa_general_settings['use-user-expiration-edd']){
			add_action( 'add_meta_boxes', array( $this, 'addMetaBoxes' ) );
			add_action( 'save_post', array( $this, 'saveMetaBox' ));

			// //display on frontend
			// add_action( 'woocommerce_before_add_to_cart_button', array($this, 'displayCustomField' ));
			add_action( 'edd_purchase_form_top', array($this, 'showNeedLogin' ), 10, 1 );

			// //check on process
			add_action('edd_checkout_error_checks', array($this, 'checkoutProcess'), 10);
			add_action('edd_insert_payment', array($this, 'insertPaymentInfo'), 10, 2);
			add_action('edd_update_payment_status', array($this, 'paymentStatusCompleted'), 10, 3);

			// //display item in order
			// add_filter('woocommerce_order_item_display_meta_key', array($this, 'orderItemDisplayMetaKey'));

			// add_action( 'woocommerce_order_status_completed', array($this, 'orderStatusCompleted') );
		}
		//
	}

	public function addSetting(){
		add_settings_field(
			'dwqa_options[use-user-expiration-edd]',
			__( 'Intergration with EDD', 'dwqa' ),
			array($this, 'useUserExpirationEDD'),
			'dwqa-settings',
			'dwqa-user-expiration-settings'
		);
	}
	public function useUserExpirationEDD(){
		global $dwqa_general_settings;
		
		echo '<p><label for="dwqa_use_user_expiration_edd"><input type="checkbox" name="dwqa_options[use-user-expiration-edd]"  id="dwqa_use_user_expiration_edd" value="1" '.checked( 1, (isset($dwqa_general_settings['use-user-expiration-edd'] ) ? $dwqa_general_settings['use-user-expiration-edd'] : false ) , false ) .'><span class="description">'.__( 'Enable integration with Easy Digital Downloads (enable user expiration required)', 'dwqa' ).'</span></label></p>';
	}

	public function addCustomDataToOrder( $item, $cart_item_key, $values, $order ) {
	 foreach( $item as $cart_item_key=>$values ) {
		 if( isset( $values['title_field'] ) ) {
		 $item->add_meta_data( __( 'Custom Field', 'cfwc' ), $values['title_field'], true );
		 }
	 }
	}

	public function cartItemName( $name, $cart_item, $cart_item_key ) {
		if( isset( $cart_item['title_field'] ) ) {
		$name .= sprintf(
		'<p>%s</p>',
		esc_html( $cart_item['title_field'] )
		);
		}
		return $name;
	}

	public function beforeCalculateTotals( $cart_obj ) {
		if ( is_admin() && ! defined( 'DOING_AJAX' ) ) {
			return;
		}
		// Iterate through each cart item
		foreach( $cart_obj->get_cart() as $key=>$value ) {
			if( isset( $value['total_price'] ) ) {
				$price = $value['total_price'];
				$value['data']->set_price( ( $price ) );
			}
		}
	}

	public function addCustomFieldItemData( $cart_item_data, $product_id, $variation_id, $quantity ) {
		if( ! empty( $_POST['cfwc-title-field'] ) ) {
			// Add the item data
			$cart_item_data['title_field'] = $_POST['cfwc-title-field'];
			$product = wc_get_product( $product_id ); // Expanded function
			$price = $product->get_price(); // Expanded function
			$cart_item_data['total_price'] = $price + 100; // Expanded function
		}
		return $cart_item_data;
	}

	public function orderStatusCompleted($order_id){
		$order = wc_get_order( $order_id );
		if ( ! $order ) {
			return;
		}

		$user_id = $order->get_user_id();
		if(!$user_id){
			return;
		}

		//get current time
		$old_dwqa_expiration = get_user_meta( $user_id, 'dwqa_expiration', true );
		//if not exist expiration time => get current time
		$old_dwqa_expiration = $old_dwqa_expiration?$old_dwqa_expiration:current_time('timestamp');

		$new_date = date("Y-m-d H:i:s", $old_dwqa_expiration);

		$items = $order->get_items();
		foreach($items as $item){
			$item_id = $item->get_id();
			$item_quantity = $item->get_quantity();
			$dwqa_woo_type = wc_get_order_item_meta($item_id, 'dwqa_woo_type', true);


			if($dwqa_woo_type == 'time'){
				$dwqa_woo_time_unit = wc_get_order_item_meta($item_id, 'dwqa_woo_time_unit', true);
				$dwqa_woo_time_number = wc_get_order_item_meta($item_id, 'dwqa_woo_time_number', true);

				switch ($dwqa_woo_time_unit) {
					case 'day':
						$new_date = date("Y-m-d H:i:s", strtotime($new_date . ' +' . (int)($dwqa_woo_time_number * $item_quantity) . ' days'));
						break;
					case 'month':
						$new_date = date("Y-m-d H:i:s", strtotime($new_date . ' +' . (int)($dwqa_woo_time_number * $item_quantity) . ' months'));
						break;
					case 'month':
						$new_date = date("Y-m-d H:i:s", strtotime($new_date . ' +' . (int)($dwqa_woo_time_number * $item_quantity) . ' year'));
						break;
					
					default:
						
						break;
				}
			}
			
		}

		update_user_meta( $user_id, 'dwqa_expiration', strtotime($new_date) );
	}

	public function orderItemDisplayMetaKey($display_key){
		if($display_key == 'dwqa_woo_time_unit'){
			return __('DWQA Unit', 'dwqa');
		}
		if($display_key == 'dwqa_woo_time_number'){
			return __('DWQA Number', 'dwqa');
		}
		if($display_key == 'dwqa_woo_type'){
			return __('DWQA Type', 'dwqa');
		}
		return $display_key;
	}

	public function insertPaymentInfo($payment_id, $payment_data){
		if($this->isDWQAInCart()){

			if( isset($payment_data['user_info']) && isset($payment_data['user_info']['id']) && $payment_data['user_info']['id'] && is_array( $payment_data['cart_details'] ) && ! empty( $payment_data['cart_details'] ) ) {

				foreach ( $payment_data['cart_details'] as $cart_item ) {

					$download_id = $cart_item['id'];
		   		 	$dwqa_edd_enable = get_post_meta($download_id, 'dwqa_edd_enable', true);

					if($dwqa_edd_enable){
						$dwqa_edd_type = get_post_meta($download_id, 'dwqa_edd_type', true);

						
						if($dwqa_edd_type == 'time'){
							$dwqa_edd_time_unit = get_post_meta($download_id, 'dwqa_edd_time_unit', true);
							$dwqa_edd_time_number = get_post_meta($download_id, 'dwqa_edd_time_number', true);

							if($dwqa_edd_time_unit && $dwqa_edd_time_number){
								update_post_meta( $payment_id, 'dwqa_edd_type_'.$download_id, $dwqa_edd_type );
								update_post_meta( $payment_id, 'dwqa_edd_time_unit_'.$download_id, $dwqa_edd_time_unit );
								update_post_meta( $payment_id, 'dwqa_edd_time_number_'.$download_id, $dwqa_edd_time_number );
							}
						}
					}
				}
			}

		}
	}

	public function paymentStatusCompleted($payment_id, $status, $old_status){
		if($status == 'publish' && $old_status != 'publish'){
			$payment  = edd_get_payment( $payment_id );
			$payment_data = $payment->get_meta();

			if( isset($payment_data['user_info']) && isset($payment_data['user_info']['id']) && $payment_data['user_info']['id'] && is_array( $payment_data['cart_details'] ) && ! empty( $payment_data['cart_details'] ) ) {
				
				$user_id = $payment_data['user_info']['id'];

				$check = false;

				//get current time
				$old_dwqa_expiration = get_user_meta( $user_id, 'dwqa_expiration', true );
				//if not exist expiration time => get current time
				$old_dwqa_expiration = $old_dwqa_expiration?$old_dwqa_expiration:current_time('timestamp');

				$new_date = date("Y-m-d H:i:s", $old_dwqa_expiration);

				foreach ( $payment_data['cart_details'] as $cart_item ) {

					$download_id = $cart_item['id'];
					$item_quantity = $cart_item['quantity'];

					$dwqa_edd_type = get_post_meta( $payment_id, 'dwqa_edd_type_'.$download_id, true );

					if($dwqa_edd_type && $dwqa_edd_type == 'time'){
						$check = true;

						$dwqa_edd_time_unit = get_post_meta( $payment_id, 'dwqa_edd_time_unit_'.$download_id, true );
						$dwqa_edd_time_number = get_post_meta( $payment_id, 'dwqa_edd_time_number_'.$download_id, true );

						if($dwqa_edd_time_unit && $dwqa_edd_time_number){

							switch ($dwqa_edd_time_unit) {
								case 'day':
									$new_date = date("Y-m-d H:i:s", strtotime($new_date . ' +' . (int)($dwqa_edd_time_number * $item_quantity) . ' days'));
									break;
								case 'month':
									$new_date = date("Y-m-d H:i:s", strtotime($new_date . ' +' . (int)($dwqa_edd_time_number * $item_quantity) . ' months'));
									break;
								case 'month':
									$new_date = date("Y-m-d H:i:s", strtotime($new_date . ' +' . (int)($dwqa_edd_time_number * $item_quantity) . ' year'));
									break;
								
								default:
									
									break;
							}
						}

					}

				}

				if($check){
					update_user_meta( $user_id, 'dwqa_expiration', strtotime($new_date) );
				}

			}
		}
	}

	public function checkoutProcess(){
        if(!is_user_logged_in()){

		    if($this->isDWQAInCart()){
		    	$error_notice = apply_filters( 'dwqa_edd_checkout_process_login_message', __( 'Have DWQA product in cart. Need to login!', 'dwqa' ) );
		 		edd_set_error( 'dwqa_need_login', $error_notice );
		    }
		}
	}

	public function showNeedLogin($checkout){

		if(!is_user_logged_in()){

		    if($this->isDWQAInCart()){
		    	?>
		    	<div id="dwqa_edd_need_login" class="edd_errors edd-alert edd-alert-error">
		    		<p class="edd_error" id="edd_error_logged_in_only">
		    			<strong><?php echo __('Error', 'dwqa');?></strong>: <?php echo apply_filters( 'dwqa_woo_checkout_login_message', __( 'Have DWQA product in cart. Need to login!', 'dwqa' ) );?>
		    		</p>
		    	</div>
		    	<?php
		    	//check if edd setting have login form
			    if('none' === edd_get_option( 'show_register_form', 'none' )){
			    	//no setting => show login form
			    	?>
			    	
					<div id="edd_checkout_login_register">
						<?php do_action( 'edd_purchase_form_login_fields' ); ?>
					</div>
					<?php

			    }
		    }
		}
	}

	private function isDWQAInCart(){
		$cart = EDD()->cart->get_contents();

		if(!empty($cart)){
			foreach ( $cart as $cart_item ) {
	   		 	$download_id = $cart_item['id'];
	   		 	if(get_post_meta($download_id, 'dwqa_edd_enable', true )){
	   		 		return true;
	   		 	}
		    }
	    }
	    return false;
	}

	public function displayCustomField() {
		global $post;
 		// Check for the custom field value
		$product = wc_get_product( $post->ID );

		
		$dwqa_woo_enable = $product->get_meta( 'dwqa_woo_enable' );
		if($dwqa_woo_enable){
			$dwqa_woo_type = $product->get_meta( 'dwqa_woo_type' );

			if($dwqa_woo_type == 'time'){
				$dwqa_woo_time_unit = $product->get_meta( 'dwqa_woo_time_unit' );
				$dwqa_woo_time_number = $product->get_meta( 'dwqa_woo_time_number' );

				if($dwqa_woo_time_unit && $dwqa_woo_time_number){
					printf(
						'<div class="dwqa-woo-wrapper">
							<label for="cfwc-title-field">%s %s</label>
						</div>',
						esc_html( $dwqa_woo_time_number ), esc_html( $dwqa_woo_time_unit )
					);
				}
			}
		}
		

	}

	public function saveMetaBox( $post_id ) {

		if ( !current_user_can('administrator'))
			return;

		if(isset($_POST['dwqa_edd_enable'])){
			update_post_meta($post_id, 'dwqa_edd_enable', sanitize_text_field( $_POST['dwqa_edd_enable'] ) );
		}else{
			update_post_meta($post_id, 'dwqa_edd_enable', false );
		}

		if(isset($_POST['dwqa_edd_type'])){
			update_post_meta($post_id, 'dwqa_edd_type', sanitize_text_field( $_POST['dwqa_edd_type'] ) );
		}

		if(isset($_POST['dwqa_edd_time_unit'])){
			update_post_meta($post_id, 'dwqa_edd_time_unit', sanitize_text_field( $_POST['dwqa_edd_time_unit'] ) );
		}

		if(isset($_POST['dwqa_edd_time_number'])){
			update_post_meta($post_id, 'dwqa_edd_time_number', sanitize_text_field( $_POST['dwqa_edd_time_number'] ) );
		}
	}



	public function createCustomField($download) {

		$download_id = $download->ID;

		$dwqa_edd_enable = get_post_meta($download_id, 'dwqa_edd_enable', true);
		$dwqa_edd_time_number = get_post_meta($download_id, 'dwqa_edd_time_number', true);
		$dwqa_edd_time_number = $dwqa_edd_time_number?$dwqa_edd_time_number:'';

		$dwqa_edd_time_unit = get_post_meta($download_id, 'dwqa_edd_time_unit', true);
		$dwqa_edd_time_unit = $dwqa_edd_time_unit?$dwqa_edd_time_unit:'';

		$options_time = array( 'day' => 'day', 'month' => 'month', 'year' => 'year' );

		?>

		<table class="wp-list-table widefat striped">
			<tr>
				<th><?php echo  __( 'Enable', 'dwqa' ); ?></th>
				<td>
					<label for="dwqa_edd_enable">
						<input type="checkbox" name="dwqa_edd_enable" id="dwqa_edd_enable" value="1" <?php checked( 1, $dwqa_edd_enable ); ?> />
						<?php echo  __( 'Enable DWQA in Download.', 'dwqa' ); ?>
					</label>
				</td>
			</tr>

			<tr>
				<th><?php echo  __( 'Number of time', 'dwqa' ); ?></th>
				<td>
					<input type="number" name="dwqa_edd_time_number" id="dwqa_edd_time_number" value="<?php echo $dwqa_edd_time_number; ?>" />
				</td>
			</tr>

			<tr>
				<th><?php echo  __( 'Time Unit', 'dwqa' ); ?></th>
				<td>
					<select name="dwqa_edd_time_unit">
						<?php foreach($options_time as $key => $value):?>
						<option value="<?php echo $key;?>" <?php echo $key==$dwqa_edd_time_unit?'selected':'';?>><?php echo $value;?></option>
						<?php endforeach;?>
					</select>
					<span class="description"><?php echo __( 'Unit of time.', 'dwqa' );?></span>
				</td>
			</tr>
		</table>

		<input type="hidden" name="dwqa_edd_type" value="time" />

		<?php
	}

	// add meta box
	public function addMetaBoxes() {
	    add_meta_box(
	    	'meta-box-dwqa-edd',
	    	__( 'DWQA', 'dwqa' ),
	    	array($this, 'createCustomField'),
	    	'download'
	    );
	}
}
endif;
