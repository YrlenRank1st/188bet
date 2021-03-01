<?php

$EN_MONTHS=array(
  'january','february','march','april',
  'may','june','july','august',
  'september','october','november','december'
);
$PT_MONTHS=array(
  'Janeiro','Fevereiro',htmlentities('Março'),'Abril',
  'Maio','Junho','Julho','Agosto',
  'Setembro','Outubro','Novembro','Dezembro'
);
$date_string=str_ireplace($EN_MONTHS,$PT_MONTHS,date('F j, Y',$created));
?><article class="blog-article-page">
  <?php echo render($content['field_image']); ?>
  <div class="content">
    <div class="date-created"><?php echo $date_string; ?></div>
    <h1><?php echo $title; ?></h1>
    <?php print render($content); ?>
  </div>
</article>
