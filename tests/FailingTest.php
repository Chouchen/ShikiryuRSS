<?php

use PHPUnit\Framework\TestCase;
use Shikiryu\SRSS\SRSS;

class FailingTest extends TestCase
{
    private ?SRSS $srss;

    public function testInvalidChannel()
    {
        $this->srss = SRSS::create();
        self::assertFalse($this->srss->isValid(), var_export($this->srss->validated, true));
        $this->srss->title = 'title'; // mandatory
        self::assertFalse($this->srss->isValid(), var_export($this->srss->validated, true));
        $this->srss->description = 'desc'; // mandatory
        self::assertFalse($this->srss->isValid(), var_export($this->srss->validated, true));

        $this->srss->link = 'desc'; // mandatory but should be a url
        self::assertFalse($this->srss->isValid(), var_export($this->srss->validated, true));
        $this->srss->link = 'https://example.org';
        self::assertTrue($this->srss->isValid(), var_export($this->srss->validated, true));

        $this->srss->language = 'en-en'; // should be a valid language
        self::assertFalse($this->srss->isValid(), var_export($this->srss->validated, true));
        $this->srss->language = 'en-us'; // should be a valid
        self::assertTrue($this->srss->isValid(), var_export($this->srss->validated, true));

        $this->srss->copyright = '<strong>test</strong>'; // should not have html element
        self::assertFalse($this->srss->isValid(), var_export($this->srss->validated, true));
        $this->srss->copyright = 'shikiryu';
        self::assertTrue($this->srss->isValid(), var_export($this->srss->validated, true));

        $this->srss->managingEditor = '<strong>test</strong>'; // should not have html element
        self::assertFalse($this->srss->isValid(), var_export($this->srss->validated, true));
        $this->srss->managingEditor = 'shikiryu';
        self::assertTrue($this->srss->isValid(), var_export($this->srss->validated, true));

        $this->srss->webMaster = '<strong>test</strong>'; // should not have html element
        self::assertFalse($this->srss->isValid(), var_export($this->srss->validated, true));
        $this->srss->webMaster = 'shikiryu';
        self::assertTrue($this->srss->isValid(), var_export($this->srss->validated, true));

        $this->srss->pubDate = 'test'; // should be a valid date
        self::assertFalse($this->srss->isValid(), var_export($this->srss->validated, true));
        $this->srss->pubDate = (new DateTime())->format(DATE_RSS);
        self::assertTrue($this->srss->isValid(), var_export($this->srss->validated, true));

        $this->srss->lastBuildDate = 'test'; // should be a valid date
        self::assertFalse($this->srss->isValid(), var_export($this->srss->validated, true));
        $this->srss->lastBuildDate = (new DateTime())->format(DATE_RSS);
        self::assertTrue($this->srss->isValid(), var_export($this->srss->validated, true));

        $this->srss->generator = '<strong>test</strong>'; // should not have html element
        self::assertFalse($this->srss->isValid(), var_export($this->srss->validated, true));
        $this->srss->generator = 'shikiryuRSS';
        self::assertTrue($this->srss->isValid(), var_export($this->srss->validated, true));

        $this->srss->docs = 'desc'; //should be a url
        self::assertFalse($this->srss->isValid(), var_export($this->srss->validated, true));
        $this->srss->docs = 'https://example.org';
        self::assertTrue($this->srss->isValid(), var_export($this->srss->validated, true));

        $this->srss->ttl = 'desc'; // should be an int
        self::assertFalse($this->srss->isValid(), var_export($this->srss->validated, true));
        $this->srss->ttl = '85';
        self::assertTrue($this->srss->isValid(), var_export($this->srss->validated, true));

        // rating and textInput not tested because there's no validation

        $this->srss->skipHours = 'desc'; // should be an hour
        self::assertFalse($this->srss->isValid(), var_export($this->srss->validated, true));
        $this->srss->skipHours = '12';
        self::assertTrue($this->srss->isValid(), var_export($this->srss->validated, true));

        $this->srss->skipDays = 'desc'; // should be a day
        self::assertFalse($this->srss->isValid(), var_export($this->srss->validated, true));
        $this->srss->skipDays = 'monday';
        self::assertTrue($this->srss->isValid(), var_export($this->srss->validated, true));
    }

    public function testItem()
    {
        $item = new Shikiryu\SRSS\Entity\Item();

        self::assertFalse($item->isValid(), var_export($item->validated, true));
        $item->title = 'title';
        self::assertTrue($item->isValid(), var_export($item->validated, true));

        $item->link = 'test';
        self::assertFalse($item->isValid(), var_export($item->validated, true));
        $item->link = 'https://example.org/link1';
        self::assertTrue($item->isValid(), var_export($item->validated, true));

        $item->title = null;
        self::assertFalse($item->isValid(), var_export($item->validated, true));
        $item->description = 'desc';
        self::assertTrue($item->isValid(), var_export($item->validated, true));
        $item->title = 'title';
        self::assertTrue($item->isValid(), var_export($item->validated, true));

        $item->author = 'test';
        self::assertFalse($item->isValid(), var_export($item->validated, true));
        $item->author = 'email@example.org';
        self::assertTrue($item->isValid(), var_export($item->validated, true));

        $item->comments = 'test';
        self::assertFalse($item->isValid(), var_export($item->validated, true));
        $item->comments = 'https://example.org/link1';
        self::assertTrue($item->isValid(), var_export($item->validated, true));

        // guid is not validated and, so, not tested
    }
}