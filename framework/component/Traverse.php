<?php

/**
 *   Medication For All Framework source file Traverse,
 *   Copyright (C) 2012  James M Adams
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
 *   Traverse is used for bulk processing new images / videoes being introduced to the gallery, Originally was built directly into gallery and gallery2.
 *   The Actual traversal is primarily controlled by traverse.js which kicks off this scripts run and determines when to stop calling traverse. 
 *
 *@author  James M Adams <james@medicationforall.com>
 *@version 0.1
 *
 *@package framework
 */


class Traverse extends Component
{
//data
/**
 *Flag used for checking if a traverse run should be kicked off by traverseCheck..
 *@var boolean
 */
	private $traverseImage = false;

/**
 *ArrayList of log entries.
 *@var array
 */
	private $log = array();

/**
 *Arraylist of directories searched. This is used as a means of keeping track of which directories have been scanned.
 *@var array
 */
	private $searched = array();

	/**
	 *Optimum thumbnail image size.
	 *@var int
	 */
	private $thumbSize = 100;

	private $thumbCompression = 70;

	private $thumbOverride = 'height';


	/**
	 *Optimum medium image size
	 *@var int
	 */
	private $mediumSize = 800;

	private $mediumCompression = null;

//constructor

/**
 * Constructs the traverse component.
 *@param string $h Header text.
 *@param string $type Component type, default is traverse.
 */
	function __construct($h='Traverse bulk image load.', $type='traverse')
	{
		parent::__construct($h, $type);
		$this->setLevel('admin');
	}

//methods

/**
 * Currently the actual logic processing for traverse is being done by gallery2,
 * @todo move the logic processing form gallery2 to here.
 */
	function process()
	{
		$page = $this->getParent('page');

		$this->addClass('dialog');
		$page->script('traverse.js');

		$this->buildTab();

		$this->children('process');
	}

/**
 *Builds the tab component to store the log and history text components.
 */	
	function buildTab()
	{
		$tab = $this->findChildByName('tab');
		if(empty($tab))
		{
			//build components
			$tab = new Tab();
			$log = new Text();
			$log->setUnique('logText');
			$log->addClass('log');

			$errors = new Text();
			$errors->setUnique('errorText');
			$errors->addClass('errors');

			//order components
			$tab->add($errors,'History');
			$tab->add($log,'Log');


			$this->add($tab);
		}
	}

/**
 * Display content of traverse as a dialog.
 */
	function cContent()
	{
		print 'running traverse';
		echo '<div class="cContent">';

		$log =  $this->findChild('logText');

		if(!empty($log))
		{
			$log->setText($this->printLog());
		}

		$this->children('show');


		//echo '<div class="log">'.$this->printLog().'</div>';
		//echo '<div class="errors"></div>';

		if($this->traverseImage)
		{
			echo '<div class="loadIcon"></div>';
		}
		echo '</div>';
	}



/**
 *   Traverse is a recursive function which scans through a directory if another directory
 *   is found traverse is called on that directory. When traverse finds an image 
 *
 *   1 If it has an uppercase extension, or jpeg the pics extension is renamed to lowercase and jpg.
 *   2 If the pic doesn't have a thumbnail one is created.
 *   3 If the pic doesn't have a mid-size version, one is created.
 *   4 If the pic doesn't have a database entry one is created with default data and what little information we do know about the pic at the time.
 *   5 Based on the level of the directories that were recursed over adds a tag for each directory to the image. Allowing for auto tagging depending on where the image resides.
 *
 *@todo would be ideal to see if we can handle gif images ad sell.
 *@param string $p File path to parse if null uses a default directory (most common use case).
 *@param string $type traverse type, default is image2.
 */
	function traverse($p='', $type='image2')
	{
		$parent = $this->getParent();
		$directory = $parent->getDirectory(); 
		$path;
		$sub;
		$medDir = 'med';
		$thumbDir = 'thumb';

		print("<br />Parent is ".$p);
		print("<br />Directory is ".$directory);

		//if $p is not empty then we are dealing with a traverse iterating over a subdirectory under the main image directory
		if(empty($p))
		{
			$p = $directory;
		}
		else
		{
			$path = $p;
			$sub = str_Replace($directory, '', $path);

			//http://stackoverflow.com/questions/1252693/php-str-replace-that-only-acts-on-the-first-match
			$pos = strpos($sub, '/');
			if($pos !== false && $pos === 0) 
			{
			    $sub = substr_replace($sub, '', $pos, strlen('/'));
			}

			if(!empty($sub))
			{
				$medDir .= '/'.$sub;
				$thumbDir .= '/'.$sub;

				if(!file_exists($medDir))
				{
					mkdir($medDir); 
				}

				if(!file_exists($thumbDir))
				{
					print("making thumb directory ".$thumbDir);
					mkdir($thumbDir); 
				}
			}
		}

		$dir = dir($path);
		$files = array();

		if(!in_array($path,$this->searched))
		{
			print '<br />searching '.$path;

			//loop through directories
			while($tmp = $dir->read())
			{		
				if(($tmp != '.')&&($tmp!='..')) //skip current directory pointer, back directory pointer
				{
					//was throwing warnings because the file path wasn't specified for filemtime().
					//http://www.issociate.de/board/post/459798/filemtime%28%29:_stat_failed_for_file.jpg.html
					$files[count($files)] = $path.'/'.$tmp;
				}
			}

			//http://www.computing.net/answers/webdevel/php-sort-directory-contents-by-date/3483.html
			array_multisort(array_map('filemtime', $files), SORT_NUMERIC, SORT_DESC, $files);

			foreach($files as $tmp)
			{
				//stripping the file path that was added.
				$list = explode('/', $tmp);
				$tmp = $list[count($list)-1];

				
				//this caused major headaches it was renaming directories to lowercase, the intent is to rename file extensions to lower case!
				if(is_dir($path.'/'.$tmp)==false){
					$tmp = $this->cleanUpExtension($path,$tmp);
				}


				if(strstr($tmp, '.jpg') || strstr($tmp, '.png') || strstr($tmp, '.gif')) {

					//gather extension
					$extension = 'jpg';

					if(strstr($tmp, '.png')){
						$extension = 'png';
					}

					//run picture compare 				
					if($this->pictureCompare($tmp, $thumbDir,$extension)==false)//looking for a thumbnail
					{
						//create png image this works but it is very memory intensive	
						$this::createThumb($path.'/'.$tmp, $tmp, $this->thumbSize, $thumbDir, $extension, $this->thumbOverride,$this->thumbCompression);
						$this->addEntry('Thumb:&nbsp;&nbsp; '.$tmp);
					}

					if($this->pictureCompare($tmp, $medDir,$extension)==false)//looking for a medium sized image
					{
						//create mid size pic
						$this::createThumb($path.'/'.$tmp, $tmp, $this->mediumSize, $medDir, $extension,'',$this->mediumCompression);
						$this->addEntry('Medium: '.$tmp);
					}

					//this is the database entry check currently there is no follow up action if an entry is added to the database but this could benefit by calling a function callback here.
					if($this->entryCheck($tmp, $path, $sub, $type))
					{
					}
				}
				else if(strstr($tmp, '.swf'))//print('traverse found .swf');
				{
				}
				else
				{
					//making a conscious decision here not to search directories with periods in them.
					if(!strstr($tmp, '.') && is_dir($path.'/'.$tmp))//if not a file with a dot in it it must be a directory
					{
						//recursive part of the function calls traverse on the dir that was found
						$this->traverse($path.'/'.$tmp, $type);
					}
				}
	
			}
			$this->searched[count($this->searched)] = $path;
		}
		else
		{
			print '<br />searched '.$path;
		}
	}

/**
 *
 */
	private function cleanUpExtension($path,$name){

		//grab the extension
		$nameParts = explode('.',$name);

		//grab the extension
		$ext = $nameParts[count($nameParts)-1];

		//backup extension
		
		$bExt = $ext;
		$ext = strtolower(trim($ext));


		//print '<br />'.$bExt;
		if(strcmp($bExt,'jpeg')==0 || strcmp($bExt,'jpe')==0 || strcmp($bExt,'jfif')==0 || strcmp($bExt,'jif')==0 )
		{
			//print ' renaming file extension';
			$ext = 'jpg';
		}

		if(strcmp($bExt,$ext)!=0){
			$newName = '';

			for($i=0;$i<count($nameParts);$i++){

				if(!empty($newName)){
					$newName .= '.';
				}

				if($i+1<count($nameParts)){
					$newName .= $nameParts[$i];
				}else{
					$newName .= $ext;
				}
			}
			//print 'fail';
			print '<br />renaming '.$name.' '.$newName;

			rename(($path.'/'.$name), ($path.'/'.$newName)); //sets the name
		}
		
		return $name;
	}


/**
 *   Takes a picture file as an argument searches a directory for a matching file if found returns true.
 *@param string $pic Name of the image to be searched for
 *@param string $tmpDir directory on which to look for the image.
 *@return boolean If match found returns true.
 */
	function pictureCompare($pic, $tmpDir,$extension = 'jpg')
	{
		//explode on the dot
		$find = explode('.', $pic);

		$count = count($find)-1;
		$concat = "";

		//this sillyness is to account for files with multiple periods in the name.
		for($i=0;$i<$count;$i++)
		{
			if(!empty($concat))
			{
				$concat .= '.';
			}
			$concat .= $find[$i];		
		}

		return file_exists($tmpDir.'/'.$concat.".".$extension);
	}


/**
 *   Creates a sub image at the set size, and places it into a directory specified.
 *@todo default image type should probably be jpg.
 *@todo Override needs to be better explained.
 *@param image $pic Image object
 *@param string $name Name of the file
 *@param int $optimumSize Pixel size.
 *@param string $dest Destination file path.
 *@param string $type extension output type, default is png (should probably be jpg).
 *@param string override
 */
	static function createThumb($pic, $name, $optimumSize, $dest, $type='png', $override = '',$compression=null)
	{
		//this should be commented out in production scripts
		//ini_set("memory_limit", "1024M");

		//array of image dimensions
		$size =getimagesize($pic);

		$ratio=0;

		$width = $size[0];
		$height = $size[1];

		$newHeight = 0;
		$newWidth = 0;

		$src;

		$ext = explode('.', $pic);
		$ext = $ext[count($ext)-1];

		Traverse::setResizeDimensions($width,$height,$optimumSize,$override,$ratio,$newWidth,$newHeight);

		//Traverse::CreateResizedImage();	

		if(strcmp($ext, 'jpg')==0)
		{
			$src = imagecreatefromjpeg($pic);
		}
		else if(strcmp($ext, 'png')==0)
		{
			$src = imagecreatefrompng($pic);
		}
		else if(strcmp($ext, 'gif')==0){
			$src = imagecreatefromgif($pic);
		}

		$dst = imagecreatetruecolor($newWidth, $newHeight);

		if(strcmp($type, 'png')==0){
			imagealphablending( $dst, false );
			imagesavealpha( $dst, true );
		}

		if(imagecopyresampled($dst, $src, 0, 0, 0, 0, $newWidth, $newHeight, $width, $height))
		{
			//print 'resized image';
			imagedestroy($src);
		}

		if(strcmp($type, 'png')==0)
		{
			$compression = 9;

			if(imagepng($dst, $dest.'/'.str_replace($ext, 'png', $name), $compression))
			{
				//print 'created image';
				imagedestroy($dst);
			}
		}
		else if(strcmp($type, 'jpg')==0)
		{
			if(empty($compression)){
				$compression = 70;
			}
			if(imagejpeg($dst, $dest.'/'.str_replace($ext, 'jpg', $name), $compression))
			{
				//print 'created image';
				imagedestroy($dst);
			}
		}else if(strcmp($type,'gif')==0){
			if(imagegif($dst, $dest.'/'.str_replace($ext, 'gif', $name)))
			{
				//print 'created image';
				imagedestroy($dst);
			}

		}
	}


/**
 *
 */
	static function setResizeDimensions($width,$height,$optimumSize,$override,&$ratio,&$newWidth,&$newHeight){

		//longer than wide
		if($width>=$height)
		{
			$ratio = $width / $optimumSize;
			$newWidth = $optimumSize;
			$newHeight = $height / $ratio;
		}
		else //wider than long
		{
			$ratio = $height / $optimumSize;
			$newWidth = $width / $ratio;
			$newHeight = $optimumSize;
		}

		//setting height so it is always a set size
		if(strcmp($override, 'height')==0)
		{
			$ratio = $height / $optimumSize;
			$newWidth = $width / $ratio;
			$newHeight = $optimumSize;
		} else if(strcmp($override, 'width')==0){

			//print "override width";
			$ratio = $width / $optimumSize;
			$newWidth = $optimumSize;
			$newHeight = $height / $ratio;			
		}

		//safety check to make sure were not upscaling images (which looks terrible)
		if($newHeight > $height || $newWidth > $width)
		{
			$newHeight = $height;
			$newWidth = $width;
		}

	}

/**
 *This now excludes images with a delete status.
 *@param string $name
 *@param string $path
 *@param string sub Sub directory to scan
 *@param string $type Value passed to the database as the type. 
 *@see http://php.net/date.timezone
 */
	function entryCheck($name, $path, $sub='', $type='image')
	{
		$returner = false;
		$createEntry = false;
		$mysqli = $this->getParent('page')->getConnect()->getMysqli();
		$site = $this->getParent('page')->getSettings()->getSite();

		$mSub='';

		if(!empty($sub))
		{
			$mSub = $sub.'/';
		}

		//looking for a tblpicture match
		$query='SELECT name, DATE_FORMAT(date, \'%m/%d/%Y\'), `status` FROM tblimage WHERE name=? AND site=?';

		if($stmnt = $mysqli->prepare($query))
		{
			$mName = $mSub.$name;
			$stmnt->bind_param('ss', $mName, $site);
			$stmnt->execute();
			$stmnt->bind_result($name, $date, $status);
					
			$createEntry = true;

			while($stmnt->fetch())
			{
				$createEntry=false;
				if(strcmp($status, 'active')==0 or strcmp($status, 'edit')==0)
				{
					$returner = true;
				}
			}
		}

		if($createEntry)
		{
			$insertId;
			$query='INSERT INTO tblimage (name, path, site, type) VALUES (?, ?, ?, ?)';

			if($stmnt = $mysqli->prepare($query))
			{
				$mName = $mSub.$name;	
				$epath = $path.'/'.$name;
				$stmnt->bind_param('ssss', $mName, $epath, $site, $type);
				$stmnt->execute();
						
				if($stmnt->affected_rows > 0)
				{
					$this->debug('inserting entry into tblimage '.$name);
					$this->addEntry('DB: '.$name);
					//throws an e-warning in php for date() if date.timezone is not set in php.ini http://php.net/date.timezone (recommended that you set a timezone) default is UTC if not set. 
					$returner = true;

					$insertId = $stmnt->insert_id;
				}				
			}

			//add tags if any
			if(!empty($sub) && !empty($insertId))
			{
				$sub = explode('/', $sub);
				$parent = $this->getParent();
				$parent->queryTags(false);
				$tagList = $parent->getTagList();

				$modSubFlag = $this->getParent('page')->getSettings()->getModSub();

				foreach($sub as $s)
				{
					$modSub = $s;

					if($modSubFlag){
						$modSub = str_replace(' ', '_', trim(strtolower($s)));
					}

					$query = 'INSERT INTO `tags` (`rid` ,`tname`) VALUES (?, ?);';

					if($stmnt = $mysqli->prepare($query))
					{

						$stmnt->bind_param('ss', $insertId, $modSub);
						$stmnt->execute();
						if($stmnt->affected_rows > 0)
						{
							//print '<br /> image list updated';
						}
					}

					//if neccessary add directory to tag
					if(!in_array($modSub, $tagList))
					{
						//print '<br />need to add tag to primary list';
						$query = 'INSERT INTO `tag` (`name`, `component`) VALUES (?, \'gallery2\');';

						if($stmnt = $mysqli->prepare($query))
						{
							$stmnt->bind_param('s', $modSub);
							$stmnt->execute();
							if($stmnt->affected_rows > 0)
							{
								//print '<br /> primary list updated';
							}
						}
					}
				}
			}					
		}
		return $returner;
	}


/**
 *ParseFeed is currently for parsing youtube RSS feeds and adding the entires to our gallery database.
 */
	function parseFeed()
	{
		$parent = $this->getParent();
		$feed = $parent->getFeed();
		$reader = new Reader();
		$reader->setParent($this);
		$reader->addFeed('YoutubeGallery', 'YoutubeGallery', $feed);

		$reader->process();
		$children = $reader->getChildren();
		foreach($children as $child)
		{
			$this->parseItem($child);
		}
		$parent->setLoad(false);
	}


/**
 *The video information that's being stored in the database is being parsed out here.
 *http://stackoverflow.com/questions/3627489/php-parse-html-code
 *@param Item $item Feed item.
 */
function parseItem($item)
{

	$parent = $this->getParent();
	$tagList = $parent->getTagList();
	$createEntry=true;
	$mysqli = $this->getParent('page')->getConnect()->getMysqli();
	$site = $this->getParent('page')->getSettings()->getSite();

	//vid hash link
	$link = $item->getLink();
	$parse = parse_url($link);
    	if(isset($parse['query'])) {
        	parse_str(urldecode($parse['query']), $parse['query']);
    	}
	$link = $parse['query']['v'];

	//title & description
	$DOM = new DOMDocument;
	$DOM->loadHTML($item->getDescription());

	$items = $DOM->getElementsByTagName('div');

	$title = $items->item(2)->nodeValue;
	$description = $items->item(3)->nodeValue;

	//date
	$tmpDate = strtotime($item->getPubDate());	
	$date = date("Y-m-d g:i:s", $tmpDate);


	//test to see if in database already
	$query='SELECT name FROM tblimage WHERE name=? AND site=?';

	if($stmnt = $mysqli->prepare($query))
	{
		$stmnt->bind_param('ss', $link, $site);
		$stmnt->execute();
		$stmnt->bind_result($name);
					
		$createEntry = true;

		while($stmnt->fetch())
		{
			$createEntry=false;
		}
	}


	//if not add to database
	if($createEntry)
	{
		$insertId;
		$query='INSERT INTO tblimage (name, title, description, date, site, type) VALUES (?, ?, ?, ?, ?, ?)';

		if($stmnt = $mysqli->prepare($query))
		{	
			$type= 'video';
			$stmnt->bind_param('ssssss', $link, $title, $description, $date, $site, $type );
			$stmnt->execute();
					
			if($stmnt->affected_rows > 0)
			{
				$this->debug('inserting entry into tblimage '.$name);
				$insertId = $stmnt->insert_id;
			}				
		}

		//add video tag
		$tag = 'video';
		if(!empty($insertId))
		{
		
			$query = 'INSERT INTO `tags` (`rid`, `tname`) VALUES (?, ?)';
			if($stmnt = $mysqli->prepare($query))
			{
				$stmnt->bind_param('ss', $insertId, $tag);
				$stmnt->execute();
				if($stmnt->affected_rows > 0)
				{
					//print '<br /> image list updated';
				}
			}

			//if neccessary add directory to tag
			if(!in_array($tag, $tagList))
			{
				//print '<br />need to add tag to primary list';
				$query = 'INSERT INTO `tag` (`name`, `component`) VALUES (?, \'gallery2\');';

				if($stmnt = $mysqli->prepare($query))
				{
					//print 'query is ready to go so far';
					$stmnt->bind_param('s', $tag);
					$stmnt->execute();
					if($stmnt->affected_rows > 0)
					{
						//print '<br /> primary list updated';
					}
				}
			}
		}			
	}
}


/**
 *Sets the traverseImage flag.
 *@param boolean $ti
 */
	function setTraverseImage($ti)
	{
		$this->traverseImage = $ti;
	}

/**
 *Sets the traverseImage flag.
 *@return boolean
 */
	function getTraverseImage()
	{
		return $this->traverseImage;
	}


/**
 *Controls when traverse is called , in particular traverse is only called when a new image is uploaded or a logged in admin user requests a forceload.
 *@todo should actualy enforce that forcecheck is being performed by a user with correct permissions.
 */
	function traverseCheck()
	{
		$page = $this->getParent('page');
		$account = $page->getAccount();
		if($this->traverseImage && $account->access($this->getLevel()))
		{
			$parent = $this->getParent();
			$directory = $parent->getDirectory();
			$spriteDirectory = $parent->getSpriteDirectory();
			$feed = $parent->getFeed();

			//print $directory.' ';
			if(file_exists('upload/files')){
				//print 'I should move images from the upload path';
				$this->moveUploads();
			}
			

			//only use this in circumstances where I know I'm uploading from a certain place
			if(!empty($_REQUEST['path']))
			{
				$this->traverse($_REQUEST['path']);
			}
			else if(!empty($directory))//default behavior
			{
				//print 'calling traverse';
				$this->traverse($directory);
			}

			//searches for rotating images in the designated sprites directory
			if(!empty($spriteDirectory))
			{
				$this->traverse($spriteDirectory, 'imageRotate');
			}

			//searches for youtube videos if an rss feed is specified.
			if(!empty($feed))
			{
				$this->parseFeed();
			}

			$this->traverseImage = false;
		}
	}

/**
 *
 */
	function moveUploads()
	{
		$parent = $this->getParent();
		$directory = $parent->getDirectory(); 

		$path='upload/files';

		//mve uploaded files
		if(file_exists('upload/files'))
		{
			$upload = dir('upload/files');
			$files = array();

			//loop through directories
			while($tmp = $upload->read())
			{		
				if(($tmp != '.')&&($tmp!='..') && ($tmp != 'thumbnail') && (is_dir($path.'/'.$tmp)==false)) //skip current directory pointer, back directory pointer
				{
					//$files[count($files)] = $path.'/'.$tmp;
					//print '<br />'.$path.'/'.$tmp.' move over: '.$directory.'/'.$tmp;

					if(rename($path.'/'.$tmp, $directory.'/'.$tmp))
					{
						echo 'moved: '.$tmp.'<br />';
					}
					else
					{
						throw new Exception('failed to move: '.$path.'/'.$tmp.' to '.$directory.'/'.$tmp);
					}
				}
			}
		}

		//clear thumbnail directory
		if(file_exists('upload/files/thumbnail'))
		{
			
			$thumbPath='upload/files/thumbnail';
			$thumbnail = dir($thumbPath);
			while($tmp = $thumbnail->read())
			{
				print 'thumb: '.$tmp.' <br />';		
				if(($tmp != '.')&&($tmp!='..')) //skip current directory pointer, back directory pointer
				{
					if(is_file($thumbPath.'/'.$tmp)){
    						unlink($thumbPath.'/'.$tmp); // delete file
					}
				}
			}
			
		}
	}


/**
 *Adds a log entry.
 *@param string $entry
 */
	function addEntry($entry)
	{
		if(!empty($entry))
		{
			$count = count($this->log);	
			$this->log[$count] = $entry;
		}
	}

/**
 *Outputs the log arraylist.
 */
	function printLog()
	{
		foreach($this->log as $entry)
		{
			echo '<br />'.$entry;
		}
	}

/**
 *Set optimum medium size
 *@param int $s
 */
	function setMediumSize($s)
	{
		$this->mediumSize = $s;
	}

/**
 *Set optimum thumb size
 *@param string $s
 */
	function setThumbSize($s)
	{
		$this->thumbSize = $s;
	}

	function setThumbCompression($c){
		$this->thumbCompression = $c;
	}

	function setMediumCompression($c){
		$this->mediumCompression = $c;
	}

	function setThumbOverride($override){
		$this->thumbOverride = $override;
	}
}

?>
