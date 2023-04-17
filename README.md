# SћíкïяўЦЯSS 

> PHP library reading and creating RSS

**disclaimer:**
This class is functional. Anyway, use it only if you don't have other choices.  
For example, [Zend](http://framework.zend.com/manual/current/en/modules/zend.feed.introduction.html) and Symfony got their own RSS factory, don't add another one in.


## :books: Table of Contents

- [Installation](#package-installation)
- [Usage](#rocket-usage)
- [Features](#sparkles-features)
- [Support](#hammer_and_wrench-support)
- [Contributing](#memo-contributing)
- [License](#scroll-license)

## :package: Installation

```sh
composer install shikiryu/shikiryurss
```

or 

```php
include '/path/to/this/library/autoload.php';
```

## :rocket: Usage

----------------------------------

How to make it read RSS?

First, we need to load the RSS :

    $rss = SRSS::read('http://exemple.com/rss.xml');

Easy, right? Then you can extract general information :

    echo $rss->title; // will display blog title

Then, you can take care of articles. You can select a precise article :

    $article1 = $rss->getItem(1); // or $rss->getFirst();

Or looping them :

    foreach($rss as $article) {
        echo '<a href="'.$article->link.'">'. SRSSTools::formatDate('d/m/y', $item->pubDate).' '.$item->title.'';
    }

If you like arrays, you can transform the RSS into an array :

    $rssArray = $rss->toArray();

You can also save it into your server with :

    $rss->save('/www/rss/rss.xml'); // example

Or finally, you can display it with :

    $rss->show();

----------------------------------

How to make it create RSS?

First, we need to initialize the RSS :

    $rss = SRSS::create();

Easy, right? Then you can add general information :

    $rss->title = 'My Awesome Blog';
    $rss->link = 'http://shikiryu.com/devblog/';
    $rss->description = 'is awesome';

Those 3 are mandatory to validate your RSS, other options can be added.
Then, you can add articles. Let's imagine $content contains an array from your database.

    foreach($content as $item){
        $rssitem = new Item(); // we create an item
        $rssitem->title = $item["title"]; // adding title (option)
        $rssitem->link = $item['link']; // adding link (option)
        $rssitem->pubDate = $item["date"]; // date automatically transformed into RSS format (option)
        $rssitem->description = $item["text"]; // adding description (mandatory)
        $rss->addItem($rssitem); // we add the item into our RSS
    }

There are 2 functions to add item.
The first one will add items in the order you enter them, from top to bottom.

    $rss->addItem($item);

The other one does the opposite and add the next item in top of your RSS

    $rss->addItemBefore($item);

----------------------------------

## :sparkles: Features

<dl>
  <dt>Read every RSS 2.0</dt>
  <dd>
    Based on RSS 2.0 specifications.
  </dd>
</dl>

<dl>
  <dt>Write and validate RSS 2.0 file</dt>
  <dd>
    Based on RSS 2.0 specifications.
  </dd>
</dl>

## :hammer_and_wrench: Support

Please [open an issue](https://github.com/Chouchen/ShikiryuRSS/issues) for support.


## :memo: Contributing

Please contribute using [Github Flow](https://guides.github.com/introduction/flow/). Create a branch, add commits, and [open a pull request](https://github.com/Chouchen/ShikiryuRSS/pulls).

## :scroll: License

[Creative Commons Attribution NonCommercial (CC-BY-NC)](<https://tldrlegal.com/license/creative-commons-attribution-noncommercial-(cc-nc)>) © [Chouchen](https://github.com/Chouchen/)

All documentation @ http://labs.shikiryu.com/SRSS/#_how.

Contact : https://shikiryu.com/contact








