<style>
input[type="text"],
input[type="number"]{
	width: 80%;
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
}
</style>

<h3>詳細料金設定</h3>
<?php echo $this->Form->create('PrivilegePrice'); ?>
<table class="table table-condensed detail">
<?php
$arrayChunk = array_chunk($priceData, 7);
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
		<td>
		<?php
		echo $this->Form->input('PrivilegePrice.'.$value['PrivilegePrice']['id'].'.price', array(
				'type' => 'number',
				'value' => $value['PrivilegePrice']['price'],
				'label' => false,
				'div' => false,
				'required' => true,
		));
		?>円
		<?php echo $this->Form->hidden('PrivilegePrice.'.$value['PrivilegePrice']['id'].'.id', array('value' => $value['PrivilegePrice']['id'])); ?>
		</td>
		<?php } ?>
	</tr>
<?php } ?>
</table>

<?php echo $this->Form->submit('設定保存', array(
		'class' => 'btn btn-success'
		)); ?>

<?php echo $this->Form->end(); ?>


