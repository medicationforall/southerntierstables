<?php
include('global.php');
include('pagesetup.php');


$page = clone $_SESSION['site'];

$page->setTitle('Donate - Southern Tier Stables');
$page->meta('keywords','Southern Tier Stables, Donna Minnoe, Mark Minnoe, Makin\' Memories, Therapeutic Riding, Therapeutic Riding Center, Moravia NY, physical, mental, social, disabilities, adults, children, Equine-assisted Therapies, Physical, Psychological, social, educational advancement, challenging experience, safe environment, PATH, Professional Association of Therapeutic Horsemanship International, certified instructor, Freedom Riders, CILLS, Hillside, Girl Scouts, volunteer, donate, Annual 5k Trail Mud Run, purchase goal, independence saddle, expanding therapeutic riding program, donate, donation, for-profit organization, sponsorship, fundraising, fund-raising, donation form');
$page->meta('description','Makin\' Memories Therapeutic Riding Center are always appreciative of every cent that is donated to our Therapeutic Riding Center!
Your donation will go towards our yearly purchase goal (this years goal is a lift), toward needed equipment or sponsorship program. 
You can always specify what you\'d like your donation to go towards and we will try to accommodate.
To set up a scholarship or sponsorship in honor of or for someone contact the office for more details.');

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


$donate = new Text('','<b>DONATE!</b>
<p>
We are always appreciative of every cent that is donated to our Therapeutic Riding Center!<br />
Your donation will go towards our yearly purchase goal (this years goal is a lift), toward needed equipment or sponsorship program. <br />
You can always specify what you\'d like your donation to go towards and we will try to accommodate.<br />
To set up a scholarship or sponsorship in honor of or for someone contact the office for more details.
</p>
<p>
Please Download, Print, Fill out and Send in the Donation Form with your generous donation. So we can properly thank you on our website and Facebook page.<br />
If you\'d like your donation to be anonymous, please fill out the Donation Form as stated but check the "Donate Anonymously" check box at the top.<br />
</p>
<p>
<a href="donationform.pdf" target="_blank">Download Donation Form Here</a>
</p>
<p>
Please send Donation Forms to:<br />
<b>Makin\' Memories Therapeutic Riding Center</b><br />
In care of: Donna Minnoe<br />
2068 Dumplin Hill Rd.<br />
Moravia, NY 13118
</p>
<p>
*Makin\' Memories Therapeutic Riding Center is a For-Profit Organization, as such donations are not Tax Deductible. *
</p>
<p>
We also have our Annual Mud Run Fund-raising Event, which will be held in June this year. <br />
For more information, check out the Mud Run page.
</p>



');
$pictures = new Text('This Years Purchase Goal','<br />SureHands Lift<a href="surehandslift.png" target="_blank"> <img src="surehandslift.png" alt="Makin\' Memories" style="width:500px;></a> <br /><a href="lift.png" target="_blank"> <img src="lift.png" alt="Makin\' Memories" style="width:250px;></a>');

$center->add($left);
$center->add($right);
$left->add($donate);
$right->add($pictures);

//$center->add($donate);


//$center->add($welcome);

$page->process();
$page->scriptUnload('menu.js');
$page->show();

//$page->awesomeDebug();

?>
