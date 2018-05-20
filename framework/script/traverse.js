/**
 *   Medication For All Framework javascript file traverse,
 *   Copyright (C) 2012  James M Adams
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
 */
function traverse()
{
	var sPath = window.location.pathname;
	var sPage = sPath.substring(sPath.lastIndexOf('/') + 1);

	var data = 'page='+sPage+'&shortType=gallery2&traverseCheck=true';

	var returner = false;

	//alert(data);

	$.ajax(
	{
		type:"POST",
		url: 'short.php',
		data: data,
		success: function(msg)
		{
//http://stackoverflow.com/questions/1789945/javascript-string-contains
			//alert(msg);
 //Maximum execution time
			if(msg.indexOf("Maximum execution time") != -1)
			{
				//alert('time to run again');
				//
				getLog();

				$('.traverse .errors').html(msg);
				traverse();
			}
			else
			{
				getLog();
				$('.traverse .errors').html(msg);

				$('.traverse .loadIcon').after('<div>Done !</div>');
				$('.traverse .loadIcon').remove();

				//being drawn from global scope of gallery2.js
				reloadGallery();				
			}
		}
	});

	return returner;
}

/**
 *
 */
function getLog()
{

	var sPath = window.location.pathname;
	var sPage = sPath.substring(sPath.lastIndexOf('/') + 1);

	var data = 'page='+sPage+'&shortType=gallery2&getLog=true';

	var returner = false;

	//alert(data);

	$.ajax(
	{
		type:"POST",
		url: 'short.php',
		data: data,
		success: function(msg)
		{
			//alert(msg);
			$('.traverse .log').html(msg);
		}
	});		
}

$(document).ready(function()
{
	//alert('this is a test');

	traverse();


});


