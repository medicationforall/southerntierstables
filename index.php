<?php
include('global.php');
include('pagesetup.php');

$calendar = $_SESSION['site']->getApp('indexCalendar');

if($calendar == null){
	$calendar = new Calendar('','currentweek','list');
	//$calendar->setEventClass('CmpEvent');
	$calendar->setRefPage('calendar.php');
	//$calendar->setCustomWhere("AND `type`<>'feature'");
	$_SESSION['site']->addApp('indexCalendar',$calendar);
}


$index = clone $_SESSION['site'];

$index->setTitle('Southern Tier Stables');
$index->meta('keywords','Southern Tier Stables, Donna Minnoe, Mark Minnoe, Makin\' Memories, Therapeutic Riding, Therapeutic Riding Center, Moravia NY, physical, mental, social, disabilities, adults, children, Mud Run 2, Annual Mud Run, Game Shows, Barn Dances, Poker Runs, Trail Trials, Game Nights, Family Farm Days, Summer Horse Day Camp, Horse Camp, Summer Camp, Mystic Mountain Haunted Hayride');
$index->meta('description','Southern Tier Stables and Makin\' Memories Therapeutic Riding Center. Nestled into the gently rolling hills surrounding Moravia, NY. Southern Tier Stables offers many trails and events for people of all ages. Makin\' Memories Therapeutic Riding Center offers Equestrian Therapies for Children and Adults with physical, mental, and social disabilities as well as veterans.');


$index->setStyle
(
'
	div.news .cContent
	{
		height:150px;
		overflow:auto;
		border:1px solid #000;
	}
'
);



$center = $index->findChild('center');

$right=new Panel('right');
$right->setUnique('right'); 

$left=new Panel('left');
$left->setUnique('left');

$welcome = new Text('','<b>Welcome to the Southern Tier Stables and our Makin\' Memories Therapeutic Riding Center.</b> <br/>
Nestled into the gently rolling hills surrounding Moravia, N.Y.<br />
<p>

<b>Our Makin\' Memories Therapeutic Riding Center </b>offers programs, classes and private lessons for those with physical, mental, and social disabilities.<br />
<a href="makinmemories.php">For more information on Our Makin\' Memories Therapeutic Riding Center.</a><br />


<!--
<b>**As of December 18th, 2015, we now have a SureHands Lift!**</b> We\'d like to thank the Moravia Rotary Club, Everyone who attended our Mud Run 2 and the Benefit at Cortland Country Music Park! Check out the article written in <a href="http://auburnpub.com/news/local/moravia-therapeutic-horse-riding-stable-receives-mechanical-lift-to-get/article_9eb4d44f-5c9d-5776-864d-42383e194e21.html" target="_blank">The Citizen!</a>
</p>
-->




<p>
Check out our <a href="https://www.facebook.com/SouthernTierStables" target="_blank"><b>Facebook</b></a> page & our <a href="events.php"><b> Calendar</b></a> for a list of upcoming events. 
</p>
<p>

<table>
<b>We also host many events throughout the year:</b>




<tr><td><a href="dinnerShows.php">Dinner Shows</a></td><td><a href="summer.php">Kid\'s Summer Horse Day Camp</a></td></tr>
<tr><td><a href="shows.php">Game Shows</a></td><td>Family Farm Days</td></tr>
<tr><td>Barn Dances</td><td><a href="scouts.php">Girl Scout Events</a></td></tr>
<!--
<tr><td><a href="mudrun.php">Annual Mud Run</a></td><td>Mystic Mountain Haunted Hayride</td></tr>
-->
<tr><td>Annual Mud Run</td><td>Mystic Mountain Haunted Hayride</td></tr>
<tr><td>
<a href="competitions.php">Competitions</a>
<ul>
		<li><a href="competitions.php">Poker Runs</a></li>
		<li><a href="competitions.php">Trail Trials</a></li>
		<li><a href="competitions.php">Game Nights</a></li>
</ul>
</td><td><a href="programs.php">Specialty Programs</a>
<ul>
<li><a href="cills.php">CILLS Program</a></li>
<li><a href="hillside.php">Hillside Program</a></li>
<li><a href="riders.php">Freedom Riders Program</li>
</ul>
</td></tr>
<tr><td><a href="programs.php">School Programs</a>
<ul>
<li><a href="schoolPrograms.php">After School Programs</a></li>
<li><a href="PE 129.php">Wells College: PE 129 </a</li>

</ul>
</td></tr></td><td></td></tr>

</table>




		For more information on the Winter Game Show Series and Finger Lakes Equestrian Game Show Series see the <a href="shows.php">Show Page</a> within Events.
</p>

</p>');

$pictures = new Text('<a href="http://www.dougstone.com/index.html" target="_blank">Doug Stone & The Stone Age Band, July 23rd</a>','<a href="dougstone.png" target="_blank"> <img src="dougstone.png" alt="Makin\' Memories" style="width:100%;></a> ');




$center->add($left);
$center->add($right);
//$left->add($pictures);
$left->add($welcome);
$right->add($calendar);

$index->process();
$index->scriptUnload('menu.js');
$index->show();


?>
