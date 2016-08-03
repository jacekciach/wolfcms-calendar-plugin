<?php

if (!defined('IN_CMS')) { exit(); }

define('CALENDAR_ID', 'calendar');
define('CALENDAR_ROOT', PLUGINS_ROOT.'/'.CALENDAR_ID);
define('CALENDAR_VIEWS_RELATIVE', CALENDAR_ID.'/views');
define('CALENDAR_VIEWS', PLUGINS_ROOT.'/'.CALENDAR_VIEWS_RELATIVE);

define('CALENDAR_SQL_DATE_FORMAT', 'Y-m-d');

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

Plugin::addController('calendar', __('Calendar'), 'admin_view', true);
AutoLoader::addFile('CalendarEvent', CALENDAR_ROOT.'/models/CalendarEvent.php');
Behavior::add('calendar', CALENDAR_ID.'/behaviour.php');

function showCalendar($slug, DateTime $date = null) {
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
  $calendar = new View(
                    CALENDAR_VIEWS.'/calendar_table',
                    array(
                      'base_path' => get_url($slug),
                      'date'      => $date,
                      'map'       => $events_map
                    ));
  $calendar->display();
}

function showEvent($event, $show_author = true) {
  /* Prepare the event's data */
  $vars['id']    = $event->getId();
  $vars['title'] = $event->getTitle();

  $vars['date_from'] = strftime("%x", $event->getDateFrom()->getTimestamp());

  if (empty($event->date_to))
    $vars['date_to'] = null;
  else {
    $vars['date_to'] = strftime("%x", $event->getDateTo()->getTimestamp());
  }

  $vars['days']    = $event->getLength();
  $vars['author']  = $event->getAuthor();
  $vars['content'] = $event->getContent();

  $vars['show_author'] = $show_author;

  /* Display an event */
  $view = new View(CALENDAR_VIEWS.'/event_frontend', $vars);
  $view->display();
}

function showEvents(array $events) {
  foreach ($events as $event)
    showEvent($event);
}
