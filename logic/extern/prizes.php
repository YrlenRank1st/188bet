<?php require_once(__DIR__ . '/../user/loggedin.php'); ?>
<div class="prizes">
  <div class="standard-top">
    <div class="standard-title">
      <h1>Prêmios</h1>
    </div>
  </div>
  <div class="monthly-prizes">
<?php require_once( $_SERVER['DOCUMENT_ROOT'] . '/logic/extern/monthly-prizes.php' ); ?>
  </div><div class="weekly-prizes">
<?php require_once( $_SERVER['DOCUMENT_ROOT'] . '/logic/extern/weekly-prizes.php' ); ?>
  </div><div class="prize-rules">
    <p>No caso de empate em pontuação por um prêmio o critério de desempate será a data e hora de entrada na competição. O jogador que se registrou primeiro receberá o prêmio principal.</p>
    <p>Todos os prêmios são provisórios. Caso um prêmio não esteja disponível, um equivalente será dado ao vencedor.</p>
  </div>
</div>