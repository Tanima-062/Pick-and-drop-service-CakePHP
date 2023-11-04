
function eliminateTitle(){
	// レビュータイトルがコンテンツと同値、またはレビュータイトルがない場合はタイトル欄をなくす
  	$('.yotpo-review').not('.yotpo-template').each(function() {
    	let review_ttl = $(this).find(".content-title").text();
    	let review_cont =$(this).find(".content-review").text();
    	if ((review_ttl === review_cont) || (review_ttl === null)) {
      	$(this).find(".content-title").remove();
   	 }
	// // たまに出てくる、対象：〜　という店舗表記のカスタム
	// const txt2 = $(".product-link").text();
	// const reviewNum2 = txt.replace(" 対象： ", "");
	// $(".product-link").text(reviewNum2);

  });
};

$(window).on('load', function () {
  try {
		// CLSを減らすためheightを設定しとく
		$(".yotpo_container").css("min-height", $(window).height());

		//　文言置換
		$(".free-text-search-input").attr("placeholder", "レビューを検索");
		$(".desktop-clear-btn-text").eq(0).text("全てのレビューを表示");
		$(".mobile-clear-filters-btn").text("全てのレビューを表示");
		$(".more-filters-text").text("その他のフィルター");

		//　レビュー件数表示のカスタム
		const txt = $(".reviews-amount").eq(0).text();
		const reviewNum = txt.replace(/[^0-9]/g, "");
		$(".reviews-amount").text("全" + reviewNum + "件");
		
		// Yotpo枠内のレビュータイトルを整理
		eliminateTitle();

		// 取得が終わったら、yotpoのコンテンツを表示する（取得中表示がCLSを起こしているため）
		var isYotpoConnected =
			$(".yotpo-display-wrapper").css("visibility") == "visible" &&
			($(".yotpo-main-widget").css("display") == "none" ||
				$(".testimonials.yotpo-display-wrapper").css("display") == "none");

		if (isYotpoConnected) {
			$(".yotpo-main-widget").css("display", "block");
			$(".testimonials.yotpo-display-wrapper").css("display", "block");
		}
		
		// 会社レビューページでCLSを減らすため設定したheightをもとに戻す
		$(".yotpo_container").css("min-height", "");

		// レビュー件数表示のタグ
		const review_count_target = document.getElementsByClassName("reviews-amount")[0];

		// ページングしたたびか、レビューが読み込まれるたびに実行
		let page_change_targets = document.getElementsByClassName("yotpo-reviews");
		let page_change_target = page_change_targets[0]
		if(!page_change_targets?.length){
			// オリジナル実装の場合
			page_change_target = document.getElementById("review_content");
		}

		const page_change_observer = new MutationObserver((mutations) => {
			mutations.forEach((mutation) => {
				// オリジナル実装の場合から除外（タグがないため）
				if(page_change_targets?.length){
					if(review_count_target){
						//　レビュー件数表示のカスタム
						const txt = $(review_count_target).text();
						const reviewNum = txt.replace(/[^0-9]/g, "");
						review_count_target.textContent = "全" + reviewNum + "件";
					}

					//　文言置換
					let tagtxt = $(
						'.mobile-more-filters-container > .yotpo-filter-tag[data-type="images"] > .filter-tag-text'
					);
					if($(tagtxt)?.length){
						$(tagtxt).text("画像 / 動画");
					}
				}

				eliminateTitle();
			});
		});

		const page_change_config = {
			childList: true,
		};

		// 該当のタグが存在する場合のみ実行
		if(page_change_target){
			page_change_observer.observe(page_change_target, page_change_config);
		}

		// 検索ボックスからフォーカスが外れた時にも文言置換
		$(".free-text-search-input").blur(function () {
			$(".free-text-search-input").attr("placeholder", "レビューを検索");
		});
	} catch (e) {
    console.error('Yotpo Error: ' + e)
  }
});