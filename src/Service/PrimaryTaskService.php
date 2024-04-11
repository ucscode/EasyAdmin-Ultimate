<?php

namespace App\Service;

use DateTime;
use DateTimeInterface;

class PrimaryTaskService
{
    public function keygen(int $length = 10, bool $includeSpecialChars = false): string
    {
        $characters = [
            ...range(0, 9), 
            ...range('a', 'z'), 
            ...range('A', 'Z')
        ];

        if($includeSpecialChars) {
            $specialChars = ['!', '@', '#', '$', '%', '^', '&', '*', '(', ')', '[', ']', '{', '}', '/', ':', '.', ';', '|', '>', '~', '_', '-'];
            $characters = [...$characters, ...$specialChars];
        };

        $keyset = [];

        for($x = 0; $x < $length; $x++) {
            $randKey = array_rand($characters);
            $keyset[] = $characters[$randKey];
        };

        return implode($keyset);
    }

    public function truncateText(string $text, $length = 63): string
    {
        if (strlen($text) > $length) {
            $text = substr($text, 0, $length) . '&hellip;';
        }
        return $text;
    }

    public function relativeTime(DateTimeInterface|int|string $time, bool $verbose = false): string
    {
        if(!($time instanceof DateTimeInterface)) {
            $time = !is_numeric($time) ? new DateTime($time) : (new DateTime())->setTimestamp($time);
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

        foreach($formats as $key => $unit) {
            $value = $interval->{$key};
            if($value > 0) {
                $unit = !$verbose ? substr($unit, 0, 1) : ' ' . $unit . ($value > 1 ? 's' : '');
                $result = $interval->format("%{$key}{$unit} ago");
                break;
            }
        };

        return $result;
    }
}