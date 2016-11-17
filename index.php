<?php
require('vars.php');
//index.php

//error function that nicely outputs errors. 
function logError($error) {
    print("<div style='text-align:center;color:red;font-family:sans-serif;'><h1>ERROR :(</h1><p>" . $error . "</p><p>Email <a href='mailto:aswwu.webmaster@wallawalla.edu'>aswwu.webmaster@wallawalla.edu</a></div>");
}

$URI = $_GET["redirectURI"];
if(!isset($URI)){
    $URI = "";
}

//Get dat simple saml library
require_once('../../simplesamlphp/lib/_autoload.php');


//Create the simple saml instance
$as = new SimpleSAML_Auth_Simple('default-sp');

//Send user to login page
$as->requireAuth();

//Get user attributes
$attributes = $as->getAttributes();

// The data to send to the API
$postData = array(
    'employee_id' => $attributes["employee_id"][0],
    'email_address' => $attributes["email_address"][0],
    'full_name' => $attributes["full_name"][0],
    'secret_key' => $super_secret_key
);

// Setup cURL
$ch = curl_init('https://aswwu.com/server/saml/account/');
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
    logError(curl_error($ch));
    die();
}

// Decode the response
$responseData = json_decode($response, TRUE);

// Check to make sure our response was a success
if(array_key_exists("error",$responseData)){
	logError($responseData["error"]);
	die();
}

//Generate Token
$now = time();
$hash = hash_hmac('sha256',$attributes["employee_id"][0] . "|" . time(),$secret_key);

$token = $attributes["employee_id"][0] . "|" . $now . "|" . $hash;

//give the token to the user
setrawcookie("token", $token, $now+1209600, "/", "aswwu.com", 1);

//redirect user to target page
header('Location: https://aswwu.com'. $URI);
?>
