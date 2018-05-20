<?php
include('global.php');
include('pagesetup.php');


$page = clone $_SESSION['site'];

$page->setTitle('News - Southern Tier Stables');
$page->meta('keywords','Southern Tier Stables, Moravia NY, Moravia, news, southern tier stables, makin\' memories, articles, youtube videos, youtube, horse assisted therapy');
$page->meta('description','News, Where we have been mentioned in news articles and videos.');

$page->setStyle('
#about > .subMenu{
display:block;
}
');

$center = $page->findChild('center');


/*
$right=new Panel('right');
$right->setUnique('right'); 

$left=new Panel('left');
$left->setUnique('left');

$center->add($left);
$center->add($right);
$left->add($welcome);
*/
$right=new Panel('right');
$right->setUnique('right'); 

$left=new Panel('left');
$left->setUnique('left');


$news = new Text('','<b>Makin\' Memories Therapeutic Riding Center News </b>
<p>
<b>Articles:</b><br />
<a href="http://auburnpub.com" target="_blank">The Citizen:</a><br />
<ul>

<li><a href="http://auburnpub.com/news/local/moravia-therapeutic-horse-riding-stable-receives-mechanical-lift-to-get/article_9eb4d44f-5c9d-5776-864d-42383e194e21.html" target="_blank">Moravia therapeutic horse riding stable receives mechanical lift to get riders to horses</a></li>

<li><a href="http://auburnpub.com/lifestyles/freedom-riders-moravia-s-makin-memories-offers-horse-assisted-therapy/article_a62ecc92-e140-53fc-a1e7-67eaaf6e94b8.html" target="_blank">Freedom Riders: Moravia\'s Makin\' Memories offers horse-assisted therapy to disabled children, adults</a></li>
</ul>
<a href="http://www.syracuse.com" target="_blank">Syracuse.com</a><br />
<ul>
<li><a href="http://www.syracuse.com/news/index.ssf/2012/12/freedom_recreational_services.html" target="_blank">Freedom Riders and Southern Tier Stables</a></li>
</ul>
</p>
<p>
<b>Videos:</b><br />
<a href="http://auburnpub.com" target="_blank">The Citizen:</a><br />
<ul>
<li><a href="https://www.youtube.com/watch?v=8wvym74X0Rw" target="_blank">Makin\' Memories horse-assisted therapy</a></li>
</ul>

</p>
');

$video = new Code ('<iframe width="550" height="315" src="https://www.youtube.com/embed/8wvym74X0Rw" frameborder="0" allowfullscreen></iframe>');

$center->add($left);
$center->add($right);
$left->add($news);
$right->add($video);

//$center->add($news);


//$center->add($welcome);

$page->process();
$page->scriptUnload('menu.js');
$page->show();

//$page->awesomeDebug();

?>
