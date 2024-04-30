<?php

namespace App\Utils\Service;

use DateTime;
use DateTimeInterface;

class DateTimeUtils
{
    protected DateTimeInterface $dateTime;

    protected $dateIntervalLabels = [
        'y' => 'year',
        'm' => 'month',
        'd' => 'day',
        'h' => 'hour',
        'i' => 'minute',
        's' => 'second',
    ];

    public function __construct(DateTimeInterface|int|string $dateTime)
    {

        $this->dateTime = $this->harmonizeTime($dateTime);
    }
    
    public function getRelativeTime(bool $verbose = false): string
    {
        $label = 'just now';
        $interval = (new DateTime())->diff($this->dateTime);

        foreach ($this->dateIntervalLabels as $key => $timeUnit) {
            /**
             * @var int
             */
            $timeDiff = $interval->{$key};

            if ($timeDiff > 0) {
                $delimeter = !$verbose ? substr($timeUnit, 0, 1) : sprintf(' %s%s', $timeUnit, $timeDiff > 1 ? 's' : '');
                $displayUnit = sprintf('%s%s ago', $key, $delimeter);
                $label = $interval->format('%' . $displayUnit);
                break;
            }
        }

        return $label;
    }

    private function harmonizeTime(DateTimeInterface|int|string $dateTime): DateTimeInterface
    {
        if (!($dateTime instanceof DateTimeInterface)) {
            $dateTime = !is_numeric($dateTime) ? new DateTime($dateTime) : (new DateTime())->setTimestamp($dateTime);
        }
        return $dateTime;
    }
}