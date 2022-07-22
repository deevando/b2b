<?php
/* Copyright (C) 2020 Sergi Rodrigues <proyectos@imasdeweb.com>
 *
 * Licensed under the GNU GPL v3 or higher (See file gpl-3.0.html)
 *
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program. If not, see <http://www.gnu.org/licenses/>.
 * or see http://www.gnu.org/
 */

// == ACTIVATE the ERROR reporting
ini_set('display_errors',1);ini_set('display_startup_errors',1);error_reporting(-1);

$res=0;
if (! $res && file_exists("../main.inc.php")) $res=@include("../main.inc.php");
if (! $res && file_exists("../../main.inc.php")) $res=@include("../../main.inc.php");
if (! $res && file_exists("../../../main.inc.php")) $res=@include("../../../main.inc.php");
if (! $res && file_exists("../../../../main.inc.php")) $res=@include("../../../../main.inc.php");
if (! $res && preg_match('/\/imasdeweb([^\/]*)\//',$_SERVER["PHP_SELF"],$reg)) $res=@include("../../../dolibarr".$reg[1]."/htdocs/main.inc.php"); // Used on dev env only
if (! $res) die("Include of main fails");

// == MODULE DOCUMENT_ROOT & URL_ROOT
    if (file_exists(DOL_DOCUMENT_ROOT.'/custom/totp2fa/core/modules/modTotp2fa.class.php')){
        define('TOTP2FA_MODULE_DOCUMENT_ROOT',DOL_DOCUMENT_ROOT.'/custom/totp2fa');
        define('TOTP2FA_MODULE_URL_ROOT',DOL_URL_ROOT.'/custom/totp2fa');
    }else{
        define('TOTP2FA_MODULE_DOCUMENT_ROOT',DOL_DOCUMENT_ROOT.'/totp2fa');
        define('TOTP2FA_MODULE_URL_ROOT',DOL_URL_ROOT.'/totp2fa');
    }

	require_once TOTP2FA_MODULE_DOCUMENT_ROOT.'/class/totp.class.php';

global $user;			
$user->getrights('totp2fa');

$langs->load("totp2fa@totp2fa");

// == Get parameters
    $op = GETPOST('op','alpha');
    
    switch ($op){
		
        case 'generate_2FA_sk':
        
			list($secret,$qr_img) = Totp::generate_sk();
			$_SESSION['totp2fa_sk'] = $secret; // to be validated in the next user pass with a first 6-digit code
            echo json_encode(array('ok'=>'1','sk'=>$secret,'qr_img'=>$qr_img));
            
            break;

        case 'set_2FA_sk':
        
			list($secret,$qr_img) = Totp::set_sk($_POST['sk']); // if the $_POST['sk'] is a valid secret, this function returns the same secret and the QR image, if not it returns empty secret
			$_SESSION['totp2fa_sk'] = $secret; // to be validated in the next user pass with a first 6-digit code
            echo json_encode(array('ok'=>'1','sk'=>$secret,'qr_img'=>$qr_img));
            
            break;

        case 'validate_2FA_sk':
        
			$id        = GETPOST('id');
			$hashed_id = GETPOST('hid');
			$c6        = GETPOST('c6');
			if ($hashed_id != md5('1441hashkey'.$id)){
				echo json_encode(array('ok'=>'0'));
				break;
			}
				
			// security check
			$cancreateselfpassword = ($user->id == $id) && $user->rights->user->self->password;
			if (!$cancreateselfpassword){
				echo json_encode(array('ok'=>'0','msg'=>'No permisssion.'));
				break;
			}
				
			$sk = $_SESSION['totp2fa_sk'];
			$valid = Totp::validate_sk($sk,$c6);
			
			if ($valid=='1'){
				$error = Totp::setUserSecret($id,$sk);
				if (!empty($error)) $valid = '0';
			}
			
            echo json_encode(array('ok'=>$valid));
            
            break;
            
    }
    die();
