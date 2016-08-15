<?php

if (!defined('IN_CMS')) { exit(); }

/**
  * A *calendar* behaviour.
  *
  * This class is **NOT** a part of the public API.
  * Its fields and methods can be changed in any version of the plugin.
  */
class Calendar {

    private $page;
    private $params;

    private function beginCapture()
    {
      ob_start();
    }

    private function endCapture()
    {
      $this->page->part->body->content_html = ob_get_contents();
      ob_end_clean();
    }

    private function pageNotFound()
    {
      pageNotFound();
      exit();
    }

    public function __construct(&$page, $params)
    {
        $this->page   = & $page;
        $this->params = $params;

        switch (count($params)) {
          case 0:
            break; // main page of calendar behaviour, don't change anything

          case 1: // there's one parameter after slash
            $slug = $params[0];

            /* We try to find a subpage of the calendar page, so the event's page can be customized */
            $page_found = Page::findBySlug($slug, $this->page, true);
            /* A subpage is found, so display it */
            if (is_a($page_found, "Page"))
              $this->page = $page_found;
            /* A subpage is not found, so try to parse a date and then create an event's page */
            elseif (CalendarPlugin::validateDateString($slug, CALENDAR_SQL_DATE_FORMAT)) {
              $date = new DateTime($slug);
              $events = CalendarEvent::findEventsByDate($date);
              $this->page->title = $date->format(CALENDAR_DISPLAY_DATE_FORMAT);
              $this->beginCapture();
              CalendarPlugin::showEvents($events);
              $this->endCapture();
            }
            /* Or maybe it's an event Id? */
            elseif (is_numeric($slug) && ($event = CalendarEvent::findById((int)$slug))) {
              $this->page->title = $event->getTitle();
              $this->beginCapture();
              CalendarPlugin::showEvent($event);
              $this->endCapture();
            }
            else
              $this->pageNotFound();

            break;

          case 2: // there're two parameters after slash
            $year  = (int)$params[0];
            $month = (int)$params[1];

            $date = new DateTime();
            $date->setDate($year, $month, 1);

            $this->beginCapture();
            CalendarPlugin::showCalendar($this->page->slug, $date);
            $this->endCapture();
            break;

          default:
            $this->pageNotFound();
        }
    }

}
