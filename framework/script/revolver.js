/**
 *   Medication For All Framework javascript file revolver,
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


function imageRotate()
{

xprior = 0;
x = 0;

xoff = 0;
yoff = 0;



width = $('.revolver').width();
height = $('.revolver').height();

xbound = (width*6)-width;

ybound = (height*6)-height;

var clicking = false;

if($('.revolver').hasClass('degree5'))
{
ybound = (height*12)-height;
}

	$('.revolver').prepend('<div class="label" style="position:absolute;left:'+((width/2)+200)+'px;top:'+(height/2)+'px">Click &amp; Drag!</div>');

	$('.revolver').mousedown(function(e)
	{
		//http://www.redips.net/firefox/disable-image-dragging/
		e.preventDefault();
		clicking = true;

		xprior = e.pageX;
		//$('.cFooter').text('mousedown '+xprior);
		$('.revolver').find('.label').fadeOut('medium');
	});

	$(document).mouseup(function(){
	clicking = false;
	// $('.cFooter').text('mouseup');
	});

function counterClock()
//alert('counterclock');
{
	if(xoff < 0 )
	{
		xoff+= width;
	}
	else if(yoff < 0)
	{
		yoff += height;
		xoff = -xbound;
	}
	else
	{
		xoff = -xbound;
		yoff = -ybound;
	}


	//$('.revolver').css("backgroundPosition",xoff+' '+yoff);
	$('.revolver img').css("margin-left",xoff);
	$('.revolver img').css("margin-top",yoff);
}

function clockWise()
{

	if(xoff > -xbound )
	{
		xoff+= -width;
	}
	else if(yoff > -ybound)
	{
		yoff += -height;
		xoff = 0;
	}
	else
	{
		xoff = 0;
		yoff = 0;
	}
	//$('.revolver').css("backgroundPosition",xoff+' '+yoff);
	$('.revolver img').css("margin-left",xoff);
	$('.revolver img').css("margin-top",yoff);
}

    // Mouse click + moving logic here
$(document).mousemove(function(e)
{
	if(clicking)
	{
		if(e.pageX > xprior)
		{

			if(x==3)
			{
				//$('.cFooter').html(e.pageX +', increase');
				x=0;
				xprior = e.pageX;
				counterClock();
			}
			x++;
		}
		else if(e.pageX < xprior)
		{

			if(x== -3)
			{
				//$('.cFooter').html(e.pageX +', decrease');
				x=0;
				xprior = e.pageX;
				clockWise();
			}
			x--;
		}
	}
});
}

$(document).ready(function()
{
	imageRotate();
});
