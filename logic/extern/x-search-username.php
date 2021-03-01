<?php
/**
 * Finds a username using the Drupal session
 * The username is requested with $_GET['name']
 */

define('DRUPAL_ROOT',$_SERVER['DOCUMENT_ROOT']);
$base_url= 'http' . ($_SERVER['HTTPS']?'s':'') . '://' . $_SERVER['HTTP_HOST'];
require_once($_SERVER['DOCUMENT_ROOT'] . '/includes/bootstrap.inc');
drupal_bootstrap(DRUPAL_BOOTSTRAP_FULL);

/* Hide specific values from other users! */
ob_start();
require_once($_SERVER['DOCUMENT_ROOT'] . '/logic/api/member-by-name.php');
$user_json=ob_get_contents();
ob_end_clean();
$user_data=json_decode($user_json,true);
unset($user_data['data']['facebookId']);
unset($user_data['data']['email']);
echo json_encode($user_data);
?>