<?php

class ActionsConvertprice {
	
	function doActions($parameters, $object, $action) {
        global $conf, $inputalsopricewithtax;

        $inputalsopricewithtax = 1;
        $conf->global->MAIN_FEATURES_LEVEL = 2;

        return 0;
    }

    function formObjectOptions($parameters, $object, $action) {
        global $conf, $db, $langs, $user;
        return $this->convertPrice($parameters, $object, $action);
    }

    function formAddObjectLine($parameters, $object, $action) {
        global $conf, $db, $langs, $user;

        return $this->convertPrice($parameters, $object, $action);
    }

    function convertPrice($parameters, $object, $action) {
        global $conf, $db, $langs, $user;

        $element = $object->element;

        // TODO uniformiser
        if ($element == 'propal')
            $element = 'propale';

        if ($element == 'propale' || $element == 'commande' || $element == 'facture') {
            if ($action != 'create') {
				print '
						<script>
							jQuery(document).ready(function() {
								jQuery("#price_ttc").change(function() {
									var tva = $("#tva_tx").val();
									if (tva == 0) jQuery("#price_ht").val($(this).val());
									else {
										var calc_arrondi = ' . ($conf->global->MAIN_MAX_DECIMALS_UNIT ? $conf->global->MAIN_MAX_DECIMALS_UNIT : 5) . ' * 100;
										jQuery("#price_ht").val(Math.round($(this).val() / (1 + tva/100) * calc_arrondi) / calc_arrondi);
									}
								});
                                jQuery("#tva_tx").change(function() {
									if (jQuery(this).val() == 0) jQuery("#price_ht").val(jQuery("#price_ttc").val());
									else {
										var calc_arrondi = ' . ($conf->global->MAIN_MAX_DECIMALS_UNIT ? $conf->global->MAIN_MAX_DECIMALS_UNIT : 5) . ' * 100;
										jQuery("#price_ht").val(Math.round(parseFloat(jQuery("#price_ttc").val()) / (1 + jQuery(this).val()/100) * calc_arrondi)/calc_arrondi);
									}
								});
								jQuery("#price_ht").change(function() {
									var tva = $("#tva_tx").val();
									if (tva == 0) jQuery("#price_ttc").val($(this).val());
									else {
										var calc_arrondi = ' . ($conf->global->MAIN_MAX_DECIMALS_UNIT ? $conf->global->MAIN_MAX_DECIMALS_UNIT : 5) . ' * 100;
										jQuery("#price_ttc").val(Math.round($(this).val() * (1 + tva/100) * calc_arrondi) / calc_arrondi);
									}
								});
							});
						</script>';
				return 0;
            }
        }
    }

}