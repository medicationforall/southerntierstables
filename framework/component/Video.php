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
class Video extends Image2
{

//data
/**
 *Unique identifier of a youtube video
 *@access private
 *@var string
 */
private $link;

//constructor
/**
 *Creates the video object.
 *@param string $h Header text.
 *@param string $type Component type.
 */
function __construct($h='', $type='video')
{
	parent::__construct($h, $type);
}

//methods
/**
 *Process the video conponent.
 *@param boolean $processChildren Process children flag.
 */
	function process($processChildren=true)
	{
		$this->addCLass('image2');

		parent::process();
	}
	

/**
 *Content displayed to the user. Needs to be refactored to separate the edit mode from the display mode.
 * http://maxmorgandesign.com/fix_youtube_iframe_overlay_and_z_index_issues/
 */
function cContent()
{
	//$size =getimagesize('med/'.$this->fileNameResized);
	echo '<div class="cContent" style="width:660px">';
	//echo '<a href="'.$this->path.'"><img class="mainImage" alt="'.$this->fileName.'" src="med/'.$this->fileNameResized.'"></a>';

	echo'<div class="video mainImage">';

	echo '<iframe style="width:100%;height:400px;" src="http://www.youtube-nocookie.com/embed/'.$this->link.'?rel=0&wmode=opaque" frameborder="0" allowfullscreen></iframe>';
	echo '</div>';

	//echo '<img src="'.$this->path.'" />';
	echo '</div>';

	$this->printInfo();
}


/**
 *Prints the thumbnail image and link of the video.
 *@todo As is this doesn't work with lazyload, and does it need to ?
 */
	function showThumb()
	{

		$id = 'id="image-'.$this->id.'"';
		if($this->selected)
		{
			echo'<div '.$id.' class="thumb selectedThumb"><img style="height:100px;" src="http://i.ytimg.com/vi/'.$this->link.'/default.jpg" /></div>';
			//<img src="http://i.ytimg.com/vi/XVNfy8CggmI/default.jpg" alt="">	
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
			echo '<div '.$id.' class="thumb" ><a href="?selectImage='.$this->id.$tag.'"><img style="height:100px;" src="http://i.ytimg.com/vi/'.$this->link.'/default.jpg" alt="'.$this->fileName.'" /></a></div>';
		}
	}

/**
 *Sets the link.
 *@param string $l
 */
	function setLink($l)
	{
		$this->link = $l;
	}

/**
 *Sets the fileName.
 *@param string $name
 */
	function setFileName($name)
	{
		$this->fileName = $name;

		$this->link = $name;
	}

/**
 *Custom awesome debug content.
 */
	function adContent()
	{
		parent::adContent();
		echo '<br /><br />Video Content: ';
		echo '<br />youtube Link: '.$this->link;
	}

}

?>
