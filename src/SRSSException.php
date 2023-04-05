<?php

namespace Shikiryu\SRSS;

use Exception;

class SRSSException extends Exception
{
    public function __construct($msg)
    {
        parent :: __construct($msg);
    }

    public function getError()
    {
        return 'Une exception a été générée : <strong>Message : ' . $this->getMessage() . '</strong> à la ligne : ' . $this->getLine();
    }
}