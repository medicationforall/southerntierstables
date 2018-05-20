<?php
include('global.php');
include('pagesetup.php');

$calendar = $_SESSION['site']->getApp('schoolCalendar');

if($calendar == null){
	$calendar = new Calendar('','upcoming','list');
	$calendar->setRefPage('calendar.php');
	$calendar->setCustomWhere("AND `type`='class' AND `title` like '%School Program%'");
	$_SESSION['site']->addApp('schoolCalendar',$calendar);
}


$page = clone $_SESSION['site'];

$page->setTitle('School Programs');
$page->meta('keywords','Southern Tier Stables, Donna Minnoe, Mark Minnoe, Makin\' Memories, Therapeutic Riding, Therapeutic Riding Center, Moravia NY, physical, mental, social, disabilities, adults, children, Equine-assisted Therapies, Physical, Psychological, social, educational advancement, challenging experience, safe environment, PATH, Professional Association of Therapeutic Horsemanship International, certified instructor, school programs, after school program, children with disabilities, fully insured, group rate');
$page->meta('description','Through our Makin\' Memories School Programs we strive to build programs designed for your students and their needs. ');

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



$school = new Text('','<b>Makin\' Memories School Programs</b>
<p>

Through our Makin\' Memories School Programs we strive to build programs designed for your students and their needs. 
<br />
For more information on our <a href="memories.php">Makin\' Memories Program.</a><br />

</p>
<p>
Why you should chose our program:
	<ul>
	<li>Programs are custom designed for each student and class needs</li>
	<li>PATH Certified Instructor</li>
	<li>Fully Insured Facility</li>
	<!--<li>We have experience with many different disabilities such as:
		<ul>
		<li>Cerebral Palsy</li>
		<li>Autism</li>
		<li>MR</li>
		<li>Brain Trauma</li>
		<li>Paraplegic</li>
		<li>Downs Syndrome</li>
		<li>Infancy Syndrome</li>
		<li>Learning Disabilities</li>
		<li>Social Disabilities</li>
		<li>Emotional Disabilities</li>
		</ul>
	</li>
	-->
	</ul>
</p>
<p>
Availability:
<ul>
<li>Monday through Friday</li>
<li>Times: 9 am - 3 pm</li>
<li>Cost: Group Rate Available</li>
<li><b>If your school is not a participant in our School Program, please contact your Principle, Superintendent, or Special Education Department Director to tell them of your interest </b></li>
</ul>
</p>
<br />
<p>
<b>Weedsport School Program</b> 
<ul>
<li>Once a Month</li>
<li>Typically on Fridays</li>
<li>Times: 10:30 am - 1 pm</li>
<li>Contact Weedsport Special Education Director for more information</li>
</ul>
</p>

<p>
<b><u>This programs is offered by Southern Tier Stables and Makin\' Memories Therapeutic Riding Center</u></b><br />
</p>');


$center->add($left);
$center->add($right);
$left->add($school);
$right->add($calendar);

//$center->add($pe129);
//$center->add($welcome);

$page->process();
$page->scriptUnload('menu.js');
$page->show();

//$page->awesomeDebug();

?>
