$(function()
{
	$(".kinogallery-pic img").css("opacity", pic_opacity);
	$(".kinogallery-pic:not(.current) img").hover(function(){

		$(this).fadeTo(pic_fade, 1);

	},function(){

		$(this).fadeTo(pic_fade, pic_opacity);

	});

});