<!-- wrap -->
<div class="wrap contents clearfix mypage mypages_page">

<?php echo $this->element('progress_bar'); ?>
<?php
	if (Constant::isReservedStatus($result['Reservation']['reservation_status_id'])) {
?>

<?php
		if (!empty($sessionMessage)) {
			echo $this->element('session_message');
		}
?>

	<div class="h2_wrap text_center st-table">
		<div class="st-table_cell">
			<h2><?php echo $result['Reservation']['last_name']; ?><?php echo $result['Reservation']['first_name']; ?> 様</h2>
		</div>
	</div>

	<div class="h3_wrap st-table rent-margin-bottom">
		<div class="st-table_cell">
			<h3>レンタカー予約内容</h3>
<?php
		if (!_isLogin()){
			echo $this->Html->link('ログアウト', '/mypages/logout/', array('class' => 'btn btn_plain'));
		}
?>
		</div>
	</div>
<?php
		if ($hokkaidoCampaignFlg) {
?>
	<p class="campaign-notice">
		ご予約いただいたプランは<b>HOKKAIDriveキャンペーン</b>の対象です。<br/>
		キャンペーンの事前登録はこちらから(<a target="_blank" rel="noopener noreferrer" href="https://hokkaidrive.com">https://hokkaidrive.com</a>)お願いいたします。
	</p>
<?php
		}
?>

	<?php // セルフチェックイン案内 
		echo $this->element('pc_note_self-checkin', [
			'planname' => $result['Commodity']['name'], 
			'clientid' => $result['Client']['id']
		]);
	?>

	<h4 class="hd-left-bordered">予約状況</h4>
	<table class="contents_mypage_tbl rent-margin-bottom">
		<tr class="">
			<th>予約番号</th>
			<td>
				<span class="contents_mypage_number"><?php echo $result['Reservation']['reservation_key']; ?></span>
<?php
		// TASK-2209の理由でコメントアウト echo $this->Html->link('予約完了メールを再送', '/resend/', array('class' => 'btn btn_plain'));

		if ($result['Reservation']['reservation_status_id'] == Constant::STATUS_RESERVATION && $cancelDeadlineDatetime > date('Y-m-d H:i:s') && !($isEconMaintenance && $isPaidInAdvance) && $isVisibleButton) {
			echo $this->Html->link('この予約をキャンセルする', '/mypages/cancel/', array('class' => 'btn btn_plain'));
		}
?>
			</td>
		</tr>
		<tr class="">
			<th>予約申し込み日</th>
			<td>
<?php
		$w = $week[date('w', strtotime($result['Reservation']['created']))];
		echo date('Y年m月d日 ('.$w.') H:i', strtotime($result['Reservation']['created']));
?>
			</td>
		</tr>
		<tr class="">
			<th>予約状況</th>
			<td><?php echo $reservationStatus[$result['Reservation']['reservation_status_id']]; ?></td>
		</tr>
		<tr class="">
			<th>予約確認ページからのキャンセル</th>
			<td>
				<?php $w = $week[date('w', strtotime($cancelDeadlineDatetime))]; ?>
				<?php echo date('Y年m月d日 ('.$w.') H:i', strtotime($cancelDeadlineDatetime)); ?>&nbsp;まで<br>
				上記を過ぎた場合は、レンタカーご利用店舗へ直接ご連絡ください。
<?php 
		if ($isEconMaintenance && $isPaidInAdvance) {
?>
				<br>現在システムメンテナンス中のため、キャンセルできません。
<?php
		}
?>
			</td>
		</tr>
	</table>

	<h4 class="hd-left-bordered">予約詳細</h4>
	<table class="contents_mypage_tbl rent-margin-bottom">
		<tr class="">
			<th>レンタカー事業者</th>
			<td>
<?php
		if(!empty($result['Client']['sp_logo_image'])){
			echo $this->Html->image('logo/square/'.$result['Client']['id'].'/'.$result['Client']['sp_logo_image'], array('alt' => $result['Client']['name'].'ロゴ', 'class' => 'contents_complete_tbl_img','width' => 64));
			echo '<br>';
		}
?>
				<div class="contents_complete_tbl_shop">
					<span><?php echo $result['Client']['name']; ?></span>
<?php
		if (!empty($result['Client']['clause_pdf'])) {
			echo $this->Html->link('貸渡約款を見る', '/files/clause_pdf/'.$result['Client']['clause_pdf'], array('target' => '_blank'));
?>
					<p class="text_danger">※ご契約につきましては、記載のレンタカー事業者との契約となります。上記、貸渡約款をごらんください。</p>
<?php
		}
?>
				</div>
			</td>
		</tr>
		<tr class="">
			<th>受取日時</th>
			<td>
<?php
		$rentW = $week[date('w', strtotime($result['Reservation']['rent_datetime']))];
		echo date('Y年m月d日 ('.$rentW.') H:i', strtotime($result['Reservation']['rent_datetime']));
?>
			</td>
		</tr>
		<tr class="">
			<th>受取店舗</th>
			<td>
				<p><span><?php echo $result['RentOffice']['name']; ?>（</span><span><?php echo $result['RentOffice']['tel']; ?></span>）</p>
			</td>
		</tr>
		<tr class="">
			<th>返却日時</th>
			<td>
<?php
		$returnW = $week[date('w', strtotime($result['Reservation']['return_datetime']))];
		echo date('Y年m月d日 ('.$returnW.') H:i', strtotime($result['Reservation']['return_datetime']));
?>
			</td>
		</tr>
		<tr class="">
			<th>返却店舗</th>
			<td>
				<p><span><?php echo $result['ReturnOffice']['name']; ?>（</span><span><?php echo $result['ReturnOffice']['tel']; ?></span>）</p>
			</td>
		</tr>
	</table>

	<section class="plan_info_block">
		<div class="plan_info_block_body">
			<div class="plan_contents_list_left">
<?php
		/*$imageRelativeUrl = !empty($commodityImages[0]['image_relative_url']) ?
		'/img/commodity_reference/' . $result['Client']['id'] . '/' . $commodityImages[0]['image_relative_url'] :
		'/img/noimage.png';*/
		$imageRelativeUrl = !empty($result['Commodity']['image_relative_url']) ? 
		'/img/commodity_reference/' . $result['Client']['id'] . '/' . $result['Commodity']['image_relative_url'] : 
		'/img/noimage.png';

		echo $this->Html->image($imageRelativeUrl, array('width' => '268', 'height' => 'auto', 'class' => 'plan_contents_img', 'alt' => '画像'.$result['Commodity']['name']));
?>
			</div>
			<div class="plan_contents_list_center">
				<p class="plan_contents_name_wrap">
<?php
		$link_name = $result['CarType']['name'] .'（'. $result['CarModel'];
		// 車種指定フラグ
		$flgModelSelect = ( !empty( $result['CommodityItem']['car_model_id']) );
		( $flgModelSelect ) ? $link_name .= '）' : $link_name .= '他）';
?>
					<span class="plan_contents_name"><?=$link_name;?></span>
				</p>
				<ul class="plan_car_spec_ul">
					<li class="plan_car_spec_li">
<?php
		// 喫煙・禁煙
		$smokingCarString = $smokingCarList[$result['Commodity']['smoking_flg']];
		if ($result['Commodity']['smoking_flg'] == 0) {
?>
						<p class="plan_car_spec is_no_smoking">
							<i class="icm-no_smoking"></i> <?=$smokingCarString;?>
						</p>
<?php
		} else if($result['Commodity']['smoking_flg'] == 1) {
?>
						<p class="plan_car_spec is_smoking">
							<i class="icm-smoking"></i> <?=$smokingCarString; ?>
						</p>
<?php
		}
?>
					</li>
					<li class="plan_car_spec_li">
						<p class="plan_car_spec">定員<?=$result['Recommend']['capacity'];?>名</p>
					</li>
					<li class="plan_car_spec_li">
						<p class="plan_car_spec is_car_model <?php if(!$flgModelSelect){ ?> is_inactive<?php } ?>"><i class="icm-car-side"></i> 車種指定</p>
					</li>
					<li class="plan_car_spec_li">
						<p class="plan_car_spec is_new_car <?php if($result['Commodity']['new_car_registration'] != 1 &&  $result['Commodity']['new_car_registration'] != 2){ ?> is_inactive<?php } ?>"><i class="icm-sparkle"></i> 新車</p>
					</li>
				</ul>
				<ul class="plan_equipment_ul">
					<li class="plan_equipment_li is_active">
						<p>免責補償</p>
						<aside class="plan_equipment_aside">免責補償料金込みプラン</aside>
					</li>
<?php
		foreach($equipmentList as $equipment) {
			$equipment = $equipment['Equipment'];
			if (!empty($result['Equipment'][$equipment['id']])) {
?>
					<li class="plan_equipment_li is_active">
						<p><?=$equipment['name']; ?></p>
						<aside class="plan_equipment_aside"><?=$equipment['description']; ?></aside>
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
		if ($result['Commodity']['transmission_flg'] == 0) {
?>
					<li class="plan_equipment_li is_active">
						<p>AT車</p>
						<aside class="plan_equipment_aside">
							<p class="plan_equipment_description">オートマチックトランスミッションの車です</p>
						</aside>
					</li>
<?php
		} else if ($result['Commodity']['transmission_flg'] == 1) {
?>
					<li class="plan_equipment_li">
						<p>AT車</p>
					</li>
<?php
		}
?>
				</ul>
			</div>
		</div>
	</section>

	<h4 class="hd-left-bordered">お支払金額</h4>
	<table class="contents_mypage_tbl rent-margin-bottom-l">
<?php
		if ($unpaidAmount > 0){
?>
		<tr>
			<th>未払金額</th>
			<td class="amount unpaid_amount">
				<div class="inner">
					<span class="unpaid_price">&yen;<?php echo number_format($unpaidAmount); ?></span>
					<small>(税込)</small>
<?php
			if (!$paymentApi) {
?>
						<!-- payment1 -->
						<button class="btn btn_plain" onclick="location.href='/rentacar/mypages/input/'">お支払いへ</button>
<?php
			} else {

				if (empty($cartId)) {
?>
							<!-- payment2 -->					
							<button class="btn btn_plain" onclick="location.href='/rentacar/mypages/input/'">お支払いへ</button>
<?php
				} else {
?>
							<!-- payment3 -->
							<button class="btn btn_plain" onclick='location.href="<?php echo $paymentRedirectUrl; ?>"'>お支払いへ</button>
<?php
				}

			}
?>
					<span class="unpaid_limit">(ご入金期限:<?php echo $result['Reservation']['payment_limit_datetime']; ?>)</span>				
				</div>
			</td>
		</tr>
<?php
		}
?>
		<tr>
			<th>オプション</th>
			<td>
<?php
		if (!empty($dropOffNightFee)) {
			foreach ($dropOffNightFee as $key => $value) {
?>
				<span><?php echo $value['DetailType']['name']; ?>&nbsp;</span>
<?php
			}
		}
		if (!empty($reservationChildSheet)) {
			foreach ($reservationChildSheet as $key => $value) {
?>
				<span><?php echo $value['Privilege']['name']; ?>×<?php echo $value['ReservationChildSheet']['count']; ?>&nbsp;</span>
<?php
			}
		}
		if (!empty($reservationPrivilege)) {
			foreach ($reservationPrivilege as $key => $value) {
?>
				<span><?php echo $value['Privilege']['name']; ?>×<?php echo $value['ReservationPrivilege']['count']; ?>&nbsp;</span>
<?php
			}
		}
?>
			</td>
		</tr>
		<tr>
			<th>お支払合計金額</th>
			<td class="contents_result_detail_amount">
				<div class="text_right rent-padding">
					<span class="bubble bubble-right">税込価格</span>
					<span class="contents_result_detail_amount_price">¥<?php echo number_format($result['Reservation']['amount']); ?></span>
				</div>
				<hr class="rent-margin">
				<div class="rent-padding">
<?php
		if (!empty($result['Cards'])) {
			foreach ($result['Cards']['url'] as $key => $card) {
				echo $this->Html->image($card, array('alt' => $result['Cards']['name'][$key], 'class' => 'inline-block va-middle'));
			}
		}
?>
<?php
		//事前決済済みかどうかで表示を切り替える
		if (!empty($isPaidInAdvance)) {
			if ($isPaidInAdvance == true) {
				echo "クレジットカード事前決済";
			} else {
?>
					<span class="inline-block va-middle">お支払いは当日お受取手続きの際に、店舗で精算させて頂きます。<br>
<?php
			if ($result['Client']['accept_cash'] == 1 && $result['Client']['accept_card'] == 1) {
				echo "原則クレジットカードまたは現金払いです。";
			} else if ($result['Client']['accept_cash'] == 1) {
				echo "現金払いです。";
			} else if ($result['Client']['accept_card'] == 1) {
				echo "クレジットカードのみ";
			}
?>
					</span>
<?php
			}
		}
?>


			  	</div>
			</td>
		</tr>
		<tr>
			<th>キャンセルポリシー</th>
			<td class="frame">
<?php
		//事前決済済みかどうかで表示を切り替える
		if (!empty($isPaidInAdvance) && $isPaidInAdvance == true) {
?>
				ご予約をキャンセルされる場合、下記のキャンセル料を申し受けます。<br>
				<br>
				〈キャンセル料〉<br>
				<?php echo $result['CancelPolicy']; ?><br>
				・予約時間を１時間以上過ぎてもご連絡のない場合は、キャンセルとして処理させていただきます。<br>
				・無連絡キャンセルの場合、ご返金はいたしかねますのでご了承ください。<br>
				<br>
				■キャンセルポリシーに関するお知らせ<br>
				<?php echo nl2br($result['Client']['cancel_policy']);?>
<?php
		} else {
?>
					予約をキャンセルされる場合、下記のキャンセル料を申し受けます。<br>
					<br>
					<?php echo $result['CancelPolicy']; ?><br>
					・予約時間を１時間以上過ぎてもご連絡のない場合は、キャンセルとして処理させていただきます。<br>
					<?php echo nl2br($result['Client']['cancel_policy']);?>
<?php
		}
?>

			</td>
		</tr>
	</table>

	<div class="h3_wrap st-table rent-margin-bottom">
		<div class="st-table_cell">
			<h3>お客様情報</h3>
		</div>
	</div>

	<table class="contents_mypage_tbl rent-margin-bottom">
		<tr>
			<th>氏名カナ</th>
			<td>
				<span><?php echo $result['Reservation']['last_name']; ?> <?php echo $result['Reservation']['first_name']; ?></span>
				<?php /* echo $this->Html->link('お客様情報を登録', '/mypages/edit/name/', array('class' => 'btn btn_plain')); */ ?>
			</td>
		</tr>
		<tr>
			<th>電話番号</th>
			<td>
				<?php echo $result['Reservation']['tel']; ?>
				<?php
					if ($isVisibleButton) {
						echo $this->Html->link('電話番号を変更', '/mypages/edit/tel/', array('class' => 'btn btn_plain'));
					}
				?>
			</td>
		</tr>
		<tr>
			<th>メールアドレス</th>
			<td>
				<?php echo $result['Reservation']['email']; ?>
				<?php
					if ($isVisibleButton) {
						echo $this->Html->link('メールアドレスを変更', '/mypages/edit/mail/', array('class' => 'btn btn_plain'));
					}
				?>
			</td>
		</tr>
		<tr>
			<th>ご利用人数</th>
			<td>
				大人（12歳以上）<?php echo $result['Reservation']['adults_count']; ?>名&emsp;
<?php
		if (!empty($result['Reservation']['children_count'])) {
?>
				/&emsp;子供（6〜11歳）<?php echo $result['Reservation']['children_count']; ?>名&emsp;
<?php
		}
		if (!empty($result['Reservation']['infants_count'])) {
?>
				/&emsp;幼児（6歳未満）<?php echo $result['Reservation']['infants_count']; ?>名
<?php
		}
?>
<?php
		if ($isVisibleButton) {
			echo $this->Html->link('ご利用人数を変更', '/mypages/edit/count/', array('class' => 'btn btn_plain'));
		}
?>

			</td>
		</tr>
<?php
		if (!empty($arrivalAirport)) {
?>
		<tr>
			<th>到着便</th>
			<td>
<?php
			if (!empty($result['Reservation']['arrival_flight_number'])) {
?>
				<span><?php echo $result['Reservation']['arrival_flight_number']; ?></span>
<?php
			} else {
?>
				<span>未登録</span>
<?php
			}
			if ($isVisibleButton) {
				echo $this->Html->link('空港到着便を登録', '/mypages/edit/airport/', array('class' => 'btn btn_plain'));
			}
?>
			</td>
		</tr>
<?php
		}
		if (!empty($departureAirport)) {
?>
		<tr>
			<th>出発便</th>
			<td>
<?php
			if (!empty($result['Reservation']['departure_flight_number'])) {
?>
				<span><?php echo $result['Reservation']['departure_flight_number']; ?></span>
<?php
			} else {
?>
				<span>未登録</span>
<?php
			}
			if ($isVisibleButton) {
				echo $this->Html->link('空港出発便を登録', '/mypages/edit/airport/', array('class' => 'btn btn_plain'));
			}
?>
			</td>
		</tr>
<?php
		}
?>
	</table>

	<div class="h3_wrap st-table rent-margin-bottom">
		<div class="st-table_cell">
			<h3>受取店舗の情報</h3>
		</div>
	</div>

	<table class="contents_mypage_tbl rent-margin-bottom">
		<tr>
			<th>受取店舗</th>
			<td>
				<?php
				if(!empty($result['Client']['sp_logo_image'])){
					echo $this->Html->image('logo/square/'.$result['Client']['id'].'/'.$result['Client']['sp_logo_image'], array('alt' => $result['Client']['name'].'ロゴ', 'class' => 'contents_complete_tbl_img','width'=>64));
					echo '<br>';
				}
				?>
				<div class="contents_complete_tbl_shop">
					<span class="contents_complete_tbl_shopName"><?php echo $result['RentOffice']['name']; ?></span>
					<span class="contents_complete_tbl_shopTel"><?php echo $result['RentOffice']['tel']; ?></span>
				</div>
			</td>
		</tr>
<?php
		if ($result['Client']['inquiry_display'] != '0') {
?>
		<tr>
			<th>お問い合わせ</th>
			<td>
				<p><?php echo $result['Client']['name']; ?>にお問い合わせを送信します。※ご返信にお時間をいただく場合がございます。<br>お急ぎのご質問・ご連絡は、電話で店舗にご確認ください。</p>
				<?php echo $this->Html->link('お問い合わせフォームへ', '/mypages/edit/contents/', array('class' => 'btn btn_plain')); ?>
			</td>
		</tr>
<?php
		}
		if (!empty($result['RentOffice']['email'])) {
?>
		<tr>
			<th>メールアドレス</th>
			<td><?php echo $result['RentOffice']['email']; ?></td>
		</tr>
<?php
		}
?>
		<tr class="office-data">
			<th>営業時間</th>
			<td>
				<?php echo date('H:i', strtotime($result['RentOffice']['office_hours_from'])); ?>　～　<?php echo date('H:i', strtotime($result['RentOffice']['office_hours_to'])); ?>
<?php
		if (!empty($result['RentOffice']['start_day']) && !empty($result['RentOffice']['end_day'])) {
?>
				<p class="note_irregular-business-hours">
					<i class="icm-info-button-fill" aria-hidden="true"></i>
					<?php echo date('Y/m/d', strtotime($result['RentOffice']['start_day'])); ?>～<?php echo date('Y/m/d', strtotime($result['RentOffice']['end_day'])); ?> は営業時間が通常と異なります。詳細は店舗へお問い合わせください。
				</p>
<?php
		}
?>
			</td>
		</tr>
<?php
		if (!empty($result['RentOffice']['rent_meeting_info'])) {
?>
		<tr>
			<th>送迎や待ち合わせに関する情報</th>
			<td><?php echo nl2br(h($result['RentOffice']['rent_meeting_info'])); ?></td>
		</tr>
<?php
		}
?>
<?php
		if (!empty($result['RentOffice']['notification'])) {
?>
		<tr>
			<th>店舗からのご案内</th>
			<td><?php echo nl2br(h($result['RentOffice']['notification'])); ?></td>
		</tr>
<?php
		}
?>
		<tr>
			<th>ご案内・アクセス</th>
			<td>
				<p>住所：<?php echo $result['RentOffice']['address']; ?><br>アクセス：<?php echo $result['RentOffice']['access_dynamic']; ?></p>
<?php
		if (!empty($result['RentOffice']['address'])) {
?>
				<p>店舗周辺の地図</p>
				<div id="rent_map" class="google_map">
					<iframe width="100%" height="100%" frameborder="0" style="border:0;" src="https://www.google.com/maps/embed/v1/place?key=<?=getGoogleAPIKey( GOOGLE_APIKEY_Maps_Embed )?>&q=<?= $result['RentOffice']['address'] ?>"></iframe>
				</div>
<?php
		}
?>
			</td>
		</tr>
	</table>

	<div class="h3_wrap st-table rent-margin-bottom">
		<div class="st-table_cell">
			<h3>返却店舗の情報</h3>
		</div>
	</div>

	<table class="contents_mypage_tbl rent-margin-bottom">
		<tr>
			<th>店舗</th>
			<td>
<?php
		if (!empty($result['Client']['sp_logo_image'])) {
			echo $this->Html->image('logo/square/'.$result['Client']['id'].'/'.$result['Client']['sp_logo_image'], array('alt' => $result['Client']['name'].'ロゴ', 'class' => 'contents_complete_tbl_img','width'=>64));
			echo '<br>';
		}
?>
				<div class="contents_complete_tbl_shop">
					<span class="contents_complete_tbl_shopName"><?php echo $result['ReturnOffice']['name']; ?></span>
					<span class="contents_complete_tbl_shopTel"><?php echo $result['ReturnOffice']['tel']; ?></span>
				</div>
			</td>
		</tr>
<?php
		if ($result['Client']['inquiry_display'] != '0') {
?>
		<tr>
			<th>お問い合わせ</th>
			<td>
				<p><?php echo $result['Client']['name']; ?>にお問い合わせを送信します。※ご返信にお時間をいただく場合がございます。<br>お急ぎのご質問・ご連絡は、電話で店舗にご確認ください。</p>
				<?php echo $this->Html->link('お問い合わせフォームへ', '/mypages/edit/contents/', array('class' => 'btn btn_plain')); ?>
			</td>
		</tr>
<?php
		}
		if (!empty($result['ReturnOffice']['email'])) {
?>
		<tr>
			<th>メールアドレス</th>
			<td><?php echo $result['ReturnOffice']['email']; ?></td>
		</tr>
<?php
		}
?>
		<tr class="office-data">
			<th>営業時間</th>
			<td>
				<?php echo date('H:i', strtotime($result['ReturnOffice']['office_hours_from'])); ?>　～　<?php echo date('H:i', strtotime($result['ReturnOffice']['office_hours_to'])); ?>
<?php
		if (!empty($result['ReturnOffice']['start_day']) && !empty($result['ReturnOffice']['end_day'])) {
?>
				<p class="note_irregular-business-hours"><i class="icm-info-button-fill" aria-hidden="true"></i>
					<?php echo date('Y/m/d', strtotime($result['ReturnOffice']['start_day'])); ?>～<?php echo date('Y/m/d', strtotime($result['ReturnOffice']['end_day'])); ?> は営業時間が通常と異なります。詳細は店舗へお問い合わせください。
				</p>
<?php
		}
?>
			</td>
		</tr>
<?php
		if (!empty($result['ReturnOffice']['return_meeting_info'])) {
?>
		<tr>
			<th>送迎や待ち合わせに関する情報</th>
			<td><?php echo nl2br(h($result['ReturnOffice']['return_meeting_info'])); ?></td>
		</tr>
<?php
		}
?>
		<tr>
			<th>ご案内・アクセス</th>
			<td>
				<p>住所：<?php echo $result['ReturnOffice']['address']; ?><br>アクセス：<?php echo $result['ReturnOffice']['access_dynamic']; ?></p>
<?php
		if (!empty($result['ReturnOffice']['address'])) {
?>
				<p>店舗周辺の地図</p>
				<div id="shop-map-return" class="google_map">
					<iframe width="100%" height="100%" frameborder="0" style="border:0;" src="https://www.google.com/maps/embed/v1/place?key=<?=getGoogleAPIKey( GOOGLE_APIKEY_Maps_Embed )?>&q=<?= $result['ReturnOffice']['address'] ?>"></iframe>
				</div>
<?php
		}
?>
			</td>
		</tr>
	</table>

<?php
	} else { // キャンセル済みの場合
?>

	<div class="h2_wrap text_center st-table">
		<div class="st-table_cell">
			<h2><?php echo $result['Reservation']['last_name']; ?><?php echo $result['Reservation']['first_name']; ?> 様</h2>
		</div>
	</div>

	<div class="h3_wrap st-table rent-margin-bottom-l">
		<div class="st-table_cell">
			<h3>ご予約はキャンセルされております。</h3>
<?php
		if (!_isLogin()){
			echo $this->Html->link('ログアウト', '/mypages/logout/', array('class' => 'btn btn_plain'));
		}
?>
		</div>
	</div>
	<h4 class="hd-left-bordered">予約状況</h4>
	<table class="contents_mypage_tbl rent-margin-bottom-l">
		<tr class="">
			<th>予約番号</th>
			<td>
				<span class="contents_mypage_number"><?php echo $result['Reservation']['reservation_key']; ?></span>
<?php
		if ($result['Reservation']['reservation_status_id'] == Constant::STATUS_RESERVATION && $isVisibleButton) {
			echo $this->Html->link('この予約をキャンセルする', '/mypages/cancel/', array('class' => 'btn btn_plain'));
		}
?>
			</td>
		</tr>
		<tr class="">
			<th>予約申し込み日</th>
			<td>
<?php
		$w = $week[date('w', strtotime($result['Reservation']['created']))];
		echo date('Y年m月d日 ('.$w.') H:i', strtotime($result['Reservation']['created']));
?>
			</td>
		</tr>
		<tr class="">
			<th>予約状況</th>
			<td><?php echo $reservationStatus[$result['Reservation']['reservation_status_id']]; ?></td>
		</tr>
		<tr class="">
			<th>キャンセル日時</th>
			<td>
<?php
		$rentW = $week[date('w', strtotime($result['Reservation']['cancel_datetime']))];
		echo date('Y年m月d日 ('.$rentW.') H:i', strtotime($result['Reservation']['cancel_datetime']));
?>
			</td>
		</tr>
		<tr class="">
			<th>受取日時</th>
			<td>
<?php
		$rentW = $week[date('w', strtotime($result['Reservation']['rent_datetime']))];
		echo date('Y年m月d日 ('.$rentW.') H:i', strtotime($result['Reservation']['rent_datetime']));
?>
			</td>
		</tr>
		<tr class="">
			<th>返却日時</th>
			<td>
<?php
		$returnW = $week[date('w', strtotime($result['Reservation']['return_datetime']))];
		echo date('Y年m月d日 ('.$returnW.') H:i', strtotime($result['Reservation']['return_datetime']));
?>
			</td>
		</tr>
		<tr class="">
			<th>レンタカー事業者</th>
			<td>
<?php
		if (!empty($result['Client']['sp_logo_image'])) {
			echo $this->Html->image('logo/square/'.$result['Client']['id'].'/'.$result['Client']['sp_logo_image'], array('alt' => $result['Client']['name'].'ロゴ', 'class' => 'contents_complete_tbl_img', 'width' => 64));
			echo '<br>';
		}
?>
				<div class="contents_complete_tbl_shop">
					<span><?php echo $result['Client']['name']; ?></span>
<?php
		if (!empty($result['Client']['clause_pdf'])) {
			echo $this->Html->link('貸渡約款を見る', '/files/clause_pdf/'.$result['Client']['clause_pdf'], array('target' => '_blank'));
?>
					<p class="text_danger">※ご契約につきましては、記載のレンタカー事業者との契約となります。上記、貸渡約款をごらんください。</p>
<?php
		}
?>
				</div>
			</td>
		</tr>
		<tr>
			<th>お支払合計金額</th>
			<td class="contents_result_detail_amount">
				<div class="text_right rent-padding">
					<span class="bubble bubble-right">税込価格</span>
					<span class="contents_result_detail_amount_price">¥<?php echo number_format($result['Reservation']['amount']); ?></span>
				</div>
			</td>
		</tr>
		<tr>
			<th>キャンセルポリシー</th>
			<td>
<?php
		//事前決済済みかどうかで表示を切り替える
		if (!empty($isPaidInAdvance) && $isPaidInAdvance == true) {
?>
				ご予約をキャンセルされる場合、下記のキャンセル料を申し受けます。<br>
				<br>
				〈キャンセル料〉<br>
				<?php echo $result['CancelPolicy']; ?><br>
				・予約時間を１時間以上過ぎてもご連絡のない場合は、キャンセルとして処理させていただきます。<br>
				・無連絡キャンセルの場合、ご返金はいたしかねますのでご了承ください。<br>
				<br>
				■キャンセルポリシーに関するお知らせ<br>
				<?php echo nl2br($result['Client']['cancel_policy']);?>
<?php
		} else {
?>
					ご予約をキャンセルされる場合、下記のキャンセル料を申し受けます。<br>
					<br>
					<?php echo $result['CancelPolicy']; ?><br>
					・予約時間を１時間以上過ぎてもご連絡のない場合は、キャンセルとして処理させていただきます。<br>
					<?php echo nl2br($result['Client']['cancel_policy']);?>
<?php
		}
?>
			</td>
		</tr>
	</table>
<?php
	}
?>

<?php
    // お問い合わせ履歴
	if (!empty($reservationMail)) {
?>
	<div class="h3_wrap st-table rent-margin-bottom">
		<div class="st-table_cell">
			<h3>お問い合わせ</h3>
		</div>
	</div>
	<div>
<?php
        foreach ($reservationMail as $key => $value) {
?>
		<p class="h3_wrap st-table">
			<?php echo !empty($value['Staff']['name']) ? $result['Client']['name'] : 'お客様'; ?>
			<span><?php echo date('Y-m-d H時i分', strtotime($value['ReservationMail']['mail_datetime'])); ?></span>
		</p>
		<p class="rent-margin-bottom" style="word-wrap: break-word;"><?php echo nl2br(h($value['ReservationMail']['contents'])); ?></p>
<?php
        }
?>
	</div>
<?php
	}
?>
</div>