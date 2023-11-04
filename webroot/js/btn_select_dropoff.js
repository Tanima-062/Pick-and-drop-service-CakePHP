$(function () {
	// 駅から乗り捨てで検索するボタン-station2.jsから分離
	$("#js-btn-select-dropoff").on("click", function () {
		// 出発店舗へ返却にチェックが入っていたら、チェックを外す
		if ($("#return_way_check").is(":checked")) {
			$("#return_way_check").prop("checked", false).trigger("change");
		}
	});
});