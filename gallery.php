<?php
include('global.php');
include('pagesetup.php');


$page = clone $_SESSION['site'];

$page->setTitle('Gallery - Southern Tier Stables');

$page->meta('keywords','Southern Tier Stables, Moravia NY, Moravia, gallery, pictures, photos, pics');
$page->meta('description','Our photo gallery of past and current events.');


$page->setStyle('
.tagFilter .cFooter{
display:none;
}

#center
{
text-align:center;
padding:0px;
}
.gallery2
{
display:inline-block;
}
');

//manage subnav for calendar
$subNav= new Panel('subNav');
$subNav->setUnique('subNav');

$bar = $page->findChild('bar');

if(!empty($bar)){
$bar->add($subNav);
}

$tagFilter = new TagFilter();
$subNav->add($tagFilter);
//end manage subnav


$center = $page->findChild('center');
$gallery = $page->getApp('gallery2');
$center->add($gallery);

$page->process();
$page->scriptUnload('menu.js');
$page->show();

//$page->awesomeDebug();

?>
