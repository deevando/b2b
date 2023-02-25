 <!DOCTYPE html>
<html>
<head>
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
</head>
<body>
<?php
//include_once 'redsysHMAC256_API_PHP_4.0.2/apiRedsys.php';
							
if ($_POST['submitPayment']) {
    include "apiRedsys.php";  
    $miObj = new RedsysAPI;
    $amount = $_POST['amount'];    
    $numpedido = $_POST['numpedido'];
    
    
    //Entorno pruebas
    //$url_tpv = "https://sis-t.redsys.es:25443/sis/realizarPago";
    //$clave = "sq7HjrUOBfKmC576ILgskD5srU870gJ7";
    //$code = "999008881";
    //$terminal = "1";
    //tarjeta : 4548812049400004
   

    //Entorno Real
    $url_tpv = "https://sis.redsys.es/sis/realizarPago";
    $clave = "6vHjejdJQ/+GDWUnIpJLLLtSf2WmuEmB"; //poner la clave SHA-256 facilitada por el banco
    $code = "148500556";
    $terminal = "002";
    

    $version = "HMAC_SHA256_V1"; 
    $name = "DEEVANDO INFORMATICA";
    $order = date('dmyHis');
    //$order = "FCVE2023-00025";
    $amount = $amount * 100;
    $currency = "978";
    $consumerlng = "001";
    $transactionType = "0";
    $urlMerchant = "https://www.deevando.es/";
    $urlweb_ok = 'https://b2b.deevando.com/public/ticket/create_ticket.php?refpag='.$order;
    $urlweb_ko = 'https://b2b.deevando.com/public/ticket/index.php';
    $numpedido =  $numpedido;
 
    $miObj->setParameter("DS_MERCHANT_AMOUNT", $amount);
    $miObj->setParameter("DS_MERCHANT_CURRENCY", $currency);
    $miObj->setParameter("DS_MERCHANT_ORDER", $order);
    $miObj->setParameter("DS_MERCHANT_MERCHANTCODE", $code);
    $miObj->setParameter("DS_MERCHANT_TERMINAL", $terminal);
    $miObj->setParameter("DS_MERCHANT_TRANSACTIONTYPE", $transactionType);
    $miObj->setParameter("DS_MERCHANT_MERCHANTURL", $urlMerchant);
    $miObj->setParameter("DS_MERCHANT_URLOK", $urlweb_ok);      
    $miObj->setParameter("DS_MERCHANT_URLKO", $urlweb_ko);
    $miObj->setParameter("DS_MERCHANT_MERCHANTNAME", $name); 
    $miObj->setParameter("DS_MERCHANT_CONSUMERLANGUAGE", $consumerlng);  
    $miObj->setParameter("DS_MERCHANT_PRODUCTDESCRIPTION", $numpedido);  
	//$miObj->setParameter("DS_MERCHANT_BIZUM_MOBILENUMBER": "+34717003409");
 
    $params = $miObj->createMerchantParameters();
    $signature = $miObj->createMerchantSignature($clave);
    ?>

  
    <form id="realizarPago" action="<?php echo $url_tpv; ?>" method="post">
        <input type="hidden" name="Ds_SignatureVersion" value="<?php echo $version; ?>"/><br> 
        <input type="hidden" name="Ds_MerchantParameters" value="<?php echo $params; ?>"/></br> 
        <input type="hidden" name="Ds_Signature" value="<?php echo $signature; ?>"/></br> 
    </form>
    <script>
    $(document).ready(function(){
        $('#realizarPago').submit();
    });
    </script>

<?php
} else { 

echo "PASARELA DE PAGO - Compruebe el importe y el número de pedido.";
$amount = $_GET['importe'];
$numpedido = $_GET['pedido'];

?>

<form class="form-amount" action="pagotpv15min.php" method="post">
    <div class="form-group">
       <!-- <label for="amount">Cantidad</label> -->
        <input type="hidden" id="amount" name="amount" class="form-control" placeholder="Por ejemplo: 50.00" value="20.00";>
        <BR>
       <!-- <label for="order">Pedido</label> -->
        <input type="hidden" id="pedido" name="numpedido" class="form-control" placeholder="Por ejemplo: 123443" value="FCVE2023-00025 - REMOTO EXPRESS 15 Min";>
    </div>
    <br>
    <input class="btn btn-lg btn-primary btn-block" name="submitPayment" type="submit" value="Pagar">
</form>

<?php
}
?> 

</body>
</html>