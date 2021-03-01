<?php
/**
 * Invites a member $_POST['member_id'] to the group $_POST['id']
 */

define('DRUPAL_ROOT',$_SERVER['DOCUMENT_ROOT']);
$base_url= 'http' . ($_SERVER['HTTPS']?'s':'') . '://' . $_SERVER['HTTP_HOST'];
require_once($_SERVER['DOCUMENT_ROOT'] . '/includes/bootstrap.inc');
drupal_bootstrap(DRUPAL_BOOTSTRAP_FULL);

$ENDPOINT='/Group/InviteNewMember';
$JSON_DATA=array(
  'id'=>$_POST['id'],
  'members'=>array(
    array('id'=>$_POST['member_id'])
  )
);
ob_start();
require_once($_SERVER['DOCUMENT_ROOT'] . '/logic/api/api-post-user.php');
$json=ob_get_contents();
ob_end_clean();
echo $json;
?>