<div class="prefectures view">
<h2><?php  echo __('Prefecture'); ?></h2>
	<dl>
		<dt><?php echo __('Id'); ?></dt>
		<dd>
			<?php echo h($prefecture['Prefecture']['id']); ?>
			&nbsp;
		</dd>
		<dt><?php echo __('Name'); ?></dt>
		<dd>
			<?php echo h($prefecture['Prefecture']['name']); ?>
			&nbsp;
		</dd>
		<dt><?php echo __('Created'); ?></dt>
		<dd>
			<?php echo h($prefecture['Prefecture']['created']); ?>
			&nbsp;
		</dd>
		<dt><?php echo __('Modified'); ?></dt>
		<dd>
			<?php echo h($prefecture['Prefecture']['modified']); ?>
			&nbsp;
		</dd>
		<dt><?php echo __('Staff'); ?></dt>
		<dd>
			<?php echo $this->Html->link($prefecture['Staff']['name'], array('controller' => 'staffs', 'action' => 'view', $prefecture['Staff']['id'])); ?>
			&nbsp;
		</dd>
		<dt><?php echo __('Delete Flg'); ?></dt>
		<dd>
			<?php echo h($prefecture['Prefecture']['delete_flg']); ?>
			&nbsp;
		</dd>
	</dl>
</div>

<div class="related">
	<h3><?php echo __('Related Reservations'); ?></h3>
	<?php if (!empty($prefecture['Reservation'])): ?>
	<table>
	<tr>
		<th><?php echo __('Id'); ?></th>
		<th><?php echo __('Client Id'); ?></th>
		<th><?php echo __('Session Id'); ?></th>
		<th><?php echo __('Reservation Datetime'); ?></th>
		<th><?php echo __('Reservation Status Id'); ?></th>
		<th><?php echo __('Reservation Key Id'); ?></th>
		<th><?php echo __('Estimate Id'); ?></th>
		<th><?php echo __('Commodity Id'); ?></th>
		<th><?php echo __('Car Class Id'); ?></th>
		<th><?php echo __('Rent Datetime'); ?></th>
		<th><?php echo __('Rent Office Id'); ?></th>
		<th><?php echo __('Rent Hotel Name'); ?></th>
		<th><?php echo __('Return Datetime'); ?></th>
		<th><?php echo __('Return Office Id'); ?></th>
		<th><?php echo __('Return Hotel Name'); ?></th>
		<th><?php echo __('Adults Count'); ?></th>
		<th><?php echo __('Children Count'); ?></th>
		<th><?php echo __('Price Span Id'); ?></th>
		<th><?php echo __('Span Count'); ?></th>
		<th><?php echo __('Cars Count'); ?></th>
		<th><?php echo __('Total Price'); ?></th>
		<th><?php echo __('Total Tax'); ?></th>
		<th><?php echo __('Amount'); ?></th>
		<th><?php echo __('Commodity Json'); ?></th>
		<th><?php echo __('Last Name'); ?></th>
		<th><?php echo __('First Name'); ?></th>
		<th><?php echo __('Email'); ?></th>
		<th><?php echo __('Tel'); ?></th>
		<th><?php echo __('Prefecture Id'); ?></th>
		<th><?php echo __('Arrival Airline Id'); ?></th>
		<th><?php echo __('Arrival Flight Number'); ?></th>
		<th><?php echo __('Cancel Datetime'); ?></th>
		<th><?php echo __('Cancel Contact Method Id'); ?></th>
		<th><?php echo __('Cancel Staff Id'); ?></th>
		<th><?php echo __('Cancel Remark'); ?></th>
		<th><?php echo __('Created'); ?></th>
		<th><?php echo __('Modified'); ?></th>
		<th><?php echo __('Staff Id'); ?></th>
		<th><?php echo __('Delete Flg'); ?></th>
		<th class="actions"><?php echo __('Actions'); ?></th>
	</tr>
	<?php
		$i = 0;
		foreach ($prefecture['Reservation'] as $reservation): ?>
		<tr>
			<td><?php echo $reservation['id']; ?></td>
			<td><?php echo $reservation['client_id']; ?></td>
			<td><?php echo $reservation['session_id']; ?></td>
			<td><?php echo $reservation['reservation_datetime']; ?></td>
			<td><?php echo $reservation['reservation_status_id']; ?></td>
			<td><?php echo $reservation['reservation_key_id']; ?></td>
			<td><?php echo $reservation['estimate_id']; ?></td>
			<td><?php echo $reservation['commodity_id']; ?></td>
			<td><?php echo $reservation['car_class_id']; ?></td>
			<td><?php echo $reservation['rent_datetime']; ?></td>
			<td><?php echo $reservation['rent_office_id']; ?></td>
			<td><?php echo $reservation['rent_hotel_name']; ?></td>
			<td><?php echo $reservation['return_datetime']; ?></td>
			<td><?php echo $reservation['return_office_id']; ?></td>
			<td><?php echo $reservation['return_hotel_name']; ?></td>
			<td><?php echo $reservation['adults_count']; ?></td>
			<td><?php echo $reservation['children_count']; ?></td>
			<td><?php echo $reservation['price_span_id']; ?></td>
			<td><?php echo $reservation['span_count']; ?></td>
			<td><?php echo $reservation['cars_count']; ?></td>
			<td><?php echo $reservation['total_price']; ?></td>
			<td><?php echo $reservation['total_tax']; ?></td>
			<td><?php echo $reservation['amount']; ?></td>
			<td><?php echo $reservation['commodity_json']; ?></td>
			<td><?php echo $reservation['last_name']; ?></td>
			<td><?php echo $reservation['first_name']; ?></td>
			<td><?php echo $reservation['email']; ?></td>
			<td><?php echo $reservation['tel']; ?></td>
			<td><?php echo $reservation['prefecture_id']; ?></td>
			<td><?php echo $reservation['arrival_airline_id']; ?></td>
			<td><?php echo $reservation['arrival_flight_number']; ?></td>
			<td><?php echo $reservation['cancel_datetime']; ?></td>
			<td><?php echo $reservation['cancel_contact_method_id']; ?></td>
			<td><?php echo $reservation['cancel_staff_id']; ?></td>
			<td><?php echo $reservation['cancel_remark']; ?></td>
			<td><?php echo $reservation['created']; ?></td>
			<td><?php echo $reservation['modified']; ?></td>
			<td><?php echo $reservation['staff_id']; ?></td>
			<td><?php echo $reservation['delete_flg']; ?></td>
			<td class="actions">
				<?php echo $this->Html->link(__('View'), array('controller' => 'reservations', 'action' => 'view', $reservation['id'])); ?>
				<?php echo $this->Html->link(__('Edit'), array('controller' => 'reservations', 'action' => 'edit', $reservation['id'])); ?>
				<?php echo $this->Form->postLink(__('Delete'), array('controller' => 'reservations', 'action' => 'delete', $reservation['id']), null, __('Are you sure you want to delete # %s?', $reservation['id'])); ?>
			</td>
		</tr>
	<?php endforeach; ?>
	</table>
<?php endif; ?>


</div>
