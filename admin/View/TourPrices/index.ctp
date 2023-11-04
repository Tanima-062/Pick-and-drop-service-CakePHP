<div class="tourprices index">
	<h3>募集型料金一覧</h3>
	<?php echo $this->Form->create('TourPrice', array('type' => 'get', 'inputDefaults' => array('label' => false))); ?>
		<table class="table-bordered table-condensed">
			<tr>
				<th>利用空港</th>
				<td><?php echo $this->Form->input('iata_cd', array('div' => false, 'label' => false, 'options' => $airportList, 'empty' => '---')); ?></td>
			</tr>
			<tr>
				<th>販売日</th>
				<td><?php echo $this->element('selectDatetime', $dtOptions); ?></td>
			</tr>
			<tr>
				<th>乗車人数</th>
				<td><?php echo $this->Form->input('passenger_count', array('div' => false, 'label' => false, 'options' => $passengerOptions, 'empty' => '---')); ?></td>
			</tr>
			<tr>
				<th>公開/非公開</th>
				<td><?php echo $this->Form->input('delete_flg', array('class' => 'span2', 'options' => $deleteFlgOptions, 'div' => false, 'label' => false, 'empty' => '--')); ?></td>
			</tr>
		</table>
		<br />
		<div style="padding:0px 10px 0px 0px">
			<?php
				echo $this->Form->submit('検索する', array('class' => 'btn btn-primary', 'div' => false));
				echo $this->Form->button('リセット', array('type' => 'button', 'class' => 'btn btn-reset'));
			?>
		</div>
	<?php echo $this->Form->end(); ?>
	<?php echo $this->Form->create('TourPrice', array('action' => 'unpublish', 'inputDefaults' => array('label' => false, 'hiddenField' => false))); ?>
		<?php echo $this->Form->submit('チェックした行を非公開にする', array('id' => 'unpublish_btn', 'class' => 'btn btn-danger')); ?>
		<div class="pagination pagination-right">
			<ul>
				<li><?php echo $this->Paginator->prev('< 前へ', array(), null, array('class' => 'prev disabled')); ?></li>
				<li><?php echo $this->Paginator->numbers(); ?></li>
				<li><?php echo $this->Paginator->next('次へ >', array(), null, array('class' => 'next disabled')); ?></li>
			</ul>
		</div>
		<table class="table table-bordered">
			<tr>
				<th><?php echo $this->Form->input('check-all', array('type' => 'checkbox', 'id' => 'check-all', 'class' => 'check-all', 'value' => 'all')); ?></th>
				<th><?php echo $this->Paginator->sort('id'); ?></th>
				<th><?php echo $this->Paginator->sort('iata_cd', '利用空港'); ?></th>
				<th><?php echo $this->Paginator->sort('date_from', '販売開始'); ?></th>
				<th><?php echo $this->Paginator->sort('date_to', '販売終了'); ?></th>
				<th><?php echo $this->Paginator->sort('time_start', '営業開始'); ?></th>
				<th><?php echo $this->Paginator->sort('time_end', '営業終了'); ?></th>
				<th><?php echo $this->Paginator->sort('passenger_count', '乗車人数'); ?></th>
				<th><?php echo $this->Paginator->sort('tour_car_type_id', '車両クラス'); ?></th>
				<th><?php echo $this->Paginator->sort('tour_car_example', '車種(例)'); ?></th>
				<th><?php echo $this->Paginator->sort('price', '販売単価'); ?></th>
				<th><?php echo $this->Paginator->sort('staff_id', '更新者'); ?></th>
				<th><?php echo $this->Paginator->sort('modified', '更新日時'); ?></th>
				<th class="actions">
					<?php echo $this->Html->link('料金追加', array('action' => 'add'), array('class' => 'btn btn-success')); ?>
					<?php echo $this->Html->link('インポート', array('action' => 'import'), array('class' => 'btn btn-primary')); ?>
				</th>
			</tr>
		<?php
			foreach ($prices as $price):
				$class = '';
				if (!empty($price['TourPrice']['delete_flg'])) {
					$class = 'gray';
				}
		?>
			<tr class="<?php echo $class;?>">
				<td>
					<?php
						$options = array('type' => 'checkbox', 'name' => 'check[]', 'class' => 'check-id', 'value' => $price['TourPrice']['id']);
						if (!empty($price['TourPrice']['delete_flg'])) {
							$options['disabled'] = 'disabled';
						}
						echo $this->Form->input('check'.$price['TourPrice']['id'], $options);
					?>
				</td>
				<td><?php echo h($price['TourPrice']['id']); ?></td>
				<td><?php echo h($price['TourPrice']['iata_cd']); ?></td>
				<td><?php echo h(date('Y-m-d', strtotime($price['TourPrice']['date_from']))); ?></td>
				<td><?php echo h(date('Y-m-d', strtotime($price['TourPrice']['date_to']))); ?></td>
				<td><?php echo h(date('H:i', strtotime(date('Y-m-d').' '.$price['TourPrice']['time_start']))); ?></td>
				<td><?php echo h(date('H:i', strtotime(date('Y-m-d').' '.$price['TourPrice']['time_end']))); ?></td>
				<td><?php echo h($price['TourPrice']['passenger_count']); ?>人</td>
				<td><?php echo h($price['TourPrice']['tour_car_type_name']); ?></td>
				<td><?php echo h($price['TourPrice']['tour_car_example']); ?></td>
				<td><?php echo h(number_format($price['TourPrice']['price'])); ?>円</td>
				<td><?php echo h($price['Staff']['name']); ?></td>
				<td><?php echo h($price['TourPrice']['modified']); ?></td>
				<td class="actions">
					<?php echo $this->Html->link('編集', array('action' => 'edit', $price['TourPrice']['id']), array('class' => 'btn btn-warning')); ?>
				</td>
			</tr>
		<?php endforeach; ?>
		</table>

		<div class="pagination pagination-right">
			<ul>
				<li><?php echo $this->Paginator->prev('< 前へ', array(), null, array('class' => 'prev disabled')); ?></li>
				<li><?php echo $this->Paginator->numbers(); ?></li>
				<li><?php echo $this->Paginator->next('次へ >', array(), null, array('class' => 'next disabled')); ?></li>
			</ul>
		</div>
	<?php echo $this->Form->end(); ?>
</div>
<script>
    $(function () {
        $('#check-all').on('change', function () {
            if ($(this).is(':checked')) {
                $('.check-id:enabled').prop('checked', true);
            } else {
                $('.check-id:enabled').prop('checked', false);
            }
        });
        $('#unpublish_btn').on('click', function (e) {
            if ($('.check-id:checked').length == 0) {
                e.preventDefault();
                return;
            }
            if (!window.confirm('選択された料金を非公開にしてもよろしいですか？')) {
                e.preventDefault();
                return;
            }
        });
    });
</script>
