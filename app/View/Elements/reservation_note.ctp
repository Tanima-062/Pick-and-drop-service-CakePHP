<!-- PC/SP共通 -->
<!-- 予約ボタン直前の確認事項（利用規約とプライバシーポリシーを確認してください） -->
<?php
	$next = '';
	if ($this -> here === '/rentacar/reservations/step1/') {
		$next = '予約を完了してください';
	} elseif ($this -> here === '/rentacar/reservations/step2/') {
		$next = 'お支払いへお進みください';
	}
?>
<section class="reservation_note_wrap">
	<p class="reservation_note_title">確認事項</p>
	<p class="reservation_note_content"><a target="_blank" href="/info/terms">利用規約</a>、<a target="_blank" href="/info/privacy">プライバシーポリシー</a>、キャンセルポリシーにご同意の上、<?= $next; ?>。</p>
</section>