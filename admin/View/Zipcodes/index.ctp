<div class="zipcodes index">
	<h2>郵便番号一覧</h2>
	<?php echo $this->Form->create('Zipcode',array('type'=>'get','url'=>'.')); ?>
		<table class="table-bordered table-condensed">
			<tr>
				<th>都道府県</th>
				<td><?php echo $this->Form->input('Prefecture', array('div' => false, 'label' => false, 'options' => $prefectureList, 'empty' => false));?></td>
			</tr>
			<tr>
				<th>郵便番号</th>
				<td><?php echo $this->Form->input('Code', array('div' => false, 'label' => false, 'maxlength' => '7', 'pattern' => '\d+', 'class' => 'span3'));?>&nbsp;※ハイフンなし前方一致</td>
			</tr>
		</table>
		<br />
		<?php
			echo $this->Form->submit('検索する', array('class' => 'btn btn-primary', 'div' => false));
			echo $this->Form->button('リセット', array('type' => 'button', 'class' => 'btn btn-reset'));
		?>
	<?php echo $this->Form->end(); ?>


	<table class="table table-bordered">
	<thead>
	<tr class="btn-primary">
			<th>Id</th>
			<th>郵便番号</th>
			<th>都道府県</th>
			<th>市区町村名</th>
			<th>公開</th>
			<th class="actions"><?php echo $this->Html->link('新規登録','add',array('class'=>'btn btn-success'));?></th>
	</tr>
	</thead>
	<tbody id="sortable-div">
	<?php
	foreach ($zipcodes as $zipcode):
		$class = '';
		if(!empty($zipcode['Zipcode']['delete_flg'])) {
			$class = 'gray';
		}
	?>
	<tr id="<?php echo $zipcode['Zipcode']['id'];?>" class="ui-state <?php echo $class;?>">
		<td><?php echo h($zipcode['Zipcode']['id']); ?></td>
		<td><?php echo h($zipcode['Zipcode']['zipcode']); ?></td>
		<td><?php echo h($prefectureList[$zipcode['Zipcode']['prefecture_id']]); ?></td>
		<td><?php echo h($cityList[$zipcode['Zipcode']['city_id']]); ?></td>
		<td><?php echo h($deleteFlgOptions[$zipcode['Zipcode']['delete_flg']]); ?></td>
		<td class="actions">
			<?php echo $this->Html->link(__('編集'), array('action' => 'edit', $zipcode['Zipcode']['id']),array('class'=>'btn btn-warning')); ?>
		</td>
	</tr>
<?php endforeach; ?>
	</tbody>
	</table>

	<?php echo $this->Paginator->counter(array('format' => __('ページ {:page} / {:pages}　：　総レコード/ {:count}件')));?>

	<div class="pagination">
		<ul>
			<?php
			echo '<li>'.$this->Paginator->prev('< ' . __('戻る'), array(), null, array('class' => 'prev disabled')). '</li>';
			echo '<li>'.$this->Paginator->numbers(array('separator' => '')). '</li>';
			echo '<li>'.$this->Paginator->next(__('次へ') . ' >', array(), null, array('class' => 'next disabled')). '</li>';
			?>
		</ul>
	</div>

</div>