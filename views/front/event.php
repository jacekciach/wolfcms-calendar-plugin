<?php

if (!defined('IN_CMS')) { exit(); }

/* Format and display data */
?>

<div class="event" id="event<?php echo $event->getId(); ?>">

  <h3>
    <?php echo $event->getDateFrom()->format(CALENDAR_DISPLAY_DATE_FORMAT); ?><?php if ($event->getDateTo()) echo ' '.__('to').' '.$event->getDateTo()->format(CALENDAR_DISPLAY_DATE_FORMAT); ?>:
    <?php echo $event->getTitle();; ?>
  </h3>

  <p>
    <?php echo $event->getContent(); ?>
  </p>

  <?php if ($show_author): ?>
    <p class="info">
      <?php echo __('Posted by').': '.$event->getAuthor(); ?>
    </p>
  <?php endif ?>

</div>
