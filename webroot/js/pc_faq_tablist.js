// PC FAQタブリスト
var tabList = $("#js-tabList .js-tabList-item");
var tabListIndex = 0;
var tabListContent = $("#js-tabList .js-tabList-content");
tabListContent.hide();
//最初だけ読み込み時に表示
tabList.first().addClass("is-current");
tabListContent.first().fadeIn();

tabList.on("click", function () {
  if ($(this).hasClass("is-current")) {
    return false;
  } else {
    tabListIndex = $(this).index();
    tabList.removeClass("is-current");
    tabListContent.hide();
    $(this).addClass("is-current");
    tabListContent.eq(tabListIndex).fadeIn();
    return false;
  }
});
