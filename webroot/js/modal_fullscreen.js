// SP モーダル- Fullscreen
$(function () {
	$(".js-modalfs_open").on("click", function(){
		var mContent = $(this).parent().parent().find(".js-modalfs-window")[0];
		var scrollpos = $(window).scrollTop();

		$('body').addClass('body-fixed').css({'top': -scrollpos});
		$(mContent).fadeIn("fast").css("display","flex");

		$(".js-modalfs_close").on("click", function(){
			$("body").removeClass("body-fixed").css({"top": ""});
			window.scrollTo(0 , scrollpos);
			$(".js-modalfs_close").off("click");

			$(mContent).fadeOut("fast");
		});
	});
});
