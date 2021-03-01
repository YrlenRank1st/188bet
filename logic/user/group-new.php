<?php
/** Translate **/
$T_IMG_ERR=array(
  'not_logged_in'=>'Você não está logado.',
  'no_image_file'=>'O arquivo não pôde ser enviado.',
  'not_image'=>'O arquivo enviado não era uma imagem.'
);
/** End Translate **/

require_once('loggedin.php');
$DEFAULT_GROUP_IMAGE='imageNotFound.jpg';

$edit_mode=0;
$group_title='';
$group_image='/sites/default/files/images/extern/group-default.png';
$group_members=array();
$error_message=null;

/** DELETE GROUP **/
if(isset($_POST['group-delete'])){
  $ENDPOINT='/Group/Delete';
  $JSON_DATA=array('id'=>$_POST['id']);
  ob_start();
  require_once($_SERVER['DOCUMENT_ROOT'] . '/logic/api/api-post-user.php');
  $group_delete_result=ob_get_contents();
  ob_end_clean();
  $delete_info=json_decode($group_delete_result,true);
  if($delete_info['erro']){
    $error_message='<p class="invalid">' . $delete_info['message'] . '</p>';
  } else {
    $error_message='<p class="success">' . $delete_info['message'] . '</p>';
  }
}
/** END DELETE GROUP **/

/** SUBMIT GROUP **/
if(isset($_POST['group-update'])){

  /* Upload image */
  if(!empty($_FILES['image-upload']['size'])){
    ob_start();
    require_once($_SERVER['DOCUMENT_ROOT'] . '/logic/api/file-upload.php');
    $image_upload_result=ob_get_contents();
    ob_end_clean();
    $image_upload_info=json_decode($image_upload_result,true);
    if($image_upload_info['erro']){
      if($image_upload_info['message']==='not_logged_in'
      || $image_upload_info['message']==='no_image_file'
      || $image_upload_info['message']==='not_image'){
        $image_upload_info['message']=$T_IMG_ERR[$image_upload_info['message']];
      }
      $error_message='<p class="invalid">' . $image_upload_info['message'] . '</p>';
    } else {
      $_POST['photo']=$image_upload_info['data']['fileName'];
    }
  } else if(empty($_POST['photo'])){
    $_POST['photo']='imageNotFound.jpg';
  }

  /* End upload image */
  if(empty($error_message)){
    $JSON_DATA=array();
    if(isset($_POST['title'])){ $JSON_DATA['name']=$_POST['title']; }
    if(isset($_POST['photo'])){ $JSON_DATA['photo']=$_POST['photo']; }
    if(isset($_POST['member']) && empty($_POST['id'])){
      //Members can only be added when creating a new group
      $JSON_DATA['members']=array();
      $json_members=&$JSON_DATA['members'];
      foreach($_POST['member'] as $member_id){
        $json_members[]=array(
          'id'=>$member_id,
          'isAdmin'=>false
        );
      }
    }
    if(isset($_POST['id'])){
      $ENDPOINT='/Group/Update';  
      $JSON_DATA['id']=$_POST['id'];
      $_GET['id']=$_POST['id'];
    } else {
      $ENDPOINT='/User/CreateGroup';
    }
    ob_start();
    require($_SERVER['DOCUMENT_ROOT'] . '/logic/api/api-post-user.php');
    $result=ob_get_contents();
    ob_end_clean();
    $result_info=json_decode($result,true);
    
    if($result_info['erro']){
      $error_message='<p class="invalid">' . $result_info['message'] . '</p>';
    } else {
      //Success
      header('Location: /user/groups' . (isset($_POST['id'])?'?id=' . $_POST['id']:'')  );
      exit(0);
    }
  }

}
/** END SUBMIT GROUP **/
$group_image_input='';
$group_members=array();
$group_invited=array();
if(isset($_GET['id'])){
  //Edit group, do not create
  $ENDPOINT='/Group?id=' . $_GET['id'];
  ob_start();
  require($_SERVER['DOCUMENT_ROOT'] . '/logic/api/api-user.php');
  $result=ob_get_contents();
  ob_end_clean();
  $group_info=json_decode($result,true);
  if($group_info['erro']){
  } else {
    $edit_mode=1;
    $group_data=$group_info['data'];
    $group_title=$group_data['name'];
    $group_image=$group_data['photo'];
    $group_id=$group_data['id'];
    if(!strlen($group_image)){
      $group_image='/sites/default/files/images/extern/group-default.png';
    } else if(!(stripos($group_image,'http')===0)){
      $group_image='http://api.188bet.dev.megaleios.kinghost.net/content/upload/' . $group_image;
    }
    //
    $group_image_input=$group_image;
    $img_spos=strrpos($group_image_input,'/');
    if(!($img_spos===false)){
      $img_spos+=1;
      $group_image_input=substr($group_image_input,$img_spos,strlen($group_image_input)-$img_spos);
    }
    //
    $group_members=$group_data['members'];
    $group_invited=$group_data['invitedUsers'];
    //
  }
}

if($edit_mode==0 && empty($group_members)){
  //Add current user as admin
  $user_info=get_user_info();
  if(!empty($user_info['data'])){
    $user_info['data']['isAdmin']=true;
    $group_members[]=$user_info['data'];
  }
}
?>

<div class="groups">
<?php require_once('groups-sidebar.php');
?><div class="current-group">

    <header class="group-header group-edit-header">
      <h1>Editar grupo</h1
      ><a id="submit-group-btn" class="cta">Salvar alterações</a>
    </header>
    <form id="edit-group-form" class="edit-group" method="POST" enctype="multipart/form-data">
<?php if(!empty($error_message)){ echo $error_message; } ?>
      <input type="hidden" name="group-update" value="1">
<?php if($edit_mode===1){ ?>
      <input type="hidden" name="id" value="<?php echo $group_id; ?>">
<?php } ?>
      <script type="text/javascript">var GROUP_EDIT_MODE=<?php echo $edit_mode; ?>;</script>
      <noscript>
        <p>A página reque JavaScript para funcionar adequadamente.</p>
      </noscript>
      <div class="group-title">
        <div class="group-image">
          <img id="group-image-preview" src="<?php echo $group_image; ?>">
          <a class="group-image-edit" id="group-image-edit" href="">
            <img src="/sites/default/files/images/icons/edit.png">
          </a>
        </div><div class="title">
          <input type="text" name="title" value="<?php echo $group_title; ?>" placeholder="Novo grupo" required>
          <input id="group-image-input" type="file" name="image-upload">
          <input type="hidden" name="photo" value="<?php echo $group_image_input; ?>">
        </div>
      </div>
      <div class="group-members">
        <h2>Membros</h2>
<?php
foreach($group_members as &$member){
  render_edit_group_member($member);
}
unset($member);
foreach($group_invited as &$member){
  render_edit_group_member($member,true);
}
unset($member);
?>
        <div class="member-box member-invite">
          <div class="invite">
            <label for="search-username">
<?php if($edit_mode==1){ ?>
              Convidar membro
<?php } else { ?>
              Adicionar membro
<?php } ?>
            </label><div class="search-input">
              <input id="search-username" type="text" name="username"
              ><a id="search-user-btn" href=""><img src="/sites/default/files/images/icons/search.png"></a>
            </div>
          </div>
          <div id="added-member" class="member"></div>
        </div>
<?php if(isset($group_id)){ ?>
        <a id="group-delete-btn" class="delete-btn" href="#<?php echo $group_id; ?>|<?php echo $group_data['name']; ?>">Excluir grupo</a>
<?php } ?>
      </div>
    </form>
    <script src="/logic/js/edit-group.js?v=1.0"></script>
  </div>
</div>
<?php

require_once($_SERVER['DOCUMENT_ROOT'] . '/logic/functions.php');
function render_edit_group_member(&$member,$invited=false){
  require($_SERVER['DOCUMENT_ROOT'] . '/logic/api/params.php');
  $is_admin=$member['isAdmin'];
  $user_image=$member['photo'];
  if( !( strpos($user_image,'http')===0 ) ){
    $user_image = $API_IMAGE_ROOT . $user_image;
  }
  $user_level=((int)$member['level'])+1;
  $level_image=get_level_image($user_level);
  $user_id=$member['id'];
  $user_name=$member['firstName'] . ' ' . $member['lastName'];
?>

        <div class="member member-box<?php if($is_admin){ echo ' admin';}  ?>" id="user-<?php echo $user_id; ?>">
          <div class="member-icon user-image">
            <div>
              <img src="<?php echo $user_image; ?>">
              <img class="level-image" src="<?php echo $level_image; ?>">
            </div>
          </div><div class="member-name">
            <?php echo $user_name; ?>
          </div><div class="member-status">
<?php if($invited){ // Only if waiting for response ?>
            <span class="status">Aguardando confirmação</span>
<?php } // End if waiting ?>
          </div><div class="member-action">
<?php if($is_admin){ ?>
            <span class="status">Administrador</span>
<?php } else if(!$invited){ //User can be removed ?>
            <a class="remove-member" href="#<?php echo $user_id; ?>">
              <span class="action">Remover</span>
              <img src="/sites/default/files/images/icons/remove.png">
            </a>
            <input type="hidden" name="member[]" value="<?php echo $user_id; ?>">
<?php } // End if not admin ?>
          </div>
        </div>


<?php
} // End function render group member
