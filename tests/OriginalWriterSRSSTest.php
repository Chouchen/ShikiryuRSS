<?php

use PHPUnit\Framework\TestCase;
use Shikiryu\SRSS\Entity\Item;
use Shikiryu\SRSS\SRSS;
use Shikiryu\SRSS\SRSSTools;

class OriginalWriterSRSSTest extends TestCase
{
    public function testOriginalWriter(): void
    {
        $rss = SRSS::create();
        $rss->title = 'My Awesome Blog';
        $rss->link = 'http://shikiryu.com/devblog/';
        $rss->description = 'is awesome';

        $items = [
            ['title' => 'title 1', 'link' => 'http://shikiryu.com/devblog/article-1', 'pubDate' => SRSSTools::getRSSDate('2012-03-05 12:02:01'), 'description' => 'description 1'],
            ['title' => 'title 2', 'link' => 'http://shikiryu.com/devblog/article-2', 'pubDate' => SRSSTools::getRSSDate('2022-03-05 22:02:02'), 'description' => 'description 2'],
            ['title' => 'title 3', 'link' => 'http://shikiryu.com/devblog/article-3', 'pubDate' => SRSSTools::getRSSDate('2032-03-05 32:02:03'), 'description' => 'description 3'],
            ['title' => 'title 4', 'link' => 'http://shikiryu.com/devblog/article-4', 'pubDate' => SRSSTools::getRSSDate('2042-03-05 42:02:04'), 'description' => 'description 4'],
        ];

        foreach($items as $item){
            $rss_item = new Item();
            $rss_item->title = $item["title"];
            $rss_item->link = $item['link'];
            $rss_item->pubDate = $item["pubDate"];
            $rss_item->description =  $item["description"];
            $rss->addItem($rss_item);
        }

        $firstItem = new Item();
        $firstItem->title = 'title 0';
        $firstItem->link = 'http://shikiryu.com/devblog/article-0';
        $firstItem->pubDate = SRSSTools::getRSSDate('2011-03-05 12:02:01');
        $firstItem->description = 'description 0';
        $rss->addItemBefore($firstItem);

        self::assertCount(5, $rss->items, var_export($rss->items, true));
        self::assertEquals('title 0', $rss->getFirst()->title, var_export($rss->items, true));
        self::assertEquals('title 1', $rss->getItem(2)->title, var_export($rss->items, true));

    }
}