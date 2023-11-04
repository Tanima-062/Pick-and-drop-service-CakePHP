// 読み込んでないと思われるので一旦全文コメントアウト

// (function e(t,n,r){function s(o,u){if(!n[o]){if(!t[o]){var a=typeof require=="function"&&require;if(!u&&a)return a(o,!0);if(i)return i(o,!0);var f=new Error("Cannot find module '"+o+"'");throw f.code="MODULE_NOT_FOUND",f}var l=n[o]={exports:{}};t[o][0].call(l.exports,function(e){var n=t[o][1][e];return s(n?n:e)},l,l.exports,e,t,n,r)}return n[o].exports}var i=typeof require=="function"&&require;for(var o=0;o<r.length;o++)s(r[o]);return s})({1:[function(require,module,exports){
// /*
//  * addEventListener対応
//  *
//  * @param t 対象ノード
//  * @param p イベントタイプ
//  * @param l 実行される関数
//  */
// var _addEvent = function(t, p, l) {
// 	try {
// 		t.addEventListener(p, l, false);
// 	} catch(e) {
// 		if(typeof attachEvent !== 'undefined') {
// 			t.attachEvent('on' + p, function(e) {
// 				l.call(t, e);
// 			});
// 		}
// 	}
// };
// /* removeClass
//  *
//  * _removeClass(ELEMENT, 'CLASSNAME');
//  */
// var _removeClass = function(el, className) {
// 	if(el.classList) {
// 		el.classList.remove(className);
// 	} else if (hasClass(el, className)) {
// 		var reg = new RegExp('(\\s|^)' + className + '(\\s|$)');
// 		el.className=el.className.replace(reg, ' ');
// 	}
// };
// /* addClass
//  *
//  * _addClass(ELEMENT, 'CLASSNAME');
//  */
// var _addClass = function(el, className) {
// 	if(el.classList) {
// 		el.classList.add(className);
// 	} else if (!hasClass(el, className)) {
// 		el.className += ' ' + className;
// 	}
// };
// /* hasClass
//  *
//  * _hasClass(ELEMENT, 'CLASSNAME');
//  */
// var _hasClass = function(el, className) {
// 	if(el.classList) {
// 		return el.classList.contains(className);
// 	} else {
// 		return !!el.className.match(new RegExp('(\\s|^)' + className + '(\\s|$)'));
// 	}
// };

// $(function() {
// 	/*
// 	 * DatePicker
// 	 */

// 	//<select>⇔カレンダー
// 	setDepartureDatePicker();

// 	var datePicker = $('.datePickerBtn');
// 	datePicker.pickadate({
// 		format: 'yyyy/m/d',
// 		firstDay: 0,
// 		min: 'today',
// 		today: '',
// 		clear: '',
// 		onOpen: function() {
// 			// 直近の検索可能日（現在の11時間後）にセット
// 			var depDateMin = addDate(new Date(), 11, 'hh');
// 			depDateMin = formatDate(depDateMin);

// 			var selectedDate = this.get('value');
// 			var getDate = new Date(selectedDate);
// 			var nextDate = formatDate(addDate(getDate, 1));
// 			var nextID = Number(this.get('id').replace(/js-datePicker_/g, '')) + 1;

// 			if(nextID < datePicker.length && nextDate < selectedDate) {
// 				// 復路に出発日の1日後をセット
// 				var nextPicker = $('#js-datePicker_' + String(nextID)).pickadate('picker');
// 				nextPicker.set({
// 					'min': selectedDate,
// //					'select': nextDate
// 					'select': selectedDate
// 				});
// 			} else if(nextID === 1) {
// 			// 往路
// 				this.set({
// 					'min': depDateMin,
// 					'select': selectedDate
// 				});
// 			}
// 		},
// 		onClose: function() {
// 			// 出発日 往路
// 			var selectedDate = this.get('value');
// 			this.set('select', selectedDate);

// 			// 出発日 復路
// 			var getDate = new Date(selectedDate);
// 			var nextDate = formatDate(addDate(getDate, 1));
// 			var nextID = Number(this.get('id').replace(/js-datePicker_/g, '')) + 1;

// 			if(nextID < datePicker.length) {
// 				var nextPicker = $('#js-datePicker_' + String(nextID)).pickadate('picker');
// 				var selectedNextDate = nextPicker.get('value');
// 				if(selectedNextDate < selectedDate) {
// 					// 復路に出発日の1日後をセット
// 					nextPicker.set({
// 						'min': selectedDate,
// //						'select': nextDate
// 						'select': selectedDate
// 					});
// 				} else {
// 					// 復路が往路より前の場合はそのまま
// 					nextPicker.set('min', selectedDate);
// 				}
// 			}
// 			$(document.activeElement).blur();
// 		}
// 	});

// 	// Date フォーマット
// 	var formatDate = function(date, format) {
// 		if(!format) format = 'YYYY/MM/DD';
// 		format = format.replace(/YYYY/g, date.getFullYear());
// 		format = format.replace(/MM/g, ('0' + (date.getMonth() + 1)).slice(-2));
// 		format = format.replace(/DD/g, ('0' + date.getDate()).slice(-2));
// 		format = format.replace(/hh/g, ('0' + date.getHours()).slice(-2));
// 		if (format.match(/S/g)) {
// 			var milliSeconds = ('00' + date.getMilliseconds()).slice(-3);
// 			var length = format.match(/S/g).length;
// 			for (var i = 0; i < length; i++) format = format.replace(/S/, milliSeconds.substring(i, i + 1));
// 		}
// 		return format;
// 	};

// 	// Date 加算
// 	var addDate = function(date, num, interval) {
// 		switch(interval) {
// 			case 'YYYY':
// 				date.setYear(date.getYear() + num);
// 			break;
// 			case 'MM':
// 				date.setMonth(date.getMonth() + num);
// 			break;
// 			case 'hh':
// 				date.setHours(date.getHours() + num);
// 			break;
// 			default:
// 			date.setDate(date.getDate() + num);
// 		}
// 		return date;
// 	};

// 	/*
// 	 * timePicker
// 	 */
// 	$('.js-selectTime').pickatime({
// 		format: 'H:i',
// 		clear: '',
// 		onClose: function() {
// 			$(document.activeElement).blur();
// 		}
// 	});

// 	/*
// 	 * 返却方法によるdisabledの制御
// 	 */
// 	//初期値
// 	var num = $('[name="return_way"]:checked').index(),
// 		returnWaySelector = $('#js-returnWaySelector select, #js-returnWaySelector input');

// 	// 乗捨利用の場合選択可
// 	(num === 2) ? returnWaySelector.prop('disabled', false) : returnWaySelector.prop('disabled', true);

// 	//返却方法が変更された時
// 	$('[name="return_way"]').on('click', function() {
// 		var num = $('[name="return_way"]').index(this),
// 			returnWaySelector = $('#js-returnWaySelector select, #js-returnWaySelector input');

// 		// 乗捨利用の場合選択可
// 		(num === 1) ? returnWaySelector.prop('disabled', false) : returnWaySelector.prop('disabled', true);
// 	});

// 	/*
// 	 * 地図エリアのタブ切替
// 	 */
// 	$('#js-tab a[href^="#js-"]').on('click', function() {
// 		var index = $(this).index($(this));
// 		$('#js-mapBody .js-mapLabel').hide();
// 		$(this.hash).show();
// 		$(this).parent('li').siblings('li').removeClass('is-active'); // 自分以外の兄弟（隣接）セレクタ
// 		$(this).parent('li').addClass('is-active');
// 		return false;
// 	});

// 	setDay(1);
// 	setDay(0);
// 	setDatePickerInputValue('#js-datepickerArea_0');
// 	setDatePickerInputValue('#js-datepickerArea_1');
// });

// var setDepartureDatePicker = function() {
// 	var arr = {'numberOfMonths': 3, 'minDate': 0, 'maxDate': '+ 1y', 'yearSuffix': ''};
// 	setDatePicker(arr);
// };

// var setDatePicker = function(arr) {
// 	//カレンダーからプルダウンを更新
// 	$('.js-calendar').each(function() {
// 		var id = '#' + $(this).attr('id');
// 		$(id + ' input').on('change', function() {
// 			var i = 0;
// 			var dates = $(this).val().split('/');
// 			$(id + ' select').each(function() {
// 				if(dates[i]) {
// 					$(this).val(dates[i]);
// 				}
// 				i++;
// 			});
// 		});
// 	});
// 	//プルダウンからカレンダーを更新
// 	$('.js-calendar').each(function() {
// 		var id = '#' + $(this).attr('id');
// 		$(id + ' select').bind('change', function() {
// 			setDatePickerInputValue(id);
// 		});
// 	});
// };

// var setDatePickerInputValue = function(id) {
// 	var i = 0;
// 	var dates = new Array(3);
// 	$(id + ' select').each(function() {
// 		dates[i] = $(this).val();
// 		i++;
// 	});
// 	var newdate = dates[0] + '/' + dates[1] + '/' + dates[2];
// 	$(id + ' input').val(newdate);
// }

// $('#js-datepickerArea_0 select').on('change',function(){
// 	change_date();
// });

// var change_date = function() {
// 	//出発日が変更された場合返却日を変更する
// //	var from_date = get_date('Search',1);
// 	var from_date = get_date('Search');		// 返却日は貸出日と同じ日
// 	set_date('SearchReturn',from_date);
// };

// var check_date = function() {
// 	//出発日時と返却日時のチェック
// 	var from = get_date('Search');
// 	var to = get_date('SearchReturn');
// 	var now = new Date();
// 	now.setTime(now.getTime() + 60 * 60 * 1000);

// 	if (from.getTime() <= now.getTime()) {
// 		alert('出発日時は現在の日時より1時間後以降に設定してください。');
// 		return false;
// 	} else {
// 		if(from.getTime() >= to.getTime()) {
// 			alert('返却日時は出発日時より後に設定してください。');
// 			return false;
// 		}
// 	}
// 	return true;
// };

// var get_date = function(id,plus_day) {
// 	//日付を取得する
// 	var year = $('#' + id + 'Year').val();
// 	var month = $('#' + id + 'Month').val();
// 	var day = $('#' + id + 'Day').val();
// 	var time = $('#' + id + 'Time').val().replace(/-/g,':');

// 	var date = new Date( year  + "/" + month + "/" + day + " " + time + ":00");

// 	//plus_dayがあれば日付をプラスする
// 	if(plus_day) {
// 		year = date.getFullYear();
// 		mon = date.getMonth();
// 		day = date.getDate() + plus_day;

// 		date = new Date(year, mon, day);
// 	}

// 	return date;

// };

// var set_date = function(id,date) {
// 	//日付を変更する
// 	var year = date.getFullYear();
// 	var month = date.getMonth() + 1;
// 	var day = date.getDate();

// 	$('#' + id + 'Year').val(year);
// 	$('#' + id + 'Month').val(month);
// 	$('#' + id + 'Day').val(day);
// };

// var get_place_type = function(name) {
// 	return $('input[name="'+name+'"]:checked').val();
// };

// var set_place = function(id,place_type) {
// 	$('.' + id).hide();
// 	$('#'+ id +'_area_form_' + place_type).show();
// };

// /*
//  * 貸出場所に応じて表示項目を変更する
//  */
// //ページ読み込み時
// var rent_place_type = get_place_type('place');
// set_place('borrow',rent_place_type);

// //出発種類変更時
// $('input[name="place"]').on('click',function() {
// 	var rent_place_type = get_place_type('place');
// 	set_place('borrow',rent_place_type);
// });

// /*
//  * 返却場所に応じて表示項目を変更する
//  */
// //ページ読み込み時
// var return_place_type = get_place_type('return_place');
// set_place('return',return_place_type);

// //返却種類変更時
// $('input[name="return_place"]').on('click',function() {
// 	var return_place_type = get_place_type('return_place');
// 	set_place('return',return_place_type);
// });

// //都道府県に応じたエリアを取得
// var set_area = function(prefecture,rent_flg,default_area) {
// 	$.ajax({
// 		type: "GET",
// 		url: "/rentacar/areas/set_area/" + prefecture + '/' + rent_flg + '/?default='+default_area,
// 		success: function(area) {
// 			if(rent_flg) {
// 				$('#area_id').html(area);
// 			} else {
// 				$('#return_area_id').html(area);
// 			}
// 		}
// 	});
// };

// var set_area_byclientid = function(prefecture,rent_flg,clientid) {
// 	$.ajax({
// 		type: "GET",
// 		url: "/rentacar/areas/set_area_byclientid/" + prefecture + '/' + rent_flg + '/' + clientid + '/',
// 		success: function(area) {
// 			if(rent_flg) {
// 				$('#area_id').html(area);
// 			} else {
// 				$('#return_area_id').html(area);
// 			}
// 		}
// 	});
// };

// //返却場所の都道府県に応じた地域名をあらかじめ設定
// var return_prefecutre = $("#return_prefecture").val();
// var return_area = $("#SearchReturnAreaId").val();
// if (return_prefecutre !== undefined && return_prefecutre != 0) {
// 	set_area(return_prefecutre,0,return_area);
// }

// //出発場所の都道府県に応じた地域名をあらかじめ設定
// var prefecutre = $("#prefecture").val();
// var area = $("#SearchAreaId").val();
// if (prefecutre !== undefined && prefecutre != 0) {
// 	set_area(prefecutre,1,area);
// }

// //出発都道府県に応じてエリアのセレクトボックスを変更する
// //出発都道府県が変更された場合
// $('select[name="prefecture"]').on('change',function() {
// 	var prefecture = $(this).val();
// 		if(location.pathname === '/rentacar/company'){
// 			var clientId = $('[name=client_id]').val();
// 			set_area_byclientid(prefecture,1,clientId);
// 		} else {
// 			if(prefecture == 0){
// 				$('#borrow_area_form_area').css({'cssText': 'display: none !important;'});
// 			} else {
// 				set_area(prefecture, 1);
// 				$('#borrow_area_form_area').css({'cssText': 'display: table-cell !important;'});
// 			}
// 		}
// });

// //返却都道府県に応じてエリアのセレクトボックスを変更する
// //返却エリアが変更された場合
// $('select[name="return_prefecture"]').on('change',function() {
// 	var return_prefecture = $(this).val();
// 		if(return_prefecture == 0){
// 			$('#return_area_form_area').css({'cssText': 'display: none !important;'});
// 		} else {
// 			set_area(return_prefecture,0);
// 			$('#return_area_form_area').css({'cssText': 'display: table-cell !important;'});
// 		}
// });

// ($('[name="return_way"]:checked').val() == 1) ? $('.search_hidden_box').show() : $('.search_hidden_box').hide();


// //上記の条件で検索ボタンを押されたとき
// $(".btn_search").on('click',function(){
// 	if(!check_date()) {
// 		return false;
// 	}
// });

// // pickadate i18n
// $.extend($.fn.pickadate.defaults, {
// 	monthsFull: ['1月', '2月', '3月', '4月', '5月', '6月', '7月', '8月', '9月', '10月', '11月', '12月'],
// 	monthsShort: ['1月', '2月', '3月', '4月', '5月', '6月', '7月', '8月', '9月', '10月', '11月', '12月'],
// 	weekdaysFull: ['日', '月', '火', '水', '木', '金', '土'],
// 	weekdaysShort: ['日', '月', '火', '水', '木', '金', '土'],
// 	today: '今日',
// 	clear: 'キャンセル',
// 	close: '閉じる',
// 	firstDay: 1,
// 	format: 'yyyy mm dd',
// 	formatSubmit: 'yyyy/mm/dd'
// });

// },{}]},{},[1]);

// // 日付セレクトボックスの設定
// $('#SearchYear').on('change',function() {
// 	setDay(1);
// 	setDay(0);
// });
// $('#SearchMonth').on('change',function() {
// 	setDay(1);
// 	setDay(0);
// });
// $('#SearchReturnYear').on('change',function() {
// 	setDay(0);
// });
// $('#SearchReturnMonth').on('change',function() {
// 	setDay(0);
// });

// //日にちの計算
// function monthday(year,month){
// 	var lastday = new Array('', 31, 28, 31, 30, 31, 30, 31, 31, 30, 31, 30, 31);
// 	if ((year % 4 == 0 && year % 100 != 0) || year % 400 == 0){
// 		lastday[2] = 29;
// 	}
// 	return lastday[month];
// }
// function setDay(rent_flg){
// 	var year    = (rent_flg) ? $('#SearchYear').val() : $('#SearchReturnYear').val();
// 	var month   = (rent_flg) ? $('#SearchMonth').val() : $('#SearchReturnMonth').val();
// 	var day     = (rent_flg) ? $('#SearchDay').val() : $('#SearchReturnDay').val();
// 	var lastday = monthday(year, month);
// 	var option = '';
// 	for (var i = 1; i <= lastday; i++) {
// 		if (i == day){
// 			option += '<option value="' + i + '" selected="selected">' + i + '</option>\n';
// 		}else{
// 			option += '<option value="' + i + '">' + i + '</option>\n';
// 		}
// 	}
// 	if (rent_flg) {
// 		$('#SearchDay').html(option);
// 	} else {
// 		$('#SearchReturnDay').html(option);
// 	}
// }
