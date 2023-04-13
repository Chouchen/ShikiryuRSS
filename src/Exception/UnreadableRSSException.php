<?php

namespace Shikiryu\SRSS\Exception;

class UnreadableRSSException extends SRSSException
{
    public function __construct($file)
    {
        parent::__construct(sprintf('File `%s` is unreadable.', $file));
    }

}