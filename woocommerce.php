<?php 


function join_params( $params ) {
    $query_params = array();

    foreach ( $params as $param_key => $param_value ) {
        $string = $param_key . '=' . $param_value;
        $query_params[] = str_replace( array( '+', '%7E' ), array( ' ', '~' ), rawurlencode( $string ) );
    }
    
    return implode( '%26', $query_params );
}

$data = [
              "name"=> "Test Product RG",
              "type"=> "variable",
              "regular_price"=> "31.99",
              "description"=> "Pellentesque habitant morbi tristique senectus et netus et malesuada fames ac turpis egestas. Vestibulum tortor quam, feugiat vitae, ultricies eget, tempor sit amet, ante. Donec eu libero sit amet quam egestas semper. Aenean ultricies mi vitae est. Mauris placerat eleifend leo.",
              "short_description"=> "Pellentesque habitant morbi tristique senectus et netus et malesuada fames ac turpis egestas.",
              "categories"=> [
                [
                  "id"=> 9
                ],
                [
                  "id"=> 14
                ]
              ],
              'attributes'  => [
                    [
                        'id'        => 3,
                        'variation' => true,
                        'visible'   => true,
                        'options'   => [ 'Green', 'Red'],
                    ],
                ],
            ];


// WooCommerce REST API keys. Update these with your keys.
$consumer_key = 'ck_791c6117b77a172eb333e61764496bba97bdd88a';
$consumer_secret = 'cs_4db3628ab5e03c0e0cca95a9983201282059c161';

// Request URI.
$request_uri = '/wp-json/wc/v3/products';



function RESTAPI($request_uri, $method, $data = null ,$consumer_key = 'ck_791c6117b77a172eb333e61764496bba97bdd88a', $consumer_secret = 'cs_4db3628ab5e03c0e0cca95a9983201282059c161')
{

$url = "http://box2023.temp.domains/~simsstor";

$request_uri = $url . $request_uri;

// Unique once-off parameters.
$nonce = uniqid();
$timestamp = time();

$oauth_signature_method = 'HMAC-SHA1';

$hash_algorithm = strtolower( str_replace( 'HMAC-', '', $oauth_signature_method ) ); // sha1
$secret = $consumer_secret . '&';

$http_method = $method;
$base_request_uri = rawurlencode( $request_uri );
$params = array( 'oauth_consumer_key' => $consumer_key, 'oauth_nonce' => $nonce, 'oauth_signature_method' => 'HMAC-SHA1', 'oauth_timestamp' => $timestamp );
$query_string = join_params( $params );

$string_to_sign = $http_method . '&' . $base_request_uri . '&' . $query_string;
$oauth_signature = base64_encode( hash_hmac( $hash_algorithm, $string_to_sign, $secret, true ) );


$curl = curl_init();

curl_setopt_array($curl, array(
  CURLOPT_URL => $request_uri,
  CURLOPT_RETURNTRANSFER => true,
  CURLOPT_ENCODING => "",
  CURLOPT_MAXREDIRS => 10,
  CURLOPT_TIMEOUT => 30,
  CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
  CURLOPT_CUSTOMREQUEST => $http_method,
  CURLOPT_HTTPHEADER => array(
    "Accept: */*",
    "Authorization: OAuth oauth_consumer_key=\"".$consumer_key."\",oauth_signature_method=\"".$oauth_signature_method."\",oauth_timestamp=\"".$timestamp."\",oauth_nonce=\"".$nonce."\",oauth_signature=\"".$oauth_signature."\"",
    "Cache-Control: no-cache",
    "Connection: keep-alive",
    strtoupper($method) == "POST" || strtoupper($method) == "PUT" ? "Content-Type: application/json" : null,
    "accept-encoding: gzip, deflate",
    "cache-control: no-cache"
  ),
));

if(strtoupper($method) == "POST" || strtoupper($method) == "PUT"){
    curl_setopt($curl, CURLOPT_POSTFIELDS, json_encode($data));
}

$response = curl_exec($curl);
$err = curl_error($curl);

curl_close($curl);

if ($err) {
  return "cURL Error #:" . $err;
} else {
  return $response;
}

}

$variation_data1 = [
    'regular_price' => '15.00',
    'attributes'    => [
        [
            'id'     => 3,
            'option' => 'Green',
        ],
    ],
];
$variation_data2 = [
    'regular_price' => '18.00',
    'attributes'    => [
        [
            'id'     => 3,
            'option' => 'Red',
        ],
    ],
];
$product = json_decode(RESTAPI("/wp-json/wc/v3/products", "POST", $data));
$variation[] = RESTAPI("/wp-json/wc/v3/products/$product->id/variations", "POST", $variation_data1);
$variation[] = RESTAPI("/wp-json/wc/v3/products/$product->id/variations", "POST", $variation_data2);
// $product = json_decode(RESTAPI("/wp-json/wc/v3/products", "GET"));
echo json_encode($variation);
?>