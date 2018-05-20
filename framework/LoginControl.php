<?php

/**
 *   Medication For All Framework source file Page,
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
 *   User login control bar. Originally implemented inside of page. I abstracted this class in order to simplify the code, and I also like the idea of not 
 *   forcing the developer to have a user login implementation if they don't need it.
 *
 *   Sample Usage {@link http://www.medicationforall.com/framework/sample/SampleLoginControl.php SampleLoginControl}
 *
 *   {@example ../sample/SampleLoginControl.php SampleLoginControl}
 *
 *@author James M Adams <james@medicationforall.com>
 *@version 0.1
 *@package framework
 */

class LoginControl extends Core
{
//data

/**
 *Loaded is not implemented yet.The idea would be to have login control act more as a web application.
 *@todo implement.
 *@access private
 *@var boolean
 */
private $loaded = false;

/**
 *Edit on GET value
 *@access private
 *@var string
 */
private $editOnLink = "?edittoggle=on";

/**
 *Edit off GET value
 *@access private
 *@var string
 */
private $editOffLink = "?edittoggle=off";

//constructor
/**
 *Create the Login Control object.
 */
	function __construct()
	{
		parent::__construct('LoginControl');

		$this->setLevel('user');
	}

//methods
/**
 *   Processes a LOT of requests relating to user interaction. 
 *   Responsible for loading components into the page based on the users actions.
 *@param boolean $processChildren Flag for processing children.
 *   @wishlist Trace how running sethead in LoginControl process is effecting the rest of the framework.
 *   @todo I need to rethink how I'm going to tackle template storage
 */
	function process($processChildren=true)
	{
		$this->debug('process LoginControl');
		$page = $this->getParent('page');
		$account = $page->getAccount();
		$settings = $page->getSettings();

		$loginKey = $settings->getLoginKey();
		$registerKey = $settings->getRegisterKey();
		$createPageKey = $settings->getCreatePageKey();
		$validateKey = $settings->getValidateKey();

		//empties children in case thia object is static .. this is a rough workaround
		$this->setChildren(null);

		if($account->isLogin())
		{	
			if(!empty($_REQUEST['logout']))
			{
				$account->logout();
			}

			if(!empty($_REQUEST['editMeta']))
			{
				if(strcmp($account->getMode(), 'admin')==0)
				{
					if(strcmp($_REQUEST['editMeta'], 'on')==0)
					{
						$account->setEditMeta(true);
					}
					else if(strcmp($_REQUEST['editMeta'], 'off')==0)
					{
						$account->setEditMeta(false);
					}
				}
			}

			//if(strcmp($account->getMode(),'admin')==0)
			if($account->access('admin'))
			{
				if($settings->isCreatePage())
				{
					if(!empty($_REQUEST[$createPageKey]) && strcmp($_REQUEST[$createPageKey], 'true')==0)
					{
						$create = new CreatePage('Create Page');
						$create->addClass('dialog');

						$this->add($create);
					}
				}

				if(!empty($_REQUEST['uploadFileD']) && strcmp($_REQUEST['uploadFileD'], 'true')==0)
				{
					$upload = new Upload('Upload File');
					$upload->addClass('dialog');

					$this->add($upload);				
				}

				if(!empty($_REQUEST['bulkUploadFileD']) && strcmp($_REQUEST['bulkUploadFileD'], 'true')==0)
				{
					$upload = new Upload2();
					$upload->addClass('dialog');

					$this->add($upload);				
				}

				if(!empty($_REQUEST['createDir']) && strcmp($_REQUEST['createDir'], 'true')==0)
				{
					$dir = new CreateDirectory();
					$dir->addClass('dialog');

					$this->add($dir);				
				}
			}

			if(!empty($_REQUEST['edittoggle']))
			{
				if(strcmp($_REQUEST['edittoggle'], 'on')==0)
				{
					$account->setEdit(true);
				}
				else if(strcmp($_REQUEST['edittoggle'], 'off')==0)
				{
					$account->setEdit(false);
				}
			}

			if(!empty($_REQUEST['changePass']) && strcmp($_REQUEST['changePass'], 'true')==0)
			{
				$change = new ChangePassword('Change Password');
				$change->addClass('dialog');
				$this->add($change);
			}

			if(!empty($_REQUEST['changeEmail']) && strcmp($_REQUEST['changeEmail'], 'true')==0)
			{
				$changeEmail = new ChangeEmail('Change Email');
				$changeEmail->addClass('dialog');
				$this->add($changeEmail);
			}
		}
		
		if($account->isLogin() == false) // not logged in
		{

			$loginMenu;

			if(strcmp($this->getLevel(), 'none')==0)
			{
				$loginMenu = new Menu('Login', '?'.$loginKey.'=true');
				$loginMenu->setUnique('loginMenu');
				$loginMenu->addLink('Login', '?'.$loginKey.'=true');
			}

			if(!empty($_REQUEST[$loginKey])&&strcmp($_REQUEST[$loginKey], 'true')==0)
			{
				$this->debug('Login Prompt');

				$login = new Login("Login");
				$login->addClass('dialog');

				$this->add($login);
			}

			if($settings->isRegister())//allowed to create new accounts
			{
				if(!empty($_REQUEST[$registerKey])&&strcmp($_REQUEST[$registerKey], 'true')==0)
				{
					$register = new CreateUser('Create Account');
					$register->addClass('dialog');

					$this->add($register);
				}

				if(strcmp($this->getLevel(), 'none')==0)
				{
					$loginMenu->addLink('Create Account', '?'.$registerKey.'=true');
				}
			}

			if(!empty($_REQUEST[$validateKey]) && strcmp($_REQUEST[$validateKey], 'true')==0)
			{
				$validate = new Validate('Verified Account');
				$validate->addCLass('dialog');
				$this->add($validate);
			}

			
			if(!empty($_REQUEST['forgotpassword']) && strcmp($_REQUEST['forgotpassword'], 'true')==0)
			{
				$forgotPassword = new ForgotPassword();
				$forgotPassword->addCLass('dialog');
				$this->add($forgotPassword);
			}

			if(!empty($_REQUEST['forgotusername']) && strcmp($_REQUEST['forgotusername'], 'true')==0)
			{
				$forgotUsername = new ForgotUsername();
				$forgotUsername->addCLass('dialog');
				$this->add($forgotUsername);
			}

			if(strcmp($this->getLevel(), 'none')==0)
			{
				$loginMenu->addLink('Forgot Password', '?forgotpassword=true');
				$loginMenu->addLink('Forgot Username', '?forgotusername=true');
				$this->add($loginMenu);
			}
		}

		//looks weird to call $account->getName().$_REQUEST['lname'],done to fix a bug where name does not show up when first logging in.
		$tmpName="";

		if(!empty($_REQUEST['lname']))
		{
			$tmpName = $_REQUEST['lname'];
		}

		$user = new Menu($account->getName().$tmpName, '', 'user');
		$user->addLink('Change Password', '?changePass=true');
		$user->addLink('Change Email', '?changeEmail=true');
		$user->addLink('Log Out', '?logout=true');
		$this->add($user);

		if($settings->isCreatePage())
		{
			$file = new Menu('File', '', 'admin');

			if($settings->isBasicFileMenu()==false){
				$file->addLink('Create Page', '?'.$settings->getCreatePageKey().'=true');
				$file->addLink('Create Directory', '?createDir=true');
				$file->addLink('Upload File', '?uploadFileD=true');
			}

			$file->addLink('Image Upload', '?bulkUploadFileD=true');
			$this->add($file);
		}


		//Could be problematic to have this initiated here in this control instead of in page itself.. The counter argument is if you can't have logged in users you'r not utilizing the meta table in the database anyways. Hopefully will be a non issue, *update* it is indeed an issue, @todo verify what this code is doing.
		$page->setHead();

		if($account->isEditMeta())
		{
			$meta = new Meta('Edit Meta', $page->getTitle(), $page->getMeta('description'), $page->getMeta('keywords'), $page->getStyle());
			$meta->addCLass('dialog');
			$this->add($meta);
		}

		if($processChildren)
		{
			$this->children('process');

			$lMenu = $this->findChild('loginMenu');
			if(!empty($lMenu) && $account->isLogin())
			{
				//print 'is logged in and found loginmenu';
				$this->deleteChild('loginMenu');
			}
		}


	}

/**
 *   Shows the Login Control Bar as well as potential dialog boxes the user may request.
 */
	function show()
	{
		$this->debug('print LoginControl');

		$page = $this->getParent('page');
		$account = $page->getAccount();
		//$settings = $page->getSettings();

		if($this->getParent('page')->getAccount()->access($this->getLevel()))
		{
			echo('<div class="loginControl">');
		}
		$this->children('show');

		if($this->getParent('page')->getAccount()->access($this->getLevel()))
		{
			if($account->access('edit'))
			{
				if($account->isEdit())
				{
					echo(' <a href="'.$this->editOffLink.'">Edit On</a>');
				}
				else
				{
					echo('<a href="'.$this->editOnLink.'">Edit Off</a>');
				}
			}

			if($account->access('admin'))
			{
				if($account->isEditMEta())
				{
					echo('<a href="?editMeta=off"> Edit Meta On</a>');
				}
				else
				{
					echo('<a href="?editMeta=on"> Edit Meta Off</a>');
				}
			}
			echo('</div>'."\n");
		}
	}

/**
 *Sets edit on GET value.
 *@param string $link 
 */
	function setEditOnLink($link)
	{
		$this->editOnLink = $link;
	}

/**
 *Sets edit off GET value.
 *@param string $link
 */
	function setEditOffLink($link)
	{
		$this->editOffLink = $link;
	}

/**
 *Custom Awesome Debug Content.
 */
	function adContent()
	{
		echo '<br /><span class="lightTitle">LoginControl content</span>:';
		echo '<br /><span class="lightTitle">Edit On Link</span> '.$this->editOnLink;
		echo '<br /><span class="lightTitle">Edit Off Link</span> '.$this->editOffLink;
		echo '<br /><span class="lightTitle">Loaded</span> ';
		if($this->loaded)
		{echo'true';}
		else
		{echo'false';}
		
	}
}
?>
