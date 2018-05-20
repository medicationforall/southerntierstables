<?php
include('global.php');
include('pagesetup.php');


$calendar = $_SESSION['site']->getApp('classCalendar');

if($calendar == null){
	$calendar = new Calendar('','upcoming','list');
	$calendar->setRefPage('calendar.php');
	$calendar->setCustomWhere("AND `type`='class'");
	$_SESSION['site']->addApp('classCalendar',$calendar);
}

$page = clone $_SESSION['site'];

$page->setStyle('
#programs > .subMenu{
display:block;
}
');



$page->setTitle('Programs - Southern Tier Stables');
$page->meta('keywords','Southern Tier Stables, Donna Minnoe, Mark Minnoe, Makin\' Memories, Therapeutic Riding, Therapeutic Riding Center, Moravia NY, physical, mental, social, disabilities, adults, children, Equine-assisted Therapies, Physical, Psychological, social, educational advancement, challenging experience, safe environment, PATH, Professional Association of Therapeutic Horsemanship International, certified instructor, Freedom Riders, CILLS, Hillside, Girl Scouts, volunteer, donate, Annual 5k Trail Mud Run, purchase goal, independence saddle, expanding therapeutic riding program, school programs, after school program, summer camp, summer day camp, summer horse camp, horse camp, cills program, cayuga community college, adults with disabilities, freedom riders, freedom recreational services, hillside program, children with disabilities, girl scouts horsemanship, girl scouts, pe 129, wells college pe 129');
$page->meta('description','We have a variety of Programs offered by and through the Riding Center. We are always working toward expanding our programs and class offerings. During and After School Programs, Summer Camp, CILLS Program, Freedom Riders, Hillside, PE 129, Girl Scouts Horsemanship.If you\'d like to partner with the Makin\' Memories Therapeutic Riding Center to create a program or class, please contact us using our Contact Form.');


$center = $page->findChild('center');
$right=new Panel('right');
$right->setUnique('right'); 

$left=new Panel('left');
$left->setUnique('left');



$programs = new Text('','<b>Programs</b>
<p>
We have a variety of Programs offered by and through the Riding Center. We are always working toward expanding our programs and class offerings. If you\'d like to partner with the Makin\' Memories Therapeutic Riding Center to create a program or class, please contact us using our <a href="contact.php">Contact Form.</a>
</p>
<p>
<b><u>Programs Offered by Southern Tier Stables and Makin\' Memories Therapeutic Riding Center</u></b><br />
We are currently offering open enrollment in the following Programs:<br />
</p>
<p>
<ul>

<li><a href="schoolPrograms.php"><b>Makin\' Memories School Programs</b></a><br /> Through our Makin\' Memories School Programs we strive to build programs designed for your students and their needs. </li>
<li><a href="summer.php"><b>Summer Day Camp</b></a><br />Our Summer Day Camp for Children, Open for all age levels. Topics include horsemanship, games on horseback, horse-related crafts plus opportunities for nature hikes and fishing.</li>

</ul>
</p>
<p>
<br />
<b><u>Programs currently offered through Makin\' Memories Therapeutic Riding Center</u></b><br />
These programs are not organized through Makin\' Memories and require you to contact the specific Programs Manager to get further information.<br />
</p>
<p>
<ul>
<li><a href="cills.php"><b>CILLS Program</b></a><br /> The CILLS Program is specifically designed for Adults, 18 and older, within Cayuga County, Cayuga Community College for Persons with Disabilities.</li>
<li><a href="riders.php"><b>Freedom Riders</b></a><br /> The Freedom Riders Program is designed for children under the age of 18, in Cayuga County, with Mental and Physical Disabilities.</li>
<li><a href="hillside.php"><b>Hillside Program</b></a><br /> The Hillside Program is designed for children with severe emotional challenges.</li>
<li><a href="scouts.php"><b>Girl Scouts Horsemanship</b></a><br /> We are an approved horseback riding facility for the Girl Scouts of NYPENN Pathways. We offer a facility for Girl Scouts to complete their horsemanship badges.</li>
<li><a href="pe129.php"><b>PE 129</b></a><br />Program offered by Wells College, for all students of any level. Courses are designed around rider experience. </li>
</ul>
</p>
<p>



</p>');


$center->add($left);
$center->add($right);
$left->add($programs);
$right->add($calendar);

//$center->add($welcome);

$page->process();
$page->scriptUnload('menu.js');
$page->show();

//$page->awesomeDebug();

?>
