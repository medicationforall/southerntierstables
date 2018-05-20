<?php

class SiteMap extends Core{

	private $page;

	function __construct($page=null)
	{
		parent::__construct("siteMap");
		$this->page = $page;		
	}

	function process(){
		//$this->children('process');
		//print_r($this->getChildren());
	}

	function show(){
		header('Content-type: application/xml');
		echo '<?xml version="1.0" encoding="UTF-8"?>'."\n";
		echo '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">'."\n";
		//print urls
		//print count($this->getChildren());
		$this->children('siteMap');

		if($this->page){
			$apps = $this->page->getApps();

			foreach($apps as $app){
				//process the app
				$app->process();

				//display the apps sitemap
				if(method_exists($app,'siteMap')){
				$app->siteMap();
				}
			}
		}
		echo '</urlset>';

	}
}

?>
