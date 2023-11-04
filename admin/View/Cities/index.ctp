<div class="cities index">
	<h2>市区町村一覧</h2>
	<?php echo $this->Form->create('City',array('type'=>'get','url'=>'.')); ?>
		<table class="table-bordered table-condensed">
			<tr>
				<th>都道府県</th>
				<td><?php echo $this->Form->input('Prefecture', array('div' => false, 'label' => false, 'options' => $prefectureList, 'empty' => false));?></td>
			</tr>
		</table>
		<br />
		<div style="float:left;padding:0px 20px 10px 0px;">
		<?php echo $this->Form->submit('検索する',array('class'=>'btn btn-primary'))?>
		</div>
	<?php echo $this->Form->end(); ?>


	<table class="table table-bordered">
	<thead>
	<tr class="btn-primary">
			<th>Id</th>
			<th>都道府県</th>
			<th>エリア</th>
			<th>市区町村名</th>
			<th>リンク用URL</th>
			<th>緯度</th>
			<th>経度</th>
			<th>トラベルコID</th>
			<th>公開</th>
			<th class="actions"><?php echo $this->Html->link('新規登録','add',array('class'=>'btn btn-success'));?></th>
	</tr>
	</thead>
	<tbody id="sortable-div">
	<?php
	foreach ($cities as $city):
		$class = '';
		if(!empty($city['City']['delete_flg'])) {
			$class = 'gray';
		}
	?>
	<tr id="<?php echo $city['City']['id'];?>" class="ui-state <?php echo $class;?>">
		<td><?php echo h($city['City']['id']); ?></td>
		<td><?php echo h($prefectureList[$city['City']['prefecture_id']]); ?></td>
		<td><?php echo h($areaList[$city['City']['area_id']]); ?></td>
		<td><?php echo h($city['City']['name']); ?></td>
		<td><?php echo h($city['City']['link_cd']); ?></td>
		<td><?php echo h($city['City']['latitude']); ?></td>
		<td><?php echo h($city['City']['longitude']); ?></td>
		<td><?php echo h($city['City']['travelko_city_id']); ?></td>
		<td><?php echo h($deleteFlgOptions[$city['City']['delete_flg']]); ?></td>
		<td class="actions">
			<?php echo $this->Html->link(__('編集'), array('action' => 'edit', $city['City']['id']),array('class'=>'btn btn-warning')); ?>
		</td>
	</tr>
<?php endforeach; ?>
	</tbody>
	</table>

	<?php echo $this->Paginator->counter(array('format' => __('ページ {:page} / {:pages}　：　総レコード/ {:count}件')));?>

	<div class="pagination">
		<ul>
			<?php
			echo '<li>'.$this->Paginator->first('<< ' . __('最初へ')). '</li>';
			echo '<li>'.$this->Paginator->prev('< ' . __('戻る')). '</li>';
			echo '<li>'.$this->Paginator->numbers(array('separator' => '')). '</li>';
			echo '<li>'.$this->Paginator->next(__('次へ') . ' >'). '</li>';
			echo '<li>'.$this->Paginator->last(__('最後へ') . ' >>'). '</li>';
			?>
		</ul>
	</div>

</div>