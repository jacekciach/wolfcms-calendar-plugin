<?php

if (!defined('IN_CMS')) { exit(); }

class CalendarController extends PluginController {

    private static function _checkPermission() {
        AuthUser::load();
        if (!AuthUser::isLoggedIn()) {
            redirect(get_url('login'));
        }
    }

    public function __construct() {
        self::_checkPermission();   

        $this->setLayout('backend');
        $this->assignToLayout('sidebar', new View('../../plugins/calendar/views/sidebar'));
    }

    // Take me to all events
    public function index() {
        $this->events();
    }

    // Documentation
    public function documentation() {
        $this->display(CALENDAR_VIEWS.'/documentation');
    }

    // Add new event
    public function new_event(){
        $this->display(CALENDAR_VIEWS.'/update');
    }
    
    // List all events
    public function events() {
        $events = CalendarEvent::findAllFrom('CalendarEvent','id=id ORDER BY date_from DESC, date_to DESC');
        $this->display(CALENDAR_VIEWS.'/events', array('events' => $events));
    }
    
    public function update($id){
        $event = CalendarEvent::findByIdFrom('CalendarEvent', $id);
        $this->display(CALENDAR_VIEWS.'/update', array('event' => $event));
    }

    // Delete event
    public function delete($id) {
        $notes = CalendarEvent::findByIdFrom('CalendarEvent', $id);
        $notes->delete();
        Flash::set('success', __('The event has been successfully deleted'));

        redirect(get_url('plugin/calendar/events'));
    }

    public function update_event(){

            if (!isset($_POST['save'])) {
                Flash::set('error', __('Could not update this event!'));
            }
            else {
                use_helper('Kses');
                                            
                /* Prepare the data */                            
                $data = $_POST['event'];
                if (isset($data['id']))
                  $data['id'] = kses(trim($data['id']), array());

                $event = new CalendarEvent();

                if (isset($data['id'])) {
                  $event->id            = $data['id'];
                  $event->created_by_id = $data['created_by_id'];
                }                 
                  
                $event->title       = $data['title'];
                $event->date_from   = $data['date_from'];
                $event->date_to     = $data['date_to'];
                $event->description = $data['description'];                                
                
                /* Check data and, if correct, save to DB */
                if ($event->checkData() && $event->save()) {
                  if (isset($data['id']))
                    Flash::set('success', __('The event has been updated.'));
                  else
                    Flash::set('success', __('A new event has been created.'));
                  
                  redirect(get_url('plugin/calendar/events'));
                }
                else {
                  Flash::setNow('error', __('There are errors in the form.'));                
                  $this->display(CALENDAR_VIEWS.'/update', array('event' => $event));                
                }
        }

    }
}