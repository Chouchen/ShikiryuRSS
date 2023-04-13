<?php

namespace Shikiryu\SRSS\Exception;

class PropertyNotFoundException extends SRSSException
{
    /**
     * @param string $class
     * @param string $name
     */
    public function __construct($class, $name)
    {
        parent::__construct(sprintf('Property `%s` not found in `%s`', $name, $class));
    }
}
