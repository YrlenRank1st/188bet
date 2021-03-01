<?php
/**
 * Uses the $_GET query to retrieve a list of cities for a state
 * If $_GET['stateId'] is set, the state ID is used.
 * If $_GET['state'] is set, the state ID is found using the 2-letter state code.
 * If none of the values are set, a JSON object containing "erro":true is returned.
 */

// http://api.188bet.dev.megaleios.kinghost.net/api/v1/Address/Cities/<State ID>
require_once(__DIR__ . '/../functions.php');
require('params.php');

if(isset($_GET['stateId'])){
  $result=plain_request(
    $API_URL . '/Address/Cities/' . $_GET['stateId'],
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
} else if(isset($_GET['state'])){
  $states_json=plain_request(
    $API_URL . '/Address/States',
    null,
    $response_code
  );
  $states_info=json_decode($states_json,true);
  if($states_info['erro']===true){
    //An error occurred
    echo $states_json;
  } else {
    $states=$states_info['data'];
    $state_code=strtoupper($_GET['state']);
    $state_id=null;
    foreach($states as $state){
      if($state['abbreviation']===$state_code){
        $state_id=$state['id'];
        break;
      }
    } // End for each state
    if(empty($state_id)){
      //State not found
?>
{"status":0,"data": null,"erro":true,"errors":null,"message":"state_not_found","messageEx":null}
<?php
    } else {
      $result=plain_request(
        $API_URL . '/Address/Cities/' . $state_id,
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
    } // End if state found
  } // End if states loaded
  
} else {
?>
{"status":0,"data": null,"erro":true,"errors":null,"message":"No Get Data","messageEx":null}
<?php
} // End if no data sent


?>