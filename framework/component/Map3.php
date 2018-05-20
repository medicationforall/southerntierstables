<?php

/**
 *   Medication For All Framework source file Map,
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
 *   Purpose: ComponentMap key in with the google maps api allowing googlemaps to be placed with the website
 *   Note this class has 2 dependencies on outside scripts
 *
 *   Ideally I don't see why this component should have to be limited to just the google maps if other mapping software has an api they should be valid alternates
 *
 *
 *
 *   Sample Usage {@link http://www.medicationforall.com/framework/sample/SampleMapv3.php SampleMapv3}
 *
 *   {@example ../sample/SampleMapv3.php SampleMapv3}
 *
 *@todo doesn't mark initial location.
 *@author James M Adams <james@medicationforall.com>
 *@version 0.2
 *@package framework
 */
class Map3 extends Component
{
//data
	/**
	 *   Default address.
	 *@access private
	 *@var string
	 */
	private $address;

	/**
	 *   Google api version2 Key.
	 *@access private
	 *@var string
	 */
	private $key='';


//constructor
/**
 *   Creates the google componentMap object.
 *@param string $h Header text value.
 *@param string $a Address that the map directs the user to.
 *@param string $k Google Maps Api Key.
 */
	function __construct($h='Map', $a="Binghamton NY", $k="")
	{
		parent::__construct($h, 'map');
		$this->address = $a;
		$this->key=$k;

		$this->setShowPreference(true);
	}

/**
 *   Process adds scripts on load.
 *@param boolean $processChildren Process Children flag.
 */
	function process($processChildren=true)
	{
		//run component process , run core process
		parent::process();
		$settingsKey = $this->getParent('page')->getSettings()->getGoogleMapKey();

		if(empty($this->key) && !empty($settingsKey))
		{
			$this->key = '&amp;key='.$settingsKey;
		}

//print 'key '.$this->key;

		//$this->script('http://maps.google.com/maps?file=api&amp;v=2&amp;sensor=false&amp;key='.$this->key);
		$this->script('http://maps.google.com/maps/api/js?sensor=false'.$this->key);
		$this->script('componentMap3.js');

		if($processChildren)
		{
			$this->children('process');
		}
	}

/**
 *   Printed content of the map.
 */
	function cContent()
	{
		echo('<div class="cContent">');
print '<div id="side-container">
  <ul>
    <li class="dir-label">From:</li>

    <li><input id="from-input" type=text required autofocus value=""/></li>
    <br clear="both"/>
    <li class="dir-label">To:</li>
    <li><input id="to-input" type=text value="'.$this->address.'" disabled="disabled" /></li>
  </ul>
  <div>
    <select onchange="Demo.getDirections();" id="travel-mode-input">
      <option value="driving" selected="selected">By car</option>

      <option value="bicycling">Bicycling</option>
      <option value="walking">Walking</option>
    </select>
    <select onchange="Demo.getDirections();" id="unit-input">
      <option value="imperial" selected="selected">Imperial</option>
      <option value="metric">Metric</option>
    </select>

    <input onclick="Demo.getDirections();" type=button value="Go!"/>
  </div>
  <div id="dir-container"></div>
</div>
<div id="map-container"></div>';

		$this->children('show');
		echo('</div>');
	}

/**
 *   Create the XML header text
 *@return string
 */
	function xHeader()
	{
		$returner ='';

		$returner .=parent::xHeader();

		if(!empty($this->address))
		{
			$returner .= ' address="'.$this->address.'"';
		}

		if(!empty($this->key))
		{
			$returner .= ' key="'.$this->key.'"';
		}

		return $returner;
	}

/**
 *Get Address
 *@return string
 */
	function getAddress()
	{
		return $this->address;
	}

/**
 *Set Address
 *@param string $a
 */
	function setAddress($a)
	{
		$this->address = $a;
	}

/**
 *Get the google API Key
 *@return string
 */
	function getKey()
	{
		return $this->key;
	}

/**
 *Set the google API key
 *@param string $k 
 */
	function setKey($k)
	{
		$this->key = $k;

	}
}
?>
