<?php

/**
 *   Medication For All Framework source file Meta,
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
 *   Allows a logged in user with edit privileges, to edit a pages meta header 
 *   information and embedded CSS. Meta now processes based off of unique identifiers, 
 *   so it's possible to have unique meta data per page of a an MVC page.
 *
 *   Sample Usage {@link http://www.medicationforall.com/framework/sample/SampleMeta.php SampleMeta}
 *
 *   {@example ../sample/SampleMeta.php SampleMeta}
 *
 *@author James M Adams <james@medicationforall.com>
 *@version 0.2
 *@package framework
 */
class Meta extends Component
{

//data
	/**
	 *   Page title.
	 *@access private
	 *@var string
	 */
	private $title = '';

	/**
	 *   Meta description.
	 *@access private
	 *@var string
	 */
	private $description = '';

	/**
	 *   Meta keywords.
	 *@access private
	 *@var string
	 */
	private $keywords = '';

	/**
	 *   Embedded stylesheet text.
	 *@access private
	 *@var string
	 */
	private $style = '';

//constructor
/**
 *   Directly called by Page to edit meta content on a page by page basis
 *   and otherwise should not have to be implemented as a standalone component.
 *
 *
 *@param string $h header text value of the component
 *@param string $t page title text value
 *@param string $d page description text value
 *@param string $k page keywords text value
 *@param string $s page embedded stlyesheet css declarations text value
 *@param string $file File Context.
 */
	function __construct($h='', $t='', $d='', $k='', $s='', $file='')
	{
		parent::__construct($h, "meta");
		$this->title = $t;
		$this->description = $d;
		$this->keywords = $k;
		$this->style = $s;
		$this->setLevel('admin');
		$this->setFile($file);
	}

//methods
/**
 *   Handles updating the database with user edit changes.
 *   The Page object handles loading the data from the database.
 *@param boolean $processChildren Process Children flag. 
 */
	function process($processChildren=true)
	{
		$page = $this->getParent('page');
		$account = $page->getAccount();
		$site = $this->getParent('page')->getSETTINGS()->getSite();
		$unique = $this->getParent('page')->getUnique();
		//$nameUnder = strtolower($account->getName());
		$name = $account->getName();

		if(empty($this->title))
		{
			$this->title = $page->getTitle();
		}

		if(empty($this->description))
		{
			$this->description = $page->getMeta('description');
		}

		if(empty($this->keywords))
		{
			$this->keywords = $page->getMeta('keywords');
		}

		if(empty($this->style))
		{
			$this->style = $page->getStyle();
		}


		if($account->access($this->getLevel()))
		{
			$this->debug('process meta '.$this->getUnique());

			if(!empty($_REQUEST['mEdit']) && $this->equal($_REQUEST['mEdit'], 'true'))
			{
				$pass = true;

				//if(!empty($_REQUEST['mtitle']))
				//{
					$this->title = $this->parse($_REQUEST['mtitle']);
				//}

				//if(!empty($_REQUEST['mdescription']))
				//{
					$this->description = $this->parse($_REQUEST['mdescription']);
				//}

				//if(!empty($_REQUEST['mkeywords']))
				//{
					$this->keywords = $this->parse($_REQUEST['mkeywords']);
				//}

				//if(!empty($_REQUEST['mstyle']))
				//{
					$this->style = $this->parse($_REQUEST['mstyle']);
				//}

				if(!empty($_REQUEST['mEditUnique']) && strcmp($_REQUEST['mEditUnique'], $this->getUnique())!=0 )
				{
					$pass= false;
				}

				if($pass)
				{
					$mysqli = $this->getParent('page')->getConnect()->getMysqli();

					$page = $this->getFile();

					$query = 'UPDATE tblmeta SET status=\'delete\' WHERE page=? AND status=\'active\' AND site=? AND `class`=?';

					if($stmnt = $mysqli->prepare($query))
					{
						$stmnt->bind_param('sss', $page, $site, $unique);
						$stmnt->execute();
					}


					$query = 'INSERT INTO tblmeta (page,title,description,keywords,style,author,site,class) VALUES(?,?,?,?,?,?,?,?)';

					if($stmnt = $mysqli->prepare($query))
					{
						$stmnt->bind_param('ssssssss', $page, $this->title, $this->description, $this->keywords, $this->style, $name, $site, $unique);

						$stmnt->execute();

        					if($stmnt->affected_rows > 0)
						{
							$this->message='<div class="confirm">Updated Meta Information</div>';
						}
						else
						{
							echo '<div class="error">something didn\'t work '.$stmnt->error.'</div>';
        					}
					}
				}

				if($pass)
				{
					$this->getParent('page')->setHead();
				}
			}
		}

		if($processChildren)
		{
			$this->children('process');
		}
	}

/**
 *   Form edited by the user to change page header information.
 */
	function cContent()
	{
		echo('<div class="cContent">');

		//print $this->getFile();

		echo($this->message);

		echo('<form method="post">');
		echo('<input type="hidden" name="mEdit" value="true" />');
		if($this->getUnique() != null)
		{
			echo('<input type="hidden" name="mEditUnique" value="'.$this->getUnique().'" />');
		}
		echo('<div>Title<br /><input type="text" name="mtitle" value="'.$this->title.'" /></div>');

		echo('<div>Description<br /><textarea name="mdescription">'.$this->description.'</textarea></div>');
		echo('<div>Keywords<br /><textarea name="mkeywords">'.$this->keywords.'</textarea></div>');
		echo('<div>Style<br /><textarea autofocus name="mstyle">'.$this->style.'</textarea></div>');
		echo('<div><input type="submit" value="Save" /></div>');

		echo('</form>');

		echo('</div>');
	}
}
?>
