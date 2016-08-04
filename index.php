<?php

if (!defined('IN_CMS')) { exit(); }

/* Defines */
define('CALENDAR_ID', 'calendar');
define('CALENDAR_ROOT', PLUGINS_ROOT.'/'.CALENDAR_ID);

define('CALENDAR_VIEWS_RELATIVE', CALENDAR_ID.'/views');
define('CALENDAR_VIEWS_RELATIVE_FRONT', CALENDAR_VIEWS_RELATIVE.'/front');
define('CALENDAR_VIEWS_RELATIVE_ADMIN', CALENDAR_VIEWS_RELATIVE.'/admin');

define('CALENDAR_VIEWS',       PLUGINS_ROOT.'/'.CALENDAR_VIEWS_RELATIVE);
define('CALENDAR_VIEWS_FRONT', PLUGINS_ROOT.'/'.CALENDAR_VIEWS_RELATIVE_FRONT);
define('CALENDAR_VIEWS_ADMIN', PLUGINS_ROOT.'/'.CALENDAR_VIEWS_RELATIVE_ADMIN);

define('CALENDAR_SQL_DATE_FORMAT', 'Y-m-d');
define('CALENDAR_DISPLAY_DATE_FORMAT', 'Y-m-d');

/* Basic information about the plugin */
Plugin::setInfos(array(
  'id'                    => CALENDAR_ID,
  'title'                 => __('Calendar'),
  'description'           => __('Calendar'),
  'version'               => '0.5',
  'license'               => 'GPL',
  'author'                => 'Jacek Ciach',
  'require_wolf_version'  => '0.8.0',
  'website'               => 'https://github.com/jacekciach/wolfcms-calendar-plugin',
  'update_url'            => 'https://raw.githubusercontent.com/jacekciach/wolfcms-calendar-plugin/master/version.xml'
));

/* Setup */

// setup plugin's admin controller
Plugin::addController('calendar', __('Calendar'), 'admin_view', true);

// setup CalenderEvent model
AutoLoader::addFile('CalendarEvent', CALENDAR_ROOT.'/models/CalendarEvent.php');

// setup calendar behaviour
Behavior::add('calendar', CALENDAR_ID.'/behaviour.php');


//////////////////////
/* GLOBAL FUNCTIONS */
//////////////////////

/** Shows a month calendar
  * @param $slug Slug of the calendar page. The slug becomes a base for links shown in the calendar.
  * @param $date Calendar shows this $date's month. Null means "today".
  */
function showCalendar($slug, DateTime $date = null)
{
  if (is_null($date))
    $date = new DateTime('now');

  $date_begin = clone($date);
  $date_begin->modify("first day of this month");
  $date_begin->modify("-1 week");

  $date_end = clone($date);
  $date_end->modify("last day of this month");
  $date_end->modify("+1 week");

  // generate events map
  $events = CalendarEvent::generateAllEventsBetween($date_begin, $date_end);
  $events_map = array();
  foreach ($events as $event) {
    $events_map[$event->value][] = $event->getTitle();
  }

  // display calendar table
  $view = new View(
                CALENDAR_VIEWS_FRONT.'/calendar',
                array(
                  'base_url' => get_url($slug),
                  'date'     => $date,
                  'map'      => $events_map
                ));
  $view->display();
}

/** Shows en event
  * @param $event An object of CalendarEvent class.
  * @param $show_author If true, a box with the event's author is shown below the event's description.
  */
function showEvent(CalendarEvent $event, $show_author = true)
{
  /* Display an event */
  $view = new View(CALENDAR_VIEWS_FRONT.'/event', array(
    'event'       => $event,
    'show_author' => $show_author
  ));
  $view->display();
}

/** Shows array of events. Calls showEvent() in a loop, with $show_author = true */
function showEvents(array $events)
{
  foreach ($events as $event)
    showEvent($event);
}

/** Validates if a string has a format of CALENDAR_DISPLAY_DATE_FORMAT and is a valid date
  * @param $date_str a string to be validated
  * @return true/false
  */
function validateDateString($date_str)
{
  $datetime = DateTime::createFromFormat(CALENDAR_DISPLAY_DATE_FORMAT, $date_str);
  if ($datetime) {
    $errors = $datetime->getLastErrors();
    return ($errors['warning_count'] === 0) && ($errors['error_count'] === 0);
  }
  else
    return false;
}
