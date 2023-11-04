// Reservations/step1.ctp
$(function () {
	// console.log("paymentApiFlg: " + paymentApiFlg);

	$("#btn_submit_payment").on("click", function () {
		// gaイベント
		ga("send", "event", "pc_step1", "click", "決済ボタン", {
			hitCallback: createFunctionWithTimeout(function () {
				formSubmit();
			}),
		});
	});

	$("#btn_submit_bottom_payment").on("click", function () {
		// gaイベント
		ga("send", "event", "pc_step1", "click", "フローティングボタン", {
			hitCallback: createFunctionWithTimeout(function () {
				formSubmit();
			}),
		});
	});

	function formSubmit() {
		$("#application_form input").each(function () {
			$(this).blur();
		});

		var is_error = false;
		$(".error_message").each(function () {
			$("html,body").animate({ scrollTop: $(this).offset().top - 30 });
			is_error = true;
			return false;
		});

		if (is_error) {
			return;
		}

		showLoading();

		// ここから続きはajaxのprepare
		$(".bg_orange").prop("disabled", true);
		setTimeout(() => {
			$(".bg_orange").prop("disabled", false);
		}, 5000);

		$.ajax({
			type: "POST",
			dataType: "json",
			timeout: 5000,
			url: "/rentacar/reservations/prepare_payment/",
			data: {
				family_name: $("#js-lastName").val(),
				first_name: $("#js-givenName").val(),
				tel: $("#js-tel").val(),
				email: $("#js-email").val(),
				from_rentacar_client: $("#ReservationFromRentacarClient").val(),
			},
		})
			.done(function (res) {
				//				console.log(res);
				if (res.message) {
					// TODO:サーバ側のvalidationにかかったとき
					createErrorMessageTag(res.message);
					$(".bg_orange").prop("disabled", false);
					hideLoading();
					//					console.log(result.info + ", " + result.status);
					$(window).scrollTop(0);
					return;
				}
				if (paymentApiFlg) {//決済APIがtrueのとき
					if ($("#application_form").length) {
						$("#application_form").submit();
					} else {
						sendLog({ info: "システムエラー", status: "input" });
						hideLoading();
						createErrorMessageTag("システムエラー。トップページから再申込してください。");
						$(".bg_orange").prop("disabled", false);
						setTimeout(() => {location.href = location.protocol + "//" + location.hostname + "/rentacar/";}, 2000); // 2秒後にトップへリダイレクト
					}
				} else {
					initEcon(res.session_token, res.request_id, 1);
				}
			})
			.fail(function () {
				sendLog({ info: "システムエラー", status: "input" });
				hideLoading();
				createErrorMessageTag(
					"システムエラー。トップページから再申込してください。"
				);
				$(".bg_orange").prop("disabled", false);
				setTimeout(() => {
					location.href =
						location.protocol +
						"//" +
						location.hostname +
						"/rentacar/";
				}, 2000); // 2秒後にトップへリダイレクト
			});
	}

	function initEcon(session_token, request_id, use_form) {
		//console.log("session_token:" + session_token);
		//console.log("request_id:" + request_id);
		var econapps = new eController.EconSconeCardPay(
			session_token,
			request_id
		); // eslint-disable-line
		econapps.fetchEntryResources(function (result) {
			//console.log(result);
			if (result.status === 1) {
				//success
				var usedCardFlg = use_form; // 0：会員登録済みカード情報を使用、1：入力したカード情報を使用
				var cardNo = $("#js-cardNumber").val();
				var cardOwner = $("#js-cardOwner").val();
				var expDate =
					$("#ReservationCardExpirationYear").val() +
					$("#ReservationCardExpirationMonth").val();
				var payCnt = "00";
				var cvv2Code = $("#js-cvc").val();

				econapps.saveEntries(
					cardNo,
					cardOwner,
					expDate,
					payCnt,
					cvv2Code,
					usedCardFlg,
					function (result) {
						//console.log(result);
						if (result.status === 1) {
							//success
							$("#js-cardNumber").val("");
							$("#js-cardOwner").val("");
							$("#js-cvc").val("");

							if ($("#application_form").length) {
								$("#application_form").submit();
							}
						} else {
							sendLog({
								info: result.info,
								status: result.status,
							});
							createErrorMessageTag(
								result.info + "(" + result.status + ")"
							);
							$(".bg_orange").prop("disabled", false);
							hideLoading();
						}
					}
				);
			} else {
				sendLog({ info: result.info, status: result.status });
				createErrorMessageTag(
					result.info + "(" + result.status + ")"
				);
				$(".bg_orange").prop("disabled", false);
				hideLoading();
				//console.log(result.info + ", " + result.status);
			}
		});
	}

	function createErrorMessageTag(message) {
		$("#sessionMessage").empty();
		$("#sessionMessage").append(
			'<div class="session_message_wrap">' +
				'<p class="session_message"><i class="icm-warning"></i>' +
				message +
				"</p></div>"
			// '<div class=\'session-message\'><ul><li><strong>' +
			// 	message +
			// 	'</strong></li></ul></div>'
		);
		$(window).scrollTop(0);
	}

	const showLoading = () => {
		$("#js_loading_indicator").show();
		$("html, body").addClass("is_loading");
	};
	const hideLoading = () => {
		$("#js_loading_indicator").hide();
		$("html, body").removeClass("is_loading");
	};

	function sendLog(data) {
		$.ajax({
			type: "POST",
			dataType: "json",
			url: "/rentacar/reservations/store_log/",
			data: data,
		});
	}
});
