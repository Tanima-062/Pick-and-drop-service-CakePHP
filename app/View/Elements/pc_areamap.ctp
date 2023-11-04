<?php
	$padPrefId = sprintf('%02d', $prefectureId);
?>

<section class="areamap_block_wrap">
	<h3 class="pref_map_subtitle">
		<?=$prefectureName;?>のエリアからレンタカーを探す
	</h3>

	<div id="js_area_map" class="prefecture_map">
		<img src="/rentacar/img/map/map_base_<?=$padPrefId;?>.png" alt="<?=$alt_text;?>の地図" width="540" height="400" usemap="#image_mapMap" id="image_map" loading="lazy" importance="low" decoding="async"/>
<?php
	// ここにmapデータが呼び出される
	echo $this->element('Areamap/map_'.$padPrefId);
?>
		<ul class="pref_map_facility_ul">
<?php
	foreach($airportLinkCdList as $k => $v) {
		$padAirportId = sprintf('%05d', $v['id']);
?>
			<li class="pref_map_facility_li map_airport_<?=$padAirportId;?>">
				<a href="/rentacar/<?= $baseUrl . $k?>/"><?=$v['short_name'];?></a>
			</li>
<?php
	}
?>
		</ul>
		<ul class="pref_map_facility_ul">
<?php
	foreach($mapStationList as $mapStationData) {
		$padMapStationId = sprintf('%05d', $mapStationData['id']);
?>
			<li class="pref_map_facility_li map_station_<?= $padMapStationId; ?>">
				<a href="/rentacar/<?= $baseUrl . $mapStationData['url']?>/"><?= $mapStationData['name'] . $mapStationData['type']; ?></a></span>
			</li>
<?php
	}
?>
		</ul>
		<ul class="pref_map_area_ul">
<?php
	foreach($areaList as $areaId => $areaData) {
		$padAreaId = sprintf('%03d', $areaId);
?>

			<li class="pref_map_area_li map_area_<?= $padAreaId; ?>">
				<a href="/rentacar/<?= $baseUrl . $areaData['area_link_cd']?>/" onmouseover="changeMapImage('/rentacar/img/map/map_on_<?= $padAreaId; ?>.png')" onmouseout="changeMapImage('/rentacar/img/map/map_base_<?= $padPrefId; ?>.png')"><?= $areaData['name']; ?></a>
			</li>
<?php
	}
?>
		</ul>
		<ul class="pref_map_near">
<?php
	foreach($neighborhoodPrefectureList as $nearPrefData) {
		$padNearPrefId = sprintf('%02d', $nearPrefData['id']);
		$allNearPrefUrl = $nearPrefData['region_link_cd']."/".$nearPrefData['link_cd'];
		if($nearPrefData['link_cd'] === "hokkaido"){
			$allNearPrefUrl = $nearPrefData['link_cd'];
		}
?>
			<li class="pref_map_near_li map_near_<?= $padPrefId .'_'. $padNearPrefId; ?>">
				<a href="/rentacar/<?= $allNearPrefUrl; ?>/"><?= $nearPrefData['name']; ?></a>
			</li>
<?php
	}
?>
		</ul>
	</div>

	<div class="major_facility">
<?php
	if( !empty($airportLinkCdList) ){
?>
		<h3 class="pref_map_subtitle"><?=$prefectureName;?>の空港からレンタカーを探す</h3>

		<ul class="link_cont_ul">
<?php
		foreach($airportLinkCdList as $k => $v) {
?>
			<li class="link_cont_li">
				<i class="fa fa-caret-right"></i>&nbsp;<a href="/rentacar/<?= $baseUrl . $k?>/"><span><?= $v['name'] ?></span></a>
			</li>
<?php
		}
?>
		</ul>
<?php
	}
	if( !empty($majorStationList) ){
?>
		<h3 class="pref_map_subtitle"><?=$prefectureName;?>の主要駅からレンタカーを探す</h3>

		<ul class="link_cont_ul">
<?php
		foreach($majorStationList as $majorStationData) {
?>
			<li class="link_cont_li">
				<i class="fa fa-caret-right"></i>&nbsp;<a href="/rentacar/<?= $baseUrl . $majorStationData['url']?>/"><span><?= $majorStationData['name'] . $majorStationData['type']; ?></span></a>
			</li>
<?php
		}
?>
<?php
		if ($this->params['controller'] === 'prefectures') {
?>
			<li class="link_cont_li link_bottom">
				<i class="fa fa-caret-down"></i>&nbsp;<a href="#js_station_list">全ての駅から探す</a>
			</li>
<?php
		}
?>
		</ul>
<?php
	}
?>
	</div>

<?php
	if (!empty($prefectureData[0]['pre-head-text'])) {
?>
	<p class="areamap_block_text_wrap"><?=$prefectureData[0]['pre-head-text']?></p>
<?php
	}
?>
</section>

<script>
$(function(){
	// area範囲押下時
	$("area").on("click", function(){
		var dataAreaId = $(this).data("area");
		if( dataAreaId != ""){
			location.href = $(".map_area_"+dataAreaId+" > a").attr("href");
		}
	});
});
function changeMapImage(imgPath) {
	document.getElementById('image_map').src = imgPath;
}
</script>
