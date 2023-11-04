// Reservations/input_confirm.ctp

$(function() {
	// console.log("paymentApiFlg: " + paymentApiFlg);

	$(document).on('ready', function(){
		var sessionToken = $('#session_token').val();
		if (sessionToken.length) {
			sessionToken = $('#session_token').val();
		}
		else {
			return;
		}

		if (!paymentApiFlg) { //決済APIがfalseのとき
			var econapps = new eController.EconSconeCardPay(sessionToken); // eslint-disable-line

			econapps.getEntries(function(result) {
				// console.log(result);
				if(result.status==1){    //success
					var saveData = econapps.getSaveEntries();

					$('#cardNumber').text(saveData.econCardno4);
					$('#cardOwner').text(saveData.econCardOwner);
					$('#cardExpMonth').text(saveData.cardExpdate.substr(4,2));
					$('#cardExpYear').text(saveData.cardExpdate.substr(0,4));
				}
				else {
					sendLog({info : result.info, status : result.status});
					createErrorMessageTag(result.info + '(' + result.status + ')');
				}
			});
		}
	});

	$(document).on('click', '#submitReserve', function(){
		if ($(this).attr('type') == 'submit') {
			return;
		}

		showLoading();

		$('#submitReserve').prop('disabled', true);
		setTimeout(() => {
			$('#submitReserve').prop('disabled', false);
		}, 5000);

		$.ajax({
			type: 'POST',
			dataType: 'json',
			timeout: 5000,
			data: {
				uniq_id: $('#ReservationUniqId').val()
			},
			url: '/rentacar/reservations/save_payment/',
		}).done(function(res) {
			if (res.msg.length > 0) {
				createErrorMessageTag(res.msg + ' トップページから再申込してください。');
				setTimeout(() => { location.href = location.protocol + '//' + location.hostname + '/rentacar/'; }, 2000); // 2秒後にトップへリダイレクト
				return;
			}
			if (paymentApiFlg) {
				location.href = $('#ReservationRedirectUrlEcon').val();
			} else {
				paymentEcon($('#session_token').val());
			}
		}).fail(function() {
			sendLog({info : 'システムエラー', status : 'input_confirm'});
			hideLoading();
			createErrorMessageTag('システムエラー。トップページから再申込してください。');
			$('#submitReserve').prop('disabled', false);
			setTimeout(() => { location.href = location.protocol + '//' + location.hostname + '/rentacar/'; }, 2000); // 2秒後にトップへリダイレクト
		});
	});

	function paymentEcon(session_token) {
		var econapps = new eController.EconSconeCardPay(session_token); // eslint-disable-line
		econapps.goPayment(function (result) {
			if(result.status!=1){
				sendLog({info : result.info, status : result.status});
			}
		});
	}

	function createErrorMessageTag(message) {
		$('#sessionMessage').empty();
		$('#sessionMessage').append('<div class="session-message"><ul><li><strong>' + message + '</strong></li></ul></div>');
		$(window).scrollTop(0);
	}

	const showLoading = () => {
		$('#js_loading_indicator').show();
		$('html, body').addClass('is_loading');
	};
	const hideLoading = () => {
		$('#js_loading_indicator').hide();
		$('html, body').removeClass('is_loading');
	};

	function sendLog(data) {
		$.ajax({
			type: 'POST',
			dataType: 'json',
			url: '/rentacar/reservations/store_log/',
			data: data
		});
	}

});
