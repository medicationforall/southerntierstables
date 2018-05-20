<?php
/**
 *   Medication For All Framework source file TagFilter,
 *   Copyright (C) 2014  James M Adams
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
 *Tag Filter control component
 *
 *@author James M Adams <james@medicationforall.com>
 *@version 0.1
 *@package framework 
 */
class TagFilter extends Component
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

	private $tagType = 'gallery2';

	//private $loaded = false;

//constructor
	function __construct($h='', $type='tagFilter')
	{
		parent::__construct($h, $type);
	}

//methods

	function process(){
		$page = $this->getParent('page');
		$page->script('tagFilter.js');

		//won't see a benefit from this unless storing as app
		//if($loaded == false){
		//	$this->queryTags();
		//}

		if(!empty($_REQUEST['tag'])){

			//print 'found tag to process';
			$tmp = urldecode($_REQUEST['tag']);
			$tmp = explode(',', $tmp);
			$tmpTags = array();

			foreach($tmp as $tag)
			{
				$tag = trim(strtolower($tag));

				if(!empty($tag) && in_array($tag, $tmpTags) == false){
					$tmpTags[count($tmpTags)] = $tag;
					//print ' '.$tag;
				}
			}

			$this->tags = $tmpTags;			
			
		}
	}

	function cContent(){
		echo '<div class="cContent">';
		echo '<form method="GET">';
		echo '<input class="list" type="text" name="tag" value="'.htmlspecialchars($this->printTags()).'" placeholder="Tag Search" />';
		echo '<input class="search" type="submit" value="Apply" />';
		echo '</form>';
		echo '</div>';
	}

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
					$returner .= ', ';
				}

				$returner .= $tag;
			}
		}

		return $returner;
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
 *Returns a list of all tags currently being used.
 *@param boolean $output Flag for whether to print out json response object. Default is true.
 */
	function queryTags($output = true)
	{
		$query = 'SELECT name FROM tag where component = ?';
		$list = array();
		$mysqli = $this->getParent('Page')->getConnect()->getMysqli();

		if($stmnt = $mysqli->prepare($query))
		{
			$type = $this->tagType;
			//print 'type is '.$type;
			$stmnt->bind_param('s',$type);
			$stmnt->execute();
			$stmnt->bind_result($name);
			while($stmnt->fetch())
			{
				//needs to take into account ignored tags
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
 *
 */
	function setTagType($type){
		$this->tagType = $type;
	}


/**
 *
 */
	function short(){
		if(!empty($_REQUEST['queryTags']) && strcmp($_REQUEST['queryTags'], 'true')==0)
		{
			//print 'attempting to query tags';
			$this->queryTags(true);
		}

	}

}

?>
