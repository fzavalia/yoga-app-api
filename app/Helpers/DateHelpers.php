<?php

namespace App\Helpers;

use Carbon\Carbon;

class DateHelpers
{
    public static function getStartingAndEndingDatesOfMonth($month)
    {
        return StartingAndEndingDatesOfMonth::fromMonth($month);
    }

    private function __construct()
    { }
}

class StartingAndEndingDatesOfMonth
{
    public static function fromMonth($month)
    {
        $start = Carbon::parse($month);
        $end = Carbon::parse($start);

        $start->setDay(1);
        $end->setDay($start->daysInMonth);

        return new StartingAndEndingDatesOfMonth($start, $end);
    }

    public $start;
    public $end;

    public function __construct($start, $end)
    {
        $this->start = $start;
        $this->end = $end;
    }
}
