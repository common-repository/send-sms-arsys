<?php
function sms_options_page() {
$sms_api_user = get_option( "sms_user" );
$sms_api_pass = get_option( "sms_password" );
$sms_from = get_option( "sms_from" );
$sms_metavar = get_option( "sms_metavar" );
$sms_codecountry = get_option( "sms_codecountry" );
?>
	<div class="wrap">
		<h2><?php _e('Options', 'sendsmsarsys'); ?></h2>
		<p><?php _e('Fill the necessary data in order to send SMS. Please remind that the plugin uses Arsys platform to send SMS.', 'sendsmsarsys'); ?></p>

		<br/>
		<form name='sms_update_options' id='sms_update_options' method='POST' action='<?php echo "http://" . $_SERVER['HTTP_HOST'] . $_SERVER['PHP_SELF'] . "?" . $_SERVER['QUERY_STRING'] ?>'>
			<table>
				<tr>
					<td><?php _e('API User', 'sendsmsarsys'); ?></td>
					<td><input type="text" name="sms_api_user" value="<?php echo $sms_api_user;?>"/></td>
				</tr>
				<tr>
					<td><?php _e('API Password', 'sendsmsarsys'); ?></td>
					<td><input type="text" name="sms_api_pass" value="<?php echo $sms_api_pass;?>"/></td>
				</tr>
				<tr>
					<td><?php _e('Reply SMS', 'sendsmsarsys'); ?></td>
					<td><input type="text" name="sms_from" value="<?php echo $sms_from;?>"/></td>

				<tr>
					<td><?php _e('User Metavar WP', 'sendsmsarsys'); ?></td>
					<td>
					<?php
					global $wpdb;
					$usermeta = $wpdb->get_col( "SELECT meta_key FROM $wpdb->usermeta");
					?>
					<select name="sms_metavar" value="'.$sms_metavar.'">

					<option value="" <?php if ($sms_metavar=="") {echo ' selected="selected"';}?>>
					</option>
					<?php
					foreach ($usermeta as $usermetaval)
					{
					  echo '<option value="'.$usermetaval.'" ';
					  if ($sms_metavar==$usermetaval) {echo ' selected="selected"';};
					  echo '>'.$usermetaval.'</option>';
					} ?>
					</select>
					</td>



				</tr>
				<tr>
					<td><?php _e('Code Country', 'sendsmsarsys'); ?></td>
					<td><input type="text" name="sms_codecountry" value="<?php echo $sms_codecountry;?>"/></td>
				</tr>
			</table><br/>
			<span class="submit"><input type="submit" value="<?php _e('Update', 'sendsmsarsys'); ?>" name="sms_options"/></span>
		</form>
	</div>
	<br/>
	<div>
	</div>
<?php
}

function sms_meta_box_send(){
	global $smssuccfail;
	$sms_maxlen = "160";
?>
	<div style="padding: 10px;">
		<form name='send_sms_form' id='send_sms_form' method='POST'>
			<table>
				<tr>
					<td><?php _e('Text Message:', 'sendsmsarsys'); ?></td>
				</tr>
				<tr>
					<td>
						<textarea maxlength="<?php echo $sms_maxlen; ?>" name="sms_message" id="sms_message" style="width: 521px; height: 50px;"></textarea>
					</td>
				</tr>
				<tr>
					<td><input size=5 value="<?php echo $sms_maxlen; ?>" name="sms_left" id="sms_left" readonly="true"> <?php _e('Chars', 'sendsmsarsys'); ?></td>
				</tr>
				<tr>
					<td>

					<?php
					$result = count_users();
					echo '<p><b>';
					_e('Total Users Registered:','sendsmsarsys');
					echo $result['total_users'], ' </b></p>'; ?>

					<b><?php _e('Send SMS to Users Group:', 'sendsmsarsys'); ?></b>
					<?php
					  //Selección Permisos
			          $roles_list = get_editable_roles();

			          echo '<select name="actrol" style="width:97%;" value="">';
			          echo '<option value=""></option>';
			          foreach ($roles_list as $role => $details) {
			              $roles_ID = esc_attr($role);
			              $roles_name = translate_user_role($details['name'] );



			              foreach($result['avail_roles'] as $role => $count) {
							if ($roles_ID == $role) {
							echo '<option value="'.$roles_ID.'">'.$roles_name;
						    echo ' -> '.$count.' usuario';
						    if ($count>1) { echo 's'; }
						    echo '</option>';
						    } //if roles_ID
						  }


			          }
			          echo '</select>';

			          ?>



					</td>
				</tr>
			</table>
			<span class="submit"><input type="submit" value="<?php _e('Send SMS', 'sendsmsarsys'); ?>" /></span>
		</form>
<?php
		echo $smssuccfail;
		$smssuccfail = '';?>
	</div>
<?php
}

function sms_main_page() {
	global $smssuccfail;
?>
	<div class="wrap">
		<h2><?php _e('Send SMS Panel via Arsys','sendsmsarsys'); ?></h2>
	</div>
<?php
	add_meta_box("sms_send", __("Send SMS Messages to a Users Group","sendsmsarsys"), "sms_meta_box_send", "sms");
?>
	<div id="dashboard-widgets-wrap">
		<div class="metabox-holder">
			<div style="float:left; width:50%;" class="inner-sidebar1">
<?php
	do_meta_boxes('sms','advanced','');
?>
			</div>
			<div style="float:right; width:50%;" class="inner-sidebar2">
<?php
	do_meta_boxes('smsstats','advanced','');
?>
			</div>
		</div>
	</div>
<?php
}

?>