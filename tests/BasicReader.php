<?php

use PHPUnit\Framework\TestCase;
use Shikiryu\SRSS\SRSS;
use Shikiryu\SRSS\SRSSException;

class BasicReader extends TestCase
{
    public function testReadBasicRSS()
    {
        $rss = SRSS::read(__DIR__.'/resources/basic.xml');
        self::assertEquals('test Home Page', $rss->title);
        $first_item = $rss->getFirst();
        self::assertEquals('RSS Tutorial', $first_item->title);
    }

    public function testRssNotFound()
    {
        $this->expectException(SRSSException::class);
        $rss = SRSS::read('not_found.xml');
    }
}