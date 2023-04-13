<?php

namespace Shikiryu\SRSS\Entity\Channel;

use ReflectionException;
use Shikiryu\SRSS\Entity\SRSSElement;
use Shikiryu\SRSS\Validator\HasValidator;
use Shikiryu\SRSS\Validator\Validator;

class Cloud extends HasValidator implements SRSSElement
{
    /**
     * @string
     */
    public ?string $domain = null;
    /**
     * @int
     */
    public ?int $port = null;
    /**
     * @string
     */
    public ?string $path = null;
    /**
     * @string
     */
    public ?string $registerProcedure = null;
    /**
     * @string
     */
    public ?string $protocol = null;

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