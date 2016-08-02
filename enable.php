<?php

if (!defined('IN_CMS')) { exit(); }

// Connect
$PDO = Record::getConnection();

$sql_table =
  "CREATE TABLE ".TABLE_PREFIX."calendar (
    id            INT           NOT NULL AUTO_INCREMENT,
    created_by_id INT           NOT NULL,
    title         VARCHAR(256)  NOT NULL,
    date_from     DATE          NOT NULL,
    date_to       DATE              NULL,
    description   TEXT              NULL,
    PRIMARY KEY (id),
    KEY date_from (date_from)
  ) ENGINE=MyISAM DEFAULT CHARSET=utf8";

if ($PDO->exec($sql_table) === false)
  Flash::set('error', __('Error occured during installing the calendar'));
else
  Flash::set('success', __('Calendar is enabled!'));

?>