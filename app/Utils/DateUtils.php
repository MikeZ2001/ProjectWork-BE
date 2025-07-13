<?php

namespace App\Utils;

use DateTimeInterface;

class DateUtils
{
    /**
     * Define standard format for datetime strings.
     */
    public const string DATETIME_FORMAT = 'Y-m-d H:i:s';

    /**
     * Define standard format for date strings.
     */
    public const string DATE_FORMAT = 'Y-m-d';

    /**
     * Define standard format for time strings.
     */
    public const string TIME_FORMAT = 'H:i:s';

    /**
     * Convert a given datetime object into a string representation.
     *
     * @param DateTimeInterface $dateTime
     *
     * @return string
     */
    public static function toDateTimeString(DateTimeInterface $dateTime): string
    {
        return $dateTime->format(static::DATETIME_FORMAT);
    }

    /**
     * Convert a given datetime object into a string representation containing the date component only.
     *
     * @param DateTimeInterface $dateTime
     *
     * @return string
     */
    public static function toDateString(DateTimeInterface $dateTime): string
    {
        return $dateTime->format(static::DATE_FORMAT);
    }

    /**
     * Convert a given datetime object into a string representation containing the time component only.
     *
     * @param DateTimeInterface $dateTime
     *
     * @return string
     */
    public static function toTimeString(DateTimeInterface $dateTime): string
    {
        return $dateTime->format(static::TIME_FORMAT);
    }
}
