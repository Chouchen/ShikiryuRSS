<?php

namespace Shikiryu\SRSS\Entity;

interface SRSSElement
{
    /**
     * @return bool
     */
    public function isValid(): bool;

    /**
     * @return array
     */
    public function toArray(): array;
}