<?php
		// == MODULE DOCUMENT_ROOT & URL_ROOT
			if (file_exists(DOL_DOCUMENT_ROOT.'/custom/totp2fa/core/modules/modTotp2fa.class.php')){
				define('TOTP2FA_MODULE_DOCUMENT_ROOT',DOL_DOCUMENT_ROOT.'/custom/totp2fa');
				define('TOTP2FA_MODULE_URL_ROOT',DOL_URL_ROOT.'/custom/totp2fa');
			}else{
				define('TOTP2FA_MODULE_DOCUMENT_ROOT',DOL_DOCUMENT_ROOT.'/totp2fa');
				define('TOTP2FA_MODULE_URL_ROOT',DOL_URL_ROOT.'/totp2fa');
			}
		global $user;
		$user_id = GETPOST("id");

		/*
		 user policy:
		 * only the user A can ENABLE the 2FA for user A
		 * only the user A can DISABLE the 2FA for user A, and aswell a user of role admin
		 * only the user A can SEE the secret and the QR
		 */
?>

<div id="totp2fa_disabled" style="display:none;">
	<?php 
			if ($user_id == $user->id){
				echo _render_view('user_edit_disabled',array('user_secret'=>$user_secret,'user_id'=>$user_id));
			}else{ 
				echo '<i class="fa fa-lg fa-minus-o"></i>';
			} 
	?>
</div>

<div id="totp2fa_enabled" style="display:none;">
	<?php 
			if ($user_id == $user->id){
				echo _render_view('user_edit_enabled',array('user_secret'=>$user_secret,'user_id'=>$user_id));
			}else{ 
				echo '<i class="fa fa-lg fa-check-circle"></i>&nbsp; <span style="font-family:monospace;">'.substr($user_secret,0,4).'************</span>';
			} 
	?>
</div>

<script>
	function js_totp2fa_show_dialog(state){
		$('#totp2fa_enabled').hide();
		$('#totp2fa_disabled').hide();
		$('#totp2fa_'+state).show();
	}
</script>
