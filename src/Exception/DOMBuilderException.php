<?php

namespace Shikiryu\SRSS\Exception;

class DOMBuilderException extends SRSSException
{
    public function __construct(\DOMException $e)
    {
        parent::__construct(sprintf('Error while building DOM (%s)', $e->getMessage()));
    }

}