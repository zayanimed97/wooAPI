<?php 
include(__DIR__ ."/woocommerce.php");
//RESTAPI PARAMS : API URI , METHOD, DATA(optional), KEY(optional), SECRET(optional)
$list_product = json_decode(RESTAPI("/wp-json/wc/v3/products", "GET"));

//$itemcode=7000017;//$one['id']; // !!!!!!

$ch = curl_init();

// set url
curl_setopt($ch, CURLOPT_URL, "http://emmam.wingssoft.com/api/getallitems");

//return the transfer as a string
curl_setopt($ch, CURLOPT_FAILONERROR,1);
curl_setopt($ch, CURLOPT_FOLLOWLOCATION,1);
curl_setopt($ch, CURLOPT_RETURNTRANSFER,1);

// $inventory contains the inventory string
$inventory = curl_exec($ch);
$inventory = json_decode($inventory);

// close curl resource to free up system resources
curl_close($ch);   
foreach($list_product as $one){

	$sku = '';
	$prod = array_filter($inventory, function($el) use ($one, &$sku){
		$sku = $one->id;
        return $el->strSmItemNo == $one->sku;
    });

    if (!$prod) {
    	$restapi = RESTAPI("/wp-json/wc/v3/products/$sku", "PUT", ["manage_stock" => true,"stock_quantity"=> 0, "_stock"=>0, "stock_status"=>"outofstock","_stock_status"=>"outofstock"]);
    	continue;
    }
    if ($prod) {
    	$prod = reset($prod);
    }

    if (is_object($prod)) {
	    $stock = is_null($prod->dblBalnce) || $prod->dblBalnce < 1 ? "outofstock" : "instock";
	    $restapi = RESTAPI("/wp-json/wc/v3/products/$sku", "PUT", ["manage_stock" => true,"stock_quantity"=> $prod->dblBalnce,"_stock"=>$prod->dblBalnce, "stock_status"=>$stock,"_stock_status"=>$stock]);
	//	echo '<pre>';
	//    print_r(json_encode($restapi));
    }
	

}
?>