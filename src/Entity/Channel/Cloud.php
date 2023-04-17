<?php

namespace Shikiryu\SRSS\Entity\Channel;

use ReflectionException;
use Shikiryu\SRSS\Entity\SRSSElement;
use Shikiryu\SRSS\Validator\HasValidator;
use Shikiryu\SRSS\Validator\Validator;

class Cloud extends HasValidator implements SRSSElement
{
    /**
     * @validate string
     */
    protected ?string $domain = null;
    /**
     * @validate int
     * @format int
     */
    protected ?int $port = null;
    /**
     * @validate string
     */
    protected ?string $path = null;
    /**
     * @validate string
     */
    protected ?string $registerProcedure = null;
    /**
     * @validate string
     */
    protected ?string $protocol = null;

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