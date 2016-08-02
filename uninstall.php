<?php

if (!defined('IN_CMS')) { exit(); }

$PDO = Record::getConnection();

if($PDO->exec('DROP TABLE IF EXISTS '.TABLE_PREFIX.'calendar') === false) {
    Flash::set('error', __('Calendar is not uninstalled!'));
    redirect(get_url('setting'));
}
else
	Flash::set('success', __('Calendar is uninstalled!'));

?>
