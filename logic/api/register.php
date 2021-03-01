<?php
/**
 * http://api.188bet.dev.megaleios.kinghost.net/api/v1/User/Register
 * Calls the API by encoding the $_POST fields as a JSON object.
 * The following fields are expected:
  {
    "firstName": "",
    "lastName": "",
    "username": "",
    "email": "dggarcia1@hotmail.com",
    "team": { "Name" : "ID" },
    "dateOfBirth": <Timestamp>,
    "state": { "Name": "ID" },
    "citie": { "Name": "ID" },
    "password": "",
    "confirmPassword": ""
  }
  
  * These other fields are optional
{
  "photo": "string",
  "facebookId": "string",
  "level": 0,
  "medals": 0,
  "position": 0,
  "id": "string"
}
 */
require_once(__DIR__ . '/../functions.php');
require('params.php');

if(!empty($_POST)){
  $json=json_encode($_POST);
  $response_code=0;
  $result=json_request($API_URL . '/User/Register',
    $json,null,$response_code);
  if(empty($result)){
?>
{"status":<?php echo $response_code; ?>,"data": null,"erro":true,"errors":null,"message":"HTTP error","messageEx":null}
<?php
  } else {
    echo $result;
  }
} else {
?>
{"status":0,"data": null,"erro":true,"errors":null,"message":"No Post Data","messageEx":null}
<?php
}

?>