<?php
$password_changed=false;
if(isset($_POST['oldPassword'])){
  ob_start();
  $ENDPOINT='/User/UpdatePassword';
  $JSON_DATA=$_POST;
  include($_SERVER['DOCUMENT_ROOT'] . '/logic/api/api-post-user.php');
  $json=ob_get_contents();
  ob_end_clean();
  $result=json_decode($json,true);
  $errors=array();
  if($result['erro']==true){
    if(empty($result['data'])){
      $errors[]=$result['message'];
    } else {
      //Data found. Find error message in data
      $result_data=&$result['data'];
      if(empty($result_data['errors'])){
        //Error list not found.
        if(empty($result_data['message'])){ //Error message not found. Use global message.
          $errors[]=$result['message'];
        } else { // Use local error message
          $errors[]=$result_data['message'];
        }
        
      } else {
        //Error list found
        $err_list=$result_data['errors'];
        foreach($err_list as $err_field){
          foreach($err_field as $err){
            $errors[]=$err;
          }
        } //
      } // End if error list
    } // End if data
  } else {
    //Success
    $success_message='<div class="field"><p class="success">' . $result['message']  . '</p></div>';
  }
  
}

?>

<div class="change-password">
  <h2>Alterar senha</h2>
  <div class="white-box ">
<?php if(!empty($errors)){ ?>
    <ul class="invalid">
<?php foreach($errors as $error){ ?>
      <li><?php echo $error; ?></li>
<?php } ?>
    </ul>
<?php } ?>
    <form method="POST" class="main-form">
<?php if(!empty($success_message)){echo $success_message;} ?>
      <div class="field">
        <label for="oldpass">Senha atual</label
        ><input id="oldpass" type="password" name="oldPassword" required>
      </div><div class="field">
        <label for="newpass">Nova senha</label
        ><input id="newpass" type="password" name="newPassword" required>
      </div><div class="field">
        <label for="newpass2">Confirmar nova senha</label
        ><input id="newpass2" type="password" name="confirmNewPassword" required>
      </div><div class="field submit">
        <button type="submit" class="cta">Alterar senha</button>
      </div>
    </form>
  </div>
</div>