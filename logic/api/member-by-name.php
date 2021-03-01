<?php
/**
 * Finds a member by their username. The username is given in the
 * parameter $_GET['name'].
 * API endpoint: http://api.188bet.dev.megaleios.kinghost.net/api/v1/User/SearchByUsername
 * The access token is either in $_SESSION['bet188']['user'] or in the
 * $_POST variable $_POST['access_token'] (Not recommended)
 
 */
require_once(__DIR__ . '/../functions.php');
require('params.php');
if(isset($_SESSION['bet188']['user'])){
  $access_token=$_SESSION['bet188']['user']['access_token'];
} else if(isset($_POST['access_token'])){
  $access_token=$_POST['access_token'];
} else {
  $access_token=null;
}
if(!isset($_GET['name'])){
  $_GET['name']='';
}
if(empty($access_token)){
  //User is not logged in
?>
{"status":0,"data":null,"erro":true,"errors":null,"message":"not_logged_in","messageEx":null}
<?php
} else {
  $result=json_request($API_URL . '/User/SeachByUsername?username=' . urlencode($_GET['name']),
    null,$access_token,$response_code);
  if(empty($result)){
?>
{"status":<?php echo $response_code; ?>,"data": null,"erro":true,"errors":null,"message":"HTTP error","messageEx":null}
<?php
  } else {
    echo $result;
  }
}
 
?>