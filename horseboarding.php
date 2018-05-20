<?php
include('global.php');
include('pagesetup.php');


$page = clone $_SESSION['site'];

$page->setTitle('Horse Boarding - Southern Tier Stables');
$page->meta('keywords','Southern Tier Stables, Moravia NY, children, adults,challenging experience, safe environment, horsemanship, hiking, horse boarding, boarding');
$page->meta('description','We offer horse boarding for $350.00 a month. Fresh Water as needed, hay twice daily and grain as required, daily turn out, pastures with shelters, cleaned daily stalls, full use of facilities.');

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



$boarding = new Text('','<b>Horse Boarding</b>
<p>
Rate: $350/month
</p>
<p>
Includes:<br />
<ul>
	<li>Fresh water as needed</li>
	<li>Hay twice daily; grain as required</li>
	<li>Daily turnout (weather depending)</li>
<ul>
		<li>Pastures with shelters</li>
</ul>
	<li>10ft square or 10ft x 12ft stall</li>
<ul>
		<li>Mats and windows in every stall</li>
		<li>Cleaned daily</li>
</ul>
	<li>Full use of the facilities </li>
<ul>
		<li><a href="facilities.php">List of amenities available </a></li>
		<li>(including the wash bay, trails, arenas, 60ft round pen, etc)</li>
</ul>
</ul>
</p>
<p>
Call to inquire about availablity<br />
(315) 496-2609 
</p>');


$view = new GalleryView();
$view->setTag('indoor');

$center->add($left);
$center->add($right);
$left->add($boarding);
$right->add($view);

//$center->add($boarding);

//$center->add($welcome);

$page->process();
$page->scriptUnload('menu.js');
$page->show();

//$page->awesomeDebug();

?>
