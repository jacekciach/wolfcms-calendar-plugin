<?php

if (!defined('IN_CMS')) { exit(); }

/* Format and display data */
?>

<div class="event" id="event<?php echo $id; ?>">

  <h3><?php echo $date_from; ?><?php if (!empty($date_to)) echo ' '.__('to').' '.$date_to; ?>: <?php echo $title; ?></h3>

  <p>
    <?php echo $content; ?>
  </p>

  <?php if ($show_author): ?>
    <p class="info">
      <?php echo __('Posted by').': '.$author; ?>
    </p>
  <?php endif ?>

</div>
