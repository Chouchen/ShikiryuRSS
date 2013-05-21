All documentation @ http://labs.shikiryu.com/SRSS/#_how

----------------------------------

How to make it read RSS?

First, we need to load the RSS :

    $rss = SRSS::read('http://exemple.com/rss.xml');

Easy, right? Then you can extract general informations :

    echo $rss->title; // will display blog title

Then, you can take care of articles. You can select a precise article :

    $article1 = $rss->getItem(1); // or $rss->getFirst();

Or looping them :

    foreach($rss as $article)
    {
		echo '<a href="'.$article->link.'">'. SRSSTools::formatDate('d/m/y', $item->pubDate).' '.$item->title.'';
    }

If you like arrays, you can transform the RSS into an array :

    $rssArray = $rss->toArray();

You can also save it into your server with :

    $rss->save('/www/rss/rss.xml'); // example
	
----------------------------------

How to make it create RSS?

First, we need to initialize the RSS :

    $rss = SRSS::create();

Easy, right? Then you can add general informations :

    $rss->title = 'My Awesome Blog';
    $rss->link = 'http://shikiryu.com/devblog/';
    $rss->description = 'is awesome';

Those 3 are mandatory to validate your RSS, other options can be added.
Then, you can add articles. Let's imagine $content contains an array from your database.

    foreach($content as $item){
		$rssitem= new SRSSItem; // we create an item
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

Contact :
http://shikiryu.com/contact
