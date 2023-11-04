<?php
	$popular_airport_list = Constant::popularAirportList();
?>

<section class="content_section pc_linklist_seo-region-order">
	<h2 class="title">全国の人気の空港からレンタカーを予約</h2>
<?php 
	foreach($popular_airport_list as $items) { 
?>
	<div class="sub_title"><?= $items['area_name']; ?>の空港から探す</div>
	<ul class="pc_linklist_seo-region-order_body">
<?php 
		foreach($items['airport_list'] as $item) { 
?>
		<li class="pc_linklist_seo-region-order_item">
			<a href="<?= $item['airport_link'] ?>"><span><?= $item['airport_name'] ?></span></a>  
		</li>
<?php 
		}
		if ((count($items['airport_list']) % 2)) { // 最後行のborder-topを入れるため
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