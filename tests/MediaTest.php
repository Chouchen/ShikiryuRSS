<?php

use PHPUnit\Framework\TestCase;
use Shikiryu\SRSS\SRSS;

class MediaTest extends TestCase
{
    public function testImages()
    {
        $rss = SRSS::read('resources/media/cnn.xml');
        self::assertEquals('CNN.com - RSS Channel - Entertainment', $rss->title);

        $first_item = $rss->getFirst();
        self::assertEquals('Kirstie Alley, \'Cheers\' and \'Veronica\'s Closet\' star, dead at 71', $first_item->title);

        self::assertEquals('https://cdn.cnn.com/cnnnext/dam/assets/221205172141-kirstie-alley-2005-super-169.jpg', $first_item->medias[0]->url);
    }

    public function testMusicVideo()
    {
        $rss = SRSS::read('resources/media/music-video.xml');
        self::assertEquals('Music Videos 101', $rss->title);

        self::assertCount(1, $rss->items);

        $first_item = $rss->getFirst();
        self::assertEquals('http://www.foo.com/movie.mov', $first_item->medias[0]->url);
    }
}