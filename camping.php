<?php
include('global.php');
include('pagesetup.php');


$page = clone $_SESSION['site'];

$page->setTitle('Camping - Southern Tier Stables');

$page->meta('keywords','Camping, Campsites, Cabins, Campers, southern tier stables, moravia ny, moravia, rustic camping, secluded and private setting');
$page->meta('description','Camping Season officially begins Memorial Day and runs through to Labor Day.
Southern Tier Stables has cabins and campers available for rent. 
We also have rustic campsites available, with a limited amount having access to water and bathroom facilities.');


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


$camping = new Text('','<b>Camping </b>
<p>
<b>Information for Campers</b>
<p>
Camping Season officially begins Memorial Day and runs through to Labor Day.
</p>
<p>
Southern Tier Stables has a limited amount of cabins and campers available for rent. <br />
We also have rustic campsites available, with a limited amount having access to water and bathroom facilities. 
</p>
<p>
<ul>
<li><b>Cabin Rates </b></li>
<ul>
	<li>Sleeps 4 max.</li>
	<li>1 Room</li>
	<li>Fully Insulated</li>
	<li>Heated</li>
	<li>Double Bed</li>
	<li>Table and Chairs</li>
	<li>Battery Powered Lighting</li>
	<li>Rustic, private area</li>
	<li>Picnic Table</li>
	<li>Firepit</li>
	<li>Use of Barn and Turnout Pen</li>

<ul>
		<li><b>Daily:</b> Rates to Come</li>	
		<li><b>Weekend:</b> Rates to Come</li>
		<li><b>Weekly:</b> Rates to Come</li>
</ul>
</ul>
</ul>
</p>
<p>
<ul>
<li><b>Camper Rates </b></li>
<ul>
	<li>Sleeps 6 max.</li>
	<li>Self Contained</li>
	<li>Rustic, private area</li>
	<li>Picnic Table</li>
	<li>Firepit</li>
	<li>Use of Barn and Turnout Pen</li>
<ul>	
		<li><b>Daily:</b> Rates to Come</li>
		<li><b>Weekend (</b>Friday through Sunday<b>):</b> Rates to Come</li>
</ul>
</ul>
</ul>
</p>
<p>
<ul>
<li><b>Campsite Rates</b></li>
<ul>
	<li>Rustic, private area</li>
	<li>Picnic Table</li>
	<li>Firepit</li>
	<li>Use of Barn and Turnout Pen</li>
<ul>	
	<li><b>Daily:</b> Rates to Come</li>
	<li><b>Weekend (</b>Friday through Sunday<b>):</b> Rates to Come</li>
</ul>
</ul>
</ul>
</p>


<p>
We have a number of activities available on the premises.
<ul>
<li>Horse Shoe Pit</li>
<li>Volleyball Court/Net</li>
<li>Fishing (Catch & Release)</li>
<li>Paddle Boat Rentals Available</li>
<li>Supervised Swimming</li>
<li>Hiking</li>
<li>Horseback Riding</li>
<li><a href="trailriding.php">Trail Riding</a></li>
<li>Scheduled Entertainment</li>
Check out our calendar for our <a href="events.php">Events Schedule</a>
</ul>
</p>


<p>
All sites are secluded for privacy, peacefulness, and quiet.<br />
All sites come with a picnic table and firepit. <br />
Use of barn and turnout pen included.<br />
Firewood and water available.<br />
Generators welcome.
</p>
<p>
<b>NEW!</b>
<ul>
<li>10 Stall Tie Barn</li>[Picture]
<li>40\' x 50\' Wooden & Nail Turnout Pen</li>[Picture]
</ul>
</p>
<p>
<b>
Reservations Required<br />
Please Call for Availability<br />
(315) 496-2609 </b>
</p>
</p>');

$view = new GalleryView();
$view->setTag('camping');


$center->add($left);
$center->add($right);
$left->add($camping);
$right->add($view);

//$center->add($camping);
//$center->add($welcome);

$page->process();
$page->scriptUnload('menu.js');
$page->show();

//$page->awesomeDebug();

?>
