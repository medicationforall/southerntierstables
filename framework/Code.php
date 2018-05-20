<?php

/**
 *   Medication For All Framework source file Code,
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
 *   Quick mock up tool for features that haven't been fully implemented.
 *   Allows for custom code to be directly injected into the framework; 
 *   Either by writing a code block directly into the constructor; Or by 
 *   setting an include statement for the object to run.
 *
 *@author James M Adams <james@medicationforall.com>
 *@version 0.1
 *@package framework
 */

class Code extends Core
{
	//data
	/**
	 *   Code to be shown.
	 *@access private
	 *@var string
	 */
	private $code;

	/**
	 *   Text reference to include path
	 *@access private
	 *@var string
	 */
	private $inc="";

	/**
	 *   Flag for whether the code should be evaluated or not.
	 *@access private
	 *@var boolean
	 */
	private $eval = false;

	//construct
	/**
 	*   Constructs the Code object. optional pass in string will 
	 *   be displayed when the object is shown.
	 *@param string $c Code to be displayed
	 *@param string $e Flag which sets whether the processed code should be evaluated as php code.
	 */
	function __construct($c = "", $e = false)
	{
		parent::__construct('code');

		$this->code = $c;

		$this->eval = $e;
	}


	/**
	 *   Shows the code block, and if set, runs the include statement.
	 */
	function show()
	{
		if($this->eval)
		{
			eval($this->code);
		}
		else
		{
			echo($this->code);
		}

		if($this->inc !="")
		{
			$this->debug('trying to run include');
			include $this->inc;
		}
	}

	/**
	 *   Sets the include statement for the object to show.
	 *@param string $i File resource name for the include
	 */
	function setInclude($i)
	{
		$this->debug("attempting to set include");
		$this->inc = $i;
	}

	/**
	 *   Not really pertinent since the eval flag can be set in the constructor.
	 *@param boolean $e
	 */
	function setEval($e)
	{
		$this->eval = $e;
	}
}
?>
