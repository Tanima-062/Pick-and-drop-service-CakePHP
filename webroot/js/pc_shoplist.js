$(function () {
	// open list
	$(".js-show-more-shops").click(function(){

		if ($(this).parents(".js-shoplist-wrap").attr("data-showall") === "false") {
			$(this).parents(".js-shoplist-wrap").attr("data-showall", true);
			$(this).parents(".js-shoplist-wrap").find(".js-shoplist-item-wrap").slideDown(100);
			$(this).html("閉じる");
		} else {
			$(this).parents(".js-shoplist-wrap").attr("data-showall", false);
			$(this).parents(".js-shoplist-wrap").find(".js-shoplist-item-wrap:nth-of-type(n + 6)").slideUp(100);
			$(this).html("すべての店舗を見る");
		}

	}).parents(".js-shoplist-wrap").attr("data-showall", false);

	$(".js-shoplist-wrap .js-shoplist-item-wrap:nth-of-type(n + 6)").hide();
});
