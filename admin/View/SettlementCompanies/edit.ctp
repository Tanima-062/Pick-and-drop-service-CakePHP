<div class="row-fluid span8">
	<h3>精算管理会社編集</h3>
	<?php echo $this->Form->create('SettlementCompany', array('class' => 'form-horizontal', 'inputDefaults' => array('label' => false, 'div' => false)));?>
	<?php $referer = ($this->request->data['Custom']['referer'] ? $this->request->data['Custom']['referer'] : $this->request->referer()); ?>
	<?php echo $this->Form->hidden('Custom.referer', array('value' => $referer)); ?>
	<table class="table table-bordered form-inline">
		<tr>
			<th class="alert-success">経理用管理コード</th>
			<td>
			<?php
				echo $this->Form->hidden('id');
				echo $this->Form->input('accounting_code', array( 'required' => 'required', 'div' => false));
			?>
			</td>
		</tr>
		<tr>
			<th class="alert-success">インボイス登録番号</th>
			<td>
			<?php
				echo $this->Form->input('invoice_number', array( 'maxlength' => '14', 'div' => false));
			?>
				<code style="margin-left:10px;">※</code>
			</td>
		</tr>
		<tr>
			<th class="alert-success">精算管理会社名</th>
			<td>
			<?php
				echo $this->Form->input('name', array( 'required' => 'required', 'div' => false));
			?>
			</td>
		</tr>
		<tr>
			<th class="alert-success">クライアント名</th>
			<td><?php echo $this->Form->input('client_id', [
					'options' => $clientList,
					'required' => true,
					'div' => false,
					'class' => 'client-id'
				]);
				?>
			</td>
		</tr>
<?php
if (count($settlement_company_staff) > 0) {
	foreach($settlement_company_staff as $sk => $staffId) {
		if ($sk == 0) {
			$required = true;
		} else {
			$required = false;
		}
		if (count($settlement_company_staff) == ($sk +1)) {
			$staff_btn = "<span class='add_staff_button'>+</span>";
		} else {
			$staff_btn = "";
		}
?>
		<tr>
			<th class="alert-success" style="position:relative;">担当者名<?php echo $staff_btn; ?></th>
			<td>
				<?php echo $this->Form->input('settlement_staff_id',
					[
						'name' => 'settlement_staff_id[]',
						'options' => $staffList,
						'value' => '',
						'required' => $required,
						'div' => false,
						'class' => 'user-name'
					]);
				?>
			</td>
		</tr>
<?php
	}
} else {
?>
		<tr>
			<th class="alert-success" style="position:relative;">担当者名<span class='add_staff_button'>+</span></th>
			<td>
				<?php echo $this->Form->input('settlement_staff_id',
					[
						'name' => 'settlement_staff_id[]',
						'options' => $staffList,
						'value' => '',
						'required' => true,
						'div' => false,
						'class' => 'user-name'
					]);
				?>
			</td>
		</tr>
<?php
}
?>
		<tr>
			<th class="alert-success">決済手数料率(%)</th>
			<td>
			<?php
				echo $this->Form->input('fee_rate', array('div' => false));
			?>
			</td>
		</tr>
		<tr>
			<th>手数料に係る消費税計算</th>
			<td><?php echo $this->Form->input('is_internal_tax', [
					'type' => 'radio',
					'options' => Constant::isInternalTax(),
					'value' => $this->data['SettlementCompany']['is_internal_tax'],
					'required' => true,
					'div' => false
				]);
				?>
				<code style="margin-left:10px;">※</code>
			</td>
		</tr>
		<tr>
			<th class="alert-success">銀行名</th>
			<td>
			<?php
				echo $this->Form->input('bank_name', array('div' => false));
			?>
				<code style="margin-left:10px;">※</code>
			</td>
		</tr>
		<tr>
			<th class="alert-success">支店名</th>
			<td>
			<?php
				echo $this->Form->input('bank_branch_name', array('div' => false));
			?>
				<code style="margin-left:10px;">※</code>
			</td>
		</tr>
		<tr>
			<th>種別</th>
			<td><?php echo $this->Form->input('account_type', [
					'type' => 'radio',
					'options' => Constant::accountType(),
					'value' => $this->data['SettlementCompany']['account_type'],
					'required' => true,
					'div' => false
				]);
				?>
				<code style="margin-left:10px;">※</code>
			</td>
		</tr>
		<tr>
			<th class="alert-success">口座番号</th>
			<td>
			<?php
				echo $this->Form->input('account_number', array('div' => false));
			?>
				<code style="margin-left:10px;">※</code>
			</td>
		</tr>
		<tr>
			<th class="alert-success">口座名義カナ</th>
			<td>
			<?php
				echo $this->Form->input('account_holder', array('div' => false));
			?>
				<code style="margin-left:10px;">※</code>
			</td>
		</tr>
		<tr>
			<th class="alert-success">精算書再発行期限なし</th>
			<td>
				<?php echo $this->Form->input('recreate_limit_flg', array('type' => 'checkbox', 'div' => false)); ?>
				<code style="margin-left:10px;">※</code>
			</td>
		</tr>
		<?php
			for ($i = 1; $i <= 10; $i++){
				// 最低3個は表示 記述されているものも表示
				if ($i <= 3 || $i <= $max_billing_email) {
					$mail_display = '';
				} else {
					$mail_display = 'display:none;';
				}
				// 3~9の間で最後に表示されている枠以降に追加ボタンを表示
				if ($i < 3 || $i == 10 || $i < $max_billing_email) {
					$mail_add_display = 'display:none;';
				} else {
					$mail_add_display = '';
				}
		?>
		<tr style='<?php echo $mail_display; ?>'>
			<th class="alert-success" style="position:relative;">請求先メールアドレス<?php echo $i; ?><span style="<?php echo $mail_add_display; ?>" class='add_mail_button'>+</span></th>
			<td>
			<?php
				if ($i == 1) {
					// 1個目だけ必須
					$options = array('required' => 'required', 'div' => false);
				} else {
					$options = array('div' => false);
				}
				echo $this->Form->input('billing_email'.$i, $options);
			?>
			</td>
		</tr>
		<?php
			}
		?>
	</table>

	<span class="left">
		<?php echo $this->Form->submit('編集する', array('class' => 'btn btn-success right', 'div' => false));?>
	</span>
	<code style="margin-left:20px;">※同じ経理用管理コードに対して保存内容が反映されます。</code>
	<?php echo $this->Form->hidden('payment_cycle', array('value' => $this->data['SettlementCompany']['payment_cycle']));?>
	<?php echo $this->Form->hidden('amount_include_tax', array('value' => $this->data['SettlementCompany']['amount_include_tax']));?>
	<?php echo $this->Form->end();?>

</div>
<script>
$(function() {
	$('.add_mail_button').click(function(){
		$(this).hide();
		$(this).parents('tr').next('tr').show();
	})

	$(document).on('click', '.add_staff_button', function(){
		var staffDOM = $(this).parents('tr');
		staffDOM.after(staffDOM.prop('outerHTML'));
		// 2個目以降必須削除
		staffDOM.next('tr').find('select').removeAttr('required');
		$(this).remove();
	});

	var $userName = $('.user-name');
	// 全ユーザ一覧保持
	var userNameHtml = $userName.html();
	// 遷移時: 登録済のクライアントIDと一致しないものを削除
	$(document).ready(function() {
		var settlementCompanyStaffId = <?php echo json_encode($settlement_company_staff); ?>;
		var clientId = $('.client-id').val();
		var cnt = 0;
		$userName.each(function(){
			$(this).html(userNameHtml).find('option').each(function() {
				var dataClientId = $(this).data('client-id');
				if (clientId != dataClientId && dataClientId != '') {
					$(this).remove();
				}
			});
			$(this).val(settlementCompanyStaffId[cnt]);
			cnt++;
		});
	});

	// クライアント名とユーザ名のセレクトボックス連動処理
	$('.client-id').change(function() {
		var $userName = $('.user-name');
		var clientId = $(this).val();

		$userName.each(function(){
			$(this).html(userNameHtml).find('option').each(function() {
				var dataClientId = $(this).data('client-id');
				if (clientId != dataClientId  && dataClientId != '') {
					$(this).remove();
				}
			});
		});

		// 選択できるものがない場合にdisabledにする
		if ($(this).val() === '') {
			$userName.attr('disabled', 'disabled');
		} else {
			$userName.removeAttr('disabled');
		}
	});
<?php
if ($settlement_summary_cnt > 0) {
?>

	// 経理用管理コードが編集されたら更新していいか確認する
	$('#SettlementCompanyEditForm').submit(function(){
		var defaultAccountingCode = <?php echo $this->data['SettlementCompany']['accounting_code']; ?>;
		if (defaultAccountingCode != $('#SettlementCompanyAccountingCode').val()) {
			if (confirm('経理用管理コードの変更後は\n発行済みの精算書が閲覧できなくなりますがよろしいですか？')) {
				return true;
			} else {
				return false;
			}
		}
	});
<?php
}
?>
});
</script>
