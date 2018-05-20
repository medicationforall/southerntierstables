<?php

/**
 *   Medication For All Framework source file TagManager,
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
 *Central TagManagement object
 *
 *@author James M Adams <james@medicationforall.com>
 *@version 0.1
 *@package framework 
 */
class TagManager extends Component
{
//data

	/**
	 *Parsed tags from the GET response
	 *@var array
	 */
	protected $tags=array();

	/**
	 *List of officially sanctioned tags
	 *@var array
	 */
	protected $tagList =  array();

	/**
	 *Global tags to ignore
	 *@var array
	 */
	protected $ignoreTags = array();

	/**
	 *Arraylist of loaded childsets based on tags the user has already seen
	 *@var array
	 */
	protected $childSet = array();

	/**
	 *Child sets - any modifications HAS to bear childset manipulation in mind
	 *@var Core
	 */
	protected $currentChildSet;

	/**
	 *Force the tagmanager to load, after the gallery is loaded this is set to false.
	 *@var boolean
	 */
	protected $load = true;

	/**
	 *Fired when a reload event occurs ie deleting a child. this flag will never be set to true unless the user is an admin user capable of editing.
	 *@var boolean
	 */
	protected $reloadContent = false;

	protected $refPage = '';

/**
 *
 */
	function adContent(){
		parent::adContent();

		echo '<br /><br /><span class="lightTitle">TagManager Content:</span>';

		echo '<br /><span class="lightTitle">Tags:</span>';
		foreach($this->tags as $tag)
		{
			echo ' '.$tag;
		}

		echo '<br /><span class="lightTitle">Sanctioned Tags:</span>';
		foreach($this->tagList as $tag)
		{
			echo ' '.$tag.',';
		}

		echo '<br /><span class="lightTitle">Ignored Tags:</span>';
		foreach($this->ignoreTags as $tag)
		{
			echo ' '.$tag.',';
		}

		echo '<br /><span class="lightTitle">Child Sets:</span>';
		foreach($this->childSet as $key => $value)
		{
			echo ' '.$key.'';
		}

		echo '<br /><span class="lightTitle">Current Child Sets:</span> '.$this->currentChildSet;

		echo '<br /> '.'<span class="lightTitle">Load:</span> ';
		if($this->load)
		{echo 'true';}
		else
		{echo 'false';}

		echo '<br /> '.'<span class="lightTitle">re-Load:</span> ';
		if($this->reloadContent)
		{echo 'true';}
		else
		{echo 'false';}
		/*echo '<br /> '.'<span class="lightTitle">Counter:</span> '.self::$counter;*/

	}




//constructor

/**
 *Constructs the tagmanager object
 *@param string $h Header text.
 *@param string $type Component Type, default is tagManager
 */
	function __construct($h='', $type = 'tagManager')
	{
		parent::__construct($h, $type);
	}


//methods

/**
 *Grabs the list of tags the user requested to view and prints them out in a comma delimited list.
 */
	function printTags()
	{
		$returner = "";
		foreach($this->tags as $tag)
		{
			if(strcmp($tag, 'default')!=0)
			{
				if(!empty($returner))
				{
					$returner .= ',';
				}

				$returner .= $tag;
			}
		}

		return $returner;
	}


/**
 *Returns a list of all tags currently being used.
 *@param boolean $output Flag for whether to print out json response object. Default is true.
 */
	function queryTags($output = true)
	{
		$query = 'SELECT name FROM tag where component = ? order by name';
		$list = array();
		$mysqli = $this->getParent('Page')->getConnect()->getMysqli();

		if($stmnt = $mysqli->prepare($query))
		{
			$type = $this->getType();
			//print 'type is '.$type;
			$stmnt->bind_param('s',$type);
			$stmnt->execute();
			$stmnt->bind_result($name);
			while($stmnt->fetch())
			{
				$list[count($list)] = $name;

				//print'<br />likt kicked';
			}

			if($output)
			{
				echo json_encode(array_values($list));
			}

			$this->tagList = $list;
		}
	}


/**
 *Gets the list of tags the user is requesting to view.
 *@return array
 */
	function getTags()
	{
		return $this->tags;
	}


/**
 *Grabs the cached tag list
 *@return array
 */
	function getTagList()
	{
		return $this->tagList;
	}


/**
 *Adds a tag to the ignore list.
 *@param string $tag
 */
	function addTagToIgnore($tag)
	{
		//print 'adding tag to ignore';
		$this->ignoreTags[count($this->ignoreTags)] = $tag;
	}


/**
 *Removes a tag from being ignored. The Method Could be named better.
 *@param string $tag
 */
	function removeTagToIgnore($tag)
	{
		if(in_array($tag, $this->ignoreTags))
		{
			//print 'found tag to not ignore';

			$newIgnore = array();
			foreach($this->ignoreTags as $tagAdd)
			{
				if(strcmp($tag, $tagAdd)!=0)
				{
					$newIgnore[count($newIgnore)] = $tagAdd; 
				}
			}
			$this->ignoreTags = $newIgnore;
		}
	}


/**
 *Finds images that contain tags on the ignore list and removes them from being viewed; unless the user is explicitly requesting to view an ignored tag.
 */
	function ignore()
	{
		if(!empty($this->ignoreTags))
		{
			//print 'ready to ignore some images<br />';

			//first compare current tag set against tags to ignore

			$diff = array_diff($this->ignoreTags, $this->tags);

			//if not empty than we have some tags to ignore
			if(!empty($diff))
			{
			//print_r($diff);
				foreach($diff as $tag)
				{
					//print 'running diff check for '.$tag;
					$children = $this->getChildren();
					$newChildren = array();

					//if the gallery has children
					if($children)
					{
						foreach($children as $child)
						{
						
							$mtags = $child->getTags();
							//print '<br />';
							//print_r($mtags);

							if(in_array($tag, $mtags))
							{
								//print 'we have a hit';
							}
							else
							{
								$newChildren[count($newChildren)] = $child;
							}
						}

						$this->setChildren($newChildren);
					}
				}	
			}
		}
	}


/**
 * Based on the http tag parameter loads a stored child set (images). If that tag has already been visited. 
 * "default" is what the childset is stored under if no tag is set, and should be considered a reserved keyword.
 */
	function loadChildSet()
	{
		$this->tags = array();
		$page = $this->getParent('page'); 		
		//set name of child set being worked upon
		if(!empty($_REQUEST['tag']))
		{
			//can't take this raw
			$this->currentChildSet = urldecode($_REQUEST['tag']);

			//print $this->currentChildSet;
			//print 'found tag to process';
			$tmp = urldecode($_REQUEST['tag']);
			$tmp = explode(',', $tmp);
			$tmpTags = array();

			foreach($tmp as $tag)
			{
				$tag = trim(strtolower($tag));

				if(!empty($tag) && in_array($tag, $tmpTags) == false){
					$tmpTags[count($tmpTags)] = $tag;
					//print '<br /> '.$tag;
				}
			}

			if(empty($this->tags))
			{
				//print 'the list is empty anyways';
				$this->tags = $tmpTags;
				$this->currentChildSet = $this->printTags();
			}
			//print $this->currentChildSet;

			$loginControl = $this->getParent('page')->findChildByName('LoginCOntrol');

			if($loginControl[0])
			{
				//print 'found login control';
				$loginControl[0]->setEditOnLink('?edittoggle=on&tag='.urldecode($_REQUEST['tag']));
				$loginControl[0]->setEditOffLink('?edittoggle=off&tag='.urldecode($_REQUEST['tag']));
			}

			//print 'setting rss link to '.'?rss=true&tag='.$_REQUEST['tag'];
			$page->setRSSLink('?rss=true&tag='.$this->printTags());
		}
		else
		{
			$this->currentChildSet = 'default';
		}

		//load child set

		//print_r($this->childSet);
		if(!empty($this->childSet[$this->currentChildSet]))
		{
			//print 'loading child set for '.$this->currentChildSet.' ';
			$this->setChildren($this->childSet[$this->currentChildSet]);
		}
		else
		{
			//print 'childset is empty';
			$this->setChildren(null);
		}
	}


/**
 * Stores the children away in a hashmap using tag as the key.
 */
	function storeChildSet()
	{
		//print ' saving child set for '.$this->currentChildSet;
		$this->childSet[$this->currentChildSet] = $this->getChildren();
	}

/**
 *Gets the list of ignored tags.
 */
	function getIgnore()
	{
		return $this->ignoreTags;
	}

/**
 * placeholder function meant to be ovrwritten
 */
	function recent()
	{

	}

/**
 * placeholder function meant to be ovrwritten
 */
	function loadOrder()
	{

	}


/**
 *Sets the load flag. Set reload is a more certain way to force a complete reload.
 *@param boolean $l
 */
	function setLoad($l)
	{
		$this->load = $l;
	}

/**
 *
 */
	function getLoad()
	{
		return $this->load;
	}

/**
 *
 */
	function setRefPage($ref){
		//print '<br />calling set ref page '.$ref;
		$this->refPage =  $ref;
	}

/**
 *
 */
	function getRefPage(){
		//print '<br />calling get ref page '.$this->refPage.' for '.$this->getType();
		return $this->refPage;
	}

/**
 *
 */
	function siteMap(){
		echo '<!--sitemap for tagManager-->'."\n";
		//echo 'count '.count($this->tagList)."\n";
		//print_r($this->tagList);

		if(count($this->tagList)==0){
			$this->queryTags(false);
		}


		foreach($this->tagList as $tag){
			echo '<url>'."\n";
			echo ' <loc>'.$this->curPageURL().str_replace('sitemap.php','',$_SERVER["PHP_SELF"]).$this->refPage.'?tag='.urlencode($tag).'</loc>'."\n";
			echo '</url>'."\n";
		}
	}

}
?>
