$(function(){
	// 出発日時
	var from = $('#ReservationFrom').val();
	// 返却日時
	var to = $('#ReservationTo').val();
	// 出発店舗ID
	var fromOfficeId = $('#ReservationFromOffice').val();
	// 返却店舗ID
	var returnOfficeId = $('#ReservationReturnOffice').val();
	// 車両クラスID
	var carClassId = $('#ReservationCarClassId').val();

	// 読み込み時在庫チェック
	stockCheck();

	// 乗り捨て料金・深夜手数料
	ajaxDropOffLateNight(from, to, fromOfficeId, returnOfficeId, carClassId);

	// 出発店舗変更
	$('#ReservationFromOffice').on('change', function(){
		var obj = $(this);
		fromOfficeId = obj.val();
		fromOfficeDisplay(fromOfficeId);

		// 乗り捨て料金
		ajaxDropOffLateNight(from, to, fromOfficeId, returnOfficeId, carClassId);
	});
	// 返却店舗変更
	$('#ReservationReturnOffice').on('change', function(){
		var obj = $(this);
		returnOfficeId = obj.val();
		returnOfficeDisplay(returnOfficeId);
		// 乗り捨て料金
		ajaxDropOffLateNight(from, to, fromOfficeId, returnOfficeId, carClassId);
	});

	// 幼児人数がカウントされたら
	childSheetChoice($('#ReservationInfants').val());
	$('#ReservationInfants').on('change', function(){
		childSheetChoice($(this).val());
	});

	// シート変更
	$('#sheet-option').find('select').on('change', function(){
		var obj = $(this);
		privilegeId = obj.data('id');
		privilegeIdPrice = obj.data('price');
		privilegeCount = obj.val();
		privilegePrice = privilegeIdPrice * privilegeCount;

		privilegeDisplay(privilegeId, privilegeCount, privilegePrice);
	});
	// その他オプション変更
	// 選択肢が二つだけの時はセレクトボックスの文字列を変える（オリジナル）
	$('#privilege-option select').each(function(index, element) {
		if ($(this).children('option').length === 2) {
			//上限1の時にoptionを書き換え
			$(this).find('option:first').text('---');
			$(this).find('option:last').text('利用する');
		}
	});

	// 選択肢が二つだけの時はチェックボックスを使用する（テストパターン）
	$('.requestOption').each(function(index, element) {
		if($(element).prop('checked') === true) {
			$(element).val(1);
		} else {
			$(element).val(0);
		}
		});
	$('#privilege-option').find(".requestOption").on('change', function(){
		var obj = $(this);
		var requestOptionChecked = $(this).prop('checked');
		privilegeId = obj.data('id');
		privilegeIdPrice = obj.data('price');
		if(requestOptionChecked == true) {
			obj.val(1);
			privilegeCount = obj.val();
		} else {
			obj.val(0);
			privilegeCount = obj.val();
		}
		privilegePrice = privilegeIdPrice * privilegeCount;
		privilegeDisplay(privilegeId, privilegeCount, privilegePrice);
	});

	// 選択肢が二つ以上の時はセレクトを使用する（既存）
	$('#privilege-option').find('select').on('change', function(){
		var obj = $(this);
		privilegeId = obj.data('id');
		privilegeIdPrice = obj.data('price');
		privilegeCount = obj.val();
		privilegePrice = privilegeIdPrice * privilegeCount;
		privilegeDisplay(privilegeId, privilegeCount, privilegePrice);
	});

	// GROWTH-900 オプションの数を選択する位置にオプションの価格を表示する
	optionPriceDisplay();
});

function childSheetChoice(infantsSelect) {
	if (infantsSelect == 0) {
		$('#js-sheet-note').hide();
	} else {
		$('#js-sheet-note').show();
	}
}
function fromOfficeDisplay(fromOfficeId) {
	var obj = $('#fromOfficeId'+fromOfficeId);
	obj.siblings().hide();
	obj.show();
}
function returnOfficeDisplay(returnOfficeId) {
	var obj = $('#returnOfficeId'+returnOfficeId);
	obj.siblings().hide();
	obj.show();
}
function privilegeDisplay(privilegeId, privilegeCount, privilegePrice) {
	var obj = $('#other-price #privilege'+privilegeId);
	if (!privilegeCount || privilegeCount == 0) {
		obj.hide();
	} else {
		obj.show();
	}
	privilegePrice = privilegePrice.toString().replace(/(\d)(?=(\d\d\d)+$)/g, '$1,');
	obj.find('.count .num').text(privilegeCount);
	obj.find('.price').text('¥ ' + privilegePrice);

	// 料金変更
	priceDisplay();
}
function optionPriceDisplay() {
	$('#sheet-option, #privilege-option').find('select').each(function(i, e){
		var obj = $(this);
		var privilegeId = obj.data('id');
		var privilegeIdPrice = obj.data('price');
		var priceObj = $('#option-price'+privilegeId);
		privilegeIdPrice = privilegeIdPrice.toString().replace(/(\d)(?=(\d\d\d)+$)/g, '$1,');
		priceObj.text('¥ ' + privilegeIdPrice);
	});
}
function priceDisplay() {
	// 基本料金
	var basicPrice = parseInt($('#ReservationBasicPrice').val());
	// その他料金
	var otherCount = 0;
	var otherPrice = parseInt(0);
	$('.price').each(function(i, e){
		if ($(this).is(':visible')) {
			otherCount++;
			otherPrice += parseInt($(this).text().replace(/,/g, '').trim().slice(2));
		}
	});
	if (otherCount == 0) {
		$('#other-none').text('オプションなし');
	} else {
		$('#other-none').text('');
	}
	// 合計料金
	var totalPrice = parseInt(0);
	totalPrice = basicPrice + otherPrice;
	strTotalPrice = totalPrice.toString().replace(/(\d)(?=(\d\d\d)+$)/g, '$1,');
	$('#total-place').text('¥ ' + strTotalPrice);
	$('#ReservationEstimationTotalPrice').val(totalPrice);
}
function ajaxDropOffLateNight(from, to, fromOfficeId, returnOfficeId, carClassId) {

	var url = '/rentacar/plan/ajaxAction/';
	var submitFlgObj = $('#ReservationSubmitFlg');

	$.ajax({
		url: url,
		method: 'POST',
		dataType: 'json',
		data: {
			from: from,
			to: to,
			carClassId: carClassId,
			fromOfficeId: fromOfficeId,
			returnOfficeId: returnOfficeId,
			ajaxAction: 'dropOffLateNight'
		}
	}).done(function(data){
		submitFlgObj.val(1);
		if (fromOfficeId != returnOfficeId) {
			if (!data['dropPrice']) {
				$('#drop').hide();
				$('#drop .price').text('¥ ' + 0);
				submitFlgObj.val(0);
			} else {
				var dropPrice = $.parseJSON(data['dropPrice']);
				if ($.isNumeric(dropPrice)/* && dropPrice !== undefined*/) {
					dropPrice = dropPrice.toString().replace(/(\d)(?=(\d\d\d)+$)/g, '$1,');
					$('#drop').show();
					$('#drop .price').text('¥ ' + dropPrice);
				} else {
					$('#drop').hide();
					$('#drop .price').text('¥ ' + 0);
					submitFlgObj.val(0);
				}
			}
		} else {
			$('#drop').hide();
			$('#drop .price').text('¥ ' + 0);
		}
		if (!data['nightFee']) {
			$('#nightfee').hide();
			$('#nightfee .price').text('¥ ' + 0);
		} else {
			var nightFee = $.parseJSON(data['nightFee']);
			if ($.isNumeric(nightFee) && nightFee !== undefined) {
				nightFee = nightFee.toString().replace(/(\d)(?=(\d\d\d)+$)/g, '$1,');
				$('#nightfee').show();
				$('#nightfee .price').text('¥ ' + nightFee);
			} else {
				$('#nightfee').hide();
				$('#nightfee .price').text('¥ ' + 0);
			}
		}
	}).fail(function(){
		if (!confirm('データ取得に失敗しました。再取得しますか？')) {
			// キャンセルの時の処理
			submitFlgObj.val(0);
			return false;
		} else {
			// OKの時の処理
			location.reload();
		}
	}).complete(function(){
		// 出発店舗表示
		fromOfficeDisplay(fromOfficeId);
		// 返却店舗表示
		returnOfficeDisplay(returnOfficeId);
		// シート・その他オプション表示
		$('#sheet-option, #privilege-option').find("select, .requestOption").each(function(i, e){
			var obj = $(this);
			var privilegeId = obj.data('id');
			var privilegeIdPrice = obj.data('price');
			var privilegeCount = obj.val();
			var privilegePrice = privilegeIdPrice * privilegeCount;
			privilegeDisplay(privilegeId, privilegeCount, privilegePrice);
		});
		// 料金表示
		priceDisplay();

		$('#dropError').remove();
		$("#btn_submit").attr("disabled", false);
		$("#btn_submit_bottom").attr("disabled", false);
		if (submitFlgObj.val() == 0) {
			$("#btn_submit").attr("disabled", true);
			$("#btn_submit_bottom").attr("disabled", true);
			$('#returnOfficeBox').append('<p id="dropError" style="margin-bottom:10px;color:red;">この営業所には乗り捨てできません。再度営業所を選択してください。</p>');
		} else {
			$('#dropError').remove();
			// 在庫チェック
			stockCheck();
		}
	});
}
function stockCheck() {
	var obj = $('#ReservationFromOffice option:selected');

	$('#stockError').remove();
	if (obj.text().indexOf('在庫なし') != -1) {
		// 在庫無し
		$("#btn_submit").attr("disabled", true);
		$("#btn_submit_bottom").attr("disabled", true);
		$('#officeStock').after('<p id="stockError" style="margin-bottom:10px;color:red;">この営業所には在庫がありません。再度営業所を選択してください。</p>');
	} else {
		// 在庫有り
		$("#btn_submit").attr("disabled", false);
		$("#btn_submit_bottom").attr("disabled", false);
	}
}
