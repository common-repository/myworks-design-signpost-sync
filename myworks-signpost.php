<?php
/**
 * Plugin Name: Signpost Sync for WooCommerce
 * Plugin URI: http://myworks.design/
 * Description: Automatically sync your WordPress users & WooCommerce customers to your Signpost dashboard with us – all in real time!
 
				- When a Wordpress user is registered, they will be real-time synced over to your Signpost Dashboard.
				- When an order is placed in WooCommerce, the customer will be real-time synced to your Signpost Dashboard.
				- Add a signup form to the frontend of your Wordpress site with a Name, Email and Phone Number field to add signups directly into Signpost!
 * Version: 1.4
 * Author: MyWorks Design
 * Author URI: http://myworks.design/
 * License: A "Slug" license name e.g. GPL2
 */
 
if ( ! defined( 'ABSPATH' ) ) { 
    exit; // Exit if accessed directly
}

function myworkssignpost_plugin_install(){

}
// run the install scripts upon plugin activation
register_activation_hook(__FILE__,'myworkssignpost_plugin_install');

function myworkssignpost_plugin_deactivate(){

}

// run the deactivate scripts upon plugin activation
register_deactivation_hook(__FILE__,'myworkssignpost_plugin_deactivate');


function myworkssignpost_plugin_uninstall(){

}

// run the uninstall scripts upon plugin activation
register_uninstall_hook(__FILE__,'myworkssignpost_plugin_uninstall');

##Css/Js##
function mwsp_add_assets(){
	wp_register_style('mswp-style', plugins_url('css/mswp-style.css',__FILE__ ));
	wp_enqueue_style('mswp-style');
	
	wp_register_script('mswp-js', plugins_url('js/mswp-js.js', __FILE__),array('jquery')); //,'',true
    wp_enqueue_script('mswp-js');
	
	$ajax_url = admin_url('admin-ajax.php');
    $theme_url = get_template_directory_uri();
    $site_url = site_url();
	
	$plugins_url = plugins_url('', __FILE__);
	
    $data = array( 'mwsp_ajax_url' =>  $ajax_url,'mwsp_theme_url' =>  $theme_url,'mwsp_site_url' =>  $site_url,'mwsp_plugins_url' =>  $plugins_url);    
	wp_localize_script( 'mswp-js', 'mswp_js_val', $data );
}

add_action( 'wp_enqueue_scripts', 'mwsp_add_assets' );
 
 ##Plugin Options / Menu##
 
 add_action("admin_menu", "create_mwsp_menu");
 function create_mwsp_menu(){
	 add_menu_page("Signpost", "Signpost Settings", 'edit_pages', "mwsp-signpost", "mwsp_settings");
	 add_action( 'admin_init', 'register_mwsp_settings' );
 }
 
 function mwsp_settings(){
	 include('mwsp-settings.php');
 }
 
 function register_mwsp_settings() {
	register_setting( 'mwsp-settings-group', 'mwsp_api_key' );
	register_setting( 'mwsp-settings-group', 'mwsp_merchant_id' );
	register_setting( 'mwsp-settings-group', 'mwsp_sandbox' );
	register_setting( 'mwsp-settings-group', 'mwsp_sc_css' );
 }
 
function mwsp_check_if_woocommerce_active() { 
  if(class_exists( 'WooCommerce' ) && in_array('woocommerce/woocommerce.php',apply_filters( 'active_plugins', get_option( 'active_plugins' ) ))){
	  return true;
  }
  return false;
}
 
 ##Shortcode##
function mswp_get_form_shortcode($atts){
	extract( 
		shortcode_atts( 
			array(			
			'title' => 'Post Into Signpost',
			'fields' => ''		
			), 
		$atts, 'mw_sp_form' )
	);
	
	if($fields!=''){
		$fields = explode(',',$fields);
	}
	$mwsp_sc_css = esc_attr( get_option('mwsp_sc_css') );
	if(!empty($mwsp_sc_css)){
		echo '<style type="text/css">'.$mwsp_sc_css.'</style>';
	}
	
	ob_start();
	?>
	<div class="mwsp_main mswp_sc_main">	
	<?php if(! empty( $title )){echo '<h2 class="mswp-form-title">' . $title . '</h2>';}?>
		<form action="#" method="post" id="mwsp_form">
			<div class="mwsp_form_fields">
				<div class="mwsp_input">						
					<input type="email" name="mwsp_email" placeholder="Email">
				</div>
				<?php if(is_array($fields) && count($fields)){ ?>
				
				<?php if(in_array('name',$fields)){ ?>
				<div class="mwsp_input">						
					<input type="text" name="mwsp_name" placeholder="Name">
				</div>
				<?php } ?>
				
				<?php if(in_array('phone',$fields)){ ?>
				<div class="mwsp_input">						
					<input type="text" name="mwsp_phone" placeholder="Phone">
				</div>
				<?php } ?>
				
				<?php }?>
				
				<div class="mwsp_action">
					<input type="hidden" name="mwsp_form_post" value="1">
					<?php wp_nonce_field( 'mwsp_form_27062016', 'mwsp_form_key' );?>
					<input class="mwsp-btn" type="submit" name="mwsp_submit" id="mwsp_submit" value="Submit">
				</div>
			</div>
			
		</form>
		<div class="mwsp_status_msg"></div>
	</div>
	<?php
	return ob_get_clean();
}

function mswp_form_shortcode($atts){return mswp_get_form_shortcode($atts);}
add_shortcode('mw_sp_form', 'mswp_form_shortcode');
 
 ##Widget##
 
class myworkssignpost_widget extends WP_Widget {
	function __construct() {
	parent::__construct(	
		'myworkssignpost_widget',
		__('Signpost Widget', 'myworkssignpost'),
		array( 'description' => __( 'Signpost widget options', 'myworkssignpost' ), )
	);
	}
	
	public function widget( $args, $instance ) {
		$title = apply_filters( 'widget_title', $instance['title'] );
		$fields = $instance['sw_fields'];
		
		$sw_css = $instance['sw_css'];
		if(!empty($sw_css)){
			echo '<style type="text/css">'.$sw_css.'</style>';
		}
		
		echo $args['before_widget'];		
	?>
		<div class="mwsp_main">		
		<?php if(! empty( $title )){echo $args['before_title'] . $title . $args['after_title'];}?>
			<form action="#" method="post" id="mwsp_form">
				<div class="mwsp_form_fields">
					<div class="mwsp_input">						
						<input type="email" name="mwsp_email" placeholder="Email">
					</div>
					<?php if(is_array($fields) && count($fields)){ ?>
					
					<?php if(in_array('name',$fields)){ ?>
					<div class="mwsp_input">						
						<input type="text" name="mwsp_name" placeholder="Name">
					</div>
					<?php } ?>
					
					<?php if(in_array('phone',$fields)){ ?>
					<div class="mwsp_input">						
						<input type="text" name="mwsp_phone" placeholder="Phone">
					</div>
					<?php } ?>
					
					<?php }?>
					
					<div class="mwsp_action">
						<input type="hidden" name="mwsp_form_post" value="1">
						<?php wp_nonce_field( 'mwsp_form_27062016', 'mwsp_form_key' );?>
						<input class="mwsp-btn" type="submit" name="mwsp_submit" id="mwsp_submit" value="Submit">
					</div>
				</div>
				
			</form>
			<div class="mwsp_status_msg"></div>
		</div>
	<?php
		echo $args['after_widget'];
	}
	
	 function form($instance) {
		if ( isset( $instance[ 'title' ] ) ) {
			$title = $instance[ 'title' ];
			$title = sanitize_text_field($title);
		}else {
			$title = __( 'Post Into Signpost', 'myworkssignpost' );
		}
		
		if ( isset( $instance[ 'sw_fields' ] ) ) {
			$fields = $instance[ 'sw_fields' ];
			
		}else{
			$fields = array();
		}
		
		if ( isset( $instance[ 'sw_css' ] ) ) {
			$sw_css = $instance[ 'sw_css' ];
			$sw_css = sanitize_text_field($sw_css);
		}else{
			$sw_css = '';
		}
		
		
		?>
		<p>
			<label for="<?php echo $this->get_field_id( 'title' ); ?>"><?php _e( 'Title:' ); ?></label>
			<input class="widefat" id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" type="text" value="<?php echo esc_attr( $title ); ?>" />
		</p>
		
		<p>
		Email is default field, Choose other fields.
		</p>
		
		<p>
			<label for="<?php echo $this->get_field_id( 'sw_fields' ); ?>"><?php _e( 'Other fields:' ); ?></label>
			<select multiple="multiple" class="widefat" id="<?php echo $this->get_field_id( 'fields' ); ?>" name="<?php echo $this->get_field_name( 'sw_fields' ); ?>[]">
				<option value="name" <?php if(is_array($fields) && in_array('name',$fields)){echo 'selected="selected"';} ?>><?php _e( 'Name' ); ?></option>
				<option value="phone" <?php if(is_array($fields) && in_array('phone',$fields)){echo 'selected="selected"';} ?>><?php _e( 'Phone' ); ?></option>
			</select>
		</p>
		
		<p>
			<label for="<?php echo $this->get_field_id( 'sw_css' ); ?>"><?php _e( 'Add CSS:' ); ?></label>
			<textarea class="widefat" name="<?php echo $this->get_field_name( 'sw_css' ); ?>" id="<?php echo $this->get_field_name( 'sw_css' ); ?>"><?php echo esc_attr( $sw_css ); ?></textarea>
		</p>
		<?php
	}
	
	function update( $new_instance, $old_instance ) {
		$instance = array();
		$instance['title'] = ( ! empty( $new_instance['title'] ) ) ? strip_tags( $new_instance['title'] ) : '';
		$instance['sw_fields'] = ( is_array( $new_instance['sw_fields'] ) ) ?  $new_instance['sw_fields']  : array();
		$instance['sw_css'] = ( ! empty( $new_instance['sw_css'] ) ) ? strip_tags( $new_instance['sw_css'] ) : '';
		
		return $instance;
	}
	
}

function myworkssignpost_load_widget() {
	register_widget( 'myworkssignpost_widget' );
}
add_action( 'widgets_init', 'myworkssignpost_load_widget' );

##Post form data##
function mwsp_signpost_ajax(){
	ob_end_clean();
	//
	if ( isset( $_POST['mwsp_form_key'] ) && $_POST['mwsp_form_key']!=''){
		$mswp_sucs = array();
		$mswp_err = array();
		
		$return_msg = array();
		
		if (wp_verify_nonce( $_POST['mwsp_form_key'], 'mwsp_form_27062016' ) ) {
			if(trim($_POST['mwsp_email'])=='' || !is_email($_POST['mwsp_email'])){
		        $mswp_err[] =  __('Please enter a valid email address.', 'myworkssignpost');
		    }
			if(isset($_POST['mwsp_name']) && trim($_POST['mwsp_name'])==''){
		        $mswp_err[] =  __('Please enter your name.', 'myworkssignpost');
				
		    }
			
			if(isset($_POST['mwsp_phone']) && trim($_POST['mwsp_phone'])==''){
		        $mswp_err[] =  __('Please enter your phone.', 'myworkssignpost');
		    }
			
			if(isset($_POST['mwsp_phone']) && (!is_numeric($_POST['mwsp_phone']) || strlen(trim($_POST['mwsp_phone']))<10)){
				$mswp_err[] =  __('Phone must be numeric and min 10 chars.', 'myworkssignpost');
			}
		}else{
			$mswp_err[] = __('There are some problem,please refresh the page and try again.', 'myworkssignpost');
		}
		
		if(empty($mswp_err)){
				$mwsp_email = trim($_POST['mwsp_email']);
				$mwsp_name = (isset($_POST['mwsp_name']))?trim($_POST['mwsp_name']):'';
				$mwsp_phone = (isset($_POST['mwsp_phone']))?trim($_POST['mwsp_phone']):'';
				
				
				$mwsp_email = sanitize_email($mwsp_email);
				$mwsp_name = sanitize_text_field( $mwsp_name );
				$mwsp_phone = sanitize_text_field( $mwsp_phone );
				
				$sp_api_url = 'https://api.signpost.com/v1';				
				$sp_api_url_sandbox = 'https://api-sandbox.signpost.com/v1';
				
				$mwsp_sandbox = esc_attr( get_option('mwsp_sandbox') );
				if($mwsp_sandbox=='Yes'){
					$sp_api_endpoint = $sp_api_url_sandbox;
				}else{
					$sp_api_endpoint = $sp_api_url;
				}
				
				$sp_api_endpoint = $sp_api_endpoint.'/contacts';
				
				$signpost_api_key = esc_attr( get_option('mwsp_api_key') );
				$merchantId = esc_attr( get_option('mwsp_merchant_id') );
				
				$signpost_api_key = sanitize_text_field( $signpost_api_key );
				$merchantId = sanitize_text_field( $merchantId );
				
				$data = array();
				
				$data['merchantId'] = $merchantId;
				if($mwsp_name!=''){
					$data['name'] = $mwsp_name;
				}
				
				$contacts = array();
				$contacts[]['emailAddress'] = $mwsp_email;
				
				if($mwsp_phone!=''){
					$contacts[]['phoneNumber'] = $mwsp_phone;
				}
				
				$data['contacts'] = $contacts;
				
				$data_string = json_encode($data);				
				//
				$ch = curl_init($sp_api_endpoint);
				curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");                                                                     
				curl_setopt($ch, CURLOPT_POSTFIELDS, $data_string);                                                                  
				curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);                                                                      
				curl_setopt($ch, CURLOPT_HTTPHEADER, array(
					'x-api-key: '.$signpost_api_key,
					'Content-Type: application/json',                                                                                
					'Content-Length: ' . strlen($data_string))                                                                       
				);                                                                                                                   
																																	 
				$result = curl_exec($ch);
				if($result!=''){
					$result = json_decode($result,true);
					if(is_array($result) && isset($result['message'])){
						if($result['message']!='Success'){
							$mswp_err[] = __($result['message'], 'myworkssignpost');
						}						
					}else{
						$mswp_err[] = __('Error!', 'myworkssignpost');
					}
				}else{
					$mswp_err[] = __('Error!', 'myworkssignpost');
				}
				
		}
		
		if(!empty($mswp_err)){
			$err_msg = '<div class="mwsp_err_msg">'.implode('<br />',$mswp_err).'</div>';
			
			$return_msg['status']=0;
			$return_msg['msg'] = $err_msg;
		}else{
			$suc_msg = __('Success.', 'myworkssignpost');
			if(count($mswp_sucs)){
				$err_msg = '<div class="mwsp_sucs_msg">'.implode('<br />',$mswp_sucs).'</div>';
			}
			$return_msg['status']=1;
			$return_msg['msg'] = $suc_msg;
		}
		
		
		if(count($return_msg)){
			echo json_encode($return_msg);
		}
	}
	wp_die();
}

add_action( 'wp_ajax_mwsp_post_signpost', 'mwsp_signpost_ajax' );
add_action( 'wp_ajax_nopriv_mwsp_post_signpost', 'mwsp_signpost_ajax' );

if ( mwsp_check_if_woocommerce_active() ) {
	add_action( 'user_register', 'mwsp_registration_realtime', 10, 1 );
}

function mwsp_registration_realtime( $user_id ) {

    $user_info = get_userdata($user_id);
    $mwsp_name = $user_info->first_name .  " " . $user_info->last_name ;
    $mwsp_email = $user_info->user_email ;
    $mwsp_phone = get_user_meta($user_id,'phone',true);

    $sp_api_url = 'https://api.signpost.com/v1';				
	$sp_api_url_sandbox = 'https://api-sandbox.signpost.com/v1';
	
	$mwsp_sandbox = esc_attr( get_option('mwsp_sandbox') );
	if($mwsp_sandbox=='Yes'){
		$sp_api_endpoint = $sp_api_url_sandbox;
	}else{
		$sp_api_endpoint = $sp_api_url;
	}
	
	$sp_api_endpoint = $sp_api_endpoint.'/contacts';
	
	$signpost_api_key = esc_attr( get_option('mwsp_api_key') );
	$merchantId = esc_attr( get_option('mwsp_merchant_id') );
	
	$signpost_api_key = sanitize_text_field( $signpost_api_key );
	$merchantId = sanitize_text_field( $merchantId );
	
	$data = array();
	
	$data['merchantId'] = $merchantId;
	if($mwsp_name!=''){
		$data['name'] = $mwsp_name;
	}
	
	$contacts = array();
	$contacts[]['emailAddress'] = $mwsp_email;
	
	if($mwsp_phone!=''){
		$contacts[]['phoneNumber'] = $mwsp_phone;
	}
	
	$data['contacts'] = $contacts;
	
	$data_string = json_encode($data);				
	//
	$ch = curl_init($sp_api_endpoint);
	curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");                                                                     
	curl_setopt($ch, CURLOPT_POSTFIELDS, $data_string);                                                                  
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);                                                                      
	curl_setopt($ch, CURLOPT_HTTPHEADER, array(
		'x-api-key: '.$signpost_api_key,
		'Content-Type: application/json',                                                                                
		'Content-Length: ' . strlen($data_string))                                                                       
	);                                                                                                                   
																														 
	$result = curl_exec($ch);
}