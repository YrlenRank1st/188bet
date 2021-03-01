<?php
/**
 * Deletes a member with the group ID in $_POST['id']
 * and the username in $_POST['username']
 */

define('DRUPAL_ROOT',$_SERVER['DOCUMENT_ROOT']);
$base_url= 'http' . ($_SERVER['HTTPS']?'s':'') . '://' . $_SERVER['HTTP_HOST'];
require_once($_SERVER['DOCUMENT_ROOT'] . '/includes/bootstrap.inc');
drupal_bootstrap(DRUPAL_BOOTSTRAP_FULL);

ob_start();
$ENDPOINT='/Group/RemoveMember';
$JSON_DATA=array(
  'id'=>$_POST['id'],
  'members'=>array(
    array('id'=>$_POST['member_id'])
  )
);
require_once($_SERVER['DOCUMENT_ROOT'] . '/logic/api/api-post-user.php');
$json=ob_get_contents();
ob_end_clean();
echo $json;
?>