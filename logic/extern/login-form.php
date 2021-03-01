<?php
/** DICTIONARY **/
global $T_LOGOUT;
$T_LOGOUT=array(
  'user_agent_missing'=>'Seu navegador não tem um agente de usuário.',
  'user_agent_invalid'=>'Seu agente de usuário do navegador mudou. Por favor faça login novamente.',
  'ip_address_missing'=>'Seu computador não tem um endereço IP.',
  'ip_address_changed'=>'Seu endereço IP mudou. Por favor faça login novamente.',
  'session_timeout'=>'Sua seção expirou. Por favor faça login novamente.'
);
function t_logout($str){
  global $T_LOGOUT;
  return isset($T_LOGOUT[$str])?$T_LOGOUT[$str]:$str;
}

/** END DICTIONARY **/
if(function_exists('drupal_session_start')
&& session_status()===PHP_SESSION_NONE){
  drupal_session_start();
}
$logged_in=isset($_SESSION['bet188']['user']);

if(isset($_GET['logout'])){
  unset($_SESSION['bet188']['user']);
  unset($_SESSION['bet188']['cache']['user_info']);
  if(isset($_GET['redirect'])){
    $redirect=$_GET['redirect'];
  } else {
    $redirect='/';
  }
  if(!isset($_SESSION['bet188'])){
    $_SESSION['bet188']=array();
  }
  $_SESSION['bet188']['loggingout']=1;
  header('Location: ' . $redirect);
  exit(0);
}
if($logged_in){
?>
<p>Você já está logado</p>
<?php
} else { //Not logged in

$error_list=array();
if(isset($_POST['username']) && isset($_POST['password']) || isset($_POST['facebookId'])){
  ob_start();
  require_once($_SERVER['DOCUMENT_ROOT'] . '/logic/api/login.php');
  $json=ob_get_contents();
  ob_end_clean();
  $result=json_decode($json,true);
  if($result['erro']==true){
    $error_list[]=$result['message'];
  } else {
    $result_data=&$result['data'];
    $auth_token=$result_data['access_token'];
    require_once($_SERVER['DOCUMENT_ROOT'] . '/logic/functions.php');
    $_SESSION['bet188']['user']=array(
      'ip'=>get_client_ip(),
      'user_agent'=>$_SERVER['HTTP_USER_AGENT'],
      'access_token'=>$result_data['access_token'],
      'refresh_token'=>$result_data['refresh_token'],
      'expires_in'=>time()+$result_data['expires_in']
    );
    if(isset($_POST['facebookId'])){
      $_SESSION['bet188']['user']['facebookId']=$_POST['facebookId'];
    }
    if(isset($_POST['redirect'])){
      $redirect=$_POST['redirect'];
    } else if(isset($_GET['redirect'])){
      $redirect=$_GET['redirect'];
    } else {
      $redirect='/user/dashboard';
    }
    header('Location: ' . $redirect);
    exit(0);
  }
}

?>
<div class="login-screen">
  <div class="login-box white-box">
<?php
if(isset($_SESSION['reason']['message'])){
  echo '<p class="reason">' . t_logout($_SESSION['reason']['message']) . '</p>';
  unset($_SESSION['reason']);
}
?>
    <form method="POST" class="main-form">
      <a class="window-close"></a>
      <h2>Login</h2>
      <p>Entre com suas informações abaixo.</p>
<?php

foreach($error_list as $error_msg){
  echo '<p class="invalid">' . $error_msg . '</p>' . "\n";
}
if(isset($_GET['redirect'])){
  echo '<input type="hidden" name="redirect" value="' . htmlentities($_GET['redirect']) . '">';
} else if(isset($_POST['redirect'])){
  echo '<input type="hidden" name="redirect" value="' . htmlentities($_POST['redirect']) . '">';
}

?>
      <div class="field">
        <label for="login-username">Login</label><input
        id="login-username" type="text" name="username"
<?php if(isset($_POST['username'])){ echo 'value="' . htmlentities($_POST['username']) . '"'; } ?>
        required>
      </div>
      <div class="field">
        <label for="login-password">Senha</label><input
        id="login-password" type="password" name="password"
<?php if(isset($_POST['password'])){ echo 'value="' . htmlentities($_POST['password']) . '"'; } ?>
        required>
        <p class="forgot"><a href="/forgot-password">Esqueceu a senha?</a></p>
      </div>
      <div class="field submit">
        <button class="cta" type="submit" value="login">Login</button>
        <a class="cta facebook facebook-login" href="">Login pelo Facebook</a>
        <noscript>O login pelo Facebook requer Javascript para funcionar.</noscript>
      </div>
    </form>
    <div class="sign-up">
      Novo Usuário? <a href="/register">Registre-se</a>
    </div>
  </div>
</div>
<script type="text/javascript" src="/logic/js/login-fb.js" defer async></script>

<?php
} //End if not logged in
?>
