<?php
require_once('loggedin.php');
$bet_message=null;
/** SEND BET **/
if(isset($_POST['bet'])){
  $ENDPOINT='/Bet';
  $JSON_DATA=array();
  $JSON_DATA['userChoice']=isset($_POST['choice'])?$_POST['choice']:null;
  $JSON_DATA['type']=isset($_POST['type'])?$_POST['type']:null;
  $JSON_DATA['gameId']=isset($_POST['id'])?$_POST['id']:null;
  ob_start();
  require($_SERVER['DOCUMENT_ROOT'] . '/logic/api/api-post-user.php');
  $bet_json=ob_get_contents();
  ob_end_clean();
  $bet_info=json_decode($bet_json,true);
  if($bet_info['erro']){
    $bet_message='<p class="invalid">' . $bet_info['message'] . '</p>';
  } else if(isset($bet_info['message'])){
    $bet_message='<p class="success">' . $bet_info['message'] . '</p>';
  }
}

/** END BET **/

$match_id=isset($_GET['id'])?$_GET['id']:'0';
ob_start();
$ENDPOINT='/Game/Detail/' . $match_id;
require($_SERVER['DOCUMENT_ROOT'] . '/logic/api/api-user.php');
$match_json=ob_get_contents();
ob_end_clean();
//echo ' * ' . $match_json . ' * ';
$match_info=json_decode($match_json,true);
//print_r($match_info);
if($match_info['erro']){
?>
<div class="match-card white-box">
  <p class="invalid"><?php echo $match_info['message']; ?></p>
</div>
<?php
} else {
?><div class="match-card-list"><?php
  $match_data=&$match_info['data'];
  $match_cards=&$match_data['cards'];
  $match_id=$match_data['id'];
  $position=0;
  $league_name=$match_data['league'];
  foreach($match_cards as &$match_card){
    if(isset($_POST['id']) && $match_id===$_POST['id']){
      render_match_card($match_card,$match_id,$position,$bet_message,$league_name);
    } else {
      render_match_card($match_card,$match_id,$position,null,$league_name);
    }
    $position++;
  }
  unset($match_card);
  unset($match_cards);
  unset($match_data);
?></div><?php
}

/***************/
/** FUNCTIONS **/
/***************/
function get_option_class($option,$checked,$win_option){
  if(!isset($win_option)){
    if($checked){
      return 'checked';
    } else {
      return 'unimportant';
    }
  } else if($option==$win_option){
    return 'correct';
  } else if($checked){
    return 'incorrect';
  } else {
    return 'unchecked';
  }
}
function get_answer($option,$checked,$win_option){
  if(!isset($win_option)){
    return '<p class="answer"></p>';
  } else if($option==$win_option){
    return '<p class="answer correct"><img src="/sites/default/files/images/icons/correct.png">Resposta correta</p>';
  } else if($checked){
    return '<p class="answer incorrect"><img src="/sites/default/files/images/icons/incorrect.png">Resposta incorreta</p>';
  } else {
    return '<p class="answer"></p>';
  }
}

function render_match_card(&$card,$match_id,$position,$message=null,$league_name=null){
  /* Renders a card representing one way to bet on a match.
   * Format:
    {
      "title":"Amistoso Internacional",
      "question":"Resultado Final (1X2)",
      "type":3,
      "checked":false,
      "options":[
        {
          "value":"Menos de 2.5",
          "points":98.5,
          "position":1,
          "team":{
            "name":"ASA",
            "photo":"http://api.188bet.dev.megaleios.kinghost.net/content/upload/imageNotFound.jpg",
            "id":"5a1ef7cb5ab25c3d8c905217"
          },
          "checked":false
        }
      ]
    }
  */
  $checked=$card['checked'];
?>
<div class="match-card-box">
  <div class="match-card white-box<?php if($checked){echo ' checked';} ?>">
    <a href="" class="window-close"></a>
<?php if(!$checked){ ?>
    <form method="POST">
      <input type="hidden" name="bet" value="1">
      <input type="hidden" name="id" value="<?php echo $match_id; ?>">
      <input type="hidden" name="type" value="<?php echo $card['type']; ?>">
 <?php } ?>
      <header class="card-header">
        <p class="card-league-name"><?php echo $league_name; ?></p>
        <h2><?php echo $card['title']; ?></h2>
        <p class="card-question"><?php echo $card['question']; ?></p>
<?php if(isset($message)){echo $message;} ?>
      </header>
      <div class="card-options">
<?php
  $win_status=-1;
  switch($card['type']){
    case 0:
      $win_status=render_card_yes_no($card,$position);
      break;
    case 1:
      $win_status=render_card_handicap($card,$position);
      break;
    case 2:
      $win_status=render_card_over_under($card,$position);
      break;
    case 3:
      $win_status=render_card_1x2($card,$position);
      break;
  }
?>
      </div>
      <footer class="card-footer">
<?php
    if($win_status==-1 && $checked){
    ?>
        <p class="waiting"><img src="/sites/default/files/images/icons/waiting.png">Aguardando resposta do jogo...</p>
<?php } else if($win_status==1) { ?>
      <p>Você ganhou</p>
<?php 
$win_option=$card['positionWin'];
$options=&$card['options'];
$win_value=0;
foreach($options as &$option){
  if($option['position']==$win_option){
    $win_value=$option['points'];
  }
} unset($option);
unset($options);
?>
      <p class="winnings"><img src="/sites/default/files/images/icons/stars.png"> <?php echo $win_value; ?><span class="points">pts</span></p>
<?php } else if($win_status==0
      || (isset($card['positionWin'])
         && $card['positionWin']>=0
         && $card['positionWin']<=3 ) ){ ?>

<?php } else if(!$checked){ ?>
        <p>Você não poderá alterar seu chute. Tem certeza da sua escolha?</p>
        <button class="cta" type="submit">OK</a>
<?php } ?>
      </footer>
<?php if(!$checked){ ?>
    </form>
<?php } ?>
  </div>
</div>
<?php

} // End render match card

function render_card_1x2(&$card,$position){

  /* Arrange list of options */
  //Order Left, Top, Bottom, Right
  $options_order=array('2','0','1','3');
  $options=array();
  $card_options=&$card['options'];
  foreach($card_options as &$option){
    $options[$option['position']]=$option;
  }
  unset($option);
  /* End list of options */
  
  $checked=$card['checked'];
  $winner=(isset($card['positionWin']) && $card['positionWin']<=3 && $card['positionWin']>=0)?$card['positionWin']:null;
  $is_open=(!$checked && !isset($winner));
  $win_status=-1;
  
  foreach($options_order as $option_number){
    if(!isset($options[$option_number])){continue;}
    $option=&$options[$option_number];
    if(isset($winner) && $option['checked']){
      if($option_number==$winner){
        $win_status=1;
      } else {
        $win_status=0;
      }
    }
    ?><div class="card-option">
<?php if($is_open) { ?>
        <input id="option-<?php echo $position; ?>-<?php echo $option_number; ?>" type="radio" name="choice"
        value="<?php echo $option_number; ?>">
<?php } ?>
<?php if($option['checked']){?>
        <p class="your-bet">Seu Chute</p>
<?php } ?>
        <div<?php
  echo ' class="' . get_option_class($option_number,$option['checked'],$winner) . '"';
?>>
<?php if($is_open) { ?>
          <label for="option-<?php echo $position; ?>-<?php echo $option_number; ?>"></label>
<?php } ?>
          <div class="team">
<?php if(isset($option['team'])){ ?>
            <img src="<?php echo $option['team']['photo']; ?>">
<?php } else { ?>
            <img src="/sites/default/files/images/extern/tied.png">
<?php } ?>
<?php if(isset($option['team'])){ ?>
            <p class="card-option-name"><?php echo $option['team']['name']; ?></p>
<?php } else { ?>
            <p class="card-option-name">Empate</p>
<?php } ?>
          </div>
          <div class="value">
            <p class="text">Valendo</p>
            <p class="points">
              <img src="/sites/default/files/images/icons/stars.png">
              <?php echo round($option['points'],2); ?><span class="unit">pts</span>
            </p>
          </div>
        </div>
<?php
if(isset($winner)){
  echo get_answer($option_number,$option['checked'],$winner);
}
?>
      </div><?php

  } // End for options
  return $win_status;
} // End render 1X2

function render_card_handicap(&$card,$position){
  /* Arrange list of options */
  $options_order=array('2','3');
  $options=array();
  $card_options=&$card['options'];
  foreach($card_options as &$option){
    $options[$option['position']]=$option;
  }
  unset($option);
  /* End list of options */
  
  $checked=$card['checked'];
  $winner=(isset($card['positionWin']) && $card['positionWin']<=3 && $card['positionWin']>=0)?$card['positionWin']:null;
  $is_open=(!$checked && !isset($winner));
  $win_status=-1;
  
  foreach($options_order as $option_number){
    if(!isset($options[$option_number])){continue;}
    $option=&$options[$option_number];
    if(isset($winner) && $option['checked']){
      if($option_number==$winner){
        $win_status=1;
      } else {
        $win_status=0;
      }
    }
?>

      <div class="card-option">
<?php if($is_open) { ?>
        <input id="option-<?php echo $position; ?>-<?php echo $option_number; ?>" type="radio" name="choice"
        value="<?php echo $option_number; ?>">
<?php } ?>
<?php if($option['checked']){?>
        <p class="your-bet">Seu Chute</p>
<?php } ?>
        <div<?php
if($checked){
  echo ' class="' . (  ($option['checked'])?'checked':'unchecked'  ) . '"';
}
?>>
<?php if($is_open) { ?>
          <label for="option-<?php echo $position; ?>-<?php echo $option_number; ?>"></label>
<?php } ?>
          <div class="team">
            <img src="<?php echo $option['team']['photo']; ?>">
            <p class="card-option-name"><?php echo $option['team']['name']; ?></p>
            <p class="handicap-value"><?php echo $option['value']; ?></p>
          </div>
          <div class="value">
            <p class="text">Valendo</p>
            <p class="points">
              <img src="/sites/default/files/images/icons/stars.png">
              <?php echo round($option['points'],2); ?><span class="unit">pts</span>
            </p>
          </div>
        </div>
<?php
if(isset($winner)){
  echo get_answer($option_number,$option['checked'],$winner);
}
?>
      </div>
<?php
  } // End for each option
  return $win_status;
} // End render handicap



function render_card_over_under(&$card,$position){
  /* Arrange options */
  $options=array();
  $card_options=&$card['options'];
  foreach($card_options as &$option){
    $options[$option['position']]=$option;
  }
  unset($option);
  /* End arrange options */
  if(!( isset($options[0]) && isset($options[1])) ){
?>
<p class="invalid">Aconteceu um erro. As opções não estão disponíveis.</p>
<?php
    return;
  }
  $checked=$card['checked'];
  $winner=(isset($card['positionWin']) && $card['positionWin']<=3 && $card['positionWin']>=0)?$card['positionWin']:null;
  $is_open=(!$checked && !isset($winner));
  $win_status=-1;
  
  $option1=&$options[0];
  $option2=&$options[1];
  if(isset($winner)){
    if($option1['checked'] && $winner==0
    || $option2['checked'] && $winner==1){
      $win_status=1;
    } else {
      $win_status=0;
    }
  }
  ?>
  <div class="over-under-teams">
    <div class="match-teams">
      <div class="team">
        <img src="<?php echo $option1['team']['photo']; ?>" alt="<?php echo $option1['team']['name']; ?>">
        <p><?php echo $option1['team']['name']; ?></p>
      </div><div class="versus"><span></span></div><div class="team">
        <img src="<?php echo $option2['team']['photo']; ?>" alt="<?php echo $option2['team']['name']; ?>">
        <p><?php echo $option2['team']['name']; ?></p>
      </div>
    </div>
  </div><div class="over-under">
<?php $option_number=0; ?>
    <div class="card-option">
<?php if($option1['checked']){?>
        <p class="your-bet">Seu Chute</p>
<?php } ?>
<?php if($is_open) { ?>
      <input id="option-<?php echo $position; ?>-<?php echo $option1['position']; ?>" type="radio" name="choice"
        value="<?php echo $option1['position']; ?>">
<?php } ?>
      <div<?php if($checked) { echo ' class="' . ( $option1['checked']?'checked':'unchecked' ) . '"'; }?>>
<?php if($is_open) { ?>
        <label for="option-<?php echo $position; ?>-<?php echo $option1['position']; ?>"></label>
<?php } ?>
        <p class="card-option-name"><?php echo $option1['value']; ?></p>
        <div class="value">
          <p class="text">Valendo</p>
          <p class="points">
            <img src="/sites/default/files/images/icons/stars.png">
           <?php echo round($option1['points'],2); ?><span class="unit">pts</span>
          </p>
        </div>
      </div>
<?php
if(isset($winner)){
  echo get_answer($option_number,$option1['checked'],$winner);
}
?>
    </div><div class="card-option">
<?php $option_number=1; ?>
<?php if($option2['checked']){?>
        <p class="your-bet">Seu Chute</p>
<?php } ?>
<?php if($is_open) { ?>
      <input id="option-<?php echo $position; ?>-<?php echo $option2['position']; ?>" type="radio" name="choice"
        value="<?php echo $option2['position']; ?>">
<?php } ?>
      <div<?php if($checked) { echo ' class="' . ( $option2['checked']?'checked':'unchecked' ) . '"'; }?>>
<?php if($is_open) { ?>
        <label for="option-<?php echo $position; ?>-<?php echo $option2['position']; ?>"></label>
<?php } ?>
        <p class="card-option-name"><?php echo $option2['value']; ?></p>
        <div class="value">
          <p class="text">Valendo</p>
          <p class="points">
            <img src="/sites/default/files/images/icons/stars.png">
            <?php echo round($option2['points'],2); ?><span class="unit">pts</span>
          </p>
        </div>
      </div>
<?php
if(isset($winner)){
  echo get_answer($option_number,$option2['checked'],$winner);
}
?>
    </div>
  </div>
<?php
  return $win_status;
} // End render  Over Under

function render_card_yes_no(&$card,$position){

/* Arrange list of options */
  $options_order=array('2','0','1','3');
  $options=array();
  $card_options=&$card['options'];
  foreach($card_options as &$option){
    $options[$option['position']]=$option;
  }
  unset($option);
  /* End list of options */
  $checked=$card['checked'];
  $winner=(isset($card['positionWin']) && $card['positionWin']<=3 && $card['positionWin']>=0)?$card['positionWin']:null;
  $is_open=(!$checked && !isset($winner));
  $win_status=-1;
  
  $option_images=array(
    '0'=> '/sites/default/files/images/icons/yes.png',
    '2'=> '/sites/default/files/images/icons/yes.png',
    '1'=> '/sites/default/files/images/icons/no.png',
    '3'=> '/sites/default/files/images/icons/no.png'
  );
  
  foreach($options_order as $option_number){
    if(!isset($options[$option_number])){continue;}
    $option=&$options[$option_number];
    if(isset($winner) && $option['checked']){
      if($option_number==$winner){
        $win_status=1;
      } else {
        $win_status=0;
      }
    }
?>

      <div class="card-option">
<?php if(!$checked) { ?>
        <input id="option-<?php echo $position; ?>-<?php echo $option_number; ?>" type="radio" name="choice"
        value="<?php echo $option_number; ?>">
<?php } ?>
<?php if($option['checked']){?>
        <p class="your-bet">Seu Chute</p>
<?php } ?>
        <div<?php
if($checked){
  echo ' class="' . (  ($option['checked'])?'checked':'unchecked'  ) . '"';
}
?>>
<?php if($is_open) { ?>
          <label for="option-<?php echo $position; ?>-<?php echo $option_number; ?>"></label>
<?php } ?>
          <div class="team">
            <img src="<?php echo $option_images[$option['position']]; ?>">
            <p class="card-option-name"><?php echo $option['value']; ?></p>
          </div>
          <div class="value">
            <p class="text">Valendo</p>
            <p class="points">
              <img src="/sites/default/files/images/icons/stars.png">
              <?php echo round($option['points'],2); ?><span class="unit">pts</span>
            </p>
          </div>
        </div>
<?php

if(isset($winner)){
  echo get_answer($option_number,$option['checked'],$winner);
}
?>
      </div>
<?php
  } // End for each option

  return $win_status;
}

?>
