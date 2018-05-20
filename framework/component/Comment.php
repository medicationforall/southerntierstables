<?php
/**
 *   Medication For All Framework source file Comment,
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
 *   Kind of a throwback class; basically in order to show comments with replies 
 *   I needed a tree structure and instead of re-inventing the wheel; I decided to 
 *   inherit from core. A strong benefit to this, is there is no hardwired limit to the level of comment nesting.
 *
 *@author  James M Adams <james@medicationforall.com>
 *@version 0.1
 *
 *@package framework
 */

class Comment extends Core
{
//data
	/**
	 *   Comment name.
	 *@access private
	 *@var string
	 */
	private $cname;

	/**
	 *   Comment Text.
	 *@access private
	 *@var string
	 */
	private $comment;

	/**
	 *   Comment status ie active, approve.
	 *@access private
	 *@var string
	 */
	private $status;

	/**
	 *   Comment timestamp.
	 *@access private
	 *@var string
	 */
	private $time;

	/**
	 *   Display short flag mode. (Really means will display newest comment waiting for approval).
	 *@access private
	 *@var boolean
	 */
	private static $short;

	/**
	 *   Replying to comment flag.
	 *@access private
	 *@var boolean
	 */
	private static $reply = false;

	/**
	 *   Which comments is being replied to..
	 *@access private
	 *@var int
	 */
	private static $replyID='';

//construct
/**
 *   Creates the comment object.
 *@param int $id Comment ID.
 *@param string $name Comment name.
 *@param string $comment Comment text.
 *@param string $status Status ie approve, or active.
 *@param string $time Timestamp.
 */
	function __construct($id, $name, $comment, $status, $time)
	{
		parent::__construct('comments');
		$this->setUnique($id);
		$this->cname = $name;
		$this->comment = $comment;
		$this->status = $status;
		$this->time = $time;
	}

//methods
/**
 *   Shows the comment contents. Different flag states change the output of the comment.
 */
	function show()
	{
		$page = $this->getParent('page');
		//$login = $page->getAccount()->isLogin();
		$account = $page->getAccount();

		$short = self::$short;

		echo('<div class="comment">');

		echo('<div class="cHeader">');
		$delete='';
		$status = '';
		$approvalNotice = '';

		//if($login && strcmp($account->getMode(),'admin')==0)
		if($account->access('admin'))
		{
			if(strcmp($this->status, 'approve')==0)
			{
				$status = ' <a class="cApprove" href="?approveComment='.$this->getUnique().'">'.'Approve</a>';
			}
			else
			{
				$status = ' '.$this->status;
			}
			$delete =' <a class="cDelete" href="?deleteComment='.$this->getUnique().'">Delete</a>';
		}
		else if($short && (strcmp($this->status, 'approve')==0))
		{
			$approvalNotice = '<em class="time message"> Comment waiting to be approved. </em>';
		}

		echo '<em class="time"> '.$this->time.$status.$delete.' <a class="cReply" href="?reply=true&amp;id='.$this->getUnique().'">Reply</a>'.' </em>';
		echo($this->cname);


		echo $approvalNotice;
		echo('</div>');

		echo('<div class="cBody">');
		echo($this->comment);
		echo('</div>');

		$this->children('show');

		echo('</div>');
	}

/**
 *   Static sets the short flag
 *@param boolean $s Short flag.
 */
	static function setShort($s)
	{
		self::$short = $s;
	}

/**
 *   Static sets the reply flag.
 *@param boolean $r Reply flag.
 */
	static function setReply($r)
	{
		self::$reply = $r;
	}

/**
 *   Static sets the reply id.
 *@param int $r Reply id.
 */
	static function setReplyID($r)
	{
		self::$replyID = $r;
	}
}

?>
