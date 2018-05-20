<?php

/**
 *   Medication For All Framework source file CreateAccount,
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
 *   ComponentCreateUser form for account creation. 
 *
 *   Sample Usage {@link http://www.medicationforall.com/framework/sample/SampleCreateUser.php SampleCreateUser}
 *
 *   {@example ../sample/SampleComponentCreateUser.php SampleCreateUser}
 *
 *@author James M Adams <james@medicationforall.com>
 *@see ComponentLogin
 *@version 0.2
 *@package framework 
 */

class CreateUser extends Component
{
//data
	/**
	 *   Submitted email.
	 *@access private
	 *@var string
	 */
	private $email;

	/**
	 *   Submitted user name.
	 *@access private
	 *@var string
	 */
	private $name;

	/**
	 *   Submitted password.
	 *@access private
	 *@var string
	 */
	private $password;

	/**
	 *   Re-type submitted password.
	 *@access private
	 *@var string
	 */
	//private $repassword;

	/**
	 *   Display form flag.
	 *@access private
	 *@var boolean
	 */
	private $display = true;


//construct
/**
 *   Creates the ComponentCreateUser object.
 *@param string $h header text
 *@param string $u optional unique name
 */
	function __construct($h='Register', $u="")
	{
		parent::__construct($h, 'createUser');

		$this->debug('construct component CreateUser '.$u);
		$this->setUnique($u);
	}

//methods


/**
 *   Process the component http data. 
 *   Looks for create new user information, and checks to see if the information is valid.
 *@param boolean $processChildren Process children flag.
 */
	function process($processChildren=true)
	{
		$this->debug('process createUser '.$this->getUnique());

		$this->script('componentCreateUser.js');

		$page = $this->getParent('page');
		$settings = $page->getSettings();
		$account = $page->getAccount();
		$connect = $page->getConnect();
		$registerKey = $settings->getRegisterKey();
		$pass=true;
		$site = $settings->getSite();


		if($account->isLogin())
		{
			$pass=false;
			$this->display=false;

			$this->message = '<div class="error">You must log out to create a new account.</div>';
		}

		if($settings->isRegister() == false)
		{
			$pass=false;
			$this->display=false;

			$this->message = '<div class="error">New Accounts can not be created</div>';
			
			$this->debug('*Permission* Settings.php register is being set to false', 'error');
		}
		

		if(!empty($_POST[$registerKey.'Submit']) && $this->equal($_POST[$registerKey.'Submit'], 'true') && $pass=true && strcmp($_POST['token'], $account->getToken())==0)
		{
			if(!empty($_POST['ctype'])&&strcmp($_POST['ctype'], 'component'.ucFirst($this->getType()).ucFirst($this->getUnique()))==0)
			{
				$mysqli = $connect->getMysqli();

				if(!empty($_POST['cemail']))//email not null
				{
					$this->email = $this->parse(strToLower($_POST['cemail']));
				}
				else
				{
					$this->message = '<div class="error">Email can not be empty</div>';
					$pass=false;
				}

				if($pass)//check email unique
				{
					$query = 'SELECT email FROM tbluser WHERE email=? AND site=?';

					if($stmnt=$mysqli->prepare($query))
					{
						$stmnt->bind_param('ss', $this->email, $site);
						$stmnt->execute();
						//$stmnt->bind_result($email);

						while($stmnt->fetch())
						{
							$pass=false;
							$this->message='<div class="error">E-Mail Already Exists</div>';
						}
					}
				}

				if($pass)//name not null
				{
					if(!empty($_POST['cname']))
					{
						$this->name = $this->parse(strToLower($_POST['cname']));
					}
					else
					{
						$this->message = '<div class="error">Username can not be empty</div>';
						$pass=false;
					}
				}

				if($pass)//check name unique
				{
					$query = 'SELECT name FROM tbluser where name =? AND site=?';

					if($stmnt=$mysqli->prepare($query))
					{
						$stmnt->bind_param('ss', $this->name, $site);

						$stmnt->execute();
						//$stmnt->bind_result($name);

						while($stmnt->fetch())
						{
							$pass=false;
							$this->message = '<div class="error">Username Already Exists</div>';
						}
					}
				}

				if($pass)//password not null
				{
					if(!empty($_POST['cpass']))
					{
						$newPass = $_POST['cpass'];

						$subMessage = '';

						//checkPassword function does all of the password requirement checks
						if($account->checkPassword($newPass) == false)
						{
							$pass = false;
							$subMessage = $account->getMessage();
						}

						$this->message = $subMessage;

						$this->password = $account->makePassword($newPass, $this->name);
					}
					else
					{
						$this->message = '<div class="error">Password can not be empty</div>';
						$pass=false;
					}
				}

				if($pass)//check if username matches password
				{
					if(strcmp($_POST['cname'], $_POST['cpass'])==0)
					{
						$this->message = '<div class="error">Username and Password can not be the same</div>';
						$pass=false;
					}
				}

				if($pass)//repass not null
				{
					if(!empty($_POST['crepass']))
					{
						//$this->repassword = $account->makePassword($_POST['crepass'],$this->name);
					}
					else
					{
						$this->message = '<div class="error">Re-type can not be empty</div>';
						$pass=false;
					}
				}

				if($pass)//make sure password and repass match
				{

					if(strcmp($_POST['cpass'], $_POST['crepass'])==0)
					{
					}
					else
					{
						$this->message = '<div class="error">Password and Re-type did not match</div>';
						$pass=false;
					}
				}

				if($pass)//create user
				{
					if($settings->isValidate() || !$settings->isStartActive() )
					{
						$query = 'INSERT INTO tbluser (`email`,`name`,`password`,`group`,site) VALUES(?,?,?,?,?)';
					}
					else //start account active
					{
						$query = 'INSERT INTO tbluser (`email`,`name`,`password`,`group`,`status`,site) VALUES(?,?,?,?,\'active\',?)';
					}

					if($stmnt = $mysqli->prepare($query))
					{
						$group='login'.ucFirst($this->getUnique());
						$stmnt->bind_param('sssss', $this->email, $this->name, $this->password, $group, $site);
						$stmnt->execute();

						if($stmnt->affected_rows > 0)
						{
							$tmpPass = md5(uniqid(rand(), true));

							//account validation email
							if($this->getParent('page')->getSettings()->isValidate())
							{
								$query = 'INSERT INTO tblvalidate (`email`,`code`) VALUES(?,?)';
	
								if($stmnt=$mysqli->prepare($query))
								{
									$stmnt->bind_param('ss', $this->email, $tmpPass);

									$stmnt->execute();

									if($stmnt->affected_rows > 0)
									{
										$this->message = '<div class="confirm">Created user, check your email to verify</div>';
										mail($this->email, 'Validate Account', 'Go to this link to Validate your Account '.$this->curPageURL().$_SERVER["PHP_SELF"].'?'.$this->getParent('page')->getSettings()->getValidateKey().'=true&code='.$tmpPass, ' From: '.$this->getParent('page')->getSettings()->getEmail(), '-f '.$this->getParent('page')->getSettings()->getEmail());
									}
								}
							}
							else
							{
								$this->message = '<div class="confirm">Created user</div>';
							}
							$this->display= false;
						}
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
 *   Display the registration form, and account creation attempt message.
 */
	function cContent()
	{
		if($this->display)
		{
			$page = $this->getParent('page');
			$account = $page->getAccount();
			$settings = $page->getSettings();
			$eula = $account->getEULA();

			if(!empty($eula))
			{
				//print('eula not empty');
				echo '<div class="eula">';
				echo '<div>'.$eula.'</div>';

				echo '<a class="cancel" href="">Cancel</a><a class="agree" href="?agree=true">Agree</a>';

				echo '</div>';
			}
		echo '<div class="cContent">';
			echo '<form method="POST">';
				echo $this->message;

				if($this->display)
				{
				echo '<input type="hidden" name="'.$settings->getRegisterKey().'Submit" value="true" />';
				echo '<input type="hidden" name="token" value="'.$account->getToken().'"/>';
				echo '<input type="hidden" name="ctype" value="component'.ucFirst($this->getType()).ucFirst($this->getUnique()).'" />';
				echo '<div>Email <input type="email" name="cemail" required autofocus value="'.$this->email.'" /></div>';
				echo '<div>Username <input type="text" name="cname" required value="'.$this->name.'" /></div>';
				echo '<div>Password <input type="password" name="cpass" required value="" /></div>';
				echo '<div>Re-type <input type="password" name="crepass" required value="" /></div>';
				echo '<div><input type="submit" value="Create User" /></div>';
				}
			echo '</form>';
		echo '</div>';
		}
		else
		{
			echo $this->message;
		}
	}

/**
 *   Custom XML header.
 *@return string XML header.
 */
	function xHeader()
	{
		$returner ='';

		$returner .=parent::xHeader();

		return $returner;
	}
}
?>
