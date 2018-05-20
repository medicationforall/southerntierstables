<?php
include('global.php');
include('pagesetup.php');


$page = clone $_SESSION['site'];

$page->setTitle('Policies And Forms - Southern Tier Stables');
$page->meta('keywords','Southern Tier Stables, Moravia NY, Moravia, news, southern tier stables, makin\' memories, forms, policies, policy, hold harmless, hold harmless agreement');
$page->meta('description','Our policies here at Southern Tier Stables. Vaccination requirements, Attire, behavior and pets policy. Forms, our hold harmless agreement that every rider must sign');

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


$policies = new Text('','<b>Policies</b>
<p>
<p>
<ul>
<li>A reminder for any horse you bring to Southern Tier Stables: </li>
<ul>
		<li>You must have proof of a rabies vaccination certified at least two (2) weeks prior to the event date. </li>
		<li>A negative Coggins run for two (2) calendar years is also required.</li>
		<li>Before riding at our facility (including trail riding), you must sign a Hold Harmless Agreement (see below).</li>
</ul>
</p>
<p>
<li>Attire -- Southern Tier Stables requests appropriate attire for all riders. This includes: </li>
<ul>
	<li>An ASTM / SEI certified equestrian helmet -- this is MANDATORY for all riders under 18</li>
	<li>A riding boot with a heel height of one inch or greater</li>
	<li>Garments should not pose potential safety hazards by being ill-fitting or with excess fabric to catch in equipment</li>
</ul>
</p>
<p>
<li>Behavior</li>
<ul>
	<li>Roughhousing will not be tolerated</li>
	<li>Children are required to have adult supervision</li>
	<li>No excessive rough handling or mistreating of horses</li>
	<li>Be mindful of your horse in relation to other horses and people</li>
	<li>Leave your area as clean or cleaner than how you found it - <br />
		There are manure forks, muck buckets, brooms and shovels located throughout the barn</li>
	<li>Please do not approach a horse with the intent to pet, touch equipment, and/or feed treats without the rider or owner\'s permission</li>
</ul>
<li>Pets</li>
<ul>
<li>Pets must be leashed at all times.</li>
</ul>
</ul>
</p>
</p>
<b>Forms</b>
<p>
<ul>
<li>Hold Harmless Agreement</li>
<ul>
	<li>In preparation for your visit to Southern Tier Stables, please download, print, and fill out, a copy of our Hold Harmless Agreement. </li>
	<li>We require all riders to sign this agreement. If you forget yours at home, copies will be provided at all events.</li>
</ul>
</ul>
</p>
<p>
<a href="holdHarmlessAgreement.pdf" target="_blank">Click Here to Download the Hold Harmless Agreement.</a>
</p>
<p>
<b>We reserve the right to change policies and forms at any time.</b>
</p>');


$center->add($left);
$center->add($right);
$left->add($policies);
//$center->add($policies);

//$center->add($welcome);

$page->process();
$page->scriptUnload('menu.js');
$page->show();

//$page->awesomeDebug();

?>
