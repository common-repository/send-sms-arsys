<?php
/*
Plugin Name: Send SMS to Users via Arsys
Plugin URI: http://www.closemarketing.es/
Description: Allow Administrator Users send to Wordpress Users using a customized variable for the telephone number. It's send the sms via Arsys.
Author: David Perez
Stable tag: 1.2
Version: 1.2
Author URI: http://twitter.com/closemarketing
*/

//require_once WP_PLUGIN_DIR . "/send-sms-arsys/install.php";
define('SENDSMSARSYS_VERSION', '1.1');
require_once WP_PLUGIN_DIR . "/send-sms-arsys/options.php";
require_once WP_PLUGIN_DIR . "/send-sms-arsys/functions.php";

//Add the admin menu to the dashboad
add_action('admin_menu', 'sms_add_menu');

//Profile SMS field
add_action( 'send_headers', 'sms_set_cookie');



//Add ajax script to blog
wp_enqueue_script('jquery');
wp_register_script("send-sms-arsys", "/wp-content/plugins/send-sms-arsys/send-sms-arsys.js");
wp_enqueue_script('send-sms-arsys');
global $smssuccfail;

function sms_add_menu() {
	add_menu_page(__('Send SMS','sendsmsarsys'), __('Send SMS','sendsmsarsys'), 8, __FILE__, 'sms_main_page',WP_PLUGIN_URL . '/send-sms-arsys/arsys.png');
	add_submenu_page(__FILE__, __('Options','sendsmsarsys'), __('Options','sendsmsarsys'), 8, 'sendsmsarsys-options', 'sms_options_page');
}

function sendsmsarsys_init() {
	load_plugin_textdomain( 'sendsmsarsys', false, basename( dirname( __FILE__ ) ) . '/lang' );
	}
	//Load Translation
add_action('plugins_loaded', 'sendsmsarsys_init');
//Handle POST variables to save options and send messages

//First check if sms messages need to be sent
if(!empty($_POST['sms_message'])) {

	$sms_user = get_option( "sms_user" );
	$sms_password = get_option( "sms_password" );
	$sms_from = get_option( "sms_from" );
	$sms_metavar = get_option( "sms_metavar" );
	$sms_codecountry = get_option( "sms_codecountry" );
	$baseurl ="https://sms.arsys.es";
	$text = urlencode($_POST['sms_message']);

		//Send SMS to subscribed readers


			global $wpdb;
			$sess_id = trim($sess[1]); // remove any whitespace
			$actrol = $_POST['actrol'];

			$aUsersID = $wpdb->get_col( $wpdb->prepare("SELECT $wpdb->users.ID FROM $wpdb->users"));


			foreach ( $aUsersID as $iUserID ) :
				$sms_number = get_user_meta( $iUserID, $sms_metavar );
				$sms_number = $sms_number[0];

				$user = new WP_User( $iUserID );

				if ( !empty( $user->roles ) && is_array( $user->roles ) ) {
					foreach ( $user->roles as $role )
						$sms_role = $role;
				}

				if ( $sms_number <> "" && $sms_role==$actrol) {
					$sms_number = $sms_codecountry.$sms_number;

					$url= "$baseurl/smsarsys/accion/enviarSms2.jsp?id=$sms_user&phoneNumber=$sms_number&psw=$sms_password&textSms=$text&remite=$sms_from";

			        // do sendmsg call
			        $ret = file($url);
			        $send = explode(":",$ret[0]);

					$smssuccfail .= "<span style=\"color: orange\">Mensaje para $sms_number.</span>";
					$smssuccfail .= "<span style=\"color: orange\">Estado: ".$ret[2]."</span><br/>";


				}
			endforeach;

}

//Update SMS options
if(!empty($_POST['sms_options'])) {
	update_option( "sms_user", $_POST['sms_api_user']);
	update_option( "sms_password", $_POST['sms_api_pass']);
	update_option( "sms_header", $_POST['sms_header']);
	update_option( "sms_footer", $_POST['sms_footer']);
	update_option( "sms_from", $_POST['sms_from']);
	update_option( "sms_metavar", $_POST['sms_metavar']);
	update_option( "sms_codecountry", $_POST['sms_codecountry']);
}
?>