<?php
include('global.php');
include('pagesetup.php');

$calendar = $_SESSION['site']->getApp('peCalendar');

if($calendar == null){
	$calendar = new Calendar('','upcoming','list');
	$calendar->setRefPage('calendar.php');
	$calendar->setCustomWhere("AND `type`='class' AND `title` like '%PE 129%'");
	$_SESSION['site']->addApp('peCalendar',$calendar);
}


$page = clone $_SESSION['site'];

$page->setTitle('PE 129 -- Horseback Riding - Southern Tier Stables');
$page->setTitle('Girl Scouts Program');
$page->meta('keywords','Southern Tier Stables, Donna Minnoe, Mark Minnoe, Makin\' Memories, Therapeutic Riding, Therapeutic Riding Center, Moravia NY, physical, mental, social, disabilities, children, adults, Equine-assisted Therapies, Physical, Psychological, social, educational advancement, challenging experience, safe environment, PATH, Professional Association of Therapeutic Horsemanship International, certified instructor, horsemanship, classes, horseback riding, mounted, unmounted, horse, western saddle, english saddle, halter, lead, groom, horse behavior, horse mannerisms, endorphine releases, leadership, control, fitting tack and equipment, wells college, pe 129, independent course, physical education, general education');
$page->meta('description','This course explores the sport of horseback riding, mounted and unmounted. Beginner students will learn to control a horse at a walk and trot. Experienced students can explore pattern work and transitions.');


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



$pe129 = new Text('','<b>PE 129 -- Horseback Riding</b>
<p>
<b>Instructors: Mark Minnoe, Donna Minnoe</b>
 <p>
This course explores the sport of horseback riding, mounted and unmounted. Beginner students will learn to control a horse at a walk and trot. Experienced students can explore pattern work and transitions. Additional fee. Graded S/U. May be repeated for credit. Offered every semester. (0.5 semester hour)
Spring 2015 Second Seven-Week Classes begin the week of March 17th.
 </p>
<p>
Please check your course schedule to verify which section you have signed up for:<br />
<ul>
<li>PE 129: Mondays, from 12:30pm - 3:30pm.</li>
<li>PE 129 01: Tuesdays, from 1:30pm - 4:30pm.</li>
</ul>
</p>
 <p>
<b>Curriculum: </b><br />
Besides learning the basics of riding in a Western or English saddle, students will develop horsemanship skills throughout this course. Topics include connecting with the horse, learning how to properly halter, lead, and groom. Students will also learn about horse behavior (including reading horse mannerisms), endorphine releases, leadership and control, and fitting tack/equipment properly. More challenging topics based on the above will be available for more experienced riders.
 </p>
<p>
<b>Attendance and willingness to participate is vital and will determine your grade.</b>
</p>
<p>
A College van will pick up/drop off students from the circle in front of Main Building.<br />
For both classes, you must meet the van an hour prior to class start.<br />
Payment of $155 is due the first day of class either by cash or check made out to Mark Minnoe.
</p>
<p>
If you would like to take this course but the times do not fit in your schedule, it can be taken as an independent course, as long as you can provide your own transportation. You must fill out the Independent Study form and return the completed form to the Registrar\'s Office (this form can be picked up either in the Registrar\'s Office or printed from the Globe off of the Student link, Registration and Advising page, Important Documents portlet).
</p>
<p>  
This is a pass/fail course, worth 0.50 credits, and does count towards your Physical Education general education requirement.
</p>

</p>');


$center->add($left);
$center->add($right);
$left->add($pe129);
$right->add($calendar);

//$center->add($pe129);
//$center->add($welcome);

$page->process();
$page->scriptUnload('menu.js');
$page->show();

//$page->awesomeDebug();

?>
