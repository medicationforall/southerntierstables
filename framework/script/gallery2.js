/**
 *   Medication For All Framework javascript file gallery2,
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

//data

//methods

/*image tagging*/

var availableTags = [
	"ActionScript",
	"AppleScript",
	"Asp",
	"BASIC",
	"C",
	"C++",
	"Clojure",
	"COBOL",
	"ColdFusion",
	"Erlang",
	"Fortran",
	"Groovy",
	"Haskell",
	"Java",
	"JavaScript",
	"Lisp",
	"Perl",
	"PHP",
	"Python",
	"Ruby",
	"Scala",
	"Scheme"
];

var consumeArrows = true;


/**
 *JQuery extended methods
 */
//http://stackoverflow.com/questions/521291/jquery-slide-left-and-show
jQuery.fn.extend({
  slideRightShow: function() {
    return this.each(function() {
        $(this).show('slide', {direction: 'right'}, 1000);
    });
  },
  slideLeftHide: function() {
    return this.each(function() {
      $(this).hide('slide', {direction: 'left'}, 1000);
    });
  },
  slideRightHide: function() {
    return this.each(function() {
      $(this).hide('slide', {direction: 'right'}, 1000);
    });
  },
  slideLeftShow: function() {
    return this.each(function() {
      $(this).show('slide', {direction: 'left'}, 1000);
    });
  },
  hasOverflow : function() {
    var $this = $(this);
    var $children = $this.find('*');
    var len = $children.length;

    if (len) {
        var maxWidth = 0;
        var maxHeight = 0
        $children.map(function(){
            maxWidth = Math.max(maxWidth, $(this).outerWidth(true));
            maxHeight = Math.max(maxHeight, $(this).outerHeight(true));
        });

	var height = $(this).height();
	var width = $(this).width();

        return maxWidth > width || maxHeight > height;
       }
    return false;
   }
});

/**
 *parses the url and returns the selected tags statement from the url.
 */
function findTag()
{
	//http://stackoverflow.com/questions/4545697/read-the-get-variables-in-url-jquery
	var urlQuery = location.search;
	urlQuery = urlQuery.replace('?', '');

	var attr = urlQuery.split('&');
	var tagString;

	for(test in attr)
	{
		if(attr[test].search('tag')!= -1)
		{
			//alert('found tag');
			tagString = attr[test];	
		}
	}

	return tagString;
}

/**
 *
 */
function split(val) 
{
	return val.split( /,\s*/ );
}

/**
 *
 */
function extractLast(term) 
{
	return split(term).pop();
}

/**
 *image tagger
 */

function bindImageTagger()
{
	// don't navigate away from the field on tab when selecting an item
	$( ".imageTagger" ).bind( "keydown", function( event ) {
		if ( event.keyCode === $.ui.keyCode.TAB && $( this ).data( "autocomplete" ).menu.active ) 
		{
			event.preventDefault();	
			var sPath = window.location.pathname;
			var sPage = sPath.substring(sPath.lastIndexOf('/') + 1);

			var data = 'page='+sPage+'&shortType=gallery2&queryTags=true';
		}
	}).autocomplete({
		minLength: 0,
		source: function( request, response ) 
		{
			// delegate back to autocomplete, but extract the last term
			response( $.ui.autocomplete.filter(
			availableTags, extractLast( request.term ) ) );
		},
		focus: function() 
		{
			// prevent value inserted on focus
			return false;
		},
		select: function( event, ui ) 
		{
			var terms = split( this.value );
			// remove the current input
			terms.pop();
			// add the selected item
			terms.push( ui.item.value );
			// add placeholder to get the comma-and-space at the end
			terms.push( "" );
			this.value = terms.join( ", " );
			return false;
		}
	});
}


/**
 *focus blue bind events
 */
function bindFocusAndBlur()
{
	$('textarea, input').focus(function(even){
		consumeArrows = false;
		$(this).closest('.info').css('opacity','1.0');
	});

	$('textarea, input').blur(function(even){
		consumeArrows = true;
		$(this).closest('.info').css('opacity','0.65');
	});
}

/**
 *
 */
function bindImageSwipe()
{
	$(".mainImage:not('.revolver')").on('swiperight',function(event){

		//alert('left swipe');
		left();
	});

	$(".mainImage:not('.revolver')").on('swipeleft',function(event){

		//alert('right swipe');
		right();
	});

}

/**
 *
 */
	function left()
	{
			$('.tip .dismiss').trigger('click');

			//clicking and loading thumbs is asynchronous so this sillyness has to be done in order to make sure that the event handler is called on the correct object.
			if($('.selectedThumb').is(':first-child'))
			{
				$('.thumbCenter div').last().trigger('click');
			}
			else
			{
				$('.selectedThumb').prev().trigger('click');
			}

			$('#triangle-left').trigger('click');
		       return false;
	}

/**
 *
 */
	function right()
	{
			$('.tip .dismiss').trigger('click');

			if($('.selectedThumb').is(':last-child'))
			{
				$('.thumbCenter div').first().trigger('click');
			}
			else
			{
				$('.selectedThumb').next().trigger('click');
			}
			$('#triangle-right').trigger('click');
		       return false;
	}

/**
 *quickThumbReplace
 */
function replaceQuickThumb(thumb)
{
	//alert('replac thumb');

	$(thumb).each(function()
	{
		var idText = $(this).find('span').text();

		//alert('thumbtext '+idText);

		var sPath = window.location.pathname;
		var sPage = sPath.substring(sPath.lastIndexOf('/') + 1);

		var data = 'page='+sPage+'&shortType=gallery2&replaceQuickThumb='+idText;
		var quickThumb = this;

		$.ajax(
		{
			type:"POST",
			url: 'short.php',
			data: data,
			success: function(msg)
			{
				//alert(msg);
				var thumb = $(msg).filter(function(){ return $(this).is('.thumb') });

				$(quickThumb).replaceWith(thumb);
			}
		});
	});
}

/**
 *
 */
function reloadGallery()
{
	//load array
	var tagString = findTag();

	if(tagString == undefined)
	{
		tagString = '';
	}
	else
	{
		tagString = '&'+tagString;
	}

	var sPath = window.location.pathname;
	var sPage = sPath.substring(sPath.lastIndexOf('/') + 1);

	var data ='shortType=gallery2&page='+encodeURIComponent(sPage)+'&reloadGallery=true'+tagString;
	//alert('reload gallery '+data);

	$.ajax(
	{
		type:"POST",
		url: 'short.php',
		data: data,
		success: function(msg)
		{
			//alert(msg);
			$('.gallery2 div.cContent').replaceWith(msg);

			//re-setup all of the on event handlers 
			initializeGallery();

			if($('.image2').hasClass('imageRotate'))
			{
				imageRotate();
			}
		}
	});		
}


/**
 *
 */
function initializeGallery()
{
	$("img.lazy").lazyload({ 
	    effect : "fadeIn"
	});

	//global data
	var tagString = findTag();

	if(tagString == undefined)
	{
		tagString = '';
	}
	else
	{
		tagString = '&'+tagString;
	}

	//load array
	var sPath = window.location.pathname;
	var sPage = sPath.substring(sPath.lastIndexOf('/') + 1);

	var data = 'page='+sPage+'&shortType=gallery2&queryTags=true';
	//alert(data);

	$.ajax(
	{
		type:"POST",
		url: 'short.php',
		data: data,
		success: function(msg)
		{
			//http://api.jquery.com/jQuery.parseJSON/
			//http://stackoverflow.com/questions/752222/jquery-with-json-array-convert-to-javascript-array
			availableTags = jQuery.parseJSON(msg);
		}
	});


	//image tagger
	bindImageTagger();

	//focus and blur
	bindFocusAndBlur();

	bindImageSwipe();


	//tip
	$('.tip').append('<a class="dismiss" href="">Close</a>');
	$('.tip .dismiss').click(function(event)
	{
		event.preventDefault();
		$(this).parent().slideUp();
	});

	//thumb click events
	$('.thumbViewer').on('click','.thumb',function(event)
	{
		event.preventDefault();

		var anchor = $(this).find('a');
		search = anchor[0].search.replace('?','');
		

		var data ='shortType=gallery2&page='+encodeURIComponent(sPage)+'&'+search;
	        var link = $(this);

		$('.page').css('cursor','wait');

		var clickThumb = $(this);
		//alert(data);

		var height =$('.image2').first().find('img, iframe').height();
		$('.image2').fadeOut(600,function()
		{
			$('.thumbViewer').css("margin-top",height+24);
			$.ajax(
			{
				type:"POST",
				url: 'short.php',
				data: data,
				success: function(msg)
				{
					//alert(msg);
					var error = $(msg).filter(function(){ return $(this).is('.error') });

					//no errors found ie session timeout
					if(error[0] == undefined)
					{
						var image = $(msg).filter(function(){ return $(this).is('.image2') });
						var selected = $(msg).filter(function(){ return $(this).is('.selectedThumb') });
						var thumb = $(msg).filter(function(){ return $(this).is('.thumb') });

						//loading animation
						$('.image2').after('<div class="loading">&nbsp;<div></div></div>');
						$('.loading').css('width',$('.loading').closest('.cContent').width());
						$('.loading').animate({width:'-='+($('.loading').width()),"left":'+='+($('.loading').width())+'px',opacity:'+=0.99'},$('.loading').width()*8 );

						$(image).imagesLoaded(function()
						{
							console.debug('hitting image loaded code');
							//alert('hitting image loaded code');
							$('.loading').remove();
							$('.image2').replaceWith(image);
							$('.selectedThumb').replaceWith($(thumb).last());
							$(clickThumb).replaceWith(selected);

							$('.page').css('cursor','auto');
							$('.thumbViewer').css("margin-top",'0px');
							bindImageTagger();
							bindFocusAndBlur();
							bindImageSwipe();

							if($('.image2').hasClass('imageRotate'))
							{
								imageRotate();
							}
						});
					}
					else
					{
						$('body').prepend($(error));
						$('.error').css({'width':'200px','text-align':'center','margin-left':'auto','margin-right':'auto'});
					}	
				}
			});
		});
	});


	//keydown events
	$(document).keydown(function(e)
	{
		//left
		if (e.keyCode == 37 && consumeArrows)
		{
			left();
		       return false;
	    	}

		//right
		if (e.keyCode == 39 && consumeArrows )
		{
			right();
		       return false;
		}
	});


	$('.gallery2').on('movestart','.mainImage', function(e) {
		// If the movestart heads off in a upwards or downwards
		// direction, prevent it so that the browser scrolls normally.
		if ((e.distX > e.distY && e.distX < -e.distY) || (e.distX < e.distY && e.distX > -e.distY)) {
			e.preventDefault();
			return;
		}
	});


	//right arrow
	$('#triangle-right').click(function(event)
	{
		event.preventDefault();
		var data ='shortType=gallery2&page='+encodeURIComponent(sPage)+'&advanceRight=true'+tagString;
		//alert(data);

		$.ajax(
		{
			type:"POST",
			url: 'short.php',
			data: data,
			success: function(msg)
			{
				//alert(msg);
				var popThumb = $('.thumbCenter .thumb').first();
				$('.thumbCenter').append(popThumb);
			}
		});

		var thumb = $('.thumb:has(img.lazy)').first();
		$(thumb).each(function()
		{
			var lazy = $(this).find('img.lazy');
		   	$(lazy).attr("src", $(lazy).attr("data-original"));
		   	$(lazy).removeAttr("data-original");
			$(lazy).removeClass("lazy");
		});
	});


	//left arrow
	$('#triangle-left').click(function(event)
	{
		event.preventDefault();
		//alert('left');
		var data ='shortType=gallery2&page='+encodeURIComponent(sPage)+'&advanceLeft=true'+tagString;
		//alert(data);

		$.ajax(
		{
			type:"POST",
			url: 'short.php',
			data: data,
			success: function(msg)
			{
				//alert(msg);
				var popThumb = $('.thumbCenter .thumb').last();
				$('.thumbCenter').prepend(popThumb);
			}
		});

		var thumb = $('.thumb:has(img.lazy)').last();
		$(thumb).each(function() 
		{
			var lazy = $(this).find('img.lazy');
	   		$(lazy).attr("src", $(lazy).attr("data-original"));
	   		$(lazy).removeAttr("data-original");
			$(lazy).removeClass("lazy");
		});
	});


	//expand minize gallery
	$('.thumbViewer').after('<a class="triangle" id="triangle-center-down" href=""></a>');
	$('.thumbViewer').after('<a class="triangle" id="triangle-center-up" href=""></a>');



	var heightIncrease = 0;
	//down arrow
	$('.gallery2').on('click','#triangle-center-down',function(event)
	{
		event.preventDefault();


		heightIncrease = ($('.thumbCenter').height());

		//alert(heightIncrease);

		/*var thumb = $('.thumb:has(img.lazy)').last();
		$(thumb).each(function() 
		{
			var lazy = $(this).find('img.lazy');
	   		$(lazy).attr("src", $(lazy).attr("data-original"));
	   		$(lazy).removeAttr("data-original");
			$(lazy).removeClass("lazy");
		});*/

		$('.thumbViewer').animate({height:'+='+(heightIncrease)},'slow');
		$('html, body').animate({scrollTop:$('body').height()}, 'slow');
		$(this).hide();
		$('#triangle-center-up').css('display','block');
	});


	//up arrow
	$('.gallery2').on('click','#triangle-center-up',function(event)
	{
		event.preventDefault();
		//alert(heightIncrease);
		$('.thumbViewer').animate({height:'-='+(heightIncrease)},'slow');
		$(this).hide();
		$('#triangle-center-down').css('display','block');
	});


	//drag and drop
	$('.thumbCenter').sortable(
	{
		placeholder: "ui-state-highlight",
		forceHelperSize: false,
		items: ".thumb",
		opacity:0.85,
		revert:true,
		tolerance:true,
		update:function(event,ui)
		{
			//http://www.tequilafish.com/2007/12/04/jquery-how-to-get-the-id-of-your-current-object/
			var node = $(ui.item).attr('id');
			var next = $(ui.item).next().attr('id');
			var prev = $(ui.item).prev().attr('id');

			var urlQuery = location.search;
			urlQuery = urlQuery.replace('?', '');

			var data ='shortType=gallery2&page='+encodeURIComponent(sPage)+'&updateOrder=true&node='+encodeURIComponent(node)+'&next='+encodeURIComponent(next)+'&prev='+encodeURIComponent(prev)+'&'+urlQuery;
			//alert(data);

			$.ajax(
			{
				type:"POST",
				url: 'short.php',
				data: data,
				success: function(msg)
				{
					//alert(msg);
					//var popThumb = $('.thumbCenter .thumb').first();
					//$('.thumbCenter').append(popThumb);
				}
			});
		} 
	});

	$('.gallery2').on('click','.rotateCClockwise',function(event){
		event.preventDefault();
		//alert('clicked counter clockwise');

			var data ='shortType=gallery2&page='+encodeURIComponent(sPage)+'&'+this.search.replace('?','');
			//alert(data);

			$.ajax(
			{
				type:"POST",
				url: 'short.php',
				data: data,
				success: function(msg)
				{
					//alert(msg);
					$('.cContent.selected').replaceWith(msg);
				}
			});
	});

	$('.gallery2').on('click','.rotateClockwise',function(event){
		event.preventDefault();
		//alert('clicked clockwise');

		var data ='shortType=gallery2&page='+encodeURIComponent(sPage)+'&'+this.search.replace('?','');

		$.ajax(
		{
			type:"POST",
			url: 'short.php',
			data: data,
			success: function(msg)
			{
				//alert(msg);

				$('.cContent.selected').replaceWith(msg);	
			}
		});
	});
}


//main
/**
 *
 */
$(document).ready(function()
{
	initializeGallery();
});


