<?php

/**
 *   Medication For All Framework source file Component,
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
 *   Central class from which all component classes derive.
 *   This is a layer of abstraction for optimizing code re-use. 
 *   You are encouraged to write more classes which inherit from this class or 
 *   any other framework class to fit your needs. You'll notice in this 
 *   documentation now that classes derived from component list their output. 
 *   Familiarize yourself with the output to aid in writing custom CSS.
 *
 *   Note if you have more than one component of the same type e.g. multiple text boxes; 
 *   give them each a unique identifier name.
 *
 * *Output:
 *<code>
 * <div class="component
 *	for($i=0;$i<count($this->classes);$i++)
 *	{
 *		' '.$this->classes[$i]);
 *	}
 * ">
 *
 * 	<div class="cHeader"
 *		$this->head
 * 	</div>
 *
 *	<div class="cContent">
 *		$this->text
 *	</div>
 *
 *	<div class="cFooter">
 *		&nbsp;
 * 	</div>
 * </div>
 *</code>
 *
 *@author James M Adams <james@medicationforall.com>
 *@version 0.1
 *@package framework
 */
class Component extends Core
{
//data
	/**
	 *   Head text.
	 *@access private
	 *@var string
	 */
	private $head;

	/**
	 *   Message displayed to user, upon processing form.
	 *@access protected
	 *@var string
	 */
	protected $message='';

	/**
	 *   Display delete entry confirmation dialog.
	 *@access protected
	 *@var boolean
	 */
	protected $delete = false;

	/**
	 *Overrride per component to enable/disable edit functionality.
	 *note this value will only predictably work (remain statically callable) for components that are part of an app.
	 *@var boolean
	 */
	//protected $editFlag = false;



//constructor
/**
 *   Constructs a generic component. This constructor was initially 
 *   setup for testing purposes this isn't the finished variant.
 *
 *   Meaning the initial version was a component / text box ie 
 *   $this->text is probably no longer needed now in this class.
 *@param string $h value header text
 *@param string $type component type
 */
	function __construct($h="", $type="")
	{
		parent::__construct("component".ucFirst($type));
		$this->head = $h;


		$this->setType($type);
		if($this->getType() != "")
		{
			$this->addClass($this->getType());
		}
	}

/**
 *   Generic process component method right now only provides debug information.
 */
	function process()
	{
		//run core process
		parent::process();
		$this->debug("process ".$this->getName(), 'component');
	}

//CONTENT
/**
 *   Shows the component in normal or text mode. This method is used by most component classes.
 *@param boolean $coreShow Flag for whether to call cores show method.
 */
	function show($coreShow=true)
	{
		//print 'run core show'.$this->getName();

		if($coreShow)
		{
			parent::show();
		}

		if($this->getParent('page')->getAccount()->access($this->getLevel()))//default access level check!
		{
			$this->debug("print ". $this->getName());

			$this->cDiv();

			if($this->getParent('page')->getAccount()->isEdit() || $this->getParent()->isEdit()  || $this->isEdit())
			{
				//print('in edit mode'. $this->getName());
				$this->edit();
			}
			else
			{
				//print('in show mode'. $this->getName());
				$this->cHeader();
				$this->cContent();
				$this->cFooter();
			}
			echo('</div>'."\n");
		}
	}

/**
 *   Could stand to be more aptly named. This function writes the components outermost div along with it's other class names.
 */
	function cDiv()
	{
		$id=$this->getUnique();

		$class = $this->getClass();

		$coord = '';

		if($this->findClass('dialog'))
		{
			$coord =  $this->getParent('page')->getSettings()->getCoord();
		}

		if(!empty($id))
		{
			$id=' id="'.$id.'"';
		}
		else
		{
			$id='';
		}

		if(!empty($class))
		{
			$class = ' '.$class;
		}

		if(strpos($class, 'dialog') && !empty($coord))
		{
			$coord = ' '.$coord;
		}
		echo('<div'.$id.' class="component'.$class.'"'.$coord.'>'."\n");
	}

/**
 *   Generic cHeader used in most component classes.
 */
	function cHeader()
	{
		echo('<div class="cHeader">'."\n");
			echo($this->head);
		echo('</div>'."\n");
	}

/**
 *   Generic cContent this function is routinely overwritten by all components.
 */
	function cContent()
	{
		echo('<div class="cContent">'."\n");

			//print 'component children show';
			$this->children('show');
		echo('</div>'."\n");

	}

/**
 *   Generic cFooter used in most component classes.
 */
	function cFooter()
	{
		echo("\n".'<div class="cFooter">'."\n");
		echo('&nbsp;'."\n");
		echo('</div>'."\n");		
	}

/**
 *   Called from show, this method actually provides much of the same functionality 
 *   but for when an edit mode is active. This function could probably be implemented
 *   elsewhere ? or at least called in a different fashion as opposed to from within show.
 */
	function edit()
	{
		$this->cHeader();
		$this->cContent();
		$this->cFooter();
	}


/**
 *   Generic eHeader, in a lot of components this method doesn't make sense, because 
 *   there may not be a need to edit the header info.. I guess what should be set is 
 *   if the component even has an edit mode.. and if it doesn't don't bother with editing it.
 */
	function eHeader()
	{
		echo('<div class="cHeader">'."\n");
			echo('<input type="hidden" name="etype" value="Component'.ucFirst($this->getType()).ucFirst($this->getUnique()).'"/>');
			echo('<input type="hidden" name="edit" value="true" />');
			echo('<input type="text" name="eHead" value="'.$this->head.'" />');
		echo('</div>'."\n");
	}

/**
 *   Should probably show the original cContent along with the message. Then when an 
 *   editor is written just override this method.
 */
	function eContent()
	{
		echo('<div class="cContent">'."\n");
			echo('haven\'t written the editor yet');
		echo('</div>'."\n");
	}

/**
 *   Generic efooter for edit mode
 */
	function eFooter()
	{
		echo('<div class="cFooter">'."\n");
			echo('<input class="'.$this->getType().'EditSubmit" type="submit" value="save" />');
		echo('</div>'."\n");
	}

/**
 *   Custom XML header.
 *@return string XML header text.
 */
	function xHeader()
	{
		$returner ='';
		$returner .=parent::xHeader();

		if(!empty($this->head))
		{
			$returner .= ' head="'.$this->head.'"';
		}

		return $returner;
	}

//Get & Set
/**
 *   Sets the header text.
 *   This function name is used in Page. Although arguably more descriptive here as to what it does.
 *@param $h Header text.
 *@see Page::setHead()
 */	
	function setHead($h)
	{
		$this->head = $h;
	}

/**
 *   Gets the Header Text.
 *@return string Header Text.
 */
	function getHead()
	{
		return $this->head;
	}

/**
 *   Sets the global message to be displayed to the user after processing.
 *@param string $m Message.
 */
 	function setMessage($m)
 	{
		$this->message = $m;
 	}

 /**
  *   Gets the global message to be displayed to the user after processing.
  *@return string Message.
  */
  	function getMessage()
  	{
		return $this->message;
  	}

//Form
/**
 *   Delete entry confirmation dialog.
 *@param string $name Text Label displayed to the user for confirming the delete.
 */
	function deleteConfirm($name)
	{
		$token = $this->getParent('page')->getAccount()->getToken();
		echo '<div class="dialog confirm">';
			echo '<div class="cHeader">';
				echo 'Confirm Delete';
			echo '</div>';

			echo '<div class="cContent" style="text-align:center;width:100%">';
				echo '<div>Delete '.$name.' ?</div>';

				echo '<form style="display:inline-block" action="" method="post">';
				echo '<input type="hidden" name="'.$this->getType().'DelConf" value="true" ></input>';
				echo '<input type="hidden" name="token" value="'.$token.'" ></input>';
				echo '<input class="deleteConfirmOk" style="display:inline-block" type="submit" value="Ok" ></input>';
				echo '</form>';

				echo '<form style="display:inline-block" action="" method="post">';
				echo '<input type="hidden" name="'.$this->getType().'DelConf" value="false"></input>';
				echo '<input class="deleteConfirmCancel" style="display:inline-block" type="submit" value="Cancel"></input>';
				echo '</form>';
			echo '</div>';
		echo '</div>';
	}

/**
 *Custom Awesome debug content.
 */
	function adContent()
	{
		echo '<br /><br /><span class="lightTitle">Component Content:</span>';
		parent::adContent();
		echo '<br /> '.'<span class="lightTitle">Head:</span> '.$this->head;
		echo '<br /> '.'<span class="lightTitle">Message:</span> '.$this->message;
		echo '<br /> '.'<span class="lightTitle">Delete Confirmation:</span> ';
		if($this->delete)
		{echo 'true';}
		else
		{echo 'false';}
	}

/**
 *
 */
	function setDelete($d){
		$this->delete = $d;
	}
}
?>
