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

$sql_procedure =
   "CREATE PROCEDURE Calendar_GenerateDates (IN date_from date, IN date_to date)
    begin
      declare the_date date;
      create temporary table if not exists __dates (value date not null primary key);
      set the_date = date_from;
      while the_date <= date_to do
        replace into __dates values(the_date);
        set the_date = the_date + interval 1 day;
      end while;
    end";

if ($PDO->exec($sql_table) === false || $PDO->exec($sql_procedure) === false)
  Flash::set('error', __('Error occured during installing the calendar'));
else
  Flash::set('success', __('Calendar is enabled!'));

?>