<?php

namespace Shikiryu\SRSS;

use ReflectionProperty;
use Shikiryu\SRSS\Validator\Formator;

class SRSSTools
{
    /**
     * @throws \ReflectionException
     */
    public static function getPropertyType($object, $property): ?string
    {
        $rp = new ReflectionProperty($object, $property);
        return $rp->getType()?->getName();
    }

    /**
     * @param string $string
     *
     * @return string
     */
    public static function getRSSDate(string $string)
    {
        return Formator::checkDate($string);
    }
}
