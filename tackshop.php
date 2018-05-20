<?php
include('global.php');
include('pagesetup.php');


$page = clone $_SESSION['site'];

$page->setTitle('Tack Shop - Southern Tier Stables');
$page->meta('keywords','southern tier stables, moravia ny, moravia, tack shop, horse tack, supplies, clothing, helmets, consignment items, gifts, saddles, bridles, halters, show clothing, maintenance items');
$page->meta('description','We have a full service Tack Shop on the premises filled with horse tack, supplies, clothing, helmets, consignment items, gifts, and much, much more.');

$page->setStyle('
#services > .subMenu{
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


$tack = new Text('','<b>Tack Shop</b>
<p>
We have a full service Tack Shop on the premises filled with horse tack, supplies, clothing, helmets, consignment items, gifts, and much, much more. <br />
</p>
<p>
<b>Available:</b>
<ul>
<li>Saddles</li>
<li>Bridles</li>
<li>Halters</li>
<li>Show Clothing</li>
<li>Maintenance Items such as:</li>
<ul>
<li>Bathing</li>
<li>Hoof Care</li>
</ul>
</ul>
If we don\'t have it in stock, it can be ordered.
</p>
<p>
Saddle fitting is also available to assure your purchase properly fits both you and your horse!
</p>
<p>
Call Donna for Information or to Make an Order<br />
<b>(315) 496-2609 </b>
</p>');


$view = new GalleryView();
$view->setTag('tackShop');

$center->add($left);
$center->add($right);
$left->add($tack);
$right->add($view);

//$center->add($tack);

//$center->add($welcome);

$page->process();
$page->scriptUnload('menu.js');
$page->show();

//$page->awesomeDebug();

?>
