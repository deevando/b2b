<?php
/* Copyright (C) 2017 Sergi Rodrigues <proyectos@imasdeweb.com>
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
//ini_set('display_errors',1);ini_set('display_startup_errors',1);error_reporting(-1);

define('NOCSRFCHECK',1);

$res=0;
if (! $res && file_exists("../main.inc.php")) $res=@include("../main.inc.php");
if (! $res && file_exists("../../main.inc.php")) $res=@include("../../main.inc.php");
if (! $res && file_exists("../../../main.inc.php")) $res=@include("../../../main.inc.php");
if (! $res && file_exists("../../../../main.inc.php")) $res=@include("../../../../main.inc.php");
if (! $res && preg_match('/\/imasdeweb([^\/]*)\//',$_SERVER["PHP_SELF"],$reg)) $res=@include("../../../../dolibarr".$reg[1]."/htdocs/main.inc.php"); // Used on dev env only
if (! $res) die("Include of main fails");

// == MODULE DOCUMENT_ROOT & URL_ROOT
	if (file_exists(DOL_DOCUMENT_ROOT.'/custom/totp2fa/core/modules/modTotp2fa.class.php')){
		define('TOTP2FA_MODULE_DOCUMENT_ROOT',DOL_DOCUMENT_ROOT.'/custom/totp2fa');
		define('TOTP2FA_MODULE_URL_ROOT',DOL_URL_ROOT.'/custom/totp2fa');
	}else{
		define('TOTP2FA_MODULE_DOCUMENT_ROOT',DOL_DOCUMENT_ROOT.'/totp2fa');
		define('TOTP2FA_MODULE_URL_ROOT',DOL_URL_ROOT.'/totp2fa');
	}

dol_include_once("core/lib/admin.lib.php");
dol_include_once("core/class/html.formadmin.class.php");

if (!$user->admin) accessforbidden();

$langs->load("admin");
$langs->load("other");
$langs->load("totp2fa@totp2fa");
$langs->load("languages");
$langs->load("dict");

/***************************************************
 *
 *	Actions / prepare data
 *
****************************************************/

		include_once(__DIR__."/../lib/functions.lib.php");
		include_once(__DIR__."/../class/totp_ip.class.php");
		
		$b_geoip = function_exists('geoip_country_code_by_name') ? true : false;
		$sett_countries = !empty($conf->global->TOTP2FA_MODULE_SETT_COUNTRIES) ? $conf->global->TOTP2FA_MODULE_SETT_COUNTRIES : '';
		$a_sett_countries = $sett_countries!='' ? explode(',',$sett_countries) : array();

    // == request action by GET/POST
		//echo _var($_POST,'$_POST');die;
        if (!empty($_POST['remove_country_code'])){ // add a new country to filter by country-IP

				$remove_country_code = substr(mb_strtolower(trim($_POST['remove_country_code'])),0,2);
				if ($remove_country_code!=''){
					$new_a_sett_countries = array();
					foreach ($a_sett_countries as $code){
						if (!empty($code) && $code!=$remove_country_code){
							$new_a_sett_countries[] = $code;
						}
					}
					$a_sett_countries = $new_a_sett_countries;
					Totp_IP::save_SETT_COUNTRIES($a_sett_countries);
				}
				
        }else if (!empty($_POST['country_code'])){ // add a new country to filter by country-IP
			
				$new_country_code = substr(mb_strtolower(trim($_POST['country_code'])),0,2);
				if (!in_array($new_country_code,$a_sett_countries)){
					$a_sett_countries[] = $new_country_code;
					Totp_IP::save_SETT_COUNTRIES($a_sett_countries);
				}

        }

/***************************************************
 *
 *	View
 *
****************************************************/

$help_url='';
$title = $langs->trans('ModuleTotp2FaName');
llxHeader('',$title,$help_url);

// = first header row (section title & go back link)

    $linkback='<a href="'.DOL_URL_ROOT.'/admin/modules.php">'.$langs->trans("BackToModuleList").'</a>';
    print_fiche_titre($title,$linkback,'user');
    print '<br />';

// = tabs of the section

    $h=0;

    $head[$h][0] = 'config-totp.php';
    $head[$h][1] = $langs->trans("totp2fa_Config").' TOTP';
    $head[$h][2] = 'tabconfig-totp';
    $h++;

    $head[$h][0] = 'config-ip.php';
    $head[$h][1] = $langs->trans("totp2fa_Config").' IP';
    $head[$h][2] = 'tabconfig-ip';
    $h++;

    $head[$h][0] = 'about.php';
    $head[$h][1] = $langs->trans("totp2fa_About");
    $head[$h][2] = 'tababout';
    $h++;

    $head[$h][0] = 'changelog.php';
    $head[$h][1] = 'Changelog';
    $head[$h][2] = 'tabchangelog';
    $h++;

// = init current tab

    dol_fiche_head($head, 'tabconfig-ip', '',-1,'');
    
?>

<form id="totp2faForm" name="totp2faForm" action="<?= $_SERVER["PHP_SELF"] ?>" method="post">

    <!-- ** PDF GENERAL ** SETTINGS -->

    <?= load_fiche_titre($langs->trans("totp2fa_Config03"),'','') ?>

	<?php if (!$b_geoip){ ?>

		<p><i class="fa fa-lg fa-warning"></i> <?= str_replace('{library_name}','<u>php-geoip</u>',$langs->trans("totp2fa_Config04")) ?></p>

	<?php }else{

		include_once DOL_DOCUMENT_ROOT.'/core/class/html.form.class.php';
		$form = new Form($db);
	?>

		<table class="noborder" style="width:auto;min-width:70%;">
			<tr class="liste_titre">
				<td width="50%"><?= $langs->trans("Name") ?></td>
				<td width="50%"><?= $langs->trans("Value") ?></td>
			</tr>
			
			<!-- number of decimals for stock quantities -->
			<tr>
				<td><?= $langs->trans("totp2fa_Config05") ?></td>
				<td>
					<div id='totp2fa_contries' class='block' style='margin:1em;margin-left:0;'>
						<?php
							if (count($a_sett_countries)==0){
								echo "<em>".$langs->trans("totp2fa_Config06")."</em>";
							}else{
								foreach ($a_sett_countries as $code){
									echo "<a href='#' onclick=\"$('#remove_country_code').val('".$code."');$('#totp2faForm').submit();return false;\"><i class='fa fa-trash'></i> ".$langs->trans("Country".mb_strtoupper($code))."</a>";
								}
							}
						?>
					</div>
					<input type="hidden" name="remove_country_code" id="remove_country_code" value="" />
					<style>
						#totp2fa_contries a{background-color:rgba(200,200,200,0.5);color:black;display:inline-block;margin:10px;padding:5px 10px;border-radius:5px;font-weight:bold;}
						#totp2fa_contries a:hover{background-color:white;text-decoration:none;}
					</style>
					<?= $form->select_country('', 'country_code','', 0, 'maxwidth200','code2') ?>
					<button onclick="$('#totp2faForm').submit();return false;"><i class='fa fa-plus-square'></i> <?= $langs->trans("totp2fa_Config07") ?></button>
				</td>
			</tr>
		</table>

		<!-- SUBMIT button -->

		<p style="text-align:left;margin:2rem 0;">
			<a href="#" onclick="$('#totp2faForm').submit();return false;" class="button"><?= dol_escape_htmltag($langs->trans("Save")) ?></a>
		</p>

		
	<?php } ?>

</form>


<style>
	input.alertedfield, select.alertedfield, textarea.alertedfield{background-color:yellow!important;}
	.alertedcontainer td, .alertedcontainer td.fieldrequired{color:red!important;}
	.block{padding:0.5rem;background-color:rgba(100,100,100,0.05);border-radius:3px;border:1px rgba(100,100,100,0.2) solid;}
</style>


<?php 

dol_fiche_end();


clearstatcache();

dol_htmloutput_mesg($mesg);


llxFooter();

$db->close();
