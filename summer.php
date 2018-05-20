<?php
include('global.php');
include('pagesetup.php');

$calendar = $_SESSION['site']->getApp('summerCalendar');

if($calendar == null){
	$calendar = new Calendar('','upcoming','list');
	$calendar->setRefPage('calendar.php');
	$calendar->setCustomWhere("AND `type`='class' AND `title` like '%Summer Day Camp%'");
	$_SESSION['site']->addApp('summerCalendar',$calendar);
}


$page = clone $_SESSION['site'];

$page->setTitle('Summer Day Camp');
$page->meta('keywords','Southern Tier Stables, Donna Minnoe, Mark Minnoe, Makin\' Memories, Therapeutic Riding, Therapeutic Riding Center, Moravia NY, physical, mental, social, disabilities, adults, children, Equine-assisted Therapies, Physical, Psychological, social, educational advancement, challenging experience, safe environment, PATH, Professional Association of Therapeutic Horsemanship International, certified instructor, summer camp, summer day camp, summer day camp for children, day camp, summer activities');
$page->meta('description','Our Summer Day Camp for Children, Open for all age levels. Topics include horsemanship, games on horseback, horse-related crafts plus opportunities for nature hikes and fishing.');


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



$summer = new Text('','<b>Summer Day Camp</b>
<p>
Our Summer Day Camp for Children, Open for all age levels. Topics include horsemanship, games on horseback, horse-related crafts plus opportunities for nature hikes and fishing.
<ul>
<li>One Summer Session Available</li>
<ul>
<li>July 17-21</li>
</ul>
<li>Time: 9 am - 3 pm</li>
<li>Price: $250 per child, includes snacks & beverages</li>
<ul>
<li>Please Pack a Bagged Lunch</li>
</ul>
<li>The last day of Camp we hold a small horse show for the kids. <br />
Family are welcome to come join in the fun.<br />
Lunch included: Hot dog, chips, and a drink</li>
</ul>
<br />
<b><u>This programs is offered by Southern Tier Stables and Makin\' Memories Therapeutic Riding Center</u></b><br />
</p>');


$center->add($left);
$center->add($right);
$left->add($summer);
$right->add($calendar);

//$center->add($pe129);
//$center->add($welcome);

$page->process();
$page->scriptUnload('menu.js');
$page->show();

//$page->awesomeDebug();

?>
