<?php

/**
 *   Medication For All Framework source file CommentBox,
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
 *   Anonymous and logged in comments form. 
 *   By assigning unique (setUnique) to ComponentComment you can have multiple 
 *   ComponentComment's associated with a page. 
 *   For example ComponentBlog has a unique ComponentComment per entry but all entries 
 *   are displayed within the same ComponentBlog on the same page.
 *
 *   There's a weird use case in between cookie states steps to reproduce:
 *   <ol>
 *   <li>Go to SampleComponentComment.php you will be logged in as sample_user.</li>
 *   <li>In the browser controls clear the page cookies, but don't refresh the page.</li>
 *   <li>without refreshing the page add a comment. the username will come up as undefined.</li>
 *   </ol>
 *
 *Sample Usage {@link http://www.medicationforall.com/framework/sample/SampleCommentBox.php SampleCommentBox}
 *
 *{@example ../sample/SampleCommentBox.php SampleCommentBox}
 *
 *@wishlist Logged in users avatars. Priority Low.
 *
 *@wishlist Elevated polling system for comment refresh. first 10 seconds for 10 
 *      iterations than 20 for 20 than 30 for 30 than 40 for 40. Till a refresh 
 *      actually occurs than start all over. Priority Low.
 *
 *@wishlist Implement a means of closing adding new comments after a certain period after the 
 *      published date. Priority low.
 *
 *@author James M Adams <james@medicationforall.com>
 *@version 0.3
 *@package framework
 */

Class CommentBox extends Component
{
//data


	/**
	 *   Comment user name.
	 *@access private
	 *@var string
	 */
	private $name;

	/**
	 *   Comment body text.
	 *@access private
	 *@var string
	 */
	private $comment;

	/**
	 *   Reply mode flag.
	 *@access private
	 *@var boolean
	 */
	private $reply = false;

	/**
	 *   Replying to id.
	 *@access private
	 *@var int
	 */
	private $replyID;

	/**
	 *   Mode to appease a specific use case will probably be removed.
	 *@access private
	 *@var boolean
	 */
	private $listOnly = false;


//constructor
/**
 *   Creates the comment component.
 *@param string $h header text
 *@param string $file File Resource name, don't set this unless you need to specifically set what file this object refers to on lookup.
 */
	function __construct($h='Comments', $file='')//,$pub='',$priv='',$file='')
	{
		parent::__construct($h, 'comment');
		$this->setFile($file);
	}

//methods

/**
 *   Processes new comments to be added.
 *@return boolean Returns true if a comment was added.
 *@param boolean $processChildren Process children flag.
 *@see ComponentBlog
 */
	function process($processChildren=true)
	{
		$this->debug('process ComponentComment '.$this->getUnique());
		//temporary clear static comments check.
		//$children = $this->getChildren();

		//if the comment object is used in a static context this is a potential fix. Ideally your code should be refactored so it isn't using comment as a static object. 
		//if(!empty($children))
		//{
		//	$this->setChildren(array());
		//}

		$pass="true";
		$page = $this->getParent('page');
		$login = $page->getAccount()->isLogin();
		$settings = $page->getSettings();
		$account = $page->getAccount();

		$mysqli = $this->getParent('page')->getConnect()->getMysqli();

		$site = $settings->getSite();

		$id = '';

		if(!empty($_POST['commentID']))
		{
			$id = $_POST['commentID'];
		}

		//run component process , run core process
		parent::process();


		$this->script('ComponentComment.js');
	


		if(!empty($_REQUEST['reply']) && strcmp($_REQUEST['reply'], 'true')==0)//check for reply
		{
			if(!empty($_REQUEST['id']))
			{
				$this->reply = true;
				$this->replyID = $this->parse($_REQUEST['id']);
			}
		}
		//print 'comment id '.$id.' '.$this->getUnique();

		if(!empty($_POST['comment']) && $this->equal($_POST['comment'], 'true') && strcmp($id, $this->getUnique())==0) 
		{
			if($login)//Use username instead of anonymous name.
			{
				$this->name = $account->getName();
			}
			else
			{
				if(!empty($_POST['ccName']))
				{
					$this->name = $this->parse($_POST['ccName']);
				}
				else
				{
					$pass=false;
					$this->message ='<div class="error">Name can not be empty</div>';
				}
			}

			if($pass)//Comment not empty check.
			{
				if(!empty($_POST['ccComment']))
				{
					$this->comment= $this->parse($_POST['ccComment'],"spam");
					//print 'comment language test iegūtu vairāk komentāru mēstules'.utf8_encode ($_POST['ccComment']);
				}
				else
				{
					$pass=false;
					$this->message ='<div class="error">Comment can not be empty</div>';
				}
			}

			if($pass)//Comments per session limit.
			{
				if($settings->isCommentLimit())
				{
					$count = $settings->commentIncrement();

					if($count > ($settings->getCommentCount()))
					{
						$pass=false;
						$this->message ='<div class="error">Too many comments. Take a break, Come back later.</div>';
					}
				}
			}

			if($pass) {//spammer check
				if($account->isSpammer()==true){
						$pass=false;
						$this->message ='<div class="error">Please don\'t spam</div>';
				}
			}

			if($pass) //Add comment.
			{
				$ip = $this->getRealIpAddr();

				$status = 'approve';

				$tclass=$this->getUnique();

				$file = $this->getFile();
				$name = $this->name;
				$comment = $this->comment;
				$email = $settings->getEMail();
				$replyID = $this->replyID;

				if($settings->isCommentAnonApproval() === false)//skips the approval process
				{
					$status = 'active';
				}

				if($login)
				{
					$status = 'active';
				}

				if(empty($tclass))
				{
					$tclass='';
				}

				$query = 'INSERT INTO tblcomment (filename,username,comment,ip,site,status,replyid,class) VALUES(?,?,?,?,?,?,?,?)';

				//print $query;

				if(($stmnt = $mysqli->prepare($query)))
				{
					$stmnt->bind_param("ssssssss", $file, $name, $comment, $ip, $site, $status, $replyID, $tclass);
					$stmnt->execute();
				}

				if($settings->isCommentEmail())
				{
					mail($email, 'Comment for '.$file, 'Name: '.$name."\nComment: ".$comment, 'From: '.$email);
				}

				if($stmnt->affected_rows > 0)
				{
					$this->message='<div class="confirm">Comment Added'.'</div>';
				}
			}
		}

		if($account->access('admin'))
		{
			$query = 'UPDATE tblcomment SET status = ? where id =?';
			if($stmnt = $mysqli->prepare($query))
			{
				$stmnt->bind_param('si', $status, $id);

				if(!empty($_REQUEST['deleteComment']))//delete a comment
				{
					$this->debug('deleting comment '.$_REQUEST['deleteComment']);
					$id = $_REQUEST['deleteComment'];
					$status = 'delete';
					$stmnt->execute();
				}



				if(!empty($_REQUEST['approveComment']))//approve an anonymous comment
				{
					$this->debug('Approve comment '.$_REQUEST['approveComment']);
					$id = $_REQUEST['approveComment'];
					$status = 'active';
					$stmnt->execute();
				}
			}
		}

		if($processChildren)
		{
			$this->children('process');
		}

		return $pass;
	}

/**
 *   Prints the comment list and, comment submit form.
 */
	function cContent()
	{
		echo('<div class="cContent">');
		echo('<div class="commentList">');
		
		$this->printComments();
		$this->children('show');

		if($this->listOnly==false)
		{
			$this->printAdd();
		}

		echo('</div>');
		echo('</div>');
	}

/**
 *   Sets up the list of comments to be printed, by adding Comments as children to this object.
 *@param boolean $short If set to only sets the most recent comment to be shown.
 *@todo unintuitive short ajax resolution occuring here.
 *@see Comment
 */
	function printComments($short = false)
	{
		$process = true;
		$mysqli = $this->getParent('page')->getConnect()->getMysqli();
		$page = $this->getParent('page');
		$login = $page->getAccount()->isLogin();
		$account = $page->getAccount();
		$settings = $page->getSettings();
		$sqlstatus = ' AND status = \'active\'';
		$single = '';
		$file = $this->getFile();
		$site = $settings->getSite();

		if($short)
		{
			$process = $this->process();
			$single= ' order by id desc limit 1';
		}
		else
		{
			$single = ' order by time';
		}

		$class=$this->getUnique();

		if(empty($class))
		{
			$class="";
		}

		if($short || ($login && strcmp($account->getMode(), 'admin')==0))
		{
			$sqlstatus = 'AND status != \'delete\'';
		}
		$query = 'SELECT username,time,comment,id,status,replyid FROM tblcomment WHERE filename=? '.$sqlstatus.' AND site=? AND class=?'.$single;


		if($stmnt =  $mysqli->prepare($query))
		{
			$stmnt->bind_param('sss', $file, $site, $class);
			$stmnt->execute();
			$stmnt->bind_result($username, $time, $comment, $id, $status, $replyid);

			if($process && $short == false)
			{
				while($stmnt->fetch())
				{
					$comment = new Comment($id, $username, $comment, $status, $time);

					if(empty($replyid))
					{
						$this->add($comment);
					}
					else
					{
						if(($find = $this->findChild($replyid)) !=null)
						{
							$find->add($comment);
						}
					}
				}
			}

			Comment::setShort($short);
			Comment::setReply($this->reply);
			Comment::setReplyID($this->replyID);
 
			if($short)
			{
				if($process)
				{
					while($stmnt->fetch())
					{
						$comment = new Comment($id, $username, $comment, $status, $time);
						$this->add($comment);
					}

					$tmpChild = $this->getChildren();

					if(!empty($tmpChild))
					{
						$tmpChild[count($tmpChild)-1]->show();
					}
				}
				echo $this->message;
			}
		}
	}

/**
 *   Prints the submit comment form.
 *@param int $id Set when replying to a specific comment.
 */
	function printAdd($id='')
	{
		$page = $this->getParent('page');
		$account = $page->getAccount();
		$login = $account->isLogin();
		echo('<div class="form" >');
		echo($this->message);
		echo('<form  action="" method="POST">');
		echo('<input type="hidden" name="comment" value="true" />');
		echo('<input type="hidden" name="token" value="'.$account->getToken().'" />');

		//print 'printadd '.$this->getUnique();
		if($this->getUnique())
		{
			echo('<input type="hidden" name="commentID" value="'.$this->getUnique().'"/>');
		}

		if($this->reply && !empty($id))
		{
			echo('<input type="hidden" name="ccReply" value="'.$id.'" />');
		}

		if($login)
		{
			echo('<div>'.$account->getName().'</div>');
		}
		else
		{
			echo('<div>Name <input type="text" name="ccName" required autofocus value="" /></div>');
		}
		echo('<div>Comment <textarea name="ccComment" required autofocus></textarea></div>');

		echo('<div class="comSubmit"><input type="submit" value="add comment" class="cSub" /></div>');
		echo('</form>');
		echo('</div>');
	}

/**
 *   Gets the users IP address
 *@return String the users IP address.
 */
	function getRealIpAddr()
	{
    		if(!empty($_SERVER['HTTP_CLIENT_IP']))   //check ip from share internet
    		{
      			$ip=$_SERVER['HTTP_CLIENT_IP'];
    		}
    		else if(!empty($_SERVER['HTTP_X_FORWARDED_FOR']))   //to check ip is pass from proxy
    		{
      			$ip=$_SERVER['HTTP_X_FORWARDED_FOR'];
    		}
    		else
   		{
      			$ip=$_SERVER['REMOTE_ADDR'];
    		}
    		return $ip;
	}

/**
 *   Sets the listOnly flag, this feature isn't done and may be removed.
 *@param boolean $l Display the comment list without the ability to reply.
 */
	function setListOnly($l)
	{
		$this->listOnly = $l;
	}

/**
 *   Short is ran when the component is called as an ajax request.
 *@return string Response from an ajax request.
 */
	function short()
	{
		//Work around for if a comment is placed on a default index. 
		$page = 'index.php';
		if(!empty($_REQUEST['page']))
		{
			$page = $_REQUEST['page'];
		}

		$this->setFile($page);
			
		if(strcmp($_REQUEST['commentID'], 'undefined')==0)
		{
			//print 'comment id is empty';
		}
		else
		{
			//print 'comment id is not empty '.$_REQUEST['commentID'];
			$this->setUnique($_REQUEST['commentID']);
			//print('setting unique');
		}
		$this->printComments(true);
	}
}
?>
