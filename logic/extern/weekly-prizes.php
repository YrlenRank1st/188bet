<?php
require_once($_SERVER['DOCUMENT_ROOT'] . '/logic/functions.php');
$wprizes_data=get_weekly_prizes();
if(!empty($wprizes_data)){
?>
<div class="prize-list">
  <h2>Prêmios semanais</h2>
  <table>
    <tbody>
<?php foreach($wprizes_data as &$wprize){
$td_class=($wprize['week']==1)?' class="winner"':'';
?>
      <tr>
        <td<?php echo $td_class; ?>><?php echo $wprize['week']; ?></td>
        <td><?php echo $wprize['name']; ?></td>
      </tr>
<?php
}
unset($wprize);
?>
    </tbody>
  </table>
</div>
<?php

}

?>