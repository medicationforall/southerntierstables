<?php
include('global.php');
include('pagesetup.php');


$page = clone $_SESSION['site'];

$page->setTitle('Facilities - Southern Tier Stables');
$page->meta('keywords','Southern Tier Stables, Moravia NY, Moravia, horse stalls, stalls, boarding, turn out pastures with shelters, wash bay, riding arenas, indoor riding arena, outdoor riding arena, grandstand');
$page->meta('description','Facility includes 10 ft by 10 ft stalls, 10 ft by 12 ft stalls, mats and windows in every stall, turn out pastures with shelters, wash bay, heated tack room. Indoor Arena and Outdoor Arena with Grandstand and announcer booth.');

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

$facility = new Text('','<b>Facilities</b><br/>

<p> 
<b>Our amenities include:</b>
<ul>
<li>Twelve 10ft x 10ft horse stalls</li>
<li>Twelve 10ft x 12ft horse stalls</li>
<li>Mats and windows in every stall</li>
<li>Turn out pastures with shelters</li>
<li>Two-way entrance wash bay with hot and cold water</li>
<li>Heated tack, restroom and utility room</li>
</ul>
</p>
<p>
<b>Riding Arenas:</b>
<ul>
<li><b>Indoor</b></li>
<ul>
<li>72ft x 144ft (16ft high)</li>
<li>Area is well lit</li>
<li>3 large overhead doors; 4 emergency exit doors</li>
<li>Stall areas attached to arena with overhead doors</li>
<li>Large window to view arena</li>
</ul> 
<li><b>Outdoor</b></li>
<ul>
<li>60 foot round pen</li>
<li>90 x 181 foot rectangular ring</li>
<li>Sand filled</li>
<li>Grandstand with announcer booth</li>
</ul>
</ul>
</p>

Our space can accommodate many different types of events!<br />
Please call our Office to see if our facility fits your events needs.<br />
(315) 496-2609 ');

$view = new GalleryView();
$view->setTag('facilities');

$center->add($left);
$center->add($right);
$left->add($facility);
$right->add($view);
//$center->add($facility);

//$center->add($welcome);

$page->process();
$page->scriptUnload('menu.js');
$page->show();

//$page->awesomeDebug();

?>
