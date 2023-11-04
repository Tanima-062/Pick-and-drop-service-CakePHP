<?php // plan, step1 ?>
<p class="notes_onsite">
	<span class="notes_onsite_head">ご予約時のお支払いはありません</span><br />
	お支払いは当日、お受取り手続きの際に<strong>店舗で精算</strong>させていただきます。<br />
<?php
	if ($acceptCash == 1 && $acceptCard == 1) {
?>
		<strong>クレジットカードまたは現金</strong>がご利用頂けます。<br />
		※現金でお支払いの場合、運転免許証以外に本人確認書類の提示が必要となる場合がございます。<br />
		必ずお持ちください。
<?php
	} elseif ($acceptCash == 1) {
?>
		<strong>現金のみ</strong>ご利用頂けます。<br />
		※お支払いの際に、運転免許証以外に本人確認書類の提示が必要となる場合がございます。<br />
		必ずお持ちください。
<?php
	} elseif ($acceptCard == 1) {
?>
		<strong>クレジットカードのみ</strong>ご利用頂けます。
<?php
	}
?>
</p>
