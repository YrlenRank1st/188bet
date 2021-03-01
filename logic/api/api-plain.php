<?php
/**
 * For internal use only.
 * Reads an API endpoint with no parameters or authentication.
 * The API endpoint is given in the global variable $ENDPOINT
 * For example: $ENDPOINT='/User/Bets'
 */
require_once(__DIR__ . '/../functions.php');
require('params.php');
$response_code=0;
$result=plain_request(
  $API_URL . $ENDPOINT,
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