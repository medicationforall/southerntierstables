/**
 *   Medication For All Framework javascript file ComponentEmail,
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

	$(document).ready(function()
	{
		//$('div.component').corner();

		$('.email').on('click','input.eSub',function(event)
		{
			event.preventDefault();

			var par = this;
			var cEmail = $("input[name='cEmail']").val();
			var cSubject = $("input[name='cSubject']").val();
			var cMessage = $("textarea[name='cMessage']").val();
			var data = 'cEmail='+encodeURIComponent(cEmail)+'&cSubject='+encodeURIComponent(cSubject)+'&cMessage='+encodeURIComponent(cMessage)+'&email=true'+'&shortType=ComponentEmail';

			$.ajax(
			{
				type:"POST",
				url: 'short.php',
				data: data,
				success: function(msg)
				{
					$(par).closest(('div.cContent')).replaceWith(msg);
				}
			});
		});
	});
