<?php
/* Get location */
$agent=$_SERVER['HTTP_USER_AGENT'];
$ipod=stripos($agent,'ipod');
$iphone=stripos($agent,'iphone');
$ipad=stripos($agent,'ipad');
$android=stripos($agent,'android');

if($ipod || $iphone || $ipad){
  header('Location: https://itunes.apple.com/br/app/188app/id1326346368?mt=8');
} else if($android){
  header('Location: https://play.google.com/store/apps/details?id=com.megaleios.app188bet');
} else {
  //Not mobile.
  header('Location: /');
}


?>