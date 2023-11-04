<section class="sp_linklist_seo-region-order">
	<h2 class="title">全国の都道府県からレンタカーを予約</h2>
<?php 
	foreach($prefectureListGroupByRegion as $regionKey => $prefectures) { 
?>
	<div class="sub_title _accordion"><?= $regions[$regionKey]; ?></div>
	<ul class="sp_linklist_seo-region-order_body">
<?php 
		foreach($prefectures as $prefectureKey => $prefectureValue) { 
			$link_cd_str = 
				"/rentacar/" .
				($regionKey === $prefectureKey ? $prefectureKey : $regionKey . "/" . $prefectureKey) .
				"/";
?>
		<li class="sp_linklist_seo-region-order_item">
			<a href="<?= $link_cd_str ?>"><span><?= $prefectureValue ?></span></a>
		</li>
<?php 
		}
?>
	</ul>
<?php
	}
?>
</section>
