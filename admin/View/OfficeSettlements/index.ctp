<div class="row-fluid">
	<div>
		<h3>営業所精算管理マスタ</h3>

		<?php echo $this->Form->create('Payment',['type'=>'get']); ?>
		<table class="table-bordered table-condensed">
			<tr>
				<th>Id</th>
				<td><?php echo $this->Form->input('id', ['type' => 'text','div'=>false,'label'=>false, 'value'=>$id]); ?>
				</td>
			</tr>
			<tr>
				<th>クライアント名</th>
				<td><?php echo $this->Form->input('client_id', [
						'empty' => '--',
						'options' => $clientList,
						'div' => false,
						'label' => false,
						'value' => $client_id
				]);?>
				</td>
			</tr>
			<tr>
				<th>精算管理会社名</th>
				<td><?php echo $this->Form->input('settlement_company_id', [
						'empty' => '--',
						'options' => $settlementCompanyList,
						'div' => false,
						'label' => false,
						'value' => $settlement_company_id
				]);?>
				</td>
			</tr>
			<tr>
				<th>都道府県名</th>
				<td><?php echo $this->Form->input('prefecture_id', [
						'empty' => '--',
						'options' => $prefectureList,
						'div' => false,
						'label' => false,
						'value' => $prefecture_id
				]);?>
				</td>
			</tr>
			<tr>
				<th>公開</th>
				<td><?php echo $this->Form->input('delete_flg', [
						'empty' => '--',
						'options' => [ 0 => '公開', 1 => '非公開'],
						'div' => false,
						'label' => false,
						'value' => $delete_flg
				]);?>
				</td>
			</tr>
		</table>
		<br />

		<?php
			echo $this->Form->submit('検索する', ['class' => 'btn btn-primary', 'div' => false]);
			echo $this->Form->button('リセット', array('type' => 'button', 'class' => 'btn btn-reset'));
		?>
		<?php echo $this->Form->end(); ?>
	<br />

		<?php
		$pageParams = $this->Paginator->params();
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
		<?php
		}
		?>
		<?php echo $this->Paginator->counter(array('format' => __(' 合計{:count}件')));?>
		<table class="table table-bordered">
			<tr class="success">
				<th><?php echo $this->Paginator->sort('id', '営業所ID');?></th>
				<th><?php echo $this->Paginator->sort('name', '営業所名');?></th>
				<th><?php echo $this->Paginator->sort('client_id','クライアント名');?></th>
				<th><?php echo $this->Paginator->sort('settlement_company_id','精算管理会社名');?></th>
				<th><?php echo $this->Paginator->sort('prefecture_id','都道府県');?></th>
				<th><?php echo $this->Paginator->sort('delete_flg','公開');?></th>
				<th><?php echo $this->Paginator->sort('modified','更新日');?></th>
				<th class="actions"></th>
			</tr>
			<?php
			foreach ($Offices as $Office) {
			?>
			<tr>
				<td><?php echo $Office['Office']['id']; ?>&nbsp;</td>
				<td><?php echo $Office['Office']['name']; ?>&nbsp;</td>
				<td><?php echo h($Office['Client']['name']); ?>&nbsp;</td>
				<td><?php echo $Office['SettlementCompany']['name']; ?>&nbsp;</td>
				<td><?php echo h($Office['Prefecture']['name']); ?>&nbsp;</td>
				<td><?php echo (!$Office['Office']['delete_flg']) ? '公開' : '非公開'; ?>&nbsp;</td>
				<td><?php echo $Office['Office']['modified']; ?>&nbsp;</td>
				<td class="actions">
					<?php echo $this->Html->link('編集', array('action' => 'edit', $Office['Office']['id']),array('class'=>'btn btn-warning')); ?>
				</td>
			</tr>
			<?php
			}
			?>
		</table>

		<?php if(!empty($pageParams) && $pageParams['pageCount'] > 1) { ?>
		<div class="pagination">
			<ul>
				<?php
				if($this->Paginator->hasPrev()) {
					echo '<li>'.$this->Paginator->prev('< 戻る', array(), null, array('class' => 'prev disabled')). '</li>';
				}

				echo '<li>'.$this->Paginator->numbers(array('separator' => '')). '</li>';

				if($this->Paginator->hasNext()) {
					echo '<li>'.$this->Paginator->next('次へ >', array(), null, array('class' => 'next disabled')). '</li>';
				}
				?>
			</ul>
		</div>
		<?php }?>

	</div>
</div>