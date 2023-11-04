<div class="stations index">
	<h2>駅一覧</h2>
	<?php echo $this->Form->create('Station',array('type'=>'get','url'=>'.')); ?>
		<table class="table-bordered table-condensed">
			<tr>
				<th>都道府県</th>
				<td><?php echo $this->Form->input('Prefecture', array('div' => false, 'label' => false, 'options' => $prefectureList, 'empty' => '---'));?></td>
			</tr>
			<tr>
				<th>主要駅</th>
				<td><?php echo $this->Form->input('major_flg', array('type' => 'checkbox','div' => false, 'label' => '主要駅'));?></td>
			</tr>
			<tr>
				<th>駅タイプ</th>
				<td><?php echo $this->Form->input('type', array('div' => false, 'label' => false, 'options' => $stationTypes, 'empty' => '全て'));?></td>
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
			<th>市区町村</th>
			<th>駅名</th>
			<th>リンク用URL</th>
			<th>緯度</th>
			<th>経度</th>
			<th>主要駅</th>
			<th>地図</th>
			<th>駅タイプ</th>
			<th>トラベルコID</th>
			<th>公開</th>
			<th class="actions"><?php echo $this->Html->link('新規登録','add',array('class'=>'btn btn-success'));?></th>
	</tr>
	</thead>
	<tbody id="sortable-div">
	<?php
	foreach ($stations as $station):
		$class = '';
		if(!empty($station['Station']['delete_flg'])) {
			$class = 'gray';
		}
	?>
	<tr id="<?php echo $station['Station']['id'];?>" class="ui-state <?php echo $class;?>">
		<td><?php echo h($station['Station']['id']); ?></td>
		<td><?php echo h($prefectureList[$station['Station']['prefecture_id']]); ?></td>
		<td><?php echo h($cityList[$station['Station']['city_id']]); ?></td>
		<td><?php echo h($station['Station']['name']); ?></td>
		<td><?php echo h($station['Station']['url']); ?></td>
		<td><?php echo h($station['Station']['latitude']); ?></td>
		<td><?php echo h($station['Station']['longitude']); ?></td>
		<td><?php echo h($station['Station']['major_flg'] ? '主要駅' : ''); ?></td>
		<td><?php echo h($station['Station']['pref_map_flg'] ? '表示する' : ''); ?></td>
		<td><?php echo h($stationTypes[$station['Station']['type']]); ?></td>
		<td><?php echo h($station['Station']['travelko_id']); ?></td>
		<td><?php echo h($deleteFlgOptions[$station['Station']['delete_flg']]); ?></td>
		<td class="actions">
			<?php //echo $this->Html->link(__('詳細'), array('action' => 'view', $station['Station']['id']),array('class'=>'btn btn-success')); ?>
			<?php echo $this->Html->link(__('編集'), array('action' => 'edit', $station['Station']['id']),array('class'=>'btn btn-warning')); ?>
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