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

/***************************************************
 *
 *	Actions / prepare data
 *
****************************************************/

		include_once(__DIR__."/../lib/functions.lib.php");

    // == request action by GET/POST

        if (!empty($_POST['config'])){

            /* save incoming data */
                $db->begin();
                $error = 0;

				foreach ($_POST['config'] as $K => $v){
					$result = dolibarr_set_const($db, $K, $v, 'chaine', 0, '', $conf->entity);
					if (! $result > 0) $error ++;
				}

            /* message to user */
            	if (! $error) {
            		$db->commit();
            		setEventMessages($langs->trans("SetupSaved"), null, 'mesgs');
            	} else {
            		$db->rollback();
            		dol_print_error($db);
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

    dol_fiche_head($head, 'tabconfig-totp', '',-1,'');
    
?>
<form id="totp2faForm" name="totp2faForm" action="<?= $_SERVER["PHP_SELF"] ?>" method="post">

	<div style='padding:0 1.5em;'>
		<p><?= $langs->trans('totp2fa_Config01') ?></p>
		<ul>
			<li><?= $langs->trans('totp2fa_Config01A') ?><br /><br /></li>
			<li><?= $langs->trans('totp2fa_Config01B') ?></li>
		</ul>
		<p>&nbsp;</p>
		<p><?= $langs->trans('totp2fa_Config02') ?></p>
		<ul>
			<li><a href="https://authy.com/download/" target="_blank">Authy</a> (Android, iOS, web, macOS, Windows, Linux)</li>
			<li><a href="https://getaegis.app/" target="_blank">Aegis</a> (Android)</li>
			<li><a href="https://support.google.com/accounts/answer/1066447" target="_blank">Google Authenticator</a> (Android, iOS)</li>
		</ul>
	</div>

    <!-- SETTINGS -->
    
    <?= load_fiche_titre($langs->trans("totp2fa_Config08"),'','') ?>
    
    <table class="noborder" style="width:auto;min-width:60%;">
        <tr class="liste_titre">
            <td width="50%"><?= $langs->trans("Name") ?></td>
            <td width="50%"><?= $langs->trans("Value") ?></td>
        </tr>
        
        <!-- number of decimals for stock quantities -->
        <tr>
            <td><?= $langs->trans("totp2fa_Config09") ?></td>
            <td>
                <?php $sett_period = !empty($conf->global->TOTP2FA_MODULE_SETT_REMEMBER_PERIOD) ? $conf->global->TOTP2FA_MODULE_SETT_REMEMBER_PERIOD : '0'; ?> 
                <select name="config[TOTP2FA_MODULE_SETT_REMEMBER_PERIOD]">
                    <option value="0"  <?= $sett_period=='0'  ? "selected='selected'":"" ?>><?= $langs->trans("totp2fa_Config09_opt_0")  ?></option>
                    <option value="1d" <?= $sett_period=='1d' ? "selected='selected'":"" ?>><?= $langs->trans("totp2fa_Config09_opt_1d") ?></option>
                    <option value="1w" <?= $sett_period=='1w' ? "selected='selected'":"" ?>><?= $langs->trans("totp2fa_Config09_opt_1w") ?></option>
                    <option value="1m" <?= $sett_period=='1m' ? "selected='selected'":"" ?>><?= $langs->trans("totp2fa_Config09_opt_1m") ?></option>
                </select>
            </td>
        </tr>
    </table>

	<!-- SUBMIT button -->

	<p style="text-align:left;margin:2rem 0;">
		<a href="#" onclick="$('#totp2faForm').submit();return false;" class="button"><?= dol_escape_htmltag($langs->trans("Save")) ?></a>
	</p>

</form>

<?php 



dol_fiche_end();


clearstatcache();

dol_htmloutput_mesg($mesg);


llxFooter();

$db->close();
