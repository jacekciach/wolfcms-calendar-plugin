<div id="calendar">
<?php

error_reporting(E_ALL | E_NOTICE);

class CalendarTable {

  const DAYS = 7;

  const ROWS = 5;
  const COLS = self::DAYS;

  const PHP_SATURDAY = 6;
  const PHP_SUNDAY   = 0;

  private $day_names;

  private $date;
  private $events_map;
  private $base_path;

  /**********************************************************************************************/

  private static function getDaysNames($day_name_format) {
    /* Based on:
     * http://stackoverflow.com/questions/2536748/how-to-create-array-of-a-week-days-name-in-php
     * http://stackoverflow.com/a/2536802
     */

    $names = array();
    $date = new DateTime("next Monday");
    for ($i = 0; $i < self::DAYS; ++$i) {
      $names[] = strftime($day_name_format, $date->getTimestamp());
      $date->modify("+1 day");
    }
    return $names;
  }

  /**********************************************************************************************/

  /** Prints the month containing the date
   * @param $_date null means ,today'
   */
  public function display() {
    $today = new DateTime();
    $today->setTime(0,0);
    try {
      $date = new DateTime($this->date);
      $date->setTime(0,0);
    }
    catch (Exception $e) {
      echo "<p class=\"error\">The date: $this->date is incorrect.</p>\n";
      return;
    }

    /* Calculate a date to begin with */
    $day   = $date->format('d');
    $month = $date->format('m');
    $year  = $date->format('Y');

    $date->setDate($year, $month, 1);
    $first_day_of_week = ($date->format('w') -1 + self::DAYS) % self::DAYS;
    $date->modify("-$first_day_of_week day");

    /* Table begin */
    echo "<!-- BEGIN: Calendar -->\n";
    echo "<table>\n";

    /* Print header */
    echo "<thead>\n";
    echo "<tr>";
    for ($col = 0; $col < self::COLS; ++$col)
      echo "<th>".$this->day_names[$col]."</th>";
    echo "</tr>\n";
    echo "</thead>\n";

    /* Print the month */
    echo "<tbody>\n";
    for ($row = 0; $row < self::ROWS; ++$row) {
      echo "<tr>";
      for ($col = 0; $col < self::COLS; ++$col) {

          /* Calculate a desired html class */
          $class = null;
          $class .= ($date->format('m') != $month)             ? "day-grayed " : null;
          $class .= ($date->format('w') == self::PHP_SATURDAY) ? "saturday "   : null;
          $class .= ($date->format('w') == self::PHP_SUNDAY)   ? "sunday "     : null;
          $class .= ($date == $today)                          ? "today "      : null;
          if (!empty($class)) $class = " class=\"$class\"";

          /* Print the day */
          $day_number = "<span>".$date->format('j')."</span>";
          echo "<td$class>";
          echo "$day_number";
          $date_string = $date->format('Y-m-d');
          if (array_key_exists($date_string, $this->events_map)) {
            echo "<ul class=\"events-list\">";
            foreach ($this->events_map[$date_string] as $event)
              echo "<li><a href='$this->base_path/$date_string'>$event</a></li>";
            echo "</ul>";
          }
          echo "</td>";

          /* Advance the date */
          $date->modify("+1 day");
      }
      echo "</tr>\n";
    }
    echo "</tbody>\n";

    /* Table end */
    echo "</table>\n";
    echo "<!-- END: Calendar -->\n";
  }

  /**********************************************************************************************/

  public function __construct($base_path, $date = null, $events_map = array()) {
    $this->base_path = $base_path;
    $this->day_names = self::getDaysNames("%a");
    $this->date = $date;
    $this->events_map = $events_map;
  }

} // END: class CalendarTable

$date = isset($date) ? $date : null;
$map  = isset($map)  ? $map  : array();

$datetime = new DateTime($date);
$datetime_prev = clone($datetime);
$datetime_prev->modify("first day of previous month");
$datetime_next = clone($datetime);
$datetime_next->modify("first day of next month");

echo "<h3>";
echo "<span class=\"prev\"><a href=\"$base_path/".$datetime_prev->format("Y")."/".$datetime_prev->format("m")."\">".strftime("%B %Y", $datetime_prev->getTimestamp())."</a></span>";
echo " ".strftime("%B %Y", $datetime->getTimestamp())." ";
echo "<span class=\"next\"><a href=\"$base_path/".$datetime_next->format("Y")."/".$datetime_next->format("m")."\">".strftime("%B %Y", $datetime_next->getTimestamp())."</a></span>";
echo "</h3>";

$calendar = new CalendarTable($base_path, $date, $map);
$calendar->display();

?>
</div>
