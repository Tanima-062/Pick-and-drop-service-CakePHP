<section class="map_search_view">
	<div id="map-search" style="height: 500px;"></div>
	<div class="recommend-mark hidden">skyticketおすすめレンタカー</div>
	<div id="duplicated-office-list"></div>
<?php
	$recommendClientID = array();
	foreach ($rentOfficeList as $officeId => $officeInfo) {
?>
	<section class="office-info-wrap office-id_<?=$officeId?>">
		<div class="office-info">
			<span class="office-info-selected-label">選択中の店舗</span>
<?php
		if($officeInfo['pr']) {
			$recommendClientID[] = $officeInfo['client_id'];
?>
			<div class="office-info-recommended-label">PR</div>
<?php
		}
?>
			<div class="office-info-l">
				<img class="office-info-l-img" src="/rentacar/img/logo/square/<?= $officeInfo['client_id']; ?>/<?= $clientList[$officeInfo['client_id']]['sp_logo_image']; ?>" alt="<?= $officeInfo['name']; ?>" loading="lazy">
			</div>
			<div class="office-info-c">
				<div class="office-info-c-client-name">
					<div><?= $clientList[$officeInfo['client_id']]['name']; ?></div>
<?php
		if($yotpo_is_active && $use_yotpo){
?>
					<div class="yotpo-score">
<?php
			$rating_avg = '';
			$rating_count = '';
			$client_id = $officeInfo['client_id'];
			if($use_yotpo_rating){
				if(array_key_exists($client_id, $ratings)){
					$rating_avg = $ratings[$client_id]['rating'];
					$rating_count = $ratings[$client_id]['count'];
				}
			}
			if (!empty($clientList[$client_id]['url']) && !empty($officeInfo['url'])) {
?>
		  				<a href="#" onclick="location.href='/rentacar/company/<?=$clientList[$client_id]['url'];?>/<?=$officeInfo['url']?>/#reviews'">
<?php
	  		} else if (!empty($clientList[$client_id]['url'])) {
?>
		  				<a href="#" onclick="location.href='/rentacar/company/<?=$clientList[$client_id]['url'];?>/#reviews'">
<?php
	  		} else {
?>
		  				<a href="#" onclick="location.href='/rentacar/company?company_id=<?=$clientList[$client_id]['id'];?>'">
<?php
	  		}
?>
							<!-- YOTPO -->
							<div class="yotpo_widget_wrap search_yotpo_inline">
								<div class="yotpo bottomLine"
									data-appkey="<?php echo $yotpo_app_key ?>"
									data-domain="https://<?php echo $yotpo_domain; ?>/rentacar"
									data-product-id="<?=$clientList[$client_id]['id'].'cl';?>"
									data-product-models=""
									data-name="<?=$clientList[$client_id]['name'];?>"
									data-url="https://<?php echo $yotpo_domain; ?>/rentacar/company/<?=$clientList[$client_id]['url'];?>"
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
				</div>
				<div class="office-info-c-office-name">
					<?= $officeInfo['name'] ?>
				</div>
				<div class="office-info-c-way-info">
					<i class="map"></i>
					<span class="office-info-c-way-info-contents"><?= $officeInfo['access_dynamic'] ?></span>
				</div>
<?php
		if($officeInfo['pr']) {
?>
				<div class="office-info-c-recommend">
					<div class="office-info-c-recommend-icon">POINT</div>
					<div><?= $officeInfo['pr_title'] ?></div>
				</div>
<?php
		}

		$dayCount = '日帰り';

		if($commodities[$officeId][0]['CarClassStock']['day_count'] != 1) {
			$dayCount = ($commodities[$officeId][0]['CarClassStock']['day_count'] - 1) . '泊' . $commodities[$officeId][0]['CarClassStock']['day_count'] . '日';
		}
?>
			</div>
			<div class="office-info-r">
				<div class="office-info-r-fee-type">基本料金 [<?=$dayCount; ?>]</div>
				<div class="office-info-r-price">&yen;<?= number_format($commodities[$officeId][0]['CommodityPrice']['price']); ?>〜</div>
			</div>
		</div>
<?php
		if (!empty($hokkaidoCampaignFlg) && in_array($client_id, $hokkaidoCampaignTargetClientIds)) {
?>
		<div class="campaign-info">
			<img src="/rentacar/img/logo/icon/campaign_hokkaidrive2022_logo.svg" width="45" height="45"/>
			<p>
				この店舗の全てのプランは、HOKKAIDriveキャンペーンの対象です。
			</p>
		</div>
<?php
		}
?>
		<div class="map-search-result">
			この店舗には<span class="map-search-result-num"><?= count($commodities[$officeId]) ?></span>件のプランがあります
		</div>
	</section>
<?php
	}
?>

<!-- Plan List -->
<?php
  	foreach ($commodities as $officeId => $officeCommodities) {
?>
	<div class="map-plan-list office-id_<?=$officeId?>">
<?php
		foreach($officeCommodities as $commodity) {

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
?>
		<div class="plan_info_block">
		<!-- 既存のプランリストをヘッダーだけ削除する。 -->
			<div class="plan_info_block_body">
				<div class="plan_contents_list_left">
<?php
			if (array_search($clientId, $recommendClientID) !== FALSE) {
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
						<a href="javascript:void(0);" class="js-modalOpen plan_contents_name" data-code="<?= $commodity['CommodityItem']['id']; ?>" data-office-id ="<?= $officeId; ?>"><?= $link_name; ?></a>
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
						<a href="javascript:void(0);" class="js-modalOpen modal-open" data-code="<?= $commodity['CommodityItem']['id']; ?>" data-office-id ="<?= $officeId; ?>"><?= $planName; ?></a>
					</div>
				</div>

				<div class="plan_contents_list_right">
					<div class="payment_labels">
						<p class="menseki_label">免責補償込み</p>

<?php
			//決済種別
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
						if (array_search($clientId, $recommendClientID) !== FALSE) {
							echo "<input type=\"hidden\" name=\"recommend_flg\" value=\"1\" />\n";
						}
						echo "<input type=\"hidden\" name=\"office_id\" value=\"{$officeId}\" />\n";
					?>
<?php
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
				</div>
			</div>
		</div>
<?php
		}
?>
	</div>
<?php
  	}
?>
<!-- /Plan List -->
</section>

<script defer src="https://maps.googleapis.com/maps/api/js?key=<?=getGoogleAPIKey( GOOGLE_APIKEY_Maps_JavaScript )?>&callback=initMap&region=jp"></script>
<?php echo $this->Html->script(array('/lib/marker_with_label.min.js')); ?>
<script>
  const clientList = <?= json_encode($clientList); ?>;
  const originOfficeList = <?= json_encode($rentOfficeList); ?>;

  // json_encodeは順番を保証してくれないので配列に変換しソート
  const sortedOfficeList = Object.keys(originOfficeList)
    .map((key) => originOfficeList[key])
    .sort((a, b) => {
      // Commodity.phpの251行目〜259行目を参考
      if (a.pr === true && b.pr === false) return -1
      else if(a.pr === false && b.pr === true) return 1
      else {
        return (Number(a.id) <= Number(b.id)) ? -1 : 1
      }
    })
  let map
  let selectedOfficeId;
  let isLoad
  
  //  [key: 緯度_経度] : [店舗IDら]
  const byLocationOfficeIdList = {}
  
  sortedOfficeList.forEach((rentOffice) => {
    if (byLocationOfficeIdList[rentOffice.latitude + '_' + rentOffice.longitude] === undefined) {
      byLocationOfficeIdList[rentOffice.latitude + '_' + rentOffice.longitude] = new Array(rentOffice.id)
      rentOffice.is_duplicated = false
    } else {
      byLocationOfficeIdList[rentOffice.latitude + '_' + rentOffice.longitude].push(rentOffice.id)
      // 重複することが確定になった時点で最初に入れた店舗のis_duplicatedをtrueにする
      if (byLocationOfficeIdList[rentOffice.latitude + '_' + rentOffice.longitude].length === 2) {
        const firstOfficeId = byLocationOfficeIdList[rentOffice.latitude + '_' + rentOffice.longitude][0]
        sortedOfficeList.find(item => item.id === firstOfficeId).is_duplicated = true
      }
      rentOffice.is_duplicated = true
    }
  })
  function initMap() {
    map = new google.maps.Map(
      document.getElementById('map-search'), {
        // fitBoundsにより、zoom,centerの設定が不要
        // zoom: 15
        // center: getCenterLatLng(rentOfficeList)
      });
      
    let zIndex = 999;
    let bounds = new google.maps.LatLngBounds();
    let activeIw // 表示されている吹き出し
      
    sortedOfficeList.forEach((rentOffice) => {
      let lat_lng = rentOffice.latitude + '_' + rentOffice.longitude
      
      if (!selectedOfficeId) {
        if (window.sessionStorage.getItem('selected_office_id') &&
            Object.keys(originOfficeList).includes(window.sessionStorage.getItem('selected_office_id'))) {
          // 選択されてた店舗のIDが存在すれば選択状態を維持する。
          selectedOfficeId = window.sessionStorage.getItem('selected_office_id')
        } else {
          // リストで一番の店舗を選択する。
          selectedOfficeId = rentOffice.id
        }
      }
      if (rentOffice.pr && $('.recommend-mark').hasClass('hidden')) {
        $('.recommend-mark').removeClass('hidden')
      }
      let marker = new markerWithLabel.MarkerWithLabel({
        map: map,
        position: new google.maps.LatLng(Number(rentOffice.latitude), Number(rentOffice.longitude)),
        zIndex: zIndex--,
        // ラベル文字
        labelContent:
          rentOffice.is_duplicated ?
            `<div class="duplicated-office-marker"><i class="icm-car-front"></i><span>${byLocationOfficeIdList[lat_lng].length}</span></div>` :
            `<div><img src="/rentacar/img/logo/square/${rentOffice.client_id}/${clientList[rentOffice.client_id].sp_logo_image}" alt="${rentOffice.name}"></div>`,
        labelAnchor: new google.maps.Point(-21, -47), // positionを基準に位置を決める
        labelClass: `labels marker_${rentOffice.id} ${rentOffice.pr ? 'recommended' : ''}`
      });
      marker.addListener("click", function ({ latLng }) {
        if (rentOffice.is_duplicated) {
          if (activeIw) activeIw.close()
          let iwContent = `<div class="iw-wrap">
                            <div class="iw-title">
                              この場所には${byLocationOfficeIdList[lat_lng].length}の店舗があります
                            </div>`
          byLocationOfficeIdList[lat_lng].forEach(office_id => {
              iwContent += `<div class="iw-content ${originOfficeList[office_id].pr ? 'recommended' : ''}">
                              ${clientList[originOfficeList[office_id].client_id].name + originOfficeList[office_id].name}
                            </div>`
          })
          iwContent +=    '</div>'
          activeIw = new google.maps.InfoWindow({
            content: iwContent,
            maxWidth: 'none',
          });
          activeIw.open(map, this);
          
          // select box
          let duplicatedOfficeListHtml = `
            <div class="duplicated-office-list-title">店舗を選択</div>
            <label class="form-select">
              <select id="duplicated-office-list-select">`
          byLocationOfficeIdList[lat_lng].forEach(office_id => {
            duplicatedOfficeListHtml +=
                `<option value=${office_id} ${selectedOfficeId === office_id ? 'selected' : ''}>${originOfficeList[office_id].pr ? '★' : ''}${clientList[originOfficeList[office_id].client_id].name + originOfficeList[office_id].name}</option>`
          })
          duplicatedOfficeListHtml += `
              </select>
            </label>
          `
          $("#duplicated-office-list").html(duplicatedOfficeListHtml)

          $("#duplicated-office-list-select").on('change', function (e) {
            $(`.marker_${e.target.value}`).addClass("selected");
            $(`.office-id_${e.target.value}`).addClass("active")
            if (selectedOfficeId && selectedOfficeId !== e.target.value) {
              $(`.marker_${selectedOfficeId}`).removeClass("selected");
              $(`.office-id_${selectedOfficeId}`).removeClass("active")
            }
            selectedOfficeId = e.target.value
            window.sessionStorage.setItem('selected_office_id', e.target.value)
          });
        } else {
          if (activeIw) activeIw.close()
          $("#duplicated-office-list").html('')
          activeIw = undefined
        }
        if (!isLoad) {
          map.panTo({ lat: latLng.lat(), lng: latLng.lng() });
          $("html,body").animate({scrollTop:$('.contents_result').offset().top})
        } else {
          isLoad = false 
        }
        $(`.marker_${rentOffice.id}`).addClass("selected");
        $(`.office-id_${rentOffice.id}`).addClass("active")
        if (selectedOfficeId && selectedOfficeId !== rentOffice.id) {
          $(`.marker_${selectedOfficeId}`).removeClass("selected");
          $(`.office-id_${selectedOfficeId}`).removeClass("active")
        }
        selectedOfficeId = rentOffice.id
        window.sessionStorage.setItem('selected_office_id', rentOffice.id)
      });
      bounds.extend(marker.getPosition());
    })
    // 初期zoomの最大値が16になるように
    google.maps.event.addListenerOnce(map, 'bounds_changed', function(event) {
      if (this.getZoom() > 16) {
        this.setZoom(16);
      }
    });
    // https://developers.google.com/maps/documentation/javascript/reference/map?hl=ja#Map.fitBounds
    map.fitBounds(bounds);
    
    // マーカ以外のところをクリックするとinfoWindow(吹き出し)が出ないようにする。
    google.maps.event.addListener(map, 'click', function(event) {
      event.stop()
    });
  }
</script>

<script>
  $(window).on('load', function() {
    isLoad = true
    $(`.marker_${selectedOfficeId}`).click()
  })

</script>
