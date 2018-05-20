<?php
/**
 *   Medication For All Framework source file Gallery,
 *   Copyright (C) 2015  James M Adams
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

/*
 *Subset view of a gallery.
 *
 *@author James M Adams <james@medicationforall.com>
 *@version 0.1
 *@package framework 
 */

class GalleryView extends Component{

//data
private $tag;

private $gallery;

private $galCount= 10;
//consturctor

	function __construct( $head='', $type='galleryView'){
		parent::__construct($head,$type);
	}


//methods

	function process(){

		if(!empty($this->tag)){
			$_REQUEST['tag'] = $this->tag;
			//print 'processing for '.$this->tag;
			$this->getParent('page')->script('galleryView.js');
			$this->gallery = $this->getParent('page')->getApp('gallery2');

			$this->gallery->process();			
		}

	}

	function cContent(){
		echo '<div class="cContent">';

		if(!empty($this->tag)){
		print '<a href="gallery.php?tag='.urlEncode(trim($this->tag)).'">'.' Photos</a>';
		echo '<div class="photos">';
			$children = $this->gallery->getChildren();
			$count = count($children);

			for($i = 0; $i < $count && $i < $this->galCount;$i++){

				$children[$i]->showThumb();

			}
		echo '</div>';
		}

		echo '</div>';
	}

	function setTag($tag){
		$this->tag = $tag;
	}

	function setGalCount($galCount){
		$this->galCount=$galCount;
	}
}
?>
