<?php

include_once(__DIR__."/../lib/2FA_RobThree/loader.php");
Loader::register('.','RobThree\\Auth');

class Totp
{

    public function __construct(){}
    public function __destruct(){}

    /*
     * returns 	$secret: "ZWXTJHLLRERXSVZR" 
     * 			$qr_img: "image/png;base64,iVBORw0KGgoAAAANSU..."
     */
    static function generate_sk(){
        global $config_site;
		$qr_name = $_SERVER['HTTP_HOST'];
        $tfa = new RobThree\Auth\TwoFactorAuth($qr_name);
        $secret = $tfa->createSecret();
        $qr_img = $tfa->getQRCodeImageAsDataUri($qr_name, $secret);
        return array($secret,$qr_img);
    }

    static function set_sk($secret){
        global $config_site;
		$qr_name = $_SERVER['HTTP_HOST'];
        $tfa = new RobThree\Auth\TwoFactorAuth($qr_name);
        $qr_img = $tfa->getQRCodeImageAsDataUri($qr_name, $secret);
        return array($secret,$qr_img);
    }

    static function validate_sk($sk,$c6){
        global $config_site;
		$qr_name = $_SERVER['HTTP_HOST'];
        $tfa = new RobThree\Auth\TwoFactorAuth($qr_name);
        $result = $tfa->verifyCode($sk, trim($c6));
        if ($result===true)
            return '1';
        else
            return '0';
    }

    static function get_qr_image($secret){
        global $config_site;
		$qr_name = $_SERVER['HTTP_HOST'];
        $tfa = new RobThree\Auth\TwoFactorAuth($qr_name);
        $qr_img = $tfa->getQRCodeImageAsDataUri($qr_name, $secret);
        return $qr_img;
    }
    
    static function encrypt($text){
		//return $text;
		$key = 'Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua.';
        $cipher='aes-256-cbc';
        $iv = openssl_random_pseudo_bytes(openssl_cipher_iv_length($cipher));
        $encrypted = openssl_encrypt($text, $cipher, $key, 0, $iv);
        return base64_encode($encrypted.'::'.bin2hex($iv));
	}
	
    static function decrypt($text){
		//return $text;
		$key = 'Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua.';
        $cipher='aes-256-cbc';
        $a_text = explode('::',base64_decode($text));
        if (count($a_text)!=2) return false;
        $encrypted = $a_text[0];
        $iv = hex2bin($a_text[1]);
        $decrypted = openssl_decrypt($encrypted, $cipher, $key, 0, $iv);
        return $decrypted;
	}
	
	static function setUserSecret($user_id,$secret){
		global $db;
		
		$encrypted_secret = self::encrypt($secret);
		
		// check if it already exist an user_extrafields record for this user
		$sql   = "SELECT * FROM ".MAIN_DB_PREFIX."user_extrafields WHERE fk_object='".$user_id."';";
		$resql = $db->query($sql);
		
		if ($db->num_rows($resql)){
			$sql = "UPDATE ".MAIN_DB_PREFIX."user_extrafields SET totp2fa_secret='".$encrypted_secret."' WHERE fk_object='".$user_id."';";
		}else{
			$sql = "INSERT ".MAIN_DB_PREFIX."user_extrafields SET totp2fa_secret='".$encrypted_secret."', fk_object='".$user_id."';";
		}
		
		// run SQL
		$error = '';
		$db->begin();
		$resql = $db->query($sql);
		if (! $resql) { 
			$error = "Error ".$db->lasterror(); 
		}
		
		// Commit or rollback
		if (!empty($error)) {
			dol_syslog("Totp2fa save user secret error: ".$error, LOG_ERR);
			$db->rollback();
		}else{
			$db->commit();
		}
		
		return $error;
	}

	static function getUserSecret($user_id){
		global $db;
		
		// check if it already exist an user_extrafields record for this user
		$sql   = "SELECT * FROM ".MAIN_DB_PREFIX."user_extrafields WHERE fk_object='".$user_id."';";
		$resql = $db->query($sql);
		
		if ($db->num_rows($resql)){
			$row = $resql->fetch_assoc();
			if (empty($row['totp2fa_secret']) || is_null($row['totp2fa_secret'])){
				return '';
			}else{
				return self::decrypt($row['totp2fa_secret']);
			}
		}else{
			return '';
		}
		
	}

}
