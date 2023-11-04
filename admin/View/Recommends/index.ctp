<div class="row-fluid">
	<div>
		<h3>レコメンド一覧</h3>

		<?php echo $this->Form->create('Recommend',['type'=>'get']); ?>
		<table class="table-bordered table-condensed" style="width:650px;padding:0;margin:0">
			<tr>
				<th>クライアント名</th>
				<td><?php echo $this->Form->input('client_id', array('div' => false, 'label' => false, 'options' => $clientList, 'empty' => '---')); ?></td>
			</tr>
			<tr>
				<th>掲載枠</th>
				<td><?php echo $this->Form->input('space', array('options' => $spaceOptions, 'div' => false, 'label' => false, 'empty' => '---')); ?></td>
			</tr>
			<tr>
				<th>開始日(from)</th>
				<td><?php echo $this->element('selectDatetime', $applyTermFromOptions); ?></td>
			</tr>
			<tr>
				<th>終了日(to)</th>
				<td><?php echo $this->element('selectDatetime', $applyTermToOptions); ?></td>
			</tr>
			<tr>
				<th>対象日</th>
				<td><?php echo $this->element('selectDatetime', $selectDateOptions); ?></td>
			</tr>
			<tr>
				<th>対象地域</th>
				<td>
					<?php 
						echo $this->Form->input('prefecture_id', array('div' => false, 'label' => false, 'options' => $allPrefectureList, 'empty' => '---')); 
					?>
				</td>
			</tr>
			<tr>
				<th>公開/非公開</th>
				<td><?php echo $this->Form->input('is_published', array('options' => $isPublishedOptions, 'div' => false, 'label' => false, 'empty' => '---')); ?></td>
			</tr>
		</table>
		<br />

		<div>
			<?php
				echo $this->Form->submit('検索する', ['class'=>'btn btn-primary', 'div' => false,]);
				echo $this->Form->button('リセット', array('type' => 'button', 'class' => 'btn btn-reset'));
			?>
		</div>
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
				<th><?php echo $this->Paginator->sort('id', 'ID');?></th>
				<th><?php echo $this->Paginator->sort('client_id', 'クライアント名');?></th>
				<th>PRタイトル</th>
				<th><?php echo $this->Paginator->sort('space', '掲載枠');?></th>
				<th>開始日</th>
				<th>終了日</th>
				<th>対象地域</th>
				<th>手数料</th>
				<th class="actions"><?php echo $this->Html->link('新規追加','add/',array('class'=>'btn btn-success')); ?></th>
			</tr>
			<?php
			foreach ($recommends as $recommend) {
				$class = ($recommend['Recommend']['is_published'] == 0)?'gray':'';
			?>
			<tr id="<?php echo $recommend['Recommend']['id'];?>" class="ui-state <?php echo $class ?>" >
				<td><?php echo h($recommend['Recommend']['id']); ?></td>
				<td><?php echo h($recommend['Client']['name']); ?></td>
				<td><?php echo h($recommend['Recommend']['pr_title']); ?></td>
				<td><?php echo h($spaceOptions[$recommend['Recommend']['space']]); ?></td>
				<td><?php echo h(date('Y-m-d', strtotime($recommend['Recommend']['apply_term_from']))); ?></td>
				<td><?php echo h(date('Y-m-d', strtotime($recommend['Recommend']['apply_term_to']))); ?></td>
				<td style=' overflow: hidden; text-overflow: ellipsis;  white-space: nowrap; max-width:80px;'>
					<?php 
						$prefectureLabel = '';
						foreach ($recommend['RecommendPrefecture'] as $val) {
							$prefectureLabel .= $allPrefectureList[$val['RecommendPrefecture']['prefecture_id']].'、';
						}
						echo rtrim($prefectureLabel, '、');
					?>
				</td>
				<td><?php echo h($recommend['Recommend']['recommend_fee'].$recommendFeeUnit[$recommend['Recommend']['recommend_fee_unit']].'（'.Constant::isInternalTax()[$recommend['Recommend']['is_internal_tax']].'）'); ?></td>
				<td class="actions">
					<?php echo $this->Html->link('編集', array('action' => 'edit', $recommend['Recommend']['id']),array('class'=>'btn btn-warning')); ?>
					<?php echo $this->Form->postLink('削除', array('action' => 'delete', $recommend['Recommend']['id']), array('class'=>'btn btn-danger'), __('「%s」を削除しますか?', $recommend['Recommend']['id'])); ?>
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
