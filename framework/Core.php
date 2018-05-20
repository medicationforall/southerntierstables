<?php
/**
 *   Medication For All Framework source file Core,
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
 *   Core is a tree data structure; it's the base class of all objects in the framework.
 *   It provides the basic data structures and methods inherited in all of the frameworks classes.
 *
 *   You always have a root element typically of the class Page. 
 *   Off of the root are it's children, typically Panels and Components.
 *   From a child or branch you can always call back to methods or variables all way up to the root element.
 *
 *   By doing this you only have to have one session variable for the site in order to create a reusable template, 
 *   and that links to your root element.
 *
 *   When dealing with the framework always keep this class in mind.
 *
 *@wishlist Implement namespace, for using with other frameworks. Priority low.
 *@wishlist Core create ComponentAccount for managing logged in users avatar upload. Priority low.
 *@wishlist Create database installation script "dbsetup.php". Priority low.
 *@todo dialog.js cancels on .cContent meaning if a draggable dialog was placed into a cContent then it is no longer draggable.
 *
 *@author  James M Adams <james@medicationforall.com>
 *@see Page
 *@see Component
 *@see Panel
 *@version 0.2
 *
 *@package framework
 */

class Core
{
//data

//Tree
	/**
	 *   Parent.
	 *@access private
	 *@var array
	 */
	private $parent;

	/**
	 *   Children arraylist.
	 *@access private
	 *@var array
	 */
	private $children = array();

	/**
	 * Primarily used when intantiating on object of just the generic type Core directly. This is done by the HTML Parser; which creates pleaceholder core objects as stand ins for HTML tags.
	 *@access private
	 *@var string
	 */
	private $value = "";


	/**
	 *Generic tag attributes
	 *@access private
	 *@var array
	 *@todo can probably cut this down to one hashmap since attribute order does not matter.
	 */
	private $attributes = array();

	/**
	 *Generic tag values
	 *@access private
	 *@var array
	 */
	private $values =  array();

//Naming
	/**
	 *   Complete object name including parent and type ie ComponentText would have a name of "componentText",
	 *   but it would also have a type of "text".
	 *@access private
	 *@var string
	 */
	private $name;

	/**
	 *   Component type/class name
	 *@access private
	 *@var string
	 */
	private $type;

	/**
	 *   Unique css id name. Used for referencing a core object along with it's page in the database.
	 *@access private
	 *@var string
	 */
	private $unique='';

	/**
	 *   Arraylist of css class names. 
	 *@access private
	 *@var array
	 */
	private $classes = array();

	/**
	 *   Page name.
	 *@access private
	 *@var string
	 */
	private $file;

//Access
	/**
	 *   Permission level needed to access an object.
	 *@access private
	 *@var string
	 */
	private $level = 'none';

	/**
	 *   List of permission modes.
	 *@access private
	 *@var array
	 */
	 private static $modes = array('none' => 0, 'user' => 1, 'edit' => 2, 'admin' => 3);

	/**
	 *Has owner flag.
	 *@access private
	 *@var boolean
	 */
	private $hasOwner = false;

	/**
	 *Has edit permission flag.
	 *@access private
	 *@var boolean
	 */
	private $canEdit =false;


	/**
	 *   Flag which defines object cloning. When true the object is considered static and thus is not cloned; 
     *   passing along the object reference instead of a copy.
	 *@access private
	 *@var boolean
	 */
	private $static = false;


//Other

	/**
	 *   Static debugger.
	 *@access private
	 *@var array
	 */
	private static $debugger = array();

	/**
	 *   ShowPreference.
	 *@access private
	 *@var boolean
	 */
	private $showPreference = false;

//constructor
/**
 *   Constructs the core object.
 *@param string $n Name of the object e.g. "component" "panel".
 */
	function __construct($n='Core')
	{
		$this->name=$n;
		$this->debug('construct core '.$this->name);
	}

/**
 *   Engine that makes the framework tick. 
 *   Any variables that are a reference to this class object will have to be manually cloned or else the original reference is left intact.
 *   Overwrite this method to handle unique cases, but remember to still call this clone method as well, parent::__clone()
 *
 *   This method doesn't control if clone is applied to the base object, just whether clone is applied to the base objects children in turn. 
 *   By the time I've gotten to this method the cloning of the base object has already occurred.
 *   If you don't want clone to occur on base object then simply don't clone it!
 *
 * @todo Would the each() method be an option for overcoming the base object problem ?
 *
 *@see Page::__clone()
 */
 	function __clone()
	{
		$this->debug('clone '.$this->name.' '.$this->type.' '.$this->unique);

		$tmp = array();
		$count = count($this->children);
		for($i=0;$i<$count;$i++)
		{
			if($this->children[$i]->isStatic() == false)
			{
				$tmp[count($tmp)] = clone $this->children[$i];

				//this is crucial, otherwise your running a reference against the original core class !
				$tmp[count($tmp)-1]->setParent($this);
				$tmp[count($tmp)-1]->setFile();
			}
			else//If not cloned the initial object retains it's system state across all pages it's used on.
			{
				$tmp[count($tmp)] = $this->children[$i];
			}
		}
		$this->children = $tmp;
	}

/**
 *   Run before show(), responsible for processing all of the get/post data passed.
 *   All Data processing is handled by each classes respective process() function.
 * 
 *   Note once a process children subroutine starts; a child cannot add children onto the 
 *   parent because the child count is static in the loop structure. This is a natural limitation to keep in mind.
 */
	function process()
	{
		if($parent = $this->getParent('page'))
		{
			//dialog positioning
			if(!empty($_REQUEST['dTopPos']))
			{
				$top = $this->parse($_REQUEST['dTopPos']);
				$parent->getSettings()->setTop($top);
			}

			if(!empty($_REQUEST['dLeftPos']))
			{
				$left = $this->parse($_REQUEST['dLeftPos']);
				$parent->getSettings()->setLeft($left);
			}
		}
		//preference dialog link process
		if($xml = $this->getParent('xml'))
		{
			if($page=$xml->getParent('page'))
			{
				$account = $page->getAccount();

				if($account->access('admin'))
				{
					if(!empty($_REQUEST['dpref']) && strcmp($_REQUEST['dpref'], ucFirst($this->getName()).ucFirst($this->getUnique()))==0)
					{
						$this->preference();
					}
				}
			}
		}
	}

/**
 *   Prints the contents of the object onto the page.
 *   All Content shown is handled by each classes respective show() function.
 */	
	function show()
	{	
		//preference dialog link print
		if($this->getParent('page')->getAccount()->isEdit())
		{
			if($xml = $this->getParent('xml'))
			{
				if($page=$xml->getParent('page'))
				{
					$account = $page->getAccount();


					if($account->access('admin') && $this->getParent('xml') !== null)
					{
						echo '<a class="itemDelete" href="?ditem='.$this->getUnique().'" title="Delete '.$this->getType().'">Delete</a> ';
						if($this->showPreference)
						{	
							//print 'show pref linky';
							echo '<a class="prefLink" href="?dpref='.ucFirst($this->getName()).ucFirst($this->getUnique()).'" title="Open Preference Dialog for '.$this->getType().'">&nbsp;</a> ';
						}
					}
				}
			}
		}
	}

/**
 *Set the edit preference option display.
 *@param boolean $s Flag for showpreference
 */
	function setShowPreference($s)
	{
		$this->showPreference = $s;
	}

/**
 *Get the edit preference option display.
 *@todo Deprecate and rename to isShowPreference.
 *@return boolean showPreference
 */
	function getShowPreference()
	{
		return $this->showPreference;
	}

/**
 *   Used in place of method show() if the object is in edit mode; determined by process().
 */
	function edit()
	{
		echo('edit not written yet');
	}

  /**
   *  placeholder function for ajax requests.
   */
   	function short()
   	{
		print 'core short';
		$this->process();
   	}

/**
 *   Placeholder rss method to be overwritten by components that utilize RSS.
 */
	function rss()
	{
		$this->debug($this->name.' '.$this->getUnique().' rss not yet written', 'RSS');
		$this->children('rss');
	}

	function json()
	{
		$this->debug($this->name.' '.$this->getUnique().' rss not yet written', 'RSS');
		$this->iterateJson();
		return false;
	}

/**
 *   Placeholder method.
 *@return XML tag name.
 */
	function xHeader()
	{
		$returner = ''. $this->name;
		$level = $this->getLevel();
		$unique = $this->getUnique();
		$class = trim(str_replace($this->getType(), '', $this->getClass()));

		if(!empty($unique))
		{
			$returner .=' id="'.$unique.'"';
		}

		if(strcmp($level, 'none')!=0)
		{
			$returner .= ' lv="'.$level.'"';
		}

		if(!empty($class))
		{
			$returner .=' class="'.$class.'"';
		}

		foreach($this->attributes as $attr)
		{
			$returner .=' '.$attr.'="'.$this->values[$attr].'"';
		}

		return $returner;
	}

/**
 *   XML content, fills out the data and calls xmlTag on the objects children.
 */
	function xContent()
	{
		$this->debug('xContent '.$this->name, 'xml');
		$returner='';

		$value = $this->getValue();

		if($this->name=='text')
		{
			$returner.=$value;
			$count = count($this->children);
			for($i=0;$i<$count;$i++)
			{
				$returner .= $this->children[$i]->xContent();

				//check for text's here
				if(strcmp($this->children[$i]->getName(), 'text')==0)
				{
					if($i+1 < $count)
					{
						$returner.=' ';
					}
				}
			}
		}
		else if(!empty($this->children) || !empty($value))
		{
			$returner .= "\n".'<'.$this->xHeader().'>';

			$returner .= $value;

			$count = count($this->children);
			for($i=0;$i<$count;$i++)
			{
				$returner .= $this->children[$i]->xContent();

				//check for text's here
				if(strcmp($this->children[$i]->getName(), 'text')==0)
				{
					if($i+1 < $count)
					{
						$returner.=' ';
					}
				}
			}
			$returner .= "\n".'</'.$this->name.'>';
		}
		else
		{
			$returner .="\n".'<'.$this->xHeader().' />';
		}
		return $returner;
	}

//Tree traversal
/**
 *   Allows a child to call to it's parent object, this is good for running code on an instance up the chain.
 *@param string $n Optional name of the parent object you wish to have returned. If empty returns the direct parent.
 *@return Core Either the parent matching the given criteria or the direct parent of this object. Can return null.
 */
	function getParent($n = '')
	{
		$returner = null;

		//has parent
		if($this->parent!=null)
		{
			//$n not empty
			if(!empty($n))
			{
				//parent name = $n
				if(strcmp(strtolower($this->parent->getName()), strtolower($n))==0)
				{
					$returner =  $this->parent;
				}
				else//$n does not match keep searching
				{
					$returner = $this->parent->getParent($n);
				}
			}
			else
			{
				$returner = $this->parent;
			}
		}
		else
		{
			//print 'no parent match returning null '.$n."\n";
		}
		return $returner;
	}


/**
 *   Sets the parent.
 *@param Core $obj Sets Core object to be set as parent.
 */
	function setParent($obj)
	{
		$this->parent = $obj;
	}


/**
 *   Adds a child.
 *@param Core $obj Class instance which inherits from core, gets added to the current objects children.
 */
	function add($obj)
	{
		if(is_subclass_of($obj,'Core') || strcmp(get_class($obj),'Core')==0){
			$this->debug('Adding '.$obj->getName().' to '.$this->getName());
			$obj->setParent($this);
			//$obj->setLevel($this->getLevel());
			$this->children[count($this->children)]= $obj;
		}
		else{
			throw new Exception('Failed to add to Core tree: Object is not a Core class or subclass of Core.');
		}
	}


/**
 *   Adds the object to the beginning of the children array.
 *@param Core $obj Class instance which inherits from core.
 */
	function prepend($obj)
	{
		$obj->setParent($this);
		array_unshift($this->children, $obj);
	}

/**
 * Places the given node after the current node in reference to the parent object.
 */
	function after($obj){
		//print 'calling after';
		$parent = $this->getParent();
		$children = $parent->getChildren();
		$newChildren = array();

		foreach($children as $child){
			$newChildren[count($newChildren)]= $child;

			if($child === $this){
				$obj->setParent($parent);
				$newChildren[count($newChildren)]= $obj;
			}
		}
		$parent->setChildren($newChildren);				
	}


/**
 *   Unique actions to be taken upon object deletion.
 *@see Core:deleteChild
 */
	function delete()
	{
		$this->debug('deleted '.$this->unique);
		return true;
	}


/**
 *Add the generic preference dialog
 *@param string $className
 */
	function preference($className = 'Preference')
	{
		//print 'calling generic preference';

		$pass=true;
		if($this->findChildByName($className) == null)
		{
			if(strcmp($className, 'Preference')==0)
			{
				//print 'test name '.$this->getName();

				$tmp = ucfirst($this->getName()).'Preference'; 
				//concession made for the component name change
				$tmp = str_ireplace('component', '', $tmp);

				if(class_exists($tmp))
				{
					$className = $tmp;

					if($this->findChildByName($className))
					{
						$pass=false;
					}
				}
			}

			if($pass)
			{
				$this->add(new $className());
			}
		}
	}

/**
 *   Run a function on all of this objects children.
 *@param string  $function name of function to be ran.
 */
	function children($function)
	{
		$count = count($this->children);
		for($i=0;$i<$count;$i++)
		{
			$this->children[$i]->callFunction($function);
		}
	}


	function iterateJson()
	{
		$count = count($this->children);
		for($i=0;$i<$count;$i++)
		{
			

			if($this->children[$i]->json() && $i+1<$count){
				echo ',';
			}
		}
	}

	

/**
 *Takes in a function callback and runs it against the object and all of it's children
 *@param function $callBack The passed in method when ran is passed a parameter of the current object being worked upon. It must accept a parameter of type core.
 *@throw should throw a custom exception here for empty callback.
 */
	function each($callBack)
	{
		$returner = false;

		if(!empty($callBack))
		{
			//print 'callback is not empty';
			if($callBack($this))//runs the callback
			{
				$returner = true;
			}

			if(!empty($this->children))
			{
				foreach($this->children as $child)
				{
					if($child->each($callBack))
					{
						$returner = true;
					}
				}
			}
		}
		else
		{
			print 'callback is empty';
		}
		return $returner;
	}

/**
 *   Runs the passed function.
 *@param string $function value of the function to be called.
 */
	function callFunction($function)
	{
		$this->$function();
	}

/**
 *   Gets a child object at the chosen index, this  may return null.
 *@param int $i Index of the child to be returned.
 *@return Core Child of the current class at the given index.
 */
	function getChild($i)
	{
		$returner = null;
		$count = count($this->children);

		if(!empty($this->children))
		{
			if($i>=0 || $i < $count)
			{
				//check done to avoid php warning, doesn't like when undefined variables are passed.
				if(!empty($this->children[$i]))
				{
					$returner = $this->children[$i];
				}
				else
				{
					$returner = null;
				}
			}
			else
			{
				print('<div class="error">Outside of Bounds</div>');
			}
		}
		return $returner;
	}

/**
 *   Sets the child element at a specific index
 *@param int $i index
 *@param $c class Core inherited Object
 */
	function setChild($i, $c)
	{
		$count = count($this->children);

		if($i>=0 || $i < $count)
		{
			$this->children[$i] = $c;
		}
		else
		{
			print('<div class="error">Outside of Bounds</div>');
		}
	}

/**
 *   Gets the children of the current object, may return an empty array.
 *@return Core
 */
	function getChildren()
	{
		return $this->children;
	}

/**
 *   Replaces all of the objects children.
 *@param Core $c Core object to be set as this object children
 */
	function setChildren($c)
	{
		$this->children = $c;
	}

/**
 *
 */
	function clearChildren(){
		$this->children = array();
	}

/**
 *   Recursively searches children for a match with their unique name.
 *   Will search all of the objects children including grandchildren, and great grandchildren and so on.
 *   If a match is not found will return null.
 *@param string $u Unique identifier name to be searched for.
 *@return Core Child which matches the unique name.
 */
	function findChild($u)
	{
		$returner = null;
		for($i=0;$i<count($this->children);$i++)
		{
			if(strcmp($this->children[$i]->getUnique(), $u)==0)
			{
				$returner = $this->children[$i];
			}
			else
			{
				if(($find = $this->children[$i]->findChild($u)) != null)
				{
					$returner = $find;
				}
			}
		}
		return $returner;
	}

/**
 *   Find Children by complete name ie Meta would be found by searching for "componentMeta" instead of "meta".
 *@param string $t
 *@return array
 */
	function findChildByName($t)
	{
		$returner = array();
		for($i=0;$i<count($this->children);$i++)
		{
			if(strcmp(strtolower($this->children[$i]->getName()), strtolower($t))==0)
			{
				$returner[count($returner)] = $this->children[$i];
			}
			else
			{
				if(($find = $this->children[$i]->findChildByName($t)) != null)
				{
					$returner = array_merge($returner, $find);
				}
			}
		}
		return $returner;
	}

/**
 *
 */
	function lastChild()
	{
		$returner = null;
		if(count($this->children)>0)
		{
			$returner = $this->children[count($this->children)-1];
		}

		return $returner;
	}

/**
 *   Removes the child by unique name from this object.
 *@param string $u Unique child name.
 *@return boolean
 */
	function deleteChild($u)
	{
		$tmp = array();

		$returner = false;

		for($i=0;$i<count($this->children);$i++)
		{
			if(strcmp($this->children[$i]->getUnique(), $u)==0)
			{
				$returner = $this->children[$i]->delete();
			}
			else
			{
				if(($find = $this->children[$i]->deleteChild($u)))
				{
					$returner = $find;
				}

				$tmp[count($tmp)] = $this->children[$i];
			}
		}
		$this->children = $tmp;
		return $returner;
	}

/**
 *   Deletes children by name ie ComponentText. Will stop traversing nests if it finds a hit.
 *@param string $n
 */
	function deleteChildByName($n)
	{
		//print "\n".'calling delete child by name ';
		$tmp = array();

		$returner = false;

		for($i=0;$i<count($this->children);$i++)
		{
			//print "\n".'deleteChildByName '.$this->children[$i]->getName();
			if(strcmp($this->children[$i]->getName(), $n)==0)
			{
				//print "\n".'found child to remove';
				$returner = $this->children[$i]->delete();
			}
			else
			{
				if(($find = $this->children[$i]->deleteChildByName($n)))
				{
					$returner = $find;
				}
				$tmp[count($tmp)] = $this->children[$i];
			}
		}
		$this->children = $tmp;
		return $returner;
	}


//Methods
/**
 *   Gets the components type.
 *@return string Component type
 */
	function getType()
	{
		return $this->type;
	}

/**
 *   Sets the components type.
 *@param string $t The components type.
 */
	function setType($t)
	{
		$this->type = $t;
	}

/**
 *   Gets the name.
 *@return string Name of the object.
 */
	function getName()
	{
		return $this->name;


	}

/**
 *   Gets the unique identifier name.
 *@return string Unique identifier name.
 */
	function getUnique()
	{
		return $this->unique;
	}

/**
 *   Sets the unique Identifier Name.
 *@param string $u Unique identifier name.
 */
	function setUnique($u)
	{
		$this->debug('Set unique for '.$this->name.' '.$u);
		$this->unique = $u;
	}

/**
 *   Gets the class names associated with this object.
 *@return string Class names delineated by spaces.
 */
	function getClass()
	{
		$returner = '';
		$count = count($this->classes);

		for($i=0;$i<$count;$i++)
		{
			if(strcmp($this->classes[$i], $this->getUnique())!=0)
			{
				$returner .= $this->classes[$i];

				if($i+1 <$count &&(strcmp($this->classes[$i+1], $this->getUnique())!=0))
				{
					$returner .= ' ';
				}
			}
		}
		return $returner;
	}

/**
 *check to see if a class is assigned to this object.
 *@param string $f
 */
	function findClass($f)
	{
		$returner = false;

		if(in_array($f, $this->classes))
		{
			$returner = true;
		}

		return $returner;
	}

/**
 *   Adds the given class name onto the component. Can add multiple class names at once by separating with spaces.
 *@param string $c class to be added on
 *@param boolean $reset Clear the existing classes then add the new.
 */
	function addClass($c, $reset=false)
	{
		$classes = explode(' ', $c);

		$count = count($classes);

		//If told to reset than the array of classes is remade from scratch.
		if($reset==true)
		{
			//clears the array
			$this->classes = array();

			//adds object type as initial value (this is always present)
			$this->classes[count($this->classes)] = $this->getType();
		}

		for($i=0;$i<$count;$i++)
		{
			if(!in_array($classes[$i], $this->classes))//checks for duplicates
			{
				$class = $this->parse($classes[$i]);
				if(ctype_alpha(substr($class, 0, 1))==true)
				{
					$this->classes[count($this->classes)] = $class;
					$this->debug('add class '.$this->classes[count($this->classes)-1], 'class');
				}
				else
				{
					$this->debug('addClass: '.$class.' is not a valid class name.', 'warning');
				}
			}
		}		
	}

/**
 *Removes the class from the list. Used in conjunction with findClass.
 *@param string $c Class name to be removed.
 */
	function removeCLass($c)
	{
		$remove = explode(' ', $c);

		$this->classes = array_diff($this->classes,$remove);		
	}

/**
 *   Sends the passed script value upstream until it gets to the page object instance. Adding the script to the page instance.
 *@param string $n Value name of the script to be added to the page.
 *@param string $p Script path.
 *@see Page::script()
 */
	function script($n, $p='')
	{
		if($this->parent != null)
		{
			$this->parent->script($n, $p);
		}
		else
		{
			throw new exception('Could not add script, parent Page object was missing.');
		}
	}

/**
 *   Sends the passed script value upstream until it gets to the page object instance. Which then removes the script if it's part of the page.
 *@param string $n Value name of the script to be removed from the page.
 *@see Page::scriptUnload()
 */
	function scriptUnload($n)
	{
		if($this->parent != null)
		{
			$this->parent->scriptUnload($n);
		}
		else
		{
			//$this->debug('script '.$n.'unloaded unsuccessfully');
			throw new exception('Could not unload script, parent Page object was missing.');
		}
	}

/**
 *   Returns the objects required access level.
 *   0 none
 *   1 user
 *   2 edit
 *   3 admin
 */
	function getLevel()
	{
		return $this->level;
	}

//Permission Methods
/**
 *   When a Level is set on a class inheriting from core, the object cannot be displayed unless the user meets the necessary access level.
 *   0 none
 *   1 user
 *   2 edit
 *   3 admin
 *
 *@param string $l String mode name for the level you want the object to be set at.
 *@see Core::getModes()
 */
	function setLevel($l)
	{
		$returner = true;
		$modes = $this->getModes();

		//double checking for a null or empty value because php throws a warning if I don't check for empty.
		if(isset($modes[$l]) && $modes[$l] !==null)
		{
			$this->level = $l;
		}
		else
		{
			//throw new Exception('setLevel Invalid Mode '.$l);
			$returner = false;
		}

		return $returner;
	}

/**
 *   Gets the is static flag.
 *@return boolean Static flag.
 */
	function isStatic()
	{
		return $this->static;
	}

/**
 *   Sets the static flag.
 *@param boolean $s Static flag.
 */
	function setStatic($s)
	{
		$this->static = $s;
	}

/**
 *   Gets the file name for where the database should poll from.
 *@todo This isn't a static method call. Should reassess how file is being used in general.
 *@return string Name of the source page for the database to poll from.
 */
	function getFile()
	{
		$this->debug('get file '.$this->file);
		return $this->file;
	}

/**
 *   Manual override for setting where the database for the object are polling from.
 *@param string $file Name of the source page for the database to poll from.
 */
 	function setFile($file='')
 	{
 		if(!empty($file))
		{
			$this->file = $file;
		}
		else
		{
			$page = explode('/', $_SERVER['PHP_SELF']);
			$this->file = $page[Count($page)-1];
		}

		$this->debug('set file '.$this->file);
 	}

/**
 *   Gets the hashmap of valid user modes used throughout the framework.
 *@return array Hashmap of user modes. 
 */
	static function getModes()
	{
		return self::$modes;
	}

/**
 *prints the privilege mode list
 *@param strign $level The current selected level.
 *@todo does this need to be on core?
 */
	function printModes($level)
	{
		$modes = $this->getModes();

		echo '<div>Access Level ';
		echo '<select name="prefLevel">';

		foreach($modes as $key=>$value)
		{
			$select = '';

			if(strcmp($key, $level)==0)
			{
				//print 'found match';
				$select = ' SELECTED';
			}
			echo '<option value="'.$key.'"'.$select.'>'.$key.'</option>';
		}

		echo '</select>';
		echo '</div>';
	}

/**
 *Prints a drop down list.
 *@param string $title
 *@param string $name
 *@param array $values
 *@param string $selected
 *@todo does this need to be on core?
 */
	function printDropDown($title, $name, $values, $selected)
	{
		echo '<div>'.$title.' ';
		echo '<select name="'.$name.'">';

		foreach($values as $v)
		{
			$select = '';

			if(strcmp($v, $selected)==0)
			{
				//print 'found match';
				$select = ' SELECTED';
			}
			echo '<option value="'.$v.'"'.$select.'>'.$v.'</option>';
		}

		echo '</select>';
		echo '</div>';
	}


//Helper
/**
 *   Central parser, for data input.
 *@param string $v String Value to be parsed / cleaned
 *@param string $directive Can be html (strip,spam), rss or xml
 *@todo make the directives to lowercase, trim, and a set list of values.   
 */
	function parse($v, $directive='')
	{		
		if(empty($v) or $v===null) {
			$v = '';
		}

		if(strcmp($directive, 'rss')==0) {
			//print 'parsing for rss';
			//$v = str_replace('<','&lt;')
			$v = str_replace('<', '&lt;', str_replace('>', '&gt;', str_replace('"', '&quot;', str_replace('&', '&amp;', $v))));
		}
		else if(strcmp($directive, 'xml')==0) {

		}
		else
		{
			$page = $this->getParent('page');
			$dir ='getHTMLParser';

			if(strcmp($directive, 'strip')==0) {
				$dir = 'getHTMLStrip';
			}
			else if(strcmp($directive, 'spam')==0 && strcmp($this->getType(),'comment')==0) {
				//print("parsing for spam ".$this->getType());
				$dir = 'getCommentSpamFilter';
			}

			//contextual awareness of sorts, I am calling parse from within a page instance.			
			if(!empty($page)) {
				$parser = $page->$dir();
			}
			else if(strcmp($this->getName(), 'page')==0) {
				$parser = $this->$dir();
			}


			if(!empty($v)) {
			$v = str_replace('<p></p>','<br />',$v);
			$v = str_replace('<br>','<br />',$v);
			$v = str_replace('<hr>','<hr />',$v);
			//regex for images: it's a mess
			$format1 = '/(<img[a-z\s="\/.]*)[\/]*>/i';
			$value = '$1/>';

			$v = preg_replace($format1, $value, $v);
			$v = str_replace('//>','/>',$v);


			}

			if(!empty($parser)) {
				$parser->setRoot(new Core('text'));

				$parser->parseXML($v);

				//print count($parser->getRoot());

				$v = $parser->result();
			}
		}

		$this->debug('parsing '.$v, 'parse');
		//parse the contents here.

		$v = trim($v);
		return $v;
	}

/**
 *   Boolean empty test and string comparison. This will probably get stripped out because php throws a warning message for unchecked empty $_REQUEST data.
 *Due to a warning thrown by previously not checking for empty before passing a $_REQUEST variable this method is hardly used and should be removed.
 *@todo remove it.
 *@param string $v
 *@param string $c 
 */
	function equal($v, $c)
	{
		if( !empty($v) && strcmp($v, $c)==0)
		{
			$returner = true;
		}
		else
		{
			$returner = false;
		}

		return $returner;
	}

/**
 *   Gets the current pages domain name.
 *@see http://www.webcheatsheet.com/PHP/get_current_page_url.php
 */
	function curPageURL() 
	{
		$pageURL = 'http';
	 	if(!empty($_SERVER["HTTPS"]) && $_SERVER["HTTPS"] == "on")
		{
			$pageURL .= "s";
		}

		$pageURL .= "://";
		if(!empty($_SERVER["SERVER_PORT"])&& $_SERVER["SERVER_PORT"] != "80")
		{
		$pageURL .= $_SERVER["SERVER_NAME"].":".$_SERVER["SERVER_PORT"];
		}
		else if(!empty($_SERVER["SERVER_NAME"]))
		{
			$pageURL .= $_SERVER["SERVER_NAME"];
		}
		else
		{
			$pageURL .= 'localhost/';
		}
		return $pageURL;
	}

/**
 *   Accessor for adding to the debug stream.
 *   Use debug to add checks for trouble spots in your code and also gain an understanding of page load order.
 *
 *   This Methods output became too large to be useful. Adding an optional key parameter allows you to breakup the debug stream into parts.
 *   As an example CORE::debug('','xml') returns only the parts of the debug marked 'xml'. Whereas CORE::debug() returns the entire debug stream including 'xml'
 *@param string $t Appended to the debug stream (optional).
 *@param string $k the associated key the debug string $t should save to
 *@return string The contents of the debug stream.
 * 
 */
	static function debug($t='', $k='')
	{
		//print '<br />'.$k.' '.get_called_class().' '.$t;
		if(empty($k))
		{
			//PHP 5.3 FUNCTION 		
			$k = get_called_class();
		}
					
		$k = explode(',', $k);

		$count = count($k);
		for($i=0;$i<$count;$i++)
		{
			//check added to avert a php warning for adding a non-initialized hash to itself.
			if(!empty(Core::$debugger[$k[$i]]))
			{
				Core::$debugger[$k[$i]] .= $t.'<br />';
			}
			else
			{
				Core::$debugger[$k[$i]] = $t.'<br />';
			}
		}

		return 'debug Stream: '.$k[0].'<br />'.Core::$debugger[$k[0]];
	}

/**
 *Sets the generic tag value
 *@param string $v Sets value.
 */
	function setValue($v)
	{
		//print '<br />setting value for '.$this->getName().' '.$v;
		$this->value = $this->parse($v);
	}
/**
 *gets the generic tag value.
 *@return string Gets value.
 */
	function getValue()
	{
		return $this->value;
	}

/**
 *This is for setting attribute value pairs.
 *@param array $a Attributes.
 *@param array $v Values.
 */
	function setAttributes($a, $v)
	{
		$tmpA = array();
		$count = count($a);


		for($i=0;$i<$count;$i++)
		{
			if(!empty($a[$i]))
			{
				$tmpA[count($tmpA)]= $a[$i];	
			}
		}
		$this->attributes = $tmpA;

		$this->values = $v;
	}
/**
 *Gets the attribute arraylist.
 *@return array
 */
	function getAttributes()
	{
		return $this->attributes;
	}

/**
 *Gets the values hashmap.
 *@return array
 */
	function getValues()
	{
		return $this->values;
	}

/**
 *Sets the owner flag
 *@param boolean $bool
 */
	function setOwner($bool)
	{
		$this->hasOwner = $bool;
	}

/**
 *gets the owner flag.
 *@return boolean
 */
	function isOwner()
	{
		return $this->hasOwner;
	}
/**
 *Gets canEdit flag.
 *@return boolean
 *@todo should probably be called isEdit instead or isCanEdit ?
 */
	function canEdit()
	{
		return $this->canEdit;
	}

/**
 *
 */
	function isEdit()
	{
		return $this->canEdit;
	}

/**
 *Sets the canEdit flag.
 *@param boolean $bool
 */
	function setEdit($bool)
	{
		$this->canEdit = $bool; 
	}
/**
 *Awesome Debug content
 *@todo need a means for turning off debug from being viewable. ie like a permission in account or settings. Even making it permission mode based would be cool.
 */
	function awesomeDebug()
	{
		echo '<div class="awesomeDebug">';
		echo '<span class="lightTitle">Name:</span> '.$this->getName();
		echo '<br /> '.'<span class="lightTitle">Type:</span> '.$this->getType();
		echo '<br /> '.'<span class="lightTitle">Unique:</span> '.$this->getUnique();
		echo '<br /> '.'<span class="lightTitle">Access Req LV:</span> '.$this->getLevel();
		echo '<br /> '.'<span class="lightTitle">File:</span> '.$this->getFile();
		echo '<br /> '.'<span class="lightTitle">Static:</span> ';
		if($this->isStatic())
		{echo 'true';}
		else
		{echo 'false';}
		echo '<br /> '.'<span class="lightTitle">Have owner:</span> ';
		if($this->isOwner())
		{echo 'true';}
		else
		{echo 'false';}
		echo '<br /> '.'<span class="lightTitle">Parent:</span> ';
		if(!empty($this->parent))
		{echo 'true';}
		else
		{echo 'false';}
		echo '<br /> '.'<span class="lightTitle">Children:</span> ';
		if(!empty($this->children))
		{echo count($this->children);}
		else
		{echo 'false';}

		echo '<br /> '.'<span class="lightTitle">xContent:</span> <code><pre>'.str_replace('<', '&lt;', str_replace('>', '&gt;', $this->xContent())).'</pre></code>';

		echo '<div class="debugStream">';
		echo $this->debug('', get_class($this));
		echo '</div>';

		$this->adContent();
		echo '</div>';

		$this->children('awesomeDebug');
	}
/**
 *placeholder for debug additional content
 */
	function adContent()
	{
	}

/**
 *
 */
	//function siteMap(){
	//}
}
?>
