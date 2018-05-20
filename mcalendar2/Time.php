<?php
/**
 *   Medication For All Calendar source file Time,
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

/**
 *Stores the current calendars time state.
 *
 *@todo needs to be refactored since the purpose of this class has changed since it's initial concept.
 */
class Time extends Core
{
//data
	//
	private $value;

	//Weekday name long format.
	private $wLong = array("Sunday","Monday","Tuesday","Wednesday","Thursday","Friday","Saturday");

	//Weekday name short format.
	private $wShort = array("Sun","Mon","Tue","Wed","Thu","Fri","Sat");

	//Month names long format.
	private $mLong = array(1=>"January",2=>"February",3=>"March",4=>"April",5=>"May",6=>"June",7=>"July",8=>"August",9=>"September",10=>"October",11=>"November",12=>"December");

	//Month names short format.
	private $mShort = array(1=>"Jan",2=>"Feb",3=>"Mar",4=>"Apr",5=>"May",6=>"Jun",7=>"Jul",8=>"Aug",9=>"Sep",10=>"Oct",11=>"Nov",12=>"Dec");

	//private $days = array();

	//private $week;


	//traditional
	private $hour;

	private $day;

	private $month;

	private $year;

	private $decade;

	private $century;


	//end range
	/*private static $ehour;

	private static $eday;

	private static $emonth;

	private static $eyear;

	private static $edecade;

	private static $ecentury;*/


	private $interval = array();

	private $offset = array();


	

//constructor
/**
 *Creates the initial time object and sets it's state to the current date and time.
 */	
	function __construct($n='time',$v=0)
	{
		parent::__construct($n);

		$this->hour= (date('H')+0);
		$this->day =  (date('d')+0);
		$this->month = (date('m')+0);
		$this->year = date('Y');
		$this->setDecade(date('Y'));

		$this->setCentury(date('Y'));

		$this->value = $v;
	}

//methodd
/**
 *Processes state change.
 */
	function process()
	{
		//traditional values
		if((!empty($_REQUEST['century'])))
		{
			$this->setCentury($_REQUEST['century']);
		}
		if((!empty($_REQUEST['decade'])))
		{
			$this->setDecade($_REQUEST['decade']);
		}

		if((!empty($_REQUEST['year'])))
		{
			$this->setYear($_REQUEST['year']);
		}

		if(!empty($_REQUEST['month']))//because 0 is an accepted value
		{
			$this->setMonth($_REQUEST['month']);
		}

		if(!empty($_REQUEST['day']))
		{
			$this->setDay($_REQUEST['day']);
		}

		if(!empty($_REQUEST['hour']))
		{
			$this->setHour($_REQUEST['hour']);
		}

		//interval
		if(!empty($_REQUEST['interval']))
		{
			$interval = $_REQUEST['interval'];

			foreach ($interval as $k => $v)
			{
				$this->setInterval($k,$v);
			}
		}


		$this->children('process');
	}


/**
 *Sets the century based on a given year.
 *
 *@param int $y Year to set century by. 
 */
	function setCentury($y)
	{
		$this->century = substr($y,0,2).'00';
	}

/**
 *Sets the decade based on a given year.
 *
 *@param int $y Year to set decade by.
 */
	function setDecade($y)
	{
		$this->decade = substr($y,0,3).'0';
	}

/**
 *Sets the year. Setting year equal to "all" has special meaning and results in year being set to null.
 *
 *@param int $y Year.
 */
	function setYear($y)
	{
		if(strcmp($y,'all')==0)
		{
			$this->year=null;
			$this->interval['year']=1;
			$this->offset['year']=0;
		}
		else
		{
			$this->year = $y;
			$this->setDecade($y);
			$this->setCentury($y);
		}
	}

/**
 *Sets the month. Settings a month less than 1, or greater than 12, will also set the year repectively; as part of an overflow bounds check. Setting month equal to "all" has special meaning and results in month being set to null.
 *
 *@param int $m Month 1-12.
 */
	function setMonth($m)
	{
		$year=$this->year;
		$month = $m;

		if(strcmp($m,'all')==0)//select all days
		{
			$this->month=null;
			$this->interval['month']=1;
			$this->offset['month']=0;
		}
		else
		{
			while($month > 12)
			{
				$month = $month - 12;
				$year++;
			}

			while($month < 1)
			{
				$month = $month + 12;
				$year--;
			}

			$this->month=$month;
			if($this->year)
			{
				$this->setYear($year);
			}
		}
	}

/**
 *Sets the hour and day; if given hour is outside of bounds. Setting hour equal to "all" has special meaning and results in hour being set to null.
 *
 *@param int $h Hour 1-24 
 */
	function setHour($h)
	{
		$day = $this->day;
		$hour = $h;

		if(strcmp($h,'all')==0)//select all days
		{
			$this->hour=null;
			$this->interval['hour']=1;
			$this->offset['hour']=0;
		}
		else
		{
			while($hour > 24)
			{
				$hour = $hour - 24;
				$day++;
			}

			while($hour < 1)
			{
				$hour = $hour + 24;
				$day--;
			}

			$this->hour=$hour;

			if($this->day)
			{
				$this->setDay($day);
			}
		}

	}

/**
 *Sets the day and month; if day is outside of current months bounds. Setting day equal to "all" has special meaning and results in day being set to null.
 *
 *@param int $day 1 - end of month.
 */
	function setDay($d)
	{
		$day=$this->day;
		$month=$this->month;
		$year=$this->year;

		if(strcmp($d,'all')==0)//select all days
		{
			$this->day=null;
			$this->interval['day']=1;
			$this->offset['day']=0;
		}
		else
		{
			if($d>$this->daysInMonth($month,$year))
			{
				while($d>$this->daysInMonth($month,$year))
				{
					$d = $d - $this->daysInMonth($month,$year);
					if($this->month)
					{
						$this->setMonth($month+1);
					}
					$day = $d;
				}
			}
			else if($d<1)
			{
				while($d<1)
				{
					//print 'month is '.$month.' '.$this->daysInMonth($month,$year).' month will be '.($month -1).' '.$this->daysInMonth($month-1,$year);
					if($this->month)
					{
						$this->setMonth($month-1);
					}
					$d = $d + $this->daysInMonth($month-1,$year);
					//print 'set month to '.($month-1);
					$day = $d;
				}
			}
			else
			{
				$day =$d;
			}
			$this->day=$day;
		}
	}

/**
 *Gets the count of in a given month, takes into account leap years.
 *
 *@param int $m Month.
 *@param int $y Year.
 */
	function daysInMonth($m,$y)
	{
		$m--;
		$returner=0;

		if($m == 0)//jan
		{
			$returner = 31;
		}
		else if($m == 1)//feb
		{
			if($this->isLeapYear($y))
			{
				$returner = 29;
			}
			else
			{
				$returner = 28;
			}
		}
		else if($m == 2)//march
		{
			$returner = 31;
		}
		else if($m == 3)//april
		{
			$returner = 30;
		}
		else if($m == 4)//may
		{
			$returner = 31;
		}
		else if($m == 5)//june
		{
			$returner = 30;
		}
		else if($m == 6)//july
		{
			$returner = 31;
		}
		else if($m == 7)//august
		{
			$returner = 31;
		}
		else if($m == 8)//sept
		{
			$returner = 30;
		}
		else if($m == 9)//oct
		{
			$returner = 31;
		}
		else if($m == 10)//nov
		{
			$returner = 30;
		}
		else if($m == 11)//dec
		{
			$returner = 31;
		}

		return $returner;
	}

/**
 *Returns a boolean based on if the given year is a leap year. Takes into account century leap years.
 *
 *@param int $y Year.
 *@return boolean True is a leap year.
 */
	function isLeapYear($y)
	{
		$returner = false;

		if(($y % 4) == 0)
		{
			$returner = true;
		}			

		if(($y % 100) == 0)//century year
		{
			if(($y % 400) == 0)
			{
				$returner = true;
			}
			else
			{
				$returner = false;
			}
		}		
		return $returner;
	}

/**
 *Gets the hour.
 *
 *@return int Hour.
 */
	function getHour()
	{
		return $this->hour;
	}
/**
 *Gets the month
 *
 *@return int Month.
 */
	function getMonth()
	{
		return $this->month;
	}

/**
 *Gets the decade.
 *
 *@return Decade.
 */
	function getDecade()
	{
		return $this->decade;
	}
/**
 *Gets the century.
 *
 *@return Century.
 */
	function getCentury()
	{
		return $this->century;
	}

/**
 *Gets the Year.
 *
 *@return Year. 
 */
	function getYear()
	{
		return $this->year;
	}

/**
 *Gets the day.
 *
 *@return Day.
 */
	function getDay()
	{
		return $this->day;
	}


/**
 *Converts the given date to an int representing day of week 0-6; Sun-Sat.
 *
 *@param int $d Day.
 *@param int $m Month.
 *@param int $y Year.
 * 
 *@return int Day of week integer.
 */
	function dayOfWeek($d,$m,$y)
	{
		$returner = 0;

		$yearStart = (integer)($y / 100);

		$yearEnd = (integer)($y % 100);
		$c = (integer)(2*(3 - (($yearStart) % 4)));

		$start = (integer)($yearEnd/4);

		$mt = (integer)($this->monthTable($m-1,$y));

		$total = (integer)($c+$yearEnd+$start+$mt+$d);

		$returner = (integer)($total % 7);

		/*System.out.println("1:Look up the "+yearStart+"00s in the centuries table: "+c);
		System.out.println("2:Note the last two digits of the year: "+yearEnd);
		System.out.println("3:Divide the "+yearEnd+" by 4: "+yearEnd+"/4 = "+start);
		System.out.println("4:Look up "+months[m]+" in the months table: "+mt);
		System.out.println("5:Add all numbers from steps 1-4 to the day of the month (in this case, "+d+"): "+c+"+"+yearEnd+"+"+start+"+"+mt+"+"+d+"="+total+".");
		System.out.println("6:Divide the sum from step 5 by 7 and find the remainder: "+total+"%7="+(total % 7));
		System.out.println("7:Find the remainder in the days table: "+returner+"="+days[returner]+".");*/

		return $returner;
	}

/**
 *Lookup helper function.
 *@param int $m Month.
 *@param int $y Year.
 */
	function monthTable($m,$y)
	{
		$returner = 0;

		if($m==0)//jan
		{
			if($this->isLeapYear($y))
			{
				$returner = 6;
			}
			else
			{
				$returner = 0;
			}
		}
		else if($m==1)//febr
		{
			if($this->isLeapYear($y))
			{
				$returner = 2;
			}
			else
			{
				$returner = 3;
			}
		}
		else if($m==2)//mar
		{
			$returner = 3;
		}
		else if($m==3)//april
		{
			$returner = 6;
		}
		else if($m==4)//may
		{
			$returner = 1;
		}
		else if($m==5)//jun
		{
			$returner = 4;
		}
		else if($m==6)//july
		{
			$returner = 6;
		}
		else if($m==7)//aug
		{
			$returner = 2;
		}
		else if($m==8)//sept
		{
			$returner = 5;
		}
		else if($m==9)//oct
		{
			$returner = 0;
		}
		else if($m==10)//nov
		{
			$returner = 3;
		}
		else if($m==11)//dec
		{
			$returner = 5;
		}

		return $returner;
	}

/**
 *Prints the short version of the days of the week.
 */
	function monthHeader()
	{
		$count = count($this->wShort);

		//print $count;
		//print_r($this->wShort);

		echo '<div class="subHeader">';

		for($i=0;$i<$count;$i++)
		{
			echo '<div>'.$this->wShort[$i].'</div>';
		}
		echo '</div>';
	}

/**
 *Gets the weekday short array.
 *
 *@return array Weekday short format.
 */
	function getWShort()
	{
		return $this->wShort;
	}
/**
 *Gets the weekday long array.
 *
 *@return array Weekday long format.
 */
	function getWLong()
	{
		return $this->wLong;
	}

/**
 *Get month long array.
 *
 *@return array Month long format.
 */
	function getMLong()
	{
		return $this->mLong;
	}

/**
 *Get month short array.
 *
 *@return array Month short format.
 */
	function getMShort()
	{
		return $this->mShort;
	}

/**
 *
 */
	/*function getEvery()
	{
		return $this->every;
	}*/
/**
 *
 */
	/*function setEvery($e)
	{
		$this->every = $e; 
	}*/
/**
 *Gets the interval for the particular mode.
 *
 *@param string $k Key value of the mode.
 *@return int Interval.
 */
	function getInterval($k)
	{
		//print_r($this->interval);
		if(!empty($this->interval[$k])){
			return $this->interval[$k];
		}
	}

/**
 *Gets the offset for the particular mode.
 *
 *@param string $k Key value of the mode.
 *@return int Offset.
 */
	function getOffset($k)
	{
		return $this->offset[$k];
	}

/**
 *Sets the interval for the particular mode.
 *
 *@param string $k Key value of the mode.
 *@param int $v Interval.
 */
	function setInterval($k,$v)
	{
		//print 'set interval for '.$k.' '.$v;
		$this->interval[$k] = $v;
	}

/**
 *Sets the offset for the particular mode.
 *
 *@param string $k Key value of the mode.
 *@param int $v Offset.
 */
	function setOffset($k,$v)
	{
		$this->offset[$k] = $v;
	}

	function getDaysInMonth(){
		return daysInMonth($this->month,$this->year);
	}

/**
 *Sets the display mode.
 *
 *@param string $m Display Mode.
 */
	function setMode($m){

		if(in_array(strtolower($m),$this->modeList)){
			$this->mode = $m;
		}
		else
		{
			throw new Exception('Not an acepted calendar mode '.$m);
		}
	}
}
?>
