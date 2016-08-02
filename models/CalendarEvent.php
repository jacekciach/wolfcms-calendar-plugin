<?php

if (!defined('IN_CMS')) { exit(); }
 
class CalendarEvent extends Record {
    const TABLE_NAME = 'calendar';

    public $id;
    public $created_by_id;    
    public $title;
    public $date_from;    
    public $date_to;    
    public $description;
    
    public function checkData() {
      $this->title = trim($this->title);
      $this->date_from = trim($this->date_from);
      $this->date_to = trim($this->date_to);            
      $this->description = trim($this->description);
      
      if (empty($this->title) || empty($this->date_from))
        return false;
        
      return $this->checkDates();
    }
    
    public function checkDates() {
      try {
        $from = new DateTime($this->date_from);
      }
      catch (Exception $e) {
        return false;
      }              
    
      if (!empty($this->date_to)) {      
        try {
          $to = new DateTime($this->date_to);
        }
        catch (Exception $e) {
          return false;
        }
        
        if ($from == $to)
          $this->date_to = "";
        else if ($from > $to)
          return false;        
      }
      return true;      
    }

    public function getId() {
      return $this->id;
    }
    
    public function getAuthorID() {
      return $this->created_by_id;
    }
    
    public function getAuthor() {
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
	
    public function getTitle() {
      return $this->title;
    }
	
    public function getDateFrom() {
      return $this->date_from;
    }

    public function getDateTo() {
      return $this->date_to;
    }
    
    public function getLength() {
      if (isset($this->date_to))
        return 1 + date_diff(new DateTime($this->date_from), new DateTime($this->date_to))->days;
      else
        return 1;
    }
    
    public function getDescription() {
      return $this->description;
    }
    
    public function getContent() {
      return $this->getDescription();
    }
    
    public function beforeSave() {
      if ($this->checkData()) {    
        /* if creator's id is known, then just return true */
        if (empty($this->created_by_id)) {
          /* if it's not known -- get it */
          $user_id = AuthUser::getId();
          if ($user_id === false)
            return false;
          else 
            $this->created_by_id = $user_id;          
        }
        /* everything is ok */      
        return true;
      }
      else
        return false;      
    }    

    public static function generateAllEventsBetween($from, $to) {    
      $class_name = get_called_class();

      $sql = "SELECT * FROM ".self::tableNameFromClassName($class_name)." WHERE date_from BETWEEN '$from' AND '$to' OR date_to BETWEEN '$from' AND '$to'";

      $stmt = self::getConnection()->query($sql);

      self::logQuery($sql);

      $objects = array();
      while ($object = $stmt->fetchObject($class_name))
          $objects[] = $object;

      $events = array();
      foreach ($objects as $object) {
        $date     = new DateTime($object->date_from);
        $date_end = empty($object->date_to) ? new DateTime($object->date_from) : new DateTime($object->date_to);
        while ($date <= $date_end) {
          $event = clone($object);
          $event->value = $date->format('Y-m-d');
          $events[] = $event;
          $date->modify("+1 day");
        } /* while */
      } /* foreach */

      return $events;

    } /* function generateAllEventsBetween */
    
    static public function findEventsByDate($date) {
      return self::findAllFrom(get_called_class(), "date_from = '$date' OR '$date' BETWEEN date_from AND date_to");      
    }
    
    static public function findEventById($id) {
      return self::findOneFrom(get_called_class(), "id = $id");      
    }                    
}

?>