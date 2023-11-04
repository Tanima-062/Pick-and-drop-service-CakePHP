<section class="list_search_view">
<?php
	// SORTING-----
	if (!empty($commodities) && !empty($isList)) {
?>
	<section class="search_sort_wrap">
<?php
		$sorts = array();
		foreach ($searchSortList as $k => $v) {
			if ($k != $current_sort) {
				$this->request->query['sort'] = $k;
				$href = Router::reverse($this->request);

				$sorts[] = "<a href=\"{$href}\">{$v}</a>";
			} else {
				$sorts[] = $v;
			}
		}

		echo implode(' | ', $sorts);
?>
	</section>

<?php
	}
	// -----SORTING
?>

<?php
	foreach($commodities as $commodity) {

		$commodityItemId = $commodity['CommodityItem']['id'];
		$commodityId = $commodity['Commodity']['id'];
		$clientId = $commodity['Commodity']['client_id'];
		$planName = $commodity['Commodity']['name'];

		$recommendedCapacity = 0;
		// $packageNum = 0;

		//車両タイプ
		$carType = '';
		if(!empty($carInfoList[$commodityItemId]['CarType']['name'])) {
			$carType = $carInfoList[$commodityItemId]['CarType']['name'];
		}

		//車種
		$carModel = '';
		if(!empty($carInfoList[$commodityItemId]['CarModel'])) {
			$carModeLists = Hash::extract($carInfoList[$commodityItemId]['CarModel'],'{n}.name');
			if(!empty($carModeLists)) {
				$carModel = implode($carModeLists,'・');
			}

			//定員人数（推奨人数から変更）
			if(!empty($carInfoList[$commodityItemId]['CarModel'])) {
				$recommendedCapacity = Hash::get($carInfoList[$commodityItemId]['CarModel'],'0.capacity');
			}

			//推奨荷物数
			//if(!empty($carInfoList[$commodityItemId]['CarModel'])) {
			//	$packageNum = Hash::get($carInfoList[$commodityItemId]['CarModel'],'0.package_num');
			//}
		}

		// 喫煙・禁煙
		$smokingCarString = $smokingCarList[$commodity['Commodity']['smoking_flg']];

		// 車種指定フラグ
		$flgModelSelect = ( !empty( $commodity['CommodityItem']['car_model_id']) );

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

		// オプション
		$optionCategories = Constant::optionCategories();

		// 表示するオプション
		// 6:スタッドレス / 8:4WD / 7:タイヤチェーン / 13:NOC補償 / 9,10,11:シート / 999:その他
		$displayOptionCategories = array(6, 8, 7, 13, 9, 10, 11, 999);

		$optionList = array();
		$sheetList = array();
		$otherList = array();

		foreach ($displayOptionCategories as $displayId) {
			foreach ($commodity['Option'] as $option) {
				if ($option['option_category'] != $displayId) {
					continue;
				}

				switch ($displayId) {
					case 9:
					case 10:
					case 11:
						// シートはシート名のリストを作る
						$sheetList[] = $option['option_name'];
						break;
					case 999:
						// その他はオプション名のリストを作る
						$otherList[] = $option['option_name'];
						break;
					default:
						$optionList[$displayId] = $optionCategories[$displayId]['name'];
						break;

				}
			}
		}

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
	<div class="plan_info_block<?php if($commodity['pr']){?> recommend_plan<?php } ?>">

		<div class="plan_info_block_head">
<?php
		if (!empty($hokkaidoCampaignFlg) && in_array($commodity['Commodity']['client_id'], $hokkaidoCampaignTargetClientIds)) {
?>
			<div class="plan_contents_list_head_campaign-logo">
				<img src="/rentacar/img/logo/icon/campaign_hokkaidrive2022_logo.svg" width="45" height="45"/>
			</div>
<?php
		}
?>
			<div class="plan_contents_list_head_main">
<?php
		if($commodity['pr']){
?>
				<div class="recommend_label">PR</div>
<?php
		}
?>
				<div class="plan_contents_list_head_top">
					<h3 class="plan_contents_list_shop_name">
						<?php echo $clientList[$commodity['Commodity']['client_id']]['name'] . ' ' . $topOffice['name']; ?>
					</h3>
<?php
		// 「その他の店舗」モーダル-----
		if (!empty($rentOfficeNameNotTopArray)) {
?>
					<div class="btn_modalf_open_wrap">
						<a href="javascript: void(0);" class="js-modalf_open btn_modal_open">その他<?= count($rentOfficeNameNotTopArray); ?>店舗</a><i class="icm-right-arrow"></i>
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
<?php
		}
		// -----「その他の店舗」モーダル
?>
				</div>

				<div class="plan_contents_list_head_middle">
<?php
		if($commodity['pr']){ //　使用時に文言更新
?>
					<p class="recommend_point">
						<span class="recommend_icon">POINT</span>&nbsp;<?php echo $commodity['pr_title']?>
					</p>
<?php
		}
?>
				</div>

				<div class="plan_contents_list_head_bottom">

					<div class="plan_contents_list_access">
						<?php echo $topOffice['access_dynamic']; ?>
					</div>
<?php
		if($yotpo_is_active && $use_yotpo){
?>
					<div class="plan_contents_list_yotpo">
<?php
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
							<div class="yotpo_widget_wrap search_yotpo_inline">
								<div class="yotpo bottomLine"
									data-appkey="<?php echo $yotpo_app_key ?>"
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
					</div>
<?php
		}
?>
				</div><!-- /plan_contents_list_head_bottom -->
			</div><!-- /plan_contents_list_head_main -->
		</div><!-- /plan_info_block_head -->

		<div class="plan_info_block_body">
			<div class="plan_contents_list_left">
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
				<a href="/rentacar/plan/<?php echo $commodity['CommodityItem']['id'] . '/?' . $planUrl . '#ReservationPlanForm';?>">
<?php
		/*$imageRelativeUrl = !empty($commodityImages[$commodityId]) ?
			'/img/commodity_reference/' . $clientId . '/' . $commodityImages[$commodityId] :
			'/img/noimage.png';*/

		$imageRelativeUrl = !empty($commodity['Commodity']['image_relative_url']) ?
			'/img/commodity_reference/' . $clientId . '/' . $commodity['Commodity']['image_relative_url'] :
			'/img/noimage.png';

		echo $this->Html->image($imageRelativeUrl, array('width' => '268', 'height' => 'auto', 'class' => 'plan_contents_img', 'alt' => $carModel));
?>
				</a>
			</div>
			<div class="plan_contents_list_center">
				<p class="plan_contents_name_wrap is_search">
<?php
		$link_name = $carType .'（'. $carModel;
		// 車種指定フラグ
		( $flgModelSelect ) ? $link_name .= '）' : $link_name .= '他）';
?>
					<a href="javascript:void(0);" class="js-modalOpen plan_contents_name" data-code="<?= $commodity['CommodityItem']['id']; ?>"><?= $link_name; ?></a>
				</p>

				<ul class="plan_car_spec_ul">
					<li class="plan_car_spec_li">
<?php
		if($commodity['Commodity']['smoking_flg'] == 0){
?>
						<p class="plan_car_spec is_no_smoking">
							<i class="icm-no_smoking"></i> <?=$smokingCarString;?>
						</p>
<?php
		} else if($commodity['Commodity']['smoking_flg'] == 1){
?>
						<p class="plan_car_spec is_smoking">
							<i class="icm-smoking"></i> <?=$smokingCarString; ?>
						</p>
<?php
		}
?>
					</li>
					<li class="plan_car_spec_li">
						<p class="plan_car_spec">定員<?=$recommendedCapacity;?>名</p>
					</li>
					<li class="plan_car_spec_li">
						<p class="plan_car_spec is_car_model <?php if(!$flgModelSelect){ ?> is_inactive<?php } ?>"><i class="icm-car-side"></i> 車種指定</p>
					</li>
					<li class="plan_car_spec_li">
						<p class="plan_car_spec is_new_car <?php if(!$flgNewRegistation){?> is_inactive<?php } ?>"><i class="icm-sparkle"></i> 新車</p>
					</li>
				</ul>

				<ul class="plan_equipment_ul">
<?php
		foreach ($equipmentList as $equipment) {
			$equipment = $equipment['Equipment'];
			if (!empty($commodityEquipment[$commodityId][$equipment['id']])) {
?>
					<li class="plan_equipment_li is_active">
						<p><?=$equipment['name']; ?></p>
						<aside class="plan_equipment_aside">
							<p class="plan_equipment_description"><?=$equipment['description']; ?></p>
						</aside>
					</li>
<?php
			} else {
?>
					<li class="plan_equipment_li">
						<p><?=$equipment['name']; ?></p>
					</li>
<?php
			}
		}
?>

<?php
		if ($commodity['Commodity']['transmission_flg'] == 0) {
?>
					<li class="plan_equipment_li is_active">
						<p>AT車</p>
						<aside class="plan_equipment_aside">
							<p class="plan_equipment_description">オートマチックトランスミッションの車です</p>
						</aside>
					</li>
<?php
		} else if ($commodity['Commodity']['transmission_flg'] == 1) {
?>
					<li class="plan_equipment_li">
						<p>AT車</p>
					</li>
<?php
		}
?>
				</ul>

<?php
		if (!empty($optionList) || !empty($sheetList) || !empty($otherList)) {
?>
				<dl class="plan_option_dl">
					<dt class="plan_option_dt">選択可能オプション</dt>
<?php
			if (!empty($optionList[6])) {
?>
					<dd class="plan_option_dd">スタッドレス</dd>
<?php
			}
			if (!empty($optionList[8])) {
?>
					<dd class="plan_option_dd">4WD</dd>
<?php
			}
			if (!empty($optionList[7])) {
?>
					<dd class="plan_option_dd">タイヤチェーン</dd>
<?php
			}
			if (!empty($optionList[13])) {
?>
					<dd class="plan_option_dd">NOC補償</dd>
<?php
			}
			if (!empty($sheetList)) {
?>
					<dd class="plan_option_dd is_help">
						シート <i class="icm-question-fill"></i>
						<aside class="plan_option_aside">
							<p class="plan_option_list">
<?php
				if (!empty($sheetList)) {
					echo implode('<br>', $sheetList);
				}
?>
							</p>
						</aside>
					</dd>
<?php
			}
			if (!empty($otherList)) {
?>
					<dd class="plan_option_dd is_help">
						その他 <i class="icm-question-fill"></i>
						<aside class="plan_option_aside">
							<p class="plan_option_list">
<?php
				if (!empty($otherList)) {
					echo implode('<br>', $otherList);
				}
?>
							</p>
						</aside>
					</dd>
<?php
			}
?>
				</dl>
<?php
		}
?>
				<div class="plan_contents_list_plandetail">
<?php
		$CommodityItemid = $commodity['CommodityItem']['id'];
?>
					<a href="javascript:void(0);" class="js-modalOpen modal-open" data-code="<?= $commodity['CommodityItem']['id']; ?>"><?= $planName; ?></a>
				</div>
			</div><!-- /plan_contents_list_center -->

			<div class="plan_contents_list_right">
				<div class="payment_labels">
					<p class="menseki_label">免責補償込み</p>

<?php //決済種別
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
				<p class="plan_contents_price_title">基本料金 [<?=$dayCount; ?>]</p>
				<p class="plan_contents_price">&yen;<?php echo number_format($commodity['CommodityPrice']['price']); ?><span>(税込)</span></p>

<?php
		echo $this->Form->create('Plan', array('controller' => 'reservations', 'action' => 'plan', 'type' => 'get', 'url' => '/plan/' . $commodity['CommodityItem']['id'] . '/', 'class' => 'plan_contents_form'));
		// GETボタンのhidden値生成
		parse_str($reserveUrl, $params);
		foreach ($params as $name => $value) {
			if (is_array($value)) {
				foreach ($value as $k => $v) {
					echo "<input type=\"hidden\" name=\"{$name}[{$k}]\" value=\"{$v}\" />\n";
				}
			} else {
				echo "<input type=\"hidden\" name=\"{$name}\" value=\"{$value}\" />\n";
			}
		}
		if($commodity['pr']){
			echo "<input type=\"hidden\" name=\"recommend_flg\" value=\"1\" />\n";
		}
		if($returnWay > 0){
?>
				<div class="plan-notes_additional-fee">
					<p class="-text_drop">乗り捨て料金が加算されます<br>
						<span>&yen;<?php echo number_format($commodity['minDropPrice']); ?>〜</span>
					</p>
				</div>
<?php
		}
		if(isset($commodity['minLateNightFee'])){
?>
				<div class="plan-notes_additional-fee">
					<p class="-text_latenight">深夜料金が加算されます<br>
						<span>&yen;<?php echo number_format($commodity['minLateNightFee']); ?>〜</span>
					</p>
				</div>
<?php
		}
		if( isset($viewNumber) ){
?>
				<div class="plan_contents_notes notes_blue js_page_viewer">
					<p class="plan_notes_p plan_notes_hide"><i class="icm-news"></i> 同じエリアを<?=$viewNumber;?>人が検討中</p>
				</div>
<?php
		}
		if($commodity['CarClassStock']['numberRemaining'] < 6) {
?>
				<div class="plan_contents_notes notes_red js_car_stock">
					<p class="plan_notes_p plan_notes_hide"><i class="icm-clock"></i> 大人気！在庫は残り<?=$commodity['CarClassStock']['numberRemaining'];?>台です</p>
				</div>
<?php
		} else if($commodity['CarClassStock']['numberRemaining'] < 21){
?>
				<div class="plan_contents_notes notes_red js_car_stock">
					<p class="plan_notes_p plan_notes_hide"><i class="icm-clock"></i> 大人気！在庫は残りわずかです</p>
				</div>
<?php
		}
?>
				<div class="search-btn">

<?php
		echo $this->Form->button('選択する', array('class' => 'btn-type-primary'));
		echo $this->Form->end();
?>
				</div>
			</div><!-- /plan_contents_list_right -->
		</div><!-- plan_info_block_body -->

		<div class="plan_info_block_bottom">
<?php
		echo $this->Html->link($clientList[$commodity['Commodity']['client_id']]['name'].'のプランをもっと見る', '/searches?' . $clientPlanLink . '&client_id=' . $commodity['Commodity']['client_id'], array('escape' => false));
?>
		</div>
	</div><!-- /plan_info_block -->
<?php
	}
?>

<!-- SEARCH RESULT LIST FOOTER----- -->
	<div class="contents_result_ft rent-margin-bottom">
		<p>
			<span><?php echo $searchPlace; ?></span>で<?php echo $this->Paginator->counter('{:count}件 表示中 {:start}-{:end}'); ?>
		</p>
		<div class="rent_pager">
			<ul class="rent_pager_list">
<?php
	if ($this->Paginator->hasPrev()) {
		echo $this->Paginator->prev('', array('tag' => 'li', 'class' => 'rent_pager_list_item prev'), null, array());
	} else {
		echo '<li class="rent_pager_list_item prev is-first"></li>';
	}
	echo $this->Paginator->numbers(array('separator' => '', 'tag' => 'li', 'class' => 'rent_pager_list_item page_num', 'currentTag' => 'span'));
	if ($this->Paginator->hasNext()) {
		echo $this->Paginator->next('', array('tag' => 'li', 'class' => 'rent_pager_list_item next'), null, array());
	} else {
		echo '<li class="rent_pager_list_item prev is-last"></li>';
	}
?>
			</ul>
		</div>
	</div>
<!-- -----SEARCH RESULT LIST FOOTER -->
</section>