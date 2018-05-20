<?php
include('global.php');
include('pagesetup.php');


$page = clone $_SESSION['site'];

$page->setTitle('Riding Lessons - Southern Tier Stables');
$page->meta('keywords','Southern Tier Stables, Moravia NY, children, adults,challenging experience, safe environment, horsemanship, rider experience, horse, maintenance, general care, horse behavior, groundwork, riding techniques, lesson horses, trailer-ins welcome, english, western, gymkhana events, trail riding');
$page->meta('description','Our programs are designed around rider experience and knowledge desires. Topics often include groundwork and horsemanship, horse behavior, general care and maintenance, and of course, proper riding techniques. We have several lesson horses available and trailer-ins are welcome.');

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



$lessons = new Text('','<b>Riding Lessons</b>
<p>
Our programs are designed around rider experience and knowledge desires. Topics often include groundwork and horsemanship, horse behavior, general care and maintenance, and of course, proper riding techniques. We have several lesson horses available and trailer-ins are welcome.
</p>
<p>
<b>Rates: $40-50 per lesson</b><br />
Prices depend on time, availablity, curriculum, and student desires<br />
Most lessons are based on an hourly basis. <br /><br />
<b>Types:</b>
<ul>
<li>English</li>
<li>Western</li>
<li>Gymkhana Events</li>
<li>Trial Riding</li>
</ul>
For information on our <a href="makinmemories.php">Therapeutic Riding Center</a>.
</p>');

$view = new GalleryView();
$view->setTag('riding');

$center->add($left);
$center->add($right);
$left->add($lessons);
$right->add($view);

//$center->add($lessons);
//$center->add($welcome);

$page->process();
$page->scriptUnload('menu.js');
$page->show();

//$page->awesomeDebug();

?>
