<!-- プラン情報ブロック -->
<!-- Plan, step1, step2 -->
<section class="plan_info_block plan_view">
	<div class="plan_info_block_head">
		<h2 class="plan_shop_name">
			<?php echo $commodityInfo['Client']['name']; ?>
		</h2>
	</div>

	<div class="plan_info_block_body">
		<h3 class="plan_caption">
<?php
	// 定員
	$recommendedCapacity = Hash::extract($commodityInfo['CarModel'], '{n}.capacity');
	//推奨荷物数
	$packageNum = Hash::extract($commodityInfo['CarModel'], '{n}.package_num');

	foreach ($commodityInfo['CarModel'] as $key => $carModel) {
		if ($carModel === reset($commodityInfo['CarModel'])) {
			$carModels = $carModel ['name'];
		} else {
			$carModels .= " ".$carModel ['name'];
		}
	}
	$commodityName = $commodityInfo['CarType']['name'].' ('.$carModels;
	// 車種指定フラグ
	$flgModelSelect = ( !empty( $commodityInfo['CommodityItem']['car_model_id']) );
	( $flgModelSelect ) ? $commodityName .= '）' : $commodityName .= '他）';

	echo $commodityName;
?>
		</h3>
		<ul class="plan_detail_ul">
			<li class="plan_detail_li car_photo">
				<div class="car_photo_wrap">

<?php
	/*$imageRelativeUrl = !empty($commodityImages[0]['image_relative_url']) ?
		'/img/commodity_reference/' . $commodityInfo['Client']['id'] . '/' . $commodityImages[0]['image_relative_url'] :
		'/img/noimage.png';*/
	$imageRelativeUrl = !empty($commodityInfo['Commodity']['image_relative_url']) ?
		'/img/commodity_reference/' . $commodityInfo['Client']['id'] . '/' . $commodityInfo['Commodity']['image_relative_url'] :
		'/img/noimage.png';

	echo $this->Html->image($imageRelativeUrl, array('class' => 'plan_car_photo', 'alt' => $carModel));
?>
				</div>
			</li>
			<li class="plan_detail_li">
				<div class="plan_detail_topics">
					<p class="capacity">定員：<?php echo $recommendedCapacity[0]; ?>名</p>
				</div>
				<ul class="plan_detail_spec">
<?php
$flgNewRegistration = ( $commodityInfo['Commodity']['new_car_registration'] == 1 || $commodityInfo['Commodity']['new_car_registration'] == 2 );

if($commodityInfo['Commodity']['smoking_flg'] == 0){
?>
					<li><i class="icm-no_smoking icon no-smoking"></i>禁煙車</span></li>
<?php
} else if($commodityInfo['Commodity']['smoking_flg'] == 1){
?>
					<li><i class="icm-smoking icon"></i>喫煙可</span></li>
<?php
}
if($flgModelSelect){
?>
					<li><i class="icm-car-side icon"></i>車種確約</span></li>
<?php
}
if($flgNewRegistration){
?>
					<li><i class="icm-sparkle icon"></i>新車</span></li>
<?php
}
?>
				</ul>
			</li>

		</ul>
		<ul class="plan_equipment_ul">
			<li class="plan_equipment_li">免責補償</li>
<?php
	foreach ($equipmentList as $equipmentId => $equipment) {
		$equipment = $equipment['Equipment'];
		if (!empty($commodityEquipment[$equipment['id']])) {
?>
			<li class="plan_equipment_li"><?php echo $equipment['name']; ?></li>
<?php
		}
	}
?>


<?php
		if ($commodityInfo['Commodity']['transmission_flg'] == 0) {
?>
			<li class="plan_equipment_li is_active">AT車</li>
<?php
		}
?>

		</ul>

<?php
	// お見積りページでのみプラン詳細リンクを表示する
	$url = $_SERVER['REQUEST_URI'];
	if (strstr($url, '/rentacar/plan/')) {
		$planName = $commodityInfo['Commodity']['name'];

		$CommodityItemid = $commodityInfo['CommodityItem']['id'];
?>
		<div class="plan_contents_list_plandetail">
			<a href="javascript:void(0);" class="js-open-planview open-planview" data-code="<?= $commodityInfo['CommodityItem']['id']; ?>"><?= $planName; ?></a>
		</div>
<?php
	}
?>
	</div>

	<div class="plan_info_block_bottom">
		<div class="plan_info_block_bottom_left">
		
<?php
	if(isset($estimationTotalPrice)){
		$labelPrice = "料金";
		$price = $estimationTotalPrice;
	} else {
		$labelPrice = "基本料金";
		$price = $basicCharge;
	}
?>
			<div class="payment_labels">
				<p class="menseki_label">免責補償込み</p>

<?php //決済種別
	$payment_method = $commodityInfo['Commodity']['payment_method'];
	if(!is_null($payment_method)){
		switch ($payment_method){
		case '0':
			echo '<p class="payment_type">現地決済</p>';
			break;
		case '1':
			echo '<p class="payment_type">WEB決済限定料金</p>';
			break;
		case '2':
			echo '<p class="payment_type">WEB決済/現地決済</p>';
			break;
		default:
		}
	};
?>
			</div>
			<ul class="plan_price_ul">
				<li class="plan_price_li">
					<p><?php echo $labelPrice ?> <br>[<?php echo $rentalPeriod; ?>]</p>
				</li>
				<li class="plan_price_li plan_price">
					<span>&yen;<?php echo number_format($price); ?></span>
					<span>&nbsp;(税込)</span>
				</li>
			</ul>
		</div>
	</div>
</section>
<!-- /plan_info_block -->
