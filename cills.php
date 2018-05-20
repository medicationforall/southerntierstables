<?php
include('global.php');
include('pagesetup.php');

$calendar = $_SESSION['site']->getApp('cillsCalendar');

if($calendar == null){
	$calendar = new Calendar('','upcoming','list');
	$calendar->setRefPage('calendar.php');
	$calendar->setCustomWhere("AND `type`='class' AND `title` like '%CILLS%'");
	$_SESSION['site']->addApp('cillsCalendar',$calendar);
}


$page = clone $_SESSION['site'];

$page->setTitle('CILLS Program');
$page->meta('keywords','Southern Tier Stables, Donna Minnoe, Mark Minnoe, Makin\' Memories, Therapeutic Riding, Therapeutic Riding Center, Moravia NY, physical, mental, social, disabilities, adults, Equine-assisted Therapies, Physical, Psychological, social, educational advancement, challenging experience, safe environment, PATH, Professional Association of Therapeutic Horsemanship International, certified instructor, horsemanship, cayuga county, classes, Barb Gregg, May Session, June Session, June/July Session, July/August Session, August/Sept Session, Sept/October Session, 4 week horsemanship program, cills, cills program');
$page->meta('description','(Cayuga Institute for Living & Learning Skills) The CILLS Program is specifically designed for Adults, 18 and older, within Cayuga County, Cayuga Community College for Persons with Disabilities.');

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



$cills = new Text('','<b>CILLS Program</b>
<p>
<b>(Cayuga Institute for Living & Learning Skills)</b><br />
The CILLS Program is specifically designed for Adults, 18 and older, within Cayuga County, Cayuga Community College for Persons with Disabilities.
<ul>
<li>4 Week Horsemanship Program</li>
<li>Classes are held on Monday</li>
<li>Time: 6pm to 8pm</li>
<li>Price: $30 per lesson </li>
<li>Class Sessions start every four weeks during Summer and Fall</li>
<ul>
<li>May Session Classes begin <b>May 4th</b></li>
<li>June Session Classes begin <b>June 1st</b></li>
<li>June/July Session Classes begin <b>June 29th</b></li>
<li>July/August Session Classes begin <b>July 27th</b></li>
<li>August/Sept Session Classes begin <b>August 31st</b></li>
<li>Sept/October Session Classes begin <b>September 28th</b></li>
</ul>
<li><b>Contact:
<br /> Barb Gregg, Project Manager <br />
To inquire about joining this program<br />
Call: (315) 294-8841<br />
Email: Greggb@cayuga-cc.edu</b>
</ul>
<p>
<br />
<b><u>This program is currently offered through Makin\' Memories Therapeutic Riding Center</u></b><br />
These programs are not organized by Makin\' Memories and require you to contact the specific Programs Manager to get further information.<br />
</p>

</p>');


$center->add($left);
$center->add($right);
$left->add($cills);
$right->add($calendar);

//$center->add($pe129);
//$center->add($welcome);

$page->process();
$page->scriptUnload('menu.js');
$page->show();

//$page->awesomeDebug();

?>
