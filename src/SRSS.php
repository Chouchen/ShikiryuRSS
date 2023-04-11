<?php

namespace Shikiryu\SRSS;

use Iterator;
use ReflectionException;
use Shikiryu\SRSS\Builder\SRSSBuilder;
use Shikiryu\SRSS\Entity\Channel;
use Shikiryu\SRSS\Entity\Item;
use Shikiryu\SRSS\Exception\ChannelNotFoundInRSSException;
use Shikiryu\SRSS\Exception\PropertyNotFoundException;
use Shikiryu\SRSS\Exception\SRSSException;
use Shikiryu\SRSS\Exception\UnreadableRSSException;
use Shikiryu\SRSS\Parser\SRSSParser;

class SRSS implements Iterator
{
    public Channel $channel;

    /** @var Item[] */
    public array $items; // array of SRSSItems

    private int $position; // Iterator position

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
     * @throws ChannelNotFoundInRSSException
     * @throws UnreadableRSSException
     * @throws SRSSException
     */
    public static function read(string $link): SRSS
    {
        return (new SRSSParser())->parse($link);
    }

    /**
     * @return SRSS
     */
    public static function create(): SRSS
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
        try {
            $valid = true;
            foreach ($this->getItems() as $item) {
                if ($item->isValid() === false) {
                    $valid = false;
                }
            }

            return ($valid && $this->channel->isValid());
        } catch (ReflectionException $e) {
            return false;
        }
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
            throw new PropertyNotFoundException(Channel::class, $name);
        }
        // TODO add validator ?
//        if ((new Validator())->isPropertyValid($this->channel, $name)) {
            $this->channel->{$name} = $val;
//        }
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
    public function current(): Item
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

        return array_filter($doc);
    }

    /**
     * @param Item $rssItem
     *
     * @return array|Item[]
     */
    public function addItem(Item $rssItem): array
    {
        $this->items[] = $rssItem;

        return $this->items;
    }

    /**
     * @param Item $firstItem
     *
     * @return array|Item[]
     */
    public function addItemBefore(Item $firstItem): array
    {
        array_unshift($this->items, $firstItem);

        return $this->items;
    }

    /**
     * @param string $path
     *
     * @return void
     */
    public function save(string $path): void
    {
        (new SRSSBuilder('1.0', 'UTF-8'))->build($this, $path);
    }

    /**
     * @return false|string
     */
    public function show()
    {
        return (new SRSSBuilder('1.0', 'UTF-8'))->show($this);
    }

}
