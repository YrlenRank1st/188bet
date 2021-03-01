<?php
// http://api.188bet.dev.megaleios.kinghost.net/api/v1/Address/States
require_once(__DIR__ . '/../functions.php');
require('params.php');
//Get states list.
$response_code=0;
$result=plain_request(
  $API_URL . '/Address/States',
  null,
  $response_code
);

if(empty($result)){
?>
{"status":<?php echo $response_code; ?>,"data": null,"erro":true,"errors":null,"message":"HTTP Error","messageEx":null}
<?php
} else {
  echo $result;
}
?>