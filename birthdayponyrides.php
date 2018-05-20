<?php
include('global.php');
include('pagesetup.php');


$page = clone $_SESSION['site'];

$page->setTitle('Birthday Parties and Pony Rides - Southern Tier Stables');
$page->meta('keywords','Southern Tier Stables, Moravia NY, Moravia, Pony, trail riding, children parties, Birthday Pony Rides, Birthday Party');
$page->meta('description','Birthday Parties and Pony Rides. Perfect for any party! Includes Pony Rides and use of a heated room with plenty of space for tables and decorations.');

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



$parties = new Text('','<b>Birthday Parties and Pony Rides</b>
<p>
     Perfect for any party! Includes use of a heated room with plenty of space for tables and decorations. 
</p>
<p>
<b>Rates Example:</b><br />
$200<br />
<ul>
<li>Included:</li>
<ul>
<li>6 Children Max</li>
<li>Pony Rides for 1 hour</li>
<li>Room Rental for 2 hours</li>
<li>Tables and Chairs</li>
</ul>
</ul>
Plates and utensils not included.*
<br />
</p>
<p>
<b>Prices Vary Depending on Options</b><br />

Included:
<ul>
<li>Room rental for 2 hours</li>
<li>Tables and Chairs</li>
</ul>
Plates and utensils not included.*<br />
</p>
<p>
<b>Additional Options with Additional Costs:</b>
<ul>
<li>Additional Children</li>
<li>Longer Pony Rides</li>
<li>Trail Riding</li>
</ul>
<br />
</p>
<p>
<b>Please Call for Rates:<br />
(315) 496-2609 </b>
<p>
*Delivery Service Available from Local Pizza Shop<br />
Full Menu Available.*
</p>
</p>');


$view = new GalleryView();
$view->setTag('pony');


$center->add($left);
$center->add($right);
$left->add($parties);
$right->add($view);
//$center->add($parties);
//$center->add($welcome);

$page->process();
$page->scriptUnload('menu.js');
$page->show();

//$page->awesomeDebug();

?>
