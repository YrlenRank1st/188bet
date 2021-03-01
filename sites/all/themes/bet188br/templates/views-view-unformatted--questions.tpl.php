<?php if(!empty($title)) { ?>
  <div class="question-group white-box">
    <h2 class="white-title"><?php echo $title; ?></h2>
<?php
}
foreach ($rows as $id => $row) {
  ?>
    <div<?php
  if ($classes_array[$id]) {
    ?> class="<?php
    echo $classes_array[$id];
    ?>"<?php
  }
  ?>>
      <?php print $row; ?>
    </div>
<?php
}
if(!empty($title)) { ?>
  </div>
<?php
}
