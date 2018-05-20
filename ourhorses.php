<?php
include('global.php');
include('pagesetup.php');


$page = clone $_SESSION['site'];

$page->setTitle('Our Horses - Southern Tier Stables');
$page->meta('keywords','Southern Tier Stables, Makin\' Memories, Therapeutic Riding, Therapeutic Riding Center, Moravia NY, physical, mental, social, disabilities, adults, children, Equine-assisted Therapies, horses, therapy horses');
$page->meta('description','Makin\' Memories Therapeutic Riding Centers\' Therapy Horses. Our horses are one-of-a-kind and the heart of the program!
It takes a very special type of horse to have the calm demeanor and tolerance necessary of a therapy horse. Having a sense of humor is also a must!

');

$page->setStyle('
#memories > .subMenu{
display:block;
}
');


$center = $page->findChild('center');
$right=new Panel('right');
$right->setUnique('right'); 

$left=new Panel('left');
$left->setUnique('left');

$memories = new Text('','<b>Our Horses</b><br/><br/>
<p>
<b>Therapy Horses</b><br/>

Our horses are one-of-a-kind and the heart of the program!<br />

It takes a very special type of horse to have the calm demeanor and tolerance necessary of a therapy horse. Having a sense of humor is also a must! 
</p>
<p>
<ul>
<li><b>Doc</b><br /> Doc wears ribbons, barrettes, even Halloween bat headbands, all with the goal of helping his student reach, focus, and/or complete the task at hand.<br <a href="image/gallery/horses/outdoor/doc.jpg" target="_blank"> <img src="image/gallery/horses/outdoor/doc.jpg" alt="Makin\' Memories" style="width:150px;"></a></li>
<li><b>Bud</b><br /><a href="image/gallery/horses/makinMemories/therapy/freedomRiders/bud/Bud with Dalton 1.jpg" target="_blank"> <img src="image/gallery/horses/makinMemories/therapy/freedomRiders/bud/Bud with Dalton 1.jpg" alt="Makin\' Memories" style="width:150px;"></a></li>
<li><b>Star</b><br /><a href="image/gallery/horses/makinMemories/therapy/freedomRiders/star/Laura 2014.jpg" target="_blank"> <img src="image/gallery/horses/makinMemories/therapy/freedomRiders/star/Laura 2014.jpg" alt="Makin\' Memories" style="width:250px;"></a></li>
<li><b>Flash</b><br /><a href="image/gallery/horses/makinMemories/therapy/freedomRiders/flash/Flash 2.jpg" target="_blank"> <img src="image/gallery/horses/makinMemories/therapy/freedomRiders/flash/Flash 2.jpg" alt="Makin\' Memories" style="width:150px;"></a></li>
<li><b>Annie</b></li>
<li><b>K.C.</b><br /><a href="image/gallery/horses/makinMemories/therapy/freedomRiders/cassie/KC with Anela 1.jpg" target="_blank"> <img src="image/gallery/horses/makinMemories/therapy/freedomRiders/cassie/KC with Anela 1.jpg" alt="Makin\' Memories" style="width:150px;"></a></li>
<li><b>Munchy</b></li>

</ul>

</p>');


$view = new GalleryView();
$view->setGalCount(10);
$view->setTag('therapy');


$pictures = new Text('','<a href="http://www.google.com" target="_blank"> <img src="Mm1.jpg" alt="Makin\' Memories" style="width:500px;></a>');

//$pictures = new Text('','<img src="Mm1.jpg" alt="Makin\' Memories" style="width:500px;height:500px">');

$center->add($left);
$center->add($right);
$left->add($memories);
$right->add($pictures);
$right->add($view);

//$center->add($welcome);

$page->process();
$page->scriptUnload('menu.js');
$page->show();

//$page->awesomeDebug();

?>
