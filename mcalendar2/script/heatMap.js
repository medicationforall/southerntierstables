
var mapData = jQuery.parseJSON($('#cal-heatmap').attr("data-mapdata"));
var initData = jQuery.parseJSON($('#cal-heatmap').attr("data-init"));
initData.data=mapData;

initData.onClick = function(date,nb){
//alert(date+" "+nb);
var data ='shortType=calendar&'+"eventDetailsDate="+(date.getTime()/1000);

//alert(data);


	$.ajax(
	{
		type:"POST",
		url: 'short.php',
		data: data,
		success: function(msg)
		{
			//alert(msg);

			//remove existing dialogs
			$('.calendar > .cContent .event.dialog').remove();

			$('.calendar > .cContent').prepend(msg);
			setupDialog();					
		}
	});

}

//http://stackoverflow.com/questions/847185/convert-a-unix-timestamp-to-time-in-javascript
initData.start=new Date(initData.start*1000);

console.debug(initData);

var cal = new CalHeatMap(initData);
cal.init(initData);

//alert(mapData);
