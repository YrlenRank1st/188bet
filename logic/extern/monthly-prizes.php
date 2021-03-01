<?php
require_once($_SERVER['DOCUMENT_ROOT'] . '/logic/functions.php');
/*
$PORTUGUESE_MONTHS=array(
  '1'=>'Janeiro',
  '2'=>'Fevereiro',
  '3'=>'Março',
  '4'=>'Abril',
  '5'=>'Maio',
  '6'=>'Junho',
  '7'=>'Julho',
  '8'=>'Agosto',
  '9'=>'Setembro',
  '10'=>'Outubro',
  '11'=>'Novembro',
  '12'=>'Dezembro',
);
*/

$mprizes_data=get_monthly_prizes();
if(!empty($mprizes_data)){
?>
<div class="prize-list">
  <h2>Prêmios Mensais</h2>
  <table>
    <tbody>
<?php foreach($mprizes_data as &$mprize){
  $td_class=($mprize['month']==1)?' class="winner"':'';
?>
      <tr>
        <td<?php echo $td_class; ?>><?php echo $mprize['month']; ?></td>
        <td><?php echo $mprize['name']; ?></td>
      </tr>
<?php
}
unset($mprize);
?>
    </tbody>
  </table>
</div>
<?php

}

?>