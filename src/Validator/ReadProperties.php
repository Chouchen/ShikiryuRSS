<?php

namespace Shikiryu\SRSS\Validator;

use ReflectionClass;
use ReflectionException;
use ReflectionProperty;

trait ReadProperties
{
    /**
     * @param $object
     * @param $property
     * @return ReflectionProperty|null
     * @throws ReflectionException
     */
    private function getReflectedProperty($object, $property): ?ReflectionProperty
    {
        $properties = array_filter(
            $this->_getClassProperties(get_class($object)),
            static fn($p) => $p->getName() === $property
        );

        if (count($properties) !== 1) {
            return null;
        }

        return current($properties);
    }

    /**
     * @return ReflectionProperty[]
     * @throws ReflectionException
     */
    private function _getClassProperties($class): array
    {
        return (new ReflectionClass($class))->getProperties();
    }
}