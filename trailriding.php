<?php
include('global.php');
include('pagesetup.php');


$page = clone $_SESSION['site'];

$page->setTitle('Trail Riding - Southern Tier Stables');
$page->meta('keywords','Southern Tier Stables, Moravia NY, children, adults, challenging experience, safe environment, horsemanship, hiking, trail riding, trails, horseback riding, state land, filmore glen, filmore glen state park, campers, camping, camp, tours');
$page->meta('description','Southern Tier Stables has over 100 acres of maintained trails that run adjacent to 2,000 acres of State land. Our maintained trails are located mostly in the woods, trails are open for hiking and horseback riding. These trails include hills, small streams to cross, and areas of open fields.The State land adjacent, has many more horse and hiking trails to explore.');

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


$trail = new Text('','<b>Trail Riding</b>
<p>
Southern Tier Stables has over 100 acres of maintained trails that run adjacent to 2,000 acres of State land. <br />
Our maintained trails are located mostly in the woods, trails are open for hiking and horseback riding. These trails include hills, small streams to cross, and areas of open fields.<br />
The State land adjacent, has many more horse and hiking trails to explore.
</p>
 
<p>
Trailer-ins are welcome. <br />
Weekend campers are also welcome. 
</p>
<p>
For any rider wishing to use a Southern Tier Stables Horse, the ride includes a brief 10-15 minute instructional lesson to gauge the abilities of the rider and suitability of horse to the rider. We also require any rider on a Southern Tier Stables Horse to wear a helmet, regardless of age and/or riding experience. <br />
Please dress comfortably and wear a shoe or boot with a heel.
</p>
<p>
<ul>
<li>Rates: </li>
<ul>
<li>Hourly: $50</li>
</ul>
</ul>
</p>
<p>
*Tours are always guided.*<br />
*We reserve the right to deny your ride (with refund) or send you out on a guided trail ride.*
<br />
<br />
Reservations are appreciated
</p>
For Information on camping, please see our 
<a href="camping.php"><b>Camping Page</b></a>
');
$view = new GalleryView();
$view->setTag('trails');

$center->add($left);
$center->add($right);
$left->add($trail);
$right->add($view);

//$center->add($trail);

//$center->add($welcome);

$page->process();
$page->scriptUnload('menu.js');
$page->show();

//$page->awesomeDebug();

?>
