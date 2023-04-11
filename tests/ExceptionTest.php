<?php

use PHPUnit\Framework\TestCase;
use Shikiryu\SRSS\Exception\ChannelNotFoundInRSSException;
use Shikiryu\SRSS\Exception\PropertyNotFoundException;
use Shikiryu\SRSS\Exception\UnreadableRSSException;
use Shikiryu\SRSS\SRSS;

class ExceptionTest extends TestCase
{
    public function testPropertyNotFound()
    {
        $srss = new SRSS();
        $this->expectException(PropertyNotFoundException::class);
        $srss->notfound = 'true';
    }

    public function testRssNotFound()
    {
        $this->expectException(UnreadableRSSException::class);
        $rss = SRSS::read('not_found.xml');
    }

    public function testMissingChannel()
    {
        $this->expectException(ChannelNotFoundInRSSException::class);
        $rss = SRSS::read(__DIR__ . '/resources/invalid-no-channel.xml');
    }
}