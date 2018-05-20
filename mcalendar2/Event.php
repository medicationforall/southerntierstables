<?php
/**
 *   Medication For All Calendar source file Event,
 *   Copyright (C) 2013  James M Adams
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
 *@package calendar
 */

/**
 *@author  James M Adams <james@medicationforall.com>
 *@version 0.1
 *
 *@package calendar
 */


class Event extends Component{
//data
private $eventId=''; 
private $title='';
private $date;
private $description='';
private $icon =''; 
private $eventType='';
private $dateFormat = "Y-m-d H:i:s";

private $cancelled = false;
private $soldOut = false;
private $limitedTickets = false;

private $loaded =false;

//constructor
/**
 *
 */
	function __construct( $head='', $type='event'){
		parent::__construct($head,$type);
	}

//methods

/**
 *
 */
	function process(){
		if($this->loaded == false){
			if($this->isToday()){
				$this->addClass('today');
			}

			$this->loaded=true;
		}

		if(!empty($_REQUEST['eventDetails'])&&strcmp($_REQUEST['eventDetails'],$this->getUnique())==0){
			$this->pushEventDetails();
		}

		if(!empty($_REQUEST['eventDetailsDate'])&&strcmp(date($this->dateFormat,$_REQUEST['eventDetailsDate']),$this->date)==0){
			$this->pushEventDetails();
		}
	}

/**
 *
 */
	function pushEventDetails(){
		$parent = $this->getParent('componentCalendar');
		$parent->setDetail($this);
		//print 'found devent details match '.$parent->getName();
	}

/**
 *
 */
	function isToday(){
		if(strcmp(date("Y-m-d",strtotime($this->date)),date("Y-m-d"))==0){
			//print 'found match';
			return true;
		}
		return false;
	}

/**
 *
 */
	function cHeader(){
		echo '<div class="cHeader">';
			echo '<div class="date">';
				if($this->isToday()){
					echo 'Today - ';
				}
				echo date("l, F jS",strtotime($this->date));
			echo '</div>';
		echo '</div>';
	}

/**
 *
 */
	function cContent(){
		echo '<div class="cContent">';
		echo '<div class="title">';
		echo $this->title;
		echo '</div>';
		echo '<div class="description">';
		echo $this->description;
		echo '</div>';
		$this->printCancelled();
		$this->printSoldOut();
		$this->printLimitedTickets();
		
		echo '</div>';
	}

/**
 *
 */
	function printCell(){
		if(!empty($this->description)){

			echo '<a class="eventDetails title" href="?eventDetails='.$this->getUnique().'">'.$this->title.'</a>';
			echo '<a class="eventDetails description" href="?eventDetails='.$this->getUnique().'">'.$this->stripLinks($this->description).'</a>';

		
		}else{
			echo '<span data-event="'.$this->getUnique().'">'.$this->title.'</span>';
		}
		$this->printCancelled();
		$this->printSoldOut();
		$this->printLimitedTickets();
	}

/**
 *
 */
	function stripLinks($text){
		//echo 'test'.$text;
		$modText = str_replace('<a', '<u', $text);
		$modText = str_replace('</a', '</u', $modText);
		return $modText;
	}

/**
 *
 */
	function printCancelled(){
		if($this->cancelled){
			print '<div class="cancelled">';
			print 'Cancelled';
			print '</div>';
		}
	}

/**
 *
 */
	function printSoldOut(){
		if($this->soldOut){
			print '<div class="soldout">';
			print 'Sold Out';
			print '</div>';
		}
	}


/**
 *
 */
	function printLimitedTickets(){
		if($this->limitedTickets){
			print '<div class="limitedtickets">';
			print 'Limited tickets available call 607-753-0377';
			print '</div>';
		}
	}

/**
 *
 */
	function setEventId($e){
		//print 'setting id '.$e;
		$this->eventId = $e;
		$this->setUnique('event_'.$e);
	}

/**
 *
 */
	function getEventId(){
		return $this->eventId;
	}

/**
 *
 */
	function setTitle($t){
		$this->title = $t;
	}

/**
 *
 */
	function getTitle(){
		return $this->title;
	}

/**
 *
 */
	function setDate($d){
		$this->date = date($this->dateFormat,strtotime($d));
	}

/**
 *
 */
	function getDate(){
		//print 'calling get date.';
		return $this->date;
	}

/**
 *
 */
	function setDescription($d){
		//print'<br />setting description'. $d;
		$this->description = $d;
	}

/**
 *
 */
	function getDescription(){
		return $this->description;
	}

/**
 *
 */
	function setEventType($type){
		$this->eventType=$type;
	}

/**
 *
 */
	function getEventType(){
		return $this->eventType;
	}

/**
 *
 */	function getCancelled(){
		return $this->cancelled;
	}

/**
 *
 */
	function setCancelled($c){

		if($c === true  || $c === false){
			$this->cancelled = $c;
		} else if($c === 0 || $c == 1){
			if($c === 0){
				$this->cancelled = false;
			} else if($c === 1){
				$this->cancelled = true;
			}
		}
	}

/**
 *
 */	function getLimitedTickets(){
		return $this->limitedTickets;
	}

/**
 *
 */
	function setLimitedTickets($c){

		if($c === true  || $c === false){
			$this->limitedTickets = $c;
		} else if($c === 0 || $c == 1){
			if($c === 0){
				$this->limitedTickets = false;
			} else if($c === 1){
				$this->limitedTickets = true;
			}
		}
	}

/**
 *
 */	function getSoldOut(){
		return $this->soldOut;
	}

/**
 *
 */
	function setsoldOut($c){

		if($c === true  || $c === false){
			$this->soldOut = $c;
		} else if($c === 0 || $c == 1){
			if($c === 0){
				$this->soldOut = false;
			} else if($c === 1){
				$this->soldOut = true;
			}
		}
	}

/**
 *
 */
	function setIcon($i){
		$this->icon = $i;
	}

/**
 *
 */
	function getIcon(){
		return $this->icon;
	}

/**
 *
 */
	function getLoaded(){
		return $this->loaded;
	}

/**
 *
 */
	function short(){
		//print 'calling event short for '.$this->getUnique();

		if(!empty($_REQUEST['eventDetails'])&&strcmp($_REQUEST['eventDetails'],$this->getUnique())==0){
			$this->pushEventDetails();
		}

		if(!empty($_REQUEST['eventDetailsDate'])&&strcmp(date($this->dateFormat,$_REQUEST['eventDetailsDate']),$this->date)==0){
			$this->pushEventDetails();
		}
	}

	function geCalendarRefPage(){
		$ref = $this->getParent('componentCalendar')->getRefPage();

		if(!empty($ref)){
			return $ref;
		}

		return '';
	}

	function siteMap(){
//?eventDetails='.$this->getUnique()
		echo '<url>'."\n";
		echo ' <loc>'.$this->curPageURL().str_replace('sitemap.php','',$_SERVER["PHP_SELF"]).$this->geCalendarRefPage().'?eventDetails='.$this->getUnique().'</loc>'."\n";
		echo '</url>'."\n";
	}

	function heatMap(){
		print '"'.strtotime($this->date).'":1,';
	}
}
?>
