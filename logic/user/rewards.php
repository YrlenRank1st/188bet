<?php
require_once('loggedin.php');
require_once($_SERVER['DOCUMENT_ROOT'] . '/logic/functions.php');

//Get rewards
$ENDPOINT='/Rewards';
ob_start();
require($_SERVER['DOCUMENT_ROOT'] . '/logic/api/api-user.php');
$rewards_json=ob_get_contents();
ob_end_clean();
$rewards_info=json_decode($rewards_json,true);
//End get rewards

//Get user points
$ENDPOINT='/User/Home';
ob_start();
require($_SERVER['DOCUMENT_ROOT'] . '/logic/api/api-user.php');
$user_json=ob_get_contents();
ob_end_clean();
$user_info=json_decode($user_json,true);
//
$my_coins=$user_info['data']['cashe'];

if($rewards_info['erro']){
  error_log($rewards_json);
?>
<div class="rewards">
  <p class="invalid"><?php echo $rewards_info['message']; ?></p>
</div>
<?php
} else { //
  $rewards_data=&$rewards_info['data'];
  $rewards=&$rewards_data['rewards'];
?>
<div class="rewards">
  <div class="rewards-header">
    <h1>Recompensas</h1>
    <div>
      <div class="label">Faixa de valor</div>
      <div class="rewards-slider" data-low="<?php echo $rewards_data['minPrice']; ?>" data-high="<?php echo $rewards_data['maxPrice']; ?>"></div>
      <div class="points"><img src="/sites/default/files/images/icons/coin.png"> <?php echo $my_coins; ?></div>
    </div>
  </div>
  <div id="reward-list" class="reward-list">
<?php foreach($rewards as &$reward){
  $price=str_replace(',','.',$reward['price']);
  $rescued=$reward['isRescued'];
?><div class="reward<?php if($rescued){ echo ' rescued'; } ?>" data-price="<?php echo $price; ?>">
      <div class="white-box">
        <div class="image reward-image">
          <a href="/reward?id=<?php echo $reward['id']; ?>">
            <img src="<?php echo $reward['photo']; ?>">
          </a>
        </div>
        <div class="reward-info">
          <h3>
            <a href="/reward?id=<?php echo $reward['id']; ?>">
              <?php echo $reward['name']; ?>
            </a>
          </h3>
          <p<?php if($reward['isRecued']){ echo ' class="used"';} ?>><img src="/sites/default/files/images/icons/coin.png"> <?php echo $reward['price']; ?></p>
        </div>
      </div>
    </div><?php } ?>
  </div>
</div>
<script type="text/javascript" src="/logic/js/rewards.js" defer></script>
<?php
unset($rewards);
unset($reward_data);
} // End reward

?>