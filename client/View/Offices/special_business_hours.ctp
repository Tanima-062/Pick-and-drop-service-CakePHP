
<h3><?php echo $officeData['Office']['name']; ?>（特別営業時間）</h3>

<p><?php echo $this->Html->link(__('新規登録'), '/Offices/add_special_business_hours/'.$officeData['Office']['id'], array('class'=>'btn btn-success')); ?></p>

<?php foreach ($specialBusinessHours as $specialBusinessHour) {
?>
<table class="table table-bordered table-striped table-condensed">
	<?php foreach ($specialBusinessHour as $key => $value) {
	?>
		<?php if ($value === reset($specialBusinessHour)) { ?>
		<tr>
			<th class="span2" style="vertical-align:middle;"><?php echo $value['start_day']; ?>&nbsp;～&nbsp;<?php echo $value['end_day']; ?></th>
			<td class="span10" style="text-align:right;">
				<?php echo $this->Html->link(__('編集'), '/Offices/edit_special_business_hours/'.$value['id'].'/', array('class'=>'btn btn-warning')); ?>
			</td>
		</tr>
		<?php } ?>
	<?php
	foreach($weekArray as $key => $week) {
	?>
	<tr>
		<th style="<?php echo $key == 'hol' ? 'background:#fddfe2;' : ''; ?>"><?php echo $week; ?></th>
		<td style="<?php echo $key == 'hol' ? 'background:#fddfe2;' : ''; ?>">
			<?php
			if(!empty($value["{$key}_hours_from"])) {
			  echo date('H:i', strtotime($value["{$key}_hours_from"]));
			} else {
			   echo '未設定';
			}
			?>
			 &nbsp;～&nbsp;
		  <?php
			if(!empty($value["{$key}_hours_to"])) {
			  echo date('H:i', strtotime($value["{$key}_hours_to"]));
			} else {
			  echo '未設定';
			}
			?>

		</td>
	</tr>
	<?php
	}
	?>
	<?php } ?>
</table>
<?php } ?>