<?php
include('global.php');
include('pagesetup.php');


$page = clone $_SESSION['site'];

$page->setTitle('Pony Rides - Southern Tier Stables');
$page->meta('keywords','Southern Tier Stables, Moravia NY, Moravia, Pony, trail riding, children parties, carousel, fairs, festivals, carnivals, pony carousel');
$page->meta('description','Pony Carousel perfect for carnivals, festivals, fairs and parties. Up to 6 pony carousel.');


$page->setStyle('
#services > .subMenu{
display:block;
}
');


$center = $page->findChild('center');
$right=new Panel('right');
$right->setUnique('right'); 

$left=new Panel('left');
$left->setUnique('left');



$rides = new Text('','<b>Pony Rides</b>
<p>
We have a (Up to) 6 Pony Carousel <br />
Available for Fairs, Carnivals, and Festivals
</p>
<p>
<b>Call for Availability:<br />
(315) 496-2609</b>
</p>');



$view = new GalleryView();
$view->setTag('pony');


$center->add($left);
$center->add($right);
$left->add($rides);
$right->add($view);

//$center->add($welcome);

$page->process();
$page->scriptUnload('menu.js');
$page->show();

//$page->awesomeDebug();

?>
