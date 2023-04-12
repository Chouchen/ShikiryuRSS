<?php

use PHPUnit\Framework\TestCase;
use Shikiryu\SRSS\Builder\SRSSBuilder;
use Shikiryu\SRSS\Entity\Item;
use Shikiryu\SRSS\SRSS;
use Shikiryu\SRSS\SRSSTools;

class BasicBuilderTest extends TestCase
{
    private string $saved = __DIR__.'/resources/tmp/build/testCreateBasicRSS.rss';
    protected function tearDown(): void
    {
        parent::tearDown();
        if (file_exists($this->saved)) {
            unlink($this->saved);
        }
    }
    public function testCreateBasicRSS(): void
    {
        $title = 'My Blog';
        $description = 'is the best';
        $link = 'http://shikiryu.com/devblog/';
        $srss = SRSS::create();
        $srss->title = $title;
        $srss->description = $description;
        $srss->link = $link;
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

        self::assertEquals($title, $srss->title);
        self::assertEquals($description, $srss->description);
        self::assertEquals($link, $srss->link);

        $builder = new SRSSBuilder();
        $builder->build($srss, $this->saved);

        self::assertFileExists($this->saved);

        self::assertIsString($srss->show());
    }
}