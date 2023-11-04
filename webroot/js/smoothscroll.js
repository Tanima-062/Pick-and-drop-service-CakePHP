// aタグをアニメーションスクロールにする(hrefに#があるもののみ)
$('a').on('click',function(){
	var id = $(this).attr('href');
	if(id && id.substr(0,1) == "#"){
		$("html,body").animate({scrollTop:$(id).offset().top});
		return false;
	}
});