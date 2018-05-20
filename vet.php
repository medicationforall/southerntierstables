<?php
include('global.php');
include('pagesetup.php');


$page = clone $_SESSION['site'];


$center = $page->findChild('center');


$vet = new Text('','<b>Senior and Veterans Program</b>
<p>
Our Senior and Veterans program is designed for Adults with disabilities. We use horses in a therapeutic environment to help our riders achieve greater mobility!
<ul>
<li>6 Week Horsemanship Program</li>
<li>Classes are held on Tuesdays & Wednesdays</li>
<li>Time: 6pm to 8pm</li>
<li>Price: 
<ul>
<li>20% off a $50 Private lesson </li>
<li>Group Rates are also available</li>
</ul>
</li>
<li>Participants must be 60 years of age and older</li>
<li>Class Sessions start every six weeks during Spring through Fall</li>
<ul>
<li>May Session Classes begin <b>May 3rd</b></li>
<li>June Session Classes begin <b>June 14th</b></li>
<li>July Session Classes begin <b>July 26th</b></li>
<li>September Session Classes begin <b>September 6th</b></li>
</ul>
<li><b>Contact:
<br /> Donna Minnoe, Project Manager <br />
To inquire about joining this program<br />
Call: (315) 224-9085<br />
Email: contact@southerntierstables.com</b>
</ul>
<p>
<br />
<b><u>This program is currently offered by Makin\' Memories Therapeutic Riding Center</u></b><br />
</p>

</p>');

$center->add($vet);
//$center->add($welcome);

$page->process();
$page->scriptUnload('menu.js');
$page->show();

//$page->awesomeDebug();

?>
