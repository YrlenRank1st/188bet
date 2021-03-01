<?php
/** GROUP VARIABLES **/
$CURRENT_GROUP=null;
$GROUP_IDS=array();

/** ACCEPT OR DENY REQUESTS **/
if(isset($_POST['group-request'])){
  $action=$_POST['group-request'];
  $ENDPOINT=null;
  if($action==='accept'){
    $ENDPOINT='/User/AcceptRequestGroup';
  } else if($action==='deny'){
    $ENDPOINT='/User/DennyRequestGroup';
  }
  $JSON_DATA=array('id'=>$_POST['id']);
  ob_start();
  require($_SERVER['DOCUMENT_ROOT'] . '/logic/api/api-post-user.php');
  $request_result=ob_get_contents();
  ob_end_clean();
  
  $request_info=json_decode($request_result,true);
  
  if($request_info['erro']){
    $request_message='<p class="invalid">' . $request_info['message'] . '</p>';
  } else {
    $request_message='<p class="success">' . $request_info['message'] . '</p>';
  }
}

/** END REQUESTS **/

//Get group requests
ob_start();
$ENDPOINT='/User/MyGroupRequestNew';
require($_SERVER['DOCUMENT_ROOT'] . '/logic/api/api-user.php');
$request_json=ob_get_contents();
ob_end_clean();
$request_info=json_decode($request_json,true);
$requests=array();
if($request_info['erro']){
} else {
  $requests=$request_info['data'];
}

//Get My Groups
ob_start();
$ENDPOINT='/User/MyGroupListNew';
require($_SERVER['DOCUMENT_ROOT'] . '/logic/api/api-user.php');
$groups_json=ob_get_contents();
ob_end_clean();
$groups_info=json_decode($groups_json,true);
$groups=array();
if($groups_info['erro']){
} else {
  $groups=$groups_info['data'];
  foreach($groups as &$group){
    $GROUP_IDS[$group['id']]=1;
  }
  unset($group);
}

/** END GET GROUP DATA **/

//Get current group

$CURRENT_GROUP=isset($_GET['id'])?$_GET['id']:null;
if(isset($CURRENT_GROUP) && isset($GROUP_IDS[$CURRENT_GROUP])){
  $CURRENT_GROUP=$_GET['id'];
} else if(!empty($GROUP_IDS)){
  reset($GROUP_IDS);
  $CURRENT_GROUP=key($GROUP_IDS);
} else {
  $CURRENT_GROUP=null;
}
$REQUEST_JS='[]';
?>
<div class="sidebar">
<?php
if(!empty($request_message)){
  echo $request_message;
} ?>
<?php if(!empty($requests)){ ?>
    <section class="group-invites">
      <h2>Convites pendientes</h2>
<?php

foreach($requests as &$request){
  render_group_request($request);
  //Get all group members
  ob_start();
  $ENDPOINT='/User/GroupListUser/' . $request['id'] . '/1';
  require($_SERVER['DOCUMENT_ROOT'] . '/logic/api/api-user.php');
  $group_users_json=ob_get_contents();
  ob_end_clean();
  $group_users_info=json_decode($group_users_json,true);
  if(!$group_users_info['erro'] && isset($group_users_info['data']['members'])){
    $request['members']=$group_users_info['data']['members'];
  }
  
}
unset($request);
$REQUEST_JS=json_encode($requests);
?>
    </section><?php
} // End if group requests
    ?><section class="my-groups">
      <h2>Meus grupos</h2>
      <a class="group-action" href="/user/groups/update">Novo grupo</a>
<?php
$group_must_be_null=(isset($edit_mode) && $edit_mode===0);
foreach($groups as &$group){
  $active_group=false;
  if((!$group_must_be_null)
  && isset($CURRENT_GROUP) && $group['id']===$CURRENT_GROUP){
    $active_group=true;
  }
  render_my_group($group,$active_group);
}
unset($group);
?>
    </section>
    <script type="text/javascript">var GROUP_REQUESTS=<?php echo $REQUEST_JS; ?></script>
    <script src="/logic/js/group-sidebar.js?v=1.2"></script>
  </div><?php

function render_group_request(&$data){
  /*
   * Displays a request to join a group
   * name,photo,totalMembers,id,isAdmin,myPosition
   */
  $member_count=$data['totalMembers'];
  $plural=!($member_count===1);
  $group_params=($active?'':'?id=' . $data['id']);
?>
      <div class="group-box group-request">
        <div class="image">
          <div class="image-limits">
            <img src="<?php echo $data['photo']; ?>">
          </div>
        </div><div class="data">
          <span class="group-title"><?php echo $data['name']; ?></span>
          <span class="group-text"><?php echo $member_count; ?> pessoa<?php echo ($plural?'s':''); ?></span>
        </div><div class="action">
          <a class="view-group-request" href="#<?php echo $data['id']; ?>">Ver convite</a>
        </div>
      </div>
<?php
} // End render group request

function render_my_group(&$data,$active){
  /*
   * Displays a group with the following fields:
   * name,photo,id,totalMembers,myPosition,isAdmin
   */
  $member_count=((int)$data['totalMembers']);
  $plural=!($member_count===1);
  $group_params=($active?'':'?id=' . $data['id']);
  $user_info=get_user_info();
  $user_id=$user_info['data']['id'];
  
  /* Get user position */
  $position_count=0;
  $user_position=$data['myPosition'];
  /* End get user position */
?>
      <div class="group-box group-invite<?php echo ($active?' active':''); ?>">
        <div class="image">
          <div class="image-limits">
            <img src="<?php echo $data['photo']; ?>">
          </div>
        </div><div class="data">
          <span class="group-title"><?php echo $data['name']; ?></span>
          <span class="group-text"><?php echo $member_count; ?> pessoa<?php echo ($plural?'s':''); ?></span>
        </div><div class="action">
          <div>
            <span class="group-text">Posição</span>
            <span class="group-text"><?php echo $user_position; ?>/<?php echo $member_count; ?></span>
          </div>
        </div>
        <a class="group-select" href="/user/groups<?php echo $group_params; ?>"></a>
      </div>
<?php
} // End render my group