<?php

class Totp_IP
{

    public function __construct(){}
    public function __destruct(){}

    static function save_SETT_COUNTRIES($a_sett_countries){
        global $config_site, $db, $conf, $langs;

		$sett_countries = implode(',',$a_sett_countries);

		$db->begin();
		$result = dolibarr_set_const($db, "TOTP2FA_MODULE_SETT_COUNTRIES", $sett_countries , 'chaine', 0, '', $conf->entity);
		if (! $result > 0) {
			$db->rollback();
			dol_print_error($db);
			return false;
		} else {
			$db->commit();
			setEventMessages($langs->trans("SetupSaved"), null, 'mesgs');
			$conf->global->TOTP2FA_MODULE_SETT_COUNTRIES = $sett_countries;
			return true;
		}
    }

	/*
	* it requires: apt install php7.4-geoip
	* param $ip can be an IP or a domain name!! :-o (like 'imasdeweb.com')
	*/
	static function ip2country($ip=''){
		if (!function_exists('geoip_country_code_by_name')) return '';
		if ($ip=='') $ip = $_SERVER['REMOTE_ADDR'];
		if (substr($ip,0,6)=='127.0.'){
			return '';
		}else{
			$country_code = geoip_country_code_by_name($ip);
			if (!$country_code || $country_code===false){
				return '';
			}else{
				return $country_code;
			}
		}
	}

}
