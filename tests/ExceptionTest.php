<?php

use PHPUnit\Framework\TestCase;
use Shikiryu\SRSS\Exception\ChannelNotFoundInRSSException;
use Shikiryu\SRSS\Exception\PropertyNotFoundException;
use Shikiryu\SRSS\Exception\UnreadableRSSException;
use Shikiryu\SRSS\SRSS;

class ExceptionTest extends TestCase
{
    public function testPropertyNotFound(): void
    {
        $srss = new SRSS();
        $this->expectException(PropertyNotFoundException::class);
        $srss->notfound = 'true';
    }

    public function testRssNotFound(): void
    {
        $this->expectException(UnreadableRSSException::class);
        SRSS::read('not_found.xml');
    }

    public function testMissingChannel(): void
    {
        $this->expectException(ChannelNotFoundInRSSException::class);
        SRSS::read(__DIR__ . '/resources/invalid-no-channel.xml');
    }
}