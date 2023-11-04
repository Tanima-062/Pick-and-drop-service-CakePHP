<script>
// リセット追加処理
$(document).on('click', '.btn-reset', function() {
	let $yearSelect = $('#SearchDateYear');
	// 後処理
	setTimeout(function() {
		$yearSelect.prop("selectedIndex", 1);
	}, 1);
});
</script>
<style>
table {
	font-size: 14px;
}
.checkbox {
	float:left;
	width:40px;
	margin-left:2px;
}
</style>
<div class="campaigns index">
	<h2><?php echo __('キャンペーン期間一覧'); ?></h2>
	<p>
		<?php echo $this->Html->link(__('新規追加'), array('action' => 'add'), array('class' => 'btn btn-success')); ?>
	</p>
	<?php echo $this->Form->create('search', array('type' => 'get', 'inputDefaults' => array('label' => false, 'div' => false,), 'class' => 'form-search')); ?>
	<table class="table-bordered table-striped table-condensed">
		<tr>
			<th class="span4">キャンペーン期間名</th>
			<td style="width:400px;"><?php echo $this->Form->input('name');?></td>
		</tr>
		<tr>
			<th class="span4">対象期間</th>
			<td style="width:400px;"><?php echo $this->element('selectDatetime', $targetDateOptions);?></td>
		</tr>
		<tr>
			<th class="span4">曜日</th>
			<td style="width:400px;">
				<?php echo $this->Form->input("week", [
					'type' => 'select', 
					'multiple'=> 'checkbox',
					'options' => Constant::weekJp(),
					'div' => false
				]); 
				?>	
			</td>
		</tr>
	</table>
	<br>
	<p>
		<?php
			echo $this->Form->button('検索', array('type' => 'submit', 'class' => 'btn btn-primary'));
			echo $this->Form->button('リセット', array('type' => 'button', 'class' => 'btn btn-reset'));
		?>
	</p>
	<?php echo $this->Form->end(); ?>

	<table class="table table-bordered">
	<tr class="success">
			<th>キャンペーン期間名</th>
			<th>対象期間</th>
			<th>曜日</th>
			<th>公開範囲</th>
	</tr>
	<?php
		foreach ($campaigns as $campaign) {
	?>
	<tr>
		<td><?php echo $this->Html->link(h($campaign['name']), array('action' => 'edit', $campaign['id'])); ?></td>
		<td style="width: 230px;">
		<?php
			foreach ($campaign['terms'] as $k => $term) {
		?>
			<?php echo str_replace('-', '/', h($term['start_date'])); ?> ～ <?php echo str_replace('-', '/', h($term['end_date'])); ?><br>
		<?php } ?>
		</td>
		<td style="width: 170px;">
		<?php
			foreach ($campaign['terms'] as $k => $term) {
		?>
			<?php 
				$weekStr = '';
				$weekJp = Constant::weekJp();
				if ($term['mon']) {
					$weekStr .= $weekJp['0'].', ';
				}
				if ($term['tue']) {
					$weekStr .= $weekJp['1'].', ';
				}
				if ($term['wed']) {
					$weekStr .= $weekJp['2'].', ';
				}
				if ($term['thu']) {
					$weekStr .= $weekJp['3'].', ';
				}
				if ($term['fri']) {
					$weekStr .= $weekJp['4'].', ';
				}
				if ($term['sat']) {
					$weekStr .= $weekJp['5'].', ';
				}
				if ($term['sun']) {
					$weekStr .= $weekJp['6'].', ';
				}
				if ($term['hol']) {
					$weekStr .= $weekJp['7'].', ';
				}
				echo rtrim($weekStr, ', ');
			?><br>
		<?php } ?>
		</td>
		<td><?php echo $scopeList[$campaign['scope']];?></td>
	</tr>
	<?php } ?>
	</table>

</div>
