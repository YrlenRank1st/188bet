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
foreach ($fields as $id=>$field) {
  if(!empty($field->separator)) {
    print $field->separator;
  }
  echo $field->wrapper_prefix;
  echo $field->label_html;
  $content=$field->content;
  $span_pos=strpos($content,'<span class="date">');
  if(!($span_pos===false)){
    $span_end_pos=strpos($content,'</span>',$span_pos);
    $part1=substr($content,0,$span_pos);
    $part2=substr($content,$span_pos,$span_end_pos-$span_pos);
    $part3=substr($content,$span_end_pos,strlen($content));
    $content=$part1 . str_ireplace($EN_MONTHS,$PT_MONTHS,$part2) . $part3;
  }
  echo $content;
  echo $field->wrapper_suffix;
}
?>