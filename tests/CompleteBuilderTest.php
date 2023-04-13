<?php

use PHPUnit\Framework\TestCase;
use Shikiryu\SRSS\Entity\Channel\Category;
use Shikiryu\SRSS\Entity\Channel\Cloud;
use Shikiryu\SRSS\Entity\Channel\Image;
use Shikiryu\SRSS\Entity\Item\Enclosure;
use Shikiryu\SRSS\Entity\Item\Source;
use Shikiryu\SRSS\SRSS;

class CompleteBuilderTest extends TestCase
{
    public function testBuildACompleteRSS()
    {
        $file = __DIR__ . '/resources/tmp/build/complete.rss';
        $title = 'Test';
        $link = 'https://example.org';
        $description = 'My description is <a href="https://example.org">better</a>';
        $language = 'en-us';
        $copyright = 'Shikiryu';
        $managingEditor = 'editor';
        $webMaster = 'Shikiryu';
        $pubDate = (new DateTime())->format(DATE_RSS);
        $lastBuildDate = $pubDate;
        $category = new Category();
        $category->domain = $link;
        $category->value = 'Test Category';
        $generator = 'SRSS';
        $docs = $link;
        $cloud = new Cloud();
        $cloud->domain = $link;
        $cloud->port = 80;
        $cloud->path = '/test';
        $ttl = 3660;
        $image = new Image();
        $image->link = $link;
        $image->title = 'title of image';
        $rating = 'yes';
        $textInput = 'ignore';
        $skipDays = 'monday';
        $skipHours = '8';

        $srss = SRSS::create();
        $srss->title = $title;
        $srss->link = $link;
        $srss->description = $description;
        $srss->language = $language;
        $srss->copyright = $copyright;
        $srss->managingEditor = $managingEditor;
        $srss->webMaster = $webMaster;
        $srss->pubDate = $pubDate;
        $srss->lastBuildDate = $lastBuildDate;
        $srss->category = $category;
        $srss->generator = $generator;
        $srss->docs = $docs;
        $srss->cloud = $cloud;
        $srss->ttl = $ttl;
        $srss->image = $image;
        $srss->rating = $rating;
        $srss->textInput = $textInput;
        $srss->skipDays = $skipDays;
        $srss->skipHours = $skipHours;

        $item_title = 'item title';
        $item_link = 'https://example.com';
        $item_description = 'item <strong>description</strong>';
        $item_author = 'shikiryu@shikiryu.com';
        $item_category = new \Shikiryu\SRSS\Entity\Item\Category();
        $item_category->domain = 'https://shikiryu.com';
        $item_category->value = 'category shikiryu';
        $item_comments = $link.'/comments';
        $item_enclosure = new Enclosure();
        $item_enclosure->url = $item_link;
        $item_enclosure->length = 5023;
        $item_enclosure->type = 'audio/mp3';
        $item_guid = '123456';
        $item_pubdate = $pubDate;
        $item_source = new Source();
        $item_source->url = $item_link;
        $item_source->value = 'source';
        $item_media = new Shikiryu\SRSS\Entity\Media\Content();
        $item_media->url = $item_link;
        $item_media->type = 'image/jpg';
        $item = new Shikiryu\SRSS\Entity\Item();
        $item->title = $item_title;
        $item->link = $item_link;
        $item->description = $item_description;
        $item->author = $item_author;
        $item->category[] = $item_category;
        $item->comments = $item_comments;
        $item->enclosure = $item_enclosure;
        $item->guid = $item_guid;
        $item->pubDate = $item_pubdate;
        $item->source = $item_source;
        $item->medias[] = $item_media;

        $srss->addItem($item);

        self::assertTrue($srss->isValid(), var_export($srss->channel->validated + array_map(static fn ($item) => $item->validated, $srss->items), true));
    }
}