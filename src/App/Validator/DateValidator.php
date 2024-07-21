<?php

declare(strict_types=1);

namespace App\Validator;

use DateTime;

class DateValidator
{
    public static function isValidDate(string $date): bool
    {
        $format = 'Y-m-d';
        $dateTime = DateTime::createFromFormat($format, $date);

        if (!$dateTime || $dateTime->format($format) !== $date) {
            return false;
        }

        $startDate = new DateTime('2023-01-01');
        $currentDay = new DateTime();

        if ($dateTime < $startDate || $dateTime > $currentDay) {
            return false;
        }

        return true;
    }
}
