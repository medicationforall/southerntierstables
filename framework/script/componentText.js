/**
 *   Medication For All Framework javascript file ComponentText,
 *   Copyright (C) 2012-2013  James M Adams
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

$.fn.selectRange = function(start, end) {
        return this.each(function() {
                if(this.setSelectionRange) {
                        this.focus();
                        this.setSelectionRange(start, end);
                } else if(this.createTextRange) {
                        var range = this.createTextRange();
                        range.collapse(true);
                        range.moveEnd('character', end);
                        range.moveStart('character', start);
                        range.select();
                }
        });
};



$(document).ready(function()
{

$('div.text form.edit textarea').before('<div class="textButtons"> <a href="" title="Header" class="setHead">&nbsp;</a> <a href="" title="Paragraph" class="setParagraph">&nbsp;</a> <a href="" title="Bold" class="setBold">&nbsp;</a> <a href="" title="Italacize" class="setEm">&nbsp;</a> <a href="" title="Link" class="setLink">&nbsp;</a> <a href="" title="Image" class="setImage">&nbsp;</a> <a href="" title="Ordered List" class="setOl">&nbsp;</a> <a href="" title="Unordered List" class="setUl">&nbsp;</a> <a href="" title="List Item" class="setLi">&nbsp;</a></div>');


$('.text').on('click','a.setParagraph',function(event){
	event.preventDefault();
	wrapText($(this).parents('form').find('textarea'),"<p>","</p>");
});

$('.text').on('click','a.setBold',function(event){
	event.preventDefault();
	wrapText($(this).parents('form').find('textarea'),'<b>','</b>');
});

$('.text').on('click','a.setEm',function(event){
	event.preventDefault();
	wrapText($(this).parents('form').find('textarea'),'<em>','</em>');
});

$('.text').on('click','a.setLink',function(event){
	event.preventDefault();
	wrapText($(this).parents('form').find('textarea'),'<a href="">','</a>');
});

$('.text').on('click','a.setImage',function(event){
	event.preventDefault();
	wrapText($(this).parents('form').find('textarea'),'<img src="" alt="" />','');
});

$('.text').on('click','a.setHead',function(event){
	event.preventDefault();
	wrapText($(this).parents('form').find('textarea'),'<h1>','</h1>');
});

$('.text').on('click','a.setOl',function(event){
	event.preventDefault();
	wrapText($(this).parents('form').find('textarea'),"<ol>","</ol>");
});

$('.text').on('click','a.setUl',function(event){
	event.preventDefault();
	wrapText($(this).parents('form').find('textarea'),"<ul>","</ul>");
});

$('.text').on('click','a.setLi',function(event){
	event.preventDefault();
	wrapText($(this).parents('form').find('textarea'),'<li>','</li>');
});


$('.version input').click(function(event)
{
	$(this).closest('.text').find('textarea').val('test');
});


$('input.textEditSubmit').on('click',function(event)
{
	event.preventDefault();

	var parent = $(this).parents('.text')
	var type = parent.find('input[name=etype]').val();
	var head = parent.find('input[name=eHead]').val();
	var text = parent.find('textarea[name=eText]').val();

	var sPath = window.location.pathname;
	var sPage = sPath.substring(sPath.lastIndexOf('/') + 1);

	var data = 'etype='+encodeURIComponent(type)+'&edit=true&eHead='+encodeURIComponent(head)+'&eText='+encodeURIComponent(text)+'&shortType=ComponentText&page='+encodeURIComponent(sPage);

	$('.page').css('cursor','wait');

	//alert(data);
	$.ajax(
	{
		type:"POST",
		url: 'short.php',
		data: data,
		success: function(msg)
		{
			$('.page').find('div.error').remove();
			$('.page').find('div.confirm').remove();
			parent.after(msg)

			//alert(msg);

			var next = parent.next() 
			next.find('form.edit textarea').before('<div class="textButtons"> <a href="" title="Header" class="setHead">&nbsp;</a> <a href="" title="Paragraph" class="setParagraph">&nbsp;</a> <a href="" title="Bold" class="setBold">&nbsp;</a> <a href="" title="Italacize" class="setEm">&nbsp;</a> <a href="" title="Link" class="setLink">&nbsp;</a> <a href="" title="Image" class="setImage">&nbsp;</a> <a href="" title="Ordered List" class="setOl">&nbsp;</a> <a href="" title="Unordered List" class="setUl">&nbsp;</a> <a href="" title="List Item" class="setLi">&nbsp;</a></div>');
			parent.remove();

			if($(next).parents('.xml'))
			{
				$(next).addClass('ui-sortable');
			}

			$('.page').css('cursor','auto');

			next.find('div.confirm').hide().slideDown('slow');
		}
	});
	
});

});

function wrapText(elementID, openTag, closeTag)
{
	var textArea = elementID;
	var len = textArea.val().length;
	var start = textArea[0].selectionStart;
	var end = textArea[0].selectionEnd;
	var selectedText = textArea.val().substring(start, end);
	var replacement = openTag + selectedText + closeTag;
	textArea.val(textArea.val().substring(0, start) + replacement + textArea.val().substring(end, len));


	textArea.focus();

	textArea[0].setSelectionRange(start+openTag.length,start+openTag.length);
}








