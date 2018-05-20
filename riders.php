<?php
include('global.php');
include('pagesetup.php');

$calendar = $_SESSION['site']->getApp('freeCalendar');

if($calendar == null){
	$calendar = new Calendar('','upcoming','list');
	$calendar->setRefPage('calendar.php');
	$calendar->setCustomWhere("AND `type`='class' AND `title` like '%Freedom Riders%'");
	$_SESSION['site']->addApp('freeCalendar',$calendar);
}


$page = clone $_SESSION['site'];

$page->setTitle('Freedom Riders Program');
$page->meta('keywords','Southern Tier Stables, Donna Minnoe, Mark Minnoe, Makin\' Memories, Therapeutic Riding, Therapeutic Riding Center, Moravia NY, physical, mental, social, disabilities, children, Equine-assisted Therapies, Physical, Psychological, social, educational advancement, challenging experience, safe environment, PATH, Professional Association of Therapeutic Horsemanship International, certified instructor, horsemanship, cayuga county, classes, May/June Session, October/November Session, 6 week horsemanship program, human services, freedom recreational services, freedom riders');
$page->meta('description','The Freedom Riders Program is a 6 week long program offered through Freedom Recreational Services. Freedom Riders Program is designed for children under the age of 18, in Cayuga County, with Mental and Physical Disabilities. Freedom Recreational Services also offer other programs such as, Freedom Camp and Sibshops.');


$page->setStyle('
#programs > .subMenu{
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



$freedom = new Text('','<b> Freedom Riders</b><br />

<p>
<b>(Freedom Recreational Services)</b><br />
The Freedom Riders Program is a 6 week long program offered through Freedom Recreational Services. Freedom Riders Program is designed for children under the age of 18, in Cayuga County, with Mental and Physical Disabilities. Freedom Recreational Services also offer other programs such as, Freedom Camp and Sibshops. 
<ul>
<li>6 Week Long Horsemanship Program</li>
<li> Spring and Fall Sessions</li>
<ul>
<li>May - June</li>
<li>October - November</li>
</ul>
<li><b>Classes begin April 30th</b></li>
<li>Classes are held on Thursday and Friday</li>
<li>Time: 6pm to 8pm</li>
<li><b>Contact:
<br /> Josephine Emilio, Executive Director <br />
To inquire about joining this program<br />
Call: (315) 253-5465<br />
Or Visit:<br /></b>
<ul>
<li>United Way of Cayuga County<br />
<a href="http://www.unitedwayofcayugacounty.org" target="_blank">www.unitedwayofcayugacounty.org</a></li>

<li>Human Services Coalition of Cayuga County<br />
<a href="http://www.human-services.org" target="_blank">www.human-services.org</a><br />
Freedom Recreation </li>

<li>Freedom Recreational Services<br />
P.O Box 2134<br />
Auburn, NY 13021</li>
</ul>
</ul>
<br />
<b><u>This program is currently offered through Makin\' Memories Therapeutic Riding Center</u></b><br />
These programs are not organized by Makin\' Memories and require you to contact the specific Programs Manager to get further information.<br />
</p>
</p>');


$view = new GalleryView();
$view->setTag('freedomRiders');

$center->add($left);
$center->add($right);
$left->add($freedom);
$left->add($view);
$right->add($calendar);

//$center->add($pe129);
//$center->add($welcome);

$page->process();
$page->scriptUnload('menu.js');
$page->show();

//$page->awesomeDebug();

?>
