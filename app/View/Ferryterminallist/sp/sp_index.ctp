<section class="listpage-heading-section">
	<h1 class="listpage-title">フェリーターミナル一覧からレンタカーを探す</h1>
<?php   
	if(!empty($ferryTerminalDataArrayTop)){
    	foreach($ferryTerminalDataArrayTop as $value){
?>
	<section class="listpage-heading-content">
		<h2 class="-header"><?= $value['head']; ?></h2>
		<p class="-text"><?= $value['text']; ?></p>
	</section>
<?php
		}
	}
?>
</section>

<div class="common-accordion-wrap">
	<div class="common_accordion">
<?php
	foreach($landmarkList['airportArray'] as $prefectures => $terminal_prefectures){
?>
		<div class="loop_div">
			<h3 class="js-accordion-header prefectures_title"><?= $prefectures; ?>の港から探す</h3>
			<ul class="js-accordion-inner sub_menu ferryterminal_list">
<?php
		foreach($terminal_prefectures as $airport_id => $terminal){
?>
				<li>
					<span>
<?php
			if(!empty($terminalLinkCdList[$airport_id])) {
?>
						<a href="/rentacar/<?= $base_url_arr[$prefectures] . $terminalLinkCdList[$airport_id]?>/"><?= $terminal ?></a>
<?php
			} else {
?>
						<a href="/rentacar/searches?place=3&airport_id=<?=$airport_id;?>&_submit=&year=<?=$link_date_arr['year'];?>&month=<?=$link_date_arr['month'];?>&day=<?=$link_date_arr['day'];?>&time=11-00&return_way=0&_submit=&return_year=<?=$link_date_arr['year'];?>&return_month=<?=$link_date_arr['month'];?>&return_day=<?=$link_date_arr['day'];?>&return_time=17-00&adults_count=2&children_count=&infants_count="><?= $terminal ?></a>
<?php
			}
?>
					</span>
				</li>
<?php
		}
?>
			</ul>
		</div>
<?php
	}
?>
	</div>
</div>

<div class="img_caption_wrap">
	<div class="img_caption_contents">
<?php
	if(!empty($ferryTerminalDataArrayBottom)){
		foreach($ferryTerminalDataArrayBottom as $value){
?>
		<h2 class="img_caption_btn"><?= $value['head']; ?></h2>
		<div class="img_caption_detail">
			<p><?= $value['text']; ?></p>
		</div>
<?php
		}
	}
?>
	</div>
</div>

<?php echo $this->element("sp_sidebar"); ?>

<script>
$(function() {
	$(".img_caption_btn").click(function(){
		if($(this).hasClass("open")){
			$(this).removeClass("open");
		}else{
			$(this).addClass("open");
		}
	});
});
</script>
