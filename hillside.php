<?php
include('global.php');
include('pagesetup.php');

$calendar = $_SESSION['site']->getApp('hillsideCalendar');

if($calendar == null){
	$calendar = new Calendar('','upcoming','list');
	$calendar->setRefPage('calendar.php');
	$calendar->setCustomWhere("AND `type`='class' AND `title` like '%Hillside%'");
	$_SESSION['site']->addApp('hillsideCalendar',$calendar);
}


$page = clone $_SESSION['site'];

$page->setTitle('Hillside Program');
$page->meta('keywords','Southern Tier Stables, Donna Minnoe, Mark Minnoe, Makin\' Memories, Therapeutic Riding, Therapeutic Riding Center, Moravia NY, physical, mental, social, disabilities, children, Equine-assisted Therapies, Physical, Psychological, social, educational advancement, challenging experience, safe environment, PATH, Professional Association of Therapeutic Horsemanship International, certified instructor, horsemanship, classes, hillside program, children with emotional challenges, emotional challenges, hillside family of agencies, April, May, June, July, August, September, October, Sessions, hillside ');
$page->meta('description','(Hillside Family of Agencies) The Hillside Program is designed for children with severe emotional challenges.');


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



$hillside = new Text('','<b>Hillside Program</b>
<p>
<b>(Hillside Family of Agencies)</b><br />
The Hillside Program is designed for children with severe emotional challenges. 
<ul>

<li>Classes are held on Wednesday</li>
<li>Time: 3:00pm to 4:00pm</li>
<li>Class Sessions start during the Spring and go into the Fall</li>
<ul>

<li>May Session Classes begin <b>May 3rd</b></li>
<li>June Session Classes begin <b>June 7th</b></li>
<li>July Session Classes begin <b>July 5th</b></li>
<li>August Session Classes begin <b>August 2nd</b></li>
<li>September Session Classes begin <b>September 6th</b></li>
<li>October Session Classes begin <b>October 4th</b></li>
</ul>
<li><b>Contact:
<br /> Hillside Family of Agencies <br />
To inquire about joining this program<br />
Call: (585) 256-7500<br />
Email: info@hillside.com<br />
Or Visit:<br /></b>
<ul>
<li>Hillside Family of Agencies<br />
Finger Lakes Campus<br />
<b>(315)258-2100</b> <br />
7432 County House Rd<br />
Auburn 13021<br />
</li>
<li>Hillside Family of Agencies<br />
<a href="http://www.hillside.com" target="_blank">www.hillside.com</a></li>
</ul>
<p>
<br />
<b><u>This program is currently offered through Makin\' Memories Therapeutic Riding Center</u></b><br />
These programs are not organized by Makin\' Memories and require you to contact the specific Programs Manager to get further information.<br />
</p>

</p>');


$center->add($left);
$center->add($right);
$left->add($hillside);
$right->add($calendar);

//$center->add($pe129);
//$center->add($welcome);

$page->process();
$page->scriptUnload('menu.js');
$page->show();

//$page->awesomeDebug();

?>
