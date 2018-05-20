$(document).ready(function(){
//alert('Hi Nicki');
$('a.expand').click(function(event){
event.preventDefault();
//alert('expand click');
$(this).closest('.hiddenInfo').find('.hidden').toggle();


});
});
