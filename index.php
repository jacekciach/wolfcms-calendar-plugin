<?php

/**
 * @file index.php
 */

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


final class CalendarPlugin
{
  /**
   * Constructor is private, because we don't want to create instance if this class
   */
  private function __construct()
  {
    // empty
  }


  private static function registerInfos()
  {
    Plugin::setInfos(array(
      'id'                    => CALENDAR_ID,
      'title'                 => __('Calendar'),
      'description'           => __('Calendar'),
      'version'               => '1.1.3',
      'license'               => 'GPL',
      'author'                => 'Jacek Ciach',
      'require_wolf_version'  => '0.8.0',
      'website'               => 'https://github.com/jacekciach/wolfcms-calendar-plugin',
      'update_url'            => 'https://raw.githubusercontent.com/jacekciach/wolfcms-calendar-plugin/master/version.xml'
    ));
  }

  private static function registerAdminController()
  {
    Plugin::addController('calendar', __('Calendar'), 'admin_view', true);
  }

  private static function registerModel()
  {
    AutoLoader::addFile('CalendarEvent', CALENDAR_ROOT.'/models/CalendarEvent.php');
  }

  private static function registerBehaviour()
  {
    Behavior::add('calendar', CALENDAR_ID.'/behaviour.php');
  }

  public static function register()
  {
    self::registerInfos();
    self::registerAdminController();
    self::registerModel();
    self::registerBehaviour();
  }


  /**
   * Shows a month calendar.
   *
   * @param string   $slug  slug of the calendar page, the slug becomes a base for links shown in the calendar
   * @param DateTime $date  calendar shows this $date's month, null means "today"; the day of the month is ignored
   */
  public static function showCalendar($slug, DateTime $date = null)
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
      $events_map[$event->value][] = $event;
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

  /**
   * Shows en event.
   *
   * @param CalendarEvent $event        object of CalendarEvent class
   * @param boolean       $show_author  if true, a box with the event's author is shown below the event's description
   */
  public static function showEvent(CalendarEvent $event, $show_author = true)
  {
    /* Display an event */
    $view = new View(CALENDAR_VIEWS_FRONT.'/event', array(
      'event'       => $event,
      'show_author' => $show_author
    ));
    $view->display();
  }

  /**
   * Calls showEvent() in a loop, with $show_author = true
   *
   * @param array $events array of events
   * @see showEvent()
   */
  public static function showEvents(array $events)
  {
    foreach ($events as $event)
      showEvent($event);
  }

  /**
    * Validates if a string has a specific format and is a valid date
    *
    * @param string $date_str   string to be validated
    * @param string $format     date format, format accepted by PHP's date() @see http://php.net/manual/en/function.date.php
    * @retval boolean true if the string is a date and has the required format
    */
  public static function validateDateString($date_str, $format)
  {
    $datetime = DateTime::createFromFormat($format, $date_str);
    if ($datetime) {
      $errors = $datetime->getLastErrors();
      return ($errors['warning_count'] === 0) && ($errors['error_count'] === 0);
    }
    else
      return false;
  }

} // class CalendarPlugin


/**
 * Alias of CalendarPlugin::showCalendar()
 * @deprecated
 */
function showCalendar($slug, DateTime $date = null)
{
  CalendarPlugin::showCalendar($slug, $date);
}

/**
 * Alias of CalendarPlugin::showEvent()
 * @deprecated
 */
function showEvent(CalendarEvent $event, $show_author = true)
{
  CalendarPlugin::showEvent($event, $show_author);
}

/**
 * Alias of CalendarPlugin::showEvents()
 * @deprecated
 */
function showEvents(array $events)
{
  CalendarPlugin::showEvent($events);
}

/**
 * Alias of CalendarPlugin::validateDateString()
 * @deprecated
 */
function validateDateString($date_str, $format)
{
  return CalendarPlugin::validateDateString($date_str, $format);
}

/*******************************************************************************/

CalendarPlugin::register();
