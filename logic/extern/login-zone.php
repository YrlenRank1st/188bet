<?php
$logged_in=isset($_SESSION['bet188']['user']);

/* Make sure user's account is secure, logged in and up to date. */
if($logged_in){
  require_once($_SERVER['DOCUMENT_ROOT'] . '/logic/user/loggedin.php');
  $logged_in=isset($_SESSION['bet188']['user']);
}

if($logged_in){

  /* Get API data */
  //Get user data
  $user_data=get_user_info();
  /** DEBUG CODE! **/
  if(isset($_GET['debug']) && strpos($_SERVER['HTTP_HOST'],'188bet.net') === false){
    print_r($_SESSION['bet188']['user']['access_token']);
  }
  /** **/
  //Get notifications
  ob_start();
  $ENDPOINT='/User/Notifications/1';
  require($_SERVER['DOCUMENT_ROOT'] . '/logic/api/api-user.php');
  $notes_json=ob_get_contents();
  ob_end_clean();
  $notes_data=json_decode($notes_json,true);
  /* END get API data */
  
  /* Handle user data */
  if(empty($user_data) || $user_data['erro']){
    $user_info=array(
      'image'=>'',
      'level'=>0,
      'firstName'=>'Error',
      'lastName'=>''
    );
  } else {
    $user_info=$user_data['data'];
  }

  $name=$user_info['firstName'] . ' ' . $user_info['lastName'];
  $image=$user_info['photo'];
  if(empty($image)){
    $image='/sites/default/files/images/extern/user-default.jpg';
  }
  $user_level=((int)$user_info['level'])+1;
  $level_image=get_level_image($user_level);
  
  /* Handle notifications */
  $notifications=null;
  if(empty($notes_data) || $notes_data['erro']){
    error_log('Notifications fail: ' . $notes_json);
  } else {
    $notifications=$notes_data['data'];
  }
  $notification_class='';
  if(!empty($notifications)){
    global $NOTE_YESTERDAY;
    global $NOTE_NOW;
    $today=date('d');
    $NOTE_YESTERDAY=date('d',$today-86400);
    $NOTE_NOW=time();
    $notification_class='';
    $notification_html='';
    foreach($notifications as &$note){
      if(!$note['isRead']){$notification_class=' new-notifications';}
      $notification_html.=render_notification($note);
    }
    unset($note);
  }
  

?>
<div class="logged-in-zone">
  <div class="user-summary">
    <span class="notification-bell<?php echo $notification_class; ?>">
      <img src="/sites/default/files/images/icons/bell.png">
    </span>
    <div>
      <span class="user-image">
        <img src="<?php echo $image; ?>" alt="<?php echo $name; ?>">
<?php if(isset($level_image)){ ?>
        <img class="level-image" src="<?php echo $level_image; ?>" alt="<?php echo $user_level; ?>">
<?php } ?>
      </span><span class="name">
        <a href="/user/dashboard"><?php echo $name; ?></a>
      </span><span class="login-menu-btn"></span>
    </div>
  </div>
  <ul class="menu login-menu">
    <li><a href="/user/update">Editar conta</a
    ></li><li><a href="/user/my-titles">Meus títulos</a></li>
    <li><a href="/rewards">Recompensas</a
    ></li><li><a class="friend-invite-btn" href="/invite-friend">Convidar um amigo</a
    ></li><li><a id="logout-btn" class="logout" href="/login?logout=1&redirect=/">Sair</a></li>
  </ul>
  <ul class="notifications">
<?php if(empty($notifications)){
?><li>Você não tem notificações.</li><?php
} else {
?>
<?php
  echo $notification_html;
} ?>
  </ul>
</div>
<?php
  drupal_add_js('/logic/js/login-menu.js',array('defer'=>true));
} else { //Not logged in
?><div class="login-buttons"><a class="login-btn" id="login" href="/login"
>Login</a><a class="register cta" id="register" href="/register"
>Registre-se</a></div>
<?php
  drupal_add_js('/logic/js/login.js');
} //End if not logged in

if(isset($_SESSION['bet188']['loggingout'])){
  unset($_SESSION['bet188']['loggingout']);
  ob_start();
?>
window.addEventListener("DOMContentLoaded",completeFBLogout);

function completeFBLogout(e){
  if(typeof(FB)==="undefined"){
    setTimeout(completeFBLogout,500);
    return;
  }
  FB.getLoginStatus(fbLoggingOut,true);
  function fbLoggingOut(response){
    if(response.status==="connected"){
      FB.logout(fbLoggedOutComplete);
    }
  }
  function fbLoggedOutComplete(response){}
}
<?php
  $logout_js=ob_get_contents();
  ob_end_clean();
  
  drupal_add_js($logout_js,array('type'=>'inline'));
  
}


/** FUNCTIONS **/
function render_notification(&$note){
  global $NOTE_YESTERDAY;
  global $NOTE_NOW;
  $time=$note['date'];
  $time_dif=$NOTE_NOW-$time+60; //Extra minute for rounding
  $time_day=date('d',$time);
  if($time_dif<0){
    $time_str='Agora';
  } else if($time_dif<3600){
    //Minutes ago
    $time_count=round($time_dif/60);
    $plural=(($time_count>1)?'s':'');
    $time_str='Há ' . $time_count . ' minuto' . $plural;
  } else if($time_dif<86400){ //24*3600
      //Hours ago
    $time_count=round($time_dif/3600);
    $plural=(($time_count>1)?'s':'');
    $time_str='Há ' . $time_count . ' hora' . $plural;
  } else if($time_day==$NOTE_YESTERDAY){ //Yesterday
    $time_str='Ontem';
  } else if($time_dif<604800){ //7*24*3600
    //Days ago
    $time_count=round($time_dif/86400);
    $plural=(($time_count>1)?'s':'');
    $time_str='Há ' . $time_count . ' dia' . $plural;
  } else {
    //Date
    $time_str=utf8_encode(strftime('%e de %B de %Y',$time));
  }
  ob_start();
?><li<?php if(!($note['isRead'])){ echo ' class="new"';} ?>>
      <span class="date"><?php echo $time_str; ?></span>
      <span class="message"><?php echo $note['text']; ?></span>
    </li><?php
  $html=ob_get_contents();
  ob_end_clean();
  return $html;
}


?>
