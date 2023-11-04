// PC&SP モーダル-Float
// deferで追加
$(function () {
	$(".js-modalf_open").on("click", function(){
		var mOverlay = $(this).parent().parent().find(".js-modalf_overlay")[0];
		var mContent = $(this).parent().parent().find(".js-modalf-window")[0];
		var scrollpos = $(window).scrollTop();

		$('body').addClass('body-fixed').css({'top': -scrollpos});
		$(mOverlay).fadeIn("fast");
		$(mContent).fadeIn("fast").css("display","flex");

		$(".js-modalf_overlay, .js-modalf_close").on("click", function(){
			$("body").removeClass("body-fixed").css({"top": ""});
			window.scrollTo(0 , scrollpos);
			$(".js-modalf_close").off("click");

			$(mContent).fadeOut("fast");
			$(mOverlay).off("click").fadeOut("fast");
		});
	});
});
