<?php
include('global.php');
include('pagesetup.php');

$calendar = $_SESSION['site']->getApp('showCalendar');

if($calendar == null){
	$calendar = new Calendar('','upcoming','list');
	$calendar->setRefPage('calendar.php');
	$calendar->setCustomWhere("AND `type`='show'");
	$_SESSION['site']->addApp('showCalendar',$calendar);
}


$page = clone $_SESSION['site'];
$page->script('shows.js','./sts');

$page->setTitle('Shows - Southern Tier Stables');

$page->setStyle('
#events > .subMenu{
display:block;
}
');

$right=new Panel('right');
$right->setUnique('right');
$left=new Panel('left');
$left->setUnique('left');

$info = new Text('<b>Game Show Information</b>','<br />
<div class="hiddenInfo">
<p>

<b><u>Winter Game Show Series 2017</u></b> <a href="" class="expand">+</a>



<div class="hidden">
		<ul>
		<li>January 28th</li>
		<li>February 25th</li>
		<li>March 25th</li>
		<li>April 29th</li>
		</ul>
	
	Registration Opens at 9:00am<br />
	<b>Show Begins at 11:00am</b><br />
	Current Coggins and Rabies Required<br />
	<b>Helmet required for all riders 18 years old and under</b><br />
	Ride All day for <b>$25.00</b> in chosen division<br />
	Rental of STS horse: <b>$25.00</b> for all day (may have to share horse with other riders)<br />
	*All Division Open Dash for Cash- <b>$10.00</b> Additional Entry Fee, 100% Payback to the winner!*<br />
<br />
<b>Classes Offered:</b>
		<ul>
		<li><b>Leadline:</b> 8 years old and under</li>
		<li><b>Walk/Jog:</b> 12 years old and under</li>
		<li><b>Beginner:</b> 13 years old and older (w/t can cantor home only)</li>
		<li><b>Youth:</b> 13 years and under (w/t/c)</li>
		<li><b>Junior:</b> 14-18 years old (w/t/c)</li>
		<li><b>Senior:</b> 19 years old and older (w/t/c)</li>
		<li><b>PATH:</b> Any Age (w/t)</li>
		</ul>	
	Strip ribbons awarded out to Sixth place for each Division<br />
	<b>Points will be accumulated to determine winners for each division at the end of the Series.</b><br />
	<b>Points will be posted at STS for public access.</b><br />
<br />
Please Print and Fill out the available <a href="2017 Winter Games Series.pdf">Form</a>

</div>
</div>
<div class="hiddenInfo">
<p>
<b><u>Finger Lakes Equestrians Show Series 2017</u></b> <a href="" class="expand">+</a>
<div class="hidden">
		<ul>
		<li>May 20th</li>
		<li>June 24th</li>
		<li>July 22nd</li>
		<li>August 19th</li>
		<li>September 16th</li>
		</ul>
	Show Begins at 9:00am<br />
<br />



<b>Classes Offered:</b>
		<ul>
		<li><b>Leadline:</b> 8 years old and under</li>
		<li><b>Walk/Jog:</b> 12 years old and under</li>
		<li><b>Beginner:</b> 13 years old and older (w/t can cantor home only)<br />
(Beginner Riders are those that have no Loped or Cantered in a Horse Show)</li>
		<li><b>Junior:</b> 14-18 years old (w/t/c)</li>
		<li><b>Senior:</b> 19 years old and older (w/t/c)</li>
		<li><b>Green Horse:</b> 16 years and older (w/t)-No cross entering with green horse</li>
		</ul>

<div class="hiddenInfo">
<b>Rider and Family Conduct: </b> <a href="" class="expand">+</a><br />

<div class="hidden">
<ul>
	<li>ASTM Helmets are REQUIRED for ALL riders whenever mounted on an equine. New York State Law states that anyone under the age of 18 years of age must have a helmet whenever mounted on an equine. Finger Lakes Equestrians rule is as follows: If you are mounted at an FLE Event, regardless of age, you are required to have a helmet on your head! Those without will be asked to leave and will not receive any points that may have been accumulated that day. NO BICYCLE HELMETS ARE ALLOWED.</li>
	<li>Proper attire required. Must ride in English or Western boot. No work boots allowed while showing. No tank tops. We do allow riding in jeans but MUST have proper boot. All shirts must be tucked in.</li>
	<li>As volunteers we are here to help you and your children. If there is any unsportsmanlike behavior or disrespect to people or animals, you will be asked to leave the show grounds. Horses must be in proper attire for the class. No artificial aids will be allowed.</li>
	<li>NO hitting a horse in front of the cinch. This will be an automatic disqualification from the class. </li>

</ul>
</div>
</div>
<div class="hiddenInfo">
<b>Rules:</b> <a href="" class="expand">+</a><br />
<div class="hidden">
<ul>
	<li>All shows shall be governed by the Finger Lake Equestrians rules and regulations, using the current AQHA and 4-H rule books as guidelines. The judges decision will be final!</li>
	<li>We reserve the right to combine or split classes as we determine it is needed.</li>
	<li>A current negative coggins test and proof of rabies vaccination are REQUIRED at time of arrival to Southern Tier Stables.
Coggins run for 2 Calendar years. Rabies certificates must be at least 2 weeks prior to show date.</li>
	<li>Class entries must be made at least 2 classes in advance. Any exhibitor entering the show ring that has not been registered will be dismissed from the class.</li>
	<li>No refunds of entry fees.</li>
	<li>One horse may be used for more than one division by different riders.</li>
	<li>NO stallions will be allowed to be shown by anyone under 18 years of age. No one under 16 Years of age may show a green horse.</li>
	<li>NO alcoholic beverages are allowed on the premises.</li>
	<li>Dogs shall only be allowed on premises with a current rabies tag and on a leash at all times.</li>
	<li>Gymkhana classes will have 5 second penalty on a knockdown of a barrel or pole. Off course will be disqualification.</li>

</ul>
</div>
</div>
<div class="hiddenInfo">
<b>Points: </b><a href="" class="expand">+</a><br />
<div class="hidden">
<ul>
		<li>To accumulate year end points you must be a member of the Finger Lakes Equestrians. You can join the membership at any show and your points will accumulate from that day forward</li>
		<li>Please note Riders cannot hold points in two separate divisions unless riding a green horse in a green horse division an a additional division on non green horse</li>
		<li>If you choose to ride different divisions between Pleasure and Games you will be asked to pick one for year end awards.</li>
</ul>
</div>
</div>
<div class="hiddenInfo">
<b>Changes for 2016: </b><a href="" class="expand">+</a><br />
<div class="hidden">
<ul>
		<li>Pleasure and Games will be run the same day. Pleasure in the am and games in the pm.</li>
		<li>Class Divisions will be Leadline, Walk/Jog, Beginner, Junior, Senior and Green Horse. See below for more info on divisions.</li>
		<li>The Green Horse Division was added. You must be 16 years old or older. It is a walk/trot only and there is no cross entering with a green horse. You can however ride a seasoned horse in another division and hold points in both.</li>
		<li>There will be a 5 second penalty for knocking a barrel or pole over. Off course will be a disqualification.</li>
</ul>
</div>
</div>
<div class="hiddenInfo">
<b>Game Show Patterns and Rules: </b><a href="" class="expand">+</a><br />
<div class="hidden">
<ul>
<li><b>Pole Bending: </b>A timed event in which contestants must weave in and out a line of poles.</li>
<li><b>Cloverleaf: </b>The three-barrel pattern that barrel racers run; the path around the barrels resembles a cloverleaf.</li>
<li><b>Keyhole: </b>Straight up to barrels, take horse inside keyhole (horse must have butt past barrels), turn around left or right and take horse back outside. Then straight back home.</li>
<li><b>Bleeding Heart: </b>Pattern maybe run from the left or from the right. Must start on the rail and weave 3 barrels.</li>
<li><b>Coke Bottle Race: </b>Straight up to barrel, take horse to outside of barrel, grab coke bottle without knocking over barrel. Then straight back home.
</li>
</ul>

See Finger Lakes Equestrians 2016 <a href="2016 Revised Show Book.pdf">Show Book</a> for more information.
</div>
</div>
</div>
</p>
</div>
');



$center = $page->findChild('center');
$center->add($left);
$center->add($right);

$left->add($info);
$right->add($calendar);

$page->process();
$page->scriptUnload('menu.js');
$page->show();

//$page->awesomeDebug();

?>
