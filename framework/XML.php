<?php

/**
 *   Medication For All Framework source file Settings,
 *   Copyright (C) 2009 - 2011  James M Adams
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
 *   Provides the interface in which a user can edit a pages content.
 *   This class has it's own edit mode which has to be toggled on for the functionality to be enabled.
 *   If you wish to enable an end user to edit content structure, this is the component to use.
 *
 *   Currently supports.
 *   <ul>
 *   <li>componentText</li>
 *   <li>componentChangeEmail</li>
 *   <li>componentChangePassword</li>
 *   <li>componentComment</li>
 *   <li>componentContact</li>
 *   <li>componentCreateDirectory</li>
 *   <li>componentCreatePage</li>
 *   <li>componentCreateUser</li>
 *   <li>componentEmail</li>
 *   <li>componentForgotPassword</li>
 *   <li>componentGallery</li>
 *   <li>componentImage</li>
 *   <li>componentImageSlide</li>
 *   <li>componentLogin</li>
 *   <li>componentMap</li>
 *   <li>componentSlideShow</li>
 *   <li>componentUpload</li>
 *   <li>menu</li>
 *   <li>panel</li>
 *   </ul>
 *
 *   As long as the ID identifier is set, multiple xml components of the same type can co-exist on a page.
 *
 *   Sample Usage {@link http://www.medicationforall.com/framework/sample/SampleXML.php SampleXML}
 *
 *   Code for the sample.
 *
 *   {@example ../sample/SampleXML.php SampleXML}
 *
 *   Unit Test
 *
 *   {@example ../test/XMLTest.php XMLTest}
 *
 *@author James M Adams <james@medicationforall.com>
 *@version 0.2
 *@see Page
 *@package framework
 */

class XML extends Core
{
//data
	/**
	 *   Custom loads user xml data.
	 *@access private
	 *@var string
	 */
	private $xmlData;

	/**
	 *   Edit mode toggle boolean.
	 *@access private
	 *@var boolean
	 */
	private $edit = false;

	/**
	 *   Message informing user of save status.
	 *@access private
	 *@var string
	 */
	private $message2 = '';

	/**
	 *   Temporary xml children variable which overrides xml's own children if the user has a custom xml list to load.
	 *@access private
	 *@var Core
	 */
	protected $root;

	/**
	 *counter to keep track of the added id's
	 *@access private
	 *@var int
	 *@todo Does this really need public access or is protected good enough ?
	 */
	public static $counter = 0;

	/**
	 *Flag for if the xml content has been processed and loaded.
	 *@access private
	 *@var boolean
	 */
	private $loaded = false;

//constructor

/**
 *   Constructs the XML object.
 *@param string $u Unique identifier name to associate with this XML component.
 *@param string $file Apparently very important. This sets the file context from which xml is polling it's data.
 *@param string $type Component type. 
 */
	function __construct($u='', $file='', $type='XML')
	{
		parent::__construct($type);

		if(!empty($u))
		{
			$this->setUnique($u);
		}

		//very important reset which occurs with a problem from having static templates the file attribute isn't getting updated correctly. 
		$this->setFile($file);
	}

//methods

/**
 *   Responsible for queuing the Mysql database; Loading and adding XML Core components.
 *@param boolean $processChildren Process Children Flag.
 */
	function process($processChildren=true)
	{
		$this->debug('processing xml component '.$this->getUnique());

		$account = $this->getParent('page')->getAccount();

		if($this->isOwner() && $this->loaded == false && strpos($this->getUnique(), $account->getowner())== false)
		{
			//print 'setting unique owner for xml ';
			$this->setUnique($this->getUnique().$account->getowner());
			//print 'this xml component has an owner '.$this->getUnique();
		}
		else
		{
			//print 'not running is owner check';
		}


		$mysqli = $this->getParent('page')->getConnect()->getMysqli();
		$page = $this->getFile();
		$class = $this->getName() . ucfirst($this->getUnique());
		$site= $this->getParent('page')->getSettings()->getSite();


	//process xml requests
		if($account->isEdit() || $this->canEdit())//edit turned on
		{
			$this->getParent('page')->script('mfafSortable.js');

			//xml edit mode
			if(!empty($_REQUEST['xmlEdit']) && strcmp($_REQUEST['xmlEdit'], 'true')==0)
			{
					//print '<br />setting edit to true '.$class;
				if(!empty($_REQUEST['xmlclass']) && strcmp($_REQUEST['xmlclass'], $class)==0)
				{

					$this->edit=true;
				}

				if(!empty($_REQUEST['xmlEditSubmit']) && strcmp($_REQUEST['xmlEditSubmit'], 'true')==0)
				{
					if(!empty($_REQUEST['xmlclass']) && strcmp($_REQUEST['xmlclass'], $class)==0)//select which specific xml component is being edited.
					{
						if(!empty($_REQUEST['xmlData']))
						{
							$upxml = $this->parse($_REQUEST['xmlData'], 'xml');


							$query = 'UPDATE tblxml SET status=\'delete\' WHERE page=? AND status=\'active\' AND class =? AND site=?';
							if($stmnt = $mysqli->prepare($query))
							{
								$stmnt->bind_param('sss', $page, $class, $site);
								$stmnt->execute();
							}


							$query = 'INSERT INTO tblxml (page,class,xml,author,site) VALUES(?,?,?,?,?)';

							if($stmnt = $mysqli->prepare($query))
							{
								$name = $account->getName();

								$stmnt->bind_param('sssss', $page, $class, $upxml, $name, $site);
								$stmnt->execute();

								if($stmnt->affected_rows > 0)
								{
									$this->message2 ='<div class="confirm">Saved XML Data.</div>';
									$this->reload();
								}
								else
								{
									$this->message2 ='<div class="error">Failed to save XML data for some reason.</div>';
								}
							}
						}
					}
				}
			}
			else
			{
				$this->edit=false;
			}
		}

	//xml load
		if($this->loaded == false)
		{

		//print 'loading xml';
		$query = 'SELECT xml FROM tblxml WHERE page =? AND  status = \'active\' AND class =? AND site=?';

		if($stmnt = $mysqli->prepare($query))
		{
			$stmnt->bind_param('sss', $page, $class, $site);
			$stmnt->execute();
			$stmnt->bind_result($fetchxml);

			while($stmnt->fetch())
			{
				$this->xmlData =$fetchxml;

				$this->setRoot(clone $this);
				$this->root->setChildren(null);

				$this->parseXML($this->xmlData);

				if(strcmp(strtolower($this->root->getName()), 'xml')==0)
				{
					$this->setChildren($this->root->getChildren());
				}
			}
		}
		//print '<br />loading xml '.$this->getUnique();
		$this->loaded = true;
		}
		else
		{
			//print '<br />xml loaded';
		}

		//http://localhost/mfafdev/sample/SampleXML.php?xmlclass=XML&xmladd=componentText

	//xml add save
		if(($account->isEdit() || $this->canEdit()) && !empty($_REQUEST['xmlclass']) && strcmp($_REQUEST['xmlclass'], $class)==0 && !empty($_REQUEST['xmladd']))
		{
			$this->create($_REQUEST['xmladd']);

			$this->save();
		}


	//xml order change
		if(($account->isEdit() || $this->canEdit()) && !empty($_POST['xmlOrderChange']) && strcmp($_POST['xmlOrderChange'], 'true')==0 && strcmp($_POST['id'], $this->getUnique())==0)
		{
			//print "\n".'requesting order change';
			$pass =true;

			//find the element in question !

			if(!empty($_POST['changeID']))
			{
				//print 'change ID is not empty ';
				$id = $_POST['changeID'];

				
				$item = $this->findChild($id);

				//remove the item from current location
				if(!empty($item))
				{
					//print 'item is not empty '.$item->getName().' '.$item->getUnique();
					if($this->deleteChild($id))
					{
						//print 'child was deleted';

						if($this->findChild($id))
						{
							//print 'did not remove child element !';
							$pass = false;
						}
					}

					
					if($pass)
					{
						//find parent
						if(!empty($_POST['xmlParent']))
						{
							$parentID =$_POST['xmlParent'];
							$parent = null;
	
							if(strcmp($parentID, $this->getUnique())==0)
							{
								//print '\n parent ID is XML top level';
								$parent = $this;
								
							}
							else
							{
								//print 'parent has to be found';
								$parent = $this->findChild($parentID);
							}
						}
					}

					if($pass)
					{
						if($parent)
						{
							//print "\n".'parent has been found '.$parent->getName().' '.$parent->getUnique();
							//decide how to add child.
							$children = $parent->getChildren();
							$count = count($children);

							$prev = null;
							$next = null;

							$tmp = array();

							if(!empty($_POST['changePrev']))
							{
								$prev = $_POST['changePrev'];
							}
							else if(!empty($_POST['changeNext']))
							{
								$next = $_POST['changeNext'];
							}
							else
							{
								//print 'adding item';
								$tmp[count($tmp)] = $item;
							}

							for($i=0;$i<$count;$i++)
							{
								$index = $parent->getChild($i);

								if(!empty($prev) && strcmp($index->getUnique(), $prev)==0)
								{
									//print ' previous hit';
									$tmp[count($tmp)] = $index;
									$tmp[count($tmp)] = $item;
								}
								else if(!empty($next) && strcmp($index->getUnique(), $next)==0)
								{
									//print ' next hit ';
									$tmp[count($tmp)] = $item;
									$tmp[count($tmp)] = $index;
								}
								else
								{
									$tmp[count($tmp)] = $index;
								}
							}

							$parent->setChildren($tmp);
						}
					}

					if($pass)
					{
						$this->save();
					}
				}
			}
		}


		//take care of the missing ID issue.
		//this code should probably be moved into it's own sub method
		$counter = &self::$counter;
		//declaring unique here as a concession for how php 5.3 handles $this context.
		$unique = $this->getUnique();

		if($this->each(
		function($t) use (&$counter,$unique)
		{
			$returner = false;
			$unique = $t->getunique();
			if(!empty($unique))
			{
				//print '<br />unique '.$unique;
			}
			else
			{
				//doesn't account for objects that are added later
				do
				{
					$counter++;

					if($t->getParent('page')->findChild('auto'.$counter) === null)
					{
						//print '<br />unique is empty '.' '.$t->getName();

						if(strcmp($t->getName(), 'XML')!=0)
						{
							$t->setUnique('auto'.$counter.$t->getType().$unique);
						}

						//print ' '.$t->getUnique();
						$returner = true;
					}
				}
				while($returner==false);
			}
			//print '<br />unique '.$t->getUnique();

			return $returner;

			//print ' '.$counter;
		}))
		{
			$this->save();
		}

		//delete item
		if(!empty($_REQUEST['ditem']) && $this->deleteChild($_REQUEST['ditem']))
		{
				$this->save();
		}

		if($processChildren)
		{
			$this->children('process');
		}
	}


/**
 *Saves the xml output of the xml component and all of it's children to the database.
 *@param string $d Scary but it looks like you can save custom xml inserts. Otherwise if empty will use the current object xContent.
 */
	function save($d='')
	{
		$page = $this->getParent('page');
		$mysqli = $page->getConnect()->getMysqli();
		$account = $page->getAccount();
		$settings = $page->getSettings();

		$pageName = $this->getFile();
		$class = $this->getName() . ucfirst($this->getUnique());
		$site = $settings->getSite();
		$name = $account->getName();

		if(empty($d))
		{
			$d = $this->xContent();
		}

		$query = 'UPDATE tblxml SET status=\'delete\' WHERE page=? AND status=\'active\' AND class =? AND site=?';

		if($stmnt = $mysqli->prepare($query))
		{
			$stmnt->bind_param('sss', $pageName, $class, $site);
			$stmnt->execute();
		}


		$query = 'INSERT INTO tblxml (page,class,xml,author,site) VALUES(?,?,?,?,?)';

		if($stmnt = $mysqli->prepare($query))
		{
			$stmnt->bind_param('sssss', $pageName, $class, $d, $name, $site);
			$stmnt->execute();

			if($stmnt->affected_rows > 0)
			{
				$this->message2 ='<div class="confirm">Saved XML Data.</div>';
			}
			else
			{
				$this->message2 ='<div class="error">Failed to save XML data for some reason.</div>';
			}
		}
	}


/**
 *   Displays either the edit mode with an XML list of the objects children or runs the children's show() method
 */
	function show()
	{
		$account = $this->getParent('page')->getAccount();

		$unique = $this->getUnique();
		if(empty($unique))
		{
			echo '<div class="error">XML must have a unique id assigned</div>';
		}

		//print $unique;
		//print children content
		if($account->isEdit() || $this->canEdit())
		{
			//echo('<div class="xmlControl">XML '.$this->getUnique().' control Bar');
			$id = '';
			$unique = $this->getUnique();
			if(!empty($unique))
			{
				$id = 'id="'.$unique.'"';
			}

			echo '<div class="xml">';
			echo '<div '.$id.' class="xmlControl">';

			//echo($this->message2);

			if($this->edit)
			{
				echo ' <a href="?xmlEdit=false">View</a>';
			}
			else
			{
				echo ' <a href="?xmlEdit=true&amp;xmlclass='.$this->getName().ucfirst($this->getUnique()).'">XML</a>';

				echo '<div class="menu">Add';
				echo '<div class="subMenu">';
				
				echo '<a href="?xmlclass='.$this->getName().ucfirst($this->getUnique()).'&amp;xmladd=componentText">Text</a>';
				echo '<a href="?xmlclass='.$this->getName().ucfirst($this->getUnique()).'&amp;xmladd=componentComment">Comment</a>';
				echo '<a href="?xmlclass='.$this->getName().ucfirst($this->getUnique()).'&amp;xmladd=componentSlideShow">Slide Show</a>';
				echo '<a href="?xmlclass='.$this->getName().ucfirst($this->getUnique()).'&amp;xmladd=componentGallery">Gallery</a>';
				echo '<a href="?xmlclass='.$this->getName().ucfirst($this->getUnique()).'&amp;xmladd=componentEmail">Email</a>';
				echo '<a href="?xmlclass='.$this->getName().ucfirst($this->getUnique()).'&amp;xmladd=componentMap">Map</a>';
				echo '<a href="?xmlclass='.$this->getName().ucfirst($this->getUnique()).'&amp;xmladd=componentImageSlide">Image Slide</a>';
				echo '<a href="?xmlclass='.$this->getName().ucfirst($this->getUnique()).'&amp;xmladd=componentBlog">Blog</a>';
				echo '<a href="?xmlclass='.$this->getName().ucfirst($this->getUnique()).'&amp;xmladd=menu">Menu</a>';
				echo '</div>';

				echo '</div>';
			}
			echo '</div>';
		}

		if($this->edit)
		{
			echo($this->message2);

			echo '<form method="POST" class="xmlEditor">';
			echo '<input type="hidden" name="xmlEdit" value="true" />';
			echo '<input type="hidden" name="xmlEditSubmit" value="true" />';

			$this->eContent();

			echo '<input type="submit" value="save" />';
			echo '</form>';
		}
		else
		{
			$this->children('show');
		}


		if($account->isEdit() || $this->canEdit())
		{
			echo '</div>';
		}
	}

/**
 *   Edit mode content
 */
	function eContent()
	{
		echo('<input type="hidden" name="xmlclass" value ="'.$this->getName().ucfirst($this->getUnique()).'" />');
		echo('<div><textarea autofocus class="xmlData "name="xmlData">'.$this->xContent().'</textarea></div>');
	}


/**
 *   Custom XML parser. This recursive method parses XML content and passes it to addElement
 *@see addeElement
 *@param string $con The XML string data. 
 *@return string Parsed remaining contents of $con.
 */
	function parseXML($con)
	{
		//print '<br />'.$con.'<br />';

		$tag = '';
		$endTag = '';


		$param = array('');
		$value = array();

		$record ='';
		//$store = true;

		$quoteChar="'";

		$depth = 0;


		$mode;
		$host = $this;
		$setMode = function($m, $con='') use ($host, &$mode)
		{
			//$modes = array('discovery','tagRecord','paramRecord','valueRecord','openNextInstance','endTagDiscover','endTagRecord','end','comment','CDATA');
			$modes = array('search', 'doctype', 'comment', 'cdata', 'tagRecord', 'paramRecord', 'valueRecord', 'endTagRecord');
			$mode = $m;
			$host->debug('setMode to '.$modes[$m-1], 'xml');

			if(!empty($con))
			{
				$host->debug($con, 'xml');
			}
		};
		$setMode(1);//search


		$reset = function() use (&$tag, &$endTag, &$record, &$param, &$value, &$setMode)
		{
			$tag='';
			$endTag='';
			$record='';
			$param=array('');
			$value=array('');
			$setMode(1);//search
		};

		$count = strlen($con);
		for($i =0 ; $i<$count;$i++)
		{
			//$char = substr($con,$i,1);
			//print "\n<br />".$mode.' '.$char;

			if($mode == 1)//search
			{
				//print 'mode is search';
				if(substr($con, $i, 1) == '<')
				{
					$i++;
					if(substr($con, $i, 1) == '!')
					{
						$i++;
						//print 'exclamation hit';
						//print "\n".strtolower(substr($con,$i,7));

						if(strcmp(strtolower(substr($con, $i, 7)), 'doctype')==0)//doctype start
						{
							$i+=7;
							$setMode(2);//doctype
							$tag = 'DOCTYPE';
						}
						else if(strcmp(substr($con, $i, 2), '--')==0)//comment start
						{
							$i+=2;
							$setMode(3);;//comment
							$tag = 'codeComment';
						}
						else if(strcmp(strtolower(substr($con, $i, 7)), '[cdata[')==0)//cdata start
						{
							$i+=7;
							$setMode(4);//CDATA
							$tag = 'CDATA';
						}	
					}
					else if(substr($con, $i, 1) == '/')
					{
						$i++;
						$setMode(8);//endTagRecord
					}
					else
					{
						$setMode(5);//tagRecord
					}
				}
				else
				{
					$record.= substr($con, $i, 1);
				}

				if((substr($con, $i+1, 1) == '<' || $i+1 == $count))//save text block
				{	$record = trim($record);
					if(!empty($record))
					{
						//print 'record is not empty';
						$tmp = $this->addElement('text', $param, $value);

						if(!empty($tmp))
						{
							$tmp->setValue($record);
							$this->root->add($tmp);
						}
						$reset();
					}
				}
			}
			if($mode ==2)//doctype
			{
				if(substr($con, $i, 1) == '>')
				{
					$tmp = $this->addElement($tag, $param, $value);

					if(!empty($tmp))
					{
						$tmp->setValue($record);
						$this->root->add($tmp);
					}
					$reset();
				}
				else
				{
					$record .=substr($con, $i, 1);
				}
			}
			else if($mode == 3)//comment
			{
				if(strcmp(substr($con, $i, 3), '-->')==0)
				{
					$i+=2;
					$tmp = $this->addElement($tag, $param, $value);

					if(!empty($tmp))
					{
						$tmp->setValue($record);
						$this->root->add($tmp);
					}
					$reset();
				}
				else
				{
					$record.= substr($con, $i, 1);
				}
			}
			else if($mode == 4)//cdata
			{
				if(strcmp(substr($con, $i, 3), ']]>')==0)
				{
					$i+=2;
					$tmp = $this->addElement($tag, $param, $value);

					if(!empty($tmp))
					{
						$tmp->setValue($record);
						$this->root->add($tmp);
					}
					$reset();
				}
				else
				{
					$record.= substr($con, $i, 1);
				}
			}
			else if($mode == 5)//tag record
			{
				if(ctype_space(substr($con, $i, 1)) && !empty($tag))//param found
				{
					//print 'found space in tag record';
					$i++; //done for /> check
					$setMode(6);//paramrecord
				}

				if(strcmp(substr($con, $i, 2), '/>')==0)//empty tag instance
				{
					$i+=1;
					//print "\n".'attempting to add element parent is '.$this->root->getName();
					$tmp = $this->addElement($tag, $param, $value);

					if(!empty($tmp))
					{
						$tmp->setValue($record);
						$this->root->add($tmp);
					}
					$reset();
				}
				else if(substr($con, $i, 1)=='>')
				{
					//print 'end tag record '.$tag;

					$tmp = $this->addElement($tag, $param, $value);

					if(!empty($tmp))
					{
						$depth++;
						//print "\n".'increasing depth';
						$tmp->setValue($record);
						$this->root->add($tmp);

						$this->root = $tmp;
					}
					else
					{
						//print "\n".$tmp.' was null not increasing depth';
					}
					$reset();

				}
				else if($mode == 5)
				{
					if(ctype_space(substr($con, $i, 1)) == false)//clean up leading whitespace
					{
						$tag .= substr($con, $i, 1);
					}
				}
				else
				{
					$i--;//done because I skipped ahead to check for space/> but if I don't step back now src= param becomes rc=
				}

			}
			else if($mode == 6)//param record
			{
				if(substr($con, $i, 1)=='=')
				{
					if(substr($con, $i+1, 1)== '"')//value
					{
						$quoteChar = '"';
						$i++;
					}
					else if(substr($con, $i+1, 1)== "'")//value store single quotes
					{
						$quoteChar = "'";
						$i++;
					}
					else if(ctype_alnum(substr($con, $i+1, 1)))
					{
						$quoteChar = " ";
					}

					$setMode(7);//valueRecord
					$value[$param[count($param)-1]] = '';
					$host->debug('stored param '.$param[count($param)-1].' for tag '.$tag, 'xml');
				}
				else if(ctype_space(substr($con, $i, 1)) || strcmp(substr($con, $i, 2), '/>')==0 || substr($con, $i+1, 1)== '>')
				{
					//print 'param no value';
					$setMode(5);//tagRecord
					$value[$param[count($param)-1]] = $param[count($param)-1];
					$param[count($param)]='';

					if(ctype_space(substr($con, $i, 1)) || strcmp(substr($con, $i, 2), '/>')==0)
					{
						$i--;
					}
				}
				else
				{
					$param[count($param)-1] .=  substr($con, $i, 1);
				}

				//print $param[count($param)-1];
			}
			else if($mode == 7)//value record
			{
				$quoteHit=false;
				//needs to account for hitting whitespace if the quote char is a space and hitting >
				if(ctype_space($quoteChar))
				{
					if(ctype_space(substr($con, $i, 1)) || substr($con, $i, 1)=='>')
					{
						$quoteHit = true;		
					}
				}
				else
				{
					if(substr($con, $i, 1)==$quoteChar)
					{
						$quoteHit = true;
					}
				}

				if($quoteHit)
				{
					//print 'found value end';
					$setMode(5);//tagRecord
					$param[count($param)]='';

					if(ctype_space($quoteChar))//to make up for not using single or double quotes as delimiters 
					{
						$i--;
					}
				}
				else
				{
					$value[$param[count($param)-1]] .= substr($con, $i, 1);
					//print $value[$param[count($param)-1]];//placed in here so I'm not referencing null instances
				}

			}
			else if($mode == 8)//end Tag Record
			{
				if(substr($con, $i, 1)=='>')
				{

					if(strcmp($this->root->getName(), $endTag)==0)
					{
						//print 'tag '.$this->root->getName().' matches end tag '.$endTag;
						//print 'level '.$depth;

						if($depth>0)
						{
							$depth--;
							//print "\n".'decreasing depth to '.$this->root->getName();
							$tmp = $this->root->getParent();

							$this->root = $tmp;
						}
						$reset();
					}
					else
					{
						$values = $this->root->getValues();
						//print 'warning improper nesting! '.$this->root->getName()." ".$endTag.' '.$values['tag'];

						if(!empty($values['tag']) && strcmp($values['tag'], $endTag)==0)
						{
						$depth--;
						//print "\n".'decreasing depth to '.$this->root->getName();
						$tmp = $this->root->getParent();
						$this->root = $tmp;

						}

						$reset();
					}
				}
				else if(ctype_space(substr($con, $i, 1)))//skip whitespace
				{

				}
				else
				{
					$endTag .= substr($con, $i, 1);
					//print $endTag;
				}
			}
		}
		//print "\n".'depth is '.$depth;
	}



/**
 *   Returns an object which inherits from core. Note this class can return null if the XML tag is not recognized.
 *
 *@param string $tag Tag name.
 *@param array $p arraylist of Parameters.
 *@param array $v Hashmap of values.
 *
 *@return Core an object which inherits from Core.
 */
	function addElement($tag, $p=null, $v=null)
	{
		$returner = null;
		$unique ='';
		$href='';
		$src='';
		$head='';
		$class='';
		$display='';
		$mode='';
		$address='';
		$key='';
		$level='none';
		$text = '';
		$name = '';
		$phone = '';
		$hash = '';
		$freq=60;
		$target = false;

//print "\n".'  adding element '.$tag;//.$this->root->getName();

		$tag = ucFirst($tag);

		if(!empty($v['id']))
		{
			//print 'setting unique from hash '.$v['id'].'<br />';
			$unique = $v['id']; 
		}

		if(!empty($v['href']))
		{
			$href= $v['href'];
		}

		if(!empty($v['src']))
		{
			$src= $v['src'];
		}

		if(!empty($v['head']))
		{
			$head= $v['head'];
		}

		if(!empty($v['class']))
		{
			$class= $v['class'];
		}

		if(!empty($v['display']))
		{
			$display= $v['display'];
		}

		if(!empty($v['mode']))
		{
			$mode= $v['mode'];
		}

		if(!empty($v['address']))
		{
			$address= $v['address'];
		}

		if(!empty($v['key']))
		{
			$key= $v['key'];
		}

		if(!empty($v['lv']))
		{
			$level = $v['lv'];
		}

		if(!empty($v['text']))
		{
			$text = $v['text']; 
		}

		if(!empty($v['name']))
		{
			$name = $v['name']; 
		}

		if(!empty($v['phone']))
		{
			$phone = $v['phone']; 
		}

		if(!empty($v['hash']))
		{
			$hash = $v['hash']; 
		}

		if(!empty($v['freq']))
		{
			$freq = $v['freq']; 
		}

		if(!empty($v['target']))
		{
			$target = $v['target']; 
		}

		/*if(!empty($this->handler[$tag]))
		{
			$this->handler[$tag]($this);
		}
		else*/ if(strcmp($tag, 'Menu')==0)
		{
			$this->debug("attempting to add menu ".$unique." href ".$href." to ".$this->root->getName(), 'xml');
			$returner = new Menu($text, $href, $level);

			$returner->setUnique($unique);

			$returner->setTarget($target);
			if(!empty($class))
			{
				$returner->addClass($class);
			}
		}
		else if(strcmp($tag, 'MenuItem')==0)
		{
			//print '<br />adding menu item: '.$text.' root name: '.$this->root->getName();
			$this->debug("attempting to add menuItem ".$text.' '.$unique." href ".$href." to ".$this->root->getName(), 'xml');
			//$this->root->addLink($text,$href);
			$returner = new Menu($text, $href, $level);

			$tag = 'Menu';

				$returner->setUnique($unique);

				$returner->setTarget($target);
				if(!empty($class))
				{
					$returner->addClass($class);
				}
		}
		else if(strcmp($tag, 'ComponentGallery')==0 || strcmp($tag, 'Gallery')==0)
		{
			$returner = new Gallery($head, $src);

			$returner->setUnique($unique);

			if(!empty($class))
			{
				$returner->addClass($class);
			}

			if(!empty($level))
			{
				$returner->setLevel($level);
			}

			if(!empty($display))
			{
				$returner->setDisplay($display);
			}

			if(!empty($mode))
			{
				$returner->setMode($mode);
			}

		}
		else if(strcmp($tag, 'ComponentMap')==0 ||strcmp($tag, 'Map')==0)
		{

			$returner = new Map($head, $address);
			$returner->setUnique($unique);

			if(!empty($key))
			{
				$returner->setKey($key);
			}

			if(!empty($class))
			{
				$returner->addClass($class);
			}

			if(!empty($level))
			{
				$returner->setLevel($level);
			}
		}
		else if(strcmp($tag, 'Image')==0)
		{
			$this->root->addImage($src);
			$returner = null;
		}
		else if(strcmp($tag, 'Panel')==0)
		{
			$returner = new $tag();

			$returner->setUnique($unique);

			if(!empty($class))
			{
				$returner->addClass($class);
			}

			if(!empty($level))
			{
				$returner->setLevel($level);
			}
		}
		else if(strcmp($tag, 'ComponentCreateUser')==0 || strcmp($tag, 'CreateUser')==0)
		{
			$returner = new $tag($head, $hash, $unique);

			$returner->setUnique($unique);

			if(!empty($class))
			{
				$returner->addClass($class);
			}

			if(!empty($level))
			{
				$returner->setLevel($level);
			}
		}
		else if(strcmp($tag, 'ComponentLogin')==0 || strcmp($tag, 'Login')==0)
		{
			$returner = new $tag($head, $hash, $unique);

			$returner->setUnique($unique);

			if(!empty($class))
			{
				$returner->addClass($class);
			}

			if(!empty($level))
			{
				$returner->setLevel($level);
			}
		}
		else if(strcmp($tag, 'Slide')==0)
		{
			$this->debug("attempting to add slide ".$text." to ".$this->root->getName(), 'xml');
			$this->root->addStep($text);
			$returner = null;
		}
		else if(strcmp($tag, 'Title')==0)
		{
			//print '<br />xml setting title '.$unique.' '.$text;
			$this->debug("add title to tab ".$text." to ".$unique, 'xml');
			$this->root->addTitle($unique, $text);
			$returner = null;
		}
		else if(strcmp($tag, 'Feed')==0)
		{
			//print '<br />xml feed unique'.$unique;
			$this->root->addFeed($name, $unique, $src, $freq);
			$returner = null;
		}
		else if(strcmp($tag, 'XML')==0)
		{
			//print 'attempting to add xml '.$this->getName();

			//$this->root = $this;
			$returner = null;
		}
		else if(strcmp($tag, 'CodeComment')==0)
		{

		}
		/*else if(strcmp($tag,'Text')==0)
		{

		}*/
		else if(strcmp($tag, 'CDATA')==0)
		{

		}
		else if(strcmp($tag, 'ComponentComment')==0 || strcmp($tag, 'CommentBox')==0)
		{
				$returner = new CommentBox();

				$returner->setUnique($unique);

				if(!empty($head))
				{
					$returner->setHead($head);
				}

				if(!empty($class))
				{
					$returner->addClass($class);
				}

				if(!empty($level))
				{
					$returner->setLevel($level);
				}
		}
		else
		{
			$rTag = str_ireplace('Component', '', $tag);

			//this is taking advantage of php's short circuit logic http://php.net/manual/en/language.operators.logical.php
			if(class_exists($rTag) || class_exists($tag))
			{
				//print $rTag.'rtag';
				if(!empty($rTag))
				{
					$tag = $rTag;
				}

				$returner = new $tag();

				$returner->setUnique($unique);

				if(!empty($head))
				{
					$returner->setHead($head);
				}

				if(!empty($class))
				{
					$returner->addClass($class);
				}

				if(!empty($level))
				{
					$returner->setLevel($level);
				}
			}
		}

		//performing a class lookup - http://www.electrictoolbox.com/php-check-class-exists/ 
		if($returner != null && method_exists($tag, 'xHandler'))
		{
			$returner->xHandler($tag, $p, $v);
		}

		return $returner;
	}

/**
 *Dynamic XML adder.
 *@param string $t Tag name.
 *@todo make the autonaming conventions either the same in both locations here in XML or add a dynamic callback that can be overridden.
 */
	function create($t)
	{
		//print 'xml create';
		$obj = $this->addElement($t);

		//set unique
		$parent = $this->getParent('page');

		//gets the page count of that particular component type.
		$list = $parent->findChildByName($t);

		$name='';

		if(strpos(strtolower($t), 'component')!== false)
		{
			if(strpos(strtolower($t), 'componentchange'))
			{
				$name = 'c'.substr($t, 15, 3);
			}
			else
			{
				$name = 'c'.substr($t, 9, 3);
			}
		}
		else
		{
			$name = 'at';
		}

		$obj->setUnique(strtolower($name).count($list));


		if($obj->getShowPreference())
		{
			$obj->preference();
		}

		$this->add($obj);
	}

/**
 *Sets the root working tree.
 *@param Core $r Core based object tree.
 */
	function setRoot($r)
	{
		if(!empty($r))
		{
			$this->root = $r;
		}
	}

/**
 *Gets the root working tree.
 *@return Core Core based object tree.
 */
	function getRoot()
	{
		return $this->root;
	}

/**
 *Ajax request handler.
 */
	function short()
	{
		$page = 'index.php';
		if(!empty($_POST['page']))
		{
			$page = $_POST['page'];
		}

		$this->setFile($page);


		if(!empty($_REQUEST['id']))
		{
			$this->setUnique($_REQUEST['id']);
		}

		$this->process();
	}

/**
 *Probably hacking the tree load process for mid tree changes, ie voodoo stuff.
 *@todo not sure if this is actually being called
 */
	function reload()
	{
		print 'calling xml reload';
		$this->loaded = false;//this is a total hack
		$this->setChildren(null);
	}
/**
 *The xml content loaded flag.
 *@return boolean
 */
	function isLoaded()
	{
		return $this->loaded;
	}

/**
 *Custom awesome debug content.
 */
	function adContent()
	{
		echo '<br /><br /><span class="lightTitle">Additional Content:</span>';
		parent::adContent();
		//echo '<br />xml:<code><pre>'.str_replace('>','&gt;',str_replace('<','&lt;',$this->xmlData)).'</pre></code>';
		echo '<br /> '.'<span class="lightTitle">Edit:</span> ';
		if($this->edit)
		{echo 'true';}
		else
		{echo 'false';}
		echo '<br /> '.'<span class="lightTitle">Loaded:</span> ';
		if($this->loaded)
		{echo 'true';}
		else
		{echo 'false';}
		echo '<br /> '.'<span class="lightTitle">Counter:</span> '.self::$counter;
		echo '<br /> '.'<span class="lightTitle">Message 2:</span> '.$this->message2;
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
