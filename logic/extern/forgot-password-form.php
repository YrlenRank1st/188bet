<?php
if(isset($_SESSION['bet188']['user'])){
  //Logged in: Go to user dashboard
  header('Location: /user/dashboard');
  exit(0);
}
$error_list=array();
if(isset($_POST['email'])){
  ob_start();
  require_once($_SERVER['DOCUMENT_ROOT'] . '/logic/api/forgot-password.php');
  $json=ob_get_contents();
  ob_end_clean();
  $result=json_decode($json,true);
  
  
  if($result['erro']==true){
    //If error
    $error_list[]='<p class="invalid">' . $result['message'] . '</p>';
  } else {
    $error_list[]='<p class="success">' . $result['message'] . '</p>';
  } //End if success
}
?><div class="forgot-password">
  <div class="white-box">
    <form class="main-form" method="POST">
      <div class="field">
        <h2>Lembrete do Login</h2>
<?php
foreach($error_list as $error_msg){
  echo $error_msg . "\n";
}
?>
        <p>Se você perdeu seus detalhes de login, por favor insira seu endereço de e-mail abaixo para que possamos enviar uma nova senha para você:</p>
      </div>
      <div class="field">
        <label for="forgot-email">E-Mail</label><input id="forgot-email"
        type="email" name="email"
<?php if(isset($_POST['email'])){ echo 'value="' . $_POST['email'] . '"'; } ?>
        required>
      </div><div class="field submit">
        <button type="submit" class="cta">Enviar Senha</button>
      </div>
    </form>
  </div>
</div>