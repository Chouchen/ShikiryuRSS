<?php

namespace Shikiryu\SRSS\Exception;

class ChannelNotFoundInRSSException extends SRSSException
{
    public function __construct($file)
    {
        parent::__construct(sprintf('Invalid file `%s`: <channel> not found', $file));
    }

}