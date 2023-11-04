$(function(){
	$('.common_accordion .js-accordion-inner').hide();
	$('.common_accordion .js-accordion-header').click(function(){
		$('+ul.js-accordion-inner',this).slideUp();
		$(this).removeClass('chane_arrow').addClass('prefectures_title');
		if($('+ul.js-accordion-inner',this).css('display') == 'none'){
			$('+ul.js-accordion-inner',this).slideDown();
			$(this).addClass('chane_arrow').removeClass('prefectures_title');
		}
	});
});