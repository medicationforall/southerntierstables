<?php
/**
 *   Medication For All Framework source file Tab,
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
 *Tab component!
 *   Sample Usage {@link http://www.medicationforall.com/framework/sample/SampleTab.php SampleTab}
 *
 *   {@example ../sample/SampleTab.php SampleTab}
 *
 *@author James M Adams <james@medicationforall.com>
 *@version 0.1
 *@package framework
 */

class Tab extends Core
{
//data

/**
 *List of Tab Header Tables
 *@access private
 *@var array hashmap of Tab Header Titles.
 */
private $titles = array();

//constructor
/**
 *Creates the Tab object.
 *@param string $class Class associated with the tab. Can add multiple classes by delineating with spaces.
 */
	function __construct($class='')
	{
		parent::__construct('tab');
		$this->addClass($class);
	}

//methods
/**
 * Process the Tab object.
 */
	function process()
	{
		$this->script('tab.js');
		$this->children('process');
	}

/**
 * Prints the Tab object. 
 */
	function show()
	{

		$class = $this->getClass();
		if($this->getParent('page')->getAccount()->access($this->getLevel()))
		{

			$this->debug('print tab '.$class);

			$id =$this->getUnique();
			if(empty($id))
			{
				$id='';
			}
			else
			{
				$id =' id="'.$this->getUnique().'"';
			}

			echo('<div'.$id.' class="'.trim($this->getName().' '.$class).'">'."\n");
		//print 'showing tab';

			echo '<ul>';
			foreach($this->titles as $key => $value)
			{
				echo '<li>';
					echo '<a href="#'.$key.'">'.$value.'</a>';
				echo '</li>';
			}

			echo '</ul>';

			$this->children('show');//,$before,$after);

			echo('</div>'."\n");
		}
	}

/**
 *Custom Adder, Adds a Core tree node and tab title.
 *@param Core $obj typically a panel.
 *@param string $title Tab Title
 */
	function add($obj,$title='')
	{

		$unique = $obj->getUnique();

		if(empty($unique))
		{
			$count = count($this->getChildren())+1;
			$unique = $this->getName().ucFirst($this->getUnique()).'-'.$count;
			$obj->setUnique($unique);
		}

		if(empty($this->titles[$unique]))
		{
			if(!empty($title))
			{
				$this->titles[$unique] = $title;
			}
			else
			{
				$this->titles[$unique] = $unique;
			}
		}

		parent::add($obj);
	}

/**
 *   XML content, fills out the data and calls xmlTag on the objects children.
 */
	function xContent()
	{
		$this->debug('xContent '.$this->getName(), 'xml');
		$returner='';

		$children = $this->getChildren();

		if(!empty($children))
		{

			$returner .= "\n".'<'.$this->xHeader().'>';

			foreach($this->titles as $key => $value)
			{
				$returner .= "\n".'<title id="'.$key.'" text="'.$value.'" />';
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
 *Adds a title.
 *@param string $unique Unique ID tab identifier.
 *@param string $text Tab title text.
 *@todo What am I using this for ?
 */
	function addTitle($unique,$text)
	{
		//print 'setting title';
		$this->titles[$unique] = $text; 
	}
}
?>
