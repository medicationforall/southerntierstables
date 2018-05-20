<?php
include('global.php');
include('pagesetup.php');


$page = clone $_SESSION['site'];

$page->setTitle('Makin\' Memories Therapeutic Riding Center - Southern Tier Stables');
$page->meta('keywords','Southern Tier Stables, Donna Minnoe, Mark Minnoe, Makin\' Memories, Therapeutic Riding, Therapeutic Riding Center, Moravia NY, physical, mental, social, disabilities, adults, children, Mud Run 2, Annual Mud Run, Equine-assisted Therapies, Physical, Psychological, social, educational advancement, challenging experience, safe environment, PATH, Professional Association of Therapeutic Horsemanship International, certified instructor, Freedom Riders, CILLS, Hillside, Girl Scouts, volunteer, donate');
$page->meta('description','Makin\' Memories Therapeutic Riding Center offers programs, classes, and private lessons for those with physical, mental, and social disabilities. Our programs and classes offer an environment to which our riders can gain responsibility, independence, self-esteem, teamwork skills, social skills, hand-eye coordination, balance, muscle strength and tone, communication, and horsemanship.');


$page->setStyle('
#memories > .subMenu{
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

$memories = new Text('','<b>Makin\' Memories Therapeutic Riding Center</b><br/><br/>
<p>
<b>What We Do</b><br />
We offer programs, classes, and private lessons for those with physical, mental, and social disabilities. Our programs and classes offer an environment to which our riders can gain responsibility, independence, self-esteem, teamwork skills, social skills, hand-eye coordination, balance, muscle strength and tone, communication, and horsemanship. 
</p>
<p>
Equine-assisted therapies can provide an individual with great physical and emotional rewards. The rhythmic motion of a horse moves the rider\'s body, helping to improve flexibility, balance, and muscle strength. Horses in a therapy setting can help to improve the rider\'s social and emotional well-being through developing a bond with the animal, encouraging interaction (both with the horse and volunteers), and fostering self-esteem.
</p>
<p>
At Makin\' Memories Therapeutic Riding Center, our goal is to provide programs that encourage physical, psychological, social and educational advancement, and to have every rider enjoy their time with us. We strive to make our program fun and enjoyable, and a challenging experience for the rider.
</p>
<p>
Classes are limited to 6 students maximum to ensure a safe environment. Our classes are centered around student abilities and expanding upon those abilities. We have a SureHands Lift at our facility to help with mounting our therapy horses. Our programs and classes are designed and taught by a Professional Association of Therapeutic Horsemanship International (PATH Int.) certified instructor. <br />
<a href="http://www.pathintl.org" target="_blank">For more information about Path Int.</a>

</p>
<p>
We also offer hands-on-only (no riding) type of therapies. 
</p>
<p>
<b>Average Cost of a Lesson is $50.00 per student.</b><br />
Prices varies depending on disability and lesson plan.  

</p>
<p>
<b>*We ask that anyone wanting to undergo our Therapeutic Riding Program receive clearance to do so from medical personnel such as a family doctor or specialist.*</b>
</p>
<p>
<b>History</b><br/>
Our program began in 2009, offering a sponsored therapeutic riding program to the Freedom Riders, Freedom Recreation Services, United Way of Cayuga County, NY. Since then we have continued to expand our programs, we look forward to continuing to offer programs for all people with disablities. We also have expanded our programs to offer individual lessons. December of 2015, we installed a SureHands Lift; allowing us to more safely and easily lift students onto our therapy horses.<br />
We are looking forward to expanding our programs and classes.<br />
 If you have an idea to help us expand, we\'d love to hear it!

</p>

<p>
<a href="programs.php"><b>Programs</b></a>
<br />
We also offer many different kinds of programs through our Makin\' Memories Therapeutic Riding Center. We strive to create custom programs for our students, allowing them to achieve their goals. We sponsor programs through our facilities, such as CILLS Program, Freedom Riders, Hillside Program, and Girl Scouts. We also organize programs for schools with our <a href="schoolPrograms.php">Makin\' Memories School Programs.</a>
</p>

<p>
<b>How to Help</b><br/>
<ul>
<!--<li><a href="mudrun.php">Annual Mud Run Fund-Raising Event</a><br />
-->
<li>Mud Run Fund-Raising Event</li>
Where we are raising money to buy more equipment for our Therapeutic Riding Center</li>
<li><a href="volunteer.php">Volunteer</a></li>
<li><a href="donate.php">Donate</a></li>
</ul>
</p>');

$view = new GalleryView();
$view->setTag('makinMemories');
$pictures = new Text('','<a href="http://www.google.com" target="_blank"> <img src="image/gallery/horses/makinMemories/therapy/freedomRiders/Freedom%20Rec%20Tori%202014.jpg" alt="Makin\' Memories" ></a>');

//$pictures = new Text('','<img src="Mm1.jpg" alt="Makin\' Memories" style="width:500px;height:500px">');

$center->add($left);
$center->add($right);
$left->add($memories);
$right->add($pictures);
$right->add($view);

//$center->add($memories);
//$center->add($welcome);

$page->process();
$page->scriptUnload('menu.js');
$page->show();

//$page->awesomeDebug();

?>


