<?php

//error_reporting(E_ALL);
//ini_set('display_errors', 1);

require_once __DIR__ . '/../../composer/vendor/autoload.php';

use Picqer\Barcode\BarcodeGeneratorHTML;

// 바코드 생성
$generator = new BarcodeGeneratorHTML();
echo $generator->getBarcode('123456789012123456789', $generator::TYPE_CODE_128);


//echo "<BR>".strtotime('2100-01-01');

echo "<BR>";




 
// UPDATE `wms_items` SET item_code = DATE_FORMAT(item_rdate, '%Y%m%d');



// UPDATE `wms_items` SET item_code =  item_code * 100000 + item_id
 
 
?>