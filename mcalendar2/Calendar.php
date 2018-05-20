<?php
/**
 *   Medication For All Calendar source file Calendar,
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

class Calendar extends TagManager{
//data
	private $loaded = false;

	private $date;

	//The current view mode
	private $model = 'calendar';
	private $modelList = array('list','calendar','heatmap');

	private $mode = "month";
	private $modeList = array('month','year','currentweek','yearcurrent','upcoming');

	private $dateFormat = "Y-m-d H:i:s";

	private $modified;

	private $start;

	/*double nested array of events keyed by mysql datetime. Allows multiple events per day to be stored.*/
	private $eventKeyDay = array();

	private $eventKeyTime = array();

	private $detail;

	private $displayListNav = false;

	private $search = '';

	private $eventClass = 'Event';

	private $limit;

	private $customWhere="";


//constructor

/**
 *Creates the initial calendar.
 *
 *@param string $h Component Header Text.
 *@param string $m Initial display mode. 
 */
	function __construct($h="",$mode='month',$model='calendar')
	{
		parent::__construct($h,'calendar');
		$this->setMode($mode);
		$this->setModel($model);

		//now
		$this->date = date($this->dateFormat);

		//hardcoded
		//$this->date = date($this->dateFormat,strtotime('2014-02-09'));

		//I only care about data thats been updated since the component was loaded.
		$this->modified = date($this->dateFormat);
	}

//methods
/**
 *
 */
	function process(){
		//print '<br />calling calendar process';

		if($this->loaded && $this->getParent('page')->getAccount()->isEdit() == false && $this->isEdit() == false){
			$this->checkModified();
		}

		if(!empty($_REQUEST['month']) && !empty($_REQUEST['year'])){
			$this->selectMonth($_REQUEST['month'],$_REQUEST['year']);

		} else if(!empty($_REQUEST['year'])){
			$this->selectYear($_REQUEST['year']);

		}

		if(!empty($_REQUEST['previousMonth']) && strcmp($_REQUEST['previousMonth'],'true')==0){
			$this->previousMonth();
		}

		if(!empty($_REQUEST['nextMonth']) && strcmp($_REQUEST['nextMonth'],'true')==0){
			$this->nextMonth();
		}

		if(!empty($_REQUEST['previousYear']) && strcmp($_REQUEST['previousYear'],'true')==0){
			$this->previousYear();
		}

		if(!empty($_REQUEST['nextYear']) && strcmp($_REQUEST['nextYear'],'true')==0){
			$this->nextYear();
		}

		if($this->loaded == false){
			//print 'load the calendar';
			//$this->checkIfDateString();
			$this->loadEvents();
			$this->loaded = true;
		}

		if(strcmp($this->model,'calendar')==0){
			//print 'attempting to add script';
			$this->getParent('page')->script('eventDialog.js','mcalendar2/script');
		}else if(strcmp($this->model,'heatmap')==0){
			$this->getParent('page')->script('d3.min.js','mcalendar2/script');
			$this->getParent('page')->script('cal-heatmap.js','mcalendar2/script');
			$this->getParent('page')->script('heatMap.js','mcalendar2/script');
			$this->getParent('page')->addStyle('./mcalendar2/css/cal-heatmap.css');

		}
		$this->children('process');
	}


/**
 *
 */
	function selectMonth($month,$year){
		$tmpDate = strtotime('1 '.$month.' '.$year);
		$this->date = date($this->dateFormat,$tmpDate);
		$this->reload();		
	}

/**
 *
 */
	function selectYear($year){
		$tmpDate = strtotime($this->date);
		$this->date = date($this->dateFormat,mktime(0, 0, 0, 1, 1,$year));
		$this->reload();
	}


/**
 *
 */
	function previousMonth(){
		//print '<br /> current date - '.$this->date;
		$tmpDate = strtotime($this->date);
		$this->date = date($this->dateFormat,mktime(0, 0, 0, date("m",$tmpDate)-1, 1,date("Y",$tmpDate)));
		//print '<br /> previous-month - '.$this->date;
		$this->reload();
	}

/**
 *
 */
	function nextMonth(){
		//print '<br /> current date - '.$this->date;
		$tmpDate = strtotime($this->date);
		$this->date = date($this->dateFormat,mktime(0, 0, 0, date("m",$tmpDate)+1, 1,date("Y",$tmpDate)));

		//print '<br /> next-month - '.$this->date;
		$this->reload();
	}

/**
 *
 */
	function previousYear(){
		$tmpDate = strtotime($this->date);
		$this->date = date($this->dateFormat,mktime(0, 0, 0,1, 1,date("Y",$tmpDate)-1));
		$this->reload();
	}

/**
 *
 */
	function nextYear(){
		//print '<br /> current year - '.$this->date;
		$tmpDate = strtotime($this->date);
		$this->date = date($this->dateFormat,mktime(0, 0, 0, 1, 1,date("Y",$tmpDate)+1));
		//print '<br /> next year - '.$this->date;
		$this->reload();
	}

/**
 *
 */
	private function reload(){

		//here so reload isn't called uselessly
		if($this->loaded){
			//print'<br />Calling Reload';
			$this->start= null;
			$this->eventKeyDay = array();
			$this->eventKeyTime = array();
			$this->loaded = false;
			$this->detail = null;
			$this->clearChildren();
		}
	}

/**
 *
 */
	function checkModified(){

		$mysqli = $this->getParent('page')->getCOnnect()->getMysqli();
		$query = 'SELECT `modified` FROM events WHERE `modified`>? ORDER BY `modified` asc LIMIT 0 , 1';

		if($stmnt = $mysqli->prepare($query)){
			$stmnt->bind_param('s',$this->modified);
			$stmnt->execute();
			$stmnt->bind_result($modified);

			while($stmnt->fetch()){
				$this->modified = date($this->dateFormat,strtotime($modified));
				$this->setChildren(null);
				$this->loaded = false;
				//print 'updating events';
			}
		}
				
	}

/**
 *
 */
	function loadEvents(){
		$start = '';
		$end = '';

		if(strcmp($this->mode,'upcoming') ==0){
			$day = date("d",strtotime($this->date));
			$month = date("m",strtotime($this->date));
			$year = date("Y",strtotime($this->date));

			$start = $year.'-'.$month.'-'.$day.' '.'00:00:00';

			$this->start = date($this->dateFormat,strtotime($start));

		} else if(strcmp($this->mode,'yearcurrent') ==0){
			$day = date("d",strtotime($this->date));
			$month = date("m",strtotime($this->date));
			$year = date("Y",strtotime($this->date));

			$start = $year.'-'.$month.'-'.$day.' '.'00:00:00';
			$end = $year.'-'.'12'.'-'.'31'.' '.'23:59:59';
		} else if(strcmp($this->mode,'currentweek') ==0){
			//print 'attempting to get current week';

			$day = date("d",strtotime($this->date));
			$month = date("m",strtotime($this->date));
			$year = date("Y",strtotime($this->date));

			$week = mktime(0, 0, 0, date("m"),date("d")+8,date("Y"));
			$nMonth = date("m",$week);
			$nYear = date("Y",$week);
			$nDay = date("d",$week);

			$start = $year.'-'.$month.'-'.$day.' '.'00:00:00';
			$end = $nYear.'-'.$nMonth.'-'.$nDay.' '.'23:59:59';
			
			//print $start.' - '.$end;
		} else if(strcmp($this->mode,'month') ==0){
			$month = date("m",strtotime($this->date));
			$year = date("Y",strtotime($this->date));
			$count = date("t",strtotime($this->date));
			$start = $year.'-'.$month.'-'.'1'.' '.'00:00:00';
			$end = $year.'-'.$month.'-'.$count.' '.'23:59:59';
			//$end = $year.'-'.($month+1).'-'.'7'.' '.'23:59:59';

			$this->start = date($this->dateFormat,strtotime($start));
		} else if(strcmp($this->mode,'year')==0) {
			//print 'attempting to load year';
			$year = date("Y",strtotime($this->date));
			$start = $year.'-'.'1'.'-'.'1'.' '.'00:00:00';
			$end = $year.'-'.'12'.'-'.'31'.' '.'23:59:59';

			$this->start = date($this->dateFormat,strtotime($start));

			//print '<br />'.$start;
			//print '<br />'.$end;
		}

		$this->queryEvents($start,$end);
	}

/**
 *
 */
	function queryEvents($start,$end){
		$mysqli = $this->getParent('page')->getCOnnect()->getMysqli();

		$endStr = '';

		if(!empty($end)){
			$endStr = 'AND `date`<=?';
		}

		$limit='';

		if(!empty($this->limit)){
			$limit= ' limit 0,'.$this->limit;
		}

		$where = $this->queryWhere();

		$query = 'SELECT `id`,`date`,`title`,`description`,`icon`,`type`,`cancelled`,`limitedtickets`,`soldout` FROM events WHERE `status`=\'active\' AND `date`>=? '.$endStr.' '.$where.' '.$this->customWhere.' ORDER BY `date` asc '.$limit;
		//print '<br />'.$start;
		//print '<br />'.$end;
		//print '<br />'.$query;


		if($stmnt = $mysqli->prepare($query))
		{
			if(empty($end) && empty($this->search)){
				$stmnt->bind_param('s',$start);
			} else if(empty($end) && !empty($this->search)){
				$search = '%'.$this->search.'%';
				$stmnt->bind_param('sss',$start,$search,$search);
			}else if(empty($this->search)){
				$stmnt->bind_param('ss',$start, $end);
			} else if(!empty($this->search)){
				$search = '%'.$this->search.'%';
				//searching title and description
				$stmnt->bind_param('ssss',$start, $end,$search,$search);
			}
			
			$stmnt->execute();
			$stmnt->bind_result($id,$date,$title,$description,$icon,$type,$cancelled, $limited,$soldout);

			$eventsCount=0;

			while($stmnt->fetch()){
				//print '<br />found event '.$title; 

				$event = new $this->eventClass();
				$event->setEventId($id);
				$event->setTitle($title);
				$event->setDate($date);
				$event->setDescription($description);
				$event->setIcon($icon);
				$event->setEventType($type);
				$event->setCancelled($cancelled);
				$event->setSoldOut($soldout);
				$event->setLimitedTickets($limited);

				$this->add($event);

				//check the date correctly stripping hours, seconds, minutes
				$timestamp = strtotime($date);
				$modDate = date($this->dateFormat,mktime(0, 0, 0, date("m",$timestamp),date("d",$timestamp),date("Y",$timestamp)));
				if(empty($this->eventKeyDay[$modDate])){
					$this->eventKeyDay[$modDate] = array();
				}

				$this->eventKeyDay[$modDate][count($this->eventKeyDay[$modDate])] = $event;
				$this->eventKeyTime[$date] = $event;

				$eventsCount++;
			}

			//no dates found going to test if its a date string
			if($eventsCount==0){
				//print 'found no matching events';
				$this->checkIfDateString();
			}
		}
	}


/**
 *
 */
	function  checkIfDateString(){
		if($date = strtotime($this->search)){

			$this->search = "";
			$this->setSearchStringDate($date);
			$this->loadEvents();
		}
	}


/**
 *
 */
	function setSearchStringDate($timestamp){
		$this->date = date($this->dateFormat,mktime(0, 0, 0, date("m",$timestamp), 1,date("Y",$timestamp)));
	}

/**
 *
 */
	function queryWhere(){
		$returner = '';
		if(!empty($this->search)){
			$returner .= ' AND (`title` LIKE ? OR `description` LIKE ?) ';
		}
		return $returner;
	}

/**
 *
 */
	function cContent(){
		echo '<div class ="cContent">';
		//print 'this is calendar content '.$this->date;

		if(strcmp($this->model,'list')==0){
			$this->eventsList();
		}else if(strcmp($this->model,'calendar')==0){

			$this->printDetail();

			if(strcmp($this->mode,'month') ==0){ 
				$this->printMonth();	
			} else if(strcmp($this->mode,'year')==0){
				$this->printYear();
			} else if(strcmp($this->mode,'upcoming')==0){
				$this->printUpcoming();
			} 
		} else if(strcmp($this->model,'heatmap')==0){
			$this->heatMap();

		}
		echo '</div>';
	}

/**
 *
 */
	function printDetail(){
		if($this->detail){
			$this->detail->show();
		}
	}


/**
 *
 */
	function heatMap(){
		echo '<div class="heatMap">';
		//print '<br />list children '.count($this->getChildren());


		if($this->displayListNav){
			//print 'need to show list nav';
			if(strcmp($this->mode,'month')==0){
				$this->monthNav($this->start);
				
			}else if(strcmp($this->mode,'year')==0){
				$this->yearNav($this->start);
			}
		}
		//$this->children('show');

		ob_start();
		$this->children('heatMap');
		$myStr = ob_get_contents();
		ob_end_clean();

		$range="";
		$tmpDate = strtotime($this->date);
		if(strcmp($this->mode,'year')==0){

			$tmpDate = strtotime(date($this->dateFormat,mktime(0, 0, 0, 1, 1,date("Y",$tmpDate))));
			$range='"range":12,';
		}else if(strcmp($this->mode,'month')==0){
			$range='"range":1,';
		}else if(strcmp($this->mode,'upcoming')==0){
			$range='"range":'.$this->getUpcomingMonths().',';
		}

		$init = '{
"domain":"month",
"subDomain":"x_day",
"verticalOrientation":true,
"label":{"position":"top"},
"highlight": "now",
"legendVerticalPosition":"top",
"displayLegend":false,
"cellSize": 30,
"cellPadding": 5,
"domainGutter": 20,
"subDomainTextFormat": "%d",
"legend":[1, 2, 3, 4, 5],
"itemName": ["Event", "Events"],
"weekStartOnMonday":false,
'.$range.'
"start":'.$tmpDate.'
}';

		echo '<div id="cal-heatmap" data-init=\''.$init.'\' data-mapdata=\'{'.substr($myStr,0,strlen($myStr)-1).'}\'></div>';
		echo '</div>';



		
	}

/**
 *
 */
	function eventsList(){
		echo '<div class="list">';
		//print '<br />list children '.count($this->getChildren());

		if($this->displayListNav){
			//print 'need to show list nav';
			if(strcmp($this->mode,'month')==0){
				$this->monthNav($this->start);
				
			}else if(strcmp($this->mode,'year')==0){
				$this->yearNav($this->start);
			}
		}
		$this->children('show');
		echo '</div>';
	}

/**
 *
 */
	function monthNav($lDate,$navFlag=true){
		$date = strtotime($lDate);
		echo '<div class="calHeader">';
			if($navFlag){
				$previousM=date('F',mktime(0, 0, 0, date("m",$date)-1, 1,date("Y",$date)));
				$year = date('Y',mktime(0, 0, 0, date("m",$date)-1, 1,date("Y",$date)));

				//echo '<a class="calNav previous" href="?previousMonth=true&month='.$previousM.'&year='.$year.'">&lt;</a> ';
				echo '<a class="calNav previous" href="?year='.$year.'&month='.$previousM.'">&lt;</a> ';
			}

			echo '<span>'.date("F - Y",$date).'</span>';

			if($navFlag){
				$nextM=date('F',mktime(0, 0, 0, date("m",$date)+1, 1,date("Y",$date)));
				$year = date('Y',mktime(0, 0, 0, date("m",$date)+1, 1,date("Y",$date)));

				//echo ' <a class="calNav next" href="?nextMonth=true&month='.$nextM.'&year='.$year.'">&gt;</a> ';
				echo '<a class="calNav previous" href="?year='.$year.'&month='.$nextM.'">&gt;</a> ';
			}
		echo '</div>';
	}

/**
 *
 */
	function yearNav($lDate,$navFlag=true){
		$date = strtotime($lDate);
		echo '<div class="calHeader">';
			if($navFlag){
				$year = date('Y',mktime(0, 0, 0, 1, 1,date("Y",$date)-1));
				//echo '<a class="calNav previous" href="?previousYear=true">&lt;</a> ';
				echo '<a class="calNav previous" href="?year='.$year.'">&lt;</a> ';
			}

			echo '<span>'.date("Y",$date).'</span>';
			$year = date('Y',mktime(0, 0, 0, 1, 1,date("Y",$date)+1));

			if($navFlag){
				$year = date('Y',mktime(0, 0, 0, 1, 1,date("Y",$date)+1));
				//echo ' <a class="calNav next" href="?nextYear=true">&gt;</a> ';
				echo '<a class="calNav previous" href="?year='.$year.'">&gt;</a> ';
			}
		echo '</div>';
	}

/**
 *
 */
	function printMonth($lDate = null,$navFlag=true){

		if(empty($lDate)){
			$lDate = $this->start;
		}
		
		$match = false;

		//Weekday name long format.
		$wLong = array("Sunday","Monday","Tuesday","Wednesday","Thursday","Friday","Saturday");

		//timestamp
		$date = strtotime($lDate);

		//represents what to offset the end date by if were printing a month not starting from the 1st.
		$dateOffset = date("d",$date)-1;

		//represents the number of days printed so far
		$z = 0;

		echo '<div class="month">';

			$this->monthNav($lDate,$navFlag);

			echo '<table>';

			echo '<theader>';
			foreach($wLong as $weekDay){
				echo '<th class="'.$weekDay.'">'.$weekDay.'</th>';
			}
			echo '</theader>';

			echo '<tbody>';

			//weeks
			for($i=0;$i<6;$i++){

				//check to see if we should even print this week.
				if($z< date("t",$date)-$dateOffset)
				{
					echo '<tr>';

					//weekdays
					for($j=0;$j<7;$j++){
						echo '<td class="event">';
						//echo ($i*7)+$j+1;

						//print date($this->dateFormat,strtotime($this->start)).' '.date("l",$date).' '.$wLong[$j];
						if( $match == false && strcmp(date("l",$date),$wLong[$j])==0){
							//print 'foundmatching weekday';
							$match = true;
						}
			
						if($match && $z< date("t",$date)-$dateOffset){

							//print $z;

							$calendarDate = mktime(0, 0, 0, date("m",$date),date("d",$date)+$z,date("Y",$date));
							$todayClass = '';

							if($this->isToday(date($this->dateFormat,$calendarDate))){
								$todayClass = 'today';
							}
						
							echo '<div class="dHeader '.$todayClass.'">';
								echo date("j",$calendarDate);
							echo '</div>';

							$this->printEventCheck(date($this->dateFormat,$calendarDate));
							$z++; 
						}						
						echo '</td>';
					}
				echo '</tr>';
				}
			}
			echo '</tbody>';
			echo '</table>';
					
		echo '</div>';
	}

/**
 *
 */
	function printYear($navFlag=true){
		//print 'attempting tp print year';
		
		$date = strtotime($this->start);

		if($navFlag){
			$this->yearNav($this->start,$navFlag);
		}

		echo '<div class="year">';

		for($i = 0 ; $i < 12; $i++){

			$month = date($this->dateFormat,mktime(0, 0, 0, (date("m",$date)+$i), date("d",$date),date("Y",$date)));

			//print '<br />'.$month;
			$this->printMonth($month,false);
		}
		echo '</div>';
	}

/**
 *
 */
	private function getUpcomingMonths(){

		$date = strtotime($this->start);

		//start date
		$date1 = new DateTime(date("Y-m-d",strtotime($this->start)));

		//end date
		$date2 = new DateTime(date("Y-m-d",strtotime($this->getChild(count($this->getChildren())-1)->getDate())));

		$interval = $date1->diff($date2);

		return $interval->format('%m months')+1;

	}

/**
 *
 */
	function printUpcoming(){
		//print 'attempting tp print upcoming';

		$date = strtotime($this->start);

		$months = $this->getUpcomingMonths();

		//print $months;
		//print_r(count($children));
		//print $lastEvent[0].getUnique();
		//$date2 = $lastEvent.getDate();


		echo '<div class="year">';

		for($i = 0 ; $i < $months; $i++){

			if($i==0){
				$month = date($this->dateFormat,mktime(0, 0, 0, (date("m",$date)+$i), date("d",$date),date("Y",$date)));

			} else{
				$month = date($this->dateFormat,mktime(0, 0, 0, (date("m",$date)+$i), 1,date("Y",$date)));
			}



			//print '<br />'.$month;
			$this->printMonth($month,false);
		}
		echo '</div>';
	}


/**
 *
 */
	function printEventCheck($date){

		//echo 'test '.$date;
		if(!empty($this->eventKeyDay[$date])){

			foreach($this->eventKeyDay[$date] as $event){
				if(strcmp($this->model,'calendar')==0){
					$event->printCell();
				}
			}
		}
	}


/**
 *
 */
	function isToday($date){
		if(strcmp(date("Y-m-d",strtotime($date)),date("Y-m-d"))==0){
			//print 'found match';
			return true;
		}
		return false;
	}

/**
 *
 */
	function printNav(){
		print 'display nav';
	} 

/**
 *Sets the display mode.
 *
 *@param string $m Display Mode.
 */
	function setMode($m){

		if(in_array(strtolower($m),$this->modeList)) {
			$this->mode = $m;
			//print '<br />calling reload from set mode';
			$this->reload();
		}
		else {
			//throw new Exception('Not an acepted calendar mode '.$m);
		}
	}

/**
 *
 */
	function getMode(){
		return $this->mode;
	}

/**
 *
 */
	function getModeList(){
		return $this->modeList;
	}


/**
 *Sets the display model.
 *
 *@param string $m Display Model.
 */
	function setModel($m){

		if(in_array(strtolower($m),$this->modelList)){

			if(strcmp($m,'upcoming')){
				$this->date = date($this->dateFormat);
			}
			$this->model = $m;
		}
		else {
			//throw new Exception('Not an acepted calendar model '.$m);
		}
	}

/**
 *
 */
	function getModel(){
		return $this->model;
	}

/**
 *
 */
	function getModelList(){
		return $this->modelList;
	}
	

/**
 *
 */
	function setDetail($dEvent){
		if($this->detail){
			$this->detail->removeClass('dialog');
		}
		$dEvent->addClass('dialog');
		$this->detail = $dEvent;
	}

/**
 *
 */
	function setDisplayListNav($d){
		$this->displayListNav = $d;
	}

/**
 *
 */
	function setSearch($s){
		$this->search = $s;
		//print'<br />calling reload from set search';
		$this->reload();
	}

/**
 *
 */
	function getSearch(){
		return $this->search;
	}

/**
 *@todo should strip the incoming name of html ?
 */
	function short(){
		//print 'calendar short';

		if(!empty($_REQUEST['eventDetails'])){

			//set the event details object
			$event = $this->findChild($_REQUEST['eventDetails']);
			if(!empty($event)){
				$event->short();
			}

			$this->printDetail();
		}


		if(!empty($_REQUEST['eventDetailsDate'])){
			//print 'eventDetailsDate short';

			//convert timestamp to mysql date format
			$date = date($this->dateFormat,$_REQUEST['eventDetailsDate']);

			if(!empty($this->eventKeyDay[$date])){

				foreach($this->eventKeyDay[$date] as $event){
				//print 'found event in event key';
					$event->short();
				}
				$this->printDetail();
			}
		}
	}

/**
 *
 */
	function setEventClass($e){

		$this->eventClass = $e;

	}

/**
 *
 **/
	function setLimit($limit){
		$this->limit = $limit;

	}


/**
 *
 */
	function setCustomWhere($where){
		$this->customWhere = $where;
	}


/**
 *
 */
	function siteMap(){
		echo '<!--Calendar SiteMap '.$this->getUnique().'-->'."\n";
		//echo count($this->getChildren())."\n";
		$this->children('siteMap');
		
	}
}

?>
