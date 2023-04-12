<?php

namespace Shikiryu\SRSS\Entity\Item;

use Shikiryu\SRSS\Entity\SRSSElement;
use Shikiryu\SRSS\Validator\HasValidator;
use Shikiryu\SRSS\Validator\Validator;

class Enclosure extends HasValidator implements SRSSElement
{
    /**
     * @url
     */
    public ?string $url = null;

    /**
     * @int
     */
    public ?int $length = null;

    /**
     * @mediaType
     */
    public ?string $type = null;

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