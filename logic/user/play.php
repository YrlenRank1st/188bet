<?php
global $ERROR_NO_MATCHES;
global $VIEW_RESULT;
$ERROR_NO_MATCHES='Desculpe, não há jogos para mostrar no momento.';
$VIEW_RESULT='Ver resultado';

require_once('loggedin.php');
require_once($_SERVER['DOCUMENT_ROOT'] . '/logic/functions.php');
//
//$page=isset($_GET['page'])?$_GET['page']:1;

$complete=false;
$cur_page=1;
$matches=array();
while(!$complete){
  //Get match page
  ob_start();
  $ENDPOINT='/Game/Feed/' . $cur_page;
  require($_SERVER['DOCUMENT_ROOT'] . '/logic/api/api-user.php');
  $json=ob_get_contents();
  ob_end_clean();
  $result=json_decode($json,true);
  if($result['erro']){
    $cur_matches=null;
  } else {
    $cur_matches=$result['data']['games'];
  }
  //Add matches to list
  if(empty($cur_matches)){
    $complete=true;
  } else {
    foreach($cur_matches as $match){
      $matches[]=$match;
    }
  }
  //Update page
  $cur_page++;
}

?>
<div class="all-matches">
<!-- Banner -->
<div class="link-banner">
  <a href="https://www.188bet.net/lp/188APP/8357.html"><?php
  /** Old <img src="/sites/default/files/images/728x90.gif?v=1.0" alt=""> **/
  ?><img src="/sites/default/files/images/banner-2020-02.png" alt=""></a>
</div>
<!-- End Banner -->
  <div class="match-list">
<?php
if(empty($matches) || $result['data']['allDone']){
?>
    <div class="white-box match-box all-done">
      <p><?php echo empty($result['data']['message'])?$ERROR_NO_MATCHES:$result['data']['message']; ?></p>
    </div>
<?php
}
//print_r($result);
foreach($matches as $match){
  generate_match_box($match);
}
?>
  </div>
</div>
<script type="text/javascript" src="/logic/js/play-game.js?v=1.0"></script>


<?php

function generate_match_box(&$data){
  /**
   * Prints a box on the screen containing data
   * about a match. $data has the following format:
    {
      "title": "Alemanha X Brasil",
      "league": "Amistoso Internacional",
      "acquired": true,
      "isFinished": false,
      "teamOne": null,
      "teamTwo": null,
      "points": 0,
      "endDate": 1522175400,
      "startDate": 1522175400,
      "gameType": 1,
      "id": "5ab8638dc66f45e6ec56ecd9"
    }
   */
  /* Teams */
  global $VIEW_RESULT;
  $end_date=strftime('%d/%m, %H:%M',$data['endDate']);
  $acquired=$data['acquired'];
  $finished=$data['isFinished'];
  //
  $team_display=$data['title'];
  /*
  $team1_name=$data['teamOne']['name'];
  $team1_image=$data['teamOne']['photo'];
  $team2_name=$data['teamTwo']['name'];
  $team2_image=$data['teamTwo']['photo'];
  */
  
?><div class="white-box match-box">
    <div class="match-header">
      <p class="match-end">Encerra em <?php echo $end_date; ?></p>
      <h2><?php echo htmlentities($data['league']); ?></h2>
    </div>
    <div class="match-teams"><p><?php echo $team_display; ?></p></div>
    <div class="match-options">
      <p class="points"><img src="/sites/default/files/images/icons/stars.png"><?php echo $data['points'];?></p>
<?php if($finished){ ?>
      <a class="cta bet-loader" href="/play/match?id=<?php echo urlencode($data['id']); ?>"><?php echo $VIEW_RESULT; ?></a>
<?php } else if($acquired){ ?>
      <a class="cta waiting bet-loader" href="/play/match?id=<?php echo urlencode($data['id']); ?>">Aguardando...</a>
      <span class="waiting"><img src="/sites/default/files/images/icons/waiting.png"></span>
<?php } else { ?>
      <a class="cta bet-loader" href="/play/match?id=<?php echo urlencode($data['id']); ?>">Chutar</a>
<?php }?>
    </div>
  </div><?php

}

?>