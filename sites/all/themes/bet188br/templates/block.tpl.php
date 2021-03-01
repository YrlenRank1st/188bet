<div id="<?php echo $block_html_id; ?>" class="<?php echo $classes; ?>"<?php print $attributes; ?>>
<?php if($block->subject){ ?>
  <h2<?php print $title_attributes; ?>><?php print $block->subject ?></h2>
<?php } ?>
  <div class="content"<?php print $content_attributes; ?>>
    <?php echo $content; ?>
  </div>
</div>

