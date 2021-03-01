<?php
//Get user round
ob_start();
if(isset($_GET['user'])){
  $ENDPOINT='/User/BetsByRoundFriend/' . $_GET['user'] . '/' . $_GET['id'];
} else {
  $ENDPOINT='/User/BetsByRound/' . $_GET['id'];
}
require($_SERVER['DOCUMENT_ROOT'] . '/logic/api/api-user.php');
$round_json=ob_get_contents();
ob_end_clean();
$round_info=json_decode($round_json,true);

$error_message=null;
if($round_info['erro']){
  $error_message=$round_info['message'];
?>
<div class="round-page">
  <p class="invalid"><?php echo $error_message; ?></p>
</div>
<?php
} else {
  $round_data=&$round_info['data'];
  //Get user data
  $full_name=null;
  if(isset($_GET['user'])){
    ob_start();
    $ENDPOINT='/User/HomeByFriend/' . $_GET['user'];
    require($_SERVER['DOCUMENT_ROOT'] . '/logic/api/api-user.php');
    $user_json=ob_get_contents();
    ob_end_clean();
    $user_info=json_decode($user_json,true);
    if($user_info['erro']){
    } else {
      $user_data=&$user_info['data'];
      $full_name=$user_data['firstName'] . ' ' . $user_data['lastName'];
    }
  }
?>
<div class="round-page">
<?php
  if(!empty($full_name)){
?>
  <h1><a href="/user/page?id=<?php echo $_GET['user']; ?>"><?php echo $full_name; ?></a></h1>
<?php
  }
  $participated=false;
  $count=count($round_data);
  foreach($round_data as &$match){
    $hit=render_match($match);
    $participated = $participated || $hit;
  }
  if(!$participated){
?>
<div class="round-card match-card-box">
  <div class="white-box">
<?php if($count){ ?>
    <p class="message">Você não participou desta rodada.</p>
<?php } else { ?>
    <p class="message">Erro: Nenhum dado encontrado</p>
<?php } ?>
  </div>
</div>
<?php
  }
  unset($match);
  unset($round_data);
  
?>
</div>
<?php
  
} // End if no errors








/*** FUNCTIONS ***/
function render_match(&$match){
  $cards=&$match['cards'];
  $participated=false;
  foreach($cards as &$card){
    if($card['checked']){
      $participated=true;
      render_match_card($card,$match['league']);
    }
  }
  return $participated;
}

function render_match_card(&$card,$league_name){
  /* Renders a card representing a match the user betted on. */  
?>
<div class="round-card match-card-box">
  <div class="match-card white-box">
    <header class="card-header">
      <p class="card-league-name"><?php echo $league_name; ?></p>
      <h2><?php echo $card['title']; ?></h2>
      <p class="card-question"><?php echo $card['question']; ?></p>
    </header>
    <div class="card-options">
<?php
  $i_win=false;
  switch($card['type']){
    case 0:
      $i_win=render_card_yes_no($card,$position);
      break;
    case 1:
      $i_win=render_card_handicap($card,$position);
      break;
    case 2:
      $i_win=render_card_over_under($card,$position);
      break;
    case 3:
      $i_win=render_card_1x2($card,$position);
      break;
  }
?>
    </div>
    <footer class="card-footer">
<?php if($i_win){
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
      <p>Você ganhou</p>
      <p class="winnings"><img src="/sites/default/files/images/icons/stars.png"> <?php echo $win_value; ?><span class="points">pts</span></p>
<?php } ?>
    </footer>
  </div>
</div>
<?php

} // End render match card


function render_card_1x2(&$card,$position){
  $i_win=false;
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
  $winner=$card['positionWin'];
  foreach($options_order as $option_number){
    if(!isset($options[$option_number])){continue;}
    $option=&$options[$option_number];
    
    $o_check=$option['checked'];
    $o_win=$o_check && $option_number==$winner;
    $o_lose=$o_check && !($option_number==$winner);
    if($o_win){$i_win=true;}
    ?><div class="card-option">
<?php if($o_check){?>
        <p class="your-bet">Chute</p>
<?php } ?>
        <div<?php
if($option_number==$winner){
  echo ' class="correct"';
} else if($o_lose){
  echo ' class="incorrect"';
} else {
  echo ' class="unimportant"';
}
?>>
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
        </div>
<?php if($option_number==$winner){ ?>
        <p class="answer correct"><img src="/sites/default/files/images/icons/correct.png">Resposta correta</p>
<?php } else if($o_lose){ ?>
        <p class="answer incorrect"><img src="/sites/default/files/images/icons/incorrect.png">Resposta incorreta</p>
<?php } else { ?>
        <p class="answer"></p>
<?php } ?>
      </div><?php

  } // End for options

  return $i_win;
} // End render 1X2

function render_card_handicap(&$card,$position){
  /* Arrange list of options */
  $i_win=false;
  $options_order=array('2','3');
  $options=array();
  $card_options=&$card['options'];
  foreach($card_options as &$option){
    $options[$option['position']]=$option;
  }
  unset($option);
  /* End list of options */
  
  $checked=$card['checked'];
  $winner=$card['positionWin'];
  foreach($options_order as $option_number){
    if(!isset($options[$option_number])){continue;}
    $option=&$options[$option_number];
    
    $o_check=$option['checked'];
    $o_win=$o_check && $option_number==$winner;
    $o_lose=$o_check && !($option_number==$winner);
    if($o_win){$i_win=true;}
?>

      <div class="card-option">
<?php if($o_check){?>
        <p class="your-bet">Chute</p>
<?php } ?>
        <div<?php
if($option_number==$winner){
  echo ' class="correct"';
} else if($o_lose){
  echo ' class="incorrect"';
} else {
  echo ' class="unimportant"';
}
?>>
          <div class="team">
            <img src="<?php echo $option['team']['photo']; ?>">
            <p class="card-option-name"><?php echo $option['team']['name']; ?></p>
            <p class="handicap-value"><?php echo $option['value']; ?></p>
          </div>
        </div>
<?php if($option_number==$winner){ ?>
        <p class="answer correct"><img src="/sites/default/files/images/icons/correct.png">Resposta correta</p>
<?php } else if($o_lose){ ?>
        <p class="answer incorrect"><img src="/sites/default/files/images/icons/incorrect.png">Resposta incorreta</p>
<?php } else { ?>
        <p class="answer"></p>
<?php } ?>
      </div>

<?php
  } // End for each option
  return $i_win;
} // End render handicap

function render_card_over_under(&$card,$position){
  /* Arrange options */
  $i_win=false;
  $options=array();
  $card_options=&$card['options'];
  foreach($card_options as &$option){
    $options[$option['position']]=$option;
  }
    
  unset($option);
  /* End arrange options */
  if(!( isset($options[0]) && isset($options[1])) ){

?>
<p class="invalid">An error occurred. Options not available.</p>
<?php
    return;
  }
  $checked=$card['checked'];
  $option1=&$options[0];
  $option2=&$options[1];
  $winner=$card['positionWin'];
  
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
    <div class="card-option">
<?php if($option1['checked']){ ?>
        <p class="your-bet">Chute</p>
<?php } ?>
      <div<?php
if($winner==0){
  echo ' class="correct"';
  if($option1['checked']){$i_win=true;}
} else if($option1['checked']){
  echo ' class="incorrect"';
} else {
  echo ' class="unimportant"';
}
?>>
        <p class="card-option-name"><?php echo $option1['value']; ?></p>
      </div>
<?php if($winner==0){ ?>
        <p class="answer correct"><img src="/sites/default/files/images/icons/correct.png">Resposta correta</p>
<?php } else if($option1['checked']){ ?>
        <p class="answer incorrect"><img src="/sites/default/files/images/icons/incorrect.png">Resposta incorreta</p>
<?php } else { ?>
        <p class="answer"></p>
<?php } ?>
    </div><div class="card-option">
<?php if($option2['checked']){?>
        <p class="your-bet">Chute</p>
<?php } ?>
      <div<?php
if($winner==1){
  echo ' class="correct"';
  if($option2['checked']){$i_win=true;}
} else if($option2['checked']){
  echo ' class="incorrect"';
} else {
  echo ' class="unimportant"';
}
?>>
        <p class="card-option-name"><?php echo $option2['value']; ?></p>
      </div>
      
<?php if($winner==1){ ?>
        <p class="answer correct"><img src="/sites/default/files/images/icons/correct.png">Resposta correta</p>
<?php } else if($option2['checked']){ ?>
        <p class="answer incorrect"><img src="/sites/default/files/images/icons/incorrect.png">Resposta incorreta</p>
<?php } else { ?>
        <p class="answer"></p>
<?php } ?>
    </div>
  </div>
  
<?php
  return $i_win;
} // End render over/under

function render_card_yes_no(&$card,$position){
  $i_win=false;
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
  $winner=$card['positionWin'];
  
  $option_images=array(
    '0'=> '/sites/default/files/images/icons/yes-gray.png',
    '2'=> '/sites/default/files/images/icons/yes-gray.png',
    '1'=> '/sites/default/files/images/icons/no-gray.png',
    '3'=> '/sites/default/files/images/icons/no-gray.png'
  );
  
  foreach($options_order as $option_number){
    if(!isset($options[$option_number])){continue;}
    $option=&$options[$option_number];
    
    $o_check=$option['checked'];
    $o_win=$o_check && $option_number==$winner;
    $o_lose=$o_check && !($option_number==$winner);
    
?>

      <div class="card-option">
<?php if($option['checked']){?>
        <p class="your-bet">Chute</p>
<?php } ?>
        <div<?php
if($option_number==$winner){
  echo ' class="correct"';
} else if($o_lose){
  echo ' class="incorrect"';
} else {
  echo ' class="unimportant"';
}
?>>
          <div class="team">
            <img src="<?php echo $option_images[$option['position']]; ?>">
            <p class="card-option-name"><?php echo $option['value']; ?></p>
          </div>
        </div>
<?php if($option_number==$winner){ ?>
        <p class="answer correct"><img src="/sites/default/files/images/icons/correct.png">Resposta correta</p>
<?php } else if($o_lose){ ?>
        <p class="answer incorrect"><img src="/sites/default/files/images/icons/incorrect.png">Resposta incorreta</p>
<?php } else { ?>
        <p class="answer"></p>
<?php } ?>
      </div>

<?php
  } // End for each option

  return $i_win;
} // End render yes/no



?>