<?php
	$paymentApiFlgToJs = $paymentApi ? true : false ;
?>
<script type="text/javascript">
var paymentApiFlg = "<?= $paymentApiFlgToJs ?>";

$(function(){
	$('form').on('submit', function(event){
		$('#submitReserve').attr('disabled', true).val('送信中・・・');
	});
});
</script>

<!-- wrap -->
<main class="wrap contents step2-contents clearfix">
	<?php echo $this->element('reservation_steps'); // 予約ステップ ?>

	<div id='sessionMessage'>
<?php
	if (!empty($sessionMessage)) {
		echo $this->element('session_message');
	}
?>
	</div>

<?php
	echo $this->element('plan_view');
?>
<?php
	echo $this->Form->create('Reservation', array(
		'type' => 'post',
		'url' => 'completion/',
		'class' => 'st-table reservation_step2_form',
		'inputDefaults' => array(
			'div' => false,
			'label' => false,
			'legend' => false,
		),
	));
?>
	<h3 class="heading -big">お客様情報の確認</h3>
	<table class="contents_confirm_tbl">
		<tr>
			<th>氏名（カナ）</th>
			<td>
				<span><?php echo $confirmation['last_name']; ?></span> <span><?php echo $confirmation['first_name']; ?></span>
			</td>
		</tr>
		<tr>
			<th>携帯番号</th>
			<td><?php echo $confirmation['tel']; ?></td>
		</tr>
		<tr>
			<th>メールアドレス</th>
			<td><?php echo $confirmation['email']; ?></td>
		</tr>
<?php
	if (!empty($confirmation['remark'])) {
?>
		<tr>
			<th>備考欄</th>
			<td><?php echo nl2br($confirmation['remark']); ?></td>
		</tr>
<?php
	}
?>
	</table>
	<div class="rent-margin-bottom">
		<?php echo $this->Html->link('お客様情報を変更', '/reservations/step1/'.$this->data['Reservation']['uniqId'].'/'.($fromRentacarClient ? '?from_rentacar_client=true' : ''), array('class' => 'btn-type-cancel')); ?>
	</div>

<?php
	if (!$paymentApi) { //決済APIがfalseのとき
?>
	<h3 class="heading -big">クレジットカード情報の確認</h3>
	<table class="contents_confirm_tbl">
		<tr>
			<th>カード番号</th>
			<td><span id="cardNumber"></span></td>
		</tr>
		<tr>
			<th>カード名義</th>
			<td><span id="cardOwner"></span></td>
		</tr>
		<tr>
			<th>セキュリティコード</th>
			<td>****</td>
		</tr>
		<tr>
			<th>カード有効期限</th>
			<td><span id="cardExpMonth"></span> / <span id="cardExpYear"></span></td>
		</tr>
	</table>
<?php
	}
?>
	<h3 class="heading -big">レンタカー予約内容の確認</h3>
	<h4 class="heading -x-large">予約詳細</h4>
	<table class="contents_confirm_tbl">
		<tr>
			<th>レンタカー事業者</th>
			<td>
				<?php 
					if (!empty($commodityInfo['Client']['sp_logo_image'])) {
						echo $this->Html->image('logo/square/'.$commodityInfo['Client']['id'].'/'.$commodityInfo['Client']['sp_logo_image'], array('alt' => $commodityInfo['Client']['name'],'width' => 64));
						echo '<br>';
					}
				?>
				<div class="contents_complete_tbl_shop">
					<span class="contents_complete_tbl_shopName"><?php echo $confirmation['clientName']; ?></span>
				</div>
			</td>
		</tr>
		<tr class="">
			<th>受取日時</th>
			<td class="text_danger"><?php echo $confirmation['from']; ?></td>
		</tr>
<?php
	if (!empty($confirmation['arrival'])) {
?>
		<tr>
			<th>到着便名</th>
			<td><?php echo $confirmation['arrival']; ?></td>
		</tr>
<?php
	}
?>
		<tr class="">
			<th>受取店舗</th>
			<td><span><?php echo $confirmation['rentOfficeName']; ?></span><span>（<?php echo trim($confirmation['rentOfficeTel']); ?>）</span></td>
		</tr>
<?php
	if (!empty($confirmation['rentOfficeMeetingInfo'])) {
?>
		<tr class="">
			<th>受取方法</th>
			<td><span><?php echo nl2br($confirmation['rentOfficeMeetingInfo']); ?></span></td>
		</tr>
<?php
	}
?>
		<tr class="">
			<th>返却日時</th>
			<td class="text_danger"><?php echo $confirmation['to']; ?></td>
		</tr>
<?php
	if (!empty($confirmation['departure'])) {
?>
		<tr>
			<th>出発便名</th>
			<td><?php echo $confirmation['departure']; ?></td>
		</tr>
<?php
	}
?>
		<tr class="">
			<th>返却店舗</th>
			<td><span><?php echo $confirmation['returnOfficeName']; ?></span><span>（<?php echo trim($confirmation['returnOfficeTel']); ?>）</span></td>
		</tr>
<?php
	if (!empty($confirmation['returnOfficeMeetingInfo'])) {
?>
		<tr class="">
			<th>返却方法</th>
			<td><span><?php echo nl2br($confirmation['returnOfficeMeetingInfo']); ?></span></td>
		</tr>
<?php
	}
?>
	</table>

	<h4 class="heading -x-large">お支払金額</h4>
	<table class="contents_confirm_tbl">
		<tr class="">
			<th>ご利用人数</th>
			<td>
				<span>大人（12歳以上）</span><span><?php echo $confirmation['adults']; ?>名</span>
<?php
	if (!empty($confirmation['children'])) {
?>
				/ <span>子供（6〜11歳）</span><span><?php echo $confirmation['children']; ?>名</span>
<?php
	}
	if (!empty($confirmation['infants'])) {
?>
				/ <span>幼児（5歳以下）</span><span><?php echo $confirmation['infants']; ?>名</span>
<?php
	}
?>
			</td>
		</tr>
		<tr class="clearfix">
			<th>基本料金</th>
			<td>
				<span class="text_bold" style="float:right;">&yen; <?php echo number_format($basicCharge) ?></span>
			</td>
		</tr>
		<tr class="clearfix">
			<th>オプション料金</th>
			<td>
<?php
	if (!empty($confirmation['dropOffLateNight']['dropPrice'])) {
?>
				<div>
					<span class="text_bold" >乗り捨て料金</span>
					<span class="text_bold" style="float:right;" >&yen; <?php echo number_format($confirmation['dropOffLateNight']['dropPrice']); ?></span>
				</div>
<?php
	}
	if (!empty($confirmation['dropOffLateNight']['nightFee'])) {
?>
				<div>
					<span class="text_bold" >深夜手数料</span>
					<span class="text_bold" style="float:right;" >&yen; <?php echo number_format($confirmation['dropOffLateNight']['nightFee']); ?></span>
				</div>
<?php
	}
	if (!empty($confirmation['privilegeOption'])) {
		foreach ($confirmation['privilegeOption'] as $key => $value) {
?>
				<div>
					<span class="text_bold" ><?php echo $value[0]; ?></span>
					<span class="text_bold" style="float:right;" >&yen; <?php echo $value[1]; ?></span>
				</div>
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
				<span class="contents_result_detail_amount_price">&yen; <?php echo number_format($confirmation['estimationTotalPrice']); ?></span>
				</div>
				<hr class="rent-margin">
				<div class="rent-padding">
<?php
	if ($confirmation['acceptCash'] == 1 && $confirmation['acceptCard'] == 1) {
		if (!empty($confirmation['Cards'])) {
			foreach ($confirmation['Cards']['url'] as $key => $card) {
				echo $this->Html->image($card, array('alt' => $confirmation['Cards']['name'][$key]));
			}
		}
		echo '<p>原則クレジットカードまたは現金</p>';
	} elseif ($confirmation['acceptCash'] == 1) {
		echo '<p>現金のみ</p>';
	} elseif ($confirmation['acceptCard'] == 1) {
		if (!empty($confirmation['Cards'])) {
			foreach ($confirmation['Cards']['url'] as $key => $card) {
				echo $this->Html->image($card, array('alt' => $confirmation['Cards']['name'][$key]));
			}
		}
		echo '<p>クレジットカードのみ</p>';
	}
?>
				</div>
			</td>
		</tr>
		<tr>
			<th>キャンセルポリシー</th>
			<td>
				ご予約をキャンセルされる場合、下記のキャンセル料を申し受けます。<br>
				<br>
				〈キャンセル料〉<br>
				<?php echo $confirmation['cancelPolicy']; ?><br>
				・予約時間を１時間以上過ぎてもご連絡のない場合は、キャンセルとして処理させていただきます。<br>
				・無連絡キャンセルの場合、ご返金はいたしかねますのでご了承ください。<br>
				<br>
				■キャンセルポリシーに関するお知らせ<br>
				<?php echo nl2br(h($confirmation['clientCancelPolicy'])); ?>

			</td>
		</tr>
	</table>

	<section class="panel panel_green text_center rent-margin-bottom">
		予約完了後に表示される予約番号のメモをお願いいたします。予約の確認や取消に必要な情報ですので、当日まで大切にお控えください。
	</section>

	<?php echo $this->element('reservation_note'); ?>

	<section class="result-btn-wrap">
<?php
	echo $this->Html->link('お客様情報の入力へ戻る', '/reservations/step1/'.$this->data['Reservation']['uniqId'].'/'.($fromRentacarClient ? '?from_rentacar_client=true' : ''), array('class' => 'btn-type-cancel btn-left'));
	echo $this->Form->hidden('uniqId', array('value' => $this->data['Reservation']['uniqId']));
	echo $this->Form->hidden('isStep1', array('value' => false));
	if ($paymentApi) { //決済APIがtrueのとき
		echo $this->Form->button('お支払いへ', array('class' => 'btn-type-primary right-btn', 'div' => false, 'id' => 'submitReserve', 'type' => 'button'));
		echo $this->Form->hidden('redirect_url_econ' ,array('value' => $paymentRedirectUrl));
	} else {
		echo $this->Form->button('上記の内容で予約する', array('class' => 'btn-type-primary right-btn', 'div' => false, 'id' => 'submitReserve', 'type' => 'button'));
	} 
?>
	</section>

	<section class="panel panel_note rent-margin-bottom">
		<h5 class="panel_note_title">注意事項</h5>
		<?php echo nl2br(h($confirmation['precautions'])); ?>
	</section>
<?php
	echo $this->Form->end();
	if (!$paymentApi) {
		echo $this->Form->hidden('session_token', array('value' => $econ_token));
	}
?>
</main>
<!-- wrap -->

<?php echo $this->element('loading_indicator_earth'); ?>
<?php
	if (!$paymentApi) {
		echo $this->Html->script($econ_jsf_url);
	} else {
		echo $this->Html->script('jquery-1.9.1.js');
	}
	echo $this->Html->script('input_confirm_econ.js');
?>