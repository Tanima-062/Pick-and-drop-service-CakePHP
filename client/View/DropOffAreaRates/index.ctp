<div class="row-fluid">
	<div class="span11">
		<h3>乗捨料金一覧</h3>

		<?php
		echo $this->Form->create('DropOffAreaRate',array('type'=>'get','inputDefaults'=>array('label'=>false)));
		?>
		<table class="table table-bordered">
			<tr>
				<th class="span2">出発エリア</th>
				<td class="span4"><?php echo $this->Form->input('rent_drop_off_area_id',array('options'=> $dropOfAreaList,'empty'=>'---'));?></td>
				</td>
				<th class="span2">返却エリア</th>
				<td class="span4"><?php echo $this->Form->input('return_drop_off_area_id',array('options'=> $dropOfAreaList,'empty'=>'---'));?></td>
				<td>
				<?php
					echo $this->Form->submit('絞り込み', array('class' => 'btn btn-info', 'div' => false));
					echo $this->Form->button('リセット', array('type' => 'button', 'class' => 'btn btn-reset'));
	            ?>
	            </td>
			</tr>
		</table>
		<?php
		echo $this->Form->end();
		?>

		<?php
		$urlParams = $this->params['url'];
		unset($urlParams['url']);
		$this->Paginator->options(array('url' => array('?' => http_build_query($urlParams))));
		$pageParams = $this->Paginator->params();
		echo 'ページ '.$this->Paginator->current().' / '.$pageParams['pageCount'].'　：　総レコード/ '.$pageParams['count'].'件';

		if(!empty($pageParams) && $pageParams['pageCount'] > 1) {
		?>
		<div class="pagination">
			<ul>
				<?php
				if($this->Paginator->hasPrev()) {
					echo '<li>'.$this->Paginator->prev('< ' . __('戻る'), array(), null, array('class' => 'prev disabled')). '</li>';
				}

				echo '<li>'.$this->Paginator->numbers(array('separator' => '')). '</li>';

				if($this->Paginator->hasNext()) {
					echo '<li>'.$this->Paginator->next(__('次へ') . ' >', array(), null, array('class' => 'next disabled')). '</li>';
				}
				?>
			</ul>
		</div>
		<?php }?>

		<table class="table table-bordered">
			<tr class="success">
				<th><?php echo $this->Paginator->sort('rent_drop_off_area_id','出発エリア');?></th>
				<th><?php echo $this->Paginator->sort('return_drop_off_area_id','返却エリア');?></th>
				<?php foreach ($dropOffPricePatternList as $k => $v) {
					$price = ($k > 1) ? 'price' . $k : 'price';
				?>
				<th><?php echo $this->Paginator->sort($price, $v); ?></th>
				<?php } ?>
				<th><?php echo $this->Paginator->sort('delete_flg','公開フラグ');?></th>
				<th class="actions" style="width:20%;"><?php echo $this->Html->link('新規追加','add',array('class'=>'btn btn-success'));?></th>
			</tr>
		<?php
		foreach ($dropOffAreaRates as $dropOffAreaRate) {
			$class = '';
			if($dropOffAreaRate['DropOffAreaRate']['delete_flg'] == 1) {
				$class = 'deleted';
			}

		?>
			<tr class="<?php echo $class;?>">
				<td><?php echo $dropOfAreaList[$dropOffAreaRate['DropOffAreaRate']['rent_drop_off_area_id']]; ?>&nbsp;</td>
				<td><?php echo $dropOfAreaList[$dropOffAreaRate['DropOffAreaRate']['return_drop_off_area_id']]; ?>&nbsp;</td>
				<?php foreach ($dropOffPricePatternList as $k => $v) {
					$price = ($k > 1) ? 'price' . $k : 'price';
				?>
				<td><?php echo $dropOffAreaRate['DropOffAreaRate'][$price]; ?>&nbsp;</td>
				<?php } ?>
				<td><?php echo $deleteFlgOptions[$dropOffAreaRate['DropOffAreaRate']['delete_flg']]; ?>&nbsp;</td>
				<td class="actions">
					<?php echo $this->Html->link(__('編集'), array('action' => 'edit', $dropOffAreaRate['DropOffAreaRate']['id']),array('class'=>'btn btn-warning')); ?>
					<?php echo $this->Form->postLink('削除', array('action' => 'delete', $dropOffAreaRate['DropOffAreaRate']['id']), array('class'=>'btn btn-danger'), __('%sを削除しますか?', $dropOffAreaRate['DropOffAreaRate']['id'])); ?>
				</td>
			</tr>
		<?php } ?>
		</table>

		<?php
		if(!empty($pageParams) && $pageParams['pageCount'] > 1) {
		?>
		<div class="pagination">
			<ul>
				<?php
				if($this->Paginator->hasPrev()) {
					echo '<li>'.$this->Paginator->prev('< ' . __('戻る'), array(), null, array('class' => 'prev disabled')). '</li>';
				}

				echo '<li>'.$this->Paginator->numbers(array('separator' => '')). '</li>';

				if($this->Paginator->hasNext()) {
					echo '<li>'.$this->Paginator->next(__('次へ') . ' >', array(), null, array('class' => 'next disabled')). '</li>';
				}
				?>
			</ul>
		</div>
		<?php }?>

	</div>
</div>