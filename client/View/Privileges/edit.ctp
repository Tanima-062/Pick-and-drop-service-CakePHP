<style>
input[type="text"],
input[type="number"]{
	width: 40%;
	-moz-box-sizing: border-box;
	-webkit-box-sizing: border-box;
	box-sizing: border-box;
	margin: 0;
	padding: 0 0 0 5px;
}
.table.detail {
	table-layout:fixed;
}
.table.detail tr th,
.table.detail tr td {
	text-align: center;
}
.table tr th,
.table tr td {
	border: solid #ddd 1px;
}
form .table th {
	background: #d5ecbf;
	width:20%;
}
#PrivilegePeriodFlg {
	display: none;
	margin-left: 40px;
}
</style>

<div class="privileges form">
<?php echo $this->Form->create('Privilege',array('enctype' => 'multipart/form-data', 'inputDefaults' => array('label' => false, 'div' => false))); ?>
		<h3><?php echo __($optionName.'編集'); ?></h3>
		<?php echo $this->Form->input('id',array('label'=>'ID'));?>
		<table class="table table-condensed">
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
					<p><?php echo $this->Form->input('price',array('value' => $privilegePriceFirstDay['PrivilegePrice']['price'], 'min' => 0)); ?>円</p>
					<?php echo $this->Form->input('shape_flg', array('type' => 'radio', 'options' => $shapeList, 'legend' => false, 'label' => true, 'div' => true)); ?>

					<div id="PrivilegePeriodFlg">
					<?php
					echo $this->Form->input('period_flg', array(
							'type' => 'radio',
							'options' => array('1日ごとに加算', '24時間ごとに加算'),
							'label' => true,
							'legend' => false,
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
			if($this->action != 'sheet_edit') {
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
	$selected = $this->data['Privilege']['scope'];
	if (!$isClientAdmin) {
		$scopeListEdit[$selected] = $scopeList[$selected];
	} else {
		$scopeListEdit = $scopeList;
	}
	$scopeOption = array('div'=>false,'label'=>false,'options'=>$scopeListEdit);
?>
				<th>公開範囲</th>
				<td><?php echo $this->Form->input('scope', $scopeOption);?></td>
			</tr>
			<tr>
				<th>削除フラグ</th>
				<td><?php echo $this->Form->input('delete_flg');?></td>
			</tr>
			<?php echo $this->Form->hidden('client_id',array('value'=>$clientId));?>
			<?php echo $this->Form->hidden('staff_id',array('value'=>$staffId));?>
			<?php echo $this->Form->hidden('option_flg', array('value' => $optionFlg)); ?>
			<?php echo $this->Form->hidden('default_shape_flg', array('value' => $this->data['Privilege']['shape_flg'])); ?>
		</table>
		<?php echo $this->Form->submit('編集',array('class'=>'btn btn-success'))?>
		<?php echo $this->Form->end(); ?>
		<?php
		if($this->action == 'sheet_edit') {
		  echo $this->Html->link(__('戻る'), array('action' => 'sheet_index'),array('class'=>'btn btn-warning'));
		} else {
		  echo $this->Html->link(__('戻る'), array('action' => 'index'),array('class'=>'btn btn-warning'));
		}
		?>
</div>


<?php if ($this->data['Privilege']['shape_flg'] == 1) { ?>

<h3>詳細設定料金</h3>
<p><?php echo $this->Html->link('詳細設定', '/Privileges/detail_edit/'.$this->data['Privilege']['id'], array('class' => 'btn btn-primary')); ?></p>
<table class="table table-condensed detail">
<?php
$arrayChunk = array_chunk($privilegePriceData, 7);
foreach ($arrayChunk as $key => $privilegePrice) { ?>
	<tr style="background:#ccc;">
		<?php foreach ($privilegePrice as $key => $value) {
			if ($value['PrivilegePrice']['span_count'] == 0) {
				$time = '以後一日';
			} else {
				$time = $value['PrivilegePrice']['span_count'].'日';
			}
		?>
		<th><?php echo $time; ?></th>
		<?php } ?>
	</tr>
	<tr>
		<?php foreach ($privilegePrice as $key => $value) { ?>
		<td><?php echo $value['PrivilegePrice']['price']; ?></td>
		<?php } ?>
	</tr>
<?php } ?>
</table>
<?php } ?>
<script>
$('input[name=data\\[Privilege\\]\\[shape_flg\\]]').change(function() {
	if ($(this).val() == '1') {
		$('#PrivilegePeriodFlg').show();
	} else {
		$('#PrivilegePeriodFlg').hide();
	}
});
$('input[name=data\\[Privilege\\]\\[shape_flg\\]]:checked').prop('checked', true).trigger('change');
</script>
