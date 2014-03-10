<?php

if (!defined('IN_CMS')) { exit(); }

// Connect
$PDO = Record::getConnection();

$sql_table =
  "CREATE TABLE ".TABLE_PREFIX."calendar (
    id int NOT NULL AUTO_INCREMENT,
    created_by_id int NOT NULL,
    title varchar(256) COLLATE 'utf8_polish_ci' NOT NULL,
    date_from date NOT NULL,
    date_to date NULL,
    description text COLLATE 'utf8_polish_ci' NULL,
    PRIMARY KEY (id),
    KEY date_from (date_from)
  ) ENGINE=MyISAM";
  
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