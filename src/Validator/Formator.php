<?php

namespace Shikiryu\SRSS\Validator;

use DateTime;
use DateTimeInterface;
use ReflectionException;
use ReflectionProperty;

class Formator
{
    use ReadProperties;

    /**
     * @param $object
     * @param $property
     * @param $value
     *
     * @return false|string
     */
    public function formatValue($object, $property, $value): bool|string
    {
        try {
            $property = $this->getReflectedProperty($object, $property);
        } catch (ReflectionException) {
            return false;
        }

        $propertyAnnotations = $this->_getPropertyAnnotations($property);

        foreach ($propertyAnnotations as $propertyAnnotation) {
            $value = $this->_formatValue($propertyAnnotation, $value);
        }

        return $value;
    }

    /**
     * @param ReflectionProperty $property
     *
     * @return array
     */
    private function _getPropertyAnnotations(ReflectionProperty $property): array
    {
        preg_match_all('#@format (.*?)\n#s', $property->getDocComment(), $annotations);

        return array_map(static fn($annotation) => trim($annotation), $annotations[1]);
    }

    /**
     * @param string $annotation
     * @param string $value
     *
     * @return void
     */
    private function _formatValue(string $annotation, string $value)
    {
        return match ($annotation) {
            'nohtml'            => self::noHTML($value),
            'url'               => self::checkLink($value),
            'html'              => self::HTML4XML($value),
            'date'              => self::checkDate($value),
            'email'             => self::checkEmail($value),
            'int'               => self::checkInt($value),
            'float'             => self::checkFloat($value),
            'hour'              => self::checkHour($value),
            'day'               => self::checkDay($value),
            'mediaType'         => self::checkMediaType($value),
            'mediaMedium'       => self::checkMediaMedium($value),
            'bool'              => self::checkBool($value),
            'mediaExpression'   => self::checkMediumExpression($value),
        };
    }

    /**
     * format a date for RSS format
     *
     * @param string $date date to format
     * @param string $format
     *
     * @return string
     */
    public static function checkDate(string $date, string $format = ''): string
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
     */
    public static function HTML4XML(string $check): string
    {
        if (str_starts_with($check, '<![CDATA[ ') && str_ends_with($check, ' ]]>')) {
            return $check;
        }

        return sprintf('<![CDATA[ %s ]]>', $check);
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
     * check if it's a float
     *
     * @param float $check to check
     *
     * @return float|boolean the filtered data, or FALSE if the filter fails.
     */
    public static function checkFloat(float $check): bool|float
    {
        return filter_var($check, FILTER_VALIDATE_FLOAT);
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
