<?php

namespace Shikiryu\SRSS\Exception;

use Exception;

class SRSSException extends Exception
{
    public function __construct($msg)
    {
        parent :: __construct($msg);
    }

    /**
     * @return string
     */
    public function getError(): string
    {
        return 'Une exception a été générée : <strong>Message : ' . $this->getMessage() . '</strong> à la ligne : ' . $this->getLine();
    }
}