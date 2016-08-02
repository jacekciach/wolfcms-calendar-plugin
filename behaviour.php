<?php

if (!defined('IN_CMS')) { exit(); }

class Calendar {

    private $page;
    private $params;

    private function beginCapture() {
      ob_start();
    }

    private function endCapture() {
      $this->page->part->body->content_html = ob_get_contents();
      ob_end_clean();
    }

    public function __construct(&$page, $params) {
        $this->page   = & $page;
        $this->params = $params;

        switch (count($params)) {
          case 0:
            break;
          case 1:
            $slug = $params[0];
            /* We try to find a subpage of the calendar page, so the event's page can be customized */
            $page_found = Page::findBySlug($slug, $this->page, true);
            if (is_a($page_found, "Page"))
              $this->page = $page_found; /* A subpage is found, so display it */
            else {
              /* A subpage is not found, so try to parse a date and then create an event's page */
              try {
                $datetime = new DateTime($slug);
              }
              catch (Exception $e) {
                pageNotFound();
                exit();
              }
              $events = CalendarEvent::findEventsByDate($datetime->format('Y-m-d'));
              $this->page->title = strftime("%x", $datetime->getTimestamp()); /* The date should be localized */
              $this->beginCapture();
              showEvents($events);
              $this->endCapture();
            }
            break;
          case 2:
            $year  = $params[0];
            $month = $params[1];
            $this->beginCapture();
            $this->showCalendarForMonth($year, $month);
            $this->endCapture();
            break;
          default:
            pageNotFound();
            exit();
        }
    }

    private function showCalendarForMonth($year, $month) {
      $date = "$year-$month-1";
      showCalendar($this->page->slug, $date);
    }
}
