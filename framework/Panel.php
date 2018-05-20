<?php

/**
 *   Medication For All Framework source file Panel,
 *   Copyright (C) 2009  James M Adams
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
 *   Div container object used to organize Components. Also useful for HTML 5 special wrapper tags by specifying $tag in the constructor.
 *
 *   Sample Usage {@link http://www.medicationforall.com/framework/sample/SamplePanel.php SamplePanel}
 *
 *   {@example ../sample/SamplePanel.php SamplePanel}
 *
 *@author James M Adams <james@medicationforall.com>
 *@version 0.2
 *@package framework
 */

class Panel extends Core
{
//data 
	/**
	 *   The name classes is in bad form here; this was chosen because class is already used in Core. This should be changed to something less deceptive.
	 *@access private
	 *@var string
	 */
	//private $classes;

	/**
	 *   HTML tag element used in place of div.
	 *@access private
	 *@var string
	 */
	private $tag;

//constructor
/**
 *   Creates the class instance representing a panel or grouping area for page components.
 *@param String $class Name of the div class i.e. 'header' 'left' 'right'
 *@param string $tag The wrapper tag used in the code, useful for HTML 5 specifier tags.
 */
	function __construct($class='',$tag ='div')
	{
		parent::__construct("panel");
		$this->addClass($class);
		
		$this->tag = $tag;
		$this->debug('construct panel '.$class);		
	}

//method
/**
 *   Calls process() for the Panels Children.
 *@param boolean $processChildren Process Children flag. 
 */
	function process($processChildren=true)
	{
		$this->debug('process panel '.$this->getUnique());

		if($processChildren)
		{
			$this->children('process');
		}
	}

/**
 *   Shows the contents of the Panel and it's children.
 */
	function show()
	{
		$class = $this->getClass();
		if($this->getParent('page')->getAccount()->access($this->getLevel()))
		{
			$this->debug('print panel '.$class);

			$id =$this->getUnique();
			if(empty($id))
			{
				$id='';
			}
			else
			{
				$id =' id="'.$this->getUnique().'"';
			}

			echo('<'.$this->tag.$id.' class="'.trim($this->getName().' '.$class).'">'."\n");

			$this->children('show');

			echo('</'.$this->tag.'>'."\n");
		}
	}
/**
 *   Calls the rss methods of the panels children.
 */
	function rss()
	{
		$this->debug('panel rss '.$this->getUnique(), 'RSS');
		$this->children('rss');
	}

/**
 *
 */
	function siteMap(){
		echo '<!--SiteMap For'.$this->getUnique().'-->'."\n";
		//$this->children('siteMap');
		$this->children('siteMap');
	}
}
?>
