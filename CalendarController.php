<?php

if (!defined('IN_CMS')) { exit(); }

class CalendarController extends PluginController {

    private static function _checkPermission() {
        AuthUser::load();
        if (!AuthUser::isLoggedIn()) {
            redirect(get_url('login'));
        }
    }

    private function validate_post_data(array $post_data)
    {
        $errors = array();

        // title
        if (empty($post_data['title']))
            $errors['title'] = __('Title is required');

        // date_from
        if (empty($post_data['date_from']))
            $errors['date_from'] = __('Date from is required');
        elseif (validateDateString($post_data['date_from']) == false)
            $errors['date_from'] = __('Date from is invalid');

        // date_to
        if (!empty($post_data['date_to']) && validateDateString($post_data['date_to']) == false)
            $errors['date_to'] = __('Date to is invalid');

        return $errors;
    }

    private function display_update_view(array $data) {
        // prepare form fields values
        if (isset($data['post_data']))
            $form_values = $data['post_data'];
        else {
            $event = $data['event'];
            $form_values['title']       = $event->getTitle();
            $form_values['date_from']   = $event->getDateFrom() ? $event->getDateFrom()->format(CALENDAR_DISPLAY_DATE_FORMAT) : null;
            $form_values['date_to']     = $event->getDateTo()   ? $event->getDateTo()->format(CALENDAR_DISPLAY_DATE_FORMAT)   : null;
            $form_values['description'] = $event->getDescription();
        }

        // display the form
        $this->display(
            CALENDAR_VIEWS_RELATIVE_ADMIN.'/update',
            array(
                'form_values' => $form_values,
                'form_errors' => isset($data['errors']) ? $data['errors'] : array(),
                'updating'    => $data['updating'],
                'form_action' => get_url('plugin/'.CALENDAR_ID.'/'.($data['updating'] ? 'update/'.$data['event']->getId() : 'add'))
            )
        );
    }

    private function process_update_post(CalendarEvent $old_event)
    {
        $updating = (bool)($old_event->getId());

        if (isset($_POST['save']) && isset($_POST['event'])) {
            $post_data = array_map('trim', $_POST['event']);

            // validate the data and create error message
            $errors = $this->validate_post_data($post_data);
            if ($errors) {
                Flash::setNow('error', 'There are errors in the form.');
                return array(
                    'event'     => $old_event,
                    'updating'  => $updating,
                    'post_data' => $post_data,
                    'errors'    => $errors
                );
            }

            // if we'are updating an event, some data should be added to $post_data
            if ($updating) {
                $post_data['id'] = $old_event->getId();
                $post_data['created_by_id'] = $old_event->getAuthorID();
            }

            $updated_event = new CalendarEvent($post_data);
            $saved = $updated_event->save();

            if ($saved) {
                Flash::set( 'success', $updating ? __('The event has been updated.') : __('A new event has been created.') );
                redirect(get_url('plugin/'.CALENDAR_ID.'/events'));
            }
            else {
              Flash::setNow('error', __('Could not update this event!'));
              return array('event' => $updated_event, 'updating' => $updating);
            }
        }

        // if it's not POST, just return $old_event
        return array('event' => $old_event, 'updating' => $updating);
    }

    private function process_update(CalendarEvent $event)
    {
        $processed_data = $this->process_update_post($event);
        $this->display_update_view($processed_data);
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

}