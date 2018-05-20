/**
 *   Medication For All Framework javascript file dialog,
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


function setupDialog()
{
	$('div.dialog').each(

	function(index){

		var dLeft = $(this).position().left;
		var dTop = $(this).position().top;

		if($(this).is( ':in-viewport' ) == false){
			dTop = $(document).scrollTop()+20;
			//$('body').prepend('it was not in the viewport');
		}else{
			//$('body').prepend('it\'s in the viewport');
		}

		dLeft += (index*40);
		dTop += (index*40);

		$(this).offset({ top: dTop, left: dLeft });
		//alert('dialog'+$(this).position().left);
	});

	$('div.dialog').draggable({
	stack: 'div.dialog',
	cancel : ".dialog .cContent",
	stop: function(event,ui)
		{
			//alert('drag stop');

			var sPath = window.location.pathname;
			var sPage = sPath.substring(sPath.lastIndexOf('/') + 1);

			var top = ui.position['top'];

			var left = ui.position['left'];

			//alert('position '+ui.position['top']+' '+ui.position['left']);
			var data = 'dTopPos='+encodeURIComponent(top)+'&dLeftPos='+encodeURIComponent(left)+'&shortType=Core';

			//alert(data);

			$.ajax(
			{
				type:"POST",
				url: 'short.php',
				data: data,
				success: function(msg)
				{
					//alert(msg);
				}
			});
		}
	});

	//need a means to not apply two close buttons.
	$('a.dClose').remove();
	$('div.dialog > div.cHeader').prepend('<a class="close dClose" href="" title="Close">&nbsp;</a></div>');

	$('.page').on('click','.dClose',function(event){
		event.preventDefault();
		$(this).parents('div.dialog').hide();
	});

	//deprecated
	/*$('.dClose').live('click',function(event)
	{
		event.preventDefault();
		$(this).parents('div.dialog').hide();
	});*/
}


$(document).ready(function()
{
	setupDialog();
});
