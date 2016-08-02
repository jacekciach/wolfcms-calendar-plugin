<?php

if (!defined('IN_CMS')) { exit(); }

/* Format and display data */
?>
  
<div class="event" id="event<?=$id?>">
<h3><?=$date_from?><?php if (!empty($date_to)) echo " ".__("to")." $date_to"; ?>: <?=$title?></h3>
<p><?=$content?></p>
<?php if ($show_author): ?>
<p class="info"><?php echo __("Posted by").": $author"; ?></p>
<?php endif; ?>  
</div>    
      