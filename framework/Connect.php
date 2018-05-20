<?php

/**
 *   Medication For All Framework source file Connect,
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
 *   Singleton object, stored in class page.
 *   Responsible for the Mysql Database connection parameters.
 *   The framework has been updated to utilize the php MYSQLi database connector. 
 *
 *   Note: This class is not static so you should be able to set a unique connector 
 *   for separate pages. Example If you wish to have multiple levels of MYSQL user access, 
 *   based on the pages need. The trick is to clone the site template and apply a new 
 *   Connect object to the cloned instance. This is untested but should work in theory.
 *
 *@author James M Adams <james@medicationforall.com>
 *@version 0.2
 *@see Page
 *@package framework
 */
Class Connect
{
//data
	/**
	 *   Connect to database flag.
	 *@access private
	 *@var string
	 */
	private $connect = true;

	/**
	 *   Hostname ie localhost.
	 *@access private
	 *@var string
	 */
	private $host;

	/**
	 *   Login username.
	 *@access private
	 *@var string
	 */
	private $username;

	/**
	 *   Login Password.
	 *@access private
	 *@var string
	 */
	private $password;

	/**
	 *   Database to connect to.
	 *@access private
	 *@var string
	 */
	private $database;

	/**
	 *   Database type, this variable isn't used. As it stands there are no plans to implement PDO,
	 *   and changing the server variable (in the constructor) has no effect.
	 *@access private
	 *@var string
	 */
	private $server;

//constructor
/**
 *Creates the Connect object.
 *
 *@param string $u Mysql username, Default 'root'
 *@param string $p Mysql password, Default 'root'
 *@param String $h Mysql host address, Default 'localhost'
 *@param string $d Mysql database, Default 'framework'
 *@param string $s Server Type, for now this isn't used.
 */
	function __construct($u = 'root',$p = 'root',$h = 'localhost',$d = 'framework',$s="mysql")
	{
		$this->host = $h;
		$this->username = $u;
		$this->password = $p;
		$this->database = $d;
		$this->server = $s;
	}

//methods

/**
 *   Creates a new database connection when called.
 *@return mysqli MYQLI connection object.
 */
	function getMysqli()
	{
		$mysqli = new mysqli($this->host, $this->username, $this->password, $this->database);
		//http://devzone.zend.com/article/686
		if(mysqli_connect_errno())
		{
			printf("Connect failed: %s\n", mysqli_connect_error());
			exit();
		}

		//http://php.net/manual/en/mysqli.set-charset.php
		/* change character set to utf8 */
		if(!$mysqli->set_charset("utf8"))
		{
	    		printf("Error loading character set utf8: %s\n", $mysqli->error);
		}
		/*else
		{
			printf("Current character set: %s\n", $mysqli->character_set_name());
		}*/

		//end
		return $mysqli;
	}

/**
 *   Gets connect to database flag
 *@return boolean
 */
	function isdbConnect()
	{
		return $this->connect;
	}

/**
 *   Sets database connect flag
 *@param boolean $c
 */
	function dbConnect($c)
	{
		$this->connect = $c;
	}
/**
 *Awesome Debug content.
 */
	function adContent()
	{
		echo '<br /><br /><span class="lightTitle">DB Connect Content:</span>';
		echo '<br /> '.'<span class="lightTitle">DB Connect Flag:</span> ';
		if($this->connect)
		{echo 'true';}
		else
		{echo 'false';}
		echo '<br /> '.'<span class="lightTitle">Host:</span> '.$this->host;
		echo '<br /> '.'<span class="lightTitle">User Name:</span> '.$this->username;
		echo '<br /> '.'<span class="lightTitle">Password:</span> '.$this->password;
		echo '<br /> '.'<span class="lightTitle">Database:</span> '.$this->database;
		echo '<br /> '.'<span class="lightTitle">DB Type:</span> '.$this->server;
	}
}
?>
