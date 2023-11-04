<?php
	if (Constant::isReservedStatus($result['Reservation']['reservation_status_id'])) {
?>

<?php
	if (!empty($sessionMessage)) {
		echo $this->element('session_message');
	}
?>
<!-- コンテンツ -->
<div id="js-content" class="sp_mypages-index_page">
	<h2 class="title_blue_line"><span>予約の照会・変更・取消</span></h2>
<?php
		if(!empty($arrivalAirport) || !empty($departureAirport)){
?>
	<section class="">
		<div class="inner">
<?php
			if(!empty($arrivalAirport) && empty($result['Reservation']['arrival_flight_number'])){
?>
			<div class="caution">
				<p>空港到着便はまだ未入力です</p>
				<span>※受取日の前日までにご登録がない場合、空港送迎バスをご利用いただけない場合がございます。</span>
			</div>
<?php
		if ($isVisibleButton) {
?>
			<div class="ac mb10px">
				<?= $this->Html->link('登録はこちら', '/mypages/edit/airport/', array('class' => 'btn bg_orange')); ?>
			</div>
<?php
		}
			}
?>
<?php
			if(!empty($departureAirport) && empty($result['Reservation']['departure_flight_number'])){
?>
			<div class="caution">
				<p>空港出発便はまだ未入力です</p>
				<span>※返却日の前日までにご登録がない場合、空港送迎バスをご利用いただけない場合がございます。</span>
			</div>
<?php
		if ($isVisibleButton) {
?>
			<div class="ac mb10px">
				<?= $this->Html->link('登録はこちら', '/mypages/edit/airport/', array('class' => 'btn bg_orange')); ?>
			</div>
<?php
		}
			}
?>
		</div>
	</section>
<?php
		}
?>

	<section class="plan_form" id="plan_form">
		<h3 class="plan_form_title">
<?php
		if ($hokkaidoCampaignFlg) {
?>
			<p class="campaign-notice">
				ご予約いただいたプランは<b>HOKKAIDriveキャンペーン</b>の対象です。
				キャンペーンの事前登録はこちらから(<a target="_blank" rel="noopener noreferrer" href="https://hokkaidrive.com">https://hokkaidrive.com</a>)お願いいたします。
			</p>
<?php
		}
?>
			予約状況の確認
		</h3>
		<section>
			<div class="reservation_number inner mb10px">
				<h4>お客様の予約番号</h4>
				<div class="number mb10px">
					<p class=""><?php echo $result['Reservation']['reservation_key']; ?></p>
				</div>

				<?php // セルフチェックイン案内 
					echo $this->element('sp_note_self-checkin', [
						'planname' => $result['Commodity']['name'], 
						'clientid' => $result['Client']['id']
					]);
				?>

				<ul>
<?php
		if($result['Reservation']['reservation_status_id'] == Constant::STATUS_RESERVATION && $cancelDeadlineDatetime > date('Y-m-d H:i:s') && !($isEconMaintenance && $isPaidInAdvance) && $isVisibleButton){
?>
					<li><?= $this->Html->link('この予約をキャンセル', '/mypages/cancel/'); ?></li>
<?php
		}
/* TASK-2209の理由でコメントアウト
					<li><?php echo $this->Html->link('予約完了メールを再送', '/resend/'); ?></li>
*/
?>
				</ul>
				<div class="cancel_text">
					<h5>予約確認ページからのキャンセル</h5>
					<p>
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
					</p>
				</div>
			</div>
<?php
		if ($unpaidAmount > 0){
?>
			<h4 class="title_blue">未払金額のご確認</h4>
			<div class="plan_info_body_price price_block">
				<div class="plan_info_left">
					<p class="price_block_title">未払金額(税込)</p>
				</div>
				<div class="plan_info_right">
					<p class="price_block_price">&yen;<?php echo number_format($unpaidAmount); ?></p>
				</div>
			</div>
			<div class="inner txtc">
<?php 
			if (!$paymentApi){
?>
				<!-- payment1sp -->
				<button class="btn-type-primary" onclick="location.href='/rentacar/mypages/input/'">お支払いへ</button>
<?php 
			} else { 
?>
<?php 
				if (empty($cartId)){
?>
				<!-- payment2sp --> 
				<button class="btn-type-primary" onclick="location.href='/rentacar/mypages/input/'">お支払いへ</button>
<?php 
				} else {
?>
				<!-- payment3sp -->
				<button class="btn-type-primary" onclick='location.href="<?php echo $paymentRedirectUrl; ?>"'>お支払いへ</button>
<?php 
				}
?>
<?php 
			} 
?>
				<span class="plan_warning_red font_b">(ご入金期限:<?php echo $result['Reservation']['payment_limit_datetime']; ?>)</span>
			</div>
<?php
		}
?>
			<!--/reservation_number-->
			<h4 class="title_blue">予約レンタカー情報</h4>
			<div class="plan_info_block plan_view">
				<div class="plan_info_block_body">
					<div class="plan_caption">
<?php
		$commodityName = $result['CarType']['name'].' ('.$result['CarModel'];
		// 車種指定フラグ
		$flgModelSelect = (!empty($result['CommodityItem']['car_model_id']));
		($flgModelSelect) ? $commodityName .= '）' : $commodityName .= '他）';
		echo $commodityName;
?>
					</div>
					<ul class="plan_detail_ul">
						<li class="plan_detail_li car_photo">
							<div class="car_photo_wrap">
<?php
		/*$imageRelativeUrl = !empty($commodityImages[0]['image_relative_url']) ? '/img/commodity_reference/' . $result['Client']['id'] . '/' . $commodityImages[0]['image_relative_url'] : '/img/noimage.png';*/
		$imageRelativeUrl = !empty($result['Commodity']['image_relative_url']) ? '/img/commodity_reference/' . $result['Client']['id'] . '/' . $result['Commodity']['image_relative_url'] : '/img/noimage.png';
		echo $this->Html->image($imageRelativeUrl, array('class' => 'plan_car_photo', 'alt' => $result['CarModel']));
?>
							</div>
						</li>
						<li class="plan_detail_li">
							<div class="plan_detail_topics">
								<p class="capacity">定員：<?php echo $result['Recommend']['capacity']; ?>名</p>
							</div>
							<ul class="plan_detail_spec">
<?php
		$flgNewRegistration = ( $result['Commodity']['new_car_registration'] == 1 || $result['Commodity']['new_car_registration'] == 2 );
?>
<?php
		if($result['Commodity']['smoking_flg'] == 0){
?>
								<li><i class="icm-no_smoking icon no-smoking"></i>禁煙車</span></li>
<?php
		} else if($result['Commodity']['smoking_flg'] == 1){
?>
								<li><i class="icm-smoking icon"></i>喫煙可</span></li>
<?php
		}
?>

<?php
		if($flgModelSelect){
?>
								<li><i class="icm-car-side icon"></i>車種確約</span></li>
<?php
		}
?>

<?php
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
		foreach($equipmentList as $equipment) {
			$equipment = $equipment['Equipment'];
			if (!empty($result['Equipment'][$equipment['id']])) {
?>
						<li class="plan_equipment_li"><?php echo $equipment['name']; ?></li>
<?php
			}
		}
?>

<?php
		if ($result['Commodity']['transmission_flg'] == 0) {
?>
						<li class="plan_equipment_li is_active">AT車</li>
<?php
		}
?>
					</ul>
				</div>
			</div>
			<div class="reservation_info_outline">
				<div class="inner">
					<ul class="plan_info_return_date">
						<li><span>受取日</span>
<?php
		$w = $week[date('w', strtotime($result['Reservation']['rent_datetime']))];
		echo date('Y年m月d日 ('.$w.') H:i', strtotime($result['Reservation']['rent_datetime']));
?>
						</li>
						<li><span>返却日</span>
<?php
		$w = $week[date('w', strtotime($result['Reservation']['return_datetime']))];
		echo date('Y年m月d日 ('.$w.') H:i', strtotime($result['Reservation']['return_datetime']));
?>
						</li>
					</ul>
				</div>
				<div class="plan_info_body_price price_block">
					<div class="plan_info_left">
						<p class="price_block_title">合計料金<span>（税込）</span></p>
						<p class="caption_gray"><?php echo $rentalPeriod; ?>料金</p>
					</div>
					<div class="plan_info_right">
						<p class="price_block_price">&yen;<?= number_format($result['Reservation']['amount']); ?></p>
					</div>
				</div>
			</div>
		</section>

<?php
		if($result['Client']['inquiry_display'] != '0'){
?>
		<section>
			<h4 class="title_blue">お問い合わせ・返信フォーム</h4>
			<div class="inner">
				<div class="mb10px text">
					<p>
						<?php echo $result['Client']['name']; ?>にお問い合わせを送信します。
						<br> ※ご返信にお時間を頂く場合がございます。
						<br> お急ぎのご質問・ご連絡は、下記窓口までご確認ください。
						<br>
					</p>
				</div>
				<div class="ac mb20px">
					<?= $this->Html->link('お問い合わせフォームへ', '/mypages/edit/contents/', array('class' => 'btn bg_gray_3')); ?>
				</div>
			</div>
			<div class="mb20px">
				<dl class="accordion">
					<dt class="trigger">お問い合わせ履歴<span class="open-close">open</span></dt>
					<dd class="acordion_tree">
<?php
			foreach($reservationMail as $key => $value){
?>
						<p class="h3_wrap st-table bg_gray_2 p_5px mb10px">
							<?= !empty($value['Staff']['name']) ? $result['Client']['name'] : 'お客様'; ?>
							<span><?php echo date('Y-m-d H時i分', strtotime($value['ReservationMail']['mail_datetime'])); ?></span>
						</p>
						<p class="rent-margin-bottom mb10px" style="word-wrap: break-word;"><?php echo nl2br(h($value['ReservationMail']['contents'])); ?></p>
<?php
			} // /foreach
?>
					</dd>
				</dl>
			</div>
		</section>
<?php
		}
?>
		<h3 class="plan_form_title">お客様情報</h3>
		<section class="plan_form_ inner sec">
			<table>
				<tr>
					<th>氏名</th>
				</tr>
				<tr>
					<td class="select_type_line1 clearfix">
					<?= $result['Reservation']['last_name']; ?> <?php echo $result['Reservation']['first_name']; ?>
					</td>
				</tr>
			</table>
			<table>
				<tr>
					<th>携帯電話番号</th>
				</tr>
				<tr>
					<td class="select_type_line1">
					<?= $result['Reservation']['tel']; ?>
					</td>
				</tr>
			</table>
<?php
			if ($isVisibleButton) {
				echo $this->Html->link('電話番号を変更する', '/mypages/edit/tel/', array('class' => 'btn bg_orange change_btn'));
			}
?>
			<table>
				<tr>
					<th>メールアドレス</th>
				</tr>
				<tr>
					<td class="select_type_line1">
					<?= $result['Reservation']['email']; ?>
					</td>
				</tr>
			</table>
<?php
			if ($isVisibleButton) {
				echo $this->Html->link('メールアドレスを変更する', '/mypages/edit/mail/', array('class' => 'btn bg_orange change_btn'));
			}
?>
			
			<div class="ac">
				<?php /* echo $this->Html->link('お客様情報を変更する', '/mypages/edit/name/', array('class' => 'btn btn2 bg_gray_3')); */ ?>
			</div>
		</section>

		<h3 class="plan_form_title">ご利用人数</h3>
		<section class="plan_form_ inner sec">
			<div class="people_num">
				<p>
					<span>大人</span><?php echo $result['Reservation']['adults_count']; ?>人
				</p>
				<p>
					<span>子供</span><?php echo $result['Reservation']['children_count']; ?>人
				</p>
				<p>
					<span>幼児</span><?php echo $result['Reservation']['infants_count']; ?>人
				</p>
			</div>
<?php
			if ($isVisibleButton) {
				echo $this->Html->link('人数を変更する', '/mypages/edit/count/', array('class' => 'btn bg_orange change_btn'));
			}
?>
		</section>

		<h3 class="plan_form_title">予約詳細</h3>
		<section>
			<h4 class="title_blue border-top-none">貸出期間</h4>
			<div class="inner">
				<table>
					<tr>
						<th>受取日</th>
					</tr>
					<tr>
						<td>
							<div class="date-input">
								<?= date('Y-m-d H:i', strtotime($result['Reservation']['rent_datetime'])); ?>
							</div>
						</td>
					</tr>
					<tr>
						<th>返却日</th>
					</tr>
					<tr>
						<td>
							<div class="date-input">
								<?php echo date('Y-m-d H:i', strtotime($result['Reservation']['return_datetime'])); ?>
							</div>
						</td>
					</tr>
				</table>
			</div>

			<h4 class="title_blue">車両の受取営業所</h4>
			<div class="inner">
				<table>
					<tr>
						<th>受取営業所</th>
					</tr>
					<tr>
						<td class="select_type_line1">
							<div class="search_select">
								<?php echo $result['RentOffice']['name']; ?>
							</div>
						</td>
					</tr>
				</table>
<?php
		if(!empty($arrivalAirport)){
?>
				<table>
					<tr>
						<th>空港到着便名</th>
					</tr>
					<tr>
						<td class="select_type_line1">
							<div class="search_select">
								<?php echo $result['Reservation']['arrival_flight_number']; ?>
							</div>
						</td>
					</tr>
				</table>
<?php
		}
?>
			</div>

<?php
		if(!empty($arrivalAirport)){
?>
			<div class="inner">
<?php
			if(empty($result['Reservation']['arrival_flight_number'])){
?>
				<div class="caution p_5px">
					<p class="">空港到着便はまだ未入力です</p>
				</div>
<?php
			}
			if ($isVisibleButton) {
?>
				<div class="ac mb10px">
					<?= $this->Html->link('登録はこちら', '/mypages/edit/airport/', array('class' => 'btn bg_orange')); ?>
				</div>
<?php
			}
?>
			</div>
<?php
		}
?>
			<h4 class="title_blue">受取店舗の情報</h4>
			<div class="inner">
				<div class="inquiry_body">
					<div class="clearfix mb20px">
						<div class="inquiry_body_photo fl">
							<?= $this->Html->image('logo/square/'.$result['Client']['id'].'/'.$result['Client']['sp_logo_image'], array('alt' => $result['Client']['name'], 'class' => 'contents_result_list_body_detail_img st-table_cell va-middle')); ?>
						</div>
						<div class="inquiry_body_text fl">
							<ul>
								<li>
									<?= $result['Client']['name']; ?><br><?= $result['RentOffice']['name']; ?>
								</li>
								<li>
									<p class="font_b color_red"><?php echo $result['RentOffice']['tel']; ?></p>
								</li>
								<li>
									営業時間 <?= date('H:i', strtotime($result['RentOffice']['office_hours_from'])); ?>〜<?= date('H:i', strtotime($result['RentOffice']['office_hours_to'])); ?>
<?php 
		if(!empty($result['RentOffice']['start_day']) && !empty($result['RentOffice']['end_day'])){
?>
									<p class="note_irregular-business-hours">
										<i class="icm-info-button-fill" aria-hidden="true"></i>
										<?php echo date('Y/m/d', strtotime($result['RentOffice']['start_day'])); ?>～<?php echo date('Y/m/d', strtotime($result['RentOffice']['end_day'])); ?> は営業時間が通常と異なります。詳細は店舗へお問い合わせください。
									</p>
<?php
		}
?>
								</li>
							</ul>
						</div>
					</div>
				</div>
<?php
		if(!empty($result['RentOffice']['rent_meeting_info'])){
?>
				<div class="meeting_info mb20px">
					<p class="title">送迎や待ち合わせに関する情報</p>
					<p class="text"><?php echo nl2br(h($result['RentOffice']['rent_meeting_info'])); ?></p>
				</div>
<?php
		}
?>

<?php
		if(!empty($result['RentOffice']['notification'])){
?>
				<div class="meeting_info mb20px">
					<p class="title">店舗からのご案内</p>
					<p class="text"><?php echo nl2br(h($result['RentOffice']['notification'])); ?></p>
				</div>
<?php
		}
?>

<?php
		if(!empty($result['RentOffice']['address'])){
?>
				<div class="access_box mb20px">
					<?= $result['RentOffice']['address']; ?>
				</div>
				<div class="map mb20px">
					<div id="rent_map" style="width: 100%; height: 300px;">
						<iframe width="100%" height="300" frameborder="0" style="border:0;" src="https://www.google.com/maps/embed/v1/place?key=<?=getGoogleAPIKey( GOOGLE_APIKEY_Maps_Embed )?>&q=<?= $result['RentOffice']['address'] ?>"></iframe>
					</div>
				</div>
<?php
		}
?>
			</div>

			<h4 class="title_blue">車両の返却営業所</h4>
			<section>
				<div class="inner">
					<table>
						<tr>
							<th>返却営業所</th>
						</tr>
						<tr>
							<td class="select_type_line1">
								<div class="search_select">
									<?= $result['ReturnOffice']['name']; ?>
								</div>
							</td>
						</tr>
					</table>
<?php
		if(!empty($departureAirport)){
?>
					<table>
						<tr>
							<th>空港出発便名</th>
						</tr>
						<tr>
							<td class="select_type_line1">
								<div class="search_select">
									<?= $result['Reservation']['departure_flight_number']; ?>
								</div>
							</td>
						</tr>
					</table>
<?php
		}
?>
				</div>

<?php
		if(!empty($departureAirport)){
?>
				<div class="inner">
<?php
			if(empty($result['Reservation']['departure_flight_number'])){
?>
					<div class="caution p_5px">
						<p class="">空港出発便はまだ未入力です</p>
					</div>
<?php
			}
			if ($isVisibleButton) {
?>
					<div class="ac mb10px">
						<?= $this->Html->link('登録はこちら', '/mypages/edit/airport/', array('class' => 'btn bg_orange')); ?>
					</div>
<?php
			}
?>
				</div>
<?php
		}
?>
			</section>

			<h4 class="title_blue">返却店舗の情報</h4>
			<div class="inner">
				<div class="inquiry_body">
					<div class="clearfix mb20px">
						<div class="inquiry_body_photo fl">
							<?= $this->Html->image('logo/square/'.$result['Client']['id'].'/'.$result['Client']['sp_logo_image'], array('alt' => $result['Client']['name'], 'class' => 'contents_result_list_body_detail_img st-table_cell va-middle')); ?>
						</div>
						<div class="inquiry_body_text fl">
							<ul>
								<li>
									<?= $result['Client']['name']; ?><br><?php echo $result['ReturnOffice']['name']; ?>
								</li>
								<li>
									<p class="font_b color_red"><?php echo $result['ReturnOffice']['tel']; ?></p>
								</li>
								<li>
									営業時間 <?= date('H:i', strtotime($result['ReturnOffice']['office_hours_from'])); ?>〜<?= date('H:i', strtotime($result['ReturnOffice']['office_hours_to'])); ?>
<?php 
		if(!empty($result['ReturnOffice']['start_day']) && !empty($result['ReturnOffice']['end_day'])){
?>
									<p class="note_irregular-business-hours">
										<i class="icm-info-button-fill" aria-hidden="true"></i>
										<?php echo date('Y/m/d', strtotime($result['ReturnOffice']['start_day'])); ?>～<?php echo date('Y/m/d', strtotime($result['ReturnOffice']['end_day'])); ?> は営業時間が通常と異なります。詳細は店舗へお問い合わせください。
									</p>
<?php 
		}
?>
								</li>
							</ul>
						</div>
					</div>
				</div>
<?php
		if(!empty($result['ReturnOffice']['return_meeting_info'])){
?>
				<div class="meeting_info mb20px">
					<p class="title">送迎や待ち合わせに関する情報</p>
					<p class="text"><?php echo nl2br(h($result['ReturnOffice']['return_meeting_info'])); ?></p>
				</div>
<?php
		}
?>
<?php
		if(!empty($result['ReturnOffice']['address'])){
?>
				<div class="access_box mb20px">
					<?= $result['ReturnOffice']['address']; ?>
				</div>
				<div class="map mb20px">
					<div id="return_map" style="width: 100%; height: 300px;">
						<iframe width="100%" height="300" frameborder="0" style="border:0;" src="https://www.google.com/maps/embed/v1/place?key=<?=getGoogleAPIKey( GOOGLE_APIKEY_Maps_Embed )?>&q=<?= $result['ReturnOffice']['address'] ?>"></iframe>
					</div>
				</div>
<?php
		}
?>
			</div>
		</section>
	</section><!-- /plan_form -->

	<section class="plan_comfirmation">
		<section>
			<h3 class="plan_form_title">お支払い料金</h3>
			<table class="price_table mb10px border-top-none">
<?php
		if(!empty($dropOffNightFee)){
			foreach($dropOffNightFee as $key => $value){
?>
				<tr>
					<th><?php echo $value['DetailType']['name']; ?></th>
					<td></td>
				</tr>
<?php
			}
		}
?>
<?php
		if(!empty($reservationChildSheet)){
			foreach($reservationChildSheet as $key => $value){
?>
				<tr>
					<th><?php echo $value['Privilege']['name']; ?></th>
					<td>×<?php echo $value['ReservationChildSheet']['count']; ?></td>
				</tr>
<?php
			}
		}
?>
<?php
		if(!empty($reservationPrivilege)){
			foreach($reservationPrivilege as $key => $value){
?>
				<tr>
					<th><?php echo $value['Privilege']['name']; ?></th>
					<td>×<?php echo $value['ReservationPrivilege']['count']; ?></td>
				</tr>
<?php
			}
		}
?>
			</table>

			<div class="inner">
				<div class="plan_info_body_price price_block clearfix">
					<div class="fl">
						<p class="price_block_title">合計料金<span>（免責補償・税込）</span></p>
						<p class="caption_gray"><?php echo $rentalPeriod; ?>料金</p>
					</div>
					<div class="fr">
						<p class="price_block_price"><span>&yen;<?php echo number_format($result['Reservation']['amount']); ?></span></p>
					</div>
				</div>

<?php
		//事前決済済みかどうかで表示を切り替える
		if (!empty($isPaidInAdvance)) {
			if ($isPaidInAdvance == true) {
				echo "クレジットカード事前決済";
			} else {

				if($result['Client']['accept_cash'] == 1 && $result['Client']['accept_card'] == 1){
?>
				<div class="ac p_5px credit_card">
<?php
					if(!empty($result['Cards'])){
						foreach($result['Cards']['url'] as $key => $card){
?>
					<?= $this->Html->image($card, array('alt' => $result['Cards']['name'][$key], 'class' => 'inline-block va-middle')); ?>
<?php
						}
					}
?>
					<div class="p_5px bg_brown mb20px">
						<p class="caption_gray">お支払いは当日お受取手続きの際に、店舗で精算させて頂きます。
						<br>原則クレジットカードまたは現金払いです。</p>
					</div>
				</div>
<?php
				} else if ($result['Client']['accept_cash'] == 1 && $result['Client']['accept_card'] == 0){
?>
				<div class="p_5px bg_brown mb20px">
					<p class="caption_gray">お支払いは当日お受取手続きの際に、店舗で精算させて頂きます。
					<br>現金払いです。</p>
				</div>
<?php
				} else if ($result['Client']['accept_cash'] == 0 && $result['Client']['accept_card'] == 1){
?>
				<div class="ac p_5px credit_card">
<?php
					if(!empty($result['Cards'])){
						foreach($result['Cards']['url'] as $key => $card){
?>
					<?= $this->Html->image($card, array('alt' => $result['Cards']['name'][$key], 'class' => 'inline-block va-middle')); ?>
<?php
						}
					}
?>
				</div>
				<div class="p_5px bg_brown mb20px">
					<p class="caption_gray">お支払いは当日お受取手続きの際に、店舗で精算させて頂きます。
					<br>クレジットカードのみ</p>
				</div>
<?php
				}
			}   
		} // /事前決済済みかどうかで表示を切り替える
?>
			</div>
		</section>
	</section><!-- /plan_comfirmation -->

	<div class="mb20px">
		<dl class="accordion">
			<dt class="trigger">注意事項<span class="open-close">open</span></dt>
			<dd class="acordion_tree"><?php echo nl2br($result['Client']['precautions']); ?></dd>
			<dt class="trigger">キャンセルポリシー<span class="open-close">open</span></dt>
			<dd class="acordion_tree">
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
				<?php echo nl2br($result['Client']['cancel_policy']); ?>
<?php
		} else {
?>
					予約をキャンセルされる場合、下記のキャンセル料を申し受けます。<br>
					<br>
					<?php echo $result['CancelPolicy']; ?><br>
					・予約時間を１時間以上過ぎてもご連絡のない場合は、キャンセルとして処理させていただきます。<br>
					<?php echo nl2br($result['Client']['cancel_policy']);?>
<?php
		} // /事前決済済みかどうかで表示を切り替える
?>

			</dd>
		</dl>
	</div>

<?php
		if(!_isLogin()){
?>
	<div class="ac inner mb20px">
		<?= $this->Html->link('ログアウト', '/mypages/logout/', array('class' => 'btn btn2 bg_gray_2')); ?>
	</div>
<?php
		}
?>
</div>
<!-- js-content End -->
<?php
	} else { // キャンセル済みの場合
?>
<div id="js-content" class="sp_mypages-index_page">
	<h2 class="title_blue_line"><span>予約の照会・変更・取消</span></h2>
	<section class="plan_form">
		<div class="inner">
			<p class="b20px">
				ご予約はキャンセルされております。
			</p>
		</div>
		<h3 class="plan_form_title">予約状況の確認</h3>
		<section>
			<div class="reservation_number inner mb10px">
				<h4>お客様の予約番号</h4>
				<div class="number mb10px">
					<p class=""><?php echo $result['Reservation']['reservation_key']; ?></p>
				</div>
				<ul>
				</ul>
			</div>

			<div class="reservation_info_outline">
				<div class="inner">
					<ul class="plan_info_return_date">
						<li>
							<span>受取日</span>
<?php
		$w = $week[date('w', strtotime($result['Reservation']['rent_datetime']))];
		echo date('Y年m月d日 ('.$w.') H:i', strtotime($result['Reservation']['rent_datetime']));
?>
						</li>
						<li><span>返却日</span>
<?php
		$w = $week[date('w', strtotime($result['Reservation']['return_datetime']))];
		echo date('Y年m月d日 ('.$w.') H:i', strtotime($result['Reservation']['return_datetime']));
?>
						</li>
					</ul>
				</div>
				<div class="plan_info_body_price price_block">
					<div class="plan_info_left">
						<p class="price_block_title">合計料金<span>（税込）</span></p>
						<p class="caption_gray"><?php echo $rentalPeriod; ?>料金</p>
					</div>
					<div class="plan_info_right">
						<p class="price_block_price">&yen;<?= number_format($result['Reservation']['amount']); ?></p>
					</div>
				</div>
			</div>
		</section>
		<div class="mb20px">
			<dl class="accordion">
				<dt class="trigger">キャンセルポリシー<span class="open-close">open</span></dt>
				<dd class="acordion_tree">
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
		} // /事前決済済みかどうかで表示を切り替える
?>
				</dd>
			</dl>
<?php
		// お問い合わせ履歴
		if (!empty($reservationMail)) {
?>
			<dl class="accordion">
				<dt class="trigger">お問い合わせ履歴<span class="open-close">open</span></dt>
				<dd class="acordion_tree">
<?php
			foreach($reservationMail as $key => $value){
?>
					<p class="h3_wrap st-table bg_gray_2 p_5px mb10px">
						<?= !empty($value['Staff']['name']) ? $result['Client']['name'] : 'お客様'; ?>
						<span><?php echo date('Y-m-d H時i分', strtotime($value['ReservationMail']['mail_datetime'])); ?></span>
					</p>
					<p class="rent-margin-bottom mb10px" style="word-wrap: break-word;"><?php echo nl2br(h($value['ReservationMail']['contents'])); ?></p>
<?php
			}
?>
				</dd>
			</dl>
<?php
		}
?>
		</div>
	</section>
</div>
<?php
	}
?>
