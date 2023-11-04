<section class="content_section pc_linklist_seo-region-order">
	<h2 class="title">全国の都道府県からレンタカーを予約</h2>
<?php 
	foreach($prefectureListGroupByRegion as $regionKey => $prefectures) { 
?>
	<div class="sub_title"><?= $regions[$regionKey]; ?></div>
	<ul class="pc_linklist_seo-region-order_body">
<?php 
		foreach($prefectures as $prefectureKey => $prefectureValue) { 
			$link_cd_str = 
				"/rentacar/" .
				($regionKey === $prefectureKey ? $prefectureKey : $regionKey . "/" . $prefectureKey) .
				"/";
?>
		<li class="pc_linklist_seo-region-order_item">
			<a href="<?= $link_cd_str ?>"><span><?= $prefectureValue ?></span></a>
		</li>
<?php 
		}
		if ((count($prefectures) % 2)) { // 最後行のborder-topを入れるため
?>
		<li class="pc_linklist_seo-region-order_item"></li>
<?php 
		} 
?>
	</ul>
<?php 
	}
?>
</section>