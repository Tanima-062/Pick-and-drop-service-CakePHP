<style>
input[type="text"],
input[type="number"]{
	width: 35%;
	-moz-box-sizing: border-box;
	-webkit-box-sizing: border-box;
	box-sizing: border-box;
	margin: 0;
	height: 125%;
}
table th {
	width: 20%;
	background: #dff0d8;
}
#PrivilegePeriodFlg {
	display: none;
	margin-left: 40px;
}
</style>
<div class="privileges form">
<?php echo $this->Form->create('Privilege',array('enctype' => 'multipart/form-data', 'inputDefaults' => array('label' => false, 'div' => false, 'legend' => false))); ?>
		<h3><?php echo __($optionName.'新規登録'); ?></h3>
		<?php echo $this->Form->input('id',array('label'=>'ID'));?>
		<table class="table table-bordered">
			<?php if ($clientData['is_system_admin'] == 1) { ?>
			<tr>
				<th>カテゴリ</th>
				<td><?php echo $this->Form->input('option_category_id', array('empty' => '---', 'required' => true));?></td>
			</tr>
			<?php } ?>
			<tr>
				<th><?php echo $optionName; ?>名</th>
				<td><?php echo $this->Form->input('name');?></td>
			</tr>
			<tr>
				<th>料金</th>
				<td>
					<p><?php echo $this->Form->input('price');?>円</p>
					<?php
					echo $this->Form->input('shape_flg', array(
							'type' => 'radio',
							'options' => $shapeList,
							'label' => true,
							'default' => 0,
							'div' => true,
					));
					?>
					<div id="PrivilegePeriodFlg">
					<?php
					echo $this->Form->input('period_flg', array(
							'type' => 'radio',
							'options' => array('1日ごとに加算', '24時間ごとに加算'),
							'label' => true,
							'default' => 0,
							'div' => true,
					));
					?>
					</div>
					<span class="red">※日数によって料金が異なる場合は、「1日当たりの金額」で登録後に編集できます。</span>

				</td>
			</tr>
			<tr>
				<th>1レンタル当たりの上限数</th>
				<td><?php echo $this->Form->input('maximum');?></td>
			</tr>
			<?php
			//シート編集の場合は単位を「台」に固定する
			if($this->action != 'sheet_add') {
			?>
			<tr>
				<th>単位</th>
				<td>
				<?php
				echo $this->Form->input('unit_name');
				?>
				</td>
			</tr>
			<?php
			} else {
				echo $this->Form->hidden('unit_name',array('value'=>'台'));
			}
			?>
			<tr>
<?php
	if (!$isClientAdmin) {
		unset($scopeList[0]);
	}
	$scopeOption = array('div'=>false,'label'=>false,'options'=>$scopeList);
?>
				<th>公開範囲</th>
				<td><?php echo $this->Form->input('scope', $scopeOption);?></td>
			</tr>
			<?php echo $this->Form->hidden('delete_flg',array('value'=>0));?>
			<?php echo $this->Form->hidden('client_id',array('value'=>$clientId));?>
			<?php echo $this->Form->hidden('staff_id',array('value'=>$staffId));?>
			<?php echo $this->Form->hidden('option_flg',array('value'=>$optionFlg));?>
		</table>
		<?php echo $this->Form->submit('登録',array('class'=>'btn btn-success'))?>
		<?php echo $this->Form->end(); ?>
		<?php echo $this->Html->link(__('戻る'), array('action' => $indexLink),array('class'=>'btn btn-warning')); ?>

</div>
<script>
$('input[name=data\\[Privilege\\]\\[shape_flg\\]]').change(function() {
	if ($(this).val() == '1') {
		$('#PrivilegePeriodFlg').show();
	} else {
		$('#PrivilegePeriodFlg').hide();
	}
});
</script>
