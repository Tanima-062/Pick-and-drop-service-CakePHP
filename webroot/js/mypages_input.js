// Reservations/input.ctp
$(function () {
	$(document).on("click", "#submitButton", function () {
		$("#sessionMessage").empty();
		if ($(this).attr("type") == "submit") {
			return;
		}

		showLoading();

		$("#submitButton").prop("disabled", true);
		setTimeout(() => {
			$("#submitButton").prop("disabled", false);
		}, 5000);

		$.ajax({
			type: "POST",
			dataType: "json",
			timeout: 5000,
			url: "/rentacar/mypages/prepare_payment/",
		})
			.done(function (res) {
				if (res.msg.length > 0) {
					setTimeout(() => {
						location.href =
							location.protocol +
							"//" +
							location.hostname +
							"/rentacar/mypages/";
					}, 2000); // 2秒後にトップへリダイレクト
				}

				initEcon(res.session_token, res.request_id, 1);
			})
			.fail(function () {
				hideLoading();
				$("#submitReserve").prop("disabled", false);
				setTimeout(() => {
					location.href =
						location.protocol +
						"//" +
						location.hostname +
						"/rentacar/mypages/";
				}, 2000); // 2秒後にトップへリダイレクト
			});
	});

	function initEcon(session_token, request_id, use_form) {
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
					$("#cardCreditExpirationYear").val() +
					$("#cardCreditExpirationMonth").val();
				var payCnt = "00";
				var cvv2Code = $("#js-cvc").val();

				$("#js-cardNumber").val("");
				$("#js-cardOwner").val("");
				$("#js-cvc").val("");

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
							econapps.goPayment(function (result) {
								if (result.status != 1) {
									createErrorMessageTag(
										result.info + "(" + result.status + ")"
									);
								}
							});
						} else {
							createErrorMessageTag(
								result.info + "(" + result.status + ")"
							);
							$("#submitButton").prop("disabled", false);
							hideLoading();
						}
					}
				);
			} else {
				createErrorMessageTag(result.info + "(" + result.status + ")");
				$("#submitButton").prop("disabled", false);
				hideLoading();
			}
		});
	}

	// カード番号
	$("#js-cardNumber").on("blur", function () {
		var inputValue = $(this).val();
		if (
			!inputValue.match(/^4\d{12}(\d{3})?$/) && // visa
			!inputValue.match(/^5[1-5]\d{14}$/) && // mc
			!inputValue.match(/^(3\d{4}|2100|1800)\d{11}$/) && // jcb
			!inputValue.match(/^3[4|7]\d{13}$/) && // amex
			!inputValue.match(/^(?:3(0[0-5]|[68]\d)\d{11})|(?:5[1-5]\d{14})$/)
		) {
			// diners
			$(this).addClass("has-error");
			$(this).next(".error_message").remove();
			$(this).after(
				'<p class="error_message text_danger">カード番号を入力してください</p>'
			);
		} else {
			$(this).removeClass("has-error");
			$(this).next(".error_message").remove();
		}
	});

	// カード名義
	$("#js-cardOwner").on("blur", function () {
		var inputValue = $(this).val();
		if (!inputValue.match(/^([A-Z]|\s)+$/)) {
			$(this).addClass("has-error");
			$(this).next(".error_message").remove();
			$(this).after(
				'<p class="error_message text_danger">カード名義を入力してください</p>'
			);
		} else {
			$(this).removeClass("has-error");
			$(this).next(".error_message").remove();
		}
	});

	// セキュリティコード
	$("#js-cvc").on("blur", function () {
		var inputValue = $(this).val();
		if (!inputValue.match(/^(\d{3}|\d{4})$/)) {
			$(this).addClass("has-error");
			$(this).next(".error_message").remove();
			$(this).after(
				'<p class="error_message text_danger">セキュリティコードを入力してください</p>'
			);
		} else {
			$(this).removeClass("has-error");
			$(this).next(".error_message").remove();
		}
	});

	function createErrorMessageTag(message) {
		$("#sessionMessage").empty();
		$("#sessionMessage").append(
			'<div class="session_message_wrap">' +
				'<p class="session_message"><i class="icm-warning"></i>' +
				message +
				"</p></div>"
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
});
