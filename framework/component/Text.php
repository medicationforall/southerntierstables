<?php

/**
 *   Medication For All Framework source file Text,
 *   Copyright (C) 2009,2012  James M Adams
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
 *   ComponentText is the most commonly used Component in the framework, it allows 
 *   for user management of its content, and stores the Data in a mysql database.
 *   This class is a good example of a component with an edit mode.
 *
 *   Sample Usage {@link http://www.medicationforall.com/framework/sample/SampleText.php SampleText}
 *
 *   {@example ../sample/SampleText.php SampleComponentText}
 *
 *@wishlist Implement version control front end.
 *
 *@author James M Adams <james@medicationforall.com>
 *@version 0.3
 *@package framework
 */
class Text extends Component
{
//data
	/**
	 *   Body text.
	 *@access private
	 *@var string
	 */
	private $text;

	/**
	 *   If tinymce is enabled flag.
	 *@access private
	 *@var boolean
	 */
	//private $tinymce = false;

	/**
	 *   Version.
	 *@access private
	 *@var boolean
	 */
	private $version = false;

//constructor

/**
 *   Creates a ComponentText object $t and $u are optional variables
 *@param string $h Header text.
 *@param string $t Text
 *@param string $u Unique identifier name.
 *@param string $file File context.
 *@todo multiple text boxes really have to have unique id's see if there is a better way for mitigating elements without it's in a consistent way. for general use and for the xml component.
 */
	function __construct($h='', $t ="", $u ="", $file="")
	{
		parent::__construct($h, "text");

		$this->setText($t);

		$this->setFile($file);

		if(!empty($u))
		{
			$this->setUnique($u);
		}
		$this->setShowPreference(true);
	}

//methods
/**
 *   processes all request data responsible for updating user generated content and in the same turn loading the content off of the database.
 *@todo problem description If you name a text object with mixed case the first time you save the text object in edit mode the body text will be empty. The most likely culprit has to do with setunique being lowercased in the code.
 *@param boolean $processChildren Process children flag.
 */
	function process($processChildren=true)
	{

		//print 'text process';
		$class = 'Component'.ucFirst($this->getType()).ucFirst($this->getUnique());

		parent::process();

		/*if($this->tinymce)
		{
			$this->script('tiny_mce/jquery.tinymce.js');
			$this->script('tinymceInit.js');

		}
		else
		{*/
			$this->script('componentText.js');
			//$this->script('jquery-te/jquery-te-1.4.0.min.js');
			//$this->script('xregexp/xregexp-all-min.js');
			//$this->script('componentText2.js');
		//}
		

		if($this->getParent('page')->getConnect()->isdbConnect())//database installed ?
		{
			//print 'db connect is true';
			$page = $this->getParent('page');
			$account = $page->getAccount();
			//$token = $account->getToken();
			$settings = $page->getSettings();
			$mysqli = $page->getConnect()->getMysqli();
			$site = $settings->getSite();
			$file =$this->getFile();
			$name = $account->getName();

			if($account->isEdit() &&(!empty($_POST['edit'])&&strcmp($_POST['edit'], 'true')==0))
			{
				$this->debug('edit process component '.$class);

				if(!empty($_POST['etype']) && strcmp($_POST['etype'], $class)==0)
				{
					$sHead ='';
					$sContent = '';

					//print 'process text submission';

					if(!empty($_POST['eHead']))
					{
						$sHead = $this->parse($_POST['eHead']);
					}

					if(!empty($_POST['eText']))
					{
						$sContent = $this->parse($_POST['eText']);
					}
			
					$query = 'UPDATE tbltext SET status=\'delete\' WHERE page=? AND status=\'active\' AND class=? AND site=?';

					if($stmnt=$mysqli->prepare($query))
					{
						$stmnt->bind_param('sss', $file, $class, $site);
						$stmnt->execute();
					}

					$query = 'INSERT INTO tbltext (page,class,header,content,author,site) VALUES(?,?,?,?,?,?)';

					if($stmnt=$mysqli->prepare($query))
					{
						$stmnt->bind_param('ssssss', $file, $class, $sHead, $sContent, $name, $site);
						$stmnt->execute();

						if($stmnt->affected_rows > 0)
						{
							$this->message = '<div class="confirm">Content Updated</div>';
						}
					}			
				}
			}

			$query = 'SELECT header,content FROM tbltext WHERE page=? AND status=\'active\' AND class=? AND site=?';

			if($stmnt=$mysqli->prepare($query))
			{
				$stmnt->bind_param('sss', $file, $class, $site);
				$stmnt->execute();
				$stmnt->bind_result($header, $content);

				while($stmnt->fetch())
				{
					$this->setHead($header);
					$this->setText($content);
				}
			}
		}

		if(!empty($_POST['textVersion']) && strcmp($_POST['textVersion'], $class)==0)
		{
			$this->version = true;
		}

		if($processChildren)
		{
			$this->children('process');
		}	
	}

/**
 *   Generic cContent this function is routinely overwritten by all components.
 */
	function cContent()
	{
		echo('<div class="cContent">');
			echo($this->text);
			$this->children('show');
		echo('</div>');
	}

/**
 *    Edit text form content.
 */
	function edit()
	{
		echo('<form class="edit" method="POST">'."\n");
			$this->eHeader();

			$this->eContent();

			$this->eFooter();
		//echo('</form>'."\n");
	}

/**
 *   edit mode Content area
 */
	function eContent()
	{
		echo($this->message);
		echo('<div class="cContent">');

		echo '<div class="control">';
			/*echo '<a href="?textVersion=Component'.ucFirst($this->getType()).ucFirst($this->getUnique()).'">version</a>';*/
		echo '</div>';

		/*if($this->tinymce)
		{
			echo('<textarea name="eText" class="tinymce">'.$this->getText().'</textarea>');
		}
		else
		{*/
			echo('<textarea autofocus name="eText">'.$this->getText().'</textarea>');
		//}

		if($this->version)
		{
			$this->vContent();
		}
		
			echo('<div>'."\n");
			echo('<input class="'.$this->getType().'EditSubmit" type="submit" value="save" />');
			echo('</div>'."\n");

		echo('</form>'."\n");

		$this->children('show');

		echo('</div>');
	}

/**
 *Overwrites eFooter on component
 */
	function eFooter()
	{

	}

/**
 *   Version control content.
 *@wishlist Finish.
 */
	function vContent()
	{
		$mysqli = $this->getParent('page')->getCOnnect()->getMysqli();

		$query = 'SELECT id,status,author,header,content,time FROM tbltext WHERE site=? AND page=? AND class=? ORDER BY time desc';

		if($stmnt = $mysqli->prepare($query))
		{
			//$file = $page[Count($page)-1];
			$class = 'Component'.ucFirst($this->getType()).ucFirst($this->getUnique());
			$settings = $this->getParent('page')->getSettings();
			$site = $settings->getSite();

			$stmnt->bind_param('sss', $site, $this->getFile(), $class);
			$stmnt->execute();
			$stmnt->bind_result($id, $status, $author, $header, $content, $time);

			echo '<div class="version dialog">';
			echo '<div class="cHeader">Version control <em class="code">'.$class.'</em></div>';
			while($stmnt->fetch())
			{
				$active='';
				if(strcmp($status, 'active')==0)
				{
					$active = 'checked="true"';
				}
				echo '<br /><INPUT TYPE=radio NAME="selectVersion" VALUE="'.$id.'" '.$active.'>'.$time.' '.$author.' '.$status.'</input>';
			}
			echo '</div>';
			
		}
		else
		{
			echo ' query failed';
		}
	}

/**
 *   Gets the text.
 *@return string The components Text;
 */
	function getText()
	{
		return $this->text;
	}

/**
 *   Sets component text.
 *@param string $t component text
 */
	function setText($t)
	{
		$this->text = $this->parse($t);
	}

/**
 *   Boolean, enables or disables tinymce.
 */
	/*function setTinyMCE($t)
	{
		$this->tinymce = $t;
	}*/

/**
 *   Ajax short method.
 *@wishlist this can be simplified etype can just be the componenttexts id it doesn't need to be a weird concatenation
 */
	function short()
	{
		//print 'running test submission short ';

		//check to see if page is passed via post, and if not default index.php for page.
		$page = 'index.php';
		if(!empty($_POST['page']))
		{
			$page = $_POST['page'];
		}

		$this->setFile($page);

		//this is a problem area because it treats unique different by trying to concatenate component.
		$unique = lcfirst(str_ireplace('componenttext', '', $_REQUEST['etype']));

		if(!empty($unique))
		{
			//print 'unique is assume to be '.$unique;
			$this->setUnique($unique);
		}

		$this->process();

		$this->show();
	}
}
?>
