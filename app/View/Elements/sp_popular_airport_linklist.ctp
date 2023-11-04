<?php
	$popular_airport_list = Constant::popularAirportList();
?>

<section class="sp_linklist_seo-region-order">
	<h2 class="title">全国の人気の空港からレンタカーを予約</h2>
<?php 
	foreach($popular_airport_list as $items) { 
?>
	<div class="sub_title _accordion"><?= $items['area_name']; ?>の空港から探す</div>
	<ul class="sp_linklist_seo-region-order_body">
<?php 
		foreach($items['airport_list'] as $item) { 
?>
		<li class="sp_linklist_seo-region-order_item">
			<a href="<?= $item['airport_link'] ?>"><span><?= $item['airport_name'] ?></span></a>  
		</li>
<?php 
		}
?>
	</ul>
<?php
	} 
?>
</section>

<script>
let $_isAllOpen = true
</script>

<script>
const acc = document.getElementsByClassName("_accordion");
for (let i = 0; i < acc.length; i++) {
	acc[i].nextElementSibling.style.overflow = "hidden";
	acc[i].nextElementSibling.style.maxHeight = "0px";
	acc[i].nextElementSibling.style.transition = "max-height 0.2s ease-out";

	acc[i].addEventListener("click", function() {
		this.classList.toggle("active");
		const target = this.nextElementSibling;
		if (target.style.maxHeight && target.style.maxHeight !== "0px") {
			target.style.maxHeight = 0 + "px";
		} else {
			target.style.maxHeight = target.scrollHeight + "px";
		}
	});

	if(typeof $_isAllOpen !== 'undefined' && $_isAllOpen) {
		acc[i].click()
	}
}
</script>