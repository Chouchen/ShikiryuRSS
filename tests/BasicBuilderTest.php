<?php

use PHPUnit\Framework\TestCase;
use Shikiryu\SRSS\Builder\SRSSBuilder;
use Shikiryu\SRSS\Entity\Item;
use Shikiryu\SRSS\SRSS;
use Shikiryu\SRSS\SRSSTools;

class BasicBuilderTest extends TestCase
{
    public function testCreateBasicRSS()
    {
        $srss = SRSS::create();
        $srss->title = 'My Blog';
        $srss->description = 'is the best';
        $srss->link = 'http://shikiryu.com/devblog/';
        $items = [
            ['title' => 'title 1', 'link' => 'http://shikiryu.com/devblog/article-1', 'pubDate' => SRSSTools::getRSSDate('2012-03-05 12:02:01'), 'description' => 'description 1'],
            ['title' => 'title 2', 'link' => 'http://shikiryu.com/devblog/article-2', 'pubDate' => SRSSTools::getRSSDate('2022-03-05 22:02:02'), 'description' => 'description 2'],
            ['title' => 'title 3', 'link' => 'http://shikiryu.com/devblog/article-3', 'pubDate' => SRSSTools::getRSSDate('2032-03-05 32:02:03'), 'description' => 'description 3'],
            ['title' => 'title 4', 'link' => 'http://shikiryu.com/devblog/article-4', 'pubDate' => SRSSTools::getRSSDate('2042-03-05 42:02:04'), 'description' => 'description 4'],
        ];
        foreach ($items as $item) {
            $rssItem = new Item();
            $rssItem->title = $item['title'];
            $rssItem->link = $item['link'];
            $rssItem->pubDate = $item['pubDate'];
            $rssItem->description = $item['description'];
            $srss->addItem($rssItem);
        }

        self::assertTrue($srss->isValid());

        $filepath = __DIR__.'/resources/tmp/build/testCreateBasicRSS.rss';
        $builder = new SRSSBuilder();
        $builder->build($srss, $filepath);

        self::assertFileExists($filepath);
    }
}