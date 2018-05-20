<?php
/**
 *   Medication For All Framework source file HTMLParser,
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
 * Parses user input against a whitelist for allowed HTML tags, otherwise they are just parsed out.
 *
 *@author James M Adams <james@medicationforall.com>
 *@version 0.1
 *@see XML
 *@package framework
 */


class HTMLParser extends Reader
{

//HTMLParser
	/**
	 *File path and name of the whitelist xml config file.
	 *@access private
	 *@var string
	 */
	private $whiteList;

	/**
	 *System state flag for if the whitelist file has been read in.
	 *@access private
	 *@var boolean
	 */
	private $initialPass = true;
	 

//constructor
/**
 *Construct the html parser object.
 *@param string $h Header text.
 *@param String $u Unique Identifier.
 *@param string $w whitelist File default whitelist.conf.
 */
	function __construct($h='', $u='', $w ='whitelist.conf',$n='HTMLParser')
	{
		parent::__construct($h, $u, $n);

		$this->setWhiteList($w);
	}

//methods

/**
 *Placeholder process function.
 *@param boolean $processChildren Process children flag.
 */
	function process($processChildren=true)
	{
	}

/**
 *Placeholder show function.
 */
	function show()
	{

	}

/**
 *Sets The HTMLParser whitelistFile name / path.
 *@param string $w File path of the white list config file. 
 */
	function setWhiteList($w)
	{
		//print '<br />attempting to set whitelist '.$w;
		//print $this->whiteList;
		if(file_exists($w))
		{
			$this->whiteList = $w;

			$file = file_get_contents($this->whiteList);
				$this->initialPass = true;
				$this->setChildren(null);
				$this->root = $this;
				$this->parseXML($file);
				$this->initialPass = false;
				$this->root = null;
			//print $file;
			//print ' set whitelist '.$w;
			$this->debug('set whitelist '.$w);
		}
		else
		{
			//print ' whitelist does not exist '.$w;
				$this->initialPass = true;
				$this->setChildren(null);
				$this->root = $this;
				$this->parseXML('');
				$this->initialPass = false;
				$this->root = null;
		}
	}

/**
 *Gets The HTMLParser whitelistFile name / path.
 *@return string
 */
	function getWhiteList()
	{
		return $this->whiteList;
	}

/**
 *Parses in tags and if on the allowed list keeps them, If not applies the tag name remove; which is later deleted from the tree.
 *@param string $tag
 *@param array $p Parameters
 *@param array $v Values
 *@return Core TreeNode of the elements parsed in.
 */
	function addElement($tag, $p=null, $v=null)
	{
		$returner = null;
		if($this->initialPass)
		{
			//print ' attempting to add element '.$tag."\n";

			$returner = parent::addElement($tag, $p, $v);
		}
		else
		{
			if(strcmp($tag, 'text')==0)
			{
				$tmp = parent::addElement($tag, $p, $v);
				//print 'it\'s a text '."\n";
				$returner = $tmp;
			}
			else if($find = $this->findChildByName($tag))
			{
				//print 'it\'s not text but it still being added '.$find[0]->getName()."\n";
				$attr = $find[0]->getAttributes();
				$newP = array();

				//here I need to check parameters against the allowed subset and remove them if necessary.
				foreach( $p as $par)
				{
					if(in_array($par, $attr))
					{
						//print 'found attribute '.$par."\n";
						$newP[count($newP)]= $par;
					}	
				}

				$tmp = parent::addElement($tag, $newP, $v);
				$returner = $tmp;
			}
			else
			{
				//print $tag.' is not being added'."\n";
				$returner = parent::addElement('remove', array(0 =>'tag'), array('tag'=>$tag));
				//print $this->getChild(0)->getName();
			}
		}

		return $returner;
	}

/**
 *Dumps out the HTML parsed results.
 *@return String HTML parsed results
 */
	function result()
	{
		$returner = '';

		if(!empty($this->root))
		{
			//remove problem tags
			$this->root->deleteChildByName('remove');
			$returner = $this->root->xContent();
		}

		return $returner;
	}
}

?>
