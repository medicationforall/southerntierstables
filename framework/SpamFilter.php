<?php
/**
 *   Medication For All Framework source file SpamFilter,
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
 *Models a Bayesian Filter 
 *
 *@author James M Adams <james@medicationforall.com>
 *@version 0.1
 *@see XML
 *@package framework
 */

class SpamFilter extends HTMLParser {
//data


private $checkBias = false;

private $mode = 'comment';

//constructor
/**
 *Construct the html parser object.
 *@param string $h Header text.
 *@param String $u Unique Identifier.
 *@param string $w whitelist File default whitelist.conf.
 */
	function __construct($h='', $u='', $w ='whitelist.conf',$n='SpamFilter')
	{
		parent::__construct($h, $u,$w,$n);

		$this->checkBias = true;
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

	function setWhiteList($w)
	{
		$this->checkBias = false;
			parent::setWhiteList($w);
		$this->checkBias = true;
	}

/**
 *
 */
	function setMode($m)
	{
		$this->mode = $m;
	}

/**
 *
 */
	function getMode()
	{
		return $this->mode;
	}



/**
 *Custom add element to modify for bias
 */
	function addElement($tag, $p=null, $v=null) {
		if($this->checkBias) {
			$bias=0;
			//print ("checking for bias ".$tag."<br />");

			if(strcmp($tag,'a')==0) {
				//print $bias."found anchor tag increasing bias"."<br />";

				$bias+=0.5;
			}

			$this->getParent('page')->getAccount()->increaseBias($bias);
			//print "bias is now ".$this->getParent('page')->getAccount()->getBias()."<br />";
		}

		return parent::addElement($tag, $p, $v);
	}
}


?>
