<?php
	// セルフチェックイン案内
	// プラン名にセルフチェックインが含まれる場合、かつ日産レンタカー(４６)の場合に表示
	// 表示条件をコンポーネント側で持つかどうか迷うところだけどやってみた。

	$isSelfcheckinPlan = strpos($planname, "セルフチェックイン");
	
	if (($isSelfcheckinPlan !== false) && ($clientid == '46')){
?>

<section class="note-self-checkin-wrap">
	<div class="-heading">セルフチェックインのお願い</div>
	<p class="-content">
		このプランのご利用には、事前に専用URLからのセルフチェックインが必要となります。<br>
		詳しくはレンタカー会社よりメールでご案内させていただきますので、余裕をもって手続きをお済ませくださいますよう、よろしくお願い致します。
	</p>
</section>

<?php
	}
?>