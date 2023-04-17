<?php

namespace Shikiryu\SRSS\Entity\Item;

use ReflectionException;
use Shikiryu\SRSS\Entity\SRSSElement;
use Shikiryu\SRSS\Validator\HasValidator;
use Shikiryu\SRSS\Validator\Validator;

class Category extends HasValidator implements SRSSElement
{
    /**
     * @validate string
     */
    protected string $domain;
    /**
     * @validate string
     */
    protected string $value;


    public function isValid(): bool
    {
        try {
            return (new Validator())->isObjectValid($this);
        } catch (ReflectionException) {
            return false;
        }
    }

    public function toArray(): array
    {
        return get_object_vars($this);
    }
}