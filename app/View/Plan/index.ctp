<?php
	echo $this->Html->script(['plan', '/js/modal_float'], ['inline' => false, 'defer' => true]);
?>

<?php echo $this->element('pc_modal-plan'); ?>

<div class="wrap contents clearfix">
<?php
	echo $this->element('progress_bar');
?>
<?php
	if (!empty($sessionMessage)) {
		echo $this->element('session_message');
	}
	echo $this->element('plan_view');
?>

<?php
	echo $this->Form->create('Reservation', array(
		'type' => 'post',
		'id' => 'ReservationPlanForm',
		'url' => '/reservations/step1/',
		'class' => 'st-table',
		'inputDefaults' => array(
			'div' => false,
			'label' => false,
			'legend' => false,
		),
	));
?>
	<h3 class="heading -big">お見積り</h3>
	<h4 class="heading -x-large">貸出期間</h4>
	<table class="contents_detail_tbl rent-margin-bottom">
		<tr>
			<th>
				<span class="va-middle">貸出期間</span>
				<span class="label-require va-middle">必須</span>
			</th>
			<td>
				<div><?php echo date('Y年m月d日 H時i分', strtotime($requestData['from'])); ?> ～ <?php echo date('Y年m月d日 H時i分', strtotime($requestData['to'])); ?></div>
				<?php echo ($expiredFlg) ? '<div style="color:red;">貸出期間が過去の日付です。</div>' : ''; ?>
			</td>
		</tr>
	</table>

	<h4 class="heading -x-large">車両の受取・返却</h4>
	<table class="contents_detail_tbl rent-margin-bottom table-form">
		<tr>
			<th>
				<span class="va-middle">受取営業所</span>
				<span class="label-require va-middle">必須</span>
			</th>
			<td id="fromOfficeBox">
				<div id="officeStock" class="decide-width -store">
					<div class="select-form">
						<div class="field-wrap">
<?php
	echo $this->Form->input('from_office', array(
		'type' => 'select',
		'options' => !empty($fromOfficeOptionList) ? $fromOfficeOptionList : $fromOfficeList,
		'default' => $this->request->data['from_office'],
		'class' => 'field -full'
	));
?>
							<i class="icm-right-arrow"></i>
						</div>
					</div>
				</div>

				<div>
<?php
	foreach ($officeDatas as $key => $officeData) {
?>
					<div id="fromOfficeId<?php echo $officeData['id']; ?>" class="from-office office-data">
						<p>
							<span class="label-item label-item_gray">営業時間</span><span><?php echo date('H:i', strtotime($officeData['office_hours_from'])); ?>～<?php echo date('H:i', strtotime($officeData['office_hours_to'])); ?></span>
						</p>
<?php 
		if(!empty($officeData['start_day']) && !empty($officeData['end_day'])){
?>
						<p class="note_irregular-business-hours">
							<i class="icm-info-button-fill" aria-hidden="true"></i>
							<?php echo date('Y/m/d', strtotime($officeData['start_day'])); ?>～<?php echo date('Y/m/d', strtotime($officeData['end_day'])); ?> は営業時間が通常と異なります。詳細は店舗へお問い合わせください。
						</p>
<?php
		}
?>
						<p>
							<span class="label-item label-item_gray">アクセス</span><span><?php echo $officeData['access_dynamic']; ?></span>
						</p>
					</div>
<?php
	}
?>
				</div>
			</td>
		</tr>
	</table>

	<table class="contents_detail_tbl rent-margin-bottom table-form">
		<tr>
			<th>
				<span class="va-middle">返却営業所</span>
				<span class="label-require va-middle">必須</span>
			</th>
			<td id="returnOfficeBox">
				<div class="decide-width -store">
					<div class="select-form">
						<div class="field-wrap">
<?php
	echo $this->Form->input('return_office', array(
		'type' => 'select',
		'options' => $returnOfficeList,
		'default' => $this->request->data['return_office'],
		'class' => 'field -full'
	));
?>
							<i class="icm-right-arrow"></i>
						</div>
					</div>
				</div>

				<div>
<?php
	foreach ($returnOfficeDatas as $key => $returnOfficeData) {
?>
					<div id="returnOfficeId<?php echo $returnOfficeData['id']; ?>" class="return-office office-data">
						<p>
							<span class="label-item label-item_gray">営業時間</span><span><?php echo date('H:i', strtotime($returnOfficeData['office_hours_from'])); ?>～<?php echo date('H:i', strtotime($returnOfficeData['office_hours_to'])); ?></span>
						</p>
<?php 
		if(!empty($returnOfficeData['start_day']) && !empty($returnOfficeData['end_day'])){
?>
						<p class="note_irregular-business-hours">
							<i class="icm-info-button-fill" aria-hidden="true"></i>
							<?php echo date('Y/m/d', strtotime($returnOfficeData['start_day'])); ?>～<?php echo date('Y/m/d', strtotime($returnOfficeData['end_day'])); ?> は営業時間が通常と異なります。詳細は店舗へお問い合わせください。
						</p>
<?php
		}
?>
						<p>
							<span class="label-item label-item_gray">アクセス</span><span><?php echo $returnOfficeData['access_dynamic']; ?></span>
						</p>
					</div>
<?php
	}
?>
				</div>
			</td>
		</tr>
	</table>

	<h4 class="heading -x-large">ご利用人数</h4>
	<table class="contents_detail_tbl rent-margin-bottom table-form">
		<tr>
			<th>
				<span class="va-middle">利用人数</span>
				<span class="label-require va-middle">必須</span>
			</th>
			<td>
				<div class="decide-width -people">
					<div class="list">
						<div class="label">大人（12歳以上）</div>
						<fieldset class="select-form">
							<div class="field-wrap">
<?php
	echo $this->Form->input('adults', array(
		'type' => 'select',
		'options' => $adultPassengers,
		'default' => $requestData['adults'],
		'class' => 'field'
	));
?>
								<i class="icm-right-arrow"></i>
							</div>
						</fieldset>
						<span class="unit">名</span>
					</div>
					<div class="list">
						<div class="label">子供（6〜11歳）</div>
						<fieldset class="select-form">
							<div class="field-wrap">
<?php
	echo $this->Form->input('children', array(
		'type' => 'select',
		'options' => $passengers,
		'default' => $requestData['children'],
		'class' => 'field'
	));
?>
								<i class="icm-right-arrow"></i>
							</div>
						</fieldset>
						<span class="unit">名</span>
					</div>

					<div class="list">
						<div class="label">幼児（6歳未満）</div>
						<fieldset class="select-form">
							<div class="field-wrap">
<?php
	echo $this->Form->input('infants', array(
		'type' => 'select',
		'options' => $passengers,
		'default' => $requestData['infants'],
		'class' => 'field'
	));
?>
								<i class="icm-right-arrow"></i>
							</div>
						</fieldset>
						<span class="unit">名</span>
					</div>

				</div>
				<div class="caution_blue">
					<span class="icm-warning"></span>
					<p>利用人数は選択頂いた車両クラスの乗車定員をご確認の上、ご登録ください。</p>
				</div>
			</td>
		</tr>
	</table>

	<h4 class="heading -x-large">オプション</h4>
	<div class="contents_detail_tbl rent-margin-bottom table-form">
		<div class="table-wrap">
			<div class="table-left">
				<span>チャイルドシート</span>
			</div>
			<div id="sheet-option" class="table-right">
				<div>
<?php
	$privilege_option_flg_zero_cnt = 0; // 下のリクエスト表示・非表示のためのカウント用
	foreach ($commodityPrivilegeData as $key => $commodityPrivilege) {
		if ($commodityPrivilege['Privilege']['option_flg'] == 1) {
?>
					<div class="table-cell">
						<div class="text_bold"><?php echo $commodityPrivilege['Privilege']['name']; ?></div>
						<div class="text_right">
							<span class="font-size-large" id="option-price<?php echo $commodityPrivilege['Privilege']['id']; ?>">0円</span>
						</div>
						<div class="select-wrap">
							<div class="select-form">
								<div class="field-wrap">
<?php
			echo $this->Form->input('sheet.'.$commodityPrivilege['Privilege']['id'], array(
				'type' => 'select',
				'options' => $sheetOptions[$commodityPrivilege['Privilege']['id']],
				'empty' => '---',
				'data-id' => $commodityPrivilege['Privilege']['id'],
				'data-price' => $commodityPrivilege[0]['Sum'],
				'max' => $commodityPrivilege['Privilege']['maximum'],
				'class' => 'field'
			));
?>
									<i class="icm-right-arrow"></i>
								</div>
							</div>
							<?php echo '<span class="unit">'.$commodityPrivilege['Privilege']['unit_name'].'</span>'; ?>
						</div>
					</div>
<?php
		} elseif ($commodityPrivilege['Privilege']['option_flg'] == 0) {
			$privilege_option_flg_zero_cnt++;
		}
	}
?>
				</div>
				<div class="caution_blue">
					<span class="icm-warning"></span>
					<p>6歳未満の幼児を同乗させる場合、チャイルドシートの使用が法令により義務付けられています。</p>
				</div>
			</div>
		</div>
<?php
	if(!empty($privilege_option_flg_zero_cnt)) {
?>
		<div class="table-wrap">
			<div class="table-left">
				<span>リクエスト</span>
			</div>
			<div id="privilege-option" class="table-right">
				<div>
<?php
		foreach ($commodityPrivilegeData as $key => $commodityPrivilege) {
			if ($commodityPrivilege['Privilege']['option_flg'] == 0) {
?>
					<div class="table-cell">
						<div class="text_bold"><?php echo $commodityPrivilege['Privilege']['name']; ?></div>
						<div class="text_right">
							<span class="font-size-large" id="option-price<?php echo $commodityPrivilege['Privilege']['id']; ?>">0円</span>
						</div>
						<div class="select-wrap">
							<div class="select-form">
								<div class="field-wrap">
<?php
				echo $this->Form->input('privilege.'.$commodityPrivilege['Privilege']['id'], array(
					'type' => 'select',
					'options' => $privilegeOptions[$commodityPrivilege['Privilege']['id']],
					'empty' => '---',
					'data-id' => $commodityPrivilege['Privilege']['id'],
					'data-price' => $commodityPrivilege[0]['Sum'],
					'max' => $commodityPrivilege['Privilege']['maximum'],
					'class' => 'field'
				));
?>
									<i class="icm-right-arrow"></i>
								</div>
							</div>
<?php
				if ($commodityPrivilege['Privilege']['maximum'] > 1) {
					echo '<span class="unit">'.$commodityPrivilege['Privilege']['unit_name'].'</span>';
				}
?>
						</div>
					</div>
<?php
				if ($commodityPrivilege['Privilege']['option_category_id'] == 13) {
					echo '<div>※事故発生時にお客様負担額となる車両休業補償（ノンオペレーションチャージ）の支払いを免除する制度</div>';
				}
?>

<?php
			}
		}
?>
				</div>
			</div>
		</div>
<?php
	}
?>
	</div>

	<h4 class="heading -x-large">料金の確認</h4>
	<table class="contents_detail_tbl rent-margin-bottom">
		<tr>
			<th>基本料金</th>
			<td class="clearfix">
				<span class="text_bold rent-margin-right" style="float:right;">&yen; <?php echo number_format($basicCharge) ?></span>
			</td>
		</tr>
		<tr>
			<th>オプション料金</th>
			<td class="clearfix">
				<div id="other-price">
					<div id="other-none"></div>
					<div id="drop">
						<span>乗り捨て料金</span>
						<span class="text_bold rent-margin-right price" style="float:right;">&yen; 0</span>
					</div>
					<div id="nightfee">
						<span>深夜手数料</span>
						<span class="text_bold rent-margin-right price" style="float:right;">&yen; 0</span>
					</div>
<?php
	foreach ($commodityPrivilegeData as $key => $commodityPrivilege) {
?>
					<div id="privilege<?php echo $commodityPrivilege['Privilege']['id']; ?>" class="clearfix">
						<span><?php echo $commodityPrivilege['Privilege']['name']; ?></span>
<?php
		if ($commodityPrivilege['Privilege']['maximum'] > 1) {
?>
						<span class="count"><span class="num"></span></span><?php echo $commodityPrivilege['Privilege']['unit_name']; ?>
<?php
		}
?>
						<span class="text_bold rent-margin-right price" style="float:right;">&yen; 0</span>
					</div>
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
					<span id="total-place" class="contents_result_detail_amount_price"></span>
				</div>
			</td>
		</tr>
		<tr>
			<th>お支払い方法</th>
			<td>
<?php
	switch($commodityInfo['Commodity']['payment_method']){
		case 0:
?>
				・現地決済のみ
				<?php echo $this->element('pc_note_payment_onsite', [
						'acceptCash' => $commodityInfo['Client']['accept_cash'], 
						'acceptCard' => $commodityInfo['Client']['accept_card']
					]); // 現地決済の注意事項 ?>
<?php
			echo $this->Form->hidden('payment_method', array('value' => 0));
			break;

		case 1:
?>
				・クレジットカードで事前決済<?php echo $econMaintenance ? '（メンテナンス中）' : ''; ?>

				<?php echo $this->element('modal_about-card'); // デビット・プリペイドカードをご使用時の注意事項 ?>

<?php
			echo "";
			echo $this->Form->hidden('payment_method', array('value' => 1));
			break;

		case 2:
			$defaultMethod = $econMaintenance ? 0 : $defaultPaymentMethod;
			$commonOptions = array('id'=>'paymentMethod','hiddenField'=>false, 'label'=>false, 'default' => $defaultMethod, 'class' => 'js-radio-payment');
			$creditOptions = $econMaintenance ? array_merge($commonOptions, array('disabled')) : $commonOptions;
?>
				<div>
					<div class="radio-form -btn">
						<?= $this->Form->radio('payment_method', array('0' => ''), $commonOptions); ?>
						<label for="paymentMethod0" class="label">現地で決済</label>
					</div>
					<div class="radio-form -btn">
						<?= $this->Form->radio('payment_method', array('1' => ''), $creditOptions); ?>
						<label for="paymentMethod1" class="label">クレジットカードで事前決済<?php echo $econMaintenance ? '（メンテナンス中）' : ''; ?></label>
					</div>
				</div>

				<div id="js_select_onsite" style="display: none;">
					<?php echo $this->element('pc_note_payment_onsite', [
						'acceptCash' => $commodityInfo['Client']['accept_cash'], 
						'acceptCard' => $commodityInfo['Client']['accept_card']
					]); // 現地決済の注意事項 ?>
				</div>
				<div id="js_select_credit">
					<?php echo $this->element('modal_about-card'); // デビット・プリペイドカードをご使用時の注意事項 ?>
				</div>

<?php
			break;

		default:
			break;
	}
?>

			</td>
		</tr>
	</table>

	<div class="result-btn-wrap">
<?php
	echo $this->Form->hidden('uniqId', array('value' => $sessionUniqId));
	echo $this->Form->hidden('basicPrice', array('value' => $basicCharge));
	echo $this->Form->hidden('from', array('value' => $requestData['from']));
	echo $this->Form->hidden('to', array('value' => $requestData['to']));
	echo $this->Form->hidden('carClassId', array('value' => $commodityInfo['CarClass']['id']));
	echo $this->Form->hidden('commodityItemId', array('value' => $commodityInfo['CommodityItem']['id']));
	echo $this->Form->hidden('commodityId', array('value' => $commodityInfo['Commodity']['id']));
	echo $this->Form->hidden('clientId', array('value' => $commodityInfo['Client']['id']));
	echo $this->Form->hidden('estimationTotalPrice', array('value' => '0'));
	echo $this->Form->hidden('dayTimeFlg', array('value' => $commodityInfo['Commodity']['day_time_flg']));
	echo $this->Form->hidden('submitFlg', array('value' => 1));
	if ($fromRentacarClient) {
		echo $this->Form->hidden('from_rentacar_client', array('value' => 'true'));
	}
	if (!$expiredFlg && !($econMaintenance && $commodityInfo['Commodity']['payment_method'] == 1)){
		echo $this->Form->submit('次へ（お客様情報入力）', array('type' => 'button', 'id' => 'btn_submit', 'class' => 'btn-type-primary right-btn'));
	}
	if (!empty($backSearch)) {
		echo $this->Html->link('レンタカーの検索へ戻る', '/searches' . $backSearch, array('class' => 'btn-type-cancel left-btn'));
	}
	echo $this->Form->end();
?>
	</div>

</div><!-- /wrap -->

<script>

// 使ってなさそう？
// var viewModalWindow = function(name){
// 	if($("#modal-overlay-"+name)[0]) return false;
// 	$("body").append('<div id="modal-overlay-'+name+'" class="modal-overlay"></div>');

// 	$("#modal-overlay-"+name).fadeIn("fast");

// 	centeringModalSyncer('#modal-content-'+name);

// 	$("#modal-content-"+name).fadeIn("fast");

// 	$("#modal-overlay-"+name+",#modal-close-"+name).unbind().click(function(){
// 		$("#modal-content-"+name+",#modal-overlay-"+name).fadeOut("fast",function(){
// 			$("#modal-overlay-"+name).remove();
// 		});
// 	});
// 		// センタリングをする関数
// 	function centeringModalSyncer(id){
// 		var w = $(window).width();
// 		var h = $(window).height();
// 		var cw = $(id).outerWidth();
// 		var ch = $(id).outerHeight();
// 		var pxleft = ((w - cw)/2);
// 		var pxtop = ((h - ch)/2);

// 		$(id).css({"left": pxleft + "px"});
// 		// $(id).css({"top": pxtop + "px"});
// 	}
// }

$(function(){

	$("#btn_submit").on("click", function(){
		// gaイベント
		ga('send', 'event', 'pc_plan', 'click', '次へボタン', {
			hitCallback: createFunctionWithTimeout(function(){
				$("#ReservationPlanForm").submit();
			})
		});
	});

	$("#btn_submit_bottom").on("click", function(){
		ga('send', 'event', 'pc_plan', 'click', 'フローティングボタン', {
			hitCallback: createFunctionWithTimeout(function(){
				$("#ReservationPlanForm").submit();
			})
		});
	});

	$(".js-radio-payment").on("change", function(){
		if( $("#paymentMethod0").prop("checked") ){
			$("#js_select_onsite").show();
			$("#js_select_credit").hide();
		}else{
			$("#js_select_onsite").hide();
			$("#js_select_credit").show();
		}
	});

	// 合計金額を取得
	const target = document.getElementById('total-place');
	const obs_option = { childList: true };
	const observer = new MutationObserver( function(mutation_record){
		var total_price = $("#total-place").text();
		$("#js_btm_total_place").html(total_price);
	});
	observer.observe( target, obs_option );

	$(".js-radio-payment").trigger("change");
});

</script>
