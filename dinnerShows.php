<?php
include('global.php');
include('pagesetup.php');

$calendar = $_SESSION['site']->getApp('dinnerShowCalendar');

if($calendar == null){
	$calendar = new Calendar('','upcoming','list');
	$calendar->setRefPage('calendar.php');
	$calendar->setCustomWhere("AND `type`='dinnerShow'");
	$_SESSION['site']->addApp('dinnerShowCalendar',$calendar);
}


$page = clone $_SESSION['site'];
$page->script('shows.js','./sts');

$page->setTitle('Dinner Shows - Southern Tier Stables');

$page->setStyle('
#events > .subMenu{
display:block;
}
');

$right=new Panel('right');
$right->setUnique('right');
$left=new Panel('left');
$left->setUnique('left');

$info = new Text('<b>Southern Tier Stables Dinner Shows</b>','<br />
<p>
Hello Folks!<br />
</p>
<p>
We’d like to introduce ourselves, we are Mark and Donna Minnoe from Moravia, NY. Many of you may already know us as we have been entertaining for over 30 years as one of New York\'s finest classic Country Bands. We have performed at many dinner shows which include Ehrhardts in the Pocono’s (PA), the Cortland Country Music Park (Cortland, NY), as well as many other local venues.<br />
</p>

<p>

We’d like to welcome you to a new venue, one that we are offering to all of the bus companies, senior groups, adult facilities and the general public, our very own, Southern Tier Stables! Our facility is located in the beautiful scenic country hills of Moravia, NY where we are offering (4) entertaining, fun-filled dinner shows for the coming year, 2016, all of which are listed following this letter. 
<br />
</p>
<p>

If you like the country, you will love our little slice of heaven as you enjoy a beautiful, relaxing hayride through natures beauty with a visit to our little version of Petticoat Junction. Ladies will greet you with your choice of wine and cheese samples. Then its back to our state of the art equine facility. There you will enjoy a variety of buffets (depending on the show). The horses will be around for your to view, as well as our Farmall tractor display and much, much more! 
<br />
</p>
<p>

After filling up, its time for some great entertainment and music from various entertainers. Come see the unique shows as some may include Donna on her horse entertaining you, a mini horse and cart right through the audience and of course some audience participation fun!! You never can tell what else may be in store!
<br />
<p>

We hope you will come join us for a day in the country!
<br />

Whoop, whoop and Yeehaa!!<br />
Mark & Donna
</p>

</p>
');

$dinnerShows = new GalleryView();
$dinnerShows->setTag('dinnerShows');

//$hayRides = new GalleryView();
//$hayRides->setTag('hayrides');

$center = $page->findChild('center');


$center->add($left);
$center->add($right);

$left->add($info);
$left->add($dinnerShows);
//$left->add($hayRides);
$right->add($calendar);

$page->process();
$page->scriptUnload('menu.js');
$page->show();

//$page->awesomeDebug();

?>
