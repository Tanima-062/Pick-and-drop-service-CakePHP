$(function() {

	/*
	 * ページトップにスクロールするボタン
	 */
	// var topBtn = $('#pagetop');
	// topBtn.hide();
	// $(window).scroll(function() {
	// 	if ($(this).scrollTop() > 100) {
	// 		topBtn.fadeIn();
	// 	} else {
	// 		topBtn.fadeOut();
	// 	}
	// });
	// topBtn.click(function() {
	// 	$('body, html').animate({
	// 		scrollTop: 0
	// 	}, 500);
	// 	return false;
	// });

	//海外レンタカー用
	// // 言語選択
	// const languageList = [
	// 	{
	// 		'code':'jp',
	// 		'url': '//skyticket.jp/rentacar/',
	// 		'name': '日本語'
	// 	},
	// 	{
	// 		'code': 'en',
	// 		'url': '//skyticket.com/car-rental/',
	// 		'name': 'English'
	// 	},
	// 	{
	// 		'code': 'ko',
	// 		'url': '//ko.skyticket.com/car-rental/',
	// 		'name': '한국어'
	// 	},
	// 	{
	// 		'code': 'zh',
	// 		'url': '//zh.skyticket.com/car-rental/',
	// 		'name': '简体中文'
	// 	},
	// 	{
	// 		'code': 'zh-tw',
	// 		'url': '//zh-tw.skyticket.com/car-rental/',
	// 		'name': '繁體中文'
	// 	}
	// ];
	// const currencyList = {
	// 	'jp': [
	// 		{
	// 			name: '¥ 日本円',
	// 			code: 'JPY'
	// 		},
	// 		{
	// 			name:'USD USドル',
	// 			code: 'USD'
	// 		},
	// 		{
	// 			name:'GBP イギリス・ポンド',
	// 			code: 'GBP'
	// 		},
	// 		{
	// 			name:'KRW 韓国・ウォン',
	// 			code: 'KRW'
	// 		},
	// 		{
	// 			name:'CNY 人民元',
	// 			code: 'CNY'
	// 		},
	// 		{
	// 			name:'TWD 新台湾ドル',
	// 			code: 'TWD'
	// 		},
	// 		{
	// 			name:'EUR ユーロ',
	// 			code: 'EUR'
	// 		},
	// 		{
	// 			name:'RUB ロシア・ルーブル',
	// 			code: 'RUB'
	// 		},
	// 		{
	// 			name:'TRY トルコ・リラ',
	// 			code: 'TRY'
	// 		},
	// 		{
	// 			name:'THB タイ・バーツ',
	// 			code: 'THB'
	// 		},
	// 		{
	// 			name:'VND ベトナム・ドン',
	// 			code: 'VND'
	// 		}
	// 	],
	// 	'en': [
	// 		{
	// 			name:'Japanese Yen JPY',
	// 			code: 'JPY'
	// 		},
	// 		{
	// 			name:'US Dollar USD',
	// 			code: 'USD'
	// 		},
	// 		{
	// 			name:'British Pound GBP',
	// 			code: 'GBP'
	// 		},
	// 		{
	// 			name:'Korean Won KRW',
	// 			code: 'KRW'
	// 		},
	// 		{
	// 			name:'Chinese Yuan CNY',
	// 			code: 'CNY'
	// 		},
	// 		{
	// 			name:'New Taiwan Dollar TWD',
	// 			code: 'TWD'
	// 		},
	// 		{
	// 			name:'Euro EUR',
	// 			code: 'EUR'
	// 		},
	// 		{
	// 			name:'Russian Ruble RUB',
	// 			code: 'RUB'
	// 		},
	// 		{
	// 			name:'Turkish Lira TRY',
	// 			code: 'TRY'
	// 		},
	// 		{
	// 			name:'Thai Baht THB',
	// 			code: 'THB'
	// 		},
	// 		{
	// 			name:'Vietnamese Dong VND',
	// 			code: 'VND'
	// 		}
	// 	],
	// 	'ko': [
	// 		{
	// 			name: '¥ 일본 엔',
	// 			code: 'JPY'
	// 		},
	// 		{
	// 			name:'USD US 달러',
	// 			code: 'USD'
	// 		},
	// 		{
	// 			name:'GBP 영국・파운드',
	// 			code: 'GBP'
	// 		},
	// 		{
	// 			name:'KRW 한국・원',
	// 			code: 'KRW'
	// 		},
	// 		{
	// 			name:'CNY 중국 위안',
	// 			code: 'CNY'
	// 		},
	// 		{
	// 			name:'TWD 신 타이완 달러',
	// 			code: 'TWD'
	// 		},
	// 		{
	// 			name:'EUR 유로',
	// 			code: 'EUR'
	// 		},
	// 		{
	// 			name:'RUB 러시아・루블',
	// 			code: 'RUB'
	// 		},
	// 		{
	// 			name:'TRY 터키・리라',
	// 			code: 'TRY'
	// 		},
	// 		{
	// 			name:'THB 태국・바트',
	// 			code: 'THB'
	// 		},
	// 		{
	// 			name:'VND 베트남・동',
	// 			code: 'VND'
	// 		}
	// 	],
	// 	'zh': [
	// 		{
	// 			name: '¥ 日元',
	// 			code: 'JPY'
	// 		},
	// 		{
	// 			name:'USD 美元',
	// 			code: 'USD'
	// 		},
	// 		{
	// 			name:'GBP 英镑',
	// 			code: 'GBP'
	// 		},
	// 		{
	// 			name:'KRW 韩元',
	// 			code: 'KRW'
	// 		},
	// 		{
	// 			name:'CNY 人民币',
	// 			code: 'CNY'
	// 		},
	// 		{
	// 			name:'TWD 新台币',
	// 			code: 'TWD'
	// 		},
	// 		{
	// 			name:'EUR 欧元',
	// 			code: 'EUR'
	// 		},
	// 		{
	// 			name:'RUB 俄罗斯卢布',
	// 			code: 'RUB'
	// 		},
	// 		{
	// 			name:'TRY 土耳其里拉',
	// 			code: 'TRY'
	// 		},
	// 		{
	// 			name:'THB 泰铢',
	// 			code: 'THB'
	// 		},
	// 		{
	// 			name:'VND 越南盾',
	// 			code: 'VND'
	// 		}
	// 	],
	// 	'zh-tw': [
	// 		{
	// 			name: '¥ 日圓',
	// 			code: 'JPY'
	// 		},
	// 		{
	// 			name:'USD 美元',
	// 			code: 'USD'
	// 		},
	// 		{
	// 			name:'GBP 英鎊',
	// 			code: 'GBP'
	// 		},
	// 		{
	// 			name:'KRW 韓元',
	// 			code: 'KRW'
	// 		},
	// 		{
	// 			name:'CNY 人民幣',
	// 			code: 'CNY'
	// 		},
	// 		{
	// 			name:'TWD 新台幣',
	// 			code: 'TWD'
	// 		},
	// 		{
	// 			name:'EUR 歐元',
	// 			code: 'EUR'
	// 		},
	// 		{
	// 			name:'RUB 俄羅斯盧布',
	// 			code: 'RUB'
	// 		},
	// 		{
	// 			name:'TRY 土耳其里拉',
	// 			code: 'TRY'
	// 		},
	// 		{
	// 			name:'THB 泰銖',
	// 			code: 'THB'
	// 		},
	// 		{
	// 			name:'VND 越南盾',
	// 			code: 'VND'
	// 		}
	// 	]
	// };
	// $('#button_locale').on('click', function() {
	// 	if($('#select_language').children().length === 0) {
	// 		let lang_options = [];
	// 		for (let lang_index in languageList) {
	// 			let lang_option = $('<option>').attr('data-url', languageList[lang_index].url).val(languageList[lang_index].code).text(languageList[lang_index].name);
	// 			lang_options.push(lang_option);
	// 		}
	// 		$('#select_language').append(lang_options);
	// 	}
	// 	if($('#select_currency').children().length === 0) {
	// 		let curr_options = [];
	// 		for (let curr_index in currencyList['jp']) {
	// 			let curr_option = $('<option>').val(currencyList['jp'][curr_index].code).text(currencyList['jp'][curr_index].name);
	// 			curr_options.push(curr_option);
	// 		}
	// 		$('#select_currency').append(curr_options);
	// 	}

	// 	$('#js_modal_overlay').fadeIn( "slow" );
	// 	$('#locale_modal').fadeIn( "slow" );
	// });
	// // 閉じる
	// $('#js_modal_overlay').on('click', function() {
	// 	$('#js_modal_overlay').fadeOut( "fast" );
	// 	$('#locale_modal').fadeOut( "fast" );
	// });
	// $('.close_locale_modal').on('click', function() {
	// 	$('#js_modal_overlay').fadeOut( "fast" );
	// 	$('#locale_modal').fadeOut( "fast" );
	// });

	// // 言語選択
	// $('#select_language').on('change', function () {
	// 	// 通貨リストを選択した言語のリストに切り替え
	// 	$('#select_currency').empty();

	// 	const code = $(this).val();
	// 	const curr_list = currencyList[code];
	// 	let curr_options = [];
	// 	for (let curr_index in curr_list) {
	// 		let curr_option = $('<option>').val(curr_list[curr_index].code).text(curr_list[curr_index].name);
	// 		curr_options.push(curr_option);
	// 	}
	// 	$('#select_currency').append(curr_options).prop('disabled', code === 'jp')
	// });
	
	// $('#submit_english').on('click', function () {
	// 	// cookieセット
	// 	const cookie = 'currency=USD;path=/;'
	// 	document.cookie = cookie;
		
	// 	// .comに遷移
	// 	const url = $('#select_language option[value="en"]').data('url');
	// 	location.href = url;
	// });

	// // 保存ボタン
	// $('#submit_locale').on('click', function () {
	// 	const select_language = $('#select_language').val();
	// 	if(select_language == 'jp') {
	// 		$('#close_locale_modal').click();
	// 		return false;
	// 	}

	// 	// cookieセット
	// 	const select_currency = $('#select_currency').val();
	// 	const cookie = 'currency=' + select_currency + ';path=/;'
	// 	document.cookie = cookie;
		
	// 	// 該当のドメインに遷移
	// 	const url = $('#select_language option:selected').data('url');
	// 	location.href = url;
	// });
});

function createFunctionWithTimeout(callback, opt_timeout) {
    var called = false;

    var func = function() {
        if (!called) {
            called = true;
            callback();
        };
    };

    setTimeout(func, opt_timeout || 1000);

    return func;

}
