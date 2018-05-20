/**
 *   Medication For All Framework javascript file menu,
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
			//alert('menu test');
			$('div.menu').not('.noHover').hover(function()
			{
				$(this).find('div.subMenu').eq(0).show();
			},function()
			{
				//alert('hover off');
				$(this).find('div.subMenu').slideUp('medium');
			});

			$('div.subMenu a').hover(function()
			{
				//alert('hover menu');
				$(this).next('div.subMenu').show();
				//$(this).find('div.subMenu').eq(0).show();
			});

			/*,function()
			{
				//alert('hover off');
				$(this).next('div.subMenu').hide();
				//$(this).find('div.subMenu').hide();
			});*/


		});
