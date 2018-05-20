<?php
include('global.php');
include('pagesetup.php');


$page = clone $_SESSION['site'];

$page->setTitle('Volunteer! - Southern Tier Stables');
$page->meta('keywords','Southern Tier Stables, Donna Minnoe, Mark Minnoe, Makin\' Memories, Therapeutic Riding, Therapeutic Riding Center, Moravia NY, physical, mental, social, disabilities, adults, children, Equine-assisted Therapies, Physical, Psychological, social, educational advancement, challenging experience, safe environment, PATH, Professional Association of Therapeutic Horsemanship International, certified instructor, Freedom Riders, CILLS, Hillside, Girl Scouts, volunteer, donate, Annual 5k Trail Mud Run, purchase goal, independence saddle, expanding therapeutic riding program, volunteer, buddy, sidewalker, horse leader, 5 years experience');
$page->meta('description','Makin\' Memories Therapeutic Riding Centers volunteers are a priceless piece in our facility. They help with everything from office work, to helping with therapy groups, to teaching and bonding with students.');


$page->setStyle('
#memories > .subMenu{
display:block;
}

.email textarea{
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



$volunteer = new Text('','<b>Volunteer!</b>
<p>
Volunteers are always needed! 
</p>
<p>
If you enjoy working with people and horses, volunteer! <br />
We are always looking for more help and offer training in all positions from Buddy, Sidewalker, and Horse Leader
<ul>
<li>All volunteers must be at least 14 years of age.</li>
 
<li>Horse leaders must have at least 5 years of experience working with horses.</li>
 
<li>Sessions involve light physical activity (lifting, reaching, walking, assisting students, occasionally jogging, etc).</li>
</ul>
 </p>
<p>
Volunteers get a unique experience working with our Makin\' Memories Program. Volunteers are trained in basic commands, and all volunteers have to go through our basic training program to work with our therapy horses and our Makin\' Memories to ensure a safe environment for all participants. 
</p>
<p>
Interested? <br />
<a href="contact.php">Contact us;</a> we\'d love to hear from you!
</p>


');

$email = new Email();


$center->add($left);
$center->add($right);
$left->add($volunteer);
$right->add($email);

//$center->add($volunteer);

//$center->add($welcome);

$page->process();
$page->scriptUnload('menu.js');
$page->show();

//$page->awesomeDebug();

?>
