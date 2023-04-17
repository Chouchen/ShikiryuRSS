<?php

namespace Shikiryu\SRSS\Validator;

use Shikiryu\SRSS\Exception\PropertyNotFoundException;
use Shikiryu\SRSS\Exception\SRSSException;
use Shikiryu\SRSS\SRSSTools;

abstract class HasValidator
{
    /**
     * @var bool[]
     */
    public array $validated = [];

    /**
     * setter of others attributes
     *
     * @param $name
     * @param $val
     *
     * @throws SRSSException
     * @throws \ReflectionException
     */
    public function __set($name, $val)
    {
        if (!property_exists(static::class, $name)) {
            throw new PropertyNotFoundException(static::class, $name);
        }
        if ((new Validator())->isValidValueForObjectProperty($this, $name, $val)) {

            if (SRSSTools::getPropertyType(static::class, $name) === 'array') {
                /** @var array $this->{$name}  */
                $this->{$name}[] = $val;
            } else {
                $val = is_string($val) ? (new Formator())->formatValue($this, $name, $val) : $val;
                $this->{$name} = $val;
            }

        }
    }

    /**
     * @param $name
     *
     * @return bool
     */
    public function __isset($name)
    {
        return isset($this->{$name});
    }

    /**
     * getter of others attributes
     *
     * @param $name
     *
     * @return null|string
     */
    public function __get($name)
    {
        return $this->{$name} ?? null;
    }
}