<!-- プラン情報ブロック -->
<!-- Plan, step1, step2 -->
<section class="plan_info_block plan_view">
	<h3 class="plan_info_block_head">
		<div class="plan_contents_list_shop_name">
			<?php echo $commodityInfo['Client']['name']; ?>
		</div>
	</h3>
	
	<div class="plan_info_block_body">
		<div class="plan_contents_list_left">
<?php
	/*$imageRelativeUrl = !empty($commodityImages[0]['image_relative_url']) ?
		'/img/commodity_reference/' . $commodityInfo['Client']['id'] . '/' . $commodityImages[0]['image_relative_url'] :
		'/img/noimage.png';*/
	$imageRelativeUrl = !empty($commodityInfo['Commodity']['image_relative_url']) ?
		'/img/commodity_reference/' . $commodityInfo['Client']['id'] . '/' . $commodityInfo['Commodity']['image_relative_url'] :
		'/img/noimage.png';

	echo $this->Html->image($imageRelativeUrl, array('width' => '268', 'height' => 'auto', 'class' => 'plan_contents_img', 'alt' => ''));
?>
		</div>
		<div class="plan_contents_list_center">
			<p class="plan_contents_name_wrap">
<?php
	foreach ($commodityInfo['CarModel'] as $key => $carModel) {
		if ($carModel === reset($commodityInfo['CarModel'])) {
			$carModels = $carModel['name'];
		} else {
			$carModels .= '・'.$carModel['name'];
		}
	}
	$link_name = $commodityInfo['CarType']['name'] .'（'. $carModels;
	// 車種指定フラグ
	$flgModelSelect = ( !empty( $commodityInfo['CommodityItem']['car_model_id']) );
	( $flgModelSelect ) ? $link_name .= '）' : $link_name .= '他）';
?>
				<span class="plan_contents_name"><?=$link_name;?></span>
			</p>
			<ul class="plan_car_spec_ul">
				<li class="plan_car_spec_li">
<?php
	// 喫煙・禁煙
	$smokingCarString = $smokingCarList[$commodityInfo['Commodity']['smoking_flg']];
	if($commodityInfo['Commodity']['smoking_flg'] == 0){
?>
					<p class="plan_car_spec is_no_smoking">
						<i class="icm-no_smoking"></i> <?=$smokingCarString;?>
					</p>
<?php
	}else if($commodityInfo['Commodity']['smoking_flg'] == 1){
?>
					<p class="plan_car_spec is_smoking">
						<i class="icm-smoking"></i> <?=$smokingCarString; ?>
					</p>
<?php
	}
?>
				</li>
				<li class="plan_car_spec_li">
					<p class="plan_car_spec">
<?php
	// 定員
	$recommendedCapacity = Hash::extract($commodityInfo['CarModel'], '{n}.capacity');
	echo '定員'.$recommendedCapacity[0].'名';
?>
					</p>
				</li>
				<li class="plan_car_spec_li">
					<p class="plan_car_spec is_car_model <?php if(!$flgModelSelect){ ?> is_inactive<?php } ?>"><i class="icm-car-side"></i> 車種指定</p>
				</li>
				<li class="plan_car_spec_li">
					<p class="plan_car_spec is_new_car <?php if($commodityInfo['Commodity']['new_car_registration'] != 1 &&  $commodityInfo['Commodity']['new_car_registration'] != 2){ ?> is_inactive<?php } ?>"><i class="icm-sparkle"></i> 新車</p>
				</li>
			</ul>
			
			<ul class="plan_equipment_ul">
				<li class="plan_equipment_li is_active">
					<p>免責補償</p>
					<aside class="plan_equipment_aside">免責補償料金込みプラン</aside>
				</li>
<?php
	foreach ($equipmentList as $equipmentId => $equipment) {
		$equipment = $equipment['Equipment'];
		if (!empty($commodityEquipment[$equipment['id']])) {
?>
				<li class="plan_equipment_li is_active">
					<p><?=$equipment['name']; ?></p>
					<aside class="plan_equipment_aside"><?=$equipment['description']; ?></aside>
				</li>
<?php
		}else{
?>
				<li class="plan_equipment_li">
					<p><?=$equipment['name']; ?></p>
				</li>
<?php
		}
	}
?>
<?php
				if ($commodityInfo['Commodity']['transmission_flg'] == 0) {
?>
				<li class="plan_equipment_li is_active">
					<p>AT車</p>
					<aside class="plan_equipment_aside">
						<p class="plan_equipment_description">オートマチックトランスミッションの車です</p>
					</aside>
				</li>
<?php
				}else if ($commodityInfo['Commodity']['transmission_flg'] == 1){
?>
				<li class="plan_equipment_li">
					<p>AT車</p>
				</li>
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
				<a href="javascript:void(0);" class="js-modalOpen modal-open" data-code="<?= $commodityInfo['CommodityItem']['id']; ?>"><?= $planName; ?></a>
			</div>
<?php
	}
?>

		</div>
		<div class="plan_contents_list_right">
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

<?php
	if(isset($estimationTotalPrice)){
		$labelPrice = "料金";
		$price = $estimationTotalPrice;
	} else {
		$labelPrice = "基本料金";
		$price = $basicCharge;
	}
?>
			<p class="plan_contents_price_title"><?= $labelPrice; ?> [<?= $rentalPeriod; ?>]</p>
			<p class="plan_contents_price">&yen;<?php echo number_format($price); ?><span>(税込)</span></p>
		</div>
	</div>
</section>
