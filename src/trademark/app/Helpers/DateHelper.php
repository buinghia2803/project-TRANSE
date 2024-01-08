<?php

namespace App\Helpers;

use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

/**
 * Common Helper.
 *
 * @author
 */
class DateHelper
{
    /**
     * Convert date string to timestamp.
     *
     * @param string $dateTimeString
     * @param string $format
     * @param string $timezone
     * @return string|null
     */
    public static function convertDateToTimestamp(
        string $dateTimeString,
        string $format = 'Y/m/d H:i',
        string $timezone = 'Asia/Tokyo'
    ): ?string
    {
        try {
            return Carbon::createFromFormat($format, $dateTimeString)->setTimezone($timezone)->timestamp;
        } catch (\Exception $e) {
            Log::error('[DateHelper->convertDateToTimestamp:' . __LINE__ . '] ' . $e->getMessage());
            return null;
        }
    }

    /**
     * Convert timestamp to datetime.
     *
     * @param   integer $timestamp
     * @param   string  $format
     * @param   string  $timezone
     * @return  string|null
     */
    public static function convertTimestampToDate(
        int $timestamp,
        string $format = 'Y-m-d H:i',
        string $timezone = 'Asia/Tokyo'
    ): ?string
    {
        try {
            return Carbon::createFromTimestamp($timestamp, $timezone)->format($format);
        } catch (\Exception $e) {
            Log::error('[DateHelper->convertTimestampToDate:' . __LINE__ . '] ' . $e->getMessage());
            return null;
        }
    }

    /**
     * Get current date time with timezone and format.
     *
     * @param   string $format
     * @param   string $timezone
     * @return  string
     */
    public static function getNow(string $format = 'Y-m-d H:i', string $timezone = 'Asia/Tokyo'): string
    {
        if ($format == 'timestamp') {
            return Carbon::now()->timezone($timezone)->timestamp;
        } elseif (!empty($format)) {
            return Carbon::now()->timezone($timezone)->format($format);
        } else {
            return Carbon::now()->timezone($timezone);
        }
    }
}
