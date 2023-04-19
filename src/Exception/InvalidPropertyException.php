<?php

namespace Shikiryu\SRSS\Exception;

class InvalidPropertyException extends SRSSException
{

    public function __construct(string $object, string $property, ?string $value)
    {
        parent::__construct(sprintf('Invalid property `%s` = `%s` in `%s`', $property, $value, $object));
    }
}