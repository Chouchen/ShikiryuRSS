<?php

use PHPUnit\Framework\TestCase;
use Shikiryu\SRSS\Entity\Channel\Category;
use Shikiryu\SRSS\Entity\Channel\Cloud;
use Shikiryu\SRSS\Entity\Channel\Image;
use Shikiryu\SRSS\Entity\Item\Enclosure;
use Shikiryu\SRSS\Entity\Item\Source;
use Shikiryu\SRSS\Exception\InvalidPropertyException;
use Shikiryu\SRSS\SRSS;

class FailingTest extends TestCase
{
    public function testInvalidChannelMandatory(): void
    {
        $rss = SRSS::create();
        self::assertFalse($rss->isValid(), var_export($rss->validated, true));
        $rss->title = 'title'; // mandatory
        self::assertFalse($rss->isValid(), var_export($rss->validated, true));
        $rss->description = 'desc'; // mandatory
        self::assertFalse($rss->isValid(), var_export($rss->validated, true));
        $rss->link = 'https://example.org';
        self::assertTrue($rss->isValid(), var_export($rss->validated, true));
    }

    public function testInvalidChannelLink(): void
    {
        $this->expectException(InvalidPropertyException::class);
        $rss = SRSS::create();
        $rss->title = 'title'; // mandatory
        $rss->description = 'desc'; // mandatory
        $rss->link = 'desc'; // mandatory but should be an url
    }

    public function testInvalidChannelLanguage(): void
    {
        $rss = SRSS::create();
        $this->expectException(InvalidPropertyException::class);
        $rss->language = 'en-en'; // should be a valid language
    }

    public function testInvalidChannelCopyright(): void
    {
        $rss = SRSS::create();
        $this->expectException(InvalidPropertyException::class);
        $rss->copyright = '<strong>test</strong>'; // should not have html element
    }

    public function testInvalidChannelManagingEditor(): void
    {
        $rss = SRSS::create();
        $this->expectException(InvalidPropertyException::class);
        $rss->managingEditor = '<strong>test</strong>'; // should not have html element
    }

    public function testInvalidChannelWebmaster(): void
    {
        $rss = SRSS::create();
        $this->expectException(InvalidPropertyException::class);
        $rss->webMaster = '<strong>test</strong>'; // should not have html element
    }

    public function testInvalidChannelPubDate(): void
    {
        $rss = SRSS::create();
        $this->expectException(InvalidPropertyException::class);
        $rss->pubDate = 'test'; // should be a valid date
    }

    public function testInvalidChannelLastBuildDate(): void
    {
        $rss = SRSS::create();
        $this->expectException(InvalidPropertyException::class);
        $rss->lastBuildDate = 'test'; // should be a valid date
    }

    public function testInvalidChannelGenerator(): void
    {
        $rss = SRSS::create();
        $this->expectException(InvalidPropertyException::class);
        $rss->generator = '<strong>test</strong>'; // should not have html element
    }

    public function testInvalidChannelDocs(): void
    {
        $rss = SRSS::create();
        $this->expectException(InvalidPropertyException::class);
        $rss->docs = 'desc'; //should be a url
    }

    public function testInvalidChannelTTL(): void
    {
        $rss = SRSS::create();
        $this->expectException(InvalidPropertyException::class);
        $rss->ttl = 'desc'; // should be an int
    }

    public function testInvalidChannelSkipHours(): void
    {
        $rss = SRSS::create();
        $this->expectException(InvalidPropertyException::class);
        $rss->skipHours = 'desc'; // should be an hour
    }

    public function testInvalidChannelSkipDays(): void
    {
        $rss = SRSS::create();
        $this->expectException(InvalidPropertyException::class);
        $rss->skipDays = 'desc'; // should be a day
    }

    public function testInvalidItemAuthor(): void
    {
        $item = new Shikiryu\SRSS\Entity\Item();
        $this->expectException(InvalidPropertyException::class);
        $item->author = 'test'; // should be an email
    }

    public function testInvalidItemComments(): void
    {
        $item = new Shikiryu\SRSS\Entity\Item();
        $this->expectException(InvalidPropertyException::class);
        $item->comments = 'test'; // should be an url
    }

    public function testInvalidItemMandatory(): void
    {
        $item = new Shikiryu\SRSS\Entity\Item();
        $item->title = 'title';
        self::assertTrue($item->isValid(), var_export($item->validated, true));
        $item->description = 'desc';
        $item->title = null;
        self::assertTrue($item->isValid(), var_export($item->validated, true));

        $this->expectException(InvalidPropertyException::class);
        $item->title = null;
        $item->description = null;
    }

    public function testChannelCategoryDomain(): void
    {
        $category = new Category();
        $this->expectException(InvalidPropertyException::class);
        $category->domain = 'test';
    }

    public function testChannelCloudPort(): void
    {
        $cloud = new Cloud();
        $this->expectException(InvalidPropertyException::class);
        $cloud->port = 'test';
    }

    public function testChannelImageUrl(): void
    {
        $image = new Image();
        $this->expectException(InvalidPropertyException::class);
        $image->url = 'test';
    }

    public function testChannelImageTitle(): void
    {
        $image = new Image();
        $this->expectException(InvalidPropertyException::class);
        $image->title = '<strong>test</strong>';
    }

    public function testChannelImageLink(): void
    {
        $image = new Image();
        $this->expectException(InvalidPropertyException::class);
        $image->link = 'test';
    }

    public function testChannelImageWidthType(): void
    {
        $image = new Image();
        $this->expectException(InvalidPropertyException::class);
        $image->width = 'test';
    }

    public function testChannelImageWidthMax(): void
    {
        $image = new Image();
        $this->expectException(InvalidPropertyException::class);
        $image->width = '150';
    }

    public function testChannelImageHeightType(): void
    {
        $image = new Image();
        $this->expectException(InvalidPropertyException::class);
        $image->height = 'test';
    }

    public function testChannelImageHeightMax(): void
    {
        $image = new Image();
        $this->expectException(InvalidPropertyException::class);
        $image->height = '500';
    }

    public function testItemEnclosureUrl(): void
    {
        $enclosure = new Enclosure();
        $this->expectException(InvalidPropertyException::class);
        $enclosure->url = 'test';
    }

    public function testItemEnclosureLength(): void
    {
        $enclosure = new Enclosure();
        $this->expectException(InvalidPropertyException::class);
        $enclosure->length = 'test';
    }

    public function testItemSourceUrl()
    {
        $source = new Source();
        $this->expectException(InvalidPropertyException::class);
        $source->url = 'test';
    }

    public function testItemSourceValue()
    {
        $source = new Source();
        $this->expectException(InvalidPropertyException::class);
        $source->value = '<strong>test</strong>';
    }
}
