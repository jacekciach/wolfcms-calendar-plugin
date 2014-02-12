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
              $events = Calendar::getEventsByDate($datetime->format('Y-m-d'));
              $this->page->title = strftime("%x", $datetime->getTimestamp()); /* The date should be localized */
              $this->beginCapture();
              Calendar::showEventsContent($events);
              $this->endCapture();
            }            
            break;
          case 2:
            $year  = $params[0];
            $month = $params[1];
            $this->beginCapture();            
            $this->showCalendarForDate($year, $month);
            $this->endCapture();
            break;
          default:
            pageNotFound();
            exit();
        }      
    }
    
    static public function getEventsByDate($date) {
      return CalendarEvent::findAllFrom("CalendarEvent", "date_from = '$date' OR '$date' BETWEEN date_from AND date_to");      
    }
    
    static public function showEvent($event, $show_creator = true) {   
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
      $vars['author']  = $event->getCreator();
      $vars['content'] = $event->getContent();
      
      $vars['show_author'] = true;    
     
      /* Display an event */
      $view = new View(PLUGINS_ROOT.DS.CALENDAR_VIEWS.'/event_frontend', $vars);
      $view->display();  
    }
    
    public function showCalendarForDate($year, $month) {
      $date = "$year-$month-1";    
      showCalendar($this->page->slug, $date);      
    }    

    static private function showEventsContent($events) {
      ob_start();
      foreach ($events as $event)
        Calendar::showEvent($event);
  
    }

}
