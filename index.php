<?php
require('vars.php');
//index.php
print("running php script");

//Get dat simple saml library
require_once('../../simplesamlphp/lib/_autoload.php');
print("running require");

//Create the simple saml instance
$as = new SimpleSAML_Auth_Simple('default-sp');
print("Declared simple saml");

//Send user to login page
$as->requireAuth();
print("ran requireAuth()");

//Get user attributes
$attributes = $as->getAttributes();
print_r($attributes);

//Generate Token
$now = time();
$hash = hash_hmac('sha256',$attributes["employee_id"][0] . "|" . time(),$secret_key);

$token = $attributes["employee_id"][0] . "|" . $now . "|" . $hash;
print($token);

// The data to send to the API
$postData = array(
    'employee_id' => $attributes["employee_id"][0],
    'email_address' => $attributes["email_address"][0],
    'full_name' => $attributes["full_name"][0]
);

// Setup cURL
$ch = curl_init('aswwu.com:8888/saml/account/');
curl_setopt_array($ch, array(
    CURLOPT_POST => TRUE,
    CURLOPT_RETURNTRANSFER => TRUE,
    CURLOPT_HTTPHEADER => array(
        'Content-Type: application/json'
    ),
    CURLOPT_POSTFIELDS => json_encode($postData)
));

// Send the request
$response = curl_exec($ch);

// Check for errors
if($response === FALSE){
    die(curl_error($ch));
}

// Decode the response
$responseData = json_decode($response, TRUE);

// Print the date from the response
echo $responseData['status'];

//send those attributes to the super secure python endpoint.


//get back a JSON that looks like this
//{'user': user.to_json(), 'token': str(token)}

//give the token to the user

?>
