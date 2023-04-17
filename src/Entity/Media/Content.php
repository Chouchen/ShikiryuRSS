<?php

namespace Shikiryu\SRSS\Entity\Media;

use ReflectionException;
use Shikiryu\SRSS\Entity\SRSSElement;
use Shikiryu\SRSS\Validator\HasValidator;
use Shikiryu\SRSS\Validator\Validator;

class Content extends HasValidator implements SRSSElement
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
    protected ?int $filesize = null;
    /**
     * @validate mediaType
     */
    protected ?string $type = null;
    /**
     * @validate mediaMedium
     * @format mediaMedium
     */
    protected ?string $medium = null;
    /**
     * @validate bool
     * @format bool
     */
    protected ?bool $isDefault = null;
    /**
     * @validate mediaExpression
     * @format mediaExpression
     */
    protected ?string $expression = null;
    /**
     * @validate int
     * @format int
     */
    protected ?int $bitrate = null;
    /**
     * @validate int
     * @format int
     */
    protected ?int $framerate = null;
    /**
     * @validate float
     * @format float
     */
    protected ?float $samplerate = null;
    /**
     * @validate int
     * @format int
     */
    protected ?int $channels = null;
    /**
     * @validate int
     * @format int
     */
    protected ?int $duration = null;
    /**
     * @validate int
     * @format int
     */
    protected ?int $height = null;
    /**
     * @validate int
     * @format int
     */
    protected ?int $width = null;
    /**
     * @validate lang
     */
    protected ?string $lang = null;

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