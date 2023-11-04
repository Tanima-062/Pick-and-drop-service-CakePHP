<div class="carModels index">

	<h3><?php echo __('車種一覧'); ?></h3>

	<?php echo $this->Form->create('CarModel',array('type'=>'get','inputDefaults'=>array('label'=>false))); ?>
		<table class="table-bordered table-condensed">
			<tr>
				<th>自動車メーカー</th>
				<td><?php echo $this->Form->input('automaker_id', array('div' => false, 'label' => false, 'options' => $automakerList, 'empty'=>'---'));?></td>
			</tr>
			<tr>
				<th>車種名</th>
				<td><?php echo $this->Form->input('name',array('div' => false, 'label' => false, 'maxlength' => '128', 'class' => 'span8'));?></td>
			</tr>
		</table>
		<br />
		<div style="margin-bottom: 10px;">
			<?php
				echo $this->Form->submit('検索する', array('class' => 'btn btn-primary', 'div' => false));
				echo $this->Form->button('リセット', array('type' => 'button', 'class' => 'btn btn-reset'));
			?>
		</div>
		<?php echo $this->Html->link(__('新規登録'),array('action' => 'add'),array('class'=>'btn btn-success')); ?>
	<?php echo $this->Form->end(); ?>
	<table class="table table-bordered table-condensed">
	<tr>
			<th><?php echo $this->Paginator->sort('id','車種ID'); ?></th>
			<th><?php echo $this->Paginator->sort('automaker_id','自動車メーカーID'); ?></th>
			<th><?php echo $this->Paginator->sort('name','車種名'); ?></th>
			<th><?php echo $this->Paginator->sort('trunk_space','スーツケース'); ?></th>
			<th><?php echo $this->Paginator->sort('golf_bag','ゴルフバッグ'); ?></th>
			<th><?php echo $this->Paginator->sort('capacity','法定定員'); ?></th>
			<th><?php echo $this->Paginator->sort('recommended_capacity','推奨定員'); ?></th>
			<th><?php echo $this->Paginator->sort('door','ドア数'); ?></th>
			<th><?php echo $this->Paginator->sort('image_relative_url','画像相対URL'); ?></th>
			<th><?php echo $this->Paginator->sort('staff_id','更新者'); ?></th>
			<th><?php echo $this->Paginator->sort('delete_flg','公開'); ?></th>
			<th class="actions"><?php echo __('Actions'); ?></th>
	</tr>
	<?php
	foreach ($carModels as $val): ?>
	<tr class="<?php if (!empty($val['CarModel']['delete_flg'])) { echo 'gray'; } ?>">
		<td><?php echo h($val['CarModel']['id']); ?>&nbsp;</td>
		<td><?php echo h($automakerList[$val['CarModel']['automaker_id']]); ?>&nbsp;</td>
		<td><?php echo h($val['CarModel']['name']); ?>&nbsp;</td>
		<td><?php echo h($val['CarModel']['trunk_space']); ?>&nbsp;</td>
		<td><?php echo h($val['CarModel']['golf_bag']); ?>&nbsp;</td>
		<td><?php echo h($val['CarModel']['capacity']); ?>&nbsp;</td>
		<td><?php echo h($val['CarModel']['recommended_capacity']); ?>&nbsp;</td>
		<td><?php echo h($val['CarModel']['door']); ?>&nbsp;</td>
		<td><?php /* echo $this->Html->image('../../img/car_model_img/'.$val['CarModel']['image_relative_url'],array('width'=>100)); */ ?>&nbsp;</td>
		<td>
			<?php echo $val['Staff']['name'];?>
		</td>
		<td><?php echo h($deleteFlgOptions[$val['CarModel']['delete_flg']]); ?>&nbsp;</td>
		<td class="actions">
			<?php echo $this->Html->link(__('詳細'), array('action' => 'view', $val['CarModel']['id']),array('class'=>'btn btn-success btn-small')); ?>
			<?php echo $this->Html->link(__('編集'), array('action' => 'edit', $val['CarModel']['id']),array('class'=>'btn btn-warning btn-small')); ?>
		</td>
	</tr>
<?php endforeach; ?>
	</table>

	<?php echo $this->Paginator->counter(array('format' => __('ページ {:page} / {:pages}　：　総レコード/ {:count}個')));?>

	<div class="pagination">
		<ul class="pager">
			<?php
				echo '<li class=\"previous\">'.$this->Paginator->prev('< 前へ', array(), null, array('class' => 'prev disabled')). '</li>';
				echo '<li>'.$this->Paginator->numbers(array('separator' => '')). '</li>';
				echo '<li class=\"next\">'.$this->Paginator->next('次へ >', array(), null, array('class' => 'next disabled')). '</li>';
			?>
		</ul>
	</div>
</div>