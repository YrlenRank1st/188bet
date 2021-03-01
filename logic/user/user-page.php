<?php

require_once('loggedin.php');
require($_SERVER['DOCUMENT_ROOT'] . '/logic/api/params.php');
$USER_LEVEL_NAMES=array(
  1=>'Amador',
  2=>'Júnior',
  3=>'Profissional',
  4=>'Lenda'
);


//$user_info=get_user_info();
$error_message=null;
$user_id= $_GET['id'];
/* Get API data */
//Get user points
ob_start();
$ENDPOINT='/User/HomeByFriend/' . $user_id;
require($_SERVER['DOCUMENT_ROOT'] . '/logic/api/api-user.php');
$user_json=ob_get_contents();
ob_end_clean();
$user_home_info=json_decode($user_json,true);
//Get user bets
ob_start();
$ENDPOINT='/User/RoundsUserByFriend/' . $user_id . '/1';
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
  $user_image=$user_home_data['photo'];
  $user_level=((int)$user_home_data['level'])+1;
  $level_image=get_level_image($user_level);
}
//Manage rounds data
$rounds=array();
if($rounds_info['erro']==true || empty($rounds_info['data'])){
  $rounds=array();
} else {
  $rounds=$rounds_info['data'];
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
      </div><div class="name">
<?php if(isset($error_message)){echo $error_message;} ?>
        <span class="real-name"><?php echo $real_name; ?></span>
        <span class="username">@<?php echo $user_name; ?></span>
        <span class="user-level"><?php echo isset($USER_LEVEL_NAMES[$user_level])?$USER_LEVEL_NAMES[$user_level]:'Nível ' . $user_level; ?></span>
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
  render_round($round,$user_id);
}
?>

<?php } ?>
    </div>
  </div>
</div>

<?php
function render_round($round,$user_id){
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
    <a href="/user/round?user=<?php echo $user_id; ?>&id=<?php echo $round['roundId']; ?>"><?php echo $round['name']; ?></a>
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