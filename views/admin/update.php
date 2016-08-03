<?php

if (!defined('IN_CMS')) { exit(); }

?>

<h1><?php echo $updating ? __('Edit the event') : __('A new event'); ?></h1>

<form action="<?php echo $form_action; ?>" method="post">
    <fieldset style="padding:0.5em;">
        <legend style="padding: 0em 0.5em 0em 0.5em; font-weight: bold;"><?php echo __('Edit the event'); ?></legend>
            <table class="fieldset" cellspacing="0" cellpadding="0" border="0">
                <tr>
                    <td class="label">
                        <label for="event-title">
                            <?php echo __('Title'); ?><br>
                        </label>
                    </td>
                    <td class="field <?php if (isset($form_errors['title'])) echo 'error-with-field'; ?>">
                        <input type="text" id="notes-title" name="event[title]" class="textbox" value="<?php echo $form_values['title']; ?>" />
                        <?php if (isset($form_errors['title'])): ?>
                            <br><small><?php echo $form_errors['title']; ?><small>
                        <?php endif ?>
                    </td>
                </tr>
                <tr>
                    <td class="label">
                        <label for="event-date_from">
                            <?php echo __('Date from'); ?><br>
                        </label>
                    </td>
                    <td class="field <?php if (isset($form_errors['date_from'])) echo 'error-with-field'; ?>">
                        <input type="text" id="event-date_from" name="event[date_from]" size="10" value="<?php echo $form_values['date_from']; ?>" />
                        <img class="datepicker" onclick="displayDatePicker('event[date_from]');" src="<?php echo PATH_PUBLIC; ?>wolf/admin/images/icon_cal.gif" alt="<?php echo __('Show Calendar'); ?>" />
                        <?php if (isset($form_errors['date_from'])): ?>
                            <br><small><?php echo $form_errors['date_from']; ?><small>
                        <?php endif ?>
                    </td>
                </tr>
                <tr>
                    <td class="label">
                        <label for="event-date_to">
                            <?php echo __('Date to'); ?><br>
                        </label>
                        <small><?php echo " (".__('not required').")"; ?></small><br>
                    </td>
                    <td class="field <?php if (isset($form_errors['date_to'])) echo 'error-with-field'; ?>">
                        <input type="text" id="event-date_to" name="event[date_to]" size="10" value="<?php echo $form_values['date_to']; ?>" />
                        <img class="datepicker" onclick="displayDatePicker('event[date_to]');" src="<?php echo PATH_PUBLIC; ?>wolf/admin/images/icon_cal.gif" alt="<?php echo __('Show Calendar'); ?>" />
                        <?php if (isset($form_errors['date_to'])): ?>
                            <br><small><?php echo $form_errors['date_to']; ?><small>
                        <?php endif ?>
                    </td>
                </tr>
                <tr>
                    <td class="label">
                        <label for="event-description">
                            <?php echo __('Description'); ?><br>
                        </label>
                        <small><?php echo " (".__('not required').")"; ?></small><br>
                    </td>
                    <td class="text <?php if (isset($form_errors['description'])) echo 'error-with-field'; ?>">
                        <textarea id="event_description" name="event[description]" class="textarea" rows="10" cols="40"><?php echo $form_values['description']; ?></textarea>
                        <?php if (isset($form_errors['description'])): ?>
                            <br><small><?php echo $form_errors['description']; ?><small>
                        <?php endif ?>
                    </td>
                </tr>
        </table>
    </fieldset>
    <p class="buttons" align="right">
        <input class="button" type="submit" name="save" value="<?php echo __('Save'); ?>" /> or <a href="<?php echo get_url('plugin/'.CALENDAR_ID.'/events'); ?>"><?php echo __('Cancel'); ?></a>
    </p>
</form>