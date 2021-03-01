<?php
/**
 * Sends the $_POST values as JSON parameters to the endpoint
 * http://api.188bet.dev.megaleios.kinghost.net/api/v1/User/Token
 *   username,password,facebookId
 * Returns a JSON object containing the API response:
{
  "data": null,
  "erro": true, // True if an error occurs
  "errors": null,
  "message": "Senha incorreta!", // Error message
  "messageEx": null
}
 * If the API does not respond, the following JSON object is returned:
{
  "status": XXX, //HTTP code
  "erro": true, // True if an error occurs
  "message": "HTTP error", // Error message
  ...
}
 **/
require_once(__DIR__ . '/../functions.php');
require('params.php');
if(!empty($_POST)){
  if(isset($_POST['facebookId'])){
    unset($_POST['username']);
    unset($_POST['password']);
  }
  $json=json_encode($_POST);
  $response_code=0;
  $result=json_request($API_URL . '/User/Token',
    $json,null,$response_code);
  if(empty($result)){
?>
{"status":<?php echo $response_code; ?>,"data": null,"erro":true,"errors":null,"message":"HTTP error","messageEx":null}
<?php
  } else {
    echo $result;
  }
} else {
  echo '{"status":0,"data": null,"erro":true,"errors":null,"message":"No Post Data","messageEx":null}';
}
?>