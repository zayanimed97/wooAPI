<?php 
include("woocommerce.php");
$list_product = json_decode(RESTAPI("/wp-json/wc/v3/products", "GET"));
foreach($list_product as $one){
	
	$itemcode=7000017;//$one['id']; // !!!!!!
	
	$ch = curl_init();

	// set url
	curl_setopt($ch, CURLOPT_URL, "http://emmam.wingssoft.com/api/getbalbyitem?ItemCode=".$itemcode);

	//return the transfer as a string
	curl_setopt($ch, CURLOPT_FAILONERROR,1);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION,1);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER,1);
  

	// $output contains the output string
	$output = curl_exec($ch);

	
	// close curl resource to free up system resources
	curl_close($ch);   
	//header('Content-type: application/xml');
$movies = new SimpleXMLElement($output);

echo $movies->movie[0]->plot;
break;	
}
?>