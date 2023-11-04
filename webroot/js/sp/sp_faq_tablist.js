// SP FAQタブリスト
var tabList = $("#js-tabList .js-tabList-item");
var tabListIndex = 0;
var tabListContent = $("#js-tabList .js-tabList-content");
tabListContent.hide();
//最初だけ読み込み時に表示
$(".up").css("display", "none");
tabList.each(function (i) {
  $(this).on("click", function () {
    $("#js-tabList .js-tabList-content:eq(" + i + ")").slideToggle(200);
    $(".up:eq(" + i + ")").toggle();
    $(".down:eq(" + i + ")").toggle();
  });
});
