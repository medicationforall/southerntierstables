<?php

/**
 *   Medication For All Framework source file Image,
 *   Copyright (C) 2009-2012  James M Adams
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
 *   Represent an image in a gallery, which means each image has a corresponding previous, next image, and a gallery origin page in which it belongs.<br />
 *   All of this data is kept track of in Settings.
 *
 *   Sample Usage {@link http://www.medicationforall.com/framework/sample/SampleImage.php SampleImage}
 *
 *   {@example ../sample/SampleImage.php SampleImage}
 *
 *@todo Selenium test should also test for, description, mediu, title.
 *
 *@author James M Adams <james@medicationforall.com>
 *@version 0.3
 *@package framework
 */

class Image2 extends Tag
{
//data
	/**
	 *File Name
	 *@access protected
	 *@var string
	 */
	protected $fileName;

	/**
	 *File Name Resized
	 *@access protected
	 *@var string
	 */
	protected $fileNameResized;

	/**
	 *File Path.
	 *@access protected
	 *@var string
	 */
	protected $file;

	/**
	 *File path delineation.
	 *@access private
	 *@var array
	 */
	private $fileParts = array();

	/**
	 *Title.
	 *@access protected
	 *@var string
	 */
	protected $title;

	/**
	 *Date.
	 *@access private
	 *@var string
	 */
	private $date;

	/**
	 *Medium.
	 *@access private
	 *@var string
	 */
	private $medium;

	/**
	 *Description.
	 *@access protected
	 *@var string
	 */
	protected $description;

	/**
	 *For.
	 *@access private
	 *@var string
	 */
	private $ifor;


	/**
	 *File Path
	 *@access private
	 *@var string
	 */
	protected $path;

	/**
	 *Selected Flag
	 *@access protected
	 *@var Boolean
	 */
	protected $selected = false;

	/**
	 *View Thumb Flag
	 *@access private
	 *@var boolean
	 */
	private $viewThumb = false;


	/**
	 *Data Loaded flag
	 *@access private
	 *@var boolean
	 */
	private $loaded = false;
	private $degreeOffset = 0;

	private $version = 0;

	function json() {
		echo '{"name":'.json_encode($this->fileName).',';
		if($this->title){
			echo '"title":'.json_encode($this->title).',';
		}

		if($this->description){
			echo '"description":'.json_encode($this->description).',';
		}

		if($this->medium){
			echo '"medium":'.json_encode($this->medium).',';
		}
		echo '"path":'.json_encode($this->path).',';
		echo '"version":'.json_encode($this->version).',';
		echo '"date":'.json_encode($this->date).',';
	
		$counter =0;
		echo '"tags":[';
			foreach($this->tags as $tag)
				{
					$counter++;
					if($counter < count($this->tags))
					{
						echo json_encode($tag).',';
					}else{
						echo json_encode($tag);
					}
	
				};
		echo ']';
		echo '}';
		return true;
		}


//constructor
/**
 *Creates the Component Image Object.
 *@param string $h Header Text.
 *@param string $type Component type. Default is image2
 */
	function __construct($h='', $type='image2')
	{
		//print 'calling image constuctor';
		parent::__construct($h, $type);
	}

//methods
/**
 *   Processes the input data.
 *@param boolean $processChildren Process Children Flag.
 *@todo Meta description can't process html tags within the body of it, so when passing the image description to the meta description I have to parse out html tags. This will rely on making parse work first.
 *@todo Break process up into smaller methods, or at least comment better.
 */
	function process($processChildren=true)
	{
		$mysqli = $this->getParent('page')->getConnect()->getMysqli();
		$page = $this->getParent('page');
		$account = $page->getAccount();
		$token = $account->getToken();
		$site = $this->getParent('page')->getSettings()->getSite();

		$this->message = '';

		//delete image
		if(!empty($_REQUEST['deleteId']) && strcmp($_REQUEST['deleteId'], $this->id)==0)//file not empty
		{
			$this->deleteImage();
		}

		//matches image id
		if(!empty($_REQUEST['imageId']) && strcmp($_REQUEST['imageId'], $this->id)==0)
		{		
			//SET IMAGE ENTRY DATA
			if((!empty($_REQUEST['imageentry'])) && strcmp($_REQUEST['imageentry'], 'imageentry')==0)
			{
				$this->saveImage();
			}

			//rotate if not submitting the saved changes
			if(!empty($_REQUEST['rotateClockwise']) && strcmp($_REQUEST['rotateClockwise'],'true')==0){
				$this->rotateClockwise();
			}

			if(!empty($_REQUEST['rotateCClockwise']) && strcmp($_REQUEST['rotateCClockwise'],'true')==0){
				$this->rotateCClockwise();
			}
		}

		if($this->loaded == false)
		{
			$this->load();
			//print 'calling load';
			$this->loaded = true;
		}

		if($processChildren)
		{
			$this->children('process');
		}
	}

/**
 *
 */
	function saveImage(){

		//saves the angle of rotation by reprocessing the image at the specified angle.
		if($this->degreeOffset != 0){
			$this->saveImageRotate();
		}

		$mysqli = $this->getParent('page')->getConnect()->getMysqli();
		$site = $this->getParent('page')->getSettings()->getSite();
		$this->message = '';

		//print ' attempting to update image data';
		$query = 'UPDATE tblimage SET `title`=?, `date`=?, `description`=?,version=? WHERE id=? AND site =?';

		if($stmnt = $mysqli->prepare($query))
		{
			$this->title = $this->parse($_POST['title']);
			$this->date = $this->parse($_POST['date']);
			$this->description = $this->parse($_POST['description']);

			$stmnt->bind_param('ssssss', $this->title, $this->date, $this->description, $this->version, $this->id, $site);
			$stmnt->execute();
	
			//If the data doesn't change, mysql may not change the row. Can't rely on effected rows check.
			$this->message = '<div class="confirm">Applied update</div>';
			$this->getParent('componentGallery2')->setReload(true);
			$_REQUEST['imageentry'] = null;
			$_REQUEST['selectImage'] = $this->id;
		}

		//updates and saves the tags.
		$this->handleTags($this->getParent('componentGallery2'));


	}

/**
 *
 */
	function saveImageRotate(){


		$layer = ImageWorkshop::initFromPath($this->path);
		$layer->rotate($this->degreeOffset);
		print "calling image rotate";

		$realPath = $this->getRealPath();
		$createFolders = false;
		$backgroundColor = null;
		$imageQuality = 100;

		$layer->save($realPath, $this->fileName, $createFolders, $backgroundColor, $imageQuality);

		$gallery = $this->getParent('componentGallery2');

		if(!empty($gallery)){

			$fileName =$this->fileName;

			//create thumb
			Traverse::createThumb($this->path, $fileName, $gallery->getThumbSize(), 'thumb', 'jpg', 'height');

			//create medium
			Traverse::createThumb($this->path, $fileName, $gallery->getMediumSize(), 'med', 'jpg');
		}

		$this->degreeOffset = 0;

		$this->version++;
	}

/**
 *
 */
	function deleteImage(){
		$mysqli = $this->getParent('page')->getConnect()->getMysqli();
		$page = $this->getParent('page');
		$account = $page->getAccount();
		$token = $account->getToken();
		$site = $this->getParent('page')->getSettings()->getSite();
		$this->message = '';

		if($account->access('admin'))
		{
			if(!empty($_REQUEST['deleteImage']) && strcmp($_REQUEST['deleteImage'], 'true') == 0)
			{
				//print 'attempt to delete image';

				$type = $this->getType();

				if(((!empty($_REQUEST[$type.'DelConf']) && strcmp($_REQUEST[$type.'DelConf'], 'true')==0) ||(!empty($_REQUEST['imageRotateDelConf']) && strcmp($_REQUEST['imageRotateDelConf'], 'true')==0) || (!empty($_REQUEST['videoDelConf']) && strcmp($_REQUEST['videoDelConf'], 'true')==0) ) && !empty($_REQUEST['token']) && strcmp($_REQUEST['token'], $token)==0)
				{
					//print 'attempt to delete image part two';
					$query = 'UPDATE tblimage SET `status`=\'delete\' WHERE id=? AND site=?';

					if($stmnt = $mysqli->prepare($query))
					{
						$stmnt->bind_param('ss', $this->id, $site);
						$stmnt->execute();

						if($stmnt->affected_rows > 0 )
						{
							$this->message = '<div class="confirm">Image '.$this->fileName.' deleted.</div>';
							$this->getParent('componentGallery2')->setReload(true);
						}
						else
						{
							$this->message = '<div class="error">Image '.$this->fileName.' could not be deleted.</div>';
						}
						$this->delete = false;
					}
				}
				else if((!empty($_REQUEST[$type.'DelConf']) && strcmp($_REQUEST[$type.'DelConf'], 'false')==0))
				{
					$this->delete = false;
				}
				else if(empty($_REQUEST['image2DelConf']))
				{
					$this->delete = true;
				}
			}
		}
	}


/**
 *
 */
	function getRealPath(){
		//get real path
		$pathParts = explode('/',$this->path);
		$realPath = '';
		for($i=0;$i < count($pathParts);$i++){
			if($i+1<count($pathParts)){
				$realPath.=$pathParts[$i];

				if(!empty($realPath) && $i+2<count($pathParts)){
					$realPath .='/';
				}
			} 
		}
		return $realPath;
	}


/**
 *
 */
	function rotateClockwise(){
		//print 'rotate clockwise';
		$this->degreeOffset += 90;
	}

/**
 *
 */
	function rotateCClockwise(){
		//print 'rotate counter clockwise';

		$this->degreeOffset -= 90;
	}

/**
 *   Load image entry data.
 */
	function load()
	{
		$mysqli = $this->getParent('page')->getConnect()->getMysqli();
		$query = 'SELECT id, title, date, medium, description, `for` FROM tblimage WHERE name=? AND site=?';
		$site = $this->getParent('page')->getSettings()->getSite();

		if($stmnt=$mysqli->prepare($query))
		{
			$stmnt->bind_param('ss', $this->fileName, $site);
			$stmnt->execute();
			$stmnt->bind_result($id, $bTitle, $bDate, $bMedium, $bDescription, $bFor);

			while($stmnt->fetch())
			{
				$this->title=$bTitle;
				$this->date=$bDate;
				$this->medium=$bMedium;
				$this->description=$bDescription;
				$this->ifor=$bFor;
			}
		}

		$this->loadTags($this->getParent('componentGallery2'));
	}

/**
 *   Prints the image and link to larger version.
 */
	function image()
	{
		echo '<div class="image">';

		if(!empty($this->file))//file not empty
		{
			if(strstr($this->fileParts[count($this->fileParts)-1], '.jpg'))
			{
				echo '<a href="'.$this->file.'" target="_blank"><img src="med/'.str_replace('"','&quot;',$this->fileParts[count($this->fileParts)-1]).'" /></a>';
			}
			else if(strstr($this->fileParts[count($this->fileParts)-1], '.png'))
			{
				echo '<a href="'.$this->file.'" target="_blank"><img src="med/'.str_replace('"','&quot;',$this->fileParts[count($this->fileParts)-1]).'" /></a>';
			}
			else if(strstr($this->fileParts[count($this->fileParts)-1], '.swf'))
			{
				echo '<object classid="clsid:d27cdb6e-ae6d-11cf-96b8-444553540000" codebase="http://download.macromedia.com/pub/shockwave/cabs/flash/swflash.cab#version=9,0,0,0" width="450" height="300" id="Untitled-1" align="middle">';
				echo '<param name="allowScriptAccess" value="sameDomain" />';
				echo '<param name="allowFullScreen" value="false" />';
				echo '<param name="movie" value="'.$this->file.'" /><param name="quality" value="high" /><param name="bgcolor" value="#ffffff" /><embed src="'.$this->file.'" quality="high" bgcolor="#ffffff" width="450" height="300" name="'.$this->file.'" align="middle" allowScriptAccess="sameDomain" allowFullScreen="false" type="application/x-shockwave-flash" pluginspage="http://www.macromedia.com/go/getflashplayer" />';
				echo '</object>';
			}
		}
		echo '</div>';
	}

/**
 *   Content displayed to the user. Needs to be refactored to separate the edit mode from the display mode.
 */
function cContent()
{
	$size =getimagesize('med/'.$this->fileNameResized);

	echo '<div class="cContent selected" style="max-width:'.$size[0].'px;'.$this->outOfBoundsPadding().'">';
	echo '<a href="'.$this->path.$this->printVersion().'"><img class="mainImage" '.$this->styleDegreeOffset().' alt="'.$this->fileName.'" src="med/'.str_replace('"','&quot;',$this->fileNameResized.$this->printVersion()).'"></a>';
	echo '</div>';

	$this->printInfo();
}

/**
 *
 */
	function printVersion(){
		$version = '';

		if($this->version > 0){
			$version = '?v='.$this->version;
		}

		return $version;
		
	}


/**
 * Padding added to the image to make up for the angle of rotation.
 */
function outOfBoundsPadding(){

	$style = '';
	$size =getimagesize('med/'.$this->fileNameResized);

	if($this->degreeOffset % 180 != 0 && $size[0] != $size[1]){
		//print 'out of bounds '.$size[0].' '.$size[1];

		$lr= 0;
		$tb=0;
		if($size[0] < $size[1]){
			$lr = ($size[1] - $size[0]) / 2;
			$tb = $lr * -1;
		} else if($size[0] > $size[1]){
			$tb =  ($size[0] - $size[1]) / 2;
			$lr = $tb * -1;
		}
		$style='margin-top:'.($tb+20).'px;margin-right:'.$lr.'px;margin-bottom:'.$tb.'px;margin-left:'.$lr.'px';
	} 

	return $style;
}

/**
 * rotates the image based on the degree offset.
 */
function styleDegreeOffset(){
	$style='';

	if($this->degreeOffset!=0){
		$style = 'style="transform: rotate('.$this->degreeOffset.'deg); -webkit-transform:rotate('.$this->degreeOffset.'deg)"';
	}

	return $style;
}

/**
 * Writes the info div.
 */
	function printInfo()
	{
		if($this->getParent('page')->getAccount()->isEdit()== false && (!empty($this->title) || !empty($this->description) || !empty($this->tags) ))
		{
			echo '<div class="info">';
			//print 'version: '.$this->version;
			if(!empty($this->title))
			{
				echo '<h6>Title:</h6>'.$this->title;
			}

			if(!empty($this->description))
			{
				echo '<h6>Description:</h6> '.$this->description;
			}

			if(!empty($this->tags))
			{
				$gal = $this->getParent('componentGallery2');
				$count = count($gal->getChildren());
				$gTags = $gal->getTags();
				$gIgnore = $gal->getIgnore();
				echo '<h6>Tags:</h6> ';

				sort($this->tags);
				//print_r($this->getParent('componentGallery2')->getTags());

				foreach($this->tags as $tag)
				{
					if(in_array($tag, $gTags) && in_array($tag, $gIgnore))
					{
						echo '<span class="selectedTag">'.$tag.'</span>';//.' <a class="stickyLink" href="?removeIgnore='.$tag.'" title="Always show">+</a></span> ';
					}
					else if(in_array($tag, $gTags))
					{
						echo '<span class="selectedTag">'.$tag.'</span> ';
					}
					else
					{
						//should be able to set the count that sets off the + icon.
						if(!empty($_REQUEST['tag']) && $count > 25)
						{
							echo ' <a class="stickyLink" href="?tag='.urlencode(urldecode($_REQUEST['tag'].', '.$tag)).'" title="Add '.htmlspecialchars($tag).' to tags">+</a>'.'<a href="?tag='.urlencode($tag).'">'.$tag.'</a>';
						}
						else
						{
							echo '<a href="?tag='.urlencode($tag).'">'.$tag.'</a>';
						}
					}
				}
			}
			echo '</div>';
		}
	}

/**
 *   Right now overrides the default edit mode and just displays whats normally shown in show().
 */
	function edit()
	{
		$this->cHeader();
		$this->eContent();
		$this->cFooter();
	}

/**
 *   Editable content mode form.
 */
 	function eContent()
 	{
		if($this->delete)
		{
			$this->deleteConfirm($this->fileName);
		}

		$this->cContent();

		echo '<div class="info">';
		//print 'version: '.$this->version;

		$this->image();

		echo($this->message);
			echo '<form action="" method="POST">';
			echo '<input type="hidden" name="imageentry" value="imageentry"></input>';

			echo '<input type="hidden" name="imageId" value="'.$this->id.'"></input>';

			echo '<div style="border:none">Title:<br /><input type="text" name="title" value="'.$this->title.'"></input></div>';
			echo '<div style="border:none">Date:<br /><input type="text" name="date" value="'.$this->date.'"></input></div>';
			echo '<div style="border:none">Description:<br /><textarea  name="description">'.$this->description.'</textarea></div>';
			echo '<div style="border:none">Tags:<br /><textarea autofocus class="imageTagger" name="updateTags">'.$this->printTags().'</textarea></div>';

			$this->additionalFields();

			echo '<div style="border:none"><input class="imageSubmit" type="submit" value="submit"></input></div>';
			echo '</form>';
			$page = $this->getParent('page');
			$account = $page->getAccount();
			$token = $account->getToken();
			$tags ="";

			if(!empty($_REQUEST['tag']))
			{
				$tags = '&tag='.urldecode($_REQUEST['tag']);
			}

			echo '<a href="?deleteId='.$this->id.'&amp;deleteImage=true&amp;token='.$token.$tags.'" class="deleteLink">Delete Image</a>';

			$this->children('show');
		echo '</div>';

		$this->message = '';
 	}

/**
 *Placeholder function for classes that extend image2 to include additional info fields.
 */
	function additionalFields()
	{

		echo '<div class="rotateOptions">';
		echo '<a class="rotateCClockwise" href="?rotateCClockwise=true&imageId='.$this->id.'">Counter-Clockwise Rotate</a>';
		echo '<a class="rotateClockwise"href="?rotateClockwise=true&imageId='.$this->id.'">Clockwise Rotate</a>';
		echo '</div>';

	}

 /**
  *   Ajax request handler.
  */
  	function short()
  	{
		//print 'calling image short'.$this->id;
		//$this->process();
		//$this->show();


		if(!empty($_REQUEST['imageId']) && strcmp($_REQUEST['imageId'], $this->id)==0)
		{
			//rotate if not submitting the saved changes
			if(!empty($_REQUEST['rotateClockwise']) && strcmp($_REQUEST['rotateClockwise'],'true')==0){
				$this->rotateClockwise();
				$this->cContent();
			}

			if(!empty($_REQUEST['rotateCClockwise']) && strcmp($_REQUEST['rotateCClockwise'],'true')==0){
				$this->rotateCClockwise();
				$this->cContent();
			}

		}
  	}



/**
 *Set the file Name.
 *@param string $name Name of the file.
 */
	function setFileName($name)
	{
		$this->fileName = $name;

		$part = explode('.', $name);

		$count = count($part) -1;
		$this->fileNameResized='';
		

		for($i=0; $i<$count;$i++)
		{

			if(!empty($this->fileNameResized))
			{
				$this->fileNameResized .= '.';
			}
			$this->fileNameResized .= $part[$i];
		}



		if(strcmp($part[$count],'png')==0){
			$this->fileNameResized .='.png';
		}else{
			$this->fileNameResized .='.jpg';
		}

	}

/**
 *Set the title.
 *@param string $title Title text of the image.
 */
	function setTitle($title)
	{
		if(!empty($title))
		{
			$this->title =$title;
		}
	}

/** 
 *Set the file path of the image.
 *@param string $path File path.
 */
	function setPath($path)
	{
		$this->path = $path;
	}

/**
 *
 */
	function setVersion($v){
		$this->version = $v;
	}


/**
 *Set the selected image flag.
 *@param boolean $selected Selected flag.
 */
	function setSelected($selected)
	{
		$this->selected = $selected;
	}

/**
 *Gets the seleced image flag.
 *@todo test and remove.
 *@return boolean
 */
	function getSelected()
	{
		$this->debug('deprecated: called getSelected, use isSelected() instead.');
		return $this->isSelected();
	}

/**
 *Gets the seleced image flag.
 *@return boolean
 */
	function isSelected()
	{
		return $this->selected;
	}

/**
 *Set view thumb flag.
 *@param boolean $view
 */
	function setViewThumb($view)
	{
		$this->viewThumb = $view;
	}

/**
 *Set the date.
 *http://php.net/manual/en/function.strtotime.php
 *@param string $d
 */
	function setDate($d)
	{
		$tmpDate = strtotime($d);
		
		$this->date = date("Y-m-d g:i:s", $tmpDate);
	}

/**
 *Set Description.
 *@param string $d
 */
	function setDescription($d)
	{
		$this->description = $d;
	}
/**
 *Print the image thumg.
 */
	function showThumb()
	{	
		$id = 'id="image-'.$this->id.'"';
		if($this->selected)
		{
			echo'<div '.$id.' class="thumb selectedThumb"><img src="thumb/'.str_replace('"','&quot;',$this->fileNameResized.$this->printVersion()).'" /></div>';	
		}
		else
		{
			$tag ='';
			$gal = $this->getParent('ComponentGallery2');

			if(!empty($gal))
			{
				$tagsToPrint = $gal->printTags();
				if(!empty($tagsToPrint))
				{
					$tag = '&tag='.htmlspecialchars($tagsToPrint);
					//print 'found gallery';
				}
			}
			echo '<div '.$id.' class="thumb" ><a  href="'.$this->geGallerytRefPage().'?selectImage='.$this->id.$tag.'"><img src="thumb/'.str_replace('"','&quot;',$this->fileNameResized.$this->printVersion()).'" alt="'.$this->fileName.'" /></a></div>';
		}
	}

	function geGallerytRefPage(){
		$ref = $this->getParent('componentGallery2')->getRefPage();

		if(!empty($ref)){
			return $ref;
		}

		return '';
	}

/**
 *Print the thumb without the thumbnail image. Odd yes but this is used in conjunction with jquery lazyload plugin.
 */
	function showThumbQuick()
	{
		$id = 'id="image-'.$this->id.'"';
		$placeHolderWidthHeightFlag = $this->getParent('page')->getSettings()->getPlaceHolderWidthHeight();

		$size =getimagesize('thumb/'.$this->fileNameResized);

		$style='';

		if($placeHolderWidthHeightFlag){
			$style = 'style="width:'.$size[0].'px;height:'.$size[1].'px"';
		}

		if($this->selected)
		{
			echo '<div '.$id.' class="thumb selectedThumb" '.$style.'><div class="placeholder" style="width:'.$size[0].';height:'.$size[1].'"><img class="lazy" src="image/grey.gif" data-original="thumb/'.str_replace('"','&quot;',$this->fileNameResized.$this->printVersion()).'"></div></div>';
		}
		else
		{
			$tag ='';
			$gal = $this->getParent('ComponentGallery2');

			if(!empty($gal))
			{
				$tagsToPrint = $gal->printTags();
				if(!empty($tagsToPrint))
				{
					$tag = '&tag='.$tagsToPrint;
					//print 'found gallery';
				}
			}
			echo '<div '.$id.' class="thumb" '.$style.'><a  href="'.$this->geGallerytRefPage().'?selectImage='.$this->id.$tag.'" ><img class="lazy" src="image/grey.gif" data-original="thumb/'.str_replace('"','&quot;',$this->fileNameResized.$this->printVersion()).'"></a></div>';
		}
	}



/**
 *Print the RSS output.
 */
	function rss()
	{
		$tag ='';

		$tagList = $this->getParent('componentGallery2')->getTags();

		if(!empty($tagList))
		{
			$tag = '&amp;tag='.substr($this->printTags($tagList), 0, -2);
		}

		$name =$this->fileName;

		if(!empty($this->title))
		{
			$name =$this->title;
		}
		echo '<item>';
			echo '<title>'.$name.'</title>'."\n";
			echo '<link>'.$this->curPageURL().$_SERVER["PHP_SELF"].'?selectImage='.$this->id.$tag.'</link>'."\n";
			echo('<description>'.'&lt;img src="'.'med/'.str_replace('"','&quot;',$this->fileNameResized).'" /&gt;'."\n");
			echo('</description>');
         		echo '<pubDate>'.$this->date.' EST'.'</pubDate>';
		echo '</item>';
	}


/**
 *Get the image title
 *@return string
 */
	function getTitle()
	{
		return $this->title;
	}

/**
 *Get file name
 *@return string
 */
	function getFileName()
	{
		return $this->fileName;
	}

/**
 *Custom Awesome Debug content
 */
	function adContent()
	{
		echo 'Image content:';
		echo '<br />id:'.$this->id;
		echo '<br />File Name:'.$this->fileName;
		echo '<br />File Resized:'.$this->fileNameResized;
		echo '<br />Title:'.$this->title;
		echo '<br />Description: '.$this->description;
		echo '<br />Date: '.$this->date;
		echo '<br />Path:'.$this->path;
		echo '<br />Selected:'.$this->selected;
		echo '<br />Tags:';
		print_r($this->tags);
	}

	function siteMap(){
		echo '<url>'."\n";
		echo ' <loc>'.$this->curPageURL().str_replace('sitemap.php','',$_SERVER["PHP_SELF"]).$this->geGallerytRefPage().'?selectImage='.$this->id.'</loc>'."\n";
		echo '</url>'."\n";
	}

}

?>
