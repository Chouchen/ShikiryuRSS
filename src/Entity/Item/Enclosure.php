<?php

namespace Shikiryu\SRSS\Entity\Item;

use ReflectionException;
use Shikiryu\SRSS\Entity\SRSSElement;
use Shikiryu\SRSS\Validator\HasValidator;
use Shikiryu\SRSS\Validator\Validator;

class Enclosure extends HasValidator implements SRSSElement
{
    /**
     * @validate url
     * @format url
     */
    protected ?string $url = null;

    /**
     * @validate int
     * @format int
     */
    protected ?int $length = null;

    /**
     * @validate mediaType
     * @format mediaType
     */
    protected ?string $type = null;

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