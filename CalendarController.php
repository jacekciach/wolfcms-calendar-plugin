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
        $this->assignToLayout('sidebar', new View(CALENDAR_VIEWS_ADMIN.'/sidebar'));
    }

    // Take me to all events
    public function index() {
        $this->events();
    }

    // Documentation
    public function documentation() {
        $this->display(CALENDAR_VIEWS_RELATIVE_ADMIN.'/documentation');
    }

    // Add new event
    public function new_event(){
        $this->display(CALENDAR_VIEWS_RELATIVE_ADMIN.'/update');
    }

    // List all events
    public function events() {
        $events = CalendarEvent::findAllFrom('CalendarEvent','id=id ORDER BY date_from DESC, date_to DESC');
        $this->display(CALENDAR_VIEWS_RELATIVE_ADMIN.'/events', array('events' => $events));
    }

    public function update($id){
        $event = CalendarEvent::findEventById($id);
        $this->display(CALENDAR_VIEWS_RELATIVE_ADMIN.'/update', array('event' => $event));
    }

    // Delete event
    public function delete($id) {
        $notes = CalendarEvent::findByIdFrom('CalendarEvent', $id);
        $notes->delete();
        Flash::set('success', __('The event has been successfully deleted'));

        redirect(get_url('plugin/'.CALENDAR_ID.'/events'));
    }

    public function update_event(){

            if (!isset($_POST['save'])) {
                Flash::set('error', __('Could not update this event!'));
            }
            else {
                use_helper('Kses');

                /* Prepare the data */
                $post_data = $_POST['event'];
                if (isset($post_data['id']))
                  $post_data['id'] = kses(trim($post_data['id']), array());

                $event = new CalendarEvent($post_data);

                /* Check data and, if correct, save to DB */
                if ($event->save()) {
                  if (isset($data['id']))
                    Flash::set('success', __('The event has been updated.'));
                  else
                    Flash::set('success', __('A new event has been created.'));

                  redirect(get_url('plugin/'.CALENDAR_ID.'/events'));
                }
                else {
                  Flash::setNow('error', __('There are errors in the form.'));
                  $this->display(CALENDAR_VIEWS_RELATIVE_ADMIN.'/update', array('event' => $event));
                }
        }

    }
}