<?php
include('global.php');
include('pagesetup.php');

$calendar = $_SESSION['site']->getApp('scoutsCalendar');

if($calendar == null){
	$calendar = new Calendar('','upcoming','list');
	$calendar->setRefPage('calendar.php');
	$calendar->setCustomWhere("AND `type`='class' AND `title` like '%scouts%'");
	$_SESSION['site']->addApp('scoutsCalendar',$calendar);
}


$page = clone $_SESSION['site'];

$page->setTitle('Girl Scouts Program');
$page->meta('keywords','Southern Tier Stables, Donna Minnoe, Mark Minnoe, Makin\' Memories, Therapeutic Riding, Therapeutic Riding Center, Moravia NY, physical, mental, social, disabilities, children, Equine-assisted Therapies, Physical, Psychological, social, educational advancement, challenging experience, safe environment, PATH, Professional Association of Therapeutic Horsemanship International, certified instructor, horsemanship, classes, girl scouts, scouts ');
$page->meta('description','Girl Scouts of NYPENN Pathways. We are an approved horseback riding facility for the Girl Scouts of NYPENN Pathways. We offer a facility for Girl Scouts to complete their horsemanship badges.');


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



$cills = new Text('','<b>Girl Scouts of NYPENN Pathways</b><br />
<p>

We are an approved horseback riding facility for the Girl Scouts of NYPENN Pathways. We offer a facility for Girl Scouts to complete their horsemanship badges.
<ul>
<li>Time: Call to schedule an appointment
<br /> Or use our <a href="contact.php" target="_blank">Contact Form</a></li>
<li><b>Contact:
<br /> Your local chapter  <br />
To inquire about joining this program<br />
<a href="https://www.gsnypenn.org/who-we-are/contact-us/Pages/default.aspx" target="_blank">Girl Scouts of NYPENN Pathways Website</a></b>
</ul>


</p>
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
