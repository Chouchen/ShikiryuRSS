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
        self::assertNotNull($first_item);
        self::assertEquals('RSS Tutorial', $first_item->title);
    }

    public function testRssNotFound()
    {
        $this->expectException(SRSSException::class);
        $rss = SRSS::read('not_found.xml');
    }

    public function testSpecificationExampleRSS()
    {
        $rss = SRSS::read(__DIR__.'/resources/harvard.xml');
        self::assertEquals('Liftoff News', $rss->title);
        self::assertEquals('http://liftoff.msfc.nasa.gov/', $rss->link);
        self::assertEquals('Liftoff to Space Exploration.', $rss->description);
        self::assertEquals('en-us', $rss->language);
        self::assertEquals('Tue, 10 Jun 2003 04:00:00 GMT', $rss->pubDate);
        self::assertEquals('Tue, 10 Jun 2003 09:41:01 GMT', $rss->lastBuildDate);
        self::assertEquals('http://blogs.law.harvard.edu/tech/rss', $rss->docs);
        self::assertEquals('Weblog Editor 2.0', $rss->generator);
        self::assertEquals('editor@example.com', $rss->managingEditor);
        self::assertEquals('webmaster@example.com', $rss->webMaster);
        self::assertCount(4, $rss->items);
        self::assertEquals('Star City', $rss->getFirst()->title);
        self::assertEquals('http://liftoff.msfc.nasa.gov/news/2003/news-laundry.asp', $rss->getLast()->link);
        self::assertEquals('Fri, 30 May 2003 11:06:42 GMT', $rss->getItem(2)->pubDate);
    }
}