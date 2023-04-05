<?php

namespace Shikiryu\SRSS;

class SRSSTools
{
    public static function check($check, $flag)
    {
        switch($flag){
            case 'nohtml': return self::noHTML($check);
            case 'link': return self::checkLink($check);
            case 'html': return self::HTML4XML($check);
            /*case 'lang':
                return self::noHTML($check);
            */
            case 'date': return self::getRSSDate($check);
            case 'email': return self::checkEmail($check);
            case 'int': return self::checkInt($check);
            case 'hour': return self::checkHour($check);
            case 'day': return self::checkDay($check);
            case 'folder': return [];
            case 'media_type': return self::checkMediaType($check);
            case 'media_medium': return self::checkMediaMedium($check);
            case 'bool': return self::checkBool($check);
            case 'medium_expression': return self::checkMediumExpression($check);
            case '': return $check;
            default: throw new SRSSException('flag '.$flag.' does not exist.');
        }
    }

    /**
     * format the RSS to the wanted format
     * @param $format string wanted format
     * @param $date string RSS date
     * @return string date
     */
    public static function formatDate($format, $date)
    {
        return date($format, strtotime($date));
    }

    /**
     * format a date for RSS format
     * @param string $date date to format
     * @param string $format
     * @return string
     */
    public static function getRSSDate($date, $format='')
    {
        $datepos = 'dDjlNSwzWFmMntLoYyaABgGhHisueIOPTZcrU';
        if($format != '' && preg_match('~^(['.$datepos.']{1})(-|/)(['.$datepos.']{1})(-|/)(['.$datepos.']{1})$~', $format, $match)){
            $sep = $match[2];
            $format = '%'.$match[1].$sep.'%'.$match[3].$sep.'%'.$match[5];
            if($dateArray = strptime($date, $format)){
                $mois = (int)$dateArray['tm_mon'] + 1;
                $annee = strlen($dateArray['tm_year']) > 2 ? '20'.substr($dateArray['tm_year'], -2) : '19'.$dateArray['tm_year'];
                $date = $annee.'-'.$mois.'-'.$dateArray['tm_mday'];
                return date("D, d M Y H:i:s T", strtotime($date));
            }
            return '';
        }

        if(strtotime($date) !==false ){
            return date("D, d M Y H:i:s T", strtotime($date));
        }

        [$j, $m, $a] = explode('/', $date);

        return date("D, d M Y H:i:s T", strtotime($a.'-'.$m.'-'.$j));
    }

    /**
     * check if it's an url
     * @param $check string to check
     * @return string|boolean the filtered data, or FALSE if the filter fails.
     */
    public static function checkLink($check)
    {
        return filter_var($check, FILTER_VALIDATE_URL);
    }

    /**
     * make a string XML-compatible
     * @param $check string to format
     * @return string formatted string
     * TODO CDATA ?
     */
    public static function HTML4XML($check)
    {
        return htmlspecialchars($check);
    }

    /**
     * delete html tags
     * @param $check string to format
     * @return string formatted string
     */
    public static function noHTML($check)
    {
        return strip_tags($check);
    }

    /**
     * check if it's a day (in RSS terms)
     * @param $check string to check
     * @return string the day, or empty string
     */
    public static function checkDay($check)
    {
        $possibleDay = ['monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday', 'sunday'];
        return in_array(strtolower($check), $possibleDay) ? $check : '';
    }

    /**
     * check if it's an email
     * @param $check string to check
     * @return string|boolean the filtered data, or FALSE if the filter fails.
     */
    public static function checkEmail($check)
    {
        return filter_var($check, FILTER_VALIDATE_EMAIL);
    }

    /**
     * check if it's an hour (in RSS terms)
     * @param $check string to check
     * @return string|boolean the filtered data, or FALSE if the filter fails.
     */
    public static function checkHour($check)
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
     * @param $check int to check
     * @return int|boolean the filtered data, or FALSE if the filter fails.
     */
    public static function checkInt($check)
    {
        return filter_var($check, FILTER_VALIDATE_INT);
    }

    /**
     * @param $check
     *
     * @return mixed
     */
    private static function checkMediaType($check)
    {
        return $check;
    }

    /**
     * @param $check
     *
     * @return mixed|null
     */
    private static function checkMediaMedium($check)
    {
        return in_array($check, ['image', 'audio', 'video', 'document', 'executable']) ? $check : null;
    }

    /**
     * @param $check
     *
     * @return mixed|null
     */
    private static function checkBool($check)
    {
        return in_array($check, ['true', 'false']) ? $check : null;
    }

    /**
     * @param $check
     *
     * @return mixed|null
     */
    private static function checkMediumExpression($check)
    {
        return in_array($check, ['sample', 'full', 'nonstop']) ? $check : null;
    }
}