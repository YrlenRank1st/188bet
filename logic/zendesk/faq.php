<?php
require_once('params.php');

/* 1- Get data */

$result=zd_api_call($ZD_API_URL . 'help_center/pt-br/sections/360000245012/articles.json');
$json=json_decode($result,true);

/* 3- Display questions */
?>
<div class="faq">
  <div class="question-group white-box">
    <h2 class="white-title">Perguntas Frequentes</h2>
<?php
while(isset($json)){
  $articles=&$json['articles'];
  foreach($articles as &$article){
?>
    <div class="question">
      <p class="title"><?php echo $article['title']; ?></p>
      <div class="body"><?php echo $article['body']; ?></div>
    </div>
<?php

  }
  unset($article);
  unset($articles);
  
  if($json['next_page']){
    $result=zd_api_call($ZD_API_URL . 'help_center/pt-br/sections/360000245012/articles.json');
    $json=json_decode($result,true);
  } else {
    $json=null;
  }

}
?>
  </div>
  <div class="text">
    <p>Ainda precisa de ajuda? Entre em <a href="/contact">contato</a>.</p>
  </div>
</div>
<?php drupal_add_js('/logic/js/faq.js','file'); ?>