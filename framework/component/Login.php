<?php

/**
 *   Medication For All Framework source file Login,
 *   Copyright (C) 2009-2011,2012  James M Adams
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
 *   Login box for users to log into the website. 
 *   Supports a max number of failed login attempts default is 4,
 *   if set to 0 than the limit is unlimited.
 *   ComponentLogin WILL NOT work through ajax, 
 *   because a lot of other components change their visual state based on being logged in 
 *   and at what permission level.
 *
 *   Sample Usage {@link http://www.medicationforall.com/framework/sample/SampleLogin.php SampleLogin}
 *
 *   {@example ../sample/SampleLogin.php SampleLogin}
 *
 *@wishlist Possible feature, send failed login attempts to a table in the database. Priority low.
 *
 *@author James M Adams <james@medicationforall.com>
 *@version 0.3
 *@package framework
 */
class Login extends Component
{
//data

	/**
	 *   Username.
	 *@access private
	 *@var string
	 */
	private $name;

	/**
	 *   Password.
	 *@access private
	 *@var string
	 */
	private $password;

	/**
	 *   Permission mode.
	 *@access private
	 *@var string
	 */
	//private $mode;

//constructor
/**
 *   Creates the ComponentLogin object.
 *@param string $h header text
 *@param string $u unique identifier for the login box  
 */
	function __construct($h="Login", $u="")
	{
		parent::__construct($h, 'login');

		$this->debug('construct component login '.$u);
		$this->setUnique($u);
	}

//methods

/**
 *   Process the users login attempts. Note for failed login attempts; 
 *   Only one type of error causes the counter to increment, and that's when a username and password 
 *   is provided and the MYSQL query failed to produce a match.
 *   Giving users leeway for just accidentally clicking the submit button without locking them out, for malicious login attempts.
 *@param boolean $processChildren Process children flag.
 */
	function process($processChildren=true)
	{
		$this->debug('process component Login '.$this->getUnique());

		$page = $this->getParent('page');
		$account = $page->getAccount();
		$loginKey = $this->getParent('page')->getSettings()->getLoginKey();
		//if parent LoginControl is set as static, the message was not resetting properly.
		$this->message = '';

		//attempting to log in check
		if(!empty($_POST[$loginKey.'Submit']) && strcmp($_POST[$loginKey.'Submit'], 'true')==0 && !empty($_POST['ltype']) && strcmp($_POST['ltype'], 'component'.ucFirst($this->getType()).ucFirst($this->getUnique()))==0 && strcmp($_POST['token'], $account->getToken())==0  )
		{
			$pass = true;
			//$name;
			$connect = $page->getConnect();
			$settings = $this->getParent('page')->getSettings();
			$site = $settings->getSite();
			$group = $this->getType().ucFirst($this->getunique());

			//max login attempts check
			if($account->getLoginFailMax()!=0 && $account->getLoginFail() >= $account->getLoginFailMax())
			{
				$this->message = '<div class="error">Too many Failed Attempts</div>';
				$pass=false;
			}

			//empty login name
			if($pass)
			{
				if(!empty($_POST['lname']))
				{
					$this->debug('setting name '.$_POST['lname']);
					$this->name = $this->parse(strtoLower($_POST['lname']));
				}
				else
				{
					$this->message = '<div class="error">Username cannot be empty</div>';
					$pass = false;
				}
			}

			//empty login password
			if($pass)
			{
				if(!empty($_POST['lpass']))
				{
					$this->debug('setting password '.$_POST['lpass']);
					$this->password =$_POST['lpass'];
				}
				else
				{
					$this->message = '<div class="error">Password cannot be empty</div>';
					$pass = false;
				}
			}

			if($pass)
			{
				$this->debug('Attempting to Login');

				//print 'checking password ';

				$mysqli = $connect->getMysqli();

				$query = 'SELECT type,password FROM tbluser WHERE status=\'active\' AND name=? AND `group`=? AND site=?';

				//print 'SELECT type,password FROM tbluser WHERE status=\'active\' AND name=\''.$this->name.'\' AND `group`=\''.$group.'\' AND site=\''.$siteK.'\'';

				if($stmnt = $mysqli->prepare($query))
				{
					$stmnt->bind_param('sss', $this->name, $group, $site);
					$stmnt->execute();
					$stmnt->bind_result($type, $dbpass);
					$pass = false;

						while($stmnt->fetch())
						{
							$check = new PasswordHash(8, false);
							if($check->CheckPassword($this->name.$this->password.$account->getSalt(), $dbpass))
							{
								$account->setLogin(true);
								$account->setMode($type);
								$account->setName($this->name);
								$pass=true;
							}
							else
							{
								//print 'check failed';
								//print ' password '.$this->name.$this->password.$account->getSalt().' stored '.$dbpass;
							}
						}

					//increments the failed login counter.
					if($pass==false)
					{
						$this->message = '<div class="error">Login Failed.</div>';
						$account->loginFailAdd();
						$this->debug('login attempt '.$account->getLoginFail());
						$this->debug('Max Login Attempts '.$account->getLoginFailMax());
					}
				}
			}
		}

		if($processChildren)
		{
			$this->children('process');
		}
	}

/**
 *   ComponentLogin content and form.
 */
	function cContent()
	{
		$page = $this->getParent('page');
		$account = $page->getAccount();
		$settings = $this->getParent('page')->getSettings();

		$this->debug('print component Login');

		echo('<div class="cContent">');

		if($account->isLogin())
		{
			//This logged in text has to stay this way. It's displayed when users are first logged in ,and after they've already been logged in check.
			echo 'Logged in as '.$account->getName();
		}
		else
		{
			echo $this->message;
			echo '<form method="POST" action="">';

			echo '<input type="hidden" name="'.$settings->getLoginKey().'Submit" value="true" />';
			echo '<input type="hidden" name="ltype" value="component'.ucFirst($this->getType()).ucFirst($this->getUnique()).'" />';
			echo '<input type="hidden" name="token" value="'.$account->getToken().'"/>';

			echo '<div>User Name<input type="text" name="lname" required autofocus value="'.$this->name.'"/></div>';

			echo '<div>Password &nbsp;&nbsp;<input type="password" name="lpass" required value=""/></div>';

			echo '<div><input type="submit" value="Login" /></div>';

			echo '</form>';
		}
		echo '</div>';
	}
}
?>
