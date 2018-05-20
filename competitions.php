<?php
include('global.php');
include('pagesetup.php');

$calendar = $_SESSION['site']->getApp('competitionCalendar');

if($calendar == null){
	$calendar = new Calendar('','upcoming','list');
	$calendar->setRefPage('calendar.php');
	$calendar->setCustomWhere("AND `type`='competition'");
	$_SESSION['site']->addApp('competitionCalendar',$calendar);
}


$page = clone $_SESSION['site'];

$page->setTitle('Competitions - Southern Tier Stables');

$page->setStyle('
#events > .subMenu{
display:block;
}
');


$center = $page->findChild('center');
$center->add($calendar);

$page->process();
$page->scriptUnload('menu.js');
$page->show();

//$page->awesomeDebug();

?>
