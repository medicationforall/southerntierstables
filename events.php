<?php
include('global.php');
include('pagesetup.php');

//create the calendar outside of the cloned page.
$calendar = $_SESSION['site']->getApp('calendar');


$page = clone $_SESSION['site'];
$page->setTitle('Calendar - Southern Tier Stables');
$page->meta('keywords','Southern Tier Stables, Moravia NY, Moravia, cills program, cills, hillside program, hillside, freedom riders program, freedom riders, thursday game night, game night, game shows, equestrian show series, poker run, trail trials, barn dances');
$page->meta('description','We host a number of events at Southern Tier Stables and Makin\' Memories Therapeutic Riding Center. From our Makin\' Memories Programs to our stables services, as well as Trail trials, Thursday Game Night, Poker Runs, Game Shows, and our Annual Mud Run.');

$page->setStyle('
#events > .subMenu{
display:block;
}
');

//manage subnav for calendar
$subNav= new Panel('subNav');
$subNav->setUnique('subNav');

$bar = $page->findChild('bar');

if(!empty($bar)){
$bar->add($subNav);
}

$calendarNav = new CalendarNav('');
$subNav->add($calendarNav);
//end manage subnav



$center = $page->findChild('center');
$center->add($calendar);


//$center->add($welcome);

$page->process();
$page->scriptUnload('menu.js');
$page->show();

//$page->awesomeDebug();

?>
