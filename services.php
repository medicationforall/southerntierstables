<?php
include('global.php');
include('pagesetup.php');


$page = clone $_SESSION['site'];

$page->setTitle('Services - Southern Tier Stables');
$page->meta('keywords','Southern Tier Stables, Moravia NY, children, adults,challenging experience, safe environment, horsemanship, hiking, birthday parties, pony rides, camping, haunted hayride, physical education course, cills program, hillside program, freedom riders program, trail riding, trails, riding lessons, horse boarding, boarding, horse starting and training, starting, training, horse, tack shop, birthday pony rides');
$page->meta('description','We offer a variety of services to our customers. From our Therapeutic Riding Center, to riding lessons, horse boarding and starting, trail riding, and an on site tack shop; to hiking, birthday parties, camping and our haunted hay ride every October. We also host many events throughout the year.');

$page->setStyle('
#services > .subMenu{
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

$service = new Text('','<b>Services</b><br/>
<p>
We offer a variety of services to our customers. From our Therapeutic Riding Center, to riding lessons, horse boarding and starting, trail riding, and an on site tack shop; to hiking, birthday parties, camping and our <a href="http://www.mysticmountainhauntedhayride.com" target="_blank">haunted hay ride</a> every October.
</p>
<p>
We have partnered with Wells College located in Aurora, NY to offer students a different kind of Physical Education Course. <br />
PE 129 Horseback Riding, more information <a href="pe129.php">here</a>. 
</p>
<p>
We are also apart of the Cayuga Community College CILLS Program, in which riders with special needs participate in a four week program learning to become independent riders.
</p>
<p>
We also host many events throughout the year. Please see our <a href="events.php">calendar</a>. 
</p>');

$riding = new GalleryView();
$riding->setTag('riding,');

$trails = new GalleryView();
$trails->setTag('trails');

$boarding = new GalleryView();
$boarding->setTag('horseBoarding');

$starting = new GalleryView();
$starting->setTag('starting');

/*
$center->add($service);
*/
$center->add($left);
$center->add($right);
$left->add($service);
$right->add($trails);
//$right->add($boarding);
//$right->add($starting);
//$right->add($riding);
//$center->add($welcome);

$page->process();
$page->scriptUnload('menu.js');
$page->show();

//$page->awesomeDebug();

?>
