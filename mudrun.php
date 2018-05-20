<?php
include('global.php');
include('pagesetup.php');


$page = clone $_SESSION['site'];

$page->setTitle('Annual Mud Run Fund-raising Event - Southern Tier Stables');
$page->meta('keywords','Southern Tier Stables, Donna Minnoe, Mark Minnoe, Makin\' Memories, Therapeutic Riding, Therapeutic Riding Center, Moravia NY, physical, mental, social, disabilities, adults, children, Mud Run 2, Annual Mud Run, Equine-assisted Therapies, Physical, Psychological, social, educational advancement, challenging experience, safe environment, PATH, Professional Association of Therapeutic Horsemanship International, certified instructor, Freedom Riders, CILLS, Hillside, Girl Scouts, volunteer, donate, Annual 5k Trail Mud Run, purchase goal, independence saddle, expanding therapeutic riding program');
$page->meta('description','Makin\' Memories Therapeutic Riding Centers\' Annual Mud Run Fund-raising Event. Annual Mud Run to benefit the Makin\' Memories Therapeutic Riding Center and much needed equipment for the facility. All funds go towards each years purchase goal for equipment for the facility, all extra funds are to be used towards more equipment and/or sponsorship of students.');

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



$mudrun = new Text('','<b>Annual Mud Run Fund-raising Event</b>
<p>
Our 3rd Annual 5k Trail Mud Run to benefit Makin\' Memories Therapeutic Riding Program, to be announced!
</p>
<p>
<ul>
<li>Beautiful CNY trails</li>
<li>T-shirt to all sign-ups by TBA</li>
<li>Lunch included </li>
<li>1 mile youth Fun Run</li>
<li>Auyer Timing </li>
</ul>
</p>
<p>
<!--<a href="https://www.ezracereg.com/Search/event.aspx?id=28841" target="_blank"><b>Register at EZRaceReg</b></a>-->
<br />
<b>Pre-Registration (Sign ups before and on TBA)</b>
<ul>
<li>Individual: $40 (Includes T-shirt & Lunch)</li>
<li>Teams (Min. 4 Members): $30 per member (Includes T-shirt & Lunch)</li>
<li>Youth 1-mile Fun Run (14 and under): $20.00 (Includes T-shirt & Lunch)</li>
</ul>

<b>Registration (Sign ups after TBA)</b>
<ul>
<li>Individual: $50 (Includes Lunch)</li>
<li>Teams (Min. 4 Members): $50 per member (Includes Lunch)</li>
<li>Youth 1-mile Fun Run (14 and under): $20.00 (Includes Lunch)</li>
</ul>
</p>
<br />
<p>
<b>This year our goal is to purchase indoor heating for the Makin\' Memories Therapeutic Riding Center!</b><br />
The indoor heating for the arena will allow us to operate all year long, giving us the opportunity to work year round with our students! <br />

</p>
<p>
Please come down and support a good cause, all donations and proceeds go to the purchase of this years goal. <br />
</p>
<p>
Any extra donations received will be used toward the Therapeutic Riding Centers needs. <br />
Such as:
<ul>
<li>Purchase of another Independence Saddle<br />
These are wonderful pieces of equipment and we could really use a few more to accommodate students</li>
<li>Sponsorship for Student(s)<br />
This would allow us to offer classes to a student(s) free of charge or for a very reduced price</li>
</ul>
</p>
<p>
*If you are not able to attend for any reason but still would like to help our cause, check out our <a href="donate.php">Donate page.</a>*
</p>

<p>
<b>2015 Fund Raiser</b> <br />
Last years Mud Run 2 and Benefit Jamboree were a huge success, which allowed us (with the very generous help of the Moravia Rotary Club) to purchase the SureHands Lift!<br />
<b><a href="surehandslift.png">SureHands Lift to the Right</a></b><br />
We\'d like to thank the Moravia Rotary Club for their very generous donation towards the purchase of the Surehands Lift! With their incredible generousity we are already more than half way there for our funding goal. So a very special thank you from Makin\' Memories Therapeutic Riding Center. With the help of everyone who attended the Mud Run 2 and the Benefit at Cortland Country Music Park, we installed the SureHands Lift December of 2015.
</p>
<p>
<b>2014 Fund Raiser</b><br />
Last years Mud Run was a huge success, which allowed us to purchase the Independence Saddle!<br />
<a href="indsaddle.png" target="_blank"><b>Independence Saddle to the Right</b></a><br />

We\'d like to thank everyone who donated, volunteered, and participated in last years Mud Run, so we could purchase the Independence Saddle.<br />
It has allowed us to help our students and as we expand our Therapeutic Program, many more people with disabilities!
</p>
');


$pictures = new Text('This Years Purchase Goal','<br />SureHands Lift<a href="surehandslift.png" target="_blank"> <img src="surehandslift.png" alt="Makin\' Memories" style="width:500px;"></a> <br /><a href="lift.png" target="_blank"> <img src="lift.png" alt="Makin\' Memories" style="width:250px;"></a> <a href="indsaddle.png" target="_blank"> <img src="indsaddle.png" alt="Makin\' Memories" style="width:250px;"></a><br /><a href="image/gallery/makinMemories/mudRun/mudruna.png" target="_blank"> <img src="image/gallery/makinMemories/mudRun/mudruna.png" alt="Makin\' Memories" style="width:450px;"></a>');


$center->add($left);
$center->add($right);
$left->add($mudrun);
$right->add($pictures);

//$center->add($mudrun);

//$center->add($welcome);

$page->process();
$page->scriptUnload('menu.js');
$page->show();

//$page->awesomeDebug();

?>
