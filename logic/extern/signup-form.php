<?php
require_once(__DIR__ .'/../functions.php');
/** DICTIONARY **/
global $TV_REG; // Translation: Validate register 1
$TV_REG=array(
  'username_empty'=>'O campo "nome de usuário" está vazio.',
  'email_empty'=>'O campo "endereço de email" está vazio.',
  'email_mismatch'=>'Os endereços de email não correspondem.',
  'password_empty'=>'O campo "senha" está vazio.',
  'password_mismatch'=>'As senhas não correspondem.',
  'first_empty'=>'O campo "primeiro nome" está vazio.',
  'last_empty'=>'O campo "sobrenome" está vazio.',
  'bday_empty'=>'O campo "data de nascimento" está vazio.',
  'impossible_date'=>'A data de nascimento é inválida.',
  
  'state_empty'=>'O campo "estado" está vazio.',
  'state_not_found'=>'O estado não foi encontrado neste país.',
  'city_empty'=>'O campo "cidade" está vazio.',
  'city_not_found'=>'A cidade não foi encontrada neste estado.',
  'team_not_found'=>'O time escolhido não foi encontrado na lista.',
  
  'states_not_loaded'=>'Não foi possível carregar a lista de estados.',
  'teams_not_loaded'=>'Não foi possível carregar a lista de times.',
);
function tv_reg($code){
  global $TV_REG;
  return isset($TV_REG[$code])?$TV_REG[$code]:'Error: ' . $code;
}
/** END DICTIONARY **/

$logged_in=isset($_SESSION['bet188']['user']);
$error_list=array();
unset($_SESSION['bet188']['register']);
if(!empty($_POST)){
  do{
    /* 1- Validate form */
    // username,email,email2,password,password2,first,last,bday,bmonth,byear
    $error_list=valid_register($_POST);
    if(!empty($error_list)){
      break;
    }
    
    //Submit data to API
    $error_list=send_register($_POST);
    if(!empty($error_list)){
      break;
    }
    
    //Move to next page
    header('Location: /user/dashboard');
    exit(0);
  } while(0);
}
if($logged_in){
?>
<p>Você já está logado</p>
<?php
} else { //Not logged in
  $r=&$_POST;
/** LOAD API VALUES **/
//List of states
$states=get_states();
if($states==null){
  $error_list[]=tv_reg('states_not_loaded');
}
//List of teams
$teams=get_teams();


/** END LOAD API VALUES **/
  
?>
<div class="signup-screen">
  <h2>Registro</h2>
  <form method="POST" class="main-form" id="bet188-register">
<?php if(!empty($error_list['global'])){
  echo '<div class="signup-box white-box"><div class="field"><p class="invalid">' . $error_list['global'] . '</p></div></div>';
}
?>
    <div class="signup-box white-box">
      <a class="window-close"></a>
      <div class="field">
        <label for="signup-username"
        >Nome de Usuário *</label><input id="signup-username"
        name="username" type="text"
<?php if(isset($r['username'])){ echo ' value="' . htmlentities($r['username']) . '"'; } ?> required>
<?php if(!empty($error_list['username'])){
  echo '<p class="invalid">' . $error_list['username'] . '</p>';
}
?>
      </div><div class="field">
        <label for="signup-email"
        >E-mail *</label><input id="signup-email"
        name="email" type="email"
<?php if(isset($r['email'])){ echo 'value="' . htmlentities($r['email']) . '"'; } ?> required>
<?php if(!empty($error_list['email'])){
  echo '<p class="invalid">' . $error_list['email'] . '</p>';
}
?>
      </div><div class="field">
        <label for="signup-email2"
        >Confirme seu e-mail *</label><input id="signup-email2"
        name="email2" type="email"
<?php if(isset($r['email2'])){ echo 'value="' .  htmlentities($r['email2']) . '"'; } ?> required>
<?php if(!empty($error_list['email2'])){
  echo '<p class="invalid">' . $error_list['email2'] . '</p>';
}
?>
      </div><div class="field">
        <label for="signup-password"
        >Senha *</label><input id="signup-password"
        name="password" type="password"
<?php if(isset($r['password'])){ echo 'value="' .  htmlentities($r['password']) . '"'; } ?> required>
<?php if(!empty($error_list['password'])){
  echo '<p class="invalid">' . $error_list['password'] . '</p>';
}
?>
      </div><div class="field">
        <label for="signup-password2"
        >Confime sua Senha *</label><input id="signup-password2"
        name="password2" type="password"
<?php if(isset($r['password2'])){ echo 'value="' .  htmlentities($r['password2']) . '"'; } ?> required>
<?php if(!empty($error_list['password2'])){
  echo '<p class="invalid">' . $error_list['password2'] . '</p>';
}
?>
      </div>
    </div>
    <div class="signup-box white-box">
      <a class="window-close"></a>
      <div class="field">
        <label for="signup-first"
        >Primeiro Nome *</label><input id="signup-first"
        name="first" type="text"
<?php if(isset($r['first'])){ echo 'value="' .  htmlentities($r['first']) . '"'; } ?> required>
<?php if(!empty($error_list['first'])){
  echo '<p class="invalid">' . $error_list['first'] . '</p>';
}
?>
      </div><div class="field">
        <label for="signup-last"
        >Sobrenome *</label><input id="signup-last"
        name="last" type="text"
<?php if(isset($r['last'])){ echo 'value="' .  htmlentities($r['last']) . '"'; } ?> required>
<?php if(!empty($error_list['last'])){
  echo '<p class="invalid">' . $error_list['last'] . '</p>';
}
?>
      </div><div class="field field-select">
        <p class="fake-label">Data de Nascimento *</p>
        <div class="select-day">
          <select name="bday" required>
            <option value="">Dia</option>
<?php for($n=1;$n<=31;$n++){ ?>
            <option value="<?php echo $n; ?>"<?php

if(isset($r['bday']) && $r['bday']==$n){ echo ' selected'; }

?>><?php echo ($n<10)?'0' . $n:$n; ?></option>
<?php } ?>
          </select>
        </div><div class="select-month">
          <select name="bmonth" required>
            <option value="">Mês</option>
<?php for($n=1;$n<=12;$n++){ ?>
            <option value="<?php echo $n; ?>"<?php

if(isset($r['bmonth']) && $r['bmonth']==$n){ echo ' selected'; }

?> required><?php echo ($n<10)?'0' . $n:$n; ?></option>
<?php } ?>
          </select>
        </div><div class="select-year">
          <select name="byear" required>
            <option value="">AAAA</option>
<?php
$year=date('Y');
$last_year=$year-18;
$first_year=$last_year-99;
for($n=$last_year;$n>=$first_year;$n--){
?>
            <option value="<?php echo $n; ?>"<?php

if(isset($r['byear']) && $r['byear']==$n){ echo ' selected'; }

?>><?php echo $n; ?></option>
<?php } ?>
          </select>
        </div>
<?php if(!empty($error_list['bday'])){
  echo '<p class="invalid">' . $error_list['bday'] . '</p>';
}
?>
      </div>
    </div>
    <div class="signup-box white-box">
      <div class="field field-select">
        <div class="select-state">
          <select name="state" required>
            <option value="">Estado *</option>
<?php foreach($states as $state){ ?>
            <option value="<?php echo $state['id']; ?>"<?php
            if(isset($r['state']) && $r['state']===$state['id']){
              echo ' selected';
            }
            ?>><?php echo $state['name']; ?></option>
<?php } ?>
          </select>
<?php if(!empty($error_list['state'])){
  echo '<p class="invalid">' . $error_list['state'] . '</p>';
}
?>
        </div>
      </div><div class="field field-select" id="field-city">
        <label for="signup-city"
        >Cidade *</label><input id="signup-city"
        name="city" type="text"<?php
        if(isset($r['city'])){
          echo ' value="' . htmlentities($r['city']) . '"';
        }
        ?> required>
<?php if(!empty($error_list['city'])){
  echo '<p class="invalid">' . $error_list['city'] . '</p>';
}
?>
      </div><div class="field field-select">
        <div class="select-team">
          <select name="team">
            <option value="">Time do coração</option>
<?php foreach($teams as $team){
  //Team has the format: name,photo,id
?>
            <option value="<?php echo $team['id']; ?>"<?php
            if(isset($r['team']) && $r['team']===$team['id']){
              echo ' selected';
            }
            ?>><?php echo $team['name']; ?></option>
<?php } ?>
          </select>
        </div>
<?php if(!empty($error_list['team'])){
  echo '<p class="invalid">' . $error_list['team'] . '</p>';
}
?>
      </div>
    </div>
    <div>
      <button class="cta" type="submit">Cadastrar</button>
    </div>
    <script src="/logic/js/cities.js" async defer></script>
  </form>
</div>
<?php
} //End if not logged in

function valid_register(&$data){
  //username,email,email2,password,password2,first,last,bday,bmonth,byear
  $errors=array();
  /** Part 1: User name, email, real name **/
  //Username
  $n='username';$f=trim($data[$n]);
  if(empty($f)){ $errors[$n]=tv_reg('username_empty'); }
  //Email
  $n='email';$f=trim($data[$n]);
  $email=$f;
  if(empty($f)){
    $errors[$n]=tv_reg('email_empty');
  } else {
    $n='email2';$f=trim($data[$n]);
    $email2=$f;
    if(!($email===$email2)){
      $errors[$n]=tv_reg('email_empty');
    }
  }
  //Password
  $n='password';$f=$data[$n];
  $password=$f;
  if(empty($f)){
    $errors[$n]=tv_reg('password_empty');
  } else {
    $n='password2';$f=$data[$n];
    $password2=$f;
    if(!($email===$email2)){
      $errors[$n]=tv_reg('password_mismatch');
    }
  }
  //Name
  $n='first';$f=trim($data[$n]);
  if(empty($f)){ $errors[$n]=tv_reg('first_empty'); }
  $n='last';$f=trim($data[$n]);
  if(empty($f)){ $errors[$n]=tv_reg('last_empty'); }
  //Birthday
  $n='bday';
  if(empty($data['bday'])
  || empty($data['bmonth'])
  || empty($data['byear'])){
    $errors[$n]=tv_reg('bday_empty');
  } else {
    $date_ok=checkdate($data['bmonth'],$data['bday'],$data['byear']);
    if(!$date_ok){
      $errors[$n]=tv_reg('impossible_date');
    }
  }
  
  /** Part 2: State, City, Team **/
  //State and city
  $n='state';
  $f=$data[$n];
  if(empty($f)){
    $errors[$n]=tv_reg('state_empty');
  } else {
    //Get list of cities in the state
    $old_get=$_GET;
    $_GET['stateId']=$f;
    ob_start();
    include($_SERVER['DOCUMENT_ROOT'] . '/logic/api/cities.php');
    $cities_json=ob_get_contents();
    ob_end_clean();
    $_GET=$old_get;
    $cities_info=json_decode($cities_json,true);
    if($cities_info['erro']==true){
      if($cities_info['message']==='state_not_found'){
        //State is not in the list
        $errors[$n]=tv_reg('state_not_found');
      } else {
        $errors[$n]=$cities_info['message'];
      }
    } else {
      //State OK. Check if city is in list cities
      $n='city';
      $f=$data[$n];
      $city_name=strtolower(normalize_special_chars(trim($f)));
      if(empty($city_name)){
        $errors[$n]=tv_reg('city_empty');
      } else {
        $cities=&$cities_info['data'];
        $city_in_list=false;
        foreach($cities as &$city){
          if(
            strtolower(normalize_special_chars($city['name']))===$city_name
          ){ // City is not in list
            $city_in_list=true;
            $data['cityId']=$city['id'];
            break;
          }
        } // End for each city
        if(!$city_in_list){
          $errors[$n]=tv_reg('city_not_found');
        }
      } // End if not empty city
    } //End if no errors loading cities
  } // End if state not empty
  
  $n='team';
  $f=$data[$n];
  if(!empty($f)){
    //Check if team is in the list
    $teams=get_teams();
    $team_id=$f;
    $team_exists=false;
    foreach($teams as $team){
      if($team['id']===$team_id){
        $team_exists=true;
        $data['teamName']=$team['name'];
        break;
      }
    }
    if(!$team_exists){
      $errors[$n]=tv_reg('team_not_found');
    }
  } // End if not empty team
  
  
  return $errors;
}

function send_register($data){

  $old_post=$_POST;
  $_POST=array();
  if(isset($data['first'])){ $_POST['firstName']=$data['first']; }
  if(isset($data['last'])){ $_POST['lastName']=$data['last']; }
  if(isset($data['username'])){ $_POST['username']=$data['username']; }
  if(isset($data['email'])){ $_POST['email']=$data['email']; }
  if(isset($data['team']) && isset($data['teamName'])){
    //$_POST['team']=array('name'=>$data['teamName'],'id'=>$data['team']);
    $_POST['team']=array('id'=>$data['team']);
  }
  if(isset($data['bday'])
  && isset($data['bmonth'])
  && isset($data['byear'])){
    $bday_ts=strtotime($data['byear'] . '-' . $data['bmonth'] . '-' . $data['bday'] );
    $_POST['dateOfBirth']=$bday_ts;
  }
  if(isset($data['state'])){
    $states=get_states(); // Let's hope that cache is still working...
    $state_id=$data['state'];
    $state_name=null;
    foreach($states as &$state){
      if($state_id===$state['id']){
        $state_name=$state['name'];
      }
    }
    if(isset($state_name)){
      //$_POST['state']=array('name'=>$state_name,'id'=>$state_id);
      $_POST['state']=array('id'=>$state_id);
    }
  }
  if(isset($data['city']) && isset($data['cityId'])){
    //$_POST['citie']=array('name'=>$data['city'],'id'=>$data['cityId']);
    $_POST['citie']=array('id'=>$data['cityId']);
  }
  if(isset($data['password'])){ $_POST['password']=$data['password']; }
  if(isset($data['password2'])){ $_POST['confirmPassword']=$data['password2']; }
  ob_start();
  include($_SERVER['DOCUMENT_ROOT'] . '/logic/api/register.php');
  $result=ob_get_contents();
  ob_end_clean();
  $_POST=$old_post;
  $result_info=json_decode($result,true);
  if($result_info['erro']==true){
    return array('global'=>$result_info['message']);
  }
  if(!isset($_SESSION['bet188'])){
    $_SESSION['bet188']=array();
  }
  //Log in
  $result_data=$result_info['data'];
  $_SESSION['bet188']['user']=array(
    'ip'=>get_client_ip(),
    'user_agent'=>$_SERVER['HTTP_USER_AGENT'],
    'access_token'=>$result_data['access_token'],
    'refresh_token'=>$result_data['refresh_token'],
    'expires_in'=>time()+$result_data['expires_in']
  );
  
  return null;
}

?>
