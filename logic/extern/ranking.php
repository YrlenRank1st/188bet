<?php

$cur_page=isset($_GET['page'])?$_GET['page']:1;
if(!is_numeric($cur_page)){
  $cur_page=1;
} else {
  $cur_page=floor($_GET['page']);
}
if($cur_page<0){$cur_page=0;}
$cur_page++;
$cur_type='Month';
require_once($_SERVER['DOCUMENT_ROOT'] . '/logic/user/loggedin.php');
require_once($_SERVER['DOCUMENT_ROOT'] . '/logic/functions.php');
$rank_type='/Ranking/Month/' . $cur_page;
$my_type='/Ranking/MyPositionMonth';
$rank_title='Ranking do mês';
if(isset($_GET['rank'])){
  switch($_GET['rank']){
    case 'weekly':
      $rank_type='/Ranking/Week/' . $cur_page;
      $rank_title='Ranking da semana';
      $my_type='/Ranking/MyPositionWeek';
      $cur_type='Week';
      break;
    case 'monthly':
      $rank_type='/Ranking/Month/' . $cur_page;
      $rank_title='Ranking do mês';
      $my_type='/Ranking/MyPositionMonth';
      $cur_type='Month';
      break;
  }
}
ob_start();
$ENDPOINT=$rank_type;
require($_SERVER['DOCUMENT_ROOT'] . '/logic/api/api-user.php');
$rank_json=ob_get_contents();
ob_end_clean();
$rank_info=json_decode($rank_json,true);

$my_info=null;
if(isset($_SESSION['bet188']['user'])){
  ob_start();
  $ENDPOINT=$my_type;
  require($_SERVER['DOCUMENT_ROOT'] . '/logic/api/api-user.php');
  $my_json=ob_get_contents();
  ob_end_clean();
  $my_info=json_decode($my_json,true);
}

if($rank_info['erro']){
?>
  <div>
    <p class="error">Desculpe, aconteceu um erro.</p>
  </div>
<?php
} else {
  $rank_data=&$rank_info['data'];
?>
<div class="user-ranking">
<!-- Banner -->
<?php
$banner_link='https://www.188bet.net/lp/188APP/8355.html';
if(isset($_GET['rank']) && $_GET['rank']==='weekly'){
  $banner_link='https://www.188bet.net/lp/188APP/8356.html';
}
?>
<div class="link-banner">
  <a href="<?php echo $banner_link; ?>"><?php
/** Old <img src="/sites/default/files/images/728x90.gif?v=1.0" alt=""> **/
?><img src="/sites/default/files/images/banner-2020-02.png" alt=""></a>
</div>
<!-- End Banner -->
<div class="user-listing">
<?php if(!empty($my_info['data'])){
  $my_data=&$my_info['data'];
?>
  <div class="user-position">
    <h2>Minha posição</h2>
    <table>
      <thead>
        <tr>
          <th>Posiçao</th>
          <th>Usuário</th>
          <th><img src="/sites/default/files/images/icons/stars.png"> Pontos</th>
        </tr>
      </thead>
      <tbody>
<?php echo get_listed_user_html($my_data); ?>
      </tbody>
    </table>
  </div>
<?php } // End if user data ?>
<?php if(!empty($rank_data)){ ?>
  <div class="user-ranking">
    <h2><?php echo $rank_title; ?></h2>
    <table id="ranking-table">
      <thead>
        <tr>
          <th>Posiçao</th>
          <th>Usuário</th>
          <th><img src="/sites/default/files/images/icons/stars.png"> Pontos</th>
        </tr>
      </thead>
      <tbody>
<?php
foreach($rank_data as &$rank){
  echo get_listed_user_html($rank);
}
unset($rank);
?>
      </tbody>
    </table>
    <a id="ranking-load-more" class="cta" href="?<?php echo isset($_GET['rank'])?('rank=' . $_GET['rank'] . '&'):''; ?>page=<?php echo $cur_page; ?>">Ver Mais</a>
<?php if($cur_page>2){
?>
    <a class="cta" href="?<?php echo isset($_GET['rank'])?('rank=' . $_GET['rank'] . '&'):''; ?>page=<?php echo $cur_page-2; ?>">Página anterior</a>
<?php } else if($cur_page==2){ ?>
    <a class="cta" href="?<?php echo isset($_GET['rank'])?('rank=' . $_GET['rank']):''; ?>">Página anterior</a>
<?php } ?>
  </div>
  <script>
var RANKING_CUR_PAGE=<?php echo $cur_page; ?>;
var RANKING_CUR_TYPE="<?php echo $cur_type; ?>";
  </script>
  <script src="/logic/js/ranking.js?v1.0"></script>
<?php } // End if $rank_data ?>
</div>
</div>
<?php

  unset($rank_data);

} // End if no errors


function get_listed_user_html(&$user){
  /**
   * User has the fields:
   * photo,level,firstName,lastName,username,medals,cashe,position,score,id
   */
  $winner=($user['position']==1);
  
  //Get user's name
  $user_name= $user['firstName'] . ' ' . $user['lastName'];
  //Get user image
  $member_image= $user['photo'];
  if(!( strpos($member_image,'http')===0 )){
    //Image is relative
  }
  $user_level=((int)$user['level'])+1;
  $level_image=get_level_image($user_level);
  ob_start();
?>
          <tr<?php echo ($winner?' class="winner"':'' ); ?>>
            <td class="position">
              <span><?php echo $user['position']; ?></span>
            </td>
            <td class="user">
              <div class="user-image">
                <img src="<?php echo $member_image; ?>">
                <img class="level-image" src="<?php echo $level_image ?>">
              </div><div class="user-name">
                <a href="/user/page?id=<?php echo $user['id']; ?>"><?php echo $user_name; ?></a>
              </div>
            </td>
            <td class="points">
              <span><?php echo $user['score']; ?></span>
            </td>
          </tr>
<?php
  $html=ob_get_contents();
  ob_end_clean();
  return $html;
}
?>