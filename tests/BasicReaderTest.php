<?php

use PHPUnit\Framework\TestCase;
use Shikiryu\SRSS\Entity\Channel\Category;
use Shikiryu\SRSS\Entity\Channel\Cloud;
use Shikiryu\SRSS\Entity\Channel\Image;
use Shikiryu\SRSS\Entity\Item;
use Shikiryu\SRSS\SRSS;

class BasicReaderTest extends TestCase
{
    public function testReadBasicRSS(): void
    {
        $rss = SRSS::read(__DIR__.'/resources/basic.xml');
        self::assertEquals('<![CDATA[ test Home Page ]]>', $rss->title);
        $first_item = $rss->getFirst();
        self::assertNotNull($first_item);
        self::assertEquals('<![CDATA[ RSS Tutorial ]]>', $first_item->title);

        self::assertTrue($rss->isValid());
    }

    public function testSpecificationExampleRSS(): void
    {
        $rss = SRSS::read(__DIR__.'/resources/harvard.xml');
        self::assertEquals('<![CDATA[ Liftoff News ]]>', $rss->title);
        self::assertEquals('http://liftoff.msfc.nasa.gov/', $rss->link);
        self::assertEquals('<![CDATA[ Liftoff to Space Exploration. ]]>', $rss->description);
        self::assertEquals('en-us', $rss->language);
        self::assertEquals('Tue, 10 Jun 2003 04:00:00 UTC', $rss->pubDate);
        self::assertEquals('Tue, 10 Jun 2003 09:41:01 UTC', $rss->lastBuildDate);
        self::assertEquals('http://blogs.law.harvard.edu/tech/rss', $rss->docs);
        self::assertEquals('Weblog Editor 2.0', $rss->generator);
        self::assertEquals('editor@example.com', $rss->managingEditor);
        self::assertEquals('webmaster@example.com', $rss->webMaster);
        self::assertCount(4, $rss->items);
        self::assertEquals('<![CDATA[ Star City ]]>', $rss->getFirst()->title);
        self::assertEquals('http://liftoff.msfc.nasa.gov/news/2003/news-laundry.asp', $rss->getLast()->link);
        self::assertEquals('Fri, 30 May 2003 11:06:42 UTC', $rss->getItem(2)->pubDate);

        self::assertTrue($rss->isValid());
    }

    public function testChannelImage(): void
    {
        $rss = SRSS::read(__DIR__.'/resources/media/cnn.xml');
        $image = $rss->image;
        self::assertInstanceOf(Image::class, $image);
        self::assertEquals('http://i2.cdn.turner.com/cnn/2015/images/09/24/cnn.digital.png', $image->url, var_export($image, true));
        self::assertEquals('CNN.com - RSS Channel - Entertainment', $image->title, var_export($image, true));
        self::assertEquals('https://www.cnn.com/entertainment/index.html', $image->link, var_export($image, true));
    }

    public function testChannelCategory(): void
    {
        $rss = SRSS::read(__DIR__.'/resources/cloud.xml');
        $categories = $rss->category;
        self::assertCount(1, $categories);
        $category = $categories[0];
        self::assertInstanceOf(Category::class, $category);
        self::assertEquals('http://www.weblogs.com/rssUpdates/changes.xml', $category->domain, var_export($category, true));
        self::assertEquals('rssUpdates', $category->value, var_export($category, true));
    }

    public function testCloud(): void
    {
        $rss = SRSS::read(__DIR__.'/resources/cloud.xml');
        $cloud = $rss->cloud;
        self::assertInstanceOf(Cloud::class, $cloud);
        self::assertEquals('radio.xmlstoragesystem.com', $cloud->domain, var_export($cloud, true));
        self::assertEquals('80', $cloud->port, var_export($cloud, true));
        self::assertEquals('/RPC2', $cloud->path, var_export($cloud, true));
        self::assertEquals('xmlStorageSystem.rssPleaseNotify', $cloud->registerProcedure, var_export($cloud, true));
        self::assertEquals('xml-rpc', $cloud->protocol, var_export($cloud, true));
    }

    public function testSource(): void
    {
        $rss = SRSS::read(__DIR__.'/resources/basic.xml');
        $firstItem = $rss->getFirst();
        self::assertInstanceOf(Item::class, $firstItem);
        $source = $firstItem->source;
        self::assertInstanceOf(Item\Source::class, $source);
        self::assertEquals('http://www.tomalak.org/links2.xml', $source->url);
        self::assertEquals('Tomalak\'s Realm', $source->value);
    }

    public function testEnclosure(): void
    {
        $rss = SRSS::read(__DIR__.'/resources/basic.xml');
        $item = $rss->getItem(2);
        self::assertInstanceOf(Item::class, $item);
        $enclosure = $item->enclosure;
        self::assertInstanceOf(Item\Enclosure::class, $enclosure);
        self::assertEquals('http://www.scripting.com/mp3s/touchOfGrey.mp3', $enclosure->url);
        self::assertEquals('5588242', $enclosure->length);
        self::assertEquals('audio/mpeg', $enclosure->type);
    }
}