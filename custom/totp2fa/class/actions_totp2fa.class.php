<?php
class Actionstotp2fa
{ 
	/**
	 * @var DoliDB Database handler.
	 */
	public $db;

	/**
	 * @var string Error code (or message)
	 */
	public $error = '';

	/**
	 * @var array Errors
	 */
	public $errors = array();


	/**
	 * @var array Hook results. Propagated to $hookmanager->resArray for later reuse
	 */
	public $results = array();

	/**
	 * @var string String displayed by executeHook() immediately after return
	 */
	public $resprints;

	
	/**
	 * Constructor
	 *
	 *  @param		DoliDB		$db      Database handler
	 */
	public function __construct($db)
	{
		global $langs;
		$langs->load("totp2fa@totp2fa");

		$this->db = $db;
		include_once(__DIR__."/../lib/functions.lib.php");
		
	}

	/*
	 * context: mainloginpage
	 * mission: to render a third <input type=text> control for 6-digit TOTP code on login page
	 */
	function getLoginPageOptions( $parameters, &$object, &$action, $hookmanager ){
		
		global $langs, $conf;
		
		$error_style = !empty($_SESSION['totp2fa_msg'] ) ? 'border-color:red;' : '';
		
		if (intval(DOL_VERSION) <= 8){ /* it really this case don't matter because the module is not compatible with those versions */
				$this->resprints = '
						<tr>
							<td class="nowrap center valignmiddle">
								<span class="fa fa-clock-o"></span>&nbsp; 
								<input type="text" id="securitycode" maxlength="6" placeholder="'.$langs->trans('totp2fa_6digit').'" name="totp2fa" class="flat input-icon-user minwidth150" value="" tabindex="3" autocomplete="off" style="'.$error_style.'" />
							</td>
						</tr>';
		}else{
				$this->resprints = '
					<div class="trinputlogin">
						<div class="tagtd nowraponall center valignmiddle tdinputlogin">
							<span class="fa fa-clock-o"></span>
							<input type="text" id="securitycode" maxlength="6" placeholder="'.$langs->trans('totp2fa_6digit').'" name="totp2fa" class="flat input-icon-user minwidth150" value="" tabindex="3" autocomplete="off" style="'.$error_style.'" />
						</div>
					</div>';
		}
		
		$population_script = '<script>$(document).ready(function(){';
		if (!empty($_SESSION['totp2fa_username'])){
			$population_script .= '$("#username").val("'.$_SESSION['totp2fa_username'].'");';
		}	
		if (!empty($_SESSION['totp2fa_password'])){
			$population_script .= '$("#password").val("'.$_SESSION['totp2fa_password'].'");';
			$population_script .= '$("#securitycode").focus();';
		}	
		$population_script .= '});</script>';
		$this->resprints .= $population_script;

		$_SESSION['totp2fa_username'] = '';
		$_SESSION['totp2fa_password'] = '';
	}	
	
	/*
	 * context: login
	 * mission:
	 * 			a) check that the IP belong to a permitted country
	 * 			b) to validate the POSTED 6-digit TOTP code on main login page, IF it's not a SECURE REMEMBERED device for this user
	 */
	function afterLogin( $parameters, &$object, &$action, $hookmanager ){
		global $conf, $user, $langs;
		
		$user_id  = !empty($object->id)    ? $object->id : ''; // ->ref ?

		/* === filter by IP-country === */
		
		include_once(__DIR__."/../class/totp_ip.class.php");
		$ip_country_code = Totp_IP::ip2country();
		
		$sett_countries = !empty($conf->global->TOTP2FA_MODULE_SETT_COUNTRIES) ? trim($conf->global->TOTP2FA_MODULE_SETT_COUNTRIES) : '';
		if ($ip_country_code!='' && $sett_countries!=''){
			if (!preg_match('/'.$ip_country_code.'/i',$sett_countries)){

				/* close user session & return to login */
				$this->db->rollback();
				session_reset();
				
				$_SESSION['totp2fa_username'] = GETPOST('username');
				$_SESSION['totp2fa_password'] = GETPOST('password');
				$_SESSION['dol_loginmesg']    = $langs->trans('totp2fa_WrongCountry');

				header('Location: '.DOL_URL_ROOT.'/index.php');
				exit;
				
			}
		}
		
		/* === check remember SECURED device === */
		
		// 2FA cookie to remember this device need these 3 elements: 
		// - timestamp of the current TOTp validation (to validate the expiring time)
		// - random 8-digit code unique for each device and TOTP validation
		// - the HTTP_USER_AGENT (to try to "guarantee" -at leaset make it more difficult- to use the same cookie value from other devices, like a "light" browser fingerprint )

		$period_seconds = array('0'=>0, '1d'=>(24*3600), '1w'=>(7*24*3600), '1m'=>(30*24*3600));
		$sett_period = !empty($conf->global->TOTP2FA_MODULE_SETT_REMEMBER_PERIOD) ? $conf->global->TOTP2FA_MODULE_SETT_REMEMBER_PERIOD : '0';
		$time_now = time();
		$remember_cookie_lifetime = $time_now - $period_seconds[$sett_period];
		$b_secured_device = false; 
		$b_expired_devices = false;
		if (!empty($user_id) && !empty($conf->global->{'TOTP_USER_'.$user_id})){
			$devices =  json_decode($conf->global->{'TOTP_USER_'.$user_id},true);
		}else{
			$devices = array();
		}
		
		if (!empty($user_id) && !empty($_COOKIE['totp_'.$user_id]) && count($devices)>0){
			//echo var_dump($devices);die;
			foreach ($devices as $iid=>$device){
				// check that the stored cookie has not expired
				if (intval($device[1]) > $remember_cookie_lifetime){
					// check that match the random 5-digit code (10 chars in HEX)
					if ($_COOKIE['totp_'.$user_id]==$device[0]){ 
						// check that match the HTTP_USER_AGENT
						if ($_SERVER['HTTP_USER_AGENT']==$device[2]){
							$b_secured_device = true;
						}
					}
				// expired device
				}else{
					unset($devices[$iid]);
					$b_expired_devices = true;
				}
			}
			// to clean expired devices for this user
			if ($b_expired_devices){
				dol_include_once("core/lib/admin.lib.php");
				dolibarr_set_const($this->db, 'TOTP_USER_'.$user_id, json_encode($devices), '', 0, '', $conf->entity);
			}
		}
		
		if ($b_secured_device){		
			$_SESSION['totp2fa_username'] = '';
			$_SESSION['totp2fa_password'] = '';
			$_SESSION['dol_loginmesg']    = '';
			return;
		}
			
		/* === validate 6-code digit === */

		$c6 = GETPOST('totp2fa','int');

		$valid = '0';
		$user_secret = '';
		if (!empty($user_id)){
			
			include_once(__DIR__."/totp.class.php");
			$user_secret = Totp::getUserSecret($user_id);
			if (empty($user_secret)){
				$valid = '1';
			}else{
				$valid = Totp::validate_sk($user_secret,$c6);
			}
		}
		
		$_SESSION['totp2fa_username'] = '';
		$_SESSION['totp2fa_password'] = '';
		$_SESSION['dol_loginmesg']    = '';
		
		if ($valid!='1'){
		
			/* close user session & return to login */
			$this->db->rollback();
			session_reset();
			
			$_SESSION['totp2fa_username'] = GETPOST('username');
			$_SESSION['totp2fa_password'] = GETPOST('password');
			$_SESSION['dol_loginmesg']    = $langs->trans('totp2fa_WrongCode');

			header('Location: '.DOL_URL_ROOT.'/index.php');
			exit;

		}

		/* === status: valid TOTP, so we set the cookie of TEMPORALLY secured device === */
		
		$random_bytes = bin2hex(random_bytes(8));
		$devices[] = array($random_bytes, $time_now, $_SERVER['HTTP_USER_AGENT']);
		//dol_syslog('---- new devices: '.var_export($devices,true),3);
		
		dol_include_once("core/lib/admin.lib.php");
		dolibarr_set_const($this->db, 'TOTP_USER_'.$user_id, json_encode($devices), '', 0, '', $conf->entity);
		
		if (PHP_VERSION_ID < 70300) {
			setcookie('totp_'.$user_id,$random_bytes,($time_now + $period_seconds[$sett_period]),'/; samesite=strict',$_SERVER['HTTP_HOST'], false, true);
		}else{
			setcookie('totp_'.$user_id,$random_bytes,array (
                'expires' => ($time_now + $period_seconds[$sett_period]),
                'path' => '/',
                'domain' => $_SERVER['HTTP_HOST'],
				'secure' => false, // if you only want to receive the cookie over HTTPS
				'httponly' => true, // prevent JavaScript access to session cookie
 				'samesite' => 'strict'
			));
		}

		/* === message to user: reporting the time of expiration of the remembered device  === */
		
		if ($sett_period!='0'){
			$msg1 = str_replace('{period}','<b>'.$langs->trans('totp2fa_Config09_opt_'.$sett_period).'</b>',$langs->trans('totp2fa_Config09_msg1'));
			setEventMessage($msg1,'warnings');
			setEventMessage($langs->trans('totp2fa_Config09_msg2'),'warnings');
		}
	}	
		
	/*
	 * context: usercard
	 * mission: to replace the <input type=text> control forthe totp2fa_secret field with an INTERACTIVE (ajax) control to:
	 * 	1. enable/disable TOTP for this user
	 * 	2. retrieve/set the secret using a QR image
	 */
	function formObjectOptions( $parameters, &$object, &$action, $hookmanager ){

		global $user, $langs;

		$user_id = $object->id;
		$caneditpassword = (   (($user->id == $user_id) && $user->rights->user->self->password)
							|| (($user->id != $user_id) && $user->rights->user->user->password));

		// decrypt secret 
			$user_secret = !empty($object->array_options['options_totp2fa_secret']) ? $object->array_options['options_totp2fa_secret'] : '';
			include_once(__DIR__."/totp.class.php");
			$user_secret = empty($user_secret) ? '' : Totp::decrypt($user_secret);

		if ($action=='edit'){
			if ($user_secret != ''){
				if ($caneditpassword){
					if ($user->id != $user_id) $user_secret = substr($user_secret,0,4).'************';
					$after = '<span id=\'span_totp2fa_secret\' style=\'font-family:monospace;\'>'.$user_secret.' &nbsp;<i class=\'fa fa-check-circle\'></i> &nbsp;</span>'
								.'<a href=\'#\' id=\'button_delete_totp2fa_secret\' class=\'button\' onclick=\'js_delete_2FA_sk();return false;\'>'
								.'<i class=\'fa fa-times-circle-o\'></i>&nbsp; '.$langs->trans('totp2fa_Disable').'</a>';
				}else{
					$after = '<i class=\'fa fa-lg fa-check-circle\'></i>&nbsp; <span style=\'font-family:monospace;\'>'.substr($user_secret,0,4).'************</span>';
				}
			}else{
				$after = '<span class=\'opacitymedium\'><i class=\'fa fa-minus-circle\'></i> '.$langs->trans('totp2fa_NotYet').'</span>';
			}
			
			$this->resprints = '<script>
									$(document).ready(function(){
									
										/* -- when this user DOES HAS permission to EDIT HIS USER CARD -- */
										
										if ($("#options_totp2fa_secret").length){ 
											$("#options_totp2fa_secret")
													.attr("readonly","1")
													.attr("type","hidden")
													.after("'.$after.'");
													
										/* -- when this user DOES NOT HAS permission to EDIT HIS USER CARD -- */
										
										}else if ($("#user_extras_totp2fa_secret_2").length){  
											$("#user_extras_totp2fa_secret_2")
													.html("'.$after.'<br /><span class=\'opacitymedium\'>'.str_replace("'","\'",$langs->trans('totp2fa_NeedPerms')).'</span>");
											$("#button_delete_totp2fa_secret").hide();
										}
									});
									function js_delete_2FA_sk(){
										$("#options_totp2fa_secret").val("");
										$("#button_delete_totp2fa_secret").hide();
										$("#span_totp2fa_secret").html("<i class=\'fa fa-minus-circle\'></i> '.str_replace("'","\'",$langs->trans('totp2fa_SaveChanges')).'");
										$("#button_delete_totp2fa_secret").closest("form").submit();
									}
								</script>';
											
		}else{

			if (($user->id == $user_id) && $user->rights->user->self->password && $user->rights->user->self->creer){
				
					$this->resprints = '<div id="totp2fa_hidden_temp_div" style="display:none;">
										'._render_view('user_edit',array('user_secret'=>$user_secret)).'
										</div>
										<script>
											$(document).ready(function(){
												var temp_html = $("#totp2fa_hidden_temp_div").html();
												$("#user_extras_totp2fa_secret_'.$user_id.'").html(temp_html);
												$("#totp2fa_hidden_temp_div").html("");
												var user_secret = "'.trim($user_secret).'";
												if (user_secret==""){
													js_totp2fa_show_dialog("disabled");
												}else{
													js_totp2fa_show_dialog("enabled");
												}
											});
										</script>';
								
			}else{
				
					if ($user_secret != ''){
						$replace = '<i class=\'fa fa-lg fa-check-circle\'></i>&nbsp; <span style=\'font-family:monospace;\'>'.substr($user_secret,0,4).'************</span>';
					}else{
						$replace = '<span class=\'opacitymedium\'><i class=\'fa fa-minus-circle\'></i> '.$langs->trans('totp2fa_NotYet').'</span>';
						if (($user->id == $user_id) && (!$user->rights->user->self->password || !$user->rights->user->self->creer)){
							$replace .= '<br /><br /><span class=\'opacitymedium\'><i class=\'fa fa-warning\'></i> '.$langs->trans('totp2fa_NeedPerms').'</span>';
						}
					}
					
					$this->resprints = '<div id="totp2fa_hidden_temp_div" style="display:none;">
											'.$replace.'
										</div>
										<script>
											$(document).ready(function(){
												var temp_html = $("#totp2fa_hidden_temp_div").html();
												$("#user_extras_totp2fa_secret_'.$user_id.'").html(temp_html);
												$("#totp2fa_hidden_temp_div").html("");
											});
										</script>';
				
			}
		}

										
	}
	
	/*
	 * context: userlist
	 * mission: to replace the column of the TOTP 2FA secret, to mask it (more readable)
	 */
	function printFieldListFooter( $parameters, &$object, &$action, $hookmanager ){
		if (intval(DOL_VERSION) <= 8){			
			$this->resprints = '
						<script>
							$(document).ready(function(){
								/* for this versions of Dolibarr we dont have an attribute data-key marking the TD of this module so we must guess by the length */
								$("table.tagtable.liste tbody td").each(function(){
									var td_content = $(this).html();
									if (td_content!=""){
										var arr_td_content = td_content.split(" ");
										if (arr_td_content.length > 0){
											var very_long_string = false;
											$.each(arr_td_content,function(k,v){
												if (v.length > 80) very_long_string = true;
											});
											if (very_long_string){
												$(this).html("<div style=\"text-align:center;\"><i class=\"fa fa-check-circle\"></i></div>");
											}
										}
									}
								});
							});
						</script>
			';
		}else{
			$this->resprints = '
						<script>
							$(document).ready(function(){
								$("td[data-key=\'user.totp2fa_secret\'] , td[data-key=totp2fa_secret]").each(function(){
									var secret = $(this).html();
									if (secret!="") $(this).html("<div style=\"text-align:center;\"><i class=\"fa fa-check-circle\"></i></div>");
								});
							});
						</script>
			';
		}
	}	
	
}



