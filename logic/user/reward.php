<?php
$MSG_REWARD_BOUGHT='Recompensa já adquirida.';

if(isset($_GET['id'])){

  //Get reward info
  $ENDPOINT='/Rewards/Detail/' . $_GET['id'];
  ob_start();
  require($_SERVER['DOCUMENT_ROOT'] . '/logic/api/api-user.php');
  $reward_json=ob_get_contents();
  ob_end_clean();
  $reward_info=json_decode($reward_json,true);
  
  //Get user points
  $ENDPOINT='/User/Home';
  ob_start();
  require($_SERVER['DOCUMENT_ROOT'] . '/logic/api/api-user.php');
  $user_json=ob_get_contents();
  ob_end_clean();
  $user_info=json_decode($user_json,true);
  //
  $my_coins=$user_info['data']['cashe'];
  
  
  if($reward_info['erro']){
?>
<div class="reward-details">
  <p class="invalid"><?php $reward_info['message']; ?></p>
</div>
<?php
  } else { // No errors
  $reward_data=$reward_info['data'];
  $rescued=$reward_data['isRescued'];
?>
<div class="reward-details<?php if($rescued){ echo ' rescued';} ?>">
  <div class="rewards-header">
    <h2>Recompensas</h2>
    <div class="points"><img src="/sites/default/files/images/icons/coin.png"> <?php echo $my_coins; ?></div>
  </div>
  <div class="white-box">
    <a class="window-close"></a>
    <div class="image">
      <img src="<?php echo $reward_data['photo']; ?>">
    </div><div class="details">
      <h2><?php echo $reward_data['name']; ?></h2>
      <p class="description">
        <?php echo $reward_data['description']; ?>
      </p>
      
<?php if(!empty($reward_data['specifications'])){ ?>
      <p class="title">Especificações</p>
      <ul>
<?php foreach($reward_data['specifications'] as $point){ ?>
        <li><?php echo $point; ?></li>
<?php } ?>
      </ul>
<?php } ?>
    </div>
    <div class="buy">
      <img src="/sites/default/files/images/icons/coin.png">
      <span class="price"><?php echo $reward_data['price']; ?></span>
<?php if($rescued){ ?>
      <span class="cta"><?php echo $MSG_REWARD_BOUGHT; ?></span>
<?php } else { ?>
      <a class="cta" href="/reward/buy?id=<?php echo $reward_data['id']; ?>">Resgatar prêmio</a>
<?php } ?>
    </div>
  </div>
</div>
<?php
  } // End if no errors


} else {
  //Nothing to see here
  
} // End if no ID
?>