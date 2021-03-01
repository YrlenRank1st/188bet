<?php
require_once($_SERVER['DOCUMENT_ROOT'] . '/logic/api/params.php');
require_once($_SERVER['DOCUMENT_ROOT'] . '/logic/functions.php');
require_once('loggedin.php');

$error_message=null;
$user_info = get_user_info();

if(isset($_POST['exit'])){
  $ENDPOINT='/Group/Exit';
  $JSON_DATA=array(
    'id'=>$_POST['exit']
  );
  ob_start();
  require($_SERVER['DOCUMENT_ROOT'] . '/logic/api/api-post-user.php');
  $exit_group_json=ob_get_contents();
  ob_end_clean();
  $exit_group_info=json_decode($exit_group_json,true);
  
  if($exit_group_info['erro']){
    $error_message='<p class="invalid">' . $exit_group_info['message'] . '</p>';
  } else {
    $error_message='<p class="success">' . $exit_group_info['message'] . '</p>';
  }
  
}

?>

<div class="groups">
<?php require_once('groups-sidebar.php');
?><div class="current-group">
<?php

//Get current group data
$group_data=null;
if(isset($CURRENT_GROUP)){
  $ENDPOINT='/Group?id=' . $CURRENT_GROUP;
  ob_start();
  require($_SERVER['DOCUMENT_ROOT'] . '/logic/api/api-user.php');
  $result=ob_get_contents();
  ob_end_clean();
  $group_info=json_decode($result,true);
  
  if($group_info['erro']){
  } else {
    $group_data=$group_info['data'];
  }
} // End if $CURRENT_GROUP

$is_admin=false;
if(isset($group_data)){
  //Get group information
  $group_data=$group_info['data'];
  $member_count=count($group_data['members']);
  /* Loop through group members.
  1- Find myself
  2- Display members sorted by position
  */
  $member_html=array();
  $my_html='';
  //Display members and get my position
  $user_data=&$user_info['data'];
  $users=&$group_data['members'];
  foreach($users as &$user){
    $html=get_member_html($user);
    //$member_html.=$html;
    $member_html[$user['position']]=$html;
    if($user['id']===$user_data['id']){
      if($user['isAdmin']){ $is_admin=true;}
      $my_html=$html;
    }
  }
  unset($user);
  unset($user_data);
  unset($users);
  ksort($member_html);
  //Display invited members
  if($is_admin){
    $users=&$group_data['invitedUsers'];
    foreach($users as &$user){
      $html=get_member_html($user,true);
      $member_html[]=$html;
    }
    unset($user);
    unset($users);
  }
  /* End loop */

?>
  <header class="group-header group-display-header">
    <h1>
      <?php echo $group_data['name']; ?>
      (<?php echo $member_count; ?> membro<?php echo (($member_count===1)?'':'s'); ?>)
    </h1>
<?php if($is_admin){ ?>
    <a class="edit-btn" href="/user/groups/update?id=<?php echo $group_data['id']; ?>">
      <img src="/sites/default/files/images/icons/edit2.png" alt="editar">
    </a>
<?php } else { ?>
    <form method="POST">
      <input type="hidden" name="exit" value="<?php echo $group_data['id']; ?>">
      <button class="exit-group-btn">
        Sair do grupo
      </button>
    </form>
<?php } ?>
  </header>
  <div class="group-info user-listing">
    <div class="user-position">
      <?php if(isset($error_message)){echo $error_message;} ?>
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
<?php echo $my_html; ?>
        </tbody>
      </table>
    </div>
    <div class="user-ranking">
      <h2>Ranking do mês</h2>
      <table>
        <thead>
          <tr>
            <th>Posiçao</th>
            <th>Usuário</th>
            <th><img src="/sites/default/files/images/icons/stars.png"> Pontos</th>
          </tr>
        </thead>
        <tbody>
<?php foreach($member_html as $temp_html){ echo $temp_html; } ?>
        </tbody>
      </table>
    </div>
  </div>
  <script type="text/javascript" src="/logic/js/group.js?v=1.1" defer></script>
<?php
// End if group data
} else { //No group data
?>
  <header class="group-header">
    <h1>Grupos</h1>
  </header>
  <div class="group-info no-groups">
    <p>Você não faz parte de nenhum grupo.</p>
    <p><a href="/user/groups/update">Crie um novo</a></p>
  </div>
<?php if(isset($error_message)){echo $error_message;} ?>

<?php
} // End if no group data


?>
  </div>
</div>
<?php
function get_member_html(&$member,$invited=false){
  /**
   * Member has the fields: id,photo,firstName,lastName,name,isAdmin,position,score,level
   */
  global $API_IMAGE_ROOT;
  $winner=($member['position']==1);
  //Get user's name
  $member_data=json_decode($member_json,true);
  $member_name= $member['firstName'] . ' ' . $member['lastName'];
  
  //Get user image
  $member_image= $member['photo'];
  if(!( strpos($member_image,'http')===0 )){
    //Image is relative
    $member_image=$API_IMAGE_ROOT . $member_image;
  }
  $user_level=((int)$member['level'])+1;
  $level_image=get_level_image($user_level);
  
  //Get stats
  if($invited){
    $position='';
    $score='';
    $member_name .= ' (Aguardando Confirmação)';
  } else {
    $position=$member['position'];
    $score=$member['score'];
  }
  
  ob_start();
?>
          <tr<?php echo ($winner?' class="winner"':'' ); ?>>
            <td class="position">
              <span><?php echo $position; ?></span>
            </td>
            <td class="user">
              <div class="user-image">
                <img src="<?php echo $member_image; ?>">
                <img class="level-image" src="<?php echo $level_image ?>">
              </div><div class="user-name">
                <?php echo $member_name; ?>
              </div>
            </td>
            <td class="points">
              <span><?php echo $score; ?></span>
            </td>
          </tr>
<?php
  $html=ob_get_contents();
  ob_end_clean();
  return $html;
}

?>