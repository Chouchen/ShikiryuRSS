<?php

namespace Shikiryu\SRSS;

use Iterator;
use Shikiryu\SRSS\Entity\Channel;
use Shikiryu\SRSS\Entity\Item;

class SRSS implements Iterator
{
    public Channel $channel;
    public array $items; // array of SRSSItems

    private int $position; // Iterator position

    // lists of possible attributes for RSS
    protected $possibleAttr = [
        'title'          => 'nohtml',
        'link'           => 'link',
        'description'    => 'html',
        'language'       => '',
        //'language'			=> 'lang',
        'copyright'      => 'nohtml',
        'pubDate'        => 'date',
        'lastBuildDate'  => 'date',
        'category'       => 'nohtml',
        'docs'           => 'link',
        'cloud'          => '',
        'generator'      => 'nohtml',
        'managingEditor' => 'email',
        'webMaster'      => 'email',
        'ttl'            => 'int',
        'image'          => '',
        'rating'         => 'nohtml',
        //'textInput'			=> '',
        'skipHours'      => 'hour',
        'skipDays'       => 'day',
    ];

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->items = [];
        $this->position = 0;
    }

    /**
     * @param string $link url of the rss
     *
     * @return SRSS
     * @throws SRSSException
     */
    public static function read(string $link): SRSS
    {
        return (new SRSSParser())->parse($link);
    }

    /**
     * @return SRSS
     * @throws \DOMException
     */
    public static function create()
    {
        $doc = new SRSS;

        $doc->channel = new Channel();
        $doc->items = [];

        return $doc;
    }

    /**
     * check if current RSS is a valid one (based on specifications)
     * @return bool
     */
    public function isValid(): bool
    {
        $valid = true;
        $items = $this->getItems();
        $invalidItems = [];
        $i = 1;
        foreach ($items as $item) {
            if ($item->isValid() === false) {
                $invalidItems[] = $i;
                $valid = false;
            }
            $i++;
        }

        return ($valid && $this->channel->isValid());
    }

    /**
     * @param $name
     *
     * @return bool
     */
    public function __isset($name)
    {
        return isset($this->channel->{$name});
    }

    /**
     * setter of others attributes
     *
     * @param $name
     * @param $val
     *
     * @throws SRSSException
     */
    public function __set($name, $val)
    {
        if (!property_exists(Channel::class, $name)) {
            throw new SRSSException($name . ' is not a possible item');
        }
        $flag = $this->possibleAttr[$name];
        $val = SRSSTools::check($val, $flag);
        if (!empty($val)) {
            $this->channel->{$name} = $val;
        }
    }

    /**
     * getter of others attributes
     *
     * @param $name
     *
     * @return null|string
     */
    public function __get($name)
    {
        return $this->channel->{$name} ?? null;
    }

    /**
     * rewind from Iterator
     */
    public function rewind(): void
    {
        $this->position = 0;
    }

    /**
     * current from Iterator
     */
    public function current(): mixed
    {
        return $this->items[$this->position];
    }

    /**
     * key from Iterator
     */
    public function key(): int
    {
        return $this->position;
    }

    /**
     * next from Iterator
     */
    public function next(): void
    {
        ++$this->position;
    }

    /**
     * valid from Iterator
     */
    public function valid(): bool
    {
        return isset($this->items[$this->position]);
    }

    /**
     * getter of 1st item
     * @return Item|null
     */
    public function getFirst(): ?Item
    {
        return $this->getItem(1);
    }

    /**
     * getter of last item
     * @return Item
     */
    public function getLast(): Item
    {
        $items = $this->getItems();

        return $items[array_key_last($items)];
    }

    /**
     * getter of an item
     *
     * @param $i int
     *
     * @return Item|null
     */
    public function getItem(int $i): ?Item
    {
        $i--;

        return $this->items[$i] ?? null;
    }

    /**
     * getter of all items
     * @return Item[]
     */
    public function getItems(): array
    {
        return $this->items;
    }

    /**
     * transform current object into an array
     * @return array
     */
    public function toArray(): array
    {
        $doc = $this->channel->toArray();

        foreach ($this->getItems() as $item) {
            $doc['items'][] = $item->toArray();
        }

        return $doc;
    }
}
