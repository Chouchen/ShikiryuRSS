<?php

namespace Shikiryu\SRSS\Entity\Media;

use Shikiryu\SRSS\Entity\SRSSElement;
use Shikiryu\SRSS\Validator\HasValidator;
use Shikiryu\SRSS\Validator\Validator;

class Content extends HasValidator implements SRSSElement
{
    /**
     * @url
     */
    public ?string $url = null;
    /**
     * @int
     */
    public ?int $filesize = null;
    /**
     * @mediaType
     */
    public ?string $type = null;
    /**
     * @mediaMedium
     */
    public ?string $medium = null;
    /**
     * @bool
     */
    public ?bool $isDefault = null;
    /**
     * @mediaExpression
     */
    public ?string $expression = null;
    /**
     * @int
     */
    public ?int $bitrate = null;
    /**
     * @int
     */
    public ?int $framerate = null;
    /**
     * @float
     */
    public ?float $samplerate = null;
    /**
     * @int
     */
    public ?int $channels = null;
    /**
     * @int
     */
    public ?int $duration = null;
    /**
     * @int
     */
    public ?int $height = null;
    /**
     * @int
     */
    public ?int $width = null;
    /**
     * @lang
     */
    public ?string $lang = null;

    public function isValid(): bool
    {
        return (new Validator())->isObjectValid($this);
    }

    public function toArray(): array
    {
        return get_object_vars($this);
    }
}