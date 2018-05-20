/**
 *   Medication For All Framework javascript file tagFilter,
 *   Copyright (C) 2014  James M Adams
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


function bindTagFilter()
{
	// don't navigate away from the field on tab when selecting an item
	$( ".tagFilter .list" ).bind( "keydown", function( event ) {
		if ( event.keyCode === $.ui.keyCode.TAB && $( this ).data( "autocomplete" ).menu.active ) 
		{
			event.preventDefault();	
			var sPath = window.location.pathname;
			var sPage = sPath.substring(sPath.lastIndexOf('/') + 1);

			var data = 'page='+sPage+'&shortType=tagFilter&queryTags=true';
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

$(document).ready(function()
{
	bindTagFilter();
});
