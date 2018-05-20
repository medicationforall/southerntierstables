
$(document).ready(function(){
//alert('loaded event dialog');

$('.calendar').on('click','a.eventDetails',function(event){
	event.preventDefault();
	//alert('clicked on event');

	var data ='shortType=calendar&'+this.search.replace('?','');

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
				//remove existing dialogs
				//addMemberDIalog(msg);
				//editNode =undefined;
				//setupDialog();					
			}
		});


});

});
