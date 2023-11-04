<style>
.table tbody tr.success {
	background-color: #DFF0D8;
}
#CommoditiesIndexForm .table tbody tr th {
	width: 10%;
}
#CommoditiesIndexForm .table input[type="text"] {
	padding: 3px 0;
}
#CommoditiesIndexForm .table select {
	margin: 0;
}
.secret {
	background-color: #BDBDBD;
}
.request {
	background-color: whilte;
}
</style>

<div class="commodities index">

	<form>
		<div class="error">
			<h3>
				<?php echo $this->Session->flash();?>
			</h3>
		</div>
	</form>

	<h3>商品一覧</h3>
	<p>
		<?php echo $this->Html->link('新規追加', '/Commodities/add/',array('class'=>'btn btn-success')); ?>
	</p>
	<?php echo $this->Form->create('Commodities',array('action'=>'index','type'=>'get')); ?>
	<table class="table table-bordered table-striped">
		<tr>
			<th>商品名</th>
			<td colspan="3"><?php echo $this->Form->input('name',array('label'=>false, 'style' => 'width:100%')); ?></td>
		</tr>
		<?php if ($clientData['Client']['is_managed_package']) : ?>
		<tr>
			<th>販売方法</th>
			<td colspan="3"><?php echo $this->Form->select('sales_type', Constant::salesType()); ?></td>
		</tr>
		<?php endif; ?>
		<tr>
			<th>年月日</th>
			<td>
				<?php echo $this->element('selectDatetime', $datetimeFromOptions); ?>
			</td>
			<th>車両クラス</th>
			<td><?php echo $this->Form->select('car_class_id', $carClassesFromOptions); ?></td>
		</tr>
		<tr>
			<th>在庫管理地域</th>
			<td><?php echo $this->Form->select('stock_group_id', $stockGroupFromOptions); ?></td>
			<th>公開/非公開</th>
			<td>
				<?php echo $this->Form->select('is_published', array(
						1=>'公開商品のみ表示',
						0=>'非公開商品のみ表示')); ?>
			</td>
		</tr>
		<tr>
			<th>商品グループ</th>
			<td><?php echo $this->Form->select('commodity_group_id', $commodityGroupFromOptions); ?></td>
			<th>車両タイプ</th>
			<td><?php echo $this->Form->select('car_type_id', $carTypeLists); ?></td>
		</tr>

	</table>
	<p>
		<?php
			echo $this->Form->submit('検索', array('class' => 'btn btn-primary', 'div' => false));
			echo $this->Form->button('リセット', array('type' => 'button', 'class' => 'btn btn-reset'));
		?>
		<?php if (isset($count)) { ?>
		<span>
			公開：
			<?php echo $count[0]['public_count']; ?>
			件
		</span>
		<span class="secret">
			非公開：
			<?php echo $count[0]['private_count']; ?>
			件
		</span>
		<?php } ?>
	</p>
	<?php
	if (!empty($this->data['Commodities']['commodity_group_id'])) {
		echo $this->Html->link('グループ編集',
				array('controller'=>'CommodityGroups','action' => 'detail_edit',
						$this->data['Commodities']['commodity_group_id']),array('class'=>'btn btn-warning'));
			} ?>
	<?php echo $this->Form->end(); ?>

	<?php if (isset($commodities)) { ?>
	<table class="table table-bordered table-condensed">
		<tr class="success">
			<th style="width: 10%;">参考画像</th>
			<?php if ($clientData['Client']['is_managed_package']) : ?>
			<th style="width: 10%;"><?php echo $this->Paginator->sort('Commodity.sales_type', '販売方法'); ?></th>
			<?php endif; ?>
			<th style="width: 32%;"><?php echo $this->Paginator->sort('Commodity.name','商品名'); ?></th>
			<th style="width: 10%;"><?php echo $this->Paginator->sort('CarClasses.name','車両クラス'); ?></th>
			<th style="width: 10%;">1日(24h)料金</th>
			<th style="width: 20%;"><?php echo $this->Paginator->sort('CommodityTerm.available_from','対象期間'); ?></th>
			<th style="width: 8%;"></th>
		</tr>
		<?php
		foreach ($commodities as $commodity) {
			$availableFrom = explode(' ',$commodity['CommodityTerm']['available_from']);
			$availableTo   = explode(' ',$commodity['CommodityTerm']['available_to']);
			$class= 'default';
			if(!$commodity['Commodity']['is_published']) {
				$class = 'secret';
			}
		?>
		<tr class="<?php echo $class;?>">
			<td>
			<?php if(!empty($commodity['CommodityImage']['image_relative_url'])){ 
				$imagePath = "../../img/commodity_reference/".$clientId.'/'.$commodity['CommodityImage']['image_relative_url'];
			} else{
				$imagePath = "../../img/noimage_resize.png";
			}
			echo $this->Html->image($imagePath, array('style'=>'width:100%;'));
			?>
			
			<?php //echo $this->Html->image("../../img/commodity_main/".$clientId.'/'.$commodity['Commodity']['image_relative_url'], array('style'=>'width:100%;')) ?>

			</td>
			<?php if ($clientData['Client']['is_managed_package']) : ?>
			<td>
				<?php echo h(Constant::salesType()[$commodity['Commodity']['sales_type']]); ?>
			</td>
			<?php endif; ?>
			<td class="commodity_name">
				<?php echo $this->Html->link($commodity['Commodity']['name'],
						array('action' => 'edit', $commodity['Commodity']['id']));
				?>
			</td>
			<td class="commodity_car_class" style="line-height:100%;">
				<?php
				foreach ($commodity['CarClasses'] as $carClass) {
						echo '<p>' . h($carClass['name']) . '</p>';
					if ($clientData['is_system_admin'] && !empty($commodity['CommodityItem']['sipp_code'])) {
						echo '<p>' . h($commodity['CommodityItem']['sipp_code']) . '</p>';
					}
					if(!empty($carClass['id'])) {
						echo '<p>'.$this->Html->link('・プレビュー','/Commodities/preview/'. $commodity['Commodity']['id'] . '/' . $carClass['id'],array('class'=>'','target'=>'_blank')).'</p>';
						if ($carClass['priceSystem'] !== '') {
							echo '<p>' . $this->Html->link(
								((int)$commodity['Commodity']['day_time_flg'] === 1) ? '・価格変更(時間制)' : '・価格変更(暦日制)',
								"/commodities/{$carClass['priceSystem']}/{$commodity['Commodity']['id']}/{$carClass['id']}/",
								array('class'=>'','target'=>'_blank')
							) . '</p>';
						}
					}
				}
				?>
			</td>
			<td>
			<?php
			if ($commodity['Commodity']['day_time_flg'] == 1) {
				echo '24H料金<br>';
			} else {
				echo '日帰り料金<br>';
			}
			echo !empty($commodity['CommodityPrice']['price']) ? number_format($commodity['CommodityPrice']['price']).'円' : '';
			?>
			</td>
			<td class="commodity_available" style="font-size:13px;line-height:110%;">
				<?php echo h($availableFrom[0]); ?>
				～
				<?php echo h($availableTo[0]); ?>
				<?php
					if (isset($aoTerms[$commodity['CommodityItem']['id']])) {
						echo '<br>-------------------------------';
						foreach ($aoTerms[$commodity['CommodityItem']['id']] as $t) {
							echo '<br>'.h($t['start_date']) . ' 〜 ' . h($t['end_date']);
						}
					}
				?>
			</td>
			<td style="text-align: center;">
				<?php echo $this->Html->link('コピー',
						array('action'=>'copy',$commodity['Commodity']['id']),
						array('class'=>'btn btn-success btn-small')); ?>
			</td>
		</tr>
		<?php } ?>
	</table>
	<div class="pagination">
		<ul>
			<?php
			// 検索条件の値をpaginateのGETパラメータに付加
			$this->Paginator->options(array('url' => $postConditions));
			echo '<li>'.$this->Paginator->prev('< ' . __('戻る'), array(), null, array('class' => 'prev disabled')). '</li>';
			echo '<li>'.$this->Paginator->numbers(array('separator' => '')). '</li>';
			echo '<li>'.$this->Paginator->next(__('次へ') . ' >', array(), null, array('class' => 'next disabled')). '</li>';
			?>
		</ul>
	</div>
	<?php } ?>
</div>
