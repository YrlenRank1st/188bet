<?php
/** 
 * Returns a list of teams with the format: [{name,photo,id},...]
 * Uses the API end point: http://api.188bet.dev.megaleios.kinghost.net/api/v1/Team
 */
require_once(__DIR__ . '/../functions.php');
require('params.php');
$response_code=0;
$result=plain_request(
  $API_URL . '/Team',
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