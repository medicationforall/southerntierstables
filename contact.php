<?php
include('global.php');
include('pagesetup.php');


$page = clone $_SESSION['site'];

$page->setTitle('Contact - Southern Tier Stables');
$page->meta('keywords','Southern Tier Stables, Moravia NY, Moravia, news, southern tier stables, makin\' memories, contact, form, contact form, donna minnoe, mark minnoe, volunteer form, stables contact, contact information, phone number, email, address');
$page->meta('description','Contact Us, Send an email through our system, call, email, or mail us. Our contact information is listed here. ');

$page->setStyle('
.email textarea{
display:block;
}
');


$center = $page->findChild('center');

$right=new Panel('right');
$right->setUnique('right'); 

$left=new Panel('left');
$left->setUnique('left');


$email = new Email();

$contact = new Text('','<b>Donna and Mark Minnoe</b>
<p>
Address:
<ul>
<li>Southern Tier Stables<br />
2068 Dumplin Hill Rd.<br />
Moravia, N.Y. 13118<br /></li>
</ul>
</p>
<p>
Phone:
<ul>
<li>Stables: 315-496-2609</li> 
<li>Donna Cell: 315-224-9085</li>
</ul>
Email:
<ul>
<li>contact@southerntierstables.com</li>
</ul>
</p>');

$center->add($left);
$center->add($right);

$left->add($email);
$right->add($contact);

$page->process();
$page->scriptUnload('menu.js');
$page->show();

//$page->awesomeDebug();

?>
