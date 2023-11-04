<?php 
	foreach($commodities as $commodity) {
		$commodityItemId = $commodity['CommodityItem']['id'];
		$commodityId = $commodity['Commodity']['id'];
		$clientId = $commodity['Commodity']['client_id'];
		$planName = $commodity['Commodity']['name'];

		$capacity = 0;
		$recommendedCapacity = 0;
		$packageNum = 0;

		//車種
		$carType = '';
		if(!empty($carInfoList[$commodityItemId]['CarType']['name'])) {
			$carType = $carInfoList[$commodityItemId]['CarType']['name'];
		}
		$flgModelSelect = ( !empty( $commodity['CommodityItem']['car_model_id']) );

		//参考モデル
		$carModel = '';
		if(!empty($carInfoList[$commodityItemId]['CarModel'])) {
			$carModeLists = Hash::extract($carInfoList[$commodityItemId]['CarModel'],'{n}.name');
			if(!empty($carModeLists)) {
				$carModel = implode($carModeLists,'・');
				foreach ($carModeLists as $i => $carModelName) {
					if($i == 0){
						$carModels = $carModelName;
					} else {
						$carModels .= "・".$carModelName;
					}
				}
			}

			//定員
			if(!empty($carInfoList[$commodityItemId]['CarModel'])) {
				$capacity = Hash::get($carInfoList[$commodityItemId]['CarModel'],'0.capacity');
			}

			//推奨人数
			if(!empty($carInfoList[$commodityItemId]['CarModel'])) {
				$recommendedCapacity = Hash::get($carInfoList[$commodityItemId]['CarModel'],'0.recommended_capacity');
			}

			//推奨荷物数
			if(!empty($carInfoList[$commodityItemId]['CarModel'])) {
				$packageNum = Hash::get($carInfoList[$commodityItemId]['CarModel'],'0.package_num');
			}
		}

		//受け付け締め切り時間
		//$deadlineTimeStamp = $commodity['CommodityTerm']['deadline'];
		//$timeDiff = abs($deadlineTimeStamp - time());
		//$dayDiff = round($timeDiff / (60 * 60));
		//$dayDiffDay = round($timeDiff / (60 * 60 * 24));
		//$deadline = date('Y/m/d H:i',$deadlineTimeStamp) . 'まで';
		//if (isset($commodity['CommodityTerm']['deadline_hours']) && $commodity['CommodityTerm']['deadline_hours'] < 24) {
		//		$deadline = '受取時間の' . $commodity['CommodityTerm']['deadline_hours'] . '時間前まで';
		//} else if ($dayDiffDay >= 1) {
		//	$deadline .= ' 残り' . $dayDiffDay . '日';
		//}

		//レンタル期間
		if (!empty($commodity['Commodity']['day_time_flg'])) {
			$dayCount = $rentalTime . '時間';
		} else {
			$dayCount = '日帰り';
			if($commodity['CarClassStock']['day_count'] != 1) {
				$dayCount = ($commodity['CarClassStock']['day_count'] - 1) . '泊' . $commodity['CarClassStock']['day_count'] . '日';
			}
		}

		//車両年式
		$flgNewRegistation = ($commodity['Commodity']['new_car_registration'] == 1 || $commodity['Commodity']['new_car_registration'] == 2);
		//$newCarRegistration = '';
		//if(!empty($commodity['Commodity']['new_car_registration']) && !empty($newCarRegistrationList[$commodity['Commodity']['new_car_registration']])) {
		//	$newCarRegistration = '（' .$newCarRegistrationList[$commodity['Commodity']['new_car_registration']] .'）';
		//}

		//ソート順の一番上の営業所
		$topOffice = $rentOfficeList[$commodityId][0];
		$rentOfficeNameNotTopArray = array();
		if (count($rentOfficeList[$commodityId])>0) {
			foreach ($rentOfficeList[$commodityId] AS $rentOfficeDataKey => $rentOfficeData) {
				if (!empty($rentOfficeDataKey)) { // $rentOfficeDataKey == 0 除く（一番始めの店舗）
					$rentOfficeNameNotTopArray[] = $rentOfficeData;
				}
			}
		}
?>
<div class="plan_info_block search_result_box<?php if($commodity['pr']){?> recommend_plan<?php } ?>">

<?php
		if($commodity['pr']){
?>
	<div class="recommend_label">PR</div>
<?php
		}
?>
	<div class="plan_info_block_head">
<?php
		if (!empty($hokkaidoCampaignFlg) && in_array($commodity['Commodity']['client_id'], $hokkaidoCampaignTargetClientIds)) {
?>
		<div class="plan_contents_list_head_campaign-logo">
			<img src="/rentacar/img/logo/icon/campaign_hokkaidrive2022_logo.svg" width="32" height="32"/>
		</div>
<?php
		}
?>
		<div class="plan_contents_list_head_main">
	
			<div class="plan_contents_list_head_top">
				<div class="plan_contents_list_shop_name">
					<?= $clientList[$commodity['Commodity']['client_id']]['name']; ?>
				</div>
<?php
		// ADVPRO-6196 ABtest
		if (!empty($topOffice['airport_transfer_service'])) {
?>
				<span class="label-transfer"><i class="icm-airplane"></i>空港送迎あり</span>
<?php
		}
?>
			</div><!-- //plan_contents_list_head_top -->

			<div class="plan_contents_list_head_middle">
				<div class="plan_contents_list_head_middle_left">
					<div class="plan_contents_list_office_name"><?= $topOffice['name']; ?></div>
<?php			
		if (!empty($rentOfficeNameNotTopArray)) {
?>
					<div class="modal_float_wrap">
						<div class="btn_modalf_open_wrap">
							<a href="javascript: void(0);" class="js-modalf_open btn_modal_open">その他<?= count($rentOfficeNameNotTopArray); ?>店舗</a>
							<i class="icm-right-arrow"></i>
						</div>
						<div class="js-modalf_overlay modalf-overlay"></div>
						<div class="js-modalf-window modalf-window">
							<section class="modal_contents_wrap">
								<div class="js-modalf_close btn-close">
									<i class="icm-modal-close"></i>
								</div>
								<div class="modal-title">同じプランがある周辺店舗</div>
								<div class="modal_contents">
								次のページで在庫確認と選択ができます。
									<ul>
<?php
			foreach ($rentOfficeNameNotTopArray AS $rentOfficeNameNotTopKey => $rentOfficeNameNotTop) {
?>
										<li><?= $rentOfficeNameNotTop['name']; ?></li>
<?php
			}
?>
									</ul>
								</div>
							</section>
						</div>
					</div>
<?php
		}
?>
				</div>

				<div class="plan_contents_list_head_middle_right">
					<div class="plan_contents_list_yotpo">
<?php
		if($yotpo_is_active && $use_yotpo){
			$rating_avg = '';
			$rating_count = '';
			$client_id = $commodity['Commodity']['client_id'];
			if($use_yotpo_rating){
				if(array_key_exists($client_id, $ratings)){
					$rating_avg = $ratings[$client_id]['rating'];
					$rating_count = $ratings[$client_id]['count'];
				}
			}
			if (!empty($clientList[$commodity['Commodity']['client_id']]['url']) && !empty($topOffice['url'])) {
?>
						<a href="#" onclick="location.href='/rentacar/company/<?=$clientList[$commodity['Commodity']['client_id']]['url'];?>/<?=$topOffice['url']?>/#reviews'">
<?php
			} else if (!empty($clientList[$commodity['Commodity']['client_id']]['url'])) {
?>
						<a href="#" onclick="location.href='/rentacar/company/<?=$clientList[$commodity['Commodity']['client_id']]['url'];?>/#reviews'">
<?php
			} else {
?>
						<a href="#" onclick="location.href='/rentacar/company?company_id=<?=$clientList[$commodity['Commodity']['client_id']]['id'];?>'">
<?php
			}
?>
							<!-- YOTPO -->
							<div class="yotpo_widget_wrap widget-position">
								<div class="yotpo bottomLine"
									data-appkey="<?php echo $yotpo_app_key; ?>"
									data-domain="https://<?php echo $yotpo_domain; ?>/rentacar"
									data-product-id="<?=$clientList[$commodity['Commodity']['client_id']]['id'].'cl';?>"
									data-product-models=""
									data-name="<?=$clientList[$commodity['Commodity']['client_id']]['name'];?>"
									data-url="https://<?php echo $yotpo_domain; ?>/rentacar/company/<?=$clientList[$commodity['Commodity']['client_id']]['url'];?>"
									data-image-url=""
									data-description=""
									data-bread-crumbs=""
									data-rating-avg="<?= $rating_avg ?>"
									data-rating-count="<?= $rating_count ?>"
								></div>
							</div>
							<!-- YOTPO -->
						</a>
<?php
		}
?>
					</div>
				</div>
			</div><!-- //plan_contents_list_head_middle -->

			<div class="plan_contents_list_head_bottom">
				<div class="plan_contents_list_access"><?= $topOffice['access_dynamic']; ?></div>
<?php
		if($commodity['pr']){
?>
				<div class="recommend_block">
					<span class="recommend_icon">POINT</span>
					<span class="recommend_title"><?php echo $commodity['pr_title']?></span>
				</div>
<?php
		}
?>
			</div><!-- //plan_contents_list_head_bottom -->
		</div>
	</div><!-- //plan_info_block_head -->

	<div class="plan_info_block_body">
		<div class="plan_caption">
<?php
		// mb_substr($commodity['Commodity']['name'], 0, 53, 'UTF-8');

		$commodityName = $carType.' ('.$carModels;
		// 車種指定フラグ
		( $flgModelSelect ) ? $commodityName .= '）' : $commodityName .= ' 他）';
?>
			<?= $commodityName; ?>
		</div>
		<ul class="plan_detail_ul">
			<li class="plan_detail_li car_photo">
				<div class="car_photo_wrap">
<?php
		/*$imageRelativeUrl = !empty($commodityImages[$commodityId]) ?
			'/img/commodity_reference/' . $clientId . '/' . $commodityImages[$commodityId] :
			'/img/noimage.png';*/

		$imageRelativeUrl = !empty($commodity['Commodity']['image_relative_url']) ?
			'/img/commodity_reference/' . $clientId . '/' . $commodity['Commodity']['image_relative_url'] :
			'/img/noimage.png';

		echo $this->Html->image($imageRelativeUrl, array('class' => 'plan_car_photo', 'width' => '100%', 'height' => 'auto', 'alt' => $carModel));
?>
				</div>
			</li>
			<li class="plan_detail_li">
				<div class="plan_detail_topics">
					<p class="capacity">定員：<?php echo $capacity; ?>名</p>
				</div>
					
				<!-- ADVPRO-6196 ABtest-->
				<div class="load-capacity-wrap">
					<div class="load-capacity">
						おすすめ
						<span><i class="icm-user-shape"></i><?php echo $recommendedCapacity; ?>名</span>
						<span><i class="icm-baggage"></i><?php echo $packageNum; ?></span>
					
					</div>
					<div class="js-about_capacity about_capacity">
						<i class="icm-question-fill"></i>
					</div>
					<aside class="js-about_capacity_aside about_capacity_aside" data-is-shown="false">
						<p>
							ゆったりご利用いただけるおすすめの乗員数や、大きなお荷物の積載量の目安をご案内しています。（提供される車種により異なる場合があります）
						</p>
					</aside>
				</div>

				<ul class="plan_detail_spec">
<?php
		if ($commodity['Commodity']['smoking_flg'] == 0) {
?>
					<li><i class="icm-no_smoking icon no-smoking"></i>禁煙車</span></li>
<?php
		} else if ($commodity['Commodity']['smoking_flg'] == 1) {
?>
					<li><i class="icm-smoking icon"></i>喫煙可</span></li>
<?php
		}
?>
<?php
		if ($flgModelSelect) {
?>
					<li><i class="icm-car-side icon"></i>車種確約</span></li>
<?php
		}
?>
<?php
		if ($flgNewRegistation) {
?>
					<li><i class="icm-sparkle icon"></i>新車</span></li>
<?php
		}
?>
				</ul>
				<div class="plan_detail_notes_wrap">
<?php
		if ($commodity['pr']) {
?>
					<div class="search_result_notes search_result_notes_blue js-search_result_notes_blue">
						<p class="result_notes_p"><i class="icm-news"></i> <em class="result_notes_em">おトクなキャンペーン中！</em></p>
					</div>
<?php
		} else if( isset($viewNumber) ) {
?>
					<div class="search_result_notes search_result_notes_blue js-search_result_notes_blue">
						<p class="result_notes_p"><i class="icm-news"></i> <em class="result_notes_em">同じエリアを<?=$viewNumber;?>人が検討中</em></p>
					</div>
<?php
		}
		if ($commodity['CarClassStock']['numberRemaining'] < 6) {
?>
					<div class="search_result_notes search_result_notes_red js-search_result_notes_red">
						<p class="result_notes_p"><i class="icm-clock"></i> <em class="result_notes_em">大人気！在庫は残り<?=$commodity['CarClassStock']['numberRemaining'];?>台です</em></p>
					</div>
<?php
		} else if ($commodity['CarClassStock']['numberRemaining'] < 21) {
?>
					<div class="search_result_notes search_result_notes_red js-search_result_notes_red">
						<p class="result_notes_p"><i class="icm-clock"></i> <em class="result_notes_em">大人気！在庫は残りわずかです</em></p>
					</div>
<?php
		}
?>
				</div>
			</li>
		</ul>
		<ul class="plan_equipment_ul">
<?php
		foreach ($equipmentList as $equipment) {
			$equipment = $equipment['Equipment'];
			if (!empty($commodityEquipment[$commodityId][$equipment['id']])) {
?>
			<li class="js-modal_equip_open plan_equipment_li"><?php echo $equipment['name']; ?></li>
<?php
			}
		}
?>
<?php
		if ($commodity['Commodity']['transmission_flg'] == 0) {
?>
			<li class="js-modal_equip_open plan_equipment_li is_active">AT車</li>
<?php
		}
?>
		</ul>

		<div class="plan_contents_list_plandetail">
			<a href="javascript:void(0);" class="js-open-planview" data-code="<?= $commodity['CommodityItem']['id']; ?>"><?= $planName; ?></a>
		</div>

	</div>

	<div class="plan_info_block_bottom">

		<div class="plan_info_block_bottom_left">
			<div class="payment_labels">
				<p class="menseki_label">免責補償込み</p>

<?php 	//決済種別
		$payment_method = $commodity['Commodity']['payment_method'];
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
					<p>基本料金 <br>[<?php echo $dayCount; ?>]</p>
				</li>
				<li class="plan_price_li plan_price">
					<span>&yen;<?php echo number_format($commodity['CommodityPrice']['price']); ?></span>
					<span>&nbsp;(税込)</span>
				</li>
			</ul>

<?php
		if($spReturnPlace > 0){
?>
			<aside class="plan-notes_additional-fee">
				<p class="-text_drop">乗捨料金が加算されます <span>&yen;<?php echo number_format($commodity['minDropPrice']); ?>〜</span></p>
			</aside>
<?php
		}
?>
<?php
		if(isset($commodity['minLateNightFee'])){
?>
			<aside class="plan-notes_additional-fee">
				<p class="-text_latenight">深夜料金が加算されます <span>&yen;<?php echo number_format($commodity['minLateNightFee']); ?>〜</span></p>
			</aside>
<?php
		}
?>
		</div>

		<div class="plan_info_block_bottom_right">
<?php
		if ($commodity['pr']) {
			if (strlen($reserveUrl) > 0) {
				$planUrl = $reserveUrl . '&recommend_flg=1';
			} else {
				$planUrl = 'recommend_flg=1';
			}
		} else {
			$planUrl = $reserveUrl;
		}
?>
			<?php echo $this->Html->link('選択する', '/plan/' . $commodity['CommodityItem']['id'] . '/?' . $planUrl, array('class'=>'btn-type-primary')); ?>
		</div>

	</div>
</div>
<?php
	}
?>
<div class="search_count">
	<div class="pagingnav">
<?php
	if ($this->Paginator->hasPrev()) {
		echo $this->Paginator->prev(
			'<i class="icm-right-arrow icon-right-arrow_left"></i>', 
			array(
				'url' => array('action' => 'index'), 
				'tag' => 'div', 
				'class' => 'paging_prev',
				'escape'=>false
			), null, array()
		);
	} else {
?>
		<div class="paging_prev no_paging"><i class="icm-right-arrow icon-right-arrow_left"></i></div>
<?php						
	}
?>
		<div>
			<?php echo $this->Paginator->counter('{:page}&nbsp;/&nbsp;{:pages}'); ?>
		</div>
<?php
	if ($this->Paginator->hasNext()) {
		echo $this->Paginator->next(
			'<i class="icm-right-arrow"></i>', 
			array(
				'url' => array('action' => 'index'), 
				'tag' => 'div', 
				'class' => 'paging_next',
				'escape'=>false
			), null, array()
		);
	} else {
?>
		<div class="paging_next no_paging"><i class="icm-right-arrow"></i></div>
<?php						
	}
?>
	</div>
</div>







<script>
$(function(){
	// ADVPRO-6196 ABtest
	// 「推奨人数・推奨荷物数説明」表示切替
	$('.js-about_capacity').each(function(){
		$(this).on('click',function() {
			let thisAside = $(this).siblings('.js-about_capacity_aside');
			let asideIsShown = thisAside.attr('data-is-shown');

			if (asideIsShown == 'false') {
				// 全asideをまず非表示
				$('.js-about_capacity_aside').attr('data-is-shown','false');

				$(thisAside).attr('data-is-shown','true');
			} else if (asideIsShown == 'true') {
				$(thisAside).attr('data-is-shown','false');
			};
		});
	});

	$(document).on('click',function(e) {
		let asideClicked = $(e.target).closest('.js-about_capacity_aside').length;
		let iconClicked = $(e.target).closest('.js-about_capacity').length;

		if ((!asideClicked) && (!iconClicked)) {
			// aside以外かつアイコン以外のエリアをクリック時は非表示
			$('.js-about_capacity_aside').attr('data-is-shown','false');
		};
	});
});
</script>