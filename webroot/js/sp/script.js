/**
 * jQuery
 */

'use strict';

$(function() {
	/*
	* トップに戻る
	*/
	// var topBtn = $('#pagetop');
	// topBtn.hide();
	// $(window).scroll(function() {
	// 	if($(this).scrollTop() > 100) {
	// 		topBtn.fadeIn();
	// 	} else {
	// 		topBtn.fadeOut();
	// 	}
	// });
	// topBtn.click(function() {
	// 	$('body,html').animate({
	// 		scrollTop: 0
	// 	}, 500);
	// 	return false;
	// });

	// 使ってなさそう、一旦コメントアウト
	// /*
	// * ハンバーガーメニュー
	// */
	// var menu = $('#js-sideMenu'),
	// menuBtn = $('#js-menuBtn'),
	// layer = $('#js-menuLayer'),
	// wrap = $('#container'),
	// closeBtn = $('#slidemenu_close'),
	// menuWidth = menu.outerWidth(),
	// hsize = $(window).height(),

	// // jquery.smartbanner.js
	// smartbanner = $('#smartbanner'),
	// bannerHeight = smartbanner.height();

	// // menu開
	// menuBtn.on('click', function() {
	// 	wrap.toggleClass('menu_opened'); // メニュー開閉フラグ用className
	// 	// menuBtn.toggleClass('active'); // ボタン矢印用className

	// 	if(wrap.hasClass('menu_opened')) {
	// 		layer.show(); // wrapにmenuを閉じるレイヤーを覆う
	// 		layer.css('height', hsize + 'px');
	// 		// メニューを右にスライド（開く）
	// 		menu.css({
	// 			'transform' : 'translate(' + menuWidth + 'px, 0)',
	// 			'transition' : 'transform 300ms cubic-bezier(0, 0, .1, 1)'
	// 		});
	// 		// アプリバナーがある場合、バナーの高さ分wrapとmenuを下にずらす
	// 		if(smartbanner.hasClass('shown')) {
	// 			wrap.css({'margin-top' : bannerHeight + 'px'});
	// 			menu.css({
	// 				'margin-top' : bannerHeight + 'px',
	// 				'height' : (menu.height() - bannerHeight) + 'px'
	// 			});
	// 			// バナーを閉じたらwrapとmenuをスライドアップ
	// 			smartbanner.find('a').on('click', function() {
	// 				wrap.css({
	// 					'transform' : 'translate(' + menuWidth + 'px, 0)',
	// 					'transition' : 'transform 300ms cubic-bezier(0, 0, .25, 1)',
	// 					'margin-top' : 0
	// 				});
	// 				menu.css({
	// 					'transform' : 'translate(' + menuWidth + 'px, 0)',
	// 					'transition' : 'transform 300ms cubic-bezier(0, 0, .1, 1)',
	// 					'margin-top' : 0,
	// 					'height' : (menu.height() + bannerHeight) + 'px'
	// 				});
	// 			});
	// 		}
	// 	}
	// });

	// // menu閉
	// layer.on('click', function() {
	// 	wrap.removeClass('menu_opened');
	// 	// メニューを左にスライド（閉じる）
	// 	menu.css({
	// 		'transform' : 'translate(-' + menuWidth + 'px, 0)',
	// 		'transition' : 'transform 1000ms cubic-bezier(0, 0, .1, 1)'
	// 	});

	// 	// アプリバナー表示時
	// 	if(smartbanner.hasClass('shown')) {
	// 		wrap.css({'margin-top' : 0});
	// 		menu.css({
	// 			'margin-top' : 0,
	// 			'height' : (menu.height() + bannerHeight) + 'px'
	// 		});
	// 	}
	// 	// menuBtn.removeClass('active');
	// 	layer.hide();
	// });

	// // menu閉
	// closeBtn.on('click', function() {
	// 	wrap.removeClass('menu_opened');
	// 	// メニューを左にスライド（閉じる）
	// 	menu.css({
	// 		'transform' : 'translate(-' + menuWidth + 'px, 0)',
	// 		'transition' : 'transform 1000ms cubic-bezier(0, 0, .1, 1)'
	// 	});

	// 	// アプリバナー表示時
	// 	if(smartbanner != null) {
	// 		wrap.css({'margin-top' : 0});
	// 		menu.css({
	// 			'margin-top' : 0,
	// 			'height' : (menu.height() + bannerHeight) + 'px'
	// 		});
	// 	}
	// 	// menuBtn.removeClass('active');
	// 	layer.hide();
	// });

	// 使ってなさそう、一旦コメントアウト
	// // 航空会社ページのみ
	// if($('#js-mainimg').hasClass('airline_sp') || $('#contents').hasClass('airline-international')) {
	// 	$('#menuBtn').addClass('menuBtn_is-airline');
	// }

	// 使ってなさそう、一旦コメントアウト
	// // 海外航空会社最安値の翌月・翌々月
	// $('.js-nextMonth').on('click', function() {
	// 	$(this).next('ul').toggleClass('displayn');
	// });

	// 使ってなさそう、一旦コメントアウト
	// /*
	// * トップサービスナビの動作
	// */
	// if($('nav').hasClass('menu_wrap')) {
	// 	var _menu = $('#menu_main');
	// 	var totalWidth = 0;
	// 	$('.menu > .menu_list').each(function(index) {
	// 		totalWidth += parseInt($(this).outerWidth(), 10);
	// 	});
	// 	_menu.width(totalWidth);
	// 	var pos = $('.on').offset();
	// 	$('#menu_wrapper').scrollLeft(pos.left　- 150);
	// 	// スクロール
	// 	var _touchmove = ('ontouchmove' in document) ? 'touchmove' : 'click';
	// 	$(_menu).on(_touchmove,function(){
	// 		$(_menu).css({
	// 			'transform': 'none'
	// 		});
	// 	});
	// 	// その他のメニュー開閉
	// 	$('#menu_other').click(function() {
	// 		$('.menu_list.other').toggleClass('on');
	// 		$('#menu_main').scrollLeft(totalWidth);
	// 		$('#sub_menu').toggle();
	// 	});
	// 	 $(document).on('click touchend',function(e) {
	// 		 if (!$(e.target).closest('.other').length) {
	// 			$('#sub_menu').hide();
	// 			$('.menu_list.other').removeClass('on');
	// 		 }
	// 	});
	// }
	
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
		
	// 	// サイドメニューを閉じる
	// 	closeBtn.click();
	// 	// モーダルを開く
	// 	localeModalWindow('body');
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
});

/**
 * JavaScript
 */
(function() {
	// Yahoo Tag Manager
	var tagjs = document.createElement('script');
	var s = document.getElementsByTagName('script')[0];
	tagjs.async = true;
	tagjs.src = '//s.yjtag.jp/tag.js#site=B5Vxn74';
	s.parentNode.insertBefore(tagjs, s);
})();

// アコーディオンメニュー
$(document).ready(function(){
	//acordion_treeを一旦非表示に
	$(".acordion_tree").css("display","none");

	// いったんイベントを削除
	$(".trigger").off("click");
	//triggerをクリックすると以下を実行
	$(".trigger").click(function(){
		//もしもクリックしたtriggerの直後の.acordion_treeが非表示なら
		if($("+.acordion_tree",this).css("display")=="none"){
			//classにactiveを追加
			$(this).addClass("active");
			//直後のacordion_treeをスライドダウン
			$("+.acordion_tree",this).slideDown("normal");
		}else{
			//classからactiveを削除
			$(this).removeClass("active");
			//クリックしたtriggerの直後の.acordion_treeが表示されていればスライドアップ
			$("+.acordion_tree",this).slideUp("normal");
		}
	});
});

// Region Page
$(document).ready(function(){ 
	$('.js-jump_to_value').on('change', function() {
		location.href = $(this).val();
	});
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

// 海外レンタカー
// function localeModalWindow(mainWrap){
// 	var modalOverlay = "#modalOverlay";
// 	var modalClose = "#modal-close-locale, #close_locale_modal, #modalOverlay";
// 	var modalCont = "#locale_modal";
// 	if($(modalOverlay)[0])return false;
// 	var scrollpos = $(window).scrollTop();
// 	$('body').addClass('is-fixed').css({'top': -scrollpos});
// 	$(mainWrap).prepend( '<div id="modalOverlay"></div>' );
// 	$(modalOverlay).fadeIn( "slow" );
// 	$(modalCont).addClass('show');
// 	$(modalOverlay + "," + modalClose).unbind().click( function(){
// 		$(mainWrap).removeClass('is-fixed').css({'top': 0});
// 		window.scrollTo(0 , scrollpos);
// 		$( modalCont ).removeClass('show');
// 		$(modalOverlay).fadeOut( "slow" , function(){
// 			$('.remark-wrapper').hide();
// 			$('.-remark').empty();
// 			//$('#sliderSet').empty();
// 			$(modalOverlay).remove();
// 			$('.modalOpen').prop("disabled", false);
// 		});
// 	});
// 	return false;
// }
