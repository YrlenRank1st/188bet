<?php
/**
 * For internal use only.
 * Reads an API endpoint that only requires a single access token
 * and has no parameters.
 * The API endpoint is given in the global variable $ENDPOINT
 * For example: $ENDPOINT='/User/Bets';
 * The JSON data is given as an array in the global variable $JSON_DATA
 * For example: $JSON_DATA=array('name'=>'String','number'=>3235);
 * The access token is always located in $_SESSION['bet188']['user']
 */
require_once(__DIR__ . '/../functions.php');
require('params.php');

if(isset($_SESSION['bet188']['user'])){
  $access_token=$_SESSION['bet188']['user']['access_token'];
} else {
  $access_token=null;
}
if(empty($access_token)){
  //User is not logged in
?>
{"status":0,"data":null,"erro":true,"errors":null,"message":"not_logged_in","messageEx":null}
<?php
} else if(empty($ENDPOINT)){
?>
{"status":0,"data":null,"erro":true,"errors":null,"message":"endpoint_not_specified","messageEx":null}
<?php
} else {
  $result=json_request(
    $API_URL . $ENDPOINT,
    json_encode($JSON_DATA),$access_token,$response_code);
  if(empty($result)){
?>
{"status":<?php echo $response_code; ?>,"data": null,"erro":true,"errors":null,"message":"HTTP error","messageEx":null}
<?php
  } else {
    echo $result;
  }
}

?>