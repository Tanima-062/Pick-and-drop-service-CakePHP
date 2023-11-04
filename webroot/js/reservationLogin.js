$(function() {
		
	function changeSubmitButton() {
		if ($('#js-tel').hasClass('-valid-error') || $('#js-rsv-num').hasClass('-valid-error')) {
			$('#js-btn-see-this-rsv').attr('disabled', true);
		} else {
			$('#js-btn-see-this-rsv').attr('disabled', false);
		}
	}

	function fieldValidation(elm, checkValue, error_message) {
		// スペース削除、ハイフン削除、大文字化、半角化
		const inputValue = $(elm).val().trim().replaceAll('ー', '').replaceAll('−', '').replaceAll('-', '').toUpperCase().replace( /[Ａ-Ｚａ-ｚ０-９]/g, function(s) {
			return String.fromCharCode(s.charCodeAt(0) - 65248);
		});
	
		$(elm).val(inputValue);
	
		if (inputValue == '') {
			$(elm).addClass('-valid-error');
			$(elm).siblings('.js-valid-err-message').remove();
			$(elm).after('<p class="js-valid-err-message valid-err-message">必須</p>');
		} else if (!inputValue.match(checkValue)) {
			$(elm).addClass('-valid-error');
			$(elm).siblings('.js-valid-err-message').remove();
			$(elm).after('<p class="js-valid-err-message valid-err-message">' + error_message + '</p>');
		} else {
			$(elm).removeClass('-valid-error');
			$(elm).siblings('.js-valid-err-message').remove();
		}
		changeSubmitButton();
	}

	function checkRsvNum() {

		fieldValidation('#js-rsv-num', '^[0-9a-zA-Z]+$', '半角英数字で入力してください');
	}

	function checkTel() {

		fieldValidation('#js-tel', '^\\d{7,13}$', '電話番号を正しい形式で入力してください');
	}

	$('#js-rsv-num').on('blur', function() {
		checkRsvNum();
	});

	$('#js-tel').on('blur', function() {
		checkTel();
	});

	//submit時にチェック
	$('form').on('submit', function() {

		checkRsvNum();

		checkTel();

		//エラーがあればsubmitを停止
		if ($('#js-rsv-num').hasClass('-valid-error') || $('#js-tel').hasClass('-valid-error')) {
			return false;
		}
	});
});