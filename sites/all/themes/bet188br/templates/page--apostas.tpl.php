<script type="text/javascript">document.body.className+=" js";</script>
<main>
<?php if($tabs){ ?>
  <div class="tabs"><?php echo render($tabs); ?></div>
<?php } ?>
<div class="blog-apostas">
<?php echo render($page['content']); ?>  
</div>
</main>