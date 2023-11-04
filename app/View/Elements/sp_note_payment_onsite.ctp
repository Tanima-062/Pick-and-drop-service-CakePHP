<div class="note_payment_onsite">
	<h4 class="note_payment_onsite_header">ご予約時のお支払いはありません。</h4>
	<div class="note_payment_onsite_body">
		お支払いは当日、お受け取り手続きの際に<span>店舗で精算</span>させていただきます。<br />
<?php
	if ($commodityInfo['Client']['accept_cash'] == 1 && $commodityInfo['Client']['accept_card'] == 1) {
?>
		<span>クレジットカードまたは現金払い</span>がご利用いただけます。<br />
		※現金でお支払いの場合、運転免許証以外に本人確認書類の提示が必要となる場合がございます。<br />
		必ずお持ちください。
<?php
	} elseif ($commodityInfo['Client']['accept_cash'] == 1) {
?>
		<span>現金払いのみ</span>ご利用いただけます。<br />
		※お支払いの際に、運転免許証以外に本人確認書類の提示が必要となる場合がございます。<br />
		必ずお持ちください。
<?php
	} elseif ($commodityInfo['Client']['accept_card'] == 1) {
?>
		<span>クレジットカードのみ</span>ご利用いただけます。
<?php
	}
?>
	</div>
</div>