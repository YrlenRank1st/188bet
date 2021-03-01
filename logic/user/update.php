<?php
require_once('loggedin.php');
/** DICTIONARY **/
global $TU_USER; // Translation: Update User
$TU_USER=array(
  'state_not_found'=>'O estado não foi encontrado neste país.',
  'city_not_found'=>'A cidade não foi encontrada neste estado.',
  'team_not_found'=>'O time escolhido não foi encontrado na lista.'
);
function tu_user($code){
  global $TU_USER;
  return isset($TU_USER[$code])?$TU_USER[$code]:'Error: ' . $code;
}
/** END DICTIONARY **/

require_once($_SERVER['DOCUMENT_ROOT'].'/logic/functions.php');

//Save memory and time, avoid copying these large arrays twice.
global $X_TEAMS;
global $X_STATES;
global $X_USER_INFO;
$X_TEAMS=get_teams();
$X_STATES=get_states();
$X_USER_INFO=$user_info=get_user_info();
$first_name=$last_name=$email=$state=$city=$team=null;
$errors=null;
if(isset($_POST['first'])){
  $errors=send_user_update($_POST);
  $first_name=$_POST['first'];
  $last_name=$_POST['last'];
  $email=$_POST['email'];
  $state=$_POST['state'];
  $city=$_POST['city'];
  $team=$_POST['team'];
}
if($user_info['erro']){
  error_log('Update data: Error getting user data: ' . $user_info['message']);
} else {
  $user_data=$user_info['data'];
  if(!isset($first_name)){ $first_name=$user_data['firstName']; }
  if(!isset($last_name)){ $last_name=$user_data['lastName']; }
  if(!isset($email)){ $email=$user_data['email']; }
  if(!isset($state)){ $state=$user_data['state']['id']; }
  if(!isset($city)){ $city=$user_data['citie']['name']; }
  if(!isset($team)){ $team=$user_data['team']['id']; }
  
}

//firstName, state, lastName, citie, email, team
//Change password. Save.

?>
<div class="user-update">
  <h2>Editar conta</h2>
  <div class="white-box update-data">
    <form method="POST" class="main-form">
<?php if(!empty($errors)){ ?>
      <div class="field">
<?php foreach($errors as $error){ echo $error; } ?>
      </div>
<?php } ?>
      <div class="form-column">
        <div class="field">
          <label for="signup-first"
          >Nome</label><input id="signup-first"
          name="first" type="text"
          value="<?php echo $first_name; ?>">
        </div><div class="field">
          <label for="signup-last"
          >Sobrenome</label><input id="signup-last"
          name="last" type="text"
          value="<?php echo $last_name; ?>">
        </div><div class="field">
          <label for="signup-email"
          >E-mail</label><input id="signup-email"
          name="email" type="email"
          value="<?php echo $email; ?>">
        </div>
      </div><div class="form-column">
        <div class="field field-select">
          <div class="select-state">
            <select name="state">
              <option value="">Estado *</option>
<?php foreach($X_STATES as &$state0){ ?>
              <option value="<?php echo $state0['id']; ?>"<?php
              if($state===$state0['id']){ echo ' selected'; }
              ?>><?php echo $state0['name']; ?></option>
<?php } ?>
            </select>
          </div>
        </div><div class="field field-select" id="field-city">
          <label for="signup-city"
          >Cidade *</label><input id="signup-city"
          name="city" type="text"
          value="<?php echo $city; ?>">
        </div><div class="field field-select">
          <div class="select-team">
            <select name="team">
              <option value="">Time do coração</option>
<?php foreach($X_TEAMS as &$team0){
  //Team has the format: name,photo,id
?>
              <option value="<?php echo $team0['id']; ?>"<?php
              if($team===$team0['id']){ echo ' selected'; }
              ?>><?php echo $team0['name']; ?></option>
<?php } ?>
            </select>
          </div>
        </div>
      </div>
      <div class="submit">
        <div class="field form-column">
          <a href="/user/change-password">Alterar senha</a>
        </div><div class="field form-column">
          <button type="submit" class="cta">Salvar</button>
        </div>
      </div>
    </form>
    <script src="/logic/js/cities.js" async defer></script>
  </div>
</div>
<?php

function send_user_update(&$data){
  /* Data format is:
   * {first,last,email,state,city,team}
   */
  global $X_TEAMS;
  global $X_STATES;
  global $X_USER_INFO;
  $output=array();
  $errors=array();
  $user_data=&$X_USER_INFO['data'];
  /* 1- Validate some items */
  $state_id=null;
  if(!empty($data['state'])){
    $state_id=$data['state'];
    $output['state']=array('id'=>$state_id);
  }
  // 2- Validate city
  if(!empty($data['city'])){
    //Load city list
    $old_get=$_GET;
    $_GET['stateId']=$state_id;
    ob_start();
    include($_SERVER['DOCUMENT_ROOT'] . '/logic/api/cities.php');
    $cities_json=ob_get_contents();
    ob_end_clean();
    $_GET=$old_get;
    $cities_info=json_decode($cities_json,true);
    //Get city ID
    if($cities_info['erro']==true){
      $errors[]=$cities_info['message'];
    } else {
      $city_name=strtolower(normalize_special_chars(trim($data['city'])));
      $city_id=null;
      $cities=&$cities_info['data'];
      foreach($cities as &$city){
        if(
          strtolower(normalize_special_chars($city['name']))===$city_name
        ){ // City is not in list
            $city_id=$city['id'];
            $city_name=$city['name'];
            break;
        }
      }
      if(!isset($city_id)){
        $errors[]=tu_user('city_not_found');
      } else {
        $output['citie']=array('id'=>$city_id);
      }
      
    }
  }
  // 3- Validate teams
  if(!empty($data['team'])){
    $team_id=$data['team'];
    $output['team']=array('id'=>$team_id);
  }
  
  /* 2- Insert some items */
  if(strlen(trim($data['first']))>0){
    $output['firstName']=trim($data['first']);
  }
  if(strlen(trim($data['last']))>0){
    $output['lastName']=trim($data['last']);
  }
  if(strlen(trim($data['email']))>0){
    $output['email']=$data['email'];
  }
  if(!empty($errors)){ return $errors;}
  // Add extra fields
  $photo=$X_USER_INFO['data']['photo'];
  $slash_pos=strrpos($photo,'/');
  if($slash_pos===false){
    $photo_file=$photo;
  } else {
    $photo_file=substr($photo,($slash_pos+1),strlen($photo)-($slash_pos+1));
  }
  $output['photo']=$photo_file;
  /* 3- Send API output */
  $ENDPOINT='/User/Update';
  $JSON_DATA=$output;
  ob_start();
  include($_SERVER['DOCUMENT_ROOT'] . '/logic/api/api-post-user.php');
  $json=ob_get_contents();
  ob_end_clean();
  $result=json_decode($json,true);
  $messages=array();
  if($result['erro']){
    if(isset($result['data']['errors'])){
      foreach($result['data']['errors'] as &$err){
        foreach($err as &$err_item){
          $messages[]='<p class="invalid">' . $err_item . '</p>';
        }
      }
      unset($err_item);
      unset($err);
    } else if(isset($result['data']['message'])){
      $messages[]='<p class="invalid">' . $result['data']['message'] . '</p>';
    } else {
      $messages[]='<p class="invalid">' . (!empty($result['messageEx'])?$result['messageEx']:$result['message']) . '</p>';
    }
  } else {
    unset($_SESSION['bet188']['cache']['user_info']);
    $messages[]='<p class="success">' . $result['message'] . '</p>';
  }
  return $messages;
}

?>