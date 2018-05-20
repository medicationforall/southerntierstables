
var sPath = window.location.pathname;
var sPage = sPath.substring(sPath.lastIndexOf('/') + 1);

$(document).ready(function(){

$('.photos').on('click','.thumb',function(event){
	event.preventDefault();


	var anchor = $(this).find('a');

	if(anchor){
		var search = anchor[0].search.replace('?','');
		var data ='shortType=gallery2&page='+encodeURIComponent(sPage)+'&'+search;

		//alert("clicked thumbnail "+data);

		$.ajax({
				type:"POST",
				url: 'short.php',
				data: data,
				success: function(msg){
					//alert(msg);

					var error = $(msg).filter(function(){ return $(this).is('.error') });

					//no errors found ie session timeout
					if(error[0] == undefined){
						var image = $(msg).filter(function(){ return $(this).is('.image2') });

						$(image).addClass('dialog');
						$('.image2').remove();
						$('.photos').append(image);
						$('.image2 .info').remove();
						setupDialog();
					} else{
						$('body').prepend($(error));
						$('.error').css({'width':'200px','text-align':'center','margin-left':'auto','margin-right':'auto'});
					}
				}
			});
	}
});

});


