<?php

if (!defined('IN_CMS')) { exit(); }

/**
  * A model of calendar event.
  *
  * This class is **NOT** a part of the public API.
  * Its fields and methods can be changed in any version of the plugin.
  */
class CalendarEvent extends Record {
    const TABLE_NAME = 'calendar';

    // the fields need to be protected, because it's used by WolfCMS' Record class
    protected $id;
    protected $created_by_id;
    protected $title;
    protected $date_from;
    protected $date_to;
    protected $description;

    private function checkDates()
    {
      if (!empty($this->date_to)) {

        if ($this->date_from == $this->date_to)
          $this->date_to = null;
        elseif ($this->date_from > $this->date_to)
          return false;

      }
      return true;
    }

    private function checkData() {
      return !empty($this->title) && !empty($this->date_from) && $this->checkDates();
    }

    /***********************************************************************************************/

    public function __construct(array $data = null)
    {
      parent::__construct($data);

      $this->id             = isset($this->id)            ? (int)$this->id                  : null;
      $this->created_by_id  = isset($this->created_by_id) ? (int)$this->created_by_id       : null;
      $this->title          = isset($this->title)         ? trim($this->title)              : null;
      $this->date_from      = !empty($this->date_from)    ? new DateTime($this->date_from)  : null;
      $this->date_to        = !empty($this->date_to)      ? new DateTime($this->date_to)    : null;
      $this->description    = isset($this->description)   ? trim($this->description)        : null;

    }

    public function getId()
    {
      return $this->id;
    }

    public function getAuthorID()
    {
      return $this->created_by_id;
    }

    public function getAuthor()
    {
      if (empty($this->created_by_id))
        return null;
      else {
        $user = User::findById($this->created_by_id);
    		if ($user instanceof User)
    			return $user->name;
    		else
    			return null;
	    }
    }

    public function getTitle()
    {
      return $this->title;
    }

    public function getDateFrom()
    {
      return $this->date_from;
    }

    public function getDateTo()
    {
      return $this->date_to;
    }

    public function getLength()
    {
      if (isset($this->date_to))
        return 1 + date_diff($this->date_from, $this->date_to)->days;
      else
        return 1;
    }

    public function getDescription()
    {
      return $this->description;
    }

    public function getContent()
    {
      return $this->getDescription();
    }

    /***********************************************************************************************/

    public function beforeSave()
    {

      if (empty($this->created_by_id)) {
        $user_id = AuthUser::getId();
        if ($user_id === false)
          return false;
        else
          $this->created_by_id = $user_id;
      }

      if ($this->checkData())
        return parent::beforeSave();
      else
        return false;
    }

    public function save()
    {
      if (isset($this->date_from)) $this->date_from = $this->date_from->format(CALENDAR_SQL_DATE_FORMAT);
      if (isset($this->date_to))   $this->date_to   = $this->date_to->format(CALENDAR_SQL_DATE_FORMAT);

      $result = parent::save();

      if (isset($this->date_from)) $this->date_from = new DateTime($this->date_from);
      if (isset($this->date_to))   $this->date_to   = new DateTime($this->date_to);

      return $result;
    }

    /***********************************************************************************************/

    public static function generateAllEventsBetween(DateTime $date_from, DateTime $date_to)
    {
      $class_name = get_called_class();
      $date_from_str = $date_from->format(CALENDAR_SQL_DATE_FORMAT);
      $date_to_str = $date_to->format(CALENDAR_SQL_DATE_FORMAT);

      /* Let (x,y) and (a,b) be intervals. We assume, by definition, that x <= y and a <= b.
         Then intersection of (x,y) and (a,b) is not empty iff (y >= a AND b >= x) */
      $objects = CalendarEvent::find(array(
        'where' => 'COALESCE(date_to, date_from) >= :A_date_from  AND :B_date_to >= date_from', // y >= a AND b >= x
        'values' => array(
          'A_date_from'  => $date_from_str,
          'B_date_to'    => $date_to_str,
        )
      ));

      $events = array();
      foreach ($objects as $object) {
        $date     = clone($object->date_from);
        $date_end = empty($object->date_to) ? clone($object->date_from) : clone($object->date_to);
        while ($date <= $date_end) {
          $event = clone($object);
          $event->value = $date->format(CALENDAR_SQL_DATE_FORMAT);
          $events[] = $event;
          $date->modify("+1 day");
        } /* while */
      } /* foreach */

      return $events;

    } /* function generateAllEventsBetween */

    static public function findEventsByDate(DateTime $date)
    {
      $date_str = $date->format(CALENDAR_SQL_DATE_FORMAT);
      return CalendarEvent::find(array(
        'where' => 'date_from = :date1 OR (:date2 BETWEEN date_from AND date_to)',
        'values' => array(
          'date1' => $date_str,
          'date2' => $date_str
        )
      ));
    }

    static public function findEventById($id)
    {
      return CalendarEvent::findById($id);
    }
}

?>