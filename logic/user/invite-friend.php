<?php
$message_sent=false;
$message=null;
if(isset($_POST['invite-friend'])){
  //
  $ENDPOINT='/User/InviteFriendByEmail';
  $JSON_DATA=array(
    'email'=>$_POST['email']
  );
  ob_start();
  require($_SERVER['DOCUMENT_ROOT'] . '/logic/api/api-post-user.php');
  //echo '{"erro":false,"message":"Suceso: ' . $JSON_DATA['email'] . ' "}';
  $invite_json=ob_get_contents();
  ob_end_clean();
  $invite_info=json_decode($invite_json,true);
  if($invite_info['erro']){
    $message='<p class="invalid">' . $invite_info['message'] . '</p>';
    error_log($invite_json);
  } else {
    $message_sent=true;
    $message='<p class="success">' . $invite_info['message'] . '</p>';
  }
}

if($message_sent){
?>
<div class="invite-friend white-box">
  <a href="" class="window-close"></a>
<?php if(isset($message)){ echo $message; } ?>  
</div>
<?php
} else {
?>
<div class="invite-friend white-box">
  <a href="" class="window-close"></a>
  <form class="main-form" method="POST">
    <input type="hidden" name="invite-friend" value="1">
    <h2>Convidar um amigo</h2>
    <div class="field">
<?php if(isset($message)){ echo $message; } ?>
      <p>Convide um amigo para jogar no 188bet!</p>
    </div><div class="field">
      <label for="friend-email">Email do amigo</label
      ><input type="email" id="friend-email" name="email" value="<?php
echo isset($_POST['email'])?htmlentities($_POST['email']):'';
      ?>">
    </div><div class="field submit">
      <button type="submit" class="cta">Convidar</button>
    </div>
  </form>
</div>
<?php
} // End message sent

?>