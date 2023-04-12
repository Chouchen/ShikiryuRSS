<?php

namespace Shikiryu\SRSS;

use DateTime;
use DateTimeInterface;

class SRSSTools
{
    /*public static function check($check, $flag)
    {
        return match ($flag) {
            'nohtml' => self::noHTML($check),
            'link' => self::checkLink($check),
            'html' => self::HTML4XML($check),
            'date' => self::getRSSDate($check),
            'email' => self::checkEmail($check),
            'int' => self::checkInt($check),
            'hour' => self::checkHour($check),
            'day' => self::checkDay($check),
            'folder' => [],
            'media_type' => self::checkMediaType($check),
            'media_medium' => self::checkMediaMedium($check),
            'bool' => self::checkBool($check),
            'medium_expression' => self::checkMediumExpression($check)
        };
    }*/

    /**
     * format the RSS to the wanted format
     *
     * @param $format string wanted format
     * @param $date   string RSS date
     *
     * @return string date
     */
    public static function formatDate(string $format, string $date): string
    {
        return date($format, strtotime($date));
    }

    /**
     * format a date for RSS format
     *
     * @param string $date date to format
     * @param string $format
     *
     * @return string
     */
    public static function getRSSDate(string $date, string $format = ''): string
    {
        $date_position = 'dDjlNSwzWFmMntLoYyaABgGhHisueIOPTZcrU';
        if($format !== '' && preg_match('~^(['.$date_position.']{1})([-/])(['.$date_position.']{1})([-/])(['.$date_position.']{1})$~', $format)){
            $datetime = DateTime::createFromFormat($format, $date);
            if ($datetime === false) {
                return '';
            }

            return $datetime->format(DATE_RSS);
        }

        if (strtotime($date) !==false ) {
            return date("D, d M Y H:i:s T", strtotime($date));
        }

        if (count(explode(' ', $date)) === 2) {
            return DateTime::createFromFormat('Y-m-d H:i:s', $date)->format(DateTimeInterface::RSS);
        }

        [$j, $m, $a] = explode('/', $date);

        return date("D, d M Y H:i:s T", strtotime($a.'-'.$m.'-'.$j));
    }

    /**
     * check if it's an url
     *
     * @param $check string to check
     *
     * @return string|boolean the filtered data, or FALSE if the filter fails.
     */
    public static function checkLink(string $check): bool|string
    {
        return filter_var($check, FILTER_VALIDATE_URL);
    }

    /**
     * make a string XML-compatible
     *
     * @param $check string to format
     *
     * @return string formatted string
     * TODO CDATA ?
     */
    public static function HTML4XML(string $check): string
    {
        return htmlspecialchars($check);
    }

    /**
     * delete html tags
     *
     * @param $check string to format
     *
     * @return string formatted string
     */
    public static function noHTML(string $check): string
    {
        return strip_tags($check);
    }

    /**
     * check if it's a day (in RSS terms)
     *
     * @param $check string to check
     *
     * @return string the day, or empty string
     */
    public static function checkDay(string $check): string
    {
        $possibleDay = ['monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday', 'sunday'];
        return in_array(strtolower($check), $possibleDay) ? $check : '';
    }

    /**
     * check if it's an email
     *
     * @param $check string to check
     *
     * @return string|boolean the filtered data, or FALSE if the filter fails.
     */
    public static function checkEmail(string $check): bool|string
    {
        return filter_var($check, FILTER_VALIDATE_EMAIL);
    }

    /**
     * check if it's an hour (in RSS terms)
     *
     * @param $check string to check
     *
     * @return string|boolean the filtered data, or FALSE if the filter fails.
     */
    public static function checkHour(string $check): bool|string
    {
        $options = [
            'options' => [
                'default' => 0,
                'min_range' => 0,
                'max_range' => 23
            ]
        ];
        return filter_var($check, FILTER_VALIDATE_INT, $options);
    }

    /**
     * check if it's an int
     *
     * @param $check int to check
     *
     * @return int|boolean the filtered data, or FALSE if the filter fails.
     */
    public static function checkInt(int $check): bool|int
    {
        return filter_var($check, FILTER_VALIDATE_INT);
    }

    /**
     * @param $check
     *
     * @return mixed
     */
    public static function checkMediaType($check): mixed
    {
        return $check;
    }

    /**
     * @param $check
     *
     * @return mixed|null
     */
    public static function checkMediaMedium($check): ?string
    {
        return in_array($check, ['image', 'audio', 'video', 'document', 'executable']) ? $check : null;
    }

    /**
     * @param $check
     *
     * @return mixed|null
     */
    public static function checkBool($check): ?string
    {
        return in_array($check, ['true', 'false']) ? $check : null;
    }

    /**
     * @param $check
     *
     * @return mixed|null
     */
    public static function checkMediumExpression($check): ?string
    {
        return in_array($check, ['sample', 'full', 'nonstop']) ? $check : null;
    }
}