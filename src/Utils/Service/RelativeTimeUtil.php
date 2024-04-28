<?php

namespace App\Utils\Service;

use DateTime;
use DateTimeInterface;

class RelativeTimeFormatter
{
    public function __construct(protected DateTimeInterface|int|string $time)
    {
        
    }
    
    public function format(bool $verbose = false): string
    {
        if (!($this->time instanceof DateTimeInterface)) {
            $time = !is_numeric($this->time) ? new DateTime($this->time) : (new DateTime())->setTimestamp($this->time);
        }

        $interval = (new DateTime())->diff($time);

        $formats = [
            'y' => 'year',
            'm' => 'month',
            'd' => 'day',
            'h' => 'hour',
            'i' => 'minute',
            's' => 'second'
        ];

        $result = 'Just Now';

        foreach ($formats as $key => $unit) {
            $value = $interval->{$key};
            if ($value > 0) {
                $unit = !$verbose ? substr($unit, 0, 1) : ' ' . $unit . ($value > 1 ? 's' : '');
                $result = $interval->format("%{$key}{$unit} ago");
                break;
            }
        }

        return $result;
    }
}