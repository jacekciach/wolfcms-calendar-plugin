<?php

  class CalendarTable {

    const DAYS = 7;

    const ROWS = 5;
    const COLS = self::DAYS;

    const PHP_SATURDAY = 6;
    const PHP_SUNDAY   = 0;

    const DAY_SHORT_NAME_LENGTH = 3;

    private $day_names;
    private $month_names;

    private $date;
    private $events_map;
    private $base_url;

    /**********************************************************************************************/

    private static function getDaysNames()
    {
      return array(
        0 => __('Monday'),
        1 => __('Tuesday'),
        2 => __('Wednesday'),
        3 => __('Thursday'),
        4 => __('Friday'),
        5 => __('Saturday'),
        6 => __('Sunday')
      );
    }

    private static function getMonthsNames()
    {
      return array(
         1 => __('January'),
         2 => __('February'),
         3 => __('March'),
         4 => __('April'),
         5 => __('May'),
         6 => __('June'),
         7 => __('July'),
         8 => __('August'),
         9 => __('September'),
        10 => __('October'),
        11 => __('November'),
        12 => __('December'),
      );
    }

    /**********************************************************************************************/

    private function displayHeader()
    {
      // calculate previous and next months relative to $date's month
      $date_prev_month = clone($this->date);
      $date_prev_month->modify("first day of previous month");
      $date_next_month = clone($this->date);
      $date_next_month->modify("first day of next month");

      ?>
        <!-- BEGIN: Calendar header: navigation bar -->
        <h3 class="calendar-table-header">

          <span class="prev">
            <a href="<?php printf('%s/%s/%s', $this->base_url, $date_prev_month->format('Y'), $date_prev_month->format('m')); ?>">
              <?php echo $this->month_names[$date_prev_month->format('n')].' '.$date_prev_month->format('Y'); ?>
            </a>
          </span>

          <?php echo $this->month_names[$this->date->format('n')].' '.$this->date->format('Y'); ?>

          <span class="next">
            <a href="<?php printf('%s/%s/%s', $this->base_url, $date_next_month->format('Y'), $date_next_month->format('m')); ?>">
              <?php echo $this->month_names[$date_next_month->format('n')].' '.$date_next_month->format('Y'); ?>
            </a>
          </span>

        </h3>
        <!-- END: Calendar header: navigation bar -->
      <?php
    } /* function displayHeader() */

    private function displayTable()
    {
      $today = new DateTime();
      $today->setTime(0,0);

      $date = clone($this->date);
      $date->setTime(0,0);

      /* Calculate a date to begin with */
      $month = $date->format('m');
      $year  = $date->format('Y');
      $date->setDate($year, $month, 1);
      $first_day_of_week = ($date->format('w') -1 + self::DAYS) % self::DAYS;
      $date->modify(-$first_day_of_week.' day');

      ?>
        <!-- BEGIN: Calendar table -->
        <table>

          <thead>
            <tr>
              <?php for ($col = 0; $col < self::COLS; ++$col): ?>
                <th><?php echo mb_substr($this->day_names[$col], 0, self::DAY_SHORT_NAME_LENGTH); ?></th>
              <?php endfor ?>
            </tr>
          <thead>

          <tbody>
            <?php for ($row = 0; $row < self::ROWS; ++$row): ?>
              <tr>

                <?php for ($col = 0; $col < self::COLS; ++$col): ?>

                  <?php

                    /* Calculate a <td> class */
                    $td_class = null;
                    $td_class .= ($date->format('m') != $month)             ? "day-grayed " : null;
                    $td_class .= ($date->format('w') == self::PHP_SATURDAY) ? "saturday "   : null;
                    $td_class .= ($date->format('w') == self::PHP_SUNDAY)   ? "sunday "     : null;
                    $td_class .= ($date == $today)                          ? "today "      : null;

                  ?>

                  <td class="<?php echo $td_class; ?>">

                    <?php $date_string = $date->format(CALENDAR_SQL_DATE_FORMAT); ?>
                    <?php if (array_key_exists($date_string, $this->events_map)): ?>
                      <span><a href="<?php printf('%s/%s', $this->base_url, $date_string); ?>"><?php echo $date->format('j'); ?></a></span>
                      <ul class="events-list">

                        <?php foreach ($this->events_map[$date_string] as $event): ?>
                          <li class="event-color-<?php echo ($event->getId()) % self::COLS; ?>">
                            <a href="<?php printf('%s/%s', $this->base_url, $event->getId()); ?>"><?php echo $event->getTitle(); ?></a>
                          </li>
                        <?php endforeach ?>

                      </ul>
                    <?php else: ?>
                      <span><?php echo $date->format('j'); ?></span>
                    <?php endif ?>

                  </td>

                  <?php $date->modify("+1 day"); /* Advance the date */ ?>

                <?php endfor /* cols */ ?>

              </tr>
            <?php endfor /* rows */ ?>
          </tbody>

        </table>
        <!-- END: Calendar table -->
      <?php

    } /* function displayTable(); */

    /**
     * Displays a calendar table.
     */
    public function display()
    {
      $this->displayHeader();
      $this->displayTable();
    }

    /**********************************************************************************************/

    /**
     * CalendarTable's constructor
     *
     * @param string    $base_url     base URL of the links generated by CalendarTable
     * @param DateTime  $date         date that points to a month, that will be displayed by CalendarTable. The day of the month is ignored
     * @param array     $events_map   array map with events that will be displayed in the table. The map has form key => array of events, where key is a date as a string in CALENDAR_SQL_DATE_FORMAT
     */
    public function __construct($base_url, DateTime $date, $events_map)
    {
      $this->base_url = $base_url;
      $this->date = $date;
      $this->events_map = $events_map;

      $this->day_names = self::getDaysNames();
      $this->month_names = self::getMonthsNames();
    }

  } /* class CalendarTable */

?>

<div id="calendar-plugin">

  <?php

    $old_mb_internal_encoding = mb_internal_encoding();
    mb_internal_encoding("UTF-8");

    $calendar = new CalendarTable($base_url, $date, $map);
    $calendar->display();

    mb_internal_encoding($old_mb_internal_encoding);

  ?>

</div>
