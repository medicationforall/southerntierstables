<?php

/**
 *   Medication For All Framework source file Email,
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
 *   Email us component form.
 *
 *   Sample Usage {@link http://www.medicationforall.com/framework/sample/SampleEmail.php SampleEmail}
 *
 *   Code for the sample.
 *
 *   {@example ../sample/SampleEmail.php SampleEmail}
 *
 *@author James M Adams <james@medicationforall.com>
 *@version 0.2
 *@package framework
 */
class Email extends Component
{
//data
	/**
	 *   Email in which message is sent.
	 *@access private
	 *@var string
	 */
	private $email;

	/**
	 *   Users from email address.
	 *@access private
	 *@var string
	 */
	private $fromEmail;

	/**
	 *   Boolean display flag.
	 *@access private
	 *@var boolean
	 */
	private $display = true;
	
	/**
	 *   Email subject.
	 *@access private
	 *@var string
	 */
	private $subject;

	/**
	 *   Email body text.
	 *@access private
	 *@var string
	 */
	private $text;

	/**
	 *Submit button text.
	 *@access private
	 *@var string
	 */
	private $submitText = 'Send';


//constructor
/**
 *   Creates the e-mail component.
 *@param string $h header text
 *@param string $e internal site email ie the address where the user email is being sent to.
 */
	function __construct($h='Email Me', $e="")
	{
		parent::__construct($h, 'email');

		$this->email = $e;
	}


/**
 *   Adds script componentEmail.js on load. A lot of the burden for text processing falls to the email client.
 *@param boolean $processChildren Process children flag.
 */
	function process($processChildren=true)
	{
		$this->script('componentEmail.js');

		if(empty($this->email))
		{
			$this->email = $this->getParent('page')->getSettings()->getEmail();
		}
		//print($this->email);

		$pass = true;

		//$subject;

		//run component process , run core process
		parent::process();

		if((!empty($_REQUEST['email']))&&(strcmp($_REQUEST['email'], 'true')==0))
		{
			if(!empty($_REQUEST['cEmail']))
			{
				$this->fromEmail = $this->parse($_REQUEST['cEmail']);
			}
			else
			{
				$this->message = '<div class="error">Email Address can not be empty.</div>';
				$pass=false;
			}

			if($pass)
			{
				if(!empty($_REQUEST['cSubject']))
				{
					$this->subject = $this->parse($this->parse($_REQUEST['cSubject']));
				}
				else
				{
					$this->message = '<div class="error">Subject Can not be empty.</div>';
					$pass=false;
				}
			}

			if($pass)
			{
				if(!empty($_REQUEST['cMessage']))
				{
					$this->text = $this->parse($this->parse($_REQUEST['cMessage']));
				}
				else
				{
					$this->message = '<div class="error">Message Can not be empty.</div>';
					$pass=false;
				}
			}
		}
		else
		{
			$pass= false;
		}

		if($pass)
		{
			$this->debug('Sending email email address: '.$this->email.' subject: '.$this->subject.' message: '.$this->text.' from: '.$this->fromEmail);
			mail($this->email, $this->subject, $this->text, 'From: '.$this->fromEmail);
			$this->message = '<div class="confirm">Message sent</div>';
			$this->display=false;
		}

		if($processChildren)
		{
			$this->children('process');
		}
	}

/**
 *   Form shown for email.
 */
	function cContent()
	{
		$this->debug('overrode Component email cContent');
		echo('<div class="cContent">');

		echo('<div class="eForm">');
		echo $this->message;
		if($this->display)
		{

				echo('<form method="POST" action="">');
				echo '<input type="hidden" name="email" value="true">';
				echo('<div>Your Email: <input class="eName" type="email" name="cEmail" required autofocus value="'.$this->fromEmail.'" /></div>');
				echo('<div>Subject: <input class="eSubject" type="text" name="cSubject" required value="'.$this->subject.'" /></div>');
				echo('<div>Message: <textarea name="cMessage"rows="8" cols="23" required>'.$this->text.'</textarea></div>');
				echo('<input type="hidden" name="email"  value="true"/>');
				echo('<div><input class="eSub" type="submit" value="'.$this->submitText.'" /></div>');
				echo('</form>');

		}
		echo('</div>');

		$this->children('show');
		echo('</div>');
	}

/**
 *   Ajax request
 */
 	function short()
 	{
		$this->process();
		$this->cContent();
 	}
/**
 *Set the submit button text.
 *@param string $t Button text.
 */
	function setSubmitText($t)
	{
		$this->submitText = $t;
	}

}
?>
