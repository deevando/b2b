<?php
			if (!empty($user_secret)){
				
				require_once TOTP2FA_MODULE_DOCUMENT_ROOT.'/class/totp.class.php';
				$qr_img = Totp::get_qr_image($user_secret);
				
			}else{
				$qr_img = '';
			}
?>

<!-- 
	= = = = = = = = = = = = = = = = = = = = = = 
		VIEW WHEN THE TOTP IS ENABLED
	= = = = = = = = = = = = = = = = = = = = = = 
-->


	<fieldset style="max-width:500px;text-align:center;margin:1rem">
		<p><span class='data-clipboard' style='font-size:1.5em;font-family:monospace;'><?= $user_secret ?></span></p>
		<p><img class='data-qr' src="<?= $qr_img ?>" /></p>
	</fieldset>




