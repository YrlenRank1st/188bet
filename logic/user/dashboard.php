<?php
/** Dictionary **/
$T_IMG_ERR=array(
  'not_logged_in'=>'Você não está logado.',
  'no_image_file'=>'O arquivo não foi enviado.',
  'not_image'=>'O arquivo enviado não era uma imagem.'
);
$USER_LEVEL_NAMES=array(
  1=>'Amador',
  2=>'Júnior',
  3=>'Profissional',
  4=>'Lenda'
);
/** End dictionary **/

require_once('loggedin.php');
require($_SERVER['DOCUMENT_ROOT'] . '/logic/api/params.php');

$user_info=get_user_info();
$error_message=null;
/*** SUBMIT IMAGE ***/
if(!empty($_FILES['image-upload']['size'])){
  ob_start();
  require_once($_SERVER['DOCUMENT_ROOT'] . '/logic/api/file-upload.php');
  $image_upload_result=ob_get_contents();
  ob_end_clean();
  $image_upload_info=json_decode($image_upload_result,true);
  
  if($image_upload_info['erro']){
    if($image_upload_info['message']==='not_logged_in'
    || $image_upload_info['message']==='no_image_file'
    || $image_upload_info['message']==='not_image'){
      $image_upload_info['message']=$T_IMG_ERR[$image_upload_info['message']];
    }
    $error_message='<p class="invalid">' . $image_upload_info['message'] . '</p>';
      file_put_contents(
      $_SERVER['DOCUMENT_ROOT'] . '/error_log',
      '[' . date('Y-m-d H:i:s') . '] ' . 
      '[Image Upload] ' . $image_upload_result
      . "\n",
      FILE_APPEND);
  } else {
    $output=array();
    
    $output['photo']=$image_upload_info['data']['fileName'];
    //
    $user_data=$user_info['data'];
    $output['firstName']=$user_data['firstName'];
    $output['lastName']=$user_data['lastName'];
    $output['email']=$user_data['email'];
    $output['state']=array('id'=>$user_data['state']['id']);
    $output['citie']=array('id'=>$user_data['citie']['id']);
    $output['team'] =array('id'=>$user_data['team']['id']);
    
    $ENDPOINT='/User/Update';
    $JSON_DATA=$output;
    
    ob_start();
    include($_SERVER['DOCUMENT_ROOT'] . '/logic/api/api-post-user.php');
    $json=ob_get_contents();
    ob_end_clean();
    $result=json_decode($json,true);
    $messages=array();
    if($result['erro']){
      $error_message='<p class="invalid">' . $result['message'] . '</p>';
      file_put_contents(
      '[' . date('Y-m-d H:i:s') . '] ' . 
      $_SERVER['DOCUMENT_ROOT'] . '/error_log',
      '[Image change] ' . $json
      . "\n",
      FILE_APPEND);
    } else {
      unset($_SESSION['bet188']['cache']['user_info']);
      $user_info=get_user_info();
      $error_message='<p class="success">' . $result['message'] . '</p>';
    }
    
  } // End if image uploaded successfully
  
} // End if image exists

/*** END SUBMIT IMAGE ***/

/*** CONNECT FACEBOOK ***/
if(isset($_POST['fb-connect'])){
  $user_data=&$user_info['data'];
  //Set photo
  $photo=$user_data['photo'];
  $slash_pos=strrpos($photo,'/');
  if($slash_pos===false){
    $photo_file=$photo;
  } else {
    $photo_file=substr($photo,($slash_pos+1),strlen($photo)-($slash_pos+1));
  }
  //JSON
  $JSON_DATA=array(
    'firstName'  => $user_data['firstName'],
    'lastName'   => $user_data['lastName'],
    'email'      => $user_data['email'],
    'state'      => array('id'=>$user_data['state']['id']),
    'citie'      => array('id'=>$user_data['citie']['id']),
    'team'       => array('id'=>$user_data['team']['id']),
    'photo'      => $photo_file,
    'facebookId' => $_POST['facebookId']
  );
  unset($user_data);
  //Update
  $ENDPOINT='/User/Update';
  ob_start();
  include($_SERVER['DOCUMENT_ROOT'] . '/logic/api/api-post-user.php');
  $json=ob_get_contents();
  ob_end_clean();
  $result=json_decode($json,true);
  $messages=array();
  if($result['erro']){
    $error_message='<p class="invalid">' . $result['message'] . '</p>';
  } else {
    unset($_SESSION['bet188']['cache']['user_info']);
    $user_info=get_user_info();
    $error_message='<p class="success">' . $result['message'] . '</p>';
  }
  
}
/*** END CONNECT FACEBOOK ***/
/*** DISCONNECT FACEBOOK ***/
if(isset($_POST['fb-disconnect'])){
  $user_data=&$user_info['data'];
  $JSON_DATA=array(
    'firstName'  => $user_data['firstName'],
    'lastName'   => $user_data['lastName'],
    'email'      => $user_data['email'],
    'state'      => array('id'=>$user_data['state']['id']),
    'citie'      => array('id'=>$user_data['citie']['id']),
    'team'       => array('id'=>$user_data['team']['id']),
    'unlinkFacebook' => true
  );
  unset($user_data);
  
  $ENDPOINT='/User/Update';
  ob_start();
  include($_SERVER['DOCUMENT_ROOT'] . '/logic/api/api-post-user.php');
  $json=ob_get_contents();
  ob_end_clean();
  $result=json_decode($json,true);
  $messages=array();
  if($result['erro']){
    $error_message='<p class="invalid">' . $result['message'] . '</p>';
  } else {
    unset($_SESSION['bet188']['cache']['user_info']);
    $user_info=get_user_info();
    $error_message='<p class="success">' . $result['message'] . '</p>';
  }
  
}
/*** END DISCONNECT FACEBOOK ***/


/* Get API data */
//Get user points
ob_start();
$ENDPOINT='/User/Home';
require($_SERVER['DOCUMENT_ROOT'] . '/logic/api/api-user.php');
$user_json=ob_get_contents();
ob_end_clean();
$user_home_info=json_decode($user_json,true);

//Get user bets
ob_start();
$ENDPOINT='/User/RoundsUser';
require($_SERVER['DOCUMENT_ROOT'] . '/logic/api/api-user.php');
$rounds_json=ob_get_contents();
ob_end_clean();
$rounds_info=json_decode($rounds_json,true);

/* End get API data */

//Manage user data
$user_data=null;
if($user_home_info['erro']){
} else {
  $user_home_data=$user_home_info['data'];
  if(!(strpos($user_image,'http')===0)){
   $user_image=$API_IMAGE_ROOT . $user_image;
  }
  $real_name=$user_home_data['firstName'] . ' ' . $user_home_data['lastName'];
  $user_name=$user_home_data['username'];
  $coins=$user_home_data['cashe'];
  $points=$user_home_data['score'];
  $position=$user_home_data['position'];
  $medals=$user_home_data['medals'];
  
  $user_image=$level_image='';
  if(isset($user_info)){
    $user_image=$user_info['data']['photo'];
    $user_level=((int)$user_info['data']['level'])+1;
    $level_image=get_level_image($user_level);
  }
}
//Manage rounds data
$rounds=array();
if($rounds_info['erro']==true || empty($rounds_info['data'])){
  $rounds=array();
} else {
  $rounds=$rounds_info['data'];
}

$fb_id=null;
if($user_info['data']['facebookId']){
  $fb_id=$user_info['data']['facebookId'];
}

?>
<div class="dashboard">
  <div class="current">
<?php if(empty($user_home_data)){ ?>
    <p>Desculpe, aconteceu um erro.</p>
<?php } else { ?>
    <div class="profile">
      <div class="user-image">
        <img src="<?php echo $user_image; ?>" alt="<?php echo $user_name; ?>">
<?php if(isset($level_image)){ ?>
        <img class="level-image" src="<?php echo $level_image; ?>" >
<?php } ?>
<?php //if(empty($fb_id)){ ?>
        <a class="edit-user-image" href=""></a>
        <form class="user-image-form" method="POST" enctype="multipart/form-data">
          <input type="file" name="image-upload">
          <button type="submit">Enviar</button>
        </form>
<?php //}  // End if empty FB ID ?>
      </div><div class="name">
<?php if(isset($error_message)){echo $error_message;} ?>
        <span class="real-name"><?php echo $real_name; ?></span>
        <span class="username">@<?php echo $user_name; ?></span>
        <span class="user-level"><?php echo isset($USER_LEVEL_NAMES[$user_level])?$USER_LEVEL_NAMES[$user_level]:'Nível ' . $user_level; ?></span>
        <a class="edit-profile-btn" href="/user/update"><img src="/sites/default/files/images/icons/edit.png" alt="Editar conta"></a>
<?php if(empty($user_info['data']['facebookId'])) { ?>
        <a class="fb-connect cta facebook" href="">Conecte o Facebook</a>
<?php } else { ?>
        <form method="POST" id="fb-disconnect-form">
          <input type="hidden" name="fb-disconnect" value="1">
          <button class="cta facebook" type="submit">Desconecte do Facebook</button>
        </form>
<?php } ?>
      </div>
    </div>
    <div class="stats white-box">
      <div class="stat">
        <p class="stat-name">Moedas</p>
        <p class="state-value">
          <img src="/sites/default/files/images/icons/coin.png">
          <span><?php echo $coins; ?></span>
        </p>
      </div><div class="stat">
        <p class="stat-name">Pontos</p>
        <p class="state-value">
          <img src="/sites/default/files/images/icons/stars.png">
          <span><?php echo $points; ?></span>
        </p>
      </div><div class="stat">
        <p class="stat-name">Posição</p>
        <p class="state-value">
          <img src="/sites/default/files/images/icons/cup.png">
          <span><?php echo $position; ?></span>
        </p>
      </div><div class="stat">
        <p class="stat-name">Medalhas</p>
        <p class="state-value">
          <img src="/sites/default/files/images/icons/medal.png">
          <span><?php echo $medals; ?></span>
        </p>
      </div>
    </div>
    
<?php } ?>
  </div><div class="rounds">
    <div>
<?php if(!empty($rounds)){ ?>
    <h2>Rodadas anteriores</h2>
<?php
foreach($rounds as $round){
  render_round($round);
}
?>

<?php } ?>
    </div>
  </div>
</div>
<script type="text/javascript" src="/logic/js/dashboard.js" defer></script>

<?php
function render_round($round){
  /* Renders the round. Format:
    array(
      'userId'=> null,
      'beginDate'=> Timestamp,
      'endDate'=> Timestamp,
      'name'=> 'string',
      'points'=> Number,
      'position'=> Number,
      'id'=> 'String'
    )
  */
  $start_date=date('d/m',$round['beginDate']);
  $end_date=date('d/m',$round['endDate']);

?><div class="round">
  <div class="date">
    <span class="start"><?php echo $start_date; ?></span>
    -
    <span class="end"><?php echo $end_date; ?></span>
  </div><div class="name">
    <a href="/user/round?id=<?php echo $round['roundId']; ?>"><?php echo $round['name']; ?></a>
  </div><div class="rank">
    <div>
      <p class="item">Pontos</p>
      <p class="value">
        <img src="/sites/default/files/images/icons/stars.png">
        <?php echo $round['points']; ?>
      </p>
    </div><div>
      <p class="item">Posição</p>
      <p class="value">
        <img src="/sites/default/files/images/icons/cup.png">
        <?php echo $round['position']; ?>
      </p>
    </div>
  </div>
</div><?php
} // End render_round()

?>