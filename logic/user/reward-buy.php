<?php
$ERR_CITIES_NOT_LOADED='Não foi possível carregar as cidades.';
$MSG_ITEM_BOUGHT='Você já adquiriu este item';

require_once($_SERVER['DOCUMENT_ROOT'] . '/logic/functions.php');
$error_message=null;
$name=$cpf=$cep=$address=$complement=
  $p_state=$p_city=$telephone='';
$item_bought=false;
if(isset($_POST['name'])){
  $name=isset($_POST['name'])?$_POST['name']:'';
  $cpf=isset($_POST['cpf'])?$_POST['cpf']:'';
  $cep=isset($_POST['cep'])?$_POST['cep']:'';
  $address=isset($_POST['address'])?$_POST['address']:'';
  $p_state=isset($_POST['state'])?$_POST['state']:'';
  $p_city=isset($_POST['city'])?$_POST['city']:'';
  $complement=isset($_POST['complement'])?$_POST['complement']:'';
  $telephone=isset($_POST['telephone'])?$_POST['telephone']:'';
  
  do{
    //Get city data
    $old_get=$_GET;
    $_GET['stateId']=$p_state;
    ob_start();
    include($_SERVER['DOCUMENT_ROOT'] . '/logic/api/cities.php');
    $cities_json=ob_get_contents();
    ob_end_clean();
    $_GET=$old_get;
    //
    $city_id=null;
    $cities_info=json_decode($cities_json,true);
    $cities=$cities_info['data'];
    foreach($cities as $city){
      if($p_city===$city['name']){
        $city_id=$city['id'];
      }
    }
    if(!isset($city_id)){
      $error_message='<p class="invalid">' . $ERR_CITIES_NOT_LOADED . '</p>';
      break;
    }
    //
    $JSON_DATA=array(
      'rewardId'=>$_POST['rewardId'],
      'name'=>$name,
      'cpf'=>$cpf,
      'cep'=>$cep,
      'address'=>$address,
      'complement'=>$complement,
      'stateId'=>$p_state,
      'cityId'=>$city_id,
      'telephone'=>$telephone
    );
    $ENDPOINT='/Rewards/Rescue';
    ob_start();
    require($_SERVER['DOCUMENT_ROOT'] . '/logic/api/api-post-user.php');
    $buy_json=ob_get_contents();
    ob_end_clean();
    $buy_info=json_decode($buy_json,true);
    //
    if($buy_info['erro']){
      $error_message='<p class="invalid">' . $buy_info['message'] . '</p>';
    } else {
      $error_message='<p class="success">' . $buy_info['message'] . '</p>';
      $item_bought=true;
    }
    
  
  }while(0);

}

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
<div class="reward-buy">
  <p class="invalid"><?php $reward_info['message']; ?></p>
</div>
<?php
  } else { // No errors
  $reward_data=$reward_info['data'];
  if($reward_data['isRescued']){
    $item_bought=true;
    $error_message='<p class="success">' . $MSG_ITEM_BOUGHT . '</p>';
  }
?>
<div class="reward-buy">
  <div class="rewards-header">
    <h2>Resgatar</h2>
    <div class="points"><img src="/sites/default/files/images/icons/coin.png"> <?php echo $my_coins; ?></div>
  </div>
  <div class="white-box">
    <a class="window-close"></a>
    <div class="buy-reward-info">
      <p class="title">Prêmio</p>
      <div class="image"><img src="<?php echo $reward_data['photo']; ?>"></div>
      <p class="title"><?php echo $reward_data['name']; ?></p>
      <p><img src="/sites/default/files/images/icons/coin.png"><span class="price"><?php echo $reward_data['price']; ?></span>
    </div><div class="form">
<?php if($item_bought){ ?>
<?php if(isset($error_message)){ echo $error_message; } ?>
<?php } else {
  //Get states
  $states=get_states();
?>
      <form class="main-form" method="POST">
        <input type="hidden" name="rewardId" value="<?php echo $reward_data['id']; ?>">
        <p class="title">Dados para entrega</p>
<?php if(isset($error_message)){ echo $error_message; } ?>
        <div class="form-column">
          <div class="field">
            <label for="reward-name">
              Nome *
            </label><input id="reward-name"
            type="text" name="name"
            value="<?php echo htmlentities($name); ?>"
            required
            >
          </div><div class="field">
            <label for="reward-cpf">
              CPF *
            </label><input id="reward-cpf"
            type="text" name="cpf"
            value="<?php echo htmlentities($cpf); ?>"
            required
            >
          </div><div class="field">
            <label for="reward-cep">
              CEP *
            </label><input id="reward-cep"
            type="text" name="cep"
            value="<?php echo htmlentities($cep); ?>"
            required
            >
          </div><div class="field">
            <label for="reward-address">
              Endereço *
            </label><input id="reward-address"
            type="text" name="address"
            value="<?php echo htmlentities($address); ?>"
            required
            >
          </div>
        </div><div class="form-column">
          <div class="field">
            <label for="reward-complement">
              Complemento
            </label><input id="reward-complement"
            type="text" name="complement"
            value="<?php echo htmlentities($complement); ?>"
            >
          </div><div class="field field-select">
            <div class="select-state">
              <select name="state">
                <option value="">Estado</option>
<?php foreach($states as $state){ ?>
                <option value="<?php echo $state['id']; ?>"
                <?php if(!empty($p_state) && $p_state===$state['id']){echo  'selected';} ?>><?php echo $state['name']; ?></option>
<?php } ?>
              </select>
            </div>
          </div><div class="field field-select" id="field-city">
            <label for="reward-city"
            >Cidade *</label><input id="reward-city"
            type="text" name="city"
            value="<?php echo htmlentities($p_city); ?>"
            required
            >
          </div><div class="field">
            <label for="reward-telephone">
              Telefone
            </label><input id="reward-telephone"
            type="text" name="telephone"
            value="<?php echo htmlentities($telephone); ?>"
            required
            >
          </div>
        </div>
        <div class="field submit">
          <button class="cta" type="submit">Finalizar</button>
        </div>
      </form>
      <script type="text/javascript" src="/logic/js/cities.js"></script>
<?php } // End if item not bought ?>
    </div>
  </div>
</div>
<?php
  } // End if no errors


} else {
  //Nothing to see here
  
} // End if no ID
?>