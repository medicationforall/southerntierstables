<?php
include('global.php');
include('pagesetup.php');


$page = clone $_SESSION['site'];

$page->setTitle('Horse Starting and Training - Southern Tier Stables');
$page->meta('keywords','Southern Tier Stables, Moravia NY, children, adults,challenging experience, safe environment, horsemanship, horse starting and training, starting, training, horse');
$page->meta('description','We offer horse starting and training utilizing Resistance-Free techniques. Boarding is included in price. Training based on 30 day increments and discipline. Specializing in starting 2 year olds.');

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


$starting = new Text('','<b>Horse Starting and Training</b>
<p>
    We offer horse starting and training utilizing Resistance-Free techniques. Boarding is included in price. Training based on 30 day increments and discipline. Specializing in starting 2 year olds. 
</p>
<p>
Due to the availability, discipline, as well as age, pricing is varied. <br />
Please contact us for more information about rates and details about how we can meet your training needs.<br />
</p>
<p>
Call: (315) 496-2609 <br />
Email: contact@southerntierstables.com<br />

Or use our <a href="contact.php">Contact Form</a>
</p>');

$view = new GalleryView();
$view->setTag('starting');

$center->add($left);
$center->add($right);
$left->add($starting);
$right->add($view);

//$center->add($starting);

//$center->add($welcome);

$page->process();
$page->scriptUnload('menu.js');
$page->show();

//$page->awesomeDebug();

?>
