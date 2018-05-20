<?php
include('global.php');
include('pagesetup.php');

$index = clone $_SESSION['site'];

//print 'short';

if(!empty($_REQUEST['shortType']))
{

	//http://php.net/manual/en/function.http-get-request-body.php
	if(!empty($_REQUEST['getToken']) && strcmp($_REQUEST['getToken'],'true')==0){

		$index->getAccount()->setAjaxToken();

		echo $index->getAccount()->getAjaxToken();
		return true;

	}

	//app search
	$found = false;

	$apps = $_SESSION['site']->getApps();

	foreach($apps as $key => $value){
		if(strcmp($_REQUEST['shortType'],$key)==0){
			$found = true;

			//print 'running short for app';
			$value->short();
		}
	} 

	if($found == false){
			//concessions made for the component name change
			$className=str_ireplace('component','',$_POST['shortType']);
			$className=str_ireplace('comment','CommentBox',$className);
			Core::debug('running short for '.$_REQUEST['shortType']);
			$component = new $className();

			$index->add($component);

			$component->short();
	}
}

?>
