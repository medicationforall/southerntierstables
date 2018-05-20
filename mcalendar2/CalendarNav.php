<?php

/**
 *   Medication For All Calendar source file CalendarNav,
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

class CalendarNav extends Component{

//data

private $loaded = false;

private $calendar = null;

//constructor
/**
 *Creates the initial calendar.
 *
 *@param string $h Component Header Text.
 *@param string $m Initial display mode. 
 */
	function __construct($h="",$type = 'calendarNav')
	{
		parent::__construct($h,$type);
	}

//methods

/**
 *
 */
	function process(){
		if($this->loaded==false){
			//print 'load calendar nav';
			$array = $this->getParent('page')->findChildByName('componentCalendar');
			$this->calendar = $array[0];
			//$calendar->getName();


			if(!empty($this->calendar)){
				//print '<br />found calendar';
			}	
		}

		if(!empty($_REQUEST['model'])){
			//print 'found model';
			if(!empty($this->calendar)){
				$this->calendar->setModel($_REQUEST['model']);
			}
		}

		if(!empty($_REQUEST['mode'])){
			//print 'found model';
			if(!empty($this->calendar)){
				$this->calendar->setMode($_REQUEST['mode']);
			}
		}

		if(isset($_REQUEST['search'])){
			if(!empty($this->calendar)){
				$this->calendar->setsearch($_REQUEST['search']);
			}
		}

		//set list display nav to true
		//implying that by having a calendar nav that it's ok to display a list nav
		if(strcmp($this->calendar->getModel(),'list')==0 || strcmp($this->calendar->getModel(),'heatmap')==0){
			//print 'set list display nav';
			$this->calendar->setDisplayListNav(true);
		}
	}

/**
 *
 */
	function cContent() {
		echo '<div class="cContent">';

		echo '<form method="GET">';
		$this->printModel();
		$this->printMode();

		$this->printSearch();
		
		//print 'display component calendar nav';

		echo '<input class="applyButton" type="submit" value="Apply" />';
		echo '</form>';
		echo '</div>';
	}

/**
 *
 */
	function printSearch(){
		
		echo ' Search <input class="search" name="search" type="text" value="'.$this->calendar->getSearch().'" />';
	}


/**
 *
 */
	function printMode(){
		//echo 'Display As';
		echo '<select name="mode">';
		$modeList = $this->calendar->getModeList();
		$mode = $this->calendar->getMode();	

		foreach($modeList as $list){

			if(strcmp($list,'currentweek')==0 || strcmp($list,'yearcurrent')==0){

			}else{

				$selected = "";
				if(strcmp($list, $mode)==0){
					$selected = 'selected="selected"';
				}
				echo '<option value="'.$list.'" '.$selected.'>'.ucFirst($list).'</option>';

			}
		}
		echo '</select>';
	}

/**
 *
 */
	function printModel(){
		//echo 'Display As';
		echo '<select name="model">';
		$modelList = $this->calendar->getModelList();
		$model = $this->calendar->getModel();	

		foreach($modelList as $list){
			$selected = "";
			if(strcmp($list, $model)==0){
				$selected = 'selected="selected"';
			}
			echo '<option value="'.$list.'" '.$selected.'>'.ucFirst($list).'</option>';
		}
		echo '</select>';
	}

/**
 *
 */
	function cFooter(){

	}

}

?>
