
<!-- 
	= = = = = = = = = = = = = = = = = = = = = = 
		VIEW WHEN THE TOTP IS EMPTY/DISABLED 
	= = = = = = = = = = = = = = = = = = = = = = 
-->

<a href="#" id="totp2fa_enable_button" class="button" onclick="js_generate_2FA_sk();return false;">
	<i class="fa fa-qrcode"></i>&nbsp; <?= $langs->trans('totp2fa_Enable') ?>
</a>

<div id="2FA_sk" style="display:none;">
	
	<fieldset style="max-width:500px;text-align:center;margin:1rem">
		
		<p id='2FA_show_sk' style="white-space:nowrap;">
			<span class='data-clipboard' style='font-size:1.5em;'></span>
			&nbsp; <a href="#" id="totp2fa_enable_button" class="button" onclick="js_edit_2FA_sk();return false;"><i class="fa fa-pencil"></i></a>
		</p>
		
		<p id='2FA_edit_sk_p' style="white-space:nowrap;display:none;">
			<input type='text' id='2FA_edit_sk' style='width:12em;font-size:1.5em;' 
				placeholder='EFV2GDPD7...'
				onfocus='$(this).removeClass("alerted_field");'
				onkeypress="if(getKeyCode(event)==13){js_set_2FA_sk();return false;}" />
			&nbsp; <a href="#" id="totp2fa_validate_sk" class="button" onclick="js_set_2FA_sk();return false;"><i class="fa fa-qrcode"></i></a>
		</p>
		<script>
		</script>
		<p id='2FA_show_qr' style='display:none;'><img class='data-qr' src="" /></p>

		<!-- hidden dialog to validate new code -->

		<p id="totp2fa_enable_desc" class="opacitymedium"><?= $langs->trans('totp2fa_Instructions') ?></p>
		<p id="totp2fa_validation">
			<input type='text' id='first_6code' value='' autocomplete='off' style='width:6rem;'
				placeholder='123456'
				onfocus='$(this).removeClass("alerted_field");'
				onkeypress="if(getKeyCode(event)==13){js_validate_2FA_sk();return false;}" />
			<a href="#" class="button" onclick="js_validate_2FA_sk();return false;">
				<i class="fa fa-check-circle"></i>&nbsp; <?= $langs->trans('totp2fa_Validate') ?>
			</a>
		</p>
		<p id="totp2fa_error_validation"   style="display:none;color:red;"><?= $langs->trans('totp2fa_TryAgain') ?></p>
		<p id="totp2fa_success_validation" style="display:none;color:green;"><i class="fa fa-4x fa-check-circle"></i></p>
		
	</fieldset>
	
	<style>.alerted_field{border-color:red;}</style>
	
</div>


<script>
	
	function getKeyCode(e){
               e= (window.event)? event : e;
               intKey = (e.keyCode)? e.keyCode: e.charCode;
               return intKey;
	}	

	function js_edit_2FA_sk(){
		$('#2FA_show_sk, #2FA_show_qr, #totp2fa_enable_desc, #totp2fa_validation, #totp2fa_error_validation, #totp2fa_success_validation').hide();
		$('#2FA_edit_sk').val($('#2FA_sk .data-clipboard').html()).removeClass('alerted_field').focus();
		$('#2FA_edit_sk_p').show();
		$('#2FA_edit_sk').focus().select();
	}

	function js_generate_2FA_sk(){
		// reset controls
		$('body').css('cursor', 'wait');
		$('#first_6code').val('');
		$('#2FA_edit_sk_p').hide();
		$('#totp2fa_error_validation, #totp2fa_success_validation').hide();
		$('#2FA_show_sk, #2FA_show_qr, #totp2fa_enable_desc, #totp2fa_validation').show();
		
		// do JSON call to server
		var url_json = '<?= TOTP2FA_MODULE_URL_ROOT ?>/json.php?op=generate_2FA_sk';
		$.getJSON(
			url_json,
			function(data){
				$('body').css('cursor', 'default');
				$('#2FA_sk .data-clipboard').html(data.sk);
				$('#2FA_show_qr img').attr('src',data.qr_img);
				$('#2FA_show_qr').show();
				$('#2FA_sk').slideDown();
			}
		);
	}

	function js_set_2FA_sk(){
		
		// reset alerts/styles
		$('#totp2fa_error_validation').hide();
		$('#2FA_edit_sk').removeClass('alerted_field');
		
		// validate secret format
		var sk = $('#2FA_edit_sk').val();
		if (! js_is_base32(sk)){
			$('#2FA_edit_sk').addClass('alerted_field');
			$('#totp2fa_error_validation').fadeIn();
			return;
		}
		
		// reset controls
		$('body').css('cursor', 'wait');
		
		// do JSON call to server
		var url_json = '<?= TOTP2FA_MODULE_URL_ROOT ?>/json.php?op=set_2FA_sk';
		$.post(
			url_json,
			{'sk':sk,'token':'<?= isset($_SESSION['token']) ? $_SESSION['token']:'' ?>'},
			function(data){
				$('body').css('cursor', 'default');
				if (data.sk!=''){
					$('#2FA_sk .data-clipboard').html(data.sk);
					$('#2FA_show_qr img').attr('src',data.qr_img);
					$('#2FA_show_qr').show();
					$('#2FA_show_qr, #totp2fa_enable_desc, #totp2fa_validation').show();
					$('#first_6code').focus();
				}else{
					$('#totp2fa_error_validation').fadeIn();
				}
			}
			,'json'
		);
	}

	function js_validate_2FA_sk(){
		var url_json = '<?= TOTP2FA_MODULE_URL_ROOT ?>/json.php?op=validate_2FA_sk';
		var first_6code = $('#first_6code').val();
		if (first_6code=='') {
			$('#first_6code').addClass('alerted_field');
			return;
		}
		$('body').css('cursor', 'wait');
		var user_id = '<?= $user_id ?>';
		var hashed_user_id = '<?= md5('1441hashkey'.$user_id) ?>';
		$.post(
			url_json,
			{id:user_id,hid:hashed_user_id,c6:first_6code,'token':'<?= isset($_SESSION['token']) ? $_SESSION['token']:'' ?>'},
			function(data){
				$('body').css('cursor', 'default');
				$('#totp2fa_error_validation').hide();
				$('#totp2fa_success_validation').hide();
				if (data.ok && data.ok=='1'){
					$('#totp2fa_enable_button').hide();
					$('#totp2fa_enable_desc').hide();
					$('#totp2fa_validation').hide();
					$('#totp2fa_success_validation').fadeIn();
					$('#2FA_edit_sk_p').hide();
				}else {
					$('#totp2fa_error_validation').fadeIn();
				}
				$('#2FA_sk').slideDown();
			},
			'json'
		);
	}
	
	function js_is_base32(secret) {
		const regex = /^([A-Z2-7=]{8})+$/
		return regex.test(secret)
	}
</script>


