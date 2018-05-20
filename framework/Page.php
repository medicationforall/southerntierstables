<?php

/**
 *   Medication For All Framework source file Page,
 *   Copyright (C) 2009-2011  James M Adams
 *
 *   This program is free software: you can redistribute it and/or modify
 *   it under the terms of the GNU Lesser General Public License as published by
 *   the Free Software Foundation, either version 3 of the License, or
 *   any later version.
 *
 *   This program is distributed in the hope that it will be useful,
 *   but WITHOUT ANY WARRANTY; without even the implied warranty of
 *   MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *   GNU Lesser General Public License for more details.
 *
 *   You should have received a copy of the GNU Lesser General Public License
 *   along with this program.  If not, see <http://www.gnu.org/licenses/>.
 *
 *@package framework
 */

/**
 *   Represents an HTML page; central to how this framework works. 
 *   To use this class, create one page as your sites template and add in all 
 *   of its children components and panels. Place the template into a session 
 *   variable, and then on each separate php file clone this session variable 
 *   and add on only the components that are unique for that particular page. 
 *   This technique creates a write once, run anywhere system; that can be altered 
 *   at any time during the sites roll out. 
 *
 *   This codebase is intended to 
 *   be a template builder, but not for just one template, but suited for multiple instances of templates.
 *
 *   Page should be the root element. A lot of this framework only affectively works when the
 *   Root element is a Page object.
 *
 *   Sample Usage {@link http://www.medicationforall.com/framework/sample/SamplePage.php Sample Page}
 *
 *   Code for the sample.
 *
 *   {@example ../sample/SamplePage.php SamplePage}
 *
 *@author James M Adams <james@medicationforall.com>
 *@version 0.1.5
 *@package framework
 */
class Page extends Core {
//data
	/**
	 *   Page title.
	 *@access private
	 *@var string
	 */
	private $title = '';

	/**
	 *   Page stylesheet links.
	 *@access private
	 *@var array
	 */
	private $style = array();

	private $printStyleSheet="";

	/**
	 *print scripts at the top of the page in the header.
	 */
	private $scriptsTop = true;

	/**
	 *   Arraylist of script links.
	 *@access private
	 *@var array
	 */
	private $script = array();

	/**
	 *   Hashmap
	 *@access private
	 *@var array
	 */
	private $scriptPath = array();

	/**
	 *   Css styles included within the page.
	 *@access private
	 *@var string
	 */
	private $styles = '';

	/**
	 *   Meta tag name arraylist hashmap pair
	 *@access private
	 *@var array
	 */
	private $metaName = array();

	/**
	 *   Meta tag content arraylist hashmap pair
	 *@access private
	 *@var array
	 */
	private $metaContent = array();

	/**
	 *   Version number string. Used to reference for the version number you set for your template roll out, 
	 *   That way if the users currently loaded template in their session isn't the same as the current 
	 *   template the template is forced to be rebuilt.
	 *@access private
	 *@var string
	 */
	private $version;

	/**
	 *   Singleton: the instance of account is the same across all 
	 *   instances of page, per page template. Because account is overlooked and passed by reference when cloned.
	 *@access private
	 *@var Account
	 */
	private $account;

	/**
	 *   Singleton: Stores the connection variables to the mysql database in a central location.. as an aside 
	 *   individual instances for different Mysql user levels should be possible.
	 *@access private
	 *@var Connect
	 */
	private $connect;

	/**
	 *   Singleton: Page settings.
	 *@access private
	 *@var Settings
	 */
	private $settings;

	/**
	 *   Singleton: Anytime parse() is called in core it references this HTMLParse which is used to actively parse 
	 *   out html tags and attributes based off of an editable whitelist.conf.
	 *@access private
	 *@var HTMLParser
	 */
	private $HTMLParser;

	/**
	 *   Singleton: Anytime parse() is called in core with a directive of strip it references this HTMLParse which is used to actively parse 
	 *   out html tags and attributes based off of an editable whitelist.conf.
	 *@access private
	 *@var HTMLParser
	*/
	private $HTMLStrip;

	/**
	 *
	 */
	private $spamFilter;

	/**
	 *RSS display flag.
	 *@access private
	 *@var boolean
	 */
	private $rss = false;


	//allow json view of page / components
	private $jsonFlag = false;
	private $jsonpFlag = false;

	/**
	 *Favicon display flag
	 *@access private
	 *@var boolean
	 */
	private $favIcon = false;

	/**
	 *RSS Link GET value.
	 *@access private
	 *@var string
	 */
	private $rssLink = '?rss=true';

	/**
	 *favIcon Link url.
	 *@access private
	 *@var string
	 */
	private $favIconLink = 'favicon.png';

	/**
	 *Hashmap of apps;
	 */
	private $app = array();

//constructor

/**
 *   Constructs the page object.
 *@param string $t Page title
 *@param string $s Stylesheet link
 *@param string $file Used for some unique use cases, basically your setting in reference 
 *where the page is being ran from; Useful for Ajax calls.
 */
	function __construct($t='',$s='style.css',$file='') {
		parent::__construct("page");

		$this->title=$t;
		$this->addStyle($s);

		$this->account = new Account();

		$this->connect = new Connect();

		$this->settings = new Settings();

		$this->HTMLParser = new HTMLParser();
		$this->HTMLParser->setParent($this);

		
		$this->HTMLStrip = new HTMLParser('', '', '');
		$this->HTMLStrip->setParent($this);

		$this->spamFilter = new SpamFilter();
		$this->spamFilter->setParent($this);
		

		$this->debug('construct page '.$t);

		$this->setFile($file);
	}

//methods
/**
 *   Process page. Calls process() for the Pages children. Also instantiates 
 *   the page token for authenticating against actions within components.
 *@param boolean $processChildren Flag for calling process() on the pages children.
 */
	function process($processChildren=true) {
		$this->debug('process Page');

		if($processChildren) {
			$this->children('process');
		}

		//tricky bit of execution, the global token doesn't get reset until all of the children have processed.
		$this->account->setToken();
	}

/**
 *   This method is the infrastructure of the website; it interprets the object variables and builds the page.
 *   Most if not all of the sites output is controlled from here.
 *   Calls show() for the Pages children.
 */
	function show() {
		if($this->isRSS() && !empty($_REQUEST['rss']) && strcmp($_REQUEST['rss'], 'true')==0) {
			$this->rss();
		}
		else if($this->isJSON() && !empty($_REQUEST['json']) && strcmp($_REQUEST['json'], 'true')==0) {
			$this->json();
		}
		else if($this->isJSONP() && !empty($_REQUEST['jsonp']) && strcmp($_REQUEST['jsonp'], 'true')==0) {
			$this->jsonp();
		}
		else {
			$this->html5();
		}
	}


/**
 *   Writes HTML 5 to the browser. Between this method and html() they don't meet DRY conventions, 
 *   but the counter argument is I don't want to intertwine these two methods in case more render modes are added.
 */
	function html5() {
		$this->debug('print html 5 markup');

		echo '<!DOCTYPE html>'."\n";

		echo '<html lang="en">'."\n";

		echo '<head>'."\n";

		//meta
		$this->printMeta();

		echo '<meta charset="utf-8" />'."\n";
		echo '<META HTTP-EQUIV="EXPIRES" CONTENT="'.date('D, j M Y H:i:s e',strtotime("+1 week", strtotime("now"))).'">';

		if($this->isFavIcon()) {
			echo '<link rel="shortcut icon" type="image/x-icon" href="'.$this->favIconLink.'">';
		}

		echo '<title>'.$this->title.'</title>'."\n";

		$this->printStyle();

		if(!empty($this->printStyleSheet)){
			echo '<link rel="stylesheet" media="print" href="'.$this->printStyleSheet.'" type="text/css" />';
		}

		if($this->isRSS()) {
			echo '<link rel="alternate" type="application/rss+xml" title="'.$this->title.' RSS Feed" href="'.$this->rssLink.'" />';
		}

		//style
		if(!empty($this->styles)) {
			echo '<style type="text/css">'."\n";
			echo $this->styles;
			echo '</style>'."\n";
		}

		//scripts
		if($this->scriptsTop == true) {
		$this->printScripts();
		}

		echo '</head>'."\n";

		echo '<body>'."\n";
		echo '<div class="page '.$this->getClass().'">'."\n";

		//content
		$this->children('show');

		echo('</div>'."\n");
		echo('</body>'."\n");

		//scripts
		if($this->scriptsTop == false) {
		$this->printScripts();
		}
		echo('</html>'."\n");
	}

/**
 *   Writes RSS XML to the browser.
 */
	function rss() {
		$this->debug('Page RSS', 'RSS');

		echo '<?xml version="1.0" encoding="ISO-8859-1"?>'."\n";
		echo '<rss version="2.0" xmlns:dc="http://purl.org/dc/elements/1.1/">'."\n";
			echo '<channel>';
				echo '<title>'.$this->title.' - Recent</title>'."\n";
				echo '<link>'.$this->curPageURL().$_SERVER['PHP_SELF'].'</link>'."\n";
				echo '<description>Track the most recent changes to the website in this feed.</description>'."\n";
				echo '<language>en</language>'."\n";

				echo '<generator>Medication for all framework</generator>'."\n";

				$this->children('rss');
			echo '</channel>'."\n";
		echo '</rss>'."\n";
	}

	function json(){
		echo '{';
			$this->iterateJson();
		echo '}';
	}

	function jsonp(){
		echo 'jsonCallback({';
			$this->iterateJson();
		echo '});';
	}

/**
 *
 */
	function joinScript($name, $scripts,$placement='', $path='') {

		if(empty($path)){
			$path = $this->settings->getScriptBase();
		}

		if(file_exists($placement.'/'.$name)===false){
			//print 'script does not exist';

			$files = array();

			foreach($scripts as $script){
				if(stristr($script, '*')) {
					$script = $this->latestScript($script, $path);
				}

				$minScript = $this->resolveMinName($script);
				//print '<br />'.$path.'/'.$minScript;
				if(file_exists($path.'/'.$minScript)){
					$files[$script] = file_get_contents($path.'/'.$minScript);

				} else if(file_exists($path.'/'.$script)){
					//print '<br />join file exists'.$path.'/'.$script;
					$files[$script] = file_get_contents($path.'/'.$script);
					//print file_get_contents($path.'/'.$script);
				}

			}


			file_put_contents ($name,implode(PHP_EOL,$files));
		}
		$this->script($name,$placement);
	}


/**
 *   Overwritten instance of script. This method adds the passed script reference to the site.
 *Can now use a wildcard character, refer to method latest Script to see an example.
 *@param string $n Name of the script file.
 *@param string $p unique path where the script is located. Default is the globally defined path in Settings
 */
	function script($n, $p='') {

		if(stristr($n, '*')) {
			$n = $this->latestScript($n, $p);
		}

		//not in the array already
		if(!in_array($n, $this->script)) {
			$this->debug('adding script '.$n.' '.count($this->script));
			$this->script[count($this->script)] = $n;

			if(!empty($p))
			{
				$this->scriptPath[$n] = $p;
			}
		}
	}

/**
 *I got sick and tired of making pointer files to the latest version of jquery so I created this lookup method to just dynamically find the latest version of a script.
 *Note that setScriptBase in setting has to be set before you call this method because otherwise the lookup path will probably be wrong.
 *example: $page->latestScript('jquery-*.min.js'); note the wildcard character to define where the version numbering should be.
 *@param string $n The search string for what you want the latest found of.
 *@param strimg $p Script path.
 */
	function latestScript($n, $p='') {
		$list = array();

		//set the search path
		if(empty($p)) {
			$p = $this->settings->getScriptBase();
		}

		//explode search string
		$searchParts = explode('*', $n);

		//read the directory
		//print 'directory '.$p;
		$dir = dir($p);
		while($file = $dir->read()) {
			$pass = true;
			$hit ='';

			$tmpFile = $file;
			//print '<br />'.$file;

			foreach($searchParts as $search) {
				//print '<br /> search part '.$search;
				if(stristr($file, $search)) {

					$tmpFile = str_replace($search, '', $tmpFile);
					$hit = $tmpFile;
					//print '<br />This is a hit for'.$search.' '.$tmpFile;
				}
				else {
					//print '<br />This is a file '.$file.' '.$search;
					$pass = false;
					break;
				}
			}

			if(!empty($hit)) {
				//check if any alpha characters are left
				$numeric = explode('.', $hit);
				$pass = true;

				foreach($numeric as $number) {
					if(ctype_digit($number) ==false) {
						$pass = false;
					}
				}

				if($pass) {
					//print '<br />This is a hit '.$file;
					$list[count($list)] = $file;
				}
			}
		}

		//sort directory
		//http://php.net/manual/en/function.usort.php
		//http://php.net/manual/en/function.version-compare.php
		//sort($list);
		usort($list, 'version_compare');

		
		if(count($list)>0){
			$n = $list[count($list)-1];
		}

		return $n;
		//$this->script($n, $p);
	}

/**
 *   Adds a stylesheet to the page, permitting it's not already in the list.
 *@param String $s Relative or absolute path of the stylesheet to be added. 
 */
	function style($s) {
		$this->debug('Deprecated: use addStyle instead of style: '. $s);
		$this->addStyle($s);
		
	}

/**
 *Placeholder function to rename unintuitive method style
 *@param $s Relative or absolute path of the stylesheet to be added.
 */
	function addStyle($s) {
		if(!in_array($s, $this->style)) {
			$this->style[count($this->style)] = $s;
		}
	}

/**
 *
 */
	function addPrintStyleSheet($s) {
		$this->printStyleSheet = $s;
	}

/**
 *   Remove a script from being ran.
 *@param string $n The name of the script.
 */
	function scriptUnload($n) {
		if(in_array($n, $this->script)) {
			$this->debug('Removing script '.$n.' '.count($this->script));

			$count = count($this->script);
			for($i=0;$i<$count;$i++) {
				if(strcmp($this->script[$i], $n)==0) {
					//if script path not empty
					if(!empty($this->scriptPath[$this->script[$i]])) {
						unset($this->scriptPath[$this->script[$i]]);
					}

					unset($this->script[$i]);
				}
			}
			$this->script  = array_values($this->script);
		}
	}

/**
 *   Writes the pages script dependencies to the browser.
 */
	function printScripts() {
		$count = count($this->script);
		$this->debug('script count '.$count);

		for($i=0;$i<$count;$i++) {
			$name = $this->script[$i];
			$path = $this->settings->getScriptBase();

			if(stripos($this->script[$i], "//")===false) {

				if(!empty($this->scriptPath[$this->script[$i]])) {
					$path = $this->scriptPath[$this->script[$i]];
				}
				$name = $path.'/'.$this->script[$i];
			}

			$minName = $this->resolveMinName($name);

			if(file_exists($minName)) {
				$name = $minName;				
			}/*else{
				print '<br />min script does not exist '.$name.' '.$minName;
			}*/

			echo '<script type="text/javascript" src="'.$name.'"></script>'."\n";
		}
	}

/**
 *   Prints the array of stylesheets.
 */
	function printStyle() {
		$count = count($this->style);
		for($i=0;$i<$count;$i++) {

			$name = $this->style[$i];
			$minName = $this->resolveMinName($name);
			if(file_exists($minName)){
				$name = $minName;				
			}

			echo '<link rel="stylesheet" type="text/css" href="'.$name.'" />'."\n";
		}
	}

/**
 *Add .min to the file name before the last period. 
 */
	function resolveMinName($name) {
	 	//Lesson re-learned never write clever code. You have to be twice as smart as you were before to debug it!

		//if it has min already do not worry about modifying it
		if(stripos($name, ".min.")!==false || stripos($name, ".min.") >1) {
			return $name;
		}

		//find the last period (.)
		$position = strrpos($name,'.');

		//grab the part before the last period
		$returner = substr($name,0,$position);

		//insert min
		$returner .='.min'; 

		//grab the part after the last period and append it
		$returner .= substr($name,$position);

		//print '<br />'.$returner;
		return $returner;		
	}

/**
 *   Adds the given string pair to the meta list index, and value set.
 *   if the given meta name is already in the list instead of listing the tag twice,
 *   the previous meta tag gets overwritten with this latest version.
 *@param string $n meta name value
 *@param string $c meta content value
 */
	function meta($n, $c) {
		if(!in_array($n, $this->metaName)) {
			$this->metaName[count($this->metaName)] = $n;
		}
		else {
			$this->debug($n.' is in array');			
		}
		$this->metaContent[$n] = $c;
	}

/**
 *   Gets meta content, may return null.
 *@param string $c Meta content name to be returned.
 *@return string The data associated with the meta name.
 */
	function getMeta($c) {
		$returner = null;

		if(isset($this->metaContent[$c]) && !empty($this->metaContent[$c])) {
			$returner = $this->metaContent[$c];
		}
		return $returner;
	}

/**
 *   Writes the pages meta data to the browser.
 */
	function printMeta() {
		$count = count($this->metaName);
		for($i=0;$i<$count;$i++) {
			echo '<meta name="'.$this->metaName[$i].'" content="'.$this->metaContent[$this->metaName[$i]].'" />'."\n";
		}
	}

/**
 *   Sets embedded css page stylesheet.
 *@param string $s CSS syntax code block without <style> tags.
 */
	function setStyle($s) {
		$this->styles = $s;
	}

/**
 *   Gets the specific style rules set for a page.
 *@return string Page specific style rules.
 */
	function getStyle() {
		return $this->styles;
	}

/**
 *   Sets the page title.
 *@param string $t Page title
 */
	function setTitle($t) {
		//print 'setting page title '.$t;
		$this->debug('setting page title '.$t);
		$this->title = $t;
	}

/**
 *   Gets the page title.
 *@return string The pages title. 
 */
	function getTitle() {
		return $this->title;
	}

/**
 *   Proof of concept function showing how to create a clone handler for classes that inherit from Core.
 *   If you need a unique clone handler this shows how to write one and still call on the parent clone handler
 */
 	function __clone() {
		parent::__clone();
		$this->debug('clone page script '.count($this->script));
	}

/**
 *   Gets the version number which can be used to test against a hard coded version number.
 *   This is useful to check against if you need to force the users session to update.
 *@return string Pages version number		echo '<br /> '.'<span class="lightTitle">Message 2:</span> '.$this->message2;
 */
	function getVersion() {
		return $this->version;
	}

/**
 *   Sets the Pages version number
 *@param string $v value of the current version of the framework
 */
	function setVersion($v) {
		$this->version = $v;
	}

/**
 *   Gets the Account object. All Account settings are stored in this object.
 *@return Account The Account object which stores logged in users info.
 */
	function getAccount() {
		return $this->account;
	}

/**
 *   Sets the connect object for the page
 *@param Connect $c Connect object
 */
	function setConnect($c) {
		$this->connect = $c;
	}

/**
 *   Gets the Connect object. All Database settings are stored here.
 *@return Connect Page connect object
 */
	function getConnect() {
		return $this->connect;
	}

/**
 *   Sets the global page settings
 *@param Settings $s Settings object
 */
	function setSettings($s) {
		$this->settings = $s;
	}

/**
 *   Gets Page Settings. All Global page settings are stored here.
 *@return Settings Page Settings object
 */
	function getSettings() {
		return $this->settings;
	}

/**
 * Gets the HTMLParser object.
 */
	function getHTMLParser() {
		return $this->HTMLParser;
	}

/**
 * Gets the HTMLParser object.
 */
	function getHTMLStrip() {
		return $this->HTMLStrip;
	}

/**
 *
 */
	function getCommentSpamFilter() {
		$this->spamFilter->setMode('comment');
		return $this->spamFilter;
	}

/**
 *@todo this may be unneccary since comment and forum may run off of the same table
 */
	function getForumSpamFilter() {
		$this->spamFilter->setMode('forum');
		return $this->spamFilter;
	}

/**
 * Sets the HTMLParser object.
 *@param HTMLParser $p
 */
	function setHTMLParser($p) {
		$this->HTMLParser = $p;
	}

/**
 *   If the page has meta data set in the MYSQL database this method retrieves that information and places it into the pages variables.
 */
	function setHead() {
		$this->debug('page calling set head for meta update');
		if($this->connect->isdbConnect()) {
			$mysqli = $this->connect->getMysqli();
			$this->setFile();
			$page = $this->getFile();
			$site = $this->settings->getSite();

			$query = 'SELECT title,description,keywords,style FROM tblmeta WHERE page=? AND status=\'active\' AND site=? AND `class`=?';

			if($stmnt=$mysqli->prepare($query)) {
				$class = $this->getUnique();
				if(empty($class)) {
					$class = '';
				}
				$stmnt->bind_param('sss', $page, $site, $class);
				$stmnt->execute();
				$stmnt->bind_result($title, $description, $keywords, $style);

				while($stmnt->fetch()) {
					$this->setTitle($title);
					$this->meta('description', $description);
					$this->meta('keywords', $keywords);
					$this->styles = $style;
				}
			}
		}
	}

/**
 *   Gets if RSS display mode is turned on.
 *@return boolean Rss flag.
 */
	function isRSS() {
		return $this->rss;
	}

	function isJSON(){
		return $this->jsonFlag;
	}

	function setJSON($flag){
		$this->jsonFlag = $flag; 
	}

	function isJSONP(){
		return $this->jsonpFlag;
	}

	function setJSONP($flag){
		$this->jsonpFlag = $flag; 
	}

/**
 *   Sets rss display flag.
 *@param boolean $r RSS flag.
 */
	function setRSS($r) {
		$this->rss = $r;
	}

/**
 *Sets the RSS link GET segment.
 *@param string $link
 */
	function setRSSLink($link) {
		$this->rssLink = $link;
	}

/**
 *Gets the RSS link GET segment.
 *@return string
 */
	function getRSSLink() {
		return $this->rssLink;
	}

/**
 *   Gets if favIcon display mode is turned on.
 *@return boolean favIcon flag.
 */
	function isFavIcon() {
		return $this->favIcon;
	}

/**
 *   Sets favIcon display flag.
 *@param boolean $f favIcon flag.
 */
	function setFavIcon($f) {
		$this->favIcon = $f;
	}

/**
 *Sets the favIcon link URL.
 *@param string $link
 */
	function setfavIconLink($link) {
		$this->favIconLink = $link;
	}

/**
 *Gets the favIcon link GET URL.
 *@return string
 */
	function getFavIconLink() {
		return $this->favIconLink;
	}

/**
 *Sets the javascript top flag. When set to true (defautl) javascripts are place in the head section of the HTML page. 
 *When set to false the are placed at the bottom of the body tag.
 *@param boolean $t Script Top Flag.
 */
	function setScriptsTop($t) {
		$this->scriptsTop = $t;
	}

/**
 *Custom Awesome Debug content.
 */
	function adContent() {
		echo '<br /><br /><span class="lightTitle">Additional Content:</span>';
		parent::adContent();
		echo '<br /> '.'<span class="lightTitle">Title:</span> '.$this->title;
		echo '<br /> '.'<span class="lightTitle">stylesheets:</span> ';
		foreach($this->style as $style) {
			echo '<br />'.$style;
		}

		echo '<br /> '.'<span class="lightTitle">Scripts:</span> ';
		$count = count($this->script);
		for($i=0;$i<$count;$i++) {
			$path = $this->settings->getScriptBase();
			if(stripos($this->script[$i], "http")===false) {
				if(!empty($this->scriptPath[$this->script[$i]])) {
					$path = $this->scriptPath[$this->script[$i]];
				}
				echo '<br />'.$path.'/'.$this->script[$i];
			}
			else {
				echo '<br />'.$this->script[$i];
			}
		}

		echo '<br /> '.'<span class="lightTitle">Page Styles:</span> <code><pre>'.$this->styles.'</pre></code>';
		echo '<br /> '.'<span class="lightTitle">Version:</span> '.$this->version;
		echo '<br /> '.'<span class="lightTitle">RSS:</span> ';
		if($this->rss)
		{echo 'true';}
		else
		{echo 'false';}

		echo '<br /> '.'<span class="lightTitle">Scripts in header:</span> ';
		if($this->scriptsTop)
		{echo 'true';}
		else
		{echo 'false';}

		$this->account->adContent();
		$this->connect->adContent();
		$this->settings->adContent();
	}

/**
 *Important to note is that apps do not get cloned.
 */
	function addApp($appName,$appObject) {
		$appObject->setParent($this);
		$this->app[$appName]=$appObject;
	}

/**
 *
 */
	function getApp($appName) {
		$returner = null;
		if(!empty($this->app[$appName])){
			$returner =$this->app[$appName];
		}
		return $returner;
	}

/**
 *
 */
	function getApps() {
		return $this->app;
	}
}
?>
