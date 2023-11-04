<div class="tourprices import">
	<h3>募集型料金CSVインポート</h3>
	<?php echo $this->Form->create('TourPrice', array('type' => 'file', 'inputDefaults' => array('label' => false))); ?>
	<?php $referer = ($this->request->data['Custom']['referer'] ? $this->request->data['Custom']['referer'] : $this->request->referer()); ?>
	<?php echo $this->Form->hidden('Custom.referer', array('value' => $referer)); ?>
		<table class="table-bordered table-condensed">
			<tr>
				<th>CSVファイル</th>
				<td><?php echo $this->Form->file('import_csv', array('div' => false, 'label' => false, 'accept' => '.csv')); ?></td>
			</tr>
		</table>
		<div style="padding:10px 0px 0px 0px;">
			<?php echo $this->Form->submit('インポート', array('id' => 'import_btn', 'class' => 'btn btn-primary')); ?>
		</div>
	<?php echo $this->Form->end(); ?>

<?php if (!empty($errList)) { ?>
	<table class="table table-bordered">
		<tr>
			<th>行番号</th>
			<th>利用空港</th>
			<th>販売開始</th>
			<th>販売終了</th>
			<th>営業開始</th>
			<th>営業終了</th>
			<th>乗車人数</th>
			<th>販売単価</th>
			<th>エラー</th>
		</tr>
	<?php
		foreach ($errList as $err):
	?>
		<tr>
			<td><?php echo h($err['no']); ?></td>
			<td><?php echo h($err[0]); ?></td>
			<td><?php echo h($err[1]); ?></td>
			<td><?php echo h($err[2]); ?></td>
			<td><?php echo h($err[3]); ?></td>
			<td><?php echo h($err[4]); ?></td>
			<td><?php echo h($err[5]); ?></td>
			<td><?php echo h($err[6]); ?></td>
			<td><?php echo $err['errors']; ?></td>
		</tr>
	<?php endforeach; ?>
	</table>
<?php } ?>
</div>
<script>
    $(function() {
        $('#import_btn').on('click', function(e) {
            if ($('#TourPriceImportCsv').val() == '') {
                alert('CSVファイルを選択してください');
                e.preventDefault();
                return;
            }
            if (!window.confirm('CSVファイルのインポートを実行してもよろしいですか？')) {
                e.preventDefault();
            }
        });
    });
</script>