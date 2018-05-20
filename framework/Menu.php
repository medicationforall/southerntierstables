<?php

/**
 *   Medication For All Framework source file Menu,
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
 *   Category Menu and Submenu items.
 *   This concept works but is very limited in usefulness, ideally the highlight of 
 *   having the menu broken up into objects is the user should at some point
 *   be able to login and edit the menu, and even add pages, but not from here.
 *   That's the goal but the project is a ways off from that.
 *
 *   Notice that this object inherits directly from class "Core" and is not a 
 *   "Component" this is intentional by design, but does not mean that in the 
 *   future that there won't be a ComponentMenu class.
 *
 *   version 0.2: This component was refactored so now submenu elements are just 
 *   further menu objects that are children of the parent menu. which is more intuitive.
 *   There is also no inherent limitation in how many levels deep you can go for submenu 
 *   depth, but keep in mind that you'll have to write CSS that takes into account the 
 *   menu depth.  There are leftover methods in this class from the previous iteration that 
 *   are just placeholder methods now. This is done to keep backwards compatibility for prior 
 *   XML templates that are floating around. 
 *
 *   Sample Usage {@link http://www.medicationforall.com/framework/sample/SampleMenu.php SampleMenu}
 *
 *   {@example ../sample/SampleMenu.php SampleMenu}
 *
 *@author James M Adams <james@medicationforall.com>
 *@version 0.2
 *@package framework
 */

class Menu extends Core
{
//data
	/**
	 *   Menu link text.
	 *@access private
	 *@var string
	 */
	private $text;

	/**
	 *   Href link.
	 *@access private
	 *@var string
	 */
	private $link;

	/**
	*   Boolean Target open link in new window flag
	*@access private
	*@var boolean
	*/
	private $target = false;

/**
 *Flag that marks the the link is dependent upon url state ie get variable/s
 *@acess private
 *@var boolean
 */
	private $state = false;

/**
 *Flag for whether to treat submenu's as hover menu's
 *@acces private
 *@var boolean
 */
	private $hoverState = true;

//constructor
/**
 *   Creates the Menu object. Represent a "menu category", with submenu links. 
 *   To have multiple categories you'll have multiple menus.
 *
 *@param string $t Menu item text
 *@param string $l Hyperlink for the top menu (optional)
 *@param string $m default access level ie the user level access permission required to see this menu item. 
 *The default is none meaning you need no permissions to view the menu.
 */
	function __construct($t="",$l="",$m='none')
	{
		parent::__construct('menu');

		$this->text = $t;

		if(!empty($l))
		{
			$this->link = $l;
		}

		$this->setLevel($m);
		$this->setShowPreference(true);
	}

//methods
/**
 *   Process for menu, adds menu.js to the list of loaded scripts..
 *@param string $processChildren Process Children flag. 
 */
	function process($processChildren=true)
	{
		$this->debug('process Menu '.$this->text);
		$this->script('menu.js');

		if(empty($this->link)){
			$this->addClass('label');
		}

		//this was done so preference dialogs would display
		parent::process();

		if($processChildren)
		{
			$this->children('process');
		}
	}

/**
 *   Display the menu and it's children. This function goes out of it's 
 *   way to show add divs for submenu's.
 *@param boolean $coreShow Flag for running core's show method.Which gives you a free preference and delete link when combined with an xml component.
 */
	function show($coreShow=true)
	{
		//this was added for preference dialog link to show
		if($coreShow)
		{
		//print '<br />calling parent show for '.$this->getUnique();
		parent::show();
		}

		if($this->getParent('page')->getAccount()->access($this->getLevel()))//tests the current users level against the objects assigned level.
		{
			$this->debug('print Menu');
			//$menu = 'menu';
			$parent = $this->getParent();
			$children = $this->getChildren();

			$menuCheck = false;
			$subMenuCheck = false;

			$id = $this->getUnique();
			if(!empty($id))
			{
				$id='id='.$this->getUnique();
			}
			else
			{
				$id='';
			}

			if($this->hoverState == false)
			{
				$this->addClass("noHover");
			}
		

			if(strcmp($parent->getName(), 'menu')!=0)
			{
				echo '<div '.$id.' class="menu '.$this->getClass().'">';
				$menuCheck = true;
			}

			if(!empty($this->link))
			{
				echo "\n".$this->printLink($this->text, $this->link);
			}
			else
			{
				echo "\n".$this->text;
			}

			if($this->findChildByName('componentPreference') || $this->findChildByName('componentMenuPreference'))
			{
				//print'found menu preference';
				//echo "\n".'<div class="subMenuPref">';
				//$subMenuCheck = true;
			}
			//this is going to break
			//else if(strcmp($parent->getName(), 'menu')!=0 && !empty($children))
			else if(!empty($children))			
			{

				echo "\n".'<div class="subMenu">';
				$subMenuCheck = true;
			}
			else
			{
				//print "not a submenu ".$parent->getName();
			}
				$this->children('show');

			if($subMenuCheck)
			{
				echo "\n".'</div>';
			}

			if($menuCheck)
			{
				echo "\n".'</div>';
			}
		}
	}

/**
 *   Prints a link!
 *   Helper method: Takes into account if the page the user is on is the 
 *   link that's being printed, and doesn't create a link but an em tag instead.
 *@param string $n Link name
 *@param string $l Link href
 *@return string The HTML of the link to be printed.
 */
	function printLink($n, $l)
	{	
		$target = '';
		if($this->target)
		{
			$target = 'target="_blank"';
		}

		$returner = '<a href="'.$l.'" '.$target.'>'.$n.'</a>';

		//unique use case to deal with an empty link

		if($this->state == false)
		{
			if(empty($l))
			{
				$returner = $n;
			}
			else if(strstr($_SERVER['PHP_SELF'], '/'.$l))
			{
				//print '<div class="white">$l '.$l.' '.$_SERVER['PHP_SELF'].'</div>';
				$returner = '<em class="page">'.$n.'</em>';
			}
		}
		else
		{
			$link = explode('/', $_SERVER['REQUEST_URI']);

			$link = $link[count($link)-1];

			//print '<div class="white">$l'.$l.' $link'.$link.'</div>';

			if(strcmp($link, $l)==0 || (strcmp($l, 'index.php') ==0 && empty($link)))
			{
				$returner = '<em class="page">'.$n.'</em>';
			}
		}
		return $returner;
	}

/**
 *   Create the XML header text
 *@return string
 */
	function xHeader()
	{
		$returner ='';

		$returner .=parent::xHeader();

		if(!empty($this->link))
		{
			$target='';
			if($this->target)
			{
				$target='target="true"';
			}
			$returner .= ' text="'.$this->text.'" href="'.$this->link.'" '.$target;
		}
		else
		{
			$returner .=' text="'.$this->text.'"';
		}

		return $returner;
	}


/**
 *   Creates the XML tagset content and processes children
 *@return string
 */
	function xContent()
	{
		$this->debug('xContent '.$this->getName());
		//print 'calling menu xContent';

		$returner='';
		$children = $this->getChildren();

		if(!empty($children))
		{
			$returner .= "\n".'<'.$this->xHeader().'>';

			if(!empty($children))
			{
				$count = count($children);
				for($i=0;$i<$count;$i++)
				{
					$returner .= $children[$i]->xContent();
				}
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
 *   Adds a link to the Menu.
 *@param string $n Link text and identifying key
 *@param string $l Link href (not optional)
 */
	function addLink($n, $l='')
	{
		//print "<br />".$this->getLevel()." ".$n." ".$this->getText();
		$tmp = new Menu($n, $l, $this->getLevel());
		$this->add($tmp);
	}

/**
 *   Adds a subMenu to the menu. Notice that this function now has a lot in 
 *   common with addLink, the only difference is it doesn't persist level permissions.
 *@param string $n Link text and identifying key
 *@param string $l Link href (not optional, but it probably should be)
 *@param string $m Permission acces level.
 *@todo Come up with a means for checking that the permission level passed is valid.
 */
	function addSubMenu($n, $l='', $m = 'none')
	{
		$tmp = new Menu($n, $l, $m);
		$this->add($tmp);
		return $tmp;
	}

/**
 *
 */
	/*function preference()
	{
		//print 'menu preference';
		if($this->findChildByName('componentMenuPreference') == null)
		{
			$this->add(new ComponentMenuPreference());
		}
	}*/

/**
 *   Gets the hyperlink reference.
 *@return string Hyperlink reference.
 */
	function getHref()
	{
		return $this->link;
	}

/**
 *Sets the hyperlink reference
 *@param string $h Hyperlink reference.
 */
	function setHref($h)
	{
		$this->link = $this->parse($h, 'strip');


	}

/**

 *   Gets the text header of this menu.
 *@return string Menu header text.
 */
	function getText()
	{
		return $this->text;
	}

/**
 *Sets the menu text label.
 *@param String $t
 */
	function setText($t)
	{
		$this->text = $this->parse($t, 'strip'); 
	}

/**
 *   Sets the target flag. For having the link open in a new window.
 *@param boolean $t Boolean for setting what window the menu link opens in.
 */
 	function setTarget($t)
 	{
		$this->target = $t;
 	}

/**
 *Get the target value.
 *@return string
 */
	function getTarget()
	{
		return $this->target;
	}

/**
 *Custom awesome debug content.
 */
	function adContent()
	{
		echo '<br /><br /><span class="lightTitle">Additional Content:</span>';
		parent::adContent();
		echo '<br /> '.'<span class="lightTitle">Text:</span> '.$this->text;
		echo '<br /> '.'<span class="lightTitle">Link:</span> '.$this->link;
		echo '<br /> '.'<span class="lightTitle">Target:</span> ';
		if($this->target)
		{echo 'true';}
		else
		{echo 'false';}

	}

/**
 *Sets the menu state flag
 *@param boolean $s
 */
	function setState($s)
	{
		$this->state=$s;
	}

/**
 *Sets the hover state flag
 *@param boolean $hs
 */
	function setHoverState($hs)
	{
		$this->hoverState = $hs;
	}

	function siteMap(){

		if($this->target==false && empty($this->link)==false){
			echo '<url>'."\n";
			if(strpos($this->link,'http') !== false){
				//echo '<!--test http found-->';
				echo ' <loc>'.$this->link.'</loc>'."\n";
			}else{
				//echo '<!--test http not found-->';
				echo ' <loc>'.$this->curPageURL().str_replace('sitemap.php','',$_SERVER["PHP_SELF"]).$this->link.'</loc>'."\n";
			}
			
			echo '</url>'."\n";
		}

		$this->children('siteMap');
	}
}

?>
