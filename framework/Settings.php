<?php

/**
 *   Medication For All Framework source file Settings,
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
 *   Singleton object, stores the site wide settings and is stored in Page.
 *
 *   This Class is starting to get perilous. At least everything is centralized. 
 *   If you have any suggestions to better handle this class, I'm listening. Note I'm using this class to avoid the use of session variables.
 *
 *@author James M Adams <james@medicationforall.com>
 *@version 0.1
 *@see Page
 *@package framework
 */
class Settings
{

//data

/*SITE SETTINGS*/
	/**
	 *   SiteName identifier used primarily in the database to differentiate site ownership of rows. 
	 *   By default is set to an empty ('') string because of how prepared statements work. 
	 *   Because they throw an exception when passed a null value.
	 *@access private
	 *@var string
	 */
	private $site = '';

	/**
	 *   Site wide email. used for comment submission notification. Contact form submission.
	 *@access private
	 *@var string
	 */
	private $email;

	/**
	 *   Base directory path for javascript scripts.
	 *@access private
	 *@var string
	 */
	private $scriptBase = "script";

/*USER REGISTRATION*/
	/**
	 *   Can new users be registered.
	 *@access private
	 *@var boolean
	 */
	private $register = true;

	/**
	 *   New users have to validate their account via email.
	 *@access private
	 *@var boolean
	 */
	private $validate = true;

	/**
	 *   New users start with activated accounts.
	 *@access private
	 *@var boolean
	 */
	private $startActive = false;


	/**
	 *   Administrators can create pages.
	 *@access private
	 *@var boolean
	 */
	private $createPage = true;

	/**
	 *   Display only the most basic file menu options.
	 *@access private
	 *@var boolean
	 */
	private $basicFileMenu = true;

	/**
	 *   CreatePage Origin default file name.
	 *@access private
	 *@var string
	 */
	private $cpOrigin = 'template.php';


	/**
	 *   Default file upload path.
	 *@access private
	 *@var string
	 */
	private $uploadPath = 'image';
	


/*HTTP KEYS*/
	/**
	 *   Key to initiate login.
	 *@access private
	 *@var string
	 */
	private $loginKey = 'login';

	/**
	 *   Key to create a new user.
	 *@access private
	 *@var string
	 */
	private $registerKey = 'createuser';

	/**
	 *   Key to create a new page.
	 *@access private
	 *@var string
	 */
	private $createPageKey = 'createPage';

	/**
	 *   Key to validate a user.
	 *@access private
	 *@var string
	 */
	private $validateKey = 'validate';


/*ComponentComment*/
	/**
	 *   ComponentComment limit the number of comments a user can make in one session.
	 *@access private
	 *@var boolean
	 */
	private $commentLimit = true;

	/**
	 *   ComponentComment limiter integer representing how many comments a user can make 
	 *@access private
	 *@var int
	 */
	private $commentCount = 3;

	/**
	 *   Counts number of Comments made during session.
	 *@access private
	 *@var int
	 */
	private $commentCounter = 0;

	/**
	 *   Anonymous comments require administrative approval flag.
	 *@access private
	 *@var boolean
	 */
	private $commentAnonApproval = true;

	/**
	 *   Email owner when new comments are posted flag. 
	 *   Setting this to false is a performance boost, but only when adding a comment.
	 *@access private
	 *@var boolean
	 */
	private $commentEmail = true;	

/*ComponentGallery*/
	//when a gallery is traversed, searched, or queried for recent; The contents of that gallery basically sit in memory. This way a displayed image knows everything about itself, who it's neighbors are and where it came from. 
	/**
	 *   ComponentGallery global nav file paths.
	 *@access private
	 *@var array
	 *@todo cleaqr these.
	 */
	private $nav = array();

	/**
	 *   ComponentGallery global nav names.
	 *@access private
	 *@var array
	 */
	private $navNames = array();

	/**
	 *   ComponentGallery image count displayed per page.
	 *@access private
	 *@var int
	 */
	private $galleryLimit = 12;

	/**
	 * Number of images shown for a galleries RSS page.
	 *@access private
	 *@var int
	 */
	private $galleryRSSCount = 10;

	/**
	 *   ComponentGallery an array of what gallery (parent) each image links back to.
	 *@access private
	 *@var string
	 */
	private $galleryLinks = array();

	/**
	 *   ComponentGallery links to the images file paths.
	 *@access private
	 *@var string
	 */
	private $galleryPics =  array();

	/**
	 *   ComponentGallery links to the images thumbnail file paths.
	 *@access private
	 *@var string
	 */
	private $galleryThumbs = array();

	/**
	 *   ComponentGallery the images dates.
	 *@access private
	 *@var string
	 */
	private $galleryDates = array();

	/**
	 *Flag for printing Gallery Dates.
	 *@access private
	 *@var booelan
	 */
	private $galleryShowDate = true;

/*Gallery2*/
	private $modsub = true;

	private $placeHolderWidthHeight=true;

	
/*Component Blog*/
	/**
	 *   Blog entries.
	 *@access private
	 *@var array
	 */
	private $blogEntry = array();

	/**
	 *   Number of recent RSS entries shown for ComponentBlog.
	 *@access private
	 *@var int
	 */
	private $blogRSSCount = 10;

//ComponentMap
	/**
 	*Google Map Key.
	*@access private
	*@var string
 	*/
	private $googleMapKey = "";

//Feed
	/**
	 *The max number of feeds on page load that can be drawn from their source websites.
	 *@access private
	 *@var int 
	 */
	private $feedLoadMax = 3;

//dialogs

	/**
	 *y coordinate of dialog boxes
	 *@access private
	 *@var int
	 */
	private $top;

	/**
	 *x coordinate of dialog boxes
	 *@access private
	 *@var int
	 */
	private $left;



//methods

/**
 *Generic boolean setter.
 *@param boolean $value Boolean value to be set.
 *@param boolean $variable Boolean variable reference pointer.
 *@todo Is this actually being used ?
 */
function setBool($value,&$variable)
{
	if(is_bool($value))
	{
		$variable = $value;
	}
}


/**
 *   Sets email.
 *@param string $e Sets the site e-mail.
 */
	function setEmail($e)
	{
		$this->email = $e;
	} 

/**
 *   Gets email.
 *@return string The site e-mail.
 */
	function getEmail()
	{
		return $this->email;
	}

/**
 *   Gets commentLimit flag
 *@return boolean
 */
	function isCommentLimit()
	{
		return $this->commentLimit;
	}

/**
 *   Sets commentLimit flag
 *@param boolean $c
 */
	function setCommentLimit($c)
	{
		$this->commentLimit = $c;
	}

/**
 *   Gets commentCount integer
 *@return int
 */
	function getCommentCount()
	{
		return $this->commentCount;
	}

/**
 *   Sets commentCount integer
 *@param int $c
 */
	function setCommentCount($c)
	{
		$this->commentCount = $c;
	}

/**
 *   Adds 1 to the comment counter.
 *@return int The position of the counter after incrementing.
 */
	function commentIncrement()
	{
		$this->commentCounter++;
		return $this->commentCounter;
	}

/**
 *   Gets the email comments flag.
 *@return boolean $emailComment flag.
 */
	function isCommentEmail()
	{
		return $this->commentEmail;
	}
/**
 *   Sets the email comments flag.
 *@param boolean $s Email comments flag.
 */
	function setCommentEmail($s)
	{
		$this->commentEmail = $s;
	}

/**
 *   Gets register new users flag
 *@return boolean
 */
	function isRegister()
	{
		return $this->register;
	}

/**
 *   Sets register new users flag
 *@param boolean $r
 */
	function setRegister($r)
	{
		//$this->register = $r;
		$this->setBool($r, $this->register);
	}

/**
 *   Gets createPage Flag
 *@return boolean
 */
	function isCreatePage()
	{
		return $this->createPage;
	}

/**
 *   Sets CreatePage flag
 *@param boolean $c
 */
	function setCreatePage($c)
	{
		//$this->createPage = $c;
		$this->setBool($c, $this->createPage);
	}

/**
 *   Gets basicFileMenu Flag
 *@return boolean
 */
	function isBasicFileMenu()
	{
		return $this->basicFileMenu;
	}

/**
 *   Sets basicFileMenu flag
 *@param boolean $c
 */
	function setBasicFileMenu($b)
	{
		$this->setBool($c, $this->basicFileMenu);
	}



/**
 *   Gets validate user process flag
 *@return boolean
 */
	function isValidate()
	{
		return $this->validate;
	}

/**
 *   Sets validate user process flag
 *@param boolean $v
 */
	function setValidate($v)
	{
		//$this->validate = $v;
		$this->setBool($v, $this->validate);
	}

/**
 *   Gets users start active flag
 *@return boolean
 */
	function isStartActive()
	{
		return $this->startActive;
	}

/**
 *   Sets users start active flag
 *@param boolean $s
 */
	function setStartActive($s)
	{
		//$this->startActive = $s;
		$this->setBool($s, $this->startActive);
	}

/**
 *   Gets Login Key
 *@return string
 */
	function getLoginKey()
	{
		return $this->loginKey;
	}

/**
 *   Sets LoginKey (shouldn't normally have to edit)
 *@param string $l
 */
	function setLoginKey($l)
	{
		$this->loginKey = $l; 
	}

/**
 *   Gets Register new user key 
 *@return string
 */
	function getRegisterKey()
	{
		return $this->registerKey;
	}

/**
 *   Set Register new user key (shouldn't normally have to edit)
 *@param string $r
 */
	function setRegisterKey($r)
	{
		$this->registerKey = $r; 
	}

/**
 *   Gets Create Page Key
 *@return string
 */
	function getCreatePageKey()
	{
		return $this->createPageKey;
	}

/**
 *   Sets Create Page Key (shouldn't normally have to edit)
 *@param string $c
 */
	function setCreatePageKey($c)
	{
		$this->createPageKey = $c; 
	}

/**
 *   Gets the Create Page Origin Path
 *@return string
 */
	function getcpOrigin()
	{
		return $this->cpOrigin;
	}

/**
 *   Sets Create Page origin File path
 *@param string $o
 */
	function setcpOrigin($o)
	{
		if(file_exists($o))
		{
			$this->cpOrigin = $o;
		}
	}

/**
 *   Gets the ValidateKey
 *@return string
 */
	function getValidateKey()
	{
		return $this->validateKey;
	}

/**
 *   Sets the validateKey
 *@param string $v
 */
	function setValidateKey($v)
	{
		$this->ValidateKey = $v; 
	}

/**
 *   Gets the site identifier.
 *@return string Site Identifier name.
 */
	function getSite()
	{
		return $this->site;
	}

/**
 *   Sets the site identifier.
 *@param string $s Site identifier name.
 */
	function setSite($s)
	{
		$this->site = $s;
	}

/**
 *   Get default file upload path.
 *@return string Default file upload path.
 */
	function getUploadPath()
	{
		return $this->uploadPath;
	}

/**
 *   Set file upload path
 *@wishlist should check for read/write permission
 *@param string $u Upload file path. 
 */
	function setUploadPath($u)
	{
		if(file_exists($u))
		{
			$this->uploadPath = $u;
		}
	}

/**
 *   Gets the ComponentGallery nav filepath array.
 *@return array Directory file path list.
 */
	function getNav()
	{
		return $this->nav;
	}

/**
 *   Sets the ComponentGallery nav filepath array.
 *@param array $n Directory file path list.
 */
	function setNav($n)
	{
		$this->nav = $n;
	}

/**
 *   Gets the ComponentGallery nav names array.
 *@return array Directory names list.
 */
	function getNavNames()
	{
		return $this->navNames;
	}

/**
 *   Sets the ComponentGallery nav names array.
 *@param array $n Directory names list.
 */
	function setNavNames($n)
	{
		$this->navNames = $n;
	}

/**
 *Gets the number of images to be shown for RSS.
 *@return int Count of RSS images to show
 */
	function getGalleryRSSCount()
	{
		return $this->galleryRSSCount;
	}

/**
 *Sets the RSS image Count.
 *@param int $c RSS image count.
 */
	function setGalleryRSSCount($c)
	{
		$this->galleryRSSCount = $c;
	}

/**
 *   Gets the images per page gallery limit for ComponentGallery.
 *@return int Images per page limit.
 */
	function getGalleryLimit()
	{
		return $this->galleryLimit;
	}

/**
 *   Sets the images per page gallery limit for ComponentGallery.
 *@param int @g Images per page.
 */
	function setGalleryLimit($g)
	{
		$this->galleryLimit = $g;
	}

/**
 *   Gets the Gallery Links array
 *@return array
 */
	function getGalleryLinks()
	{
		return $this->galleryLinks;
	}

/**
 *   Adds a link to the end of the gallery links array.
 *@param string $l 
 */
	function addGalleryLink($l)
	{
		$this->galleryLinks[count($this->galleryLinks)] = $l;
	}
/**
 *   Sets the Gallery Links array.
 *@param array $l
 */
	function setGalleryLinks($l)
	{
		$this->galleryLinks = $l; 
	}

/**
 *   Gets the Gallery Pics array.
 *@return array
 */
	function getGalleryPics()
	{
		return $this->galleryPics;
	}

/**
 *   Adds a pic to the end of the gallery pics array.
 *@param string $p File resource filepath.
 */
	function addGalleryPic($p)
	{
		$this->galleryPics[count($this->galleryPics)] = $p;
	}
/**
 *   Sets the gallery Pics Array.
 *@param array $p
 */
	function setGalleryPics($p)
	{
		$this->galleryPics = $p; 
	}

/**
 *   Gets the gallery thumbnails
 *@return array
 */
	function getGalleryThumbs()
	{
		return $this->galleryThumbs;
	}

/**
 *   Adds a thumbnail to the end of the gallery thumbs array
 *@param string t Thumbnail filepath
 */
	function addGalleryThumb($t)
	{
		$this->galleryThumbs[count($this->galleryThumbs)] = $t;
	}
/**
 *   Sets the gallery thumbs array.
 *@param array $t
 */
	function setGalleryThumbs($t)
	{
		$this->galleryThumbs = $t; 
	}

/**
 *   Gets the gallery dates array.
 *@return array
 */
	function getGalleryDates()
	{
		return $this->galleryDates;
	}

/**
 *   Adds a date to the end of the gallery dates array.
 *@param string $d Date mm/dd/yyyy notation.
 */
	function addGalleryDate($d)
	{

		$this->galleryDates[count($this->galleryDates)] = $d;
	}
/**
 *   Sets the gallery dates array.
 *@param array $d
 */
	function setGalleryDates($d)
	{
		$this->galleryDates = $d; 
	}
/**
 *   Sets the anonymous approval flag.
 *@param boolean @a Anonymous approval flag.
 */
	function setCommentAnonApproval($a)
	{
		/*if(is_bool($a))
		{
			$this->commentAnonApproval = $a;
		}*/

		$this->setBool($a, $this->commentAnonApproval);
	}
/**
 *   Gets the anonymous approval flag.
 *@return boolean Anonymous approval flag.
 */
	function isCommentAnonApproval()
	{
		return $this->commentAnonApproval;
	}


/**
 *   Gets the javascript directory path.
 *@wishlist this is part of the install script that needs to be created. default directory should probably be one that exists refer to unit test testScriptBase().
 *@return string Javascript directory path.
 */
	function getScriptBase()
	{
		return $this->scriptBase;
	}
/**
 *   Sets the javascript directory path. Has to reference a directory that actually exists.
 *@param string $s Javascript directory path.
 */
	function setScriptBase($s)
	{
		if(file_exists($s))
		{
			$this->scriptBase = $s;
		}
	}
/**
 *   Sets blog entry to be shown id index.
 *@param array $e
 */
	function setBlogEntry($e)
	{
		$this->blogEntry = $e;
	}
/**
 *   Gets blog entry to be shown id index.
 *@wishlist come up with a better means of testing this or a way to factor this code to someplace else.
 *@return array
 */
	function getBlogEntry()
	{
		return $this->blogEntry;
	}

/**
 *   Sets the blog recent entries rss count max.
 *@param int $c
 */
	function setBlogRSSCount($c)
	{
		if(is_numeric($c) && $c > 0)
		{
			$this->blogRSSCount = $c;
		}
	}

/**
 *   Gets the blog recent entries rss count max.
 *@return int
 */
	function getBlogRSSCount()
	{
		return $this->blogRSSCount;
	}

/**
 *Gets the google map key.
 *@return string
 */
	function getGoogleMapKey()
	{
		//print '<br />getting key'.$this->googleMapKey;
		return $this->googleMapKey;
	}
/**
 *Sets the google map key.
 *@param string $k
 */
	function setGoogleMapKey($k)
	{
		//print 'setting key'.$k;
		$this->googleMapKey = $k;
	}

/**
 *This was an attempt to keep track of dialog coordinates. 
 *The only problem is most dialogs are generated on page load and are temporary objects not intended to be stored. But the coordinates DO have to be stored.
 *@return string The style tag positions for the dialog.
 */
	function getCoord()
	{
		$returner = '';

		if(isset($this->top) && isset($this->left))
		{
			$returner = 'style="left: '.$this->left.'px; top: '.$this->top.'px"';
		}

		//print $returner;

		return $returner;
	}

/**
 * Set top coordinate
 * @param int $t 
 */
	function setTop($t)
	{
		if(is_numeric($t))
		{
			$this->top = $t;
		}
	}
/**
 * Set left coordinate
 * @param int $l 
 */
	function setLeft($l)
	{
		if(is_numeric($l))
		{
			$this->left = $l;
		}
	}

/**
 *Max number of feeds that can be loaded.
 *@return int
 */
	function getFeedLoadMax()
	{
		return $this->feedLoadMax;
	}

/**
 *Set the max number of feeds that can be polled from external servers per page request. This really has to do with optimizing page load response time.
 *@param int $m Doesn't accept values less than 1.
 */
	function setFeedLoadMax($m)
	{
		if(is_numeric($m) && $m > 0)
		{
			$this->feedLoadMax = $m;
		}
	}
	
/**
 *Gets the flag for showing dates with component gallery
 *@return bool
 */
	function getGalleryShowDate()
	{
		return $this->galleryShowDate;

	}
/**
 *Flag for if dates should be shown along with image thumbnails for gallery component.
 *@param bool $d
 */
	function setGalleryShowDate($d)
	{
		$this->setBool($d, $this->galleryShowDate);
		/*if(is_bool($d))
		{
		$this->galleryShowDate = $d;
		}*/
	}

	function getModSub()
	{
		return $this->modSub;

	}

	function setModSub($m)
	{
		$this->modSub = $m;
	}

	function getPlaceHolderWidthHeight(){
		return $this->placeHolderWidthHeight;
	}

	function setPlaceHolderWidthHeight($bool){
		$this->placeHolderWidthHeight = $bool;
	}

/**
 *Custom Awesome debug content.
 */
	function adContent()
	{
		echo '<br /><br /><span class="lightTitle">Settings Content:</span>';
		echo '<br /> '.'<span class="lightTitle">Site Name:</span> '.$this->site;
		echo '<br /> '.'<span class="lightTitle">Email:</span> '.$this->email;
		echo '<br /> '.'<span class="lightTitle">Script Base:</span> '.$this->scriptBase;

		echo '<br /><br /><span class="lightTitle">Settings User Content:</span>';
		echo '<br /> '.'<span class="lightTitle">Register:</span> ';
		if($this->register)
		{echo 'true';}
		else
		{echo 'false';}
		echo '<br /> '.'<span class="lightTitle">Validate:</span> ';
		if($this->validate)
		{echo 'true';}
		else
		{echo 'false';}
		echo '<br /> '.'<span class="lightTitle">Start Active:</span> ';
		if($this->startActive)
		{echo 'true';}
		else
		{echo 'false';}
		echo '<br /> '.'<span class="lightTitle">Create Page:</span> ';
		if($this->createPage)
		{echo 'true';}
		else
		{echo 'false';}

		echo '<br /> '.'<span class="lightTitle">Create Page Origin:</span> '.$this->cpOrigin;
		echo '<br /> '.'<span class="lightTitle">Upload Path:</span> '.$this->uploadPath;
		echo '<br /><br /><span class="lightTitle">Settings Dialog Content:</span>';
		echo '<br /> '.'<span class="lightTitle">Top:</span> '.$this->top;
		echo '<br /> '.'<span class="lightTitle">Left:</span> '.$this->left;
	}
}
?>
