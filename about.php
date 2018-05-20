<?php
include('global.php');
include('pagesetup.php');


$page = clone $_SESSION['site'];

$page->setTitle('About - Southern Tier Stables');
$page->meta('keywords','Southern Tier Stables,Donna Minnoe, Mark Minnoe, Makin\' Memories, Therapeutic Riding, Therapeutic Riding Center');
$page->meta('description','Mark and Donna Minnoe are the owners of Southern Tier Stables and Makin\' Memories Therapeutic Riding Center. They are involved in their community, offering therapeutic riding to children with disabilities, and are partnered with the Cayuga Community College CILLS Program, where riders with special needs participate in a four week program learning to become independent riders. They also offer physical education course through Wells College, and are partnered with the local Girl Scouts, Weedport Schools, and Freedom Recreational Services.');

$page->setStyle('
#about > .subMenu{
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


$about = new Text('','
<b>About Us</b>
<p>
Mark and Donna Minnoe are the owners of Southern Tier Stables and Makin\' Memories Therapeutic Riding Center. They are involved in their community, offering therapeutic riding to children with disabilities, and are partnered with the Cayuga Community College CILLS Program, where riders with special needs participate in a four week program learning to become independent riders. They also offer physical education course through <a href="
http://www.wells.edu/academics/academic-catalog/courses-of-instruction/physical-education.aspx" target="_blank">Wells College</a>, and are partnered with the local Girl Scouts.
</p>
<p>
Mark Minnoe has 14 years experience working with horses. He is a Level 3 graduate of <a href="http://www.richardshrake.com/Resistance_Free%C2%AE_Bit_Series.htm?m=66&s=537" target="_blank">Richard Shrake\'s Resistance Free® Program</a>. He proves students with instruction on communication, behavioral and riding techniques. Mark is current serving as President for the <a href="http://www.fingerlakesequestrians.com" target="_blank">Finger lakes Equestrians Club</a>. 
</p>
<p>
Donna Minnoe has 15 years experience working with horses. She is a Professional Association Therapeutic Horsemanship International (<a href="http://www.pathintl.org" target="_blank">PATH Int.</a>) certified Therapeutic Riding Instructor. She instructs all our therapeutic programs. Before becoming a PATH Certified Instructor, Donna worked as a teachers aide for Persons with disablities through BOCES for 7 years. She is currently serving as Vice President for the <a href="http://www.fingerlakesequestrians.com" target="_blank">Finger lakes Equestrians Club</a>.
</p>
<p>
Mark and Donna have been working with the Freedom Riders, a program offered through the Freedom Recreational Services through the United Way of Cayuga County since 2009. A program offering therapeutic riding to children with developmental disabilities. 
</p>
<p>
For more information about: <br />
	Freedom  Recreational Services<br />
	P.O Box 2134<br />
	Auburn, NY 13021<br />
	(315)253-5465<br />
<br />
	United Way of Cayuga County<br />
	<a href="http://www.unitedwayofcayugacounty.org" target="_blank">www.unitedwayofcayugacounty.org</a><br />
	<br />
	Human Services Coalition of Cayuga County<br />
	<a href="http://www.human-services.org" target="_blank">www.human-services.org</a>
</p>

');

$view = new GalleryView();
$view->setTag('donnaMinnoe');

$center->add($left);
$center->add($right);
$left->add($about);
$right->add($view);
//$center->add($about);

//$center->add($welcome);

$page->process();
$page->scriptUnload('menu.js');
$page->show();

//$page->awesomeDebug();

?>
