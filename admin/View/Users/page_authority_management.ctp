<style>
label {
	display: inline-block;
}
.blocks {
	border:solid #e3e3e3 1px;
	margin: 15px 0;
	padding: 0 19px;
}
</style>

<script type="text/javascript">
$(function(){
	// 全ページcheckbox選択
	$('#pageAllCheck').on('click', function(){
		$("#pageList input:checkbox").prop({'checked':true});
	});
	// 全ページcheckbox非選択(管理画面TOPとパスワード変更画面は強制選択)
	$('#pageAllCheckClear').on('click', function(){
		$("#pageList input:checkbox").prop({'checked':false});
		$("#StaffPageId1").prop('checked', true);
		$("#StaffPageId24").prop('checked', true);
	});
});
</script>

<h2>メニュー設定</h2>
<p>クライアント名：<?php echo $staffData['Client']['name']; ?>&emsp;&emsp;担当者名：<?php echo $staffData['Staff']['name']; ?></p>

<?php echo $this->Form->create('Staff', array('inputDefaults' => array('label' => false,'div' => false))); ?>
	<div id="pageList">
		<div id="pageCheckBtn" style="text-align:right;">
			<span id="pageAllCheck" class="btn btn-mini btn-inverse">全ページチェック</span>
			<span id="pageAllCheckClear" class="btn btn-mini">全ページ外す</span>
		</div>
		<?php foreach ($pageCategories as $key => $page) { ?>
		<div class="blocks">
			<p style="font-size: 18px;font-weight:bold;padding:10px 0;margin:0;"><?php echo $page['PageCategory']['name']; ?></p>
			<?php if (!empty($page['Page'])) { ?>
				<?php foreach ($page['Page'] as $value) { ?>
					<?php
						if ($value['id'] == 1 || $value['id'] == 24) {
							echo $this->Form->input('Staff.page_id.'.$value['id'], array('type' => 'checkbox', 'label' => $value['name'], 'value' => $value['id'], 'disabled' => 'disabled', 'checked'=>'checked'));
						} else {
							if ($staffData['Client']['is_managed_package'] || !in_array($value['url'], array('Sales/month_organized', 'Sales/daily_organized'))) {
								echo $this->Form->input('Staff.page_id.' . $value['id'], array('type' => 'checkbox', 'label' => $value['name'], 'value' => $value['id']));
							}
						}
					?>
				<?php } ?>
			<?php } ?>
		</div>
		<?php } ?>
	</div>
<?php
	echo $this->Form->submit('登録', array('class' => 'btn btn-success', 'div' => false));
	echo $this->Form->end();
?>
<div class="clearfix">
<?php echo $this->Html->link('一覧へ戻る', '/Users/', array('class' => 'btn btn-primary pull-right')); ?>
</div>