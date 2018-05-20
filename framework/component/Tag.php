<?php
/**
 *   Medication For All Framework source file Tag,
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
 *Central TagManagement object, this is an abstraction ontop of component, for component that can inherit the tag trait.
 *
 *@author James M Adams <james@medicationforall.com>
 *@version 0.1
 *@package framework 
 */
class Tag extends Component
{
//data

/**
 * descriptive tags associated with the object
 * @access protected
 * @var string
 */
	protected $tags = array();

/**
 * Incremented auto generated ID all components that utilize tags follow this convention for there ID's
 * @access protected
 * @var int
 */
	protected $id;



//constructor

/**
 *   Creates the Component tag Object.
 *@param string $h Header Text.
 *@param string $type Component type, default is tag.
 */
	function __construct($h='', $type='tag')
	{
		parent::__construct($h, $type);
	}

//methods

/**
 *Prints comma separated list of tags associated with the object. 
 *Also acts as helper method and will print the comma separated list for an array passed to it.
 *@param array $t
 */
	function printTags($t=null)
	{
		//print '<br />calling print tags';
		$returner ='';

		$parseTags;

		if(!empty($t))
		{
			$parseTags = $t;
		}
		else
		{
			$parseTags = $this->tags;
		}
		foreach($parseTags as $tag)
		{

			$returner .= $tag;

			if(!empty($returner))
			{
				$returner .= ', ';
			}

		}
		return $returner;
	}


/**
 *Gets the tagList array
 *@return array 
 */
	function getTags()
	{
		return $this->tags;
	}


/**
 * Set ID
 *@param int $id
 */
	function setId($id)
	{
		$this->id = $id;
	}

/**
 *Get ID
 *@return int
 */
	function getId()
	{
		return $this->id;
	}

/**
 *Handles adding and removing tags from a tag object
 *@param Core $parent Parent TagManager object
 *@todo The delete clause from here seems problematic. ie you can't have the same tag listed for two components in the tag database, because they are separate namespaces.
 *@todo should kick off the handler for completely deleting a tag from the database that is no longer tied to any other objects. 
 */
	function handleTags($parent)
	{
		$mysqli = $this->getParent('Page')->getConnect()->getMysqli();
		$modifiedTags = array();
		$type = $parent->getType();


		if(!empty($_POST['updateTags']))
		{
			//print 'update tag info';
			//explode the tags
			$tags = explode(',', $_POST['updateTags']);

			foreach($tags as $tag)
			{
				//print '<br /> tag to compare '.trim(strtolower($tag));
				$tag =  trim(strtolower($tag));
				$modifiedTags[count($modifiedTags)] = $tag;

				if(!empty($tag))
				{
					if(!in_array($tag, $this->tags))
					{
						//print '<br />tag needs to be added '.$tag;

						//check if the tag has to be registered
						$tagList = $parent->getTagList();

						if(!in_array($tag, $tagList))
						{
							//print '<br />need to add tag to primary list';
							$query = 'INSERT INTO `tag` (`name`, `component`) VALUES (?,?);';

							if($stmnt = $mysqli->prepare($query))
							{
								//print 'query is ready to go so far';
								$stmnt->bind_param('ss', $tag,$type);
								$stmnt->execute();
								if($stmnt->affected_rows > 0)
								{
									//print '<br /> primary list updated';
								}
							}
						}

						//tag still needs to be added to the image database tags table

						$query = 'INSERT INTO `tags` (`rid`, `tname`) VALUES (?, ?);';
						if($stmnt = $mysqli->prepare($query))
						{
							$stmnt->bind_param('ss', $this->id, $tag);
							$stmnt->execute();
							if($stmnt->affected_rows > 0)
							{
								//print '<br /> image list updated';
								$this->tags[count($this->tags)] = $tag;
							}
						}
					}
				}
			}
		}

		//print 'outside of for loop';
		$finalTags = array();

		foreach($this->tags as $tag)
		{
			if(!in_array($tag, $modifiedTags, true))
			{
				$query = 'DELETE FROM `tags` WHERE `rid` = ? AND tname = ? ';

				if($stmnt = $mysqli->prepare($query))
				{
					$stmnt->bind_param('ss', $this->id, $tag);
					$stmnt->execute();
				}							
			}
			else
			{
				$finalTags[count($finalTags)] = $tag;
			}
		}

		$this->tags = $finalTags;
	}

/**
 *   Load image entry data.
 *@param Core $parent Parent TagManager object
 */
	function loadTags($parent)
	{
		$type = $parent->getType();
		//get receipt tags
		$mysqli = $this->getParent('page')->getConnect()->getMysqli();
		$query = 'SELECT ts.tname from tags as ts join tag as t on ts.tname = t.name WHERE ts.rid =? AND t.component =?';

		if($stmnt=$mysqli->prepare($query))
		{
			//print 'what my Id '.$this->id;
			$stmnt->bind_param('ss', $this->id,$type);
			$stmnt->execute();
			$stmnt->bind_result($tname);

			
			while($stmnt->fetch())
			{
				//print '<br />setting tag '.$tname;
				$this->tags[count($this->tags)] = $tname;
			}
		}
	}

/**
 *
 */
	function adContent(){
		parent::adContent();
		echo '<br /><br /><span class="lightTitle">Tag Content:</span>';
		echo '<br /><span class="lightTitle">ID:</span> <span class="id">'.$this->id.'</span>';
		echo '<br /><span class="lightTitle">Tags:</span>';
		foreach($this->tags as $tag)
		{
			echo ' '.$tag.',';
		}

	}

}

?>
