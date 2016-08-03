<?php

if (!defined('IN_CMS')) { exit(); }

class CalendarController extends PluginController {

    private static function _checkPermission() {
        AuthUser::load();
        if (!AuthUser::isLoggedIn()) {
            redirect(get_url('login'));
        }
    }

    private function process_update_post(CalendarEvent $old_event)
    {
        $updating = (bool)($old_event->getId());

        if (isset($_POST['save']) && isset($_POST['event'])) {
            $post_data = $_POST['event'];

            // if we'are updating an event, some data should be added to $post_data
            if ($updating) {
                $post_data['id'] = $old_event->getId();
                $post_data['created_by_id'] = $old_event->getAuthorID();
            }

            $updated_event = new CalendarEvent($post_data);
            $saved = $updated_event->save();

           /* Check data and, if correct, save to DB */
            if ($saved) {
                Flash::set( 'success', $updating ? __('The event has been updated.') : __('A new event has been created.') );
                redirect(get_url('plugin/'.CALENDAR_ID.'/events'));
            }
            else {
              Flash::setNow('error', __('There are errors in the form.'));
              return $updated_event;
            }
        }

        // if it's not POST, just return $old_event
        return $old_event;
    }

    private function display_update_view(CalendarEvent $event) {
        $updating = (bool)($event->getId());
        $this->display(
            CALENDAR_VIEWS_RELATIVE_ADMIN.'/update',
            array(
                'event' => $event,
                'updating' => $updating,
                'form_action' => get_url('plugin/'.CALENDAR_ID.'/'.($updating ? 'update/'.$event->getId() : 'add'))
            )
        );
    }

    private function process_update(CalendarEvent $event)
    {
        $event = $this->process_update_post($event);
        $this->display_update_view($event);
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

    // List all events
    public function events() {
        $events = CalendarEvent::find(array('order' => 'date_from DESC, date_to DESC'));
        $this->display(CALENDAR_VIEWS_RELATIVE_ADMIN.'/events', array('events' => $events));
    }

    // Add new event
    public function add() {
        $event = new CalendarEvent();
        $this->process_update($event);
    }

    // Edit an event
    public function update($id) {
        $event = CalendarEvent::findEventById( (int)$id );
        if (empty($event))
            redirect(get_url('plugin/'.CALENDAR_ID.'/add')); // if $id is invalid -- redirect to 'add event'
        $this->process_update($event);
    }

    // Delete event
    public function delete($id) {
        $event = CalendarEvent::findEventById($id);
        $event->delete();
        Flash::set('success', __('The event has been successfully deleted'));

        redirect(get_url('plugin/'.CALENDAR_ID.'/events'));
    }

    // Documentation
    public function documentation() {
        $this->display(CALENDAR_VIEWS_RELATIVE_ADMIN.'/documentation');
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


        }

    }
}