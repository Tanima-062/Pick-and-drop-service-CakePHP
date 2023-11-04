<?php // PC&SP 共通 ?>

<div class="hints_wrap">
	<h3 class="">検索のヒント</h3>
	<h4>出発・返却の時間は、店舗の営業時間と合っていますか？</h4>
	<ul>
<?php 
	if(!empty($earliestOffice) && !empty($latestOffice)) {
		$earliest_from = date('H:i',strtotime($earliestOffice[$identifier.'_hours_from']));
		$earliest_to = date('H:i',strtotime($earliestOffice[$identifier.'_hours_to']));
		$latest_from = date('H:i',strtotime($latestOffice[$identifier.'_hours_from']));
		$latest_to = date('H:i',strtotime($latestOffice[$identifier.'_hours_to']));
?>
		<li>
<?php
		if ($earliest_from == $latest_from && $earliest_to == $latest_to) {
?>
			<?= $searchPlace ?>周辺のレンタカー店舗の平均営業時間は<b><?= date('H:i',strtotime($earliestOffice[$identifier.'_hours_from'])); ?>〜<?= date('H:i',strtotime($earliestOffice[$identifier.'_hours_to'])); ?></b>です。
<?php
		} else {
?>
			<?= $searchPlace ?>周辺のレンタカー店舗の営業時間は、一番開店が早い店舗で<b><?= date('H:i',strtotime($earliestOffice[$identifier.'_hours_from'])); ?>〜<?= date('H:i',strtotime($earliestOffice[$identifier.'_hours_to'])); ?></b>、一番閉店が遅い店舗で<b><?= date('H:i',strtotime($latestOffice[$identifier.'_hours_from'])) ?>〜<?= date('H:i',strtotime($latestOffice[$identifier.'_hours_to'])) ?></b>です。
<?php
		}
?>
		</li>
<?php 
	} 

	if(!empty($minDeadlineHour)) { 
?>
		<li>
			このサイトからは、出発時間の<b><?= $minDeadlineHour ?>時間前</b>まで予約が可能です。
		</li>
<?php 
	} 

	if (!$fromRentacarClient) { 
?>
		<li>
			<a href="/rentacar/<?= $departureLink ?>" class="a-link" target="_blank">周辺のレンタカー店舗の営業時間を確認する</a>
		</li>
<?php 
	}
?>
	</ul>

	<h4>乗り捨て利用でなければ予約できるかもしれません</h4>
	<div>
		営業所の大きさ・立地などの理由によって、乗り捨て可能な車両クラスが制限されている場合があります。<br>
<?php	
	if (!$fromRentacarClient) { 
?>
		電車、<a href="/bus/" target="_blank" class="a-link">高速バス</a>、<a href="/ferry/" target="_blank" class="a-link">フェリー</a>などの利用もあわせてご検討ください。
<?php 
	} 
?>
	</div>

<?php 
	if(!empty($otherAreas)){
?>
	<h4>周辺のエリアならまだ予約できます！</h4>
	<div>
		乗り捨て利用の場合は、返却エリアを設定して再検索してください。
		<div class="suggests_btns">
<?php 
		foreach($otherAreas as $otherArea){
?>
			<a href="/rentacar/searches?<?= $otherArea['query']?>" class="btn-type-link">
				<?= $otherArea['name'] ?>
				<span>（<?=number_format($otherArea['price'])?>円〜）</span>
			</a>
<?php
		}
?>
		</div>
	</div>
<?php
	}
?>

</div>