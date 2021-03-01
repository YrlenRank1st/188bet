<?php
ini_set('session.use_only_cookies','true');
if(function_exists('drupal_session_start')
&& session_status()===PHP_SESSION_NONE){
  drupal_session_start();
}
require_once($_SERVER['DOCUMENT_ROOT'] . '/logic/functions.php');
$logged_in=isset($_SESSION['bet188']['user']);
if($logged_in){
  /* Do security checks */
  $r=&$_SESSION['bet188']['user'];
  $username=$r['username'];
  $fb_id=null;
  do{
    /*
    //User agent
    if(!isset($r['user_agent'])){
      $_SESSION['reason']=array('username'=>$username,'message'=>'user_agent_missing');
      unset($_SESSION['bet188']['user']);
      $logged_in=false; break;      
    }
    $new_useragent=$_SERVER['HTTP_USER_AGENT'];
    if(!($new_useragent===$r['user_agent'])){
      $_SESSION['reason']=array('username'=>$username,'message'=>'user_agent_invalid');
      unset($_SESSION['bet188']['user']);
      $logged_in=false; break;
    }
    //IP Address
    if(!isset($r['ip'])){
      $_SESSION['reason']=array('username'=>$username,'message'=>'ip_address_missing');
      unset($_SESSION['bet188']['user']);
      $logged_in=false; break;
    }
    $new_ip=get_client_ip();
    if(!($new_ip===$r['ip'])){
      $_SESSION['reason']=array('username'=>$username,'message'=>'ip_address_changed');
      unset($_SESSION['bet188']['user']);
      $logged_in=false; break;
    }
    */
    //Facebook ID
    /* Check that Facebook is connected 
    if(isset($_SESSION['bet188']['user']['facebookId'])){
      // Verify that the user is still logged in
      $fb_id=$_SESSION['bet188']['user']['facebookId'];
      if(function_exists('drupal_add_js')){
        ob_start();
        //Check facebook status
        $fb_js=ob_get_contents();
        ob_end_clean();
        drupal_add_js($fb_js,array('type'=>'inline'));
      }
    }
    /**/
    
    //Check expiry date
    $now=time();
    if($r['expires_in']-600<$now){
      //If token expires in 10 minutes, renew token.
      $old_post=$_POST;
      $_POST=array('refreshToken'=>$r['refresh_token']);
      $refresh_value=json_encode($_POST);
      ob_start();
      require($_SERVER['DOCUMENT_ROOT'] . '/logic/api/login.php');
      $json=ob_get_contents();
      ob_end_clean();
      $_POST=$old_post;
      $result=json_decode($json,true);
      if($result['erro']==true){
        //Some unknown error occurred. Need to log in again
        error_log('User ' . $username . ' logged out. Object: ' . $json);
        $_SESSION['reason']=array('username'=>$username,'message'=>'session_timeout');
        unset($_SESSION['bet188']['user']);
        $logged_in=false; break;
      } else {
        //Update access token
        $result_data=&$result['data'];
        $auth_token=$result_data['access_token'];
        $_SESSION['bet188']['user']=array(
          'ip'=>get_client_ip(),
          'user_agent'=>$_SERVER['HTTP_USER_AGENT'],
          'access_token'=>$result_data['access_token'],
          'refresh_token'=>$result_data['refresh_token'],
          'expires_in'=>time()+$result_data['expires_in']
        );
        if(isset($fb_id)){
          $_SESSION['bet188']['user']['facebookId']=$fb_id;
        }
      } // End if no error
      
    } // End if expires soon
    
  }while(0);
  /* End security checks */
}
if(!$logged_in){
  if(isset($_SESSION['reason'])){
    error_log('User ' . $_SESSION['reason']['message'] . ' logged out: ' . $_SESSION['reason']['message']);
  }
  header('Location: /login?redirect=' . urlencode($_SERVER['REQUEST_URI']));
?>
<html><head><title>Redirect</title></head><body>
  <p>Você precisa estar logado para ver esta página –  
  <a href="/login?redirect=<?php echo urlencode($_SERVER['REQUEST_URI']); ?>">Clique aqui</a> se você ainda não tiver sido redirecionado.</p>
</body></html>
<?php
  exit(0);
}
if(function_exists('drupal_add_css')){
  drupal_add_css(
    'logic/css/user.css',
    array(
      'type'=>'file',
      'group'=>CSS_THEME,
      'weight'=>0,
      'preprocess'=>true,
      'media'=>'all',
      'every_page'=>true
    )
  );
} else {
  echo 'No Drupal';
}
?>