<?php
/**
 *   Medication For All Framework SDE source file UpdateOrderTrait,
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
 *@package framework
 */

/**
 *
 *@author James M Adams <james@medicationforall.com>
 *@version 0.1
 *@package sde
 */
trait UpdateOrderTrait{

/**
 *Updates the gallery thumb images order based on ajax drag n drop events.
 */
	function updateOrder()
	{
		$returner = true;
		//print 'running update order';

		$node ="";
		$next ="";
		$prev = "";

		if(!empty($_POST['node']))
		{
			$node = explode('-', $_POST['node']);
			$node = $node[1];
			//print ' selected node is '.$node;
		}
		else
		{
			$returner = false;
		}

		if($returner)
		{
			//print 'previous is' . $_POST['prev'];
			if(!empty($_POST['prev']) && strcmp($_POST['prev'], 'undefined')!=0)
			{
				$prev = explode('-', $_POST['prev']);
				$prev = $prev[1];
				//print ' previous node is '.$prev;
			}

			if(!empty($_POST['next']) && strcmp($_POST['next'], 'undefined')!=0)
			{
				$next = explode('-', $_POST['next']);
				$next = $next[1];
				//print ' next node is '.$next;
			}
		}

		$children = $this->getChildren();

		$newChildren = array();
		$nodeAdded = false;
		$childNode;

		//find the child node
		foreach($children as $child)
		{
			$cid = $child->getId();

			if(strcmp($node, $cid)==0)
			{
				//do nothing
				$childNode = $child;
			}
		}

		if(!empty($childNode))
		{
			//build the new list
			foreach($children as $child)
			{
				$cid = $child->getId();

				if($nodeAdded == false)
				{
					if(!empty($next) && strcmp($next, $cid)==0) 
					{
						$newChildren[count($newChildren)] = $childNode;
						$nodeAdded = true;
					}
				}

				//node matches found child
				if(strcmp($node, $cid)==0)
				{
					//do nothing
				}
				else
				{
					$newChildren[count($newChildren)] = $child;
				}

				if($nodeAdded == false)
				{
					if(!empty($prev) && strcmp($prev, $cid)==0) 
					{
						$newChildren[count($newChildren)] = $childNode;
						$nodeAdded = true;
					}
				}
			}
		}
		$this->setChildren($newChildren);
	}

}

?>
