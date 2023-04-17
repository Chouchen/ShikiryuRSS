<?php

namespace Shikiryu\SRSS\Entity\Channel;

use ReflectionException;
use Shikiryu\SRSS\Entity\SRSSElement;
use Shikiryu\SRSS\Validator\HasValidator;
use Shikiryu\SRSS\Validator\Validator;

class Category extends HasValidator implements SRSSElement
{
    /**
     * @validate url
     * @format url
     */
    protected ?string $domain = null;
    /**
     * @validate string
     */
    protected ?string $value = null;

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