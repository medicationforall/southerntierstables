<?php
/**
 *   Medication For All Framework source file Gallery,
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
 *   Rebuild of the gallery component. Features keyboard navigation, drag and drop, tagging , thumbnail / medium image generation , and session caching. 
 *
 *   Sample Usage {@link http://www.medicationforall.com/framework/sample/SampleGallery.php SampleGallery}
 *
 *   {@example ../sample/SampleGallery.php SampleGallery}
 *
 *@see    Image2
 *
 *@todo make it a configuration option how big the medium and image thumbs are.
 *
 *@author James M Adams <james@medicationforall.com>
 *@version 0.1
 *@package framework 
 */
class Gallery2 extends TagManager
{
use UpdateOrderTrait;
//data
	/**
	 *   Directory in which to scan.
	 *@access private
	 *@var string
	 */
	private $directory;

	/**
 	 * Directory to search for sprite images.
	 *@var string
	 */
	private $spriteDirectory;


	/**
	 *First time loading the component this is specifically used to show the tooltip. and is never set to true again.
	 *@var boolean
	 */
	private $first = true;


	/**
	 *Big one, this is the gallery traverser.
	 *@var Traverse
	 */
	private $galTraverse = null;


	/**
	 *Right now just youtube. The idea is it's a feed that the gallery is pulling item data from.
	 *@var string
	 */
	private $feed;

	/**
	 *Optimum thumbnail image size.
	 *@var int
	 */
	private $thumbSize = 100;

	private $thumbCompression = 70;

	private $thumbOverride="height";

	/**
	 *Optimum medium image size
	 *@var int
	 */
	private $mediumSize = 800;

	private $mediumCompression = null;

	private $thumbViewerBelow=true;


//constructor
/**
 *   Creates the Gallery object. Can scan a directory and parse the image contents.
 *
 *@param string $h Header Text.
 *@param string $d Optional directory which is scanned and displayed.
 *@param string $type Component type, default is gallery2.
 */
	function __construct($h='', $d='', $type = 'gallery2')
	{
		parent::__construct($h, $type);

		$this->directory = $d;

		//$this->setShowPreference(true);
	}


//methods

/**
 *   Processes the gallery, take note with the way this application works with childset caching; 
 *   process should be called very carefully. 
 *   If altering the elements and you need to process the changes it's best to split the code into methods that are called independent of process().
 *@param boolean $processChildren Process children flag. 
 *@todo add license info for the move and swipe scripts.   
 */
	function process($processChildren=true) {
		//run component process , run core process
		parent::process();
		$this->getParent('page')->setJSON(true);

		if(!empty($_REQUEST['json']) && strcmp($_REQUEST['json'], 'true')==0) {
			header("Access-Control-Allow-Origin: *");
		}

		if(!empty($_REQUEST['forceLoad']) && strcmp($_REQUEST['forceLoad'], 'true')==0)
		{
			$this->buildTraverse();
		}
		else if(!empty($this->galTraverse) && ($this->galTraverse->getTraverseImage() == false))
		{
			//null the traverse object because were no longer asking for forceload
			$this->galTraverse = null;
		}

		if(!empty($_REQUEST['removeIgnore']))
		{
			//print 'ready to remove ignore';
			$this->removeTagToIgnore($_REQUEST['removeIgnore']);
			$this->reloadContent = true;
		}

		if($this->reloadContent)
		{
			$this->reload();
			$this->reloadContent = false;
		}


		$this->loadChildSet();

		$page = $this->getParent('page');
		
		$page->setRSS(true);
		//$page->joinScript('gallery.js',array('jquery.lazyload-*.min.js','imagesloaded.pkgd-*.min.js','jquery.event.move.js','jquery.event.swipe.js','gallery2.js'),'.');
		$page->script('jquery.lazyload-*.min.js');
		$page->script('imagesloaded.pkgd-*.min.js');

		//http://cdnjs.com/
		//$page->script('//cdnjs.cloudflare.com/ajax/libs/jquery.imagesloaded/2.1.0/jquery.imagesloaded.min.js ');
		//$page->script('//cdnjs.cloudflare.com/ajax/libs/jquery.lazyload/1.9.0/jquery.lazyload.min.js');

		//http://stephband.info/
		$page->script('jquery.event.move.js');
		$page->script('jquery.event.swipe.js');

		$page->script('gallery2.js');
		//$page->script('revolver.js');



		if(!empty($_REQUEST['tag']) &&  empty($this->childSet[$this->currentChildSet]))
		{
			//print 'we need to load a tag set'; 
			$this->load = true;
			$this->setChildren(null);
		}

		//loads default gallery
		else if(empty($this->childSet['default']) && empty($_REQUEST['tag']))
		{
			//print 'breaking load';
			//print_r($this->tags);
			//print 'we need to load default';
			$this->load = true;
			$this->tags = array();
			$this->clearChildren();
		}

		//query the images
		if($this->load)
		{
			$this->recent();
			$this->loadOrder();
		}

		if($processChildren)
		{
			$this->children('process');
		}

		//it seems counter intuitive to call ignore after recent and load order but until the images are processed we don't have their tags to compare against our ignored tags.
		if($this->load)
		{
			$this->ignore();
			$this->load = false;
		}

		$this->selectImage();

		$this->addForceLoad();

		//store childset back to array
		$this->storeChildSet();	
	}

/**
 *Builds the gallery traverse object.
 */
	function buildTraverse()
	{
		//print 'calling build traverse';
			if($this->galTraverse == null)
			{

				//print '<br />building gal traverse for reals';
				$this->galTraverse = new Traverse();
				$this->galTraverse->setParent($this);
			}

			$this->galTraverse->setThumbSize($this->thumbSize);
			$this->galTraverse->setThumbCompression($this->thumbCompression);
			$this->galTraverse->setThumbOverride($this->thumbOverride);
			$this->galTraverse->setMediumSize($this->mediumSize);
			$this->galTraverse->setMediumCompression($this->mediumCompression);
			$this->galTraverse->setTraverseImage(true);
			$this->galTraverse->process();
	}


/**
 *Add a forceload link to a logincontrol component (if one is present). 
 *With the intention that when logged in can force a fresh gallery re-build without having to clear browser cache. 
 */
	function addForceLoad()
	{
		$page= $this->getParent('page');
		$account = $page->getAccount();

		if($account->isLogin())
		{
			$loginControl = $this->getParent('page')->findChildByName('LoginCOntrol');

			if($loginControl[0])
			{
				//login control is cloned each page load so forceload is always going to be added
				$forceLoad = $loginControl[0]->findChild('forceLoad');
				if(empty($forceLoad))
				{
					$tag ='';
					if(!empty($_REQUEST['tag']))
					{
						$tag = '&tag='.urldecode($_REQUEST['tag']);
					}
					//print 'forceLoad is false';
					$forceLoad = new Menu('Force Load', '?forceLoad=true'.$tag);
					$forceLoad->setUnique('forceLoad');
					$forceLoad->setLevel('edit');
					$loginControl[0]->add($forceLoad);
				}
				//print 'found login control';
				//$loginControl[0]->setEditOnLink('?edittoggle=on&tag='.$_REQUEST['tag']);
				//$loginControl[0]->setEditOffLink('?edittoggle=off&tag='.$_REQUEST['tag']);
			}
		}
	}







/**
 * Resets back to a clean state, clears all the childsets and current children.  
 */
	function reload()
	{
		//print 'ran reload';
		$this->load = true;
		$this->setChildren(null);
		$this->currentChildSet = null;
		$this->childSet = array();
		$this->tags = array();
	}


/**
 * Loops through all of the children, and sets the selected image ID matching, also deselects the prior selected image.
 */
	private function selectImage()
	{
		if(!empty($_REQUEST['selectImage']) && is_numeric($_REQUEST['selectImage']))
		{
			$this->each(function($c)
			{
				if(strcmp($c->getType(), "image2")==0 || strcmp($c->getType(), "imageRotate")==0 || strcmp($c->getType(), "video")==0)
				{
					if(strcmp($c->getId(), $_REQUEST['selectImage'])==0)
					{
						//print 'image selected';
						$c->setSelected(true);
					}
					else
					{
						//print '<br />deselected '.$c->getId().' '.$c->getType().' '.$c->getTitle().' '.$c->getFileName();
						$c->setSelected(false);
					}
				}
			});
		}
	}


/**
 * This is called by the upload component in order to inform the gallery that a new image has been uploaded.
 *@param boolean $t Flag the indicate that forceload has been triggered.
 */
	function setTraverseImage($t)
	{
		//
		$this->buildTraverse();
		if($this->galTraverse)
		{
			$this->galTraverse->setTraverseImage($t);
		}
	}


/**
 *   Queries the database to return the most recently added pics, 
 */
	function recent()
	{
		//print 'running recent';
		$mysqli = $this->getParent('page')->getConnect()->getMysqli();


		$query = 'SELECT id,`name`, title, DATE_FORMAT(date, \'%m/%d/%Y\'), `path` ,`version`, `type` FROM `tblimage` WHERE site=? AND status = \'active\' ORDER BY `date` DESC' ;
		$parameterList = array();


		if(!empty($this->tags))
		{
			//print 'tags is not empty';
			$type = 's';
			$searcher = '';
			foreach($this->tags as $tag)
			{
				$type .= 's';

				if(empty($searcher))
				{
					$searcher .= '?';
				}
				else
				{
					$searcher .= ', ?';
				}
			}

			$parameterList[0] = $type;
			$parameterList[1] = $this->getParent('page')->getSettings()->getSite();
			$parameterList = array_merge($parameterList, $this->tags);

			//http://stackoverflow.com/questions/11074075/mysql-selecting-values-based-on-multiple-rows
			$query = '
SELECT
i.id,
i.`name`,
title,
DATE_FORMAT(date, \'%m/%d/%Y\'),
`path`,
`version`,
`type` 

FROM 
`tblimage` as i 
JOIN
(`tags` as t)
ON
(t.rid = i.id )
JOIN
(`tag` as ta)
ON 
(t.tname = ta.name)

WHERE 
site=? 
AND 
status = \'active\' 
AND
ta.component = \'gallery2\'
AND

t.tname IN ('.$searcher.')
GROUP BY
    t.rid
HAVING
    COUNT(*) = '.count($this->tags).'

ORDER BY 
`date` DESC';
		}
		$site = $this->getParent('page')->getSettings()->getSite();

		if($stmnt = $mysqli->prepare($query))
		{			
			//http://www.php.net/manual/en/mysqli-stmt.bind-param.php#104073
			if(!empty($this->tags))
			{
				$ref    = new ReflectionClass('mysqli_stmt');
				$method = $ref->getMethod("bind_param");

				//uber lame hack 
				$tmp = array();
        			foreach($parameterList as $key => $value) 
				{
					$tmp[$key] = &$parameterList[$key];
				}

				//print 'test '. $tmp[0].' '.$tmp[1].' '. $tmp[2];
				$method->invokeArgs($stmnt, $tmp);

				//print(get_class($stmnt)); 
				//$stmnt->bind_param('ss', $site, $this->tags[0]);
			}
			else
			{
				$stmnt->bind_param('s', $site);
			}

			$stmnt->execute();
			$stmnt->bind_result($id, $name, $title, $date, $bPath,$version, $type);

			while($stmnt->fetch())
			{
				//create image objects whose children are listerally the image with all of it's needed data
				//print '<br />found image '.$bPath;
				$image = null;

				if(strcmp($type, 'image2')==0)
				{
					$image = new Image2();
				}

				if(strcmp($type, 'imageRotate')==0)
				{
					$image = new ImageRotate();
				}

				if(strcmp($type, 'video')==0)
				{
					$image = new Video();
				}

				if(!empty($image))
				{
					$image->setId($id);
					$image->setFileName($name);
					$image->setTitle($title);
					$image->setPath($bPath);
					$image->setVersion($version);

					$this->add($image);
				}
			}
		}
	}


/**
 *   Sets the mode type, valid entries are "recent", "traverse", and "search".
 *@param string $m Mode type.
 */
	function setMode($m)
	{
		$returner = true;
		if(in_array($this->parse(strtolower($m)), $this->modes))
		{
			//print 'setting mode '.$m;
			$this->mode = $m;
		}
		else
		{
			//throw new Exception('Gallery Mode was not correct');
			$returner = false;
		}
		return $returner;
	}


/**
 *Prints the page content of the gallery.
 */
	function cContent()
	{
		if($this->reloadContent)
		{
			$this->process();
		}

		//selected image
		echo '<div class="cContent">';

		if(!empty($this->galTraverse))
		{
			//print 'calling galtraverse show';
			$this->galTraverse->show();
		}

		if($this->first)
		{
			echo '<div class="tip">Press the Left and Right arrow Keys,<br /> Or swipe the image</div>';
		
			$this->first = false;
		}

		if($this->thumbViewerBelow == false){
			$this->printThumbViewer();
		}
		
		$this->printSelected();

		if($this->thumbViewerBelow){
			$this->printThumbViewer();
		}
	}

/**
 *Outputs the thumbViewer  object.
 */
	function printThumbViewer()
	{
		$threshold = 10;
		echo '<div class="thumbViewer">';
		//account for tags

		echo'<a class="triangle" id="triangle-left" href="?shiftPrevious=true"><div ></div></a>';
		echo'<a class="triangle" id="triangle-right" href="?shiftForward=true"><div ></div></a>';
		echo '<div class="thumbCenter">';

		$children = $this->getChildren();
		$count = count($children);

		for($i=0; $i<$count; $i++)
		{
			if(($i<10) || ($i>($count-(10+1))))
			{
				$children[$i]->showThumb();
			}
			else
			{
				$children[$i]->showThumbQuick();
			}
		}

		
		//$this->children('showThumb');
		echo '</div>';
		echo '</div>';
		echo '<a class="rssButton" title="Rss Feed" href="'.$this->getParent('page')->getRSSLink().'">&nbsp</a>';
		echo '</div>';
	}


/**
 *Prints the selected image. The flag parameter is used when called from the ajax response method short().
 *@param boolean $thumb Flag for whether to print the selected images thumbview.
 */
	function printSelected($thumb = false)
	{
		$children = $this->getChildren();
		$selected =null;

		if($selected = $this->getSelected())
		{
			$selected->show();
		}
		else if(count($children)> 0)
		{
			$selected = $this->getChild(0); 
			$this->getChild(0)->show();
			$this->getChild(0)->setSelected(true);
		}


		if($thumb)
		{
			$selected->showThumb();
		}
	}


/**
 *Gets the selected object.
 *@return image2 Returns the currently selected image object.
 */
	function getSelected()
	{
		$returner = null;
		$children = $this->getChildren();

		if(!empty($children))
		{
			foreach($children as $child)
			{
				if($child->isSelected())
				{
					$returner = $child;
					break;
				}
			}
		}
		return $returner;
	}


/**
 *Manages all of the incoming ajax requests.
 */
	function short()
	{
		//print ' gallery short';
		if(!empty($_REQUEST['selectImage']))
		{
			//print 'selecting image';
			$this->loadChildSet();
			$selected = $this->getSelected();
			//$this->process();
			$this->selectImage();

			$this->printSelected(true);

			$selected->showThumb();
			$this->storeChildSet();
		}

		if(!empty($_REQUEST['advanceRight']) && strcmp($_REQUEST['advanceRight'], 'true')==0)
		{
			$this->loadChildSet();
			//print 'requesting Advance right';
			$this->advanceRight();
			$this->storeChildSet();
		}

		if(!empty($_REQUEST['advanceLeft']) && strcmp($_REQUEST['advanceLeft'], 'true')==0)
		{
			$this->loadChildSet();
			$this->advanceleft();
			$this->storeChildSet();
		}

		if(!empty($_REQUEST['queryTags']) && strcmp($_REQUEST['queryTags'], 'true')==0)
		{
			//print 'attempting to query tags';
			$this->queryTags();
		}

		if(!empty($_POST['updateOrder']) && strcmp($_POST['updateOrder'], 'true')==0)
		{
			//print 'attempting to update order';
			$this->loadChildSet();
			$this->updateOrder();
			$this->storeChildSet();

			$account = $this->getParent('page')->getAccount();

			if($account->isLogin() == true && $account->access('admin'))
			{
				//print 'changed order and time to save';
				$this->saveOrder();
			}
		}

		if(!empty($_POST['replaceQuickThumb']) && is_numeric($_POST['replaceQuickThumb']))
		{
			//print 'do some replacing !';
			$id = $_POST['replaceQuickThumb'];

			$children = $this->getChildren();

			foreach($children as $child)
			{
				if($child->getId() == $id)
				{
					//print 'matching child found !';
					$child->showThumb();
				}
			}
		}

		//traverse handler
		if(!empty($_POST['traverseCheck']) && strcmp($_POST['traverseCheck'],'true')==0)
		{
			//print 'going to run traverse check';
			if($this->galTraverse !=null)
			{
				$this->galTraverse->traverseCheck();
			}
		}

		//traverse handler
		if(!empty($_POST['getLog']) && strcmp($_POST['getLog'],'true')==0)
		{
			//print 'going to run traverse check';
			if($this->galTraverse !=null)
			{
				$this->galTraverse->printLog();
			}
		}


		
		if(!empty($_POST['reloadGallery']) && strcmp($_POST['reloadGallery'],'true')==0)
		{
			$this->reload();
			$this->process();
			$this->cContent();	
		}

		if((!empty($_POST['rotateCClockwise']) && strcmp($_POST['rotateCClockwise'],'true')==0) ||(!empty($_POST['rotateClockwise']) && strcmp($_POST['rotateClockwise'],'true')==0)){
			$this->children('short');
		}		
	}





/**
 *Saves the updated gallery order if the user is logged in and an admin.
 */
	function saveOrder()
	{
		//print 'running save order '.$this->currentChildSet;
		$count = 1;
		$mysqli = $this->getParent('Page')->getConnect()->getMysqli();
		$children = $this->getChildren();

		//three queries, see if the image is already in the table under the current childset
		$qSelect = 'SELECT id FROM gallerysort where imageid = ? and tagname = ?';
		$select = $mysqli->prepare($qSelect);

		$qUpdate = 'UPDATE gallerysort SET weight=? WHERE id=?';
		$update = $mysqli->prepare($qUpdate);

		$qInsert = 'INSERT INTO gallerysort (imageid, tagname, weight) VALUES (?, ?, ?)';
		$insert = $mysqli->prepare($qInsert);

		foreach($children as $child)
		{
			$id = $child->getId();
			$found = false;
			$realFid;
			//print "\n".'calling each'.$count.' id '.$id;

			$select->bind_param('ss', $id, $this->currentChildSet);
			$select->execute();
			$select->store_result();
			$select->bind_result($fid);

			while($select->fetch())
			{
				$found = true;
				$realFid = $fid;
			}

			//yes update existing weight
			if($found)
			{
				$update->bind_param('ss', $count, $realFid);
				$update->execute();

				if($update->affected_rows >0)
				{
					//print "\n".'updated my weight '.$fid.' '.$id.' '.$count.' '.$this->currentChildSet;
				}
			}
			else //no insert at entry 
			{
				$insert->bind_Param('sss', $id, $this->currentChildSet, $count);
				$insert->execute();

				if($insert->affected_rows >0)
				{
					print 'inserted my weight '.$id.' '.$count;
				}
			}
			//$select->free_result();
			$count ++;
			$select->free_result();
		}
	}


/**
 *Load Order is a weighted image sort, the lower an images numeric weight the higher priority and the farther up the list it gets. 
 *New unsorted images have no weight and appear at the top of the list. 
 *First a gallery is loaded by date, then a gallery is parsed by loadOrder if one exists, and then a gallery is parsed for ignored tags and removes the ignored images.
 */
	function loadOrder()
	{
		//print 'running load order';
		$mysqli = $this->getParent('Page')->getConnect()->getMysqli();

		//determine if load order is applicable.
		$query ="SELECT imageid, weight FROM gallerysort where tagname = ? order by weight";

		$newChildren = array();

		if($stmnt = $mysqli->prepare($query))
		{
			$stmnt->bind_param('s', $this->currentChildSet);
			$stmnt->execute();
			$stmnt->bind_result($id, $weight);

			$children = $this->getChildren();

			//print 'test print children count '.count($children);

			while($stmnt->fetch())
			{
				//print '<br />'.$id.' '.$weight;
				for($i = 0;$i< count($children);$i++)
				{
					//print '<br />'.$i;
					if(strcmp($children[$i]->getId(), $id)==0)
					{
						//print ' found id '.$id.' '.$weight;
						$newChildren[count($newChildren)] = $children[$i];

						//print 'added child '.$newChildren[$weight-1]->getId();
						unset($children[$i]);
						$children = array_values($children);
						break;
					}
				}
			}
			/*foreach($newChildren as $child)
			{
				print '<br />'.$child->getId();
			}*/
			
			if($newChildren)
			{
				$combinedChildren = array_merge($children, $newChildren);
				$this->setChildren($combinedChildren);
			}
		}
	}


/**
 *Bottom node moves to top
 */
	function advanceLeft()
	{
		//print 'advance left';
		$children =$this->getChildren();
		$node = array($children[count($children)-1]);

		array_pop($children);
		$modified = array_merge($node, $children);

		//print_r($modified);
		$this->setChildren($modified);		

	}


/**
 *top node moves to the bottom.
 */
	function advanceRight()
	{
		//print 'advance right';
		$children =$this->getChildren();
		$node = $children[0];
		array_shift($children);

		$this->setChildren($children);
		$this->add($node);
	}



/**
 *Sets the gallery reload flag
 *@param boolean $r
 */
	function setReload($r)
	{
		$this->reloadContent = $r; 
	}
	

/**
 *Awesome Debugger gallery2 content.
 */
	function adContent()
	{
		echo '<span class="lightTitle">Gallery2 Content</span>';
		echo '<br /><span class="lightTitle">Directory:</span> '.$this->directory;
		echo '<br /><span class="lightTitle">Tags:</span> ';
		foreach($this->tags as $tag)
		{
			echo $tag.' ';
		}

		echo '<br /><span class="lightTitle">force load:</span> ';
		if($this->load)
		{echo 'true';}
		else
		{echo 'false';}

		echo '<br /><span class="lightTitle">First page load:</span> ';
		if($this->first)
		{echo 'true';}
		else
		{echo 'false';}

		echo '<br /><span class="lightTitle">Tag List:</span> ';
		foreach($this->tagList as $tag)
		{
			echo $tag.' ';
		}

		echo '<br /><span class="lightTitle">Tags to Ignore:</span> ';
		foreach($this->ignoreTags as $tag)
		{
			echo $tag.' ';
		}

		echo '<br /><span class="lightTitle">Current Child Set:</span> '.$this->currentChildSet;

		echo '<br /><span class="lightTitle">Child Set List:</span> ';
		foreach($this->childSet as $key => $set)
		{
			echo '<br />'.$key.' '.count($set);
		}

		echo '<br /><span class="lightTitle">Reload Content:</span> ';
		if($this->reloadContent)
		{echo 'true';}
		else
		{echo 'false';}

		echo '<br />Gallery Traverse';
		if(!empty($this->galTraverse))
		{
			$this->galTraverse->awesomeDebug();
		}    
	}

/**
 *Gallery2 supports an RSS output, and draws the feed content from it's children.
 */
	function rss()
	{
		$rssCount = $this->getParent('page')->getSettings()->getGalleryRSSCount();

		if($rssCount > 0){
			$children = $this->getChildren();
			$count = count($children);

			for($i=0;$i<$rssCount && $i< $count;$i++){
				$this->getChild($i)->rss();
			}			
		}else{
			$this->children('rss');
		}

	}

	function json() {
		echo '"images":[';
			$this->iterateJson();
		echo ']';
	}


/**
 *Sets the sprite directory.
 *@param string $dir
 */
	function setSpriteDirectory($dir)
	{
		$this->spriteDirectory = $dir;
	}
/**
 *Get the sprite directory.
 *@return string
 */
	function getSpriteDirectory()
	{
		return $this->spriteDirectory;
	}

/**
 *Set the rss feed to pull from.
 *@param string $feed URL
 */
	function setFeed($feed)
	{
		$this->feed = $feed;
	}

/**
 *Get the feed link.
 *@return string
 */
	function getFeed()
	{
		return $this->feed;
	}

/**
 *Get the directory being scanned
 *@return string
 */
	function getDirectory()
	{
		return $this->directory;
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
 *
 */
	function getMediumSize(){
		return $this->mediumSize;
	}


/**
 *Set optimum thumb size
 *@param string $s
 */
	function setThumbSize($s)
	{
		$this->thumbSize = $s;
	}

	function setThumbCompression($comp){
		$this->thumbCompression = $comp;
	}

	function setMediumCompression($comp){
		$this->mediumCompression = $comp;
	}

	function setThumbOverride($override){
		$this->thumbOverride = $override;
	}

	function setThumbViewerBelow($flag){
		$this->thumbViewerBelow = $flag;
	}

/**
 *
 */
	function getThumbSize(){
		return $this->thumbSize;
	}


	function siteMap(){
		parent::siteMap();
		echo '<!--SiteMap for Gallery2'.$this->getUnique().'-->'."\n";
		//echo 'count'.count($this->getChildren())."\n";
		$this->children('siteMap');
	}

}
?>
