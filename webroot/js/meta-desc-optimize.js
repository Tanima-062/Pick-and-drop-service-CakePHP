

$(window).on('load', function () {
  try {
		//　レビュー件数表示のカスタム
		const txt = $(".reviews-amount").eq(0).text();
		const reviewNum = txt.replace(/[^0-9]/g, "");
		const reviewsAmount = "全" + txt.replace(/[^0-9]/g, "") + "件";

		// メタタグを更新(会社レビューページのみ処理する)
		const companyName = $(".page-title").first().text().replace('の感想・口コミ', '');
		if (reviewNum) {
			$('meta[name=description]').attr("content", `${companyName}評判・口コミを比較！実際の利用者から${reviewsAmount}の口コミが確認可能！サービス利用の感想から設備や車両までレビュー内容を絞り込んで検索できる。スカイレンタカーおすすめのレンタカープランと人気車種を簡単に比較、予約するならスカイチケット！`);
			document.title = ("content",`${companyName}評判・口コミ - ${reviewsAmount}`);
		}
		else {
			$('meta[name=description]').attr("content",`${companyName}評判・口コミを比較！実際の利用者からの口コミが確認可能！サービス利用の感想から設備や車両までレビュー内容を絞り込んで検索できる。スカイレンタカーおすすめのレンタカープランと人気車種を簡単に比較、予約するならスカイチケット！`);
			document.title = ("content",`${companyName}評判・口コミ`);
		}
	} catch (e) {
    console.error('Meta description error: ' + e)
  }
});