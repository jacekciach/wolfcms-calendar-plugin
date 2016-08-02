<?php

if (!defined('IN_CMS')) { exit(); }

?>
<div class="box">
  <p class="button"><a href="<?php echo get_url('plugin/calendar/events'); ?>"><img src="<?php echo PLUGINS_URI; ?>/calendar/images/view.png" align="middle" alt="View all events" /> <?php echo __('View all events'); ?></a></p>
  <p class="button"><a href="<?php echo get_url('plugin/calendar/new_event'); ?>"><img src="<?php echo PLUGINS_URI; ?>/calendar/images/new_event.png" align="middle" alt="New event" /> <?php echo __('New event'); ?></a></p>
  <p class="button"><a href="<?php echo get_url('plugin/calendar/documentation'); ?>"><img src="<?php echo PLUGINS_URI; ?>/calendar/images/doc.png" align="middle" alt="Docs" /> <?php echo __('Documentation'); ?></a></p>
</div>
