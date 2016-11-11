<?php
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

//send those attributes to the super secure python endpoint.


//get back a JSON that looks like this
//{'user': user.to_json(), 'token': str(token)}

//give the token to the user

?>
