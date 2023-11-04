<div class="clients form">
	<?php
	echo $this->Form->create('Client', array('inputDefaults' => array('label' => false), 'enctype' => 'multipart/form-data'));
	?>

	<h3>クライアント編集</h3>
	<table class="table table-bordered">
		<tr>
			<th class="span3">クライアント名</th>
			<td>
				<?php
				echo $this->Form->hidden('id');
				echo $this->Form->input('name');
				?>
			</td>
		</tr>
		<tr>
			<th>地域タイプ</th>
			<td><?php echo $this->Form->input('area_type', array('options' => $areaType)); ?></td>
		</tr>
		<tr>
			<th>リンク用URL</th>
			<td><?php echo $this->Form->input('url', array('pattern' => Constant::PATTERN_IDPASS)); ?></td>
		</tr>
		<tr>
			<th>成約のタイミング</th>
			<td>
				<?php
				echo $conclusionContractCriteria[empty($this->request->data['Client']['conclusion_contract_criteria']) ? 0 : 1];
				?>
			</td>
		</tr>
		<tr>
			<th>クライアントロゴ(スマホサイト用)</th>
			<td>
				<?php
				if (!empty($this->request->data['Client']['sp_logo_image'])) {
					echo $this->Html->image('../../img/logo/square/' . $this->request->data['Client']['id'] . '/' . $this->request->data['Client']['sp_logo_image']) . '<br />';
				}
				echo $this->Form->File('sp_logo_image_tmp');
				?>
				<br />
				<code>縦120px × 横120px</code>
			</td>
		</tr>
		<tr>
			<th>乗捨料金パターン数</th>
			<td><?php echo $this->Form->input('required_drop_off_price_pattern', array('min' => 1, 'max' => 3, 'default' => 1)); ?></td>
		</tr>

		<tr>
			<th>予約タグ<br />(二ケタの半角大文字英字)</th>
			<td>
				<?php
				echo $this->Form->input('reserve_tag', array(
					'onkeyup' => 'this.value=this.value.toUpperCase()',
					'style' => 'ime-mode:disabled',
					'minlength' => 2
						)
				);
				?>
				<code>※お客様が数字と誤認しやすい英字の</code><br />
				<code>「O（オー）」「D（大文字ディー）」「I（大文字アイ）」「ｌ（小文字エル）」</code><br />
				<code>のご利用はお控えください。</code>
			</td>
		</tr>
		<tr>
			<th>問い合わせフォーム表示</th>
			<td><?php echo $this->Form->input('inquiry_display', array('type' => 'checkbox')); ?></td>
		</tr>
		<tr>
			<th>事前決済許可</th>
			<td><?php echo $this->Form->input('accept_prepay', array('type' => 'checkbox')); ?></td>
		</tr>
		<tr id="cancel-deadline">
			<th>キャンセル手仕舞い</th>
			<td colspan="3">
				<input type="radio" id="HoursOrDays0" name="radioHoursOrDays" value="0" style="margin-top: -3px"<?php echo (!$hoursOrDays ? ' checked="checked"' : '');?>> 出発時間の<?php echo $this->Form->input('cancel_deadline_hours',array('label'=>false,'div'=>false));?> 時間前まで
				<br>
				<input type="radio" id="HoursOrDays1" name="radioHoursOrDays" value="1" style="margin-top: -3px"<?php echo ($hoursOrDays ? ' checked="checked"' : '');?>> 出発日の <?php echo $this->Form->input('cancel_deadline_days',array('min' => 0, 'label'=>false,'div'=>false));?> 日前
				<?php echo $this->Form->hour('cancel_deadline_time', true, $deadlineTimeOptions); ?>時
				<?php echo $this->Form->minute('cancel_deadline_time', $deadlineTimeOptions); ?> 分まで
			</td>
		</tr>
		<tr>
			<th>検索公開/非公開</th>
			<td>
				<?php echo $this->Form->input('is_searchable', array('options' => $searchable)); ?>
				<code>※非公開にすると検索結果に出なくなります。</code>
			</td>
		</tr>
		<tr>
			<th>公開/非公開</th>
			<td>
				<?php echo $this->Form->input('delete_flg', array('options' => $deleteFlgOptions)); ?>
				<code>※非公開にするとリンク用URLは未登録になります。</code>
			</td>
		</tr>
		<tr>
			<th>包括販売商品</th>
			<td>
				<?php echo $this->Form->input('is_managed_package', array('options' => $managedPackage)); ?>
			</td>
		</tr>
	</table>
	<div class="right">
		<?php
		echo $this->Form->submit('修正', array('class' => 'btn btn-success'));
		echo $this->Form->end();
		?>
	</div>
</div>

<script>
	jQuery( function() {
		function hoursOrDays() {
			if ($('#HoursOrDays0').prop('checked')) {
				$('#ClientCancelDeadlineDays').val('');
				$('#ClientCancelDeadlineTimeHour').val('');
				$('#ClientCancelDeadlineTimeMin').val('');
				$('#ClientCancelDeadlineHours').prop('disabled', false);
				$('#ClientCancelDeadlineDays').prop('disabled', true);
				$('#ClientCancelDeadlineTimeHour').prop('disabled', true);
				$('#ClientCancelDeadlineTimeMin').prop('disabled', true);
			} else {
				$('#ClientCancelDeadlineHours').val('');
				$('#ClientCancelDeadlineDays').prop('disabled', false);
				$('#ClientCancelDeadlineTimeHour').prop('disabled', false);
				$('#ClientCancelDeadlineTimeMin').prop('disabled', false);
				$('#ClientCancelDeadlineHours').prop('disabled', true);
			}
		}

		$('input[name="radioHoursOrDays' ).change( function() {
			hoursOrDays();
		});

		hoursOrDays();
	} );
</script>
