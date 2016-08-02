<?php

if (!defined('IN_CMS')) { exit(); }

define('CALENDAR_ID', 'calendar');
define('CALENDAR_ROOT', PLUGINS_ROOT.DS.CALENDAR_ID);
define('CALENDAR_VIEWS', CALENDAR_ROOT.'/views');

Plugin::setInfos(array(
  'id'                    => CALENDAR_ID,
  'title'                 => __('Calendar'),
  'description'           => __('Calendar'),
  'version'               => '0.5',
  'license'               => 'GPL',
  'author'                => 'Jacek Ciach',
  'require_wolf_version'  => '0.7.8',
  'website'               => 'https://github.com/jacekciach/wolfcms-calendar-plugin',
  'update_url'            => 'https://raw.githubusercontent.com/jacekciach/wolfcms-calendar-plugin/master/version.xml'
));

Plugin::addController('calendar', __('Calendar'), 'admin_view', true);
AutoLoader::addFile('CalendarEvent', CALENDAR_ROOT.'/models/CalendarEvent.php');
Behavior::add('calendar', CALENDAR_ID.'/behaviour.php');

function showCalendar($slug, $date = null) {
  $date_begin = new DateTime($date);
  $date_begin->modify("first day of this month");
  $date_begin->modify("-1 week");
  $date_begin = $date_begin->format('Y-m-d');

  $date_end = new DateTime($date);
  $date_end->modify("last day of this month");
  $date_end->modify("+1 week");
  $date_end = $date_end->format('Y-m-d');

  $events = CalendarEvent::generateAllEventsBetween($date_begin, $date_end);
  $events_map = array();
  foreach ($events as $event) {
    $events_map[$event->value][] = $event->getTitle();
  }

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

  $date_from = new DateTime($event->getDateFrom());
  $vars['date_from'] = strftime("%x", $date_from->getTimestamp());

  if (empty($event->date_to))
    $vars['date_to'] = null;
  else {
    $date_to = new DateTime($event->getDateTo());
    $vars['date_to'] = strftime("%x", $date_to->getTimestamp());
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
