<?php
require_once('loggedin.php');

//Get API data
ob_start();
$ENDPOINT='/User/MyTitles';
require($_SERVER['DOCUMENT_ROOT'] . '/logic/api/api-user.php');
$api=ob_get_contents();
ob_end_clean();
$results=json_decode($api,true);
//

$mine=array();
$other=array();
if($results['erro']==true){
?>
<div class="game-titles">
  <p>Aconteceu um erro. Por favor entre em contato com o administrador.</p>
</div>
<?php
} else {
  $titles=$results['data'];
  foreach($titles as $title){
    if($title['acquired']){
      $mine[]=$title;
    } else {
      $other[]=$title;
    }
  }

?>
<div class="game-titles">
<?php if(empty($titles)){ ?>
  <div class="no-titles">
    <p>Não há títulos para mostrar.</p>
  </div>
<?php } ?>
<?php if(!empty($mine)){ ?>
  <div class="my-titles">
    <h2>Meus títulos</h2>
    <div class="title-list">
<?php
    foreach($mine as $title){
      draw_title($title);
    }
?>
    </div>
  </div><?php
  }
  if(!empty($other)){
  ?><div class="other-titles">
    <h2>Títulos a conquistar</h2>
    <div class="title-list">
<?php
foreach($other as $title){
  draw_title($title);
}
?>
    </div>
  </div>
<?php } ?>
</div>
<?php

} // End if no errors

function draw_title(&$title){
  /*
    Renders the title as a div
    Title parameters: name, description, photo, acquired, id
  */
    ?><div class="game-title">
        <div class="white-box">
          <img src="<?php echo $title['photo']; ?>" alt="<?php echo $title['name']; ?>">
          <p class="name"><?php echo $title['name']; ?></p>
        </div>
        <div class="description">
          <p><?php echo $title['description']; ?></p>
        </div>
      </div><?php
}


?>