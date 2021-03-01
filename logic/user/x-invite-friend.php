<?php
define('DRUPAL_ROOT',$_SERVER['DOCUMENT_ROOT']);
$base_url= 'http' . ($_SERVER['HTTPS']?'s':'') . '://' . $_SERVER['HTTP_HOST'];
require_once($_SERVER['DOCUMENT_ROOT'] . '/includes/bootstrap.inc');
drupal_bootstrap(DRUPAL_BOOTSTRAP_FULL);
require_once('invite-friend.php');
?>