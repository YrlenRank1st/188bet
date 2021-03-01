<script type="text/javascript">document.body.setAttribute("class",document.body.getAttribute("class")+" js");</script>
<main>
<?php if($tabs){ ?>
  <div class="tabs"><?php echo render($tabs); ?></div>
<?php } ?>
<div class="appblog">
<?php echo render($page['content']); ?>  
</div>
</main>