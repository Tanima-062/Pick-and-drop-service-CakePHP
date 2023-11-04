// Mypages/cancel.ctp

$(function () {
	const notice_str =
		'<br />' + 'キャンセルポリシーに基づき、キャンセル料が必要となります。';
	$('#notice_reason').html(notice_str);
	$(document).on(
		'change',
		'[name="data[Reservation][cancel_reason_id]"]',
		function () {
			const cancelId = $(this).val();

			if (cancelId == 1) {
				const notice_str =
					'<br />' +
					'キャンセルポリシーに基づき、キャンセル料が必要となります。';
				$('#notice_reason').html(notice_str);
			} else if (cancelId == 2) {
				const notice_str =
					'<br />' +
					'キャンセル料は頂戴いたしません。' +
					'<br />' +
					'※台風等による欠航証明書をお持ちの場合は、お手数ですが別途メールにてスカイチケットレンタカーサポートセンター（rentacar@skyticket.com）まで、予約番号と氏名をを添えてご連絡ください。';
				$('#notice_reason').html(notice_str);
			} else if (cancelId == 3) {
				const notice_str =
					'<br />' +
					'キャンセル料は頂戴いたしません。' +
					'<br />' +
					'※下欄に新しい予約の予約番号をご入力ください。スカイチケットレンタカーにて、新たに同じレンタカー会社のレンタカーの予約が確認できた場合、キャンセル料は頂戴いたしません。スカイチケット以外の予約サイト及びレンタカー会社へ直接ご予約のお取り直しをされた場合には適用できませんので、ご注意ください。';
				$('#notice_reason').html(notice_str);
			} else {
				$('#notice_reason').html('');
			}
		}
	);
});
