<?php

namespace Shikiryu\SRSS\Entity\Item;

use Shikiryu\SRSS\Entity\SRSSElement;
use Shikiryu\SRSS\Validator\HasValidator;
use Shikiryu\SRSS\Validator\Validator;

class Category extends HasValidator implements SRSSElement
{
    /**
     * @string
     */
    public string $domain;
    /**
     * @string
     */
    public string $value;

    public function isValid(): bool
    {
        try {
            return (new Validator())->isObjectValid($this);
        } catch (\ReflectionException $e) {
            return false;
        }
    }

    public function toArray(): array
    {
        return get_object_vars($this);
    }
}