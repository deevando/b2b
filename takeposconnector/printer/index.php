<?php
header('Access-Control-Allow-Origin: *');
$data=$_POST["invoice"];
$data=base64_decode(str_pad(strtr($data, '-_', '+/'), strlen($data) % 4, '=', STR_PAD_RIGHT));
if (empty($_GET["printer"])) $nprinter=1;
else $nprinter=$_GET["printer"];
echo "Printer number: ".$nprinter."<br>";
$file = fopen("invoice".$nprinter.".txt", "w") or die("Unable to open file!");
fwrite($file, $data);
fclose($file);
$printer = file_get_contents('../printer'.$nprinter.'.ini', true);
exec('java -jar Print.jar "'.$printer.'" '.$nprinter,$output);
print_r( $output);
echo 'java -jar Print.jar "'.$printer.'" '.$nprinter;
