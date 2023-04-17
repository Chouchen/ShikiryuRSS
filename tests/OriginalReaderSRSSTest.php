<?php

use PHPUnit\Framework\TestCase;
use Shikiryu\SRSS\SRSS;
use Shikiryu\SRSS\SRSSTools;
use Shikiryu\SRSS\Validator\Formator;

class OriginalReaderSRSSTest extends TestCase
{
    private string $original = __DIR__.'/resources/harvard.xml';
    private string $saved = __DIR__.'/resources/tmp/build/rss.xml';
    protected function tearDown(): void
    {
        parent::tearDown();
        if (file_exists($this->saved)) {
            unlink($this->saved);
        }
    }

    public function testOriginalReader(): void
    {
        $rss = SRSS::read($this->original);
        self::assertEquals('<![CDATA[ Liftoff News ]]>', $rss->title);

        $article1 = $rss->getItem(1);
        $articleFirst = $rss->getFirst();
        self::assertEquals($article1, $articleFirst);

        $links = [];
        foreach($rss as $article) {
            $links[] = sprintf('<a href="%s">%s %s</a>', $article->link, SRSSTools::getRSSDate('d/m/y', $article->pubDate), $article->title);
        }
        self::assertCount(4, $links, var_export($links, true));

        $rssArray = $rss->toArray();
        self::assertCount(11, $rssArray, var_export($rssArray, true)); // 11 elements in RSS

        $rss->save($this->saved);

        self::assertFileExists($this->saved);
    }
}