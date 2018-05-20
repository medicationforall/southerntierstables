<?php

/**
 *   Medication For All Framework source file ImageRotate,
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
 *   Represent a rotating image.
 *
 *
 *@todo Selenium test should also test for, description, mediu, title.
 *
 *@author James M Adams <james@medicationforall.com>
 *@version 0.3
 *@package framework
 */

class ImageRotate extends Image2
{

/**
 *Boundary width 
 *@var int
 */
private $width = 800;


/**
 *Boundary height
 *@var int
 */
private $height = 800;

/**
 *Degrees per step
 *@var int
 */
private $degree = 5;


/**
 *Flag for if this object has it's settings saved to the database already or not.
 *So when we save we know whether to perform an update or insert query.
 *@var boolean
 */
private $hasRecord = false;



//constructor
/**
 *   Creates the Component Image Object.
 *@param string $h Header Text.
 */
	function __construct($h='')
	{
		parent::__construct($h, 'imageRotate');
	}

//methods
/**
 *   Processes the input data.
 *@param boolean $processChildren Process children flag.
 *@todo process is called twice when saving an image;
 *@todo Meta description can't process html tags within the body of it, so when passing the image description to the meta description I have to parse out html tags. This will rely on making parse work first.
 */
	function process($processChildren=true)
	{
		//print 'callint image rotate process<br />';
		$page = $this->getParent('page');
		$page->script('revolver.js');
		$page->script('imageRotate.js');
		$this->addCLass('image2');

				//SET IMAGE ENTRY DATA
		if(!empty($_POST['imageId']) && strcmp($_POST['imageId'], $this->id)==0)
		{

			if((!empty($_POST['imageentry'])) && strcmp($_POST['imageentry'], 'imageentry')==0)
			{
				//print 'image entry for image rotate';

				$save = false;

				if(!empty($_POST['rWidth']) && is_numeric($_POST['rWidth']))
				{
					$this->width = $_POST['rWidth'];
					$save = true;
				}

				if(!empty($_POST['rHeight']) && is_numeric($_POST['rHeight']))
				{
					$this->height = $_POST['rHeight'];
					$save = true;
				}

				if(!empty($_POST['degreeChoice']) && is_numeric($_POST['degreeChoice']))
				{
					$this->degree = $_POST['degreeChoice'];
					$save = true;
				}

				if($save)
				{
				$this->save();
				//print ' width is'.$this->width;
				$this->createCrop('med');
				$this->createCrop('thumb', 100, 'height');
				}
			}

		}
		parent::process($processChildren);
	}

/**
 *Save the imageRotate objects settings.
 */
	function save()
	{
		//print 'calling save for'.$this->id.' ';

		$mysqli = $this->getParent('page')->getConnect()->getMysqli();

		if($this->hasRecord == false)
		{
			//print 'running insert';

			$query = 'INSERT INTO tblimagerotate (imageid, width, height, degree) VALUES(?, ?, ?, ?)';

			if($stmnt = $mysqli->prepare($query))
			{
				
				$id = $this->id;
				$width = $this->width;
				$height = $this->height;
				$degree = $this->degree;

				//print 'statement built '.$id.' '.$height.' '.$width.' '.$degree;
				$stmnt->bind_param('ssss', $id, $width, $height, $degree);

				$stmnt->execute();

				if($stmnt->affected_rows > 0)
				{
					//print '<br /> image rotate updated';
					$this->hasRecord =true;
					//$this->tags[count($this->tags)] = $tag;
				}
			}
		}
		else
		{
			//print 'already exists run update';

			$query = 'UPDATE tblimagerotate SET width=?, height=?, degree=? where imageid =?';

			if($stmnt = $mysqli->prepare($query))
			{
				
				$id = $this->id;
				$width = $this->width;
				$height = $this->height;
				$degree = $this->degree;

				//print 'statement built '.$id.' '.$height.' '.$width.' '.$degree;
				$stmnt->bind_param('ssss', $width, $height, $degree, $id);

				$stmnt->execute();

				if($stmnt->affected_rows > 0)
				{
					//print '<br /> image rotate updated';
					//$this->hasRecord =true;
					//$this->tags[count($this->tags)] = $tag;
				}
			}
		}	
	}

/**
 *   Content displayed to the user. Needs to be refactored to separate the edit mode from the display mode.
 */
function cContent()
{
	echo '<div class="cContent selected" style="max-width:'.$this->width.'px">';
		echo'<div class="revolver degree'.$this->degree.' mainImage" style="width: '.$this->width.'px; height: '.$this->height.'px;overflow:hidden">';
			echo'<img alt="'.$this->fileName.'" src="'.$this->path.'" style="width:auto"/>';
		echo'</div>';
	echo'</div>';

	$this->printInfo();
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
				echo '<a href="'.$this->file.'" target="_blank"><img src="med/'.str_replace('.jpg', '.png', $this->fileParts[count($this->fileParts)-1]).'" /></a>';
				echo '<a href="'.$this->file.'" target="_blank"><img src="med/'.$this->fileParts[count($this->fileParts)-1].'" /></a>';
				//echo 'test';
				//echo '<div class="revolver degree5" style="width:800px;height:797px;background: url(\''.$this->file.'\') 0px 0px" >&nbsp;</div>';
			}
			else if(strstr($this->fileParts[count($this->fileParts)-1], '.png'))
			{
				//echo '<a href="'.$this->file.'" target="_blank"><img src="med/'.$this->fileParts[count($this->fileParts)-1].'" /></a>';
				echo '<a href="'.$this->file.'" target="_blank"><img src="med/'.str_replace('.png', '.jpg', $this->fileParts[count($this->fileParts)-1]).'" /></a>';
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
 *   Load image entry data.
 */
	function load()
	{
		//print 'load image rotate';
		parent::load();

		$mysqli = $this->getParent('page')->getConnect()->getMysqli();
		$query = 'SELECT width, height, degree FROM tblimagerotate WHERE imageid = ?';

		if($stmnt=$mysqli->prepare($query))
		{
			$stmnt->bind_param('s', $this->id);
			$stmnt->execute();
			$stmnt->bind_result($width, $height, $degree);

			while($stmnt->fetch())
			{
				$this->width = $width;
				$this->height = $height;
				$this->degree = $degree;
				$this->hasRecord = true;
			}
		}
		//print 'query '.$this->width.' '.$this->height.' '.$this->degree;

	}

/**
 *Additional fields display to the user when editing the imageRotate object.
 */
	function additionalFields()
	{
		echo '<div style="border:none">Width:<br /><input class="rWidth" type="text" name="rWidth" value="'.$this->width.'"></input></div>';
		echo '<div style="border:none">Height:<br /><input class="rHeight" type="text" name="rHeight" value="'.$this->height.'"></input></div>';

		//could totally draw this off of the sql meta data

		$checked5 ='';
		$checked10 = '';

		$degree = $this->degree;

		if(empty($degree) || $degree==5)
		{
			$checked5 = 'checked="checked"';
		}
		else if($degree==10)
		{
			$checked10 = 'checked="checked"';
		}

		echo '<div style="border:none" class="radioBox">Degrees:<br /><input type="radio" name="degreeChoice" value="5" '.$checked5.' />5<br /><input type="radio" name="degreeChoice" value="10" '.$checked10.' />10</div>';
	}


/**
 *   Creates a sub image at the set size, and places it into a directory specified.
 *@param string $dest Destination file path.
 *@param int $opt Pixel size.
 *@param string $override Height override
 *@todo override is an obscure implementation. MaKe it less so.
 */
	function createCrop($dest, $opt='', $override='')
	{
		//this should be commented out in production scripts
		//ini_set("memory_limit", "1024M");

		$newWidth = $this->width;
		$newHeight= $this->height;


		if(!empty($opt) && !empty($override))
		{

			$optimumSize = $opt;
			$ratio;

			$width = $this->width;
			$height = $this->height;

			$newHeight = 0;
			$newWidth = 0;

			$src;

			if($width>=$height)
			{
				$ratio = $width / $optimumSize;
				$newWidth = $optimumSize;
				$newHeight = $height / $ratio;
			}
			else
			{
				$ratio = $height / $optimumSize;
				$newWidth = $width / $ratio;
				$newHeight = $optimumSize;
			}

			if(strcmp($override, 'height')==0)
			{
				$ratio = $height / $optimumSize;
				$newWidth = $width / $ratio;
				$newHeight = $optimumSize;
			}
		}

		$src = imagecreatefromjpeg($this->path);

		$dst = imagecreatetruecolor($newWidth, $newHeight);

		if(imagecopyresampled($dst, $src, 0, 0, 0, 0, $newWidth, $newHeight, $this->width, $this->height))
		{
			//print 'resized image';
			imagedestroy($src);
		}

		if(imagejpeg($dst, $dest.'/'. $this->fileName, 70))
		{
			//print 'created image';
			imagedestroy($dst);
		}
	}

}
