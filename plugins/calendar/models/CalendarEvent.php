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
    
    public function getCreatorID() {
      return $this->created_by_id;
    }
    
    public function getCreator() {
      if (empty($this->created_by_id))
        return false;
      else
        /* hope this is correct id ;) */
        return User::findById($this->created_by_id)->name;
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

    public static function generateAllEventsBetween($class_name, $from, $to) {
      
      $generate = "CALL Calendar_GenerateDates('$from','$to')";
      self::getConnection()->exec($generate);
      self::logQuery($generate);      
      
      $sql = 'SELECT * FROM __dates JOIN '.self::tableNameFromClassName($class_name).' cal ON value = cal.date_from OR value BETWEEN cal.date_from AND cal.date_to';

      $stmt = self::getConnection()->query($sql);

      self::logQuery($sql);

      $objects = array();
      while ($object = $stmt->fetchObject($class_name))
          $objects[] = $object;

      return $objects;
    }    
}

?>