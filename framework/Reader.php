<?php
/**
 *   Medication For All Framework source file Reader,
 *   Copyright (C) 2011  James M Adams
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
 *Reads external rss feeds and displays the content. Inherits from XML in order to utilize the parser.
 *
 *   Sample Usage {@link http://www.medicationforall.com/framework/sample/SampleReader.php SampleReader}
 *
 *   {@example ../sample/SampleReader.php SampleReader}
 *
 *@author James M Adams <james@medicationforall.com>
 *@version 0.1
 *@package framework
 */
class Reader extends XML
{

//data
	/**
	 *Arraylist of class Feeds which can be hard coded or stored via xml.
	 *@access private
	 *@var array
	*/
	private $feeds = array(); 

	/**
	 *Title given to feed items
	 *@access private
	 *@var string
	 */
	private $title='';

	/**
	 *Class feedname given to Items as a class name.
	 *@access private
	 *@var string
	 */
	private $feedId='';

	/**
	 *Head text.
	 *@access private
	 *@var string
	 */
	private $head;


	private $limit=null;

/**
 *Custom Awesome Debug Content.
 */
	function adContent()
	{
		parent::adContent();
		echo '<br /><br /> Reader:';
		echo '<br /> <span class="lightTitle">title:</span> '.$this->title;
		echo '<br /> <span class="lightTitle">feedId:</span> '.$this->feedId;
		echo '<br /> <span class="lightTitle">head:</span> '.$this->head;
		echo '<br /> <span class="lightTitle">feed count:</span> '.count($this->feeds);

		foreach($this->feeds as $feed)
		{
			$feed->awesomeDebug();
		}
		
	} 

//constructor
/**
 *Creates the Feed Reader object.
 *@param string $h Header Text.
 *@param string $u Unique Identifier.
 *@param string $n Type Name.
 */
	function __construct($h='', $u='', $n='Reader')
	{
		parent::__construct($u, '', $n);
		$this->head = $h;
	}

//methods
//potentially an rss mode for itself

/**
 *Process the reader.
 *@param boolean $processChildren Currently not used.
 */
	function process($processChildren=true)
	{
		$this->setRoot($this);

		$this->script('feed.js');

		$childset = array();

		$newChildren = array();


		//print '<br />feed Count '.count($this->feeds);

 
		foreach($this->feeds as $feed)
		{
			//echo '<br />traditional feed loop '.$feed->getName();
			$feed->setParent($this->getParent('page'));
			$file =  $feed->getContent();

			//print '<br /> file Content:'.$file;

			$this->parseXML($file);

			//print $this->xContent();

			$this->title = $feed->getName();
			$this->feedId = $feed->getId();

			$children = $this->getChildren();

			if(!empty($children))
			{
				$childset[$feed->getId()] = $this->consume($children);
			}

			$this->setChildren(null);
		}

		foreach($childset as $set)
		{
			$newChildren = array_merge($newChildren, $set);
		}

		usort($newChildren, array( $this , "cmp_pubdate"));

		if($this->limit && $this->limit > 0){
			$newChildren = array_slice($newChildren,0,$this->limit);
		} 

		$this->setChildren($newChildren);
	}

/**
 *Compare publish date of Item objects, this sorts entries according to publish date. It's a callback function used for usort.
 *@param Item $a
 *@param Item $b
 *@return int
 */
	static function cmp_pubdate($a, $b)
	{
		//print 'printing a '.$a.get();
		$a_t = strtotime($a->getPubDate());
		$b_t = strtotime($b->getPubDate()); 

		if( $a_t == $b_t )
		{
			return 0 ;
		}
		return ($a_t > $b_t ) ? -1 : 1; 
	}
/**
 *Prints out the reader.
 */
	function show()
	{
		$id=$this->getUnique();

		if(!empty($id))
		{
			$id=' id="'.$id.'"';
		}
		else
		{
			$id='';
		}

		echo '<div'.$id.' class="reader">';
		echo '<div class="header">'.$this->head.'</div>';
		echo '<div class="content">';
		$this->children('show');
		echo '</div>';
		echo '</div>';
	}

/**
 *2nd pass for rss feed data, converts each item into a meaningful Item class
 *@param array $children ArrayList of RSS Items.
 *@return array
 *@see Item
 */
	function consume($children)
	{
		$returner = array();
		$title = "";

		//print '<br />new consume instance '.$this->title;

		foreach($children as $child)
		{

		//print '<br />its '.$child->getName().' '.$child->getValue().' '.count($child->getChildren());
			if(strcmp($child->getName(), 'rss')==0  || strcmp($child->getName(), 'channel')==0 || strcmp($child->getName(), 'rdf:RDF')==0 || strcmp($child->getName(), 'feed')==0)
			{
				//print '<br />it\'s '.$child->getName();
				$returner = $this->consume($child->getChildren());
			}
			else if(strcmp($child->getName(), 'title')==0)
			{
				//print 'found title '.$child->getValue();
				$returner = $this->consume($child->getChildren());

				if(!empty($returner))
				{
					foreach($returner as $text)
					{
						//print 'find child text';
						$title = $text->getValue();
					}
				}
				$returner = array();
			}
			else if(strcmp($child->getName(), 'item')==0 || strcmp($child->getName(), 'entry')==0)
			{
				//print '<br />Creating Item<br />';				
				$iChildren = $child->getChildren();

				$item = new Item();				
				$item->setTitle($this->title);
				if(!empty($title))
				{
					$item->setTitle($title);
				}
				$item->setId($this->feedId);

				foreach($iChildren as $iChild)
				{
					$item->setValue($iChild);
				}

				$returner[count($returner)] = $item;
			}
			else if(strcmp($child->getName(), 'text')==0)
			{
				$returner[count($returner)] = $child;
			}
			else
			{
				//print '<br />its '.$child->getName().' '.$child->getValue();
			}
		}
	//	print 'ending consume';

		return $returner;
	}

/**
 *The reader has no feed content of its own
 */
	function rss()
	{

	}

/**
 *Adds a feed to the list of processed rss feeds.
 *@param string $name
 *@param string $id
 *@param string $url
 *@param int $frequency Time in minutes that a feed should wait to be updated.
 */
	function addFeed($name, $id, $url, $frequency=60)
	{
		//print '<br />add feed '.$id;
		$feed = new Feed($name, $id, $url, $frequency);

		$this->feeds[count($this->feeds)] = $feed;
	}

/**
 *Overrides addELement in XML.
 *@param string $tag Tag name.
 *@param array $p Parameters.
 *@param array $v Values.
 *@return Core 
 */
	function addElement($tag, $p=null, $v=null)
	{
		//print '<br />adding '.$tag.' to '.$this->root->getName();

		//$this->debug('Add element '.$tag,'xml');

		$returner = null;

		if(strcmp($tag, '?xml?')!=0 && strcmp($tag, '?xml')!=0 && strcmp($tag, '?xml-stylesheet')!=0 && strcmp($tag, '?xml-stylesheet?')!=0)
		{
		$tmp = new Core($tag);
		
		if(!empty($p) && !empty($p))
		{
			$tmp->setAttributes($p, $v);
		}
		$returner = $tmp;
		//print '<br />add element '.$tag;
		}

		return $returner;
	}

/**
 *Adds and object to the reader.
 *@param Core $obj
 *@todo probably not needed, since we're not performing custom handling.
 */
	function add($obj)
	{
		//print '<br />Adding '.$obj->getName().' to '.$this->getName();
		//print '<br /> '.get_class($obj);
		//print '<br />';
		//print print_r($obj);
		parent::add($obj);
		//print "adding to reader ".$obj->getName().' '.count($this->getChildren()."\n");
	}


/**
 *   Custom XML header.
 *@return string XML header text.
 */
	function xHeader()
	{
		$returner ='';
		$returner .=parent::xHeader();

		if(!empty($this->head))
		{
			$returner .= ' head="'.$this->head.'"';
		}

		return $returner;
	}

/**
 *   XML content, fills out the data and calls xmlTag on the objects children.
 *@return string XML content output.
 */
	function xContent()
	{
		$this->debug('xContent '.$this->getName(), 'xml');
		$returner='';

		$value = $this->getValue();

		if(!empty($this->children) || !empty($value) || !empty($this->feeds))
		{
			$children = $this->getChildren();
			$returner .= "\n".'<'.$this->xHeader().'>';

			$returner .= $value;

			//print 'xContent count '.count($this->feeds);
			foreach($this->feeds as $feed)
			{
				$returner .= $feed->xContent();
			}

			$count = count($children);
			for($i=0;$i<$count;$i++)
			{
				$returner .= $children[$i]->xContent();
			}
			$returner .= "\n".'</'.$this->getName().'>';
		}
		else
		{
			$returner .="\n".'<'.$this->xHeader().' />';
		}
		return $returner;
	}

/**
 *   Sets the header text.
 *   This same function name is used in Page. Although arguably more descriptive here as to what it does.
 *@param string $h Header Text.
 *@see Page::setHead()
 */	
	function setHead($h)
	{
		$this->head = $h;
	}

/**
 *
 */
	function setLimit($l){
		$this->limit = $l;
	}

}
?>
