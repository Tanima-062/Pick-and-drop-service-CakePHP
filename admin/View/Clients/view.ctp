<div class="clients view">
<h2><?php  echo __('Client'); ?></h2>
	<dl>
		<dt><?php echo __('Id'); ?></dt>
		<dd>
			<?php echo h($client['Client']['id']); ?>
			&nbsp;
		</dd>
		<dt><?php echo __('Name'); ?></dt>
		<dd>
			<?php echo h($client['Client']['name']); ?>
			&nbsp;
		</dd>
		<dt><?php echo __('Is Admin'); ?></dt>
		<dd>
			<?php echo h($client['Client']['is_admin']); ?>
			&nbsp;
		</dd>
		<dt><?php echo __('Need Remark'); ?></dt>
		<dd>
			<?php echo h($client['Client']['need_remark']); ?>
			&nbsp;
		</dd>
		<dt><?php echo __('Cancel Policy'); ?></dt>
		<dd>
			<?php echo h($client['Client']['cancel_policy']); ?>
			&nbsp;
		</dd>
		<dt><?php echo __('Accept Cash'); ?></dt>
		<dd>
			<?php echo h($client['Client']['accept_cash']); ?>
			&nbsp;
		</dd>
		<dt><?php echo __('Accept Card'); ?></dt>
		<dd>
			<?php echo h($client['Client']['accept_card']); ?>
			&nbsp;
		</dd>
		<dt><?php echo __('Precautions'); ?></dt>
		<dd>
			<?php echo h($client['Client']['precautions']); ?>
			&nbsp;
		</dd>
		<dt><?php echo __('Reservation Content'); ?></dt>
		<dd>
			<?php echo h($client['Client']['reservation_content']); ?>
			&nbsp;
		</dd>
		<dt><?php echo __('Seo'); ?></dt>
		<dd>
			<?php echo h($client['Client']['seo']); ?>
			&nbsp;
		</dd>
		<dt><?php echo __('Reserve Tag'); ?></dt>
		<dd>
			<?php echo h($client['Client']['reserve_tag']); ?>
			&nbsp;
		</dd>
		<dt><?php echo __('Client Image'); ?></dt>
		<dd>
			<?php echo h($client['Client']['client_image']); ?>
			&nbsp;
		</dd>
		<dt><?php echo __('Client Image Comment'); ?></dt>
		<dd>
			<?php echo h($client['Client']['client_image_comment']); ?>
			&nbsp;
		</dd>
		<dt><?php echo __('Clause Pdf'); ?></dt>
		<dd>
			<?php echo h($client['Client']['clause_pdf']); ?>
			&nbsp;
		</dd>
		<dt><?php echo __('Staff'); ?></dt>
		<dd>
			<?php echo $this->Html->link($client['Staff']['name'], array('controller' => 'staffs', 'action' => 'view', $client['Staff']['id'])); ?>
			&nbsp;
		</dd>
		<dt><?php echo __('Created'); ?></dt>
		<dd>
			<?php echo h($client['Client']['created']); ?>
			&nbsp;
		</dd>
		<dt><?php echo __('Modified'); ?></dt>
		<dd>
			<?php echo h($client['Client']['modified']); ?>
			&nbsp;
		</dd>
		<dt><?php echo __('Delete Flg'); ?></dt>
		<dd>
			<?php echo h($client['Client']['delete_flg']); ?>
			&nbsp;
		</dd>
		<dt><?php echo __('Deleted'); ?></dt>
		<dd>
			<?php echo h($client['Client']['deleted']); ?>
			&nbsp;
		</dd>
	</dl>
</div>
<div class="actions">
	<h3><?php echo __('Actions'); ?></h3>
	<ul>
		<li><?php echo $this->Html->link(__('Edit Client'), array('action' => 'edit', $client['Client']['id'])); ?> </li>
		<li><?php echo $this->Form->postLink(__('Delete Client'), array('action' => 'delete', $client['Client']['id']), null, __('Are you sure you want to delete # %s?', $client['Client']['id'])); ?> </li>
		<li><?php echo $this->Html->link(__('List Clients'), array('action' => 'index')); ?> </li>
		<li><?php echo $this->Html->link(__('New Client'), array('action' => 'add')); ?> </li>
		<li><?php echo $this->Html->link(__('List Staffs'), array('controller' => 'staffs', 'action' => 'index')); ?> </li>
		<li><?php echo $this->Html->link(__('New Staff'), array('controller' => 'staffs', 'action' => 'add')); ?> </li>
		<li><?php echo $this->Html->link(__('List Campaigns'), array('controller' => 'campaigns', 'action' => 'index')); ?> </li>
		<li><?php echo $this->Html->link(__('New Campaign'), array('controller' => 'campaigns', 'action' => 'add')); ?> </li>
		<li><?php echo $this->Html->link(__('List Car Class Reservations'), array('controller' => 'car_class_reservations', 'action' => 'index')); ?> </li>
		<li><?php echo $this->Html->link(__('New Car Class Reservation'), array('controller' => 'car_class_reservations', 'action' => 'add')); ?> </li>
		<li><?php echo $this->Html->link(__('List Car Class Stocks'), array('controller' => 'car_class_stocks', 'action' => 'index')); ?> </li>
		<li><?php echo $this->Html->link(__('New Car Class Stock'), array('controller' => 'car_class_stocks', 'action' => 'add')); ?> </li>
		<li><?php echo $this->Html->link(__('List Car Classes'), array('controller' => 'car_classes', 'action' => 'index')); ?> </li>
		<li><?php echo $this->Html->link(__('New Car Class'), array('controller' => 'car_classes', 'action' => 'add')); ?> </li>
		<li><?php echo $this->Html->link(__('List Child Sheet Prices'), array('controller' => 'child_sheet_prices', 'action' => 'index')); ?> </li>
		<li><?php echo $this->Html->link(__('New Child Sheet Price'), array('controller' => 'child_sheet_prices', 'action' => 'add')); ?> </li>
		<li><?php echo $this->Html->link(__('List Client Car Model Sorts'), array('controller' => 'client_car_model_sorts', 'action' => 'index')); ?> </li>
		<li><?php echo $this->Html->link(__('New Client Car Model Sort'), array('controller' => 'client_car_model_sorts', 'action' => 'add')); ?> </li>
		<li><?php echo $this->Html->link(__('List Client Car Models'), array('controller' => 'client_car_models', 'action' => 'index')); ?> </li>
		<li><?php echo $this->Html->link(__('New Client Car Model'), array('controller' => 'client_car_models', 'action' => 'add')); ?> </li>
		<li><?php echo $this->Html->link(__('List Client Cards'), array('controller' => 'client_cards', 'action' => 'index')); ?> </li>
		<li><?php echo $this->Html->link(__('New Client Card'), array('controller' => 'client_cards', 'action' => 'add')); ?> </li>
		<li><?php echo $this->Html->link(__('List Client Emails'), array('controller' => 'client_emails', 'action' => 'index')); ?> </li>
		<li><?php echo $this->Html->link(__('New Client Email'), array('controller' => 'client_emails', 'action' => 'add')); ?> </li>
		<li><?php echo $this->Html->link(__('List Client Images'), array('controller' => 'client_images', 'action' => 'index')); ?> </li>
		<li><?php echo $this->Html->link(__('New Client Image'), array('controller' => 'client_images', 'action' => 'add')); ?> </li>
		<li><?php echo $this->Html->link(__('List Client Photos Introductions'), array('controller' => 'client_photos_introductions', 'action' => 'index')); ?> </li>
		<li><?php echo $this->Html->link(__('New Client Photos Introduction'), array('controller' => 'client_photos_introductions', 'action' => 'add')); ?> </li>
		<li><?php echo $this->Html->link(__('List Client Review Star Averages'), array('controller' => 'client_review_star_averages', 'action' => 'index')); ?> </li>
		<li><?php echo $this->Html->link(__('New Client Review Star Average'), array('controller' => 'client_review_star_averages', 'action' => 'add')); ?> </li>
		<li><?php echo $this->Html->link(__('List Client Sorts'), array('controller' => 'client_sorts', 'action' => 'index')); ?> </li>
		<li><?php echo $this->Html->link(__('New Client Sort'), array('controller' => 'client_sorts', 'action' => 'add')); ?> </li>
		<li><?php echo $this->Html->link(__('List Client Templates'), array('controller' => 'client_templates', 'action' => 'index')); ?> </li>
		<li><?php echo $this->Html->link(__('New Client Template'), array('controller' => 'client_templates', 'action' => 'add')); ?> </li>
		<li><?php echo $this->Html->link(__('List Commodities'), array('controller' => 'commodities', 'action' => 'index')); ?> </li>
		<li><?php echo $this->Html->link(__('New Commodity'), array('controller' => 'commodities', 'action' => 'add')); ?> </li>
		<li><?php echo $this->Html->link(__('List Commodity Equipments'), array('controller' => 'commodity_equipments', 'action' => 'index')); ?> </li>
		<li><?php echo $this->Html->link(__('New Commodity Equipment'), array('controller' => 'commodity_equipments', 'action' => 'add')); ?> </li>
		<li><?php echo $this->Html->link(__('List Commodity Free Child Sheets'), array('controller' => 'commodity_free_child_sheets', 'action' => 'index')); ?> </li>
		<li><?php echo $this->Html->link(__('New Commodity Free Child Sheet'), array('controller' => 'commodity_free_child_sheets', 'action' => 'add')); ?> </li>
		<li><?php echo $this->Html->link(__('List Commodity Groups'), array('controller' => 'commodity_groups', 'action' => 'index')); ?> </li>
		<li><?php echo $this->Html->link(__('New Commodity Group'), array('controller' => 'commodity_groups', 'action' => 'add')); ?> </li>
		<li><?php echo $this->Html->link(__('List Commodity Images'), array('controller' => 'commodity_images', 'action' => 'index')); ?> </li>
		<li><?php echo $this->Html->link(__('New Commodity Image'), array('controller' => 'commodity_images', 'action' => 'add')); ?> </li>
		<li><?php echo $this->Html->link(__('List Commodity Item Reservations'), array('controller' => 'commodity_item_reservations', 'action' => 'index')); ?> </li>
		<li><?php echo $this->Html->link(__('New Commodity Item Reservation'), array('controller' => 'commodity_item_reservations', 'action' => 'add')); ?> </li>
		<li><?php echo $this->Html->link(__('List Commodity Item Stocks'), array('controller' => 'commodity_item_stocks', 'action' => 'index')); ?> </li>
		<li><?php echo $this->Html->link(__('New Commodity Item Stock'), array('controller' => 'commodity_item_stocks', 'action' => 'add')); ?> </li>
		<li><?php echo $this->Html->link(__('List Commodity Items'), array('controller' => 'commodity_items', 'action' => 'index')); ?> </li>
		<li><?php echo $this->Html->link(__('New Commodity Item'), array('controller' => 'commodity_items', 'action' => 'add')); ?> </li>
		<li><?php echo $this->Html->link(__('List Commodity Prices'), array('controller' => 'commodity_prices', 'action' => 'index')); ?> </li>
		<li><?php echo $this->Html->link(__('New Commodity Price'), array('controller' => 'commodity_prices', 'action' => 'add')); ?> </li>
		<li><?php echo $this->Html->link(__('List Commodity Privileges'), array('controller' => 'commodity_privileges', 'action' => 'index')); ?> </li>
		<li><?php echo $this->Html->link(__('New Commodity Privilege'), array('controller' => 'commodity_privileges', 'action' => 'add')); ?> </li>
		<li><?php echo $this->Html->link(__('List Commodity Rankings'), array('controller' => 'commodity_rankings', 'action' => 'index')); ?> </li>
		<li><?php echo $this->Html->link(__('New Commodity Ranking'), array('controller' => 'commodity_rankings', 'action' => 'add')); ?> </li>
		<li><?php echo $this->Html->link(__('List Commodity Rent Offices'), array('controller' => 'commodity_rent_offices', 'action' => 'index')); ?> </li>
		<li><?php echo $this->Html->link(__('New Commodity Rent Office'), array('controller' => 'commodity_rent_offices', 'action' => 'add')); ?> </li>
		<li><?php echo $this->Html->link(__('List Commodity Return Offices'), array('controller' => 'commodity_return_offices', 'action' => 'index')); ?> </li>
		<li><?php echo $this->Html->link(__('New Commodity Return Office'), array('controller' => 'commodity_return_offices', 'action' => 'add')); ?> </li>
		<li><?php echo $this->Html->link(__('List Commodity Services'), array('controller' => 'commodity_services', 'action' => 'index')); ?> </li>
		<li><?php echo $this->Html->link(__('New Commodity Service'), array('controller' => 'commodity_services', 'action' => 'add')); ?> </li>
		<li><?php echo $this->Html->link(__('List Commodity Specials'), array('controller' => 'commodity_specials', 'action' => 'index')); ?> </li>
		<li><?php echo $this->Html->link(__('New Commodity Special'), array('controller' => 'commodity_specials', 'action' => 'add')); ?> </li>
		<li><?php echo $this->Html->link(__('List Commodity Terms'), array('controller' => 'commodity_terms', 'action' => 'index')); ?> </li>
		<li><?php echo $this->Html->link(__('New Commodity Term'), array('controller' => 'commodity_terms', 'action' => 'add')); ?> </li>
		<li><?php echo $this->Html->link(__('List Contracts'), array('controller' => 'contracts', 'action' => 'index')); ?> </li>
		<li><?php echo $this->Html->link(__('New Contract'), array('controller' => 'contracts', 'action' => 'add')); ?> </li>
		<li><?php echo $this->Html->link(__('List Drop Off Area Rates'), array('controller' => 'drop_off_area_rates', 'action' => 'index')); ?> </li>
		<li><?php echo $this->Html->link(__('New Drop Off Area Rate'), array('controller' => 'drop_off_area_rates', 'action' => 'add')); ?> </li>
		<li><?php echo $this->Html->link(__('List Drop Off Areas'), array('controller' => 'drop_off_areas', 'action' => 'index')); ?> </li>
		<li><?php echo $this->Html->link(__('New Drop Off Area'), array('controller' => 'drop_off_areas', 'action' => 'add')); ?> </li>
		<li><?php echo $this->Html->link(__('List Estimates'), array('controller' => 'estimates', 'action' => 'index')); ?> </li>
		<li><?php echo $this->Html->link(__('New Estimate'), array('controller' => 'estimates', 'action' => 'add')); ?> </li>
		<li><?php echo $this->Html->link(__('List Hotel Pickups'), array('controller' => 'hotel_pickups', 'action' => 'index')); ?> </li>
		<li><?php echo $this->Html->link(__('New Hotel Pickup'), array('controller' => 'hotel_pickups', 'action' => 'add')); ?> </li>
		<li><?php echo $this->Html->link(__('List Hotels'), array('controller' => 'hotels', 'action' => 'index')); ?> </li>
		<li><?php echo $this->Html->link(__('New Hotel'), array('controller' => 'hotels', 'action' => 'add')); ?> </li>
		<li><?php echo $this->Html->link(__('List Late Night Fees'), array('controller' => 'late_night_fees', 'action' => 'index')); ?> </li>
		<li><?php echo $this->Html->link(__('New Late Night Fee'), array('controller' => 'late_night_fees', 'action' => 'add')); ?> </li>
		<li><?php echo $this->Html->link(__('List Office Areas'), array('controller' => 'office_areas', 'action' => 'index')); ?> </li>
		<li><?php echo $this->Html->link(__('New Office Area'), array('controller' => 'office_areas', 'action' => 'add')); ?> </li>
		<li><?php echo $this->Html->link(__('List Office Guides'), array('controller' => 'office_guides', 'action' => 'index')); ?> </li>
		<li><?php echo $this->Html->link(__('New Office Guide'), array('controller' => 'office_guides', 'action' => 'add')); ?> </li>
		<li><?php echo $this->Html->link(__('List Office Photos Introductions'), array('controller' => 'office_photos_introductions', 'action' => 'index')); ?> </li>
		<li><?php echo $this->Html->link(__('New Office Photos Introduction'), array('controller' => 'office_photos_introductions', 'action' => 'add')); ?> </li>
		<li><?php echo $this->Html->link(__('List Office Review Star Averages'), array('controller' => 'office_review_star_averages', 'action' => 'index')); ?> </li>
		<li><?php echo $this->Html->link(__('New Office Review Star Average'), array('controller' => 'office_review_star_averages', 'action' => 'add')); ?> </li>
		<li><?php echo $this->Html->link(__('List Office Reviews'), array('controller' => 'office_reviews', 'action' => 'index')); ?> </li>
		<li><?php echo $this->Html->link(__('New Office Review'), array('controller' => 'office_reviews', 'action' => 'add')); ?> </li>
		<li><?php echo $this->Html->link(__('List Office Stock Groups'), array('controller' => 'office_stock_groups', 'action' => 'index')); ?> </li>
		<li><?php echo $this->Html->link(__('New Office Stock Group'), array('controller' => 'office_stock_groups', 'action' => 'add')); ?> </li>
		<li><?php echo $this->Html->link(__('List Offices'), array('controller' => 'offices', 'action' => 'index')); ?> </li>
		<li><?php echo $this->Html->link(__('New Office'), array('controller' => 'offices', 'action' => 'add')); ?> </li>
		<li><?php echo $this->Html->link(__('List Price Rank Calendars'), array('controller' => 'price_rank_calendars', 'action' => 'index')); ?> </li>
		<li><?php echo $this->Html->link(__('New Price Rank Calendar'), array('controller' => 'price_rank_calendars', 'action' => 'add')); ?> </li>
		<li><?php echo $this->Html->link(__('List Price Ranks'), array('controller' => 'price_ranks', 'action' => 'index')); ?> </li>
		<li><?php echo $this->Html->link(__('New Price Rank'), array('controller' => 'price_ranks', 'action' => 'add')); ?> </li>
		<li><?php echo $this->Html->link(__('List Privilege Prices'), array('controller' => 'privilege_prices', 'action' => 'index')); ?> </li>
		<li><?php echo $this->Html->link(__('New Privilege Price'), array('controller' => 'privilege_prices', 'action' => 'add')); ?> </li>
		<li><?php echo $this->Html->link(__('List Privileges'), array('controller' => 'privileges', 'action' => 'index')); ?> </li>
		<li><?php echo $this->Html->link(__('New Privilege'), array('controller' => 'privileges', 'action' => 'add')); ?> </li>
		<li><?php echo $this->Html->link(__('List Reservations'), array('controller' => 'reservations', 'action' => 'index')); ?> </li>
		<li><?php echo $this->Html->link(__('New Reservation'), array('controller' => 'reservations', 'action' => 'add')); ?> </li>
		<li><?php echo $this->Html->link(__('List Reviews'), array('controller' => 'reviews', 'action' => 'index')); ?> </li>
		<li><?php echo $this->Html->link(__('New Review'), array('controller' => 'reviews', 'action' => 'add')); ?> </li>
		<li><?php echo $this->Html->link(__('List Statistics'), array('controller' => 'statistics', 'action' => 'index')); ?> </li>
		<li><?php echo $this->Html->link(__('New Statistic'), array('controller' => 'statistics', 'action' => 'add')); ?> </li>
		<li><?php echo $this->Html->link(__('List Stock Groups'), array('controller' => 'stock_groups', 'action' => 'index')); ?> </li>
		<li><?php echo $this->Html->link(__('New Stock Group'), array('controller' => 'stock_groups', 'action' => 'add')); ?> </li>
		<li><?php echo $this->Html->link(__('List Updated Tables'), array('controller' => 'updated_tables', 'action' => 'index')); ?> </li>
		<li><?php echo $this->Html->link(__('New Updated Table'), array('controller' => 'updated_tables', 'action' => 'add')); ?> </li>
	</ul>
</div>
<div class="related">
	<h3><?php echo __('Related Campaigns'); ?></h3>
	<?php if (!empty($client['Campaign'])): ?>
	<table cellpadding = "0" cellspacing = "0">
	<tr>
		<th><?php echo __('Id'); ?></th>
		<th><?php echo __('Client Id'); ?></th>
		<th><?php echo __('Commodity Id'); ?></th>
		<th><?php echo __('Rank Date From'); ?></th>
		<th><?php echo __('Rank Date To'); ?></th>
		<th><?php echo __('Price Rank Id'); ?></th>
		<th><?php echo __('Staff Id'); ?></th>
		<th><?php echo __('Created'); ?></th>
		<th><?php echo __('Modified'); ?></th>
		<th><?php echo __('Delete Flg'); ?></th>
		<th><?php echo __('Deleted'); ?></th>
		<th class="actions"><?php echo __('Actions'); ?></th>
	</tr>
	<?php
		$i = 0;
		foreach ($client['Campaign'] as $campaign): ?>
		<tr>
			<td><?php echo $campaign['id']; ?></td>
			<td><?php echo $campaign['client_id']; ?></td>
			<td><?php echo $campaign['commodity_id']; ?></td>
			<td><?php echo $campaign['rank_date_from']; ?></td>
			<td><?php echo $campaign['rank_date_to']; ?></td>
			<td><?php echo $campaign['price_rank_id']; ?></td>
			<td><?php echo $campaign['staff_id']; ?></td>
			<td><?php echo $campaign['created']; ?></td>
			<td><?php echo $campaign['modified']; ?></td>
			<td><?php echo $campaign['delete_flg']; ?></td>
			<td><?php echo $campaign['deleted']; ?></td>
			<td class="actions">
				<?php echo $this->Html->link(__('View'), array('controller' => 'campaigns', 'action' => 'view', $campaign['id'])); ?>
				<?php echo $this->Html->link(__('Edit'), array('controller' => 'campaigns', 'action' => 'edit', $campaign['id'])); ?>
				<?php echo $this->Form->postLink(__('Delete'), array('controller' => 'campaigns', 'action' => 'delete', $campaign['id']), null, __('Are you sure you want to delete # %s?', $campaign['id'])); ?>
			</td>
		</tr>
	<?php endforeach; ?>
	</table>
<?php endif; ?>

	<div class="actions">
		<ul>
			<li><?php echo $this->Html->link(__('New Campaign'), array('controller' => 'campaigns', 'action' => 'add')); ?> </li>
		</ul>
	</div>
</div>
<div class="related">
	<h3><?php echo __('Related Car Class Reservations'); ?></h3>
	<?php if (!empty($client['CarClassReservation'])): ?>
	<table cellpadding = "0" cellspacing = "0">
	<tr>
		<th><?php echo __('Id'); ?></th>
		<th><?php echo __('Client Id'); ?></th>
		<th><?php echo __('Stock Group Id'); ?></th>
		<th><?php echo __('Car Class Id'); ?></th>
		<th><?php echo __('Stock Date'); ?></th>
		<th><?php echo __('Reservation Id'); ?></th>
		<th><?php echo __('Reservation Count'); ?></th>
		<th><?php echo __('Staff Id'); ?></th>
		<th><?php echo __('Created'); ?></th>
		<th><?php echo __('Modified'); ?></th>
		<th><?php echo __('Delete Flg'); ?></th>
		<th><?php echo __('Deleted'); ?></th>
		<th class="actions"><?php echo __('Actions'); ?></th>
	</tr>
	<?php
		$i = 0;
		foreach ($client['CarClassReservation'] as $carClassReservation): ?>
		<tr>
			<td><?php echo $carClassReservation['id']; ?></td>
			<td><?php echo $carClassReservation['client_id']; ?></td>
			<td><?php echo $carClassReservation['stock_group_id']; ?></td>
			<td><?php echo $carClassReservation['car_class_id']; ?></td>
			<td><?php echo $carClassReservation['stock_date']; ?></td>
			<td><?php echo $carClassReservation['reservation_id']; ?></td>
			<td><?php echo $carClassReservation['reservation_count']; ?></td>
			<td><?php echo $carClassReservation['staff_id']; ?></td>
			<td><?php echo $carClassReservation['created']; ?></td>
			<td><?php echo $carClassReservation['modified']; ?></td>
			<td><?php echo $carClassReservation['delete_flg']; ?></td>
			<td><?php echo $carClassReservation['deleted']; ?></td>
			<td class="actions">
				<?php echo $this->Html->link(__('View'), array('controller' => 'car_class_reservations', 'action' => 'view', $carClassReservation['id'])); ?>
				<?php echo $this->Html->link(__('Edit'), array('controller' => 'car_class_reservations', 'action' => 'edit', $carClassReservation['id'])); ?>
				<?php echo $this->Form->postLink(__('Delete'), array('controller' => 'car_class_reservations', 'action' => 'delete', $carClassReservation['id']), null, __('Are you sure you want to delete # %s?', $carClassReservation['id'])); ?>
			</td>
		</tr>
	<?php endforeach; ?>
	</table>
<?php endif; ?>

	<div class="actions">
		<ul>
			<li><?php echo $this->Html->link(__('New Car Class Reservation'), array('controller' => 'car_class_reservations', 'action' => 'add')); ?> </li>
		</ul>
	</div>
</div>
<div class="related">
	<h3><?php echo __('Related Car Class Stocks'); ?></h3>
	<?php if (!empty($client['CarClassStock'])): ?>
	<table cellpadding = "0" cellspacing = "0">
	<tr>
		<th><?php echo __('Id'); ?></th>
		<th><?php echo __('Client Id'); ?></th>
		<th><?php echo __('Stock Group Id'); ?></th>
		<th><?php echo __('Car Class Id'); ?></th>
		<th><?php echo __('Stock Date'); ?></th>
		<th><?php echo __('Stock Count'); ?></th>
		<th><?php echo __('Staff Id'); ?></th>
		<th><?php echo __('Created'); ?></th>
		<th><?php echo __('Modified'); ?></th>
		<th><?php echo __('Delete Flg'); ?></th>
		<th><?php echo __('Deleted'); ?></th>
		<th class="actions"><?php echo __('Actions'); ?></th>
	</tr>
	<?php
		$i = 0;
		foreach ($client['CarClassStock'] as $carClassStock): ?>
		<tr>
			<td><?php echo $carClassStock['id']; ?></td>
			<td><?php echo $carClassStock['client_id']; ?></td>
			<td><?php echo $carClassStock['stock_group_id']; ?></td>
			<td><?php echo $carClassStock['car_class_id']; ?></td>
			<td><?php echo $carClassStock['stock_date']; ?></td>
			<td><?php echo $carClassStock['stock_count']; ?></td>
			<td><?php echo $carClassStock['staff_id']; ?></td>
			<td><?php echo $carClassStock['created']; ?></td>
			<td><?php echo $carClassStock['modified']; ?></td>
			<td><?php echo $carClassStock['delete_flg']; ?></td>
			<td><?php echo $carClassStock['deleted']; ?></td>
			<td class="actions">
				<?php echo $this->Html->link(__('View'), array('controller' => 'car_class_stocks', 'action' => 'view', $carClassStock['id'])); ?>
				<?php echo $this->Html->link(__('Edit'), array('controller' => 'car_class_stocks', 'action' => 'edit', $carClassStock['id'])); ?>
				<?php echo $this->Form->postLink(__('Delete'), array('controller' => 'car_class_stocks', 'action' => 'delete', $carClassStock['id']), null, __('Are you sure you want to delete # %s?', $carClassStock['id'])); ?>
			</td>
		</tr>
	<?php endforeach; ?>
	</table>
<?php endif; ?>

	<div class="actions">
		<ul>
			<li><?php echo $this->Html->link(__('New Car Class Stock'), array('controller' => 'car_class_stocks', 'action' => 'add')); ?> </li>
		</ul>
	</div>
</div>
<div class="related">
	<h3><?php echo __('Related Car Classes'); ?></h3>
	<?php if (!empty($client['CarClass'])): ?>
	<table cellpadding = "0" cellspacing = "0">
	<tr>
		<th><?php echo __('Id'); ?></th>
		<th><?php echo __('Client Id'); ?></th>
		<th><?php echo __('Car Type Id'); ?></th>
		<th><?php echo __('Name'); ?></th>
		<th><?php echo __('Sort'); ?></th>
		<th><?php echo __('Staff Id'); ?></th>
		<th><?php echo __('Created'); ?></th>
		<th><?php echo __('Modified'); ?></th>
		<th><?php echo __('Delete Flg'); ?></th>
		<th><?php echo __('Deleted'); ?></th>
		<th class="actions"><?php echo __('Actions'); ?></th>
	</tr>
	<?php
		$i = 0;
		foreach ($client['CarClass'] as $carClass): ?>
		<tr>
			<td><?php echo $carClass['id']; ?></td>
			<td><?php echo $carClass['client_id']; ?></td>
			<td><?php echo $carClass['car_type_id']; ?></td>
			<td><?php echo $carClass['name']; ?></td>
			<td><?php echo $carClass['sort']; ?></td>
			<td><?php echo $carClass['staff_id']; ?></td>
			<td><?php echo $carClass['created']; ?></td>
			<td><?php echo $carClass['modified']; ?></td>
			<td><?php echo $carClass['delete_flg']; ?></td>
			<td><?php echo $carClass['deleted']; ?></td>
			<td class="actions">
				<?php echo $this->Html->link(__('View'), array('controller' => 'car_classes', 'action' => 'view', $carClass['id'])); ?>
				<?php echo $this->Html->link(__('Edit'), array('controller' => 'car_classes', 'action' => 'edit', $carClass['id'])); ?>
				<?php echo $this->Form->postLink(__('Delete'), array('controller' => 'car_classes', 'action' => 'delete', $carClass['id']), null, __('Are you sure you want to delete # %s?', $carClass['id'])); ?>
			</td>
		</tr>
	<?php endforeach; ?>
	</table>
<?php endif; ?>

	<div class="actions">
		<ul>
			<li><?php echo $this->Html->link(__('New Car Class'), array('controller' => 'car_classes', 'action' => 'add')); ?> </li>
		</ul>
	</div>
</div>
<div class="related">
	<h3><?php echo __('Related Child Sheet Prices'); ?></h3>
	<?php if (!empty($client['ChildSheetPrice'])): ?>
	<table cellpadding = "0" cellspacing = "0">
	<tr>
		<th><?php echo __('Id'); ?></th>
		<th><?php echo __('Client Id'); ?></th>
		<th><?php echo __('Child Sheet Id'); ?></th>
		<th><?php echo __('Excluding Tax'); ?></th>
		<th><?php echo __('Tax'); ?></th>
		<th><?php echo __('Price'); ?></th>
		<th><?php echo __('Staff Id'); ?></th>
		<th><?php echo __('Created'); ?></th>
		<th><?php echo __('Modified'); ?></th>
		<th><?php echo __('Delete Flg'); ?></th>
		<th><?php echo __('Deleted'); ?></th>
		<th class="actions"><?php echo __('Actions'); ?></th>
	</tr>
	<?php
		$i = 0;
		foreach ($client['ChildSheetPrice'] as $childSheetPrice): ?>
		<tr>
			<td><?php echo $childSheetPrice['id']; ?></td>
			<td><?php echo $childSheetPrice['client_id']; ?></td>
			<td><?php echo $childSheetPrice['child_sheet_id']; ?></td>
			<td><?php echo $childSheetPrice['excluding_tax']; ?></td>
			<td><?php echo $childSheetPrice['tax']; ?></td>
			<td><?php echo $childSheetPrice['price']; ?></td>
			<td><?php echo $childSheetPrice['staff_id']; ?></td>
			<td><?php echo $childSheetPrice['created']; ?></td>
			<td><?php echo $childSheetPrice['modified']; ?></td>
			<td><?php echo $childSheetPrice['delete_flg']; ?></td>
			<td><?php echo $childSheetPrice['deleted']; ?></td>
			<td class="actions">
				<?php echo $this->Html->link(__('View'), array('controller' => 'child_sheet_prices', 'action' => 'view', $childSheetPrice['id'])); ?>
				<?php echo $this->Html->link(__('Edit'), array('controller' => 'child_sheet_prices', 'action' => 'edit', $childSheetPrice['id'])); ?>
				<?php echo $this->Form->postLink(__('Delete'), array('controller' => 'child_sheet_prices', 'action' => 'delete', $childSheetPrice['id']), null, __('Are you sure you want to delete # %s?', $childSheetPrice['id'])); ?>
			</td>
		</tr>
	<?php endforeach; ?>
	</table>
<?php endif; ?>

	<div class="actions">
		<ul>
			<li><?php echo $this->Html->link(__('New Child Sheet Price'), array('controller' => 'child_sheet_prices', 'action' => 'add')); ?> </li>
		</ul>
	</div>
</div>
<div class="related">
	<h3><?php echo __('Related Client Car Model Sorts'); ?></h3>
	<?php if (!empty($client['ClientCarModelSort'])): ?>
	<table cellpadding = "0" cellspacing = "0">
	<tr>
		<th><?php echo __('Id'); ?></th>
		<th><?php echo __('Client Id'); ?></th>
		<th><?php echo __('Car Model Id'); ?></th>
		<th><?php echo __('Sort'); ?></th>
		<th><?php echo __('Delete Flg'); ?></th>
		<th><?php echo __('Created'); ?></th>
		<th><?php echo __('Modified'); ?></th>
		<th class="actions"><?php echo __('Actions'); ?></th>
	</tr>
	<?php
		$i = 0;
		foreach ($client['ClientCarModelSort'] as $clientCarModelSort): ?>
		<tr>
			<td><?php echo $clientCarModelSort['id']; ?></td>
			<td><?php echo $clientCarModelSort['client_id']; ?></td>
			<td><?php echo $clientCarModelSort['car_model_id']; ?></td>
			<td><?php echo $clientCarModelSort['sort']; ?></td>
			<td><?php echo $clientCarModelSort['delete_flg']; ?></td>
			<td><?php echo $clientCarModelSort['created']; ?></td>
			<td><?php echo $clientCarModelSort['modified']; ?></td>
			<td class="actions">
				<?php echo $this->Html->link(__('View'), array('controller' => 'client_car_model_sorts', 'action' => 'view', $clientCarModelSort['id'])); ?>
				<?php echo $this->Html->link(__('Edit'), array('controller' => 'client_car_model_sorts', 'action' => 'edit', $clientCarModelSort['id'])); ?>
				<?php echo $this->Form->postLink(__('Delete'), array('controller' => 'client_car_model_sorts', 'action' => 'delete', $clientCarModelSort['id']), null, __('Are you sure you want to delete # %s?', $clientCarModelSort['id'])); ?>
			</td>
		</tr>
	<?php endforeach; ?>
	</table>
<?php endif; ?>

	<div class="actions">
		<ul>
			<li><?php echo $this->Html->link(__('New Client Car Model Sort'), array('controller' => 'client_car_model_sorts', 'action' => 'add')); ?> </li>
		</ul>
	</div>
</div>
<div class="related">
	<h3><?php echo __('Related Client Car Models'); ?></h3>
	<?php if (!empty($client['ClientCarModel'])): ?>
	<table cellpadding = "0" cellspacing = "0">
	<tr>
		<th><?php echo __('Id'); ?></th>
		<th><?php echo __('Client Id'); ?></th>
		<th><?php echo __('Car Class Id'); ?></th>
		<th><?php echo __('Car Model Id'); ?></th>
		<th><?php echo __('Description'); ?></th>
		<th><?php echo __('Staff Id'); ?></th>
		<th><?php echo __('Created'); ?></th>
		<th><?php echo __('Modified'); ?></th>
		<th><?php echo __('Delete Flg'); ?></th>
		<th><?php echo __('Deleted'); ?></th>
		<th class="actions"><?php echo __('Actions'); ?></th>
	</tr>
	<?php
		$i = 0;
		foreach ($client['ClientCarModel'] as $clientCarModel): ?>
		<tr>
			<td><?php echo $clientCarModel['id']; ?></td>
			<td><?php echo $clientCarModel['client_id']; ?></td>
			<td><?php echo $clientCarModel['car_class_id']; ?></td>
			<td><?php echo $clientCarModel['car_model_id']; ?></td>
			<td><?php echo $clientCarModel['description']; ?></td>
			<td><?php echo $clientCarModel['staff_id']; ?></td>
			<td><?php echo $clientCarModel['created']; ?></td>
			<td><?php echo $clientCarModel['modified']; ?></td>
			<td><?php echo $clientCarModel['delete_flg']; ?></td>
			<td><?php echo $clientCarModel['deleted']; ?></td>
			<td class="actions">
				<?php echo $this->Html->link(__('View'), array('controller' => 'client_car_models', 'action' => 'view', $clientCarModel['id'])); ?>
				<?php echo $this->Html->link(__('Edit'), array('controller' => 'client_car_models', 'action' => 'edit', $clientCarModel['id'])); ?>
				<?php echo $this->Form->postLink(__('Delete'), array('controller' => 'client_car_models', 'action' => 'delete', $clientCarModel['id']), null, __('Are you sure you want to delete # %s?', $clientCarModel['id'])); ?>
			</td>
		</tr>
	<?php endforeach; ?>
	</table>
<?php endif; ?>

	<div class="actions">
		<ul>
			<li><?php echo $this->Html->link(__('New Client Car Model'), array('controller' => 'client_car_models', 'action' => 'add')); ?> </li>
		</ul>
	</div>
</div>
<div class="related">
	<h3><?php echo __('Related Client Cards'); ?></h3>
	<?php if (!empty($client['ClientCard'])): ?>
	<table cellpadding = "0" cellspacing = "0">
	<tr>
		<th><?php echo __('Id'); ?></th>
		<th><?php echo __('Client Id'); ?></th>
		<th><?php echo __('Credit Card Id'); ?></th>
		<th><?php echo __('Staff Id'); ?></th>
		<th><?php echo __('Created'); ?></th>
		<th><?php echo __('Modified'); ?></th>
		<th><?php echo __('Delete Flg'); ?></th>
		<th><?php echo __('Deleted'); ?></th>
		<th class="actions"><?php echo __('Actions'); ?></th>
	</tr>
	<?php
		$i = 0;
		foreach ($client['ClientCard'] as $clientCard): ?>
		<tr>
			<td><?php echo $clientCard['id']; ?></td>
			<td><?php echo $clientCard['client_id']; ?></td>
			<td><?php echo $clientCard['credit_card_id']; ?></td>
			<td><?php echo $clientCard['staff_id']; ?></td>
			<td><?php echo $clientCard['created']; ?></td>
			<td><?php echo $clientCard['modified']; ?></td>
			<td><?php echo $clientCard['delete_flg']; ?></td>
			<td><?php echo $clientCard['deleted']; ?></td>
			<td class="actions">
				<?php echo $this->Html->link(__('View'), array('controller' => 'client_cards', 'action' => 'view', $clientCard['id'])); ?>
				<?php echo $this->Html->link(__('Edit'), array('controller' => 'client_cards', 'action' => 'edit', $clientCard['id'])); ?>
				<?php echo $this->Form->postLink(__('Delete'), array('controller' => 'client_cards', 'action' => 'delete', $clientCard['id']), null, __('Are you sure you want to delete # %s?', $clientCard['id'])); ?>
			</td>
		</tr>
	<?php endforeach; ?>
	</table>
<?php endif; ?>

	<div class="actions">
		<ul>
			<li><?php echo $this->Html->link(__('New Client Card'), array('controller' => 'client_cards', 'action' => 'add')); ?> </li>
		</ul>
	</div>
</div>
<div class="related">
	<h3><?php echo __('Related Client Emails'); ?></h3>
	<?php if (!empty($client['ClientEmail'])): ?>
	<table cellpadding = "0" cellspacing = "0">
	<tr>
		<th><?php echo __('Id'); ?></th>
		<th><?php echo __('Client Id'); ?></th>
		<th><?php echo __('Reservation Email'); ?></th>
		<th><?php echo __('Staff Id'); ?></th>
		<th><?php echo __('Created'); ?></th>
		<th><?php echo __('Modified'); ?></th>
		<th><?php echo __('Delete Flg'); ?></th>
		<th><?php echo __('Deleted'); ?></th>
		<th class="actions"><?php echo __('Actions'); ?></th>
	</tr>
	<?php
		$i = 0;
		foreach ($client['ClientEmail'] as $clientEmail): ?>
		<tr>
			<td><?php echo $clientEmail['id']; ?></td>
			<td><?php echo $clientEmail['client_id']; ?></td>
			<td><?php echo $clientEmail['reservation_email']; ?></td>
			<td><?php echo $clientEmail['staff_id']; ?></td>
			<td><?php echo $clientEmail['created']; ?></td>
			<td><?php echo $clientEmail['modified']; ?></td>
			<td><?php echo $clientEmail['delete_flg']; ?></td>
			<td><?php echo $clientEmail['deleted']; ?></td>
			<td class="actions">
				<?php echo $this->Html->link(__('View'), array('controller' => 'client_emails', 'action' => 'view', $clientEmail['id'])); ?>
				<?php echo $this->Html->link(__('Edit'), array('controller' => 'client_emails', 'action' => 'edit', $clientEmail['id'])); ?>
				<?php echo $this->Form->postLink(__('Delete'), array('controller' => 'client_emails', 'action' => 'delete', $clientEmail['id']), null, __('Are you sure you want to delete # %s?', $clientEmail['id'])); ?>
			</td>
		</tr>
	<?php endforeach; ?>
	</table>
<?php endif; ?>

	<div class="actions">
		<ul>
			<li><?php echo $this->Html->link(__('New Client Email'), array('controller' => 'client_emails', 'action' => 'add')); ?> </li>
		</ul>
	</div>
</div>
<div class="related">
	<h3><?php echo __('Related Client Images'); ?></h3>
	<?php if (!empty($client['ClientImage'])): ?>
	<table cellpadding = "0" cellspacing = "0">
	<tr>
		<th><?php echo __('Id'); ?></th>
		<th><?php echo __('Client Id'); ?></th>
		<th><?php echo __('Name'); ?></th>
		<th><?php echo __('Delete Flg'); ?></th>
		<th><?php echo __('Created'); ?></th>
		<th><?php echo __('Modified'); ?></th>
		<th class="actions"><?php echo __('Actions'); ?></th>
	</tr>
	<?php
		$i = 0;
		foreach ($client['ClientImage'] as $clientImage): ?>
		<tr>
			<td><?php echo $clientImage['id']; ?></td>
			<td><?php echo $clientImage['client_id']; ?></td>
			<td><?php echo $clientImage['name']; ?></td>
			<td><?php echo $clientImage['delete_flg']; ?></td>
			<td><?php echo $clientImage['created']; ?></td>
			<td><?php echo $clientImage['modified']; ?></td>
			<td class="actions">
				<?php echo $this->Html->link(__('View'), array('controller' => 'client_images', 'action' => 'view', $clientImage['id'])); ?>
				<?php echo $this->Html->link(__('Edit'), array('controller' => 'client_images', 'action' => 'edit', $clientImage['id'])); ?>
				<?php echo $this->Form->postLink(__('Delete'), array('controller' => 'client_images', 'action' => 'delete', $clientImage['id']), null, __('Are you sure you want to delete # %s?', $clientImage['id'])); ?>
			</td>
		</tr>
	<?php endforeach; ?>
	</table>
<?php endif; ?>

	<div class="actions">
		<ul>
			<li><?php echo $this->Html->link(__('New Client Image'), array('controller' => 'client_images', 'action' => 'add')); ?> </li>
		</ul>
	</div>
</div>
<div class="related">
	<h3><?php echo __('Related Client Photos Introductions'); ?></h3>
	<?php if (!empty($client['ClientPhotosIntroduction'])): ?>
	<table cellpadding = "0" cellspacing = "0">
	<tr>
		<th><?php echo __('Id'); ?></th>
		<th><?php echo __('Client Id'); ?></th>
		<th><?php echo __('Number'); ?></th>
		<th><?php echo __('Name'); ?></th>
		<th><?php echo __('Text'); ?></th>
		<th><?php echo __('Created'); ?></th>
		<th class="actions"><?php echo __('Actions'); ?></th>
	</tr>
	<?php
		$i = 0;
		foreach ($client['ClientPhotosIntroduction'] as $clientPhotosIntroduction): ?>
		<tr>
			<td><?php echo $clientPhotosIntroduction['id']; ?></td>
			<td><?php echo $clientPhotosIntroduction['client_id']; ?></td>
			<td><?php echo $clientPhotosIntroduction['number']; ?></td>
			<td><?php echo $clientPhotosIntroduction['name']; ?></td>
			<td><?php echo $clientPhotosIntroduction['text']; ?></td>
			<td><?php echo $clientPhotosIntroduction['created']; ?></td>
			<td class="actions">
				<?php echo $this->Html->link(__('View'), array('controller' => 'client_photos_introductions', 'action' => 'view', $clientPhotosIntroduction['id'])); ?>
				<?php echo $this->Html->link(__('Edit'), array('controller' => 'client_photos_introductions', 'action' => 'edit', $clientPhotosIntroduction['id'])); ?>
				<?php echo $this->Form->postLink(__('Delete'), array('controller' => 'client_photos_introductions', 'action' => 'delete', $clientPhotosIntroduction['id']), null, __('Are you sure you want to delete # %s?', $clientPhotosIntroduction['id'])); ?>
			</td>
		</tr>
	<?php endforeach; ?>
	</table>
<?php endif; ?>

	<div class="actions">
		<ul>
			<li><?php echo $this->Html->link(__('New Client Photos Introduction'), array('controller' => 'client_photos_introductions', 'action' => 'add')); ?> </li>
		</ul>
	</div>
</div>
<div class="related">
	<h3><?php echo __('Related Client Review Star Averages'); ?></h3>
	<?php if (!empty($client['ClientReviewStarAverage'])): ?>
	<table cellpadding = "0" cellspacing = "0">
	<tr>
		<th><?php echo __('Id'); ?></th>
		<th><?php echo __('Client Id'); ?></th>
		<th><?php echo __('Client Star Average'); ?></th>
		<th><?php echo __('Question1 Star Average'); ?></th>
		<th><?php echo __('Question2 Star Average'); ?></th>
		<th><?php echo __('Question3 Star Average'); ?></th>
		<th><?php echo __('Question4 Star Average'); ?></th>
		<th><?php echo __('Question5 Star Average'); ?></th>
		<th><?php echo __('Review Count'); ?></th>
		<th><?php echo __('Star Count'); ?></th>
		<th><?php echo __('Created'); ?></th>
		<th><?php echo __('Modified'); ?></th>
		<th class="actions"><?php echo __('Actions'); ?></th>
	</tr>
	<?php
		$i = 0;
		foreach ($client['ClientReviewStarAverage'] as $clientReviewStarAverage): ?>
		<tr>
			<td><?php echo $clientReviewStarAverage['id']; ?></td>
			<td><?php echo $clientReviewStarAverage['client_id']; ?></td>
			<td><?php echo $clientReviewStarAverage['client_star_average']; ?></td>
			<td><?php echo $clientReviewStarAverage['question1_star_average']; ?></td>
			<td><?php echo $clientReviewStarAverage['question2_star_average']; ?></td>
			<td><?php echo $clientReviewStarAverage['question3_star_average']; ?></td>
			<td><?php echo $clientReviewStarAverage['question4_star_average']; ?></td>
			<td><?php echo $clientReviewStarAverage['question5_star_average']; ?></td>
			<td><?php echo $clientReviewStarAverage['review_count']; ?></td>
			<td><?php echo $clientReviewStarAverage['star_count']; ?></td>
			<td><?php echo $clientReviewStarAverage['created']; ?></td>
			<td><?php echo $clientReviewStarAverage['modified']; ?></td>
			<td class="actions">
				<?php echo $this->Html->link(__('View'), array('controller' => 'client_review_star_averages', 'action' => 'view', $clientReviewStarAverage['id'])); ?>
				<?php echo $this->Html->link(__('Edit'), array('controller' => 'client_review_star_averages', 'action' => 'edit', $clientReviewStarAverage['id'])); ?>
				<?php echo $this->Form->postLink(__('Delete'), array('controller' => 'client_review_star_averages', 'action' => 'delete', $clientReviewStarAverage['id']), null, __('Are you sure you want to delete # %s?', $clientReviewStarAverage['id'])); ?>
			</td>
		</tr>
	<?php endforeach; ?>
	</table>
<?php endif; ?>

	<div class="actions">
		<ul>
			<li><?php echo $this->Html->link(__('New Client Review Star Average'), array('controller' => 'client_review_star_averages', 'action' => 'add')); ?> </li>
		</ul>
	</div>
</div>
<div class="related">
	<h3><?php echo __('Related Client Sorts'); ?></h3>
	<?php if (!empty($client['ClientSort'])): ?>
	<table cellpadding = "0" cellspacing = "0">
	<tr>
		<th><?php echo __('Id'); ?></th>
		<th><?php echo __('Client Id'); ?></th>
		<th><?php echo __('Staff Id'); ?></th>
		<th><?php echo __('Created'); ?></th>
		<th><?php echo __('Modified'); ?></th>
		<th><?php echo __('Delete Flg'); ?></th>
		<th><?php echo __('Deleted'); ?></th>
		<th class="actions"><?php echo __('Actions'); ?></th>
	</tr>
	<?php
		$i = 0;
		foreach ($client['ClientSort'] as $clientSort): ?>
		<tr>
			<td><?php echo $clientSort['id']; ?></td>
			<td><?php echo $clientSort['client_id']; ?></td>
			<td><?php echo $clientSort['staff_id']; ?></td>
			<td><?php echo $clientSort['created']; ?></td>
			<td><?php echo $clientSort['modified']; ?></td>
			<td><?php echo $clientSort['delete_flg']; ?></td>
			<td><?php echo $clientSort['deleted']; ?></td>
			<td class="actions">
				<?php echo $this->Html->link(__('View'), array('controller' => 'client_sorts', 'action' => 'view', $clientSort['id'])); ?>
				<?php echo $this->Html->link(__('Edit'), array('controller' => 'client_sorts', 'action' => 'edit', $clientSort['id'])); ?>
				<?php echo $this->Form->postLink(__('Delete'), array('controller' => 'client_sorts', 'action' => 'delete', $clientSort['id']), null, __('Are you sure you want to delete # %s?', $clientSort['id'])); ?>
			</td>
		</tr>
	<?php endforeach; ?>
	</table>
<?php endif; ?>

	<div class="actions">
		<ul>
			<li><?php echo $this->Html->link(__('New Client Sort'), array('controller' => 'client_sorts', 'action' => 'add')); ?> </li>
		</ul>
	</div>
</div>
<div class="related">
	<h3><?php echo __('Related Client Templates'); ?></h3>
	<?php if (!empty($client['ClientTemplate'])): ?>
	<table cellpadding = "0" cellspacing = "0">
	<tr>
		<th><?php echo __('Id'); ?></th>
		<th><?php echo __('Client Id'); ?></th>
		<th><?php echo __('Name'); ?></th>
		<th><?php echo __('Template'); ?></th>
		<th><?php echo __('Sort'); ?></th>
		<th><?php echo __('Staff Id'); ?></th>
		<th><?php echo __('Created'); ?></th>
		<th><?php echo __('Modified'); ?></th>
		<th><?php echo __('Delete Flg'); ?></th>
		<th><?php echo __('Deleted'); ?></th>
		<th class="actions"><?php echo __('Actions'); ?></th>
	</tr>
	<?php
		$i = 0;
		foreach ($client['ClientTemplate'] as $clientTemplate): ?>
		<tr>
			<td><?php echo $clientTemplate['id']; ?></td>
			<td><?php echo $clientTemplate['client_id']; ?></td>
			<td><?php echo $clientTemplate['name']; ?></td>
			<td><?php echo $clientTemplate['template']; ?></td>
			<td><?php echo $clientTemplate['sort']; ?></td>
			<td><?php echo $clientTemplate['staff_id']; ?></td>
			<td><?php echo $clientTemplate['created']; ?></td>
			<td><?php echo $clientTemplate['modified']; ?></td>
			<td><?php echo $clientTemplate['delete_flg']; ?></td>
			<td><?php echo $clientTemplate['deleted']; ?></td>
			<td class="actions">
				<?php echo $this->Html->link(__('View'), array('controller' => 'client_templates', 'action' => 'view', $clientTemplate['id'])); ?>
				<?php echo $this->Html->link(__('Edit'), array('controller' => 'client_templates', 'action' => 'edit', $clientTemplate['id'])); ?>
				<?php echo $this->Form->postLink(__('Delete'), array('controller' => 'client_templates', 'action' => 'delete', $clientTemplate['id']), null, __('Are you sure you want to delete # %s?', $clientTemplate['id'])); ?>
			</td>
		</tr>
	<?php endforeach; ?>
	</table>
<?php endif; ?>

	<div class="actions">
		<ul>
			<li><?php echo $this->Html->link(__('New Client Template'), array('controller' => 'client_templates', 'action' => 'add')); ?> </li>
		</ul>
	</div>
</div>
<div class="related">
	<h3><?php echo __('Related Commodities'); ?></h3>
	<?php if (!empty($client['Commodity'])): ?>
	<table cellpadding = "0" cellspacing = "0">
	<tr>
		<th><?php echo __('Id'); ?></th>
		<th><?php echo __('Commodity Key'); ?></th>
		<th><?php echo __('Client Id'); ?></th>
		<th><?php echo __('Plan Id'); ?></th>
		<th><?php echo __('Commodity Group Id'); ?></th>
		<th><?php echo __('Language Id'); ?></th>
		<th><?php echo __('Name'); ?></th>
		<th><?php echo __('Rent Time From'); ?></th>
		<th><?php echo __('Rent Time To'); ?></th>
		<th><?php echo __('Return Time From'); ?></th>
		<th><?php echo __('Return Time To'); ?></th>
		<th><?php echo __('Image Relative Url'); ?></th>
		<th><?php echo __('Description'); ?></th>
		<th><?php echo __('Remark'); ?></th>
		<th><?php echo __('Public Request'); ?></th>
		<th><?php echo __('Is Published'); ?></th>
		<th><?php echo __('New Car Registration'); ?></th>
		<th><?php echo __('Day Time Flg'); ?></th>
		<th><?php echo __('Smoking Flg'); ?></th>
		<th><?php echo __('Transmission Flg'); ?></th>
		<th><?php echo __('Staff Id'); ?></th>
		<th><?php echo __('Created'); ?></th>
		<th><?php echo __('Modified'); ?></th>
		<th><?php echo __('Delete Flg'); ?></th>
		<th><?php echo __('Deleted'); ?></th>
		<th class="actions"><?php echo __('Actions'); ?></th>
	</tr>
	<?php
		$i = 0;
		foreach ($client['Commodity'] as $commodity): ?>
		<tr>
			<td><?php echo $commodity['id']; ?></td>
			<td><?php echo $commodity['commodity_key']; ?></td>
			<td><?php echo $commodity['client_id']; ?></td>
			<td><?php echo $commodity['plan_id']; ?></td>
			<td><?php echo $commodity['commodity_group_id']; ?></td>
			<td><?php echo $commodity['language_id']; ?></td>
			<td><?php echo $commodity['name']; ?></td>
			<td><?php echo $commodity['rent_time_from']; ?></td>
			<td><?php echo $commodity['rent_time_to']; ?></td>
			<td><?php echo $commodity['return_time_from']; ?></td>
			<td><?php echo $commodity['return_time_to']; ?></td>
			<td><?php echo $commodity['image_relative_url']; ?></td>
			<td><?php echo $commodity['description']; ?></td>
			<td><?php echo $commodity['remark']; ?></td>
			<td><?php echo $commodity['public_request']; ?></td>
			<td><?php echo $commodity['is_published']; ?></td>
			<td><?php echo $commodity['new_car_registration']; ?></td>
			<td><?php echo $commodity['day_time_flg']; ?></td>
			<td><?php echo $commodity['smoking_flg']; ?></td>
			<td><?php echo $commodity['transmission_flg']; ?></td>
			<td><?php echo $commodity['staff_id']; ?></td>
			<td><?php echo $commodity['created']; ?></td>
			<td><?php echo $commodity['modified']; ?></td>
			<td><?php echo $commodity['delete_flg']; ?></td>
			<td><?php echo $commodity['deleted']; ?></td>
			<td class="actions">
				<?php echo $this->Html->link(__('View'), array('controller' => 'commodities', 'action' => 'view', $commodity['id'])); ?>
				<?php echo $this->Html->link(__('Edit'), array('controller' => 'commodities', 'action' => 'edit', $commodity['id'])); ?>
				<?php echo $this->Form->postLink(__('Delete'), array('controller' => 'commodities', 'action' => 'delete', $commodity['id']), null, __('Are you sure you want to delete # %s?', $commodity['id'])); ?>
			</td>
		</tr>
	<?php endforeach; ?>
	</table>
<?php endif; ?>

	<div class="actions">
		<ul>
			<li><?php echo $this->Html->link(__('New Commodity'), array('controller' => 'commodities', 'action' => 'add')); ?> </li>
		</ul>
	</div>
</div>
<div class="related">
	<h3><?php echo __('Related Commodity Equipments'); ?></h3>
	<?php if (!empty($client['CommodityEquipment'])): ?>
	<table cellpadding = "0" cellspacing = "0">
	<tr>
		<th><?php echo __('Id'); ?></th>
		<th><?php echo __('Client Id'); ?></th>
		<th><?php echo __('Commodity Id'); ?></th>
		<th><?php echo __('Equipment Id'); ?></th>
		<th><?php echo __('Staff Id'); ?></th>
		<th><?php echo __('Created'); ?></th>
		<th><?php echo __('Modified'); ?></th>
		<th><?php echo __('Delete Flg'); ?></th>
		<th><?php echo __('Deleted'); ?></th>
		<th class="actions"><?php echo __('Actions'); ?></th>
	</tr>
	<?php
		$i = 0;
		foreach ($client['CommodityEquipment'] as $commodityEquipment): ?>
		<tr>
			<td><?php echo $commodityEquipment['id']; ?></td>
			<td><?php echo $commodityEquipment['client_id']; ?></td>
			<td><?php echo $commodityEquipment['commodity_id']; ?></td>
			<td><?php echo $commodityEquipment['equipment_id']; ?></td>
			<td><?php echo $commodityEquipment['staff_id']; ?></td>
			<td><?php echo $commodityEquipment['created']; ?></td>
			<td><?php echo $commodityEquipment['modified']; ?></td>
			<td><?php echo $commodityEquipment['delete_flg']; ?></td>
			<td><?php echo $commodityEquipment['deleted']; ?></td>
			<td class="actions">
				<?php echo $this->Html->link(__('View'), array('controller' => 'commodity_equipments', 'action' => 'view', $commodityEquipment['id'])); ?>
				<?php echo $this->Html->link(__('Edit'), array('controller' => 'commodity_equipments', 'action' => 'edit', $commodityEquipment['id'])); ?>
				<?php echo $this->Form->postLink(__('Delete'), array('controller' => 'commodity_equipments', 'action' => 'delete', $commodityEquipment['id']), null, __('Are you sure you want to delete # %s?', $commodityEquipment['id'])); ?>
			</td>
		</tr>
	<?php endforeach; ?>
	</table>
<?php endif; ?>

	<div class="actions">
		<ul>
			<li><?php echo $this->Html->link(__('New Commodity Equipment'), array('controller' => 'commodity_equipments', 'action' => 'add')); ?> </li>
		</ul>
	</div>
</div>
<div class="related">
	<h3><?php echo __('Related Commodity Free Child Sheets'); ?></h3>
	<?php if (!empty($client['CommodityFreeChildSheet'])): ?>
	<table cellpadding = "0" cellspacing = "0">
	<tr>
		<th><?php echo __('Id'); ?></th>
		<th><?php echo __('Client Id'); ?></th>
		<th><?php echo __('Commodity Id'); ?></th>
		<th><?php echo __('Child Sheet Id'); ?></th>
		<th><?php echo __('Maximum'); ?></th>
		<th><?php echo __('Staff Id'); ?></th>
		<th><?php echo __('Created'); ?></th>
		<th><?php echo __('Modified'); ?></th>
		<th><?php echo __('Delete Flg'); ?></th>
		<th><?php echo __('Deleted'); ?></th>
		<th class="actions"><?php echo __('Actions'); ?></th>
	</tr>
	<?php
		$i = 0;
		foreach ($client['CommodityFreeChildSheet'] as $commodityFreeChildSheet): ?>
		<tr>
			<td><?php echo $commodityFreeChildSheet['id']; ?></td>
			<td><?php echo $commodityFreeChildSheet['client_id']; ?></td>
			<td><?php echo $commodityFreeChildSheet['commodity_id']; ?></td>
			<td><?php echo $commodityFreeChildSheet['child_sheet_id']; ?></td>
			<td><?php echo $commodityFreeChildSheet['maximum']; ?></td>
			<td><?php echo $commodityFreeChildSheet['staff_id']; ?></td>
			<td><?php echo $commodityFreeChildSheet['created']; ?></td>
			<td><?php echo $commodityFreeChildSheet['modified']; ?></td>
			<td><?php echo $commodityFreeChildSheet['delete_flg']; ?></td>
			<td><?php echo $commodityFreeChildSheet['deleted']; ?></td>
			<td class="actions">
				<?php echo $this->Html->link(__('View'), array('controller' => 'commodity_free_child_sheets', 'action' => 'view', $commodityFreeChildSheet['id'])); ?>
				<?php echo $this->Html->link(__('Edit'), array('controller' => 'commodity_free_child_sheets', 'action' => 'edit', $commodityFreeChildSheet['id'])); ?>
				<?php echo $this->Form->postLink(__('Delete'), array('controller' => 'commodity_free_child_sheets', 'action' => 'delete', $commodityFreeChildSheet['id']), null, __('Are you sure you want to delete # %s?', $commodityFreeChildSheet['id'])); ?>
			</td>
		</tr>
	<?php endforeach; ?>
	</table>
<?php endif; ?>

	<div class="actions">
		<ul>
			<li><?php echo $this->Html->link(__('New Commodity Free Child Sheet'), array('controller' => 'commodity_free_child_sheets', 'action' => 'add')); ?> </li>
		</ul>
	</div>
</div>
<div class="related">
	<h3><?php echo __('Related Commodity Groups'); ?></h3>
	<?php if (!empty($client['CommodityGroup'])): ?>
	<table cellpadding = "0" cellspacing = "0">
	<tr>
		<th><?php echo __('Id'); ?></th>
		<th><?php echo __('Client Id'); ?></th>
		<th><?php echo __('Name'); ?></th>
		<th><?php echo __('Available From'); ?></th>
		<th><?php echo __('Available To'); ?></th>
		<th><?php echo __('Is Published'); ?></th>
		<th><?php echo __('Sort'); ?></th>
		<th><?php echo __('Staff Id'); ?></th>
		<th><?php echo __('Created'); ?></th>
		<th><?php echo __('Modified'); ?></th>
		<th><?php echo __('Delete Flg'); ?></th>
		<th><?php echo __('Deleted'); ?></th>
		<th class="actions"><?php echo __('Actions'); ?></th>
	</tr>
	<?php
		$i = 0;
		foreach ($client['CommodityGroup'] as $commodityGroup): ?>
		<tr>
			<td><?php echo $commodityGroup['id']; ?></td>
			<td><?php echo $commodityGroup['client_id']; ?></td>
			<td><?php echo $commodityGroup['name']; ?></td>
			<td><?php echo $commodityGroup['available_from']; ?></td>
			<td><?php echo $commodityGroup['available_to']; ?></td>
			<td><?php echo $commodityGroup['is_published']; ?></td>
			<td><?php echo $commodityGroup['sort']; ?></td>
			<td><?php echo $commodityGroup['staff_id']; ?></td>
			<td><?php echo $commodityGroup['created']; ?></td>
			<td><?php echo $commodityGroup['modified']; ?></td>
			<td><?php echo $commodityGroup['delete_flg']; ?></td>
			<td><?php echo $commodityGroup['deleted']; ?></td>
			<td class="actions">
				<?php echo $this->Html->link(__('View'), array('controller' => 'commodity_groups', 'action' => 'view', $commodityGroup['id'])); ?>
				<?php echo $this->Html->link(__('Edit'), array('controller' => 'commodity_groups', 'action' => 'edit', $commodityGroup['id'])); ?>
				<?php echo $this->Form->postLink(__('Delete'), array('controller' => 'commodity_groups', 'action' => 'delete', $commodityGroup['id']), null, __('Are you sure you want to delete # %s?', $commodityGroup['id'])); ?>
			</td>
		</tr>
	<?php endforeach; ?>
	</table>
<?php endif; ?>

	<div class="actions">
		<ul>
			<li><?php echo $this->Html->link(__('New Commodity Group'), array('controller' => 'commodity_groups', 'action' => 'add')); ?> </li>
		</ul>
	</div>
</div>
<div class="related">
	<h3><?php echo __('Related Commodity Images'); ?></h3>
	<?php if (!empty($client['CommodityImage'])): ?>
	<table cellpadding = "0" cellspacing = "0">
	<tr>
		<th><?php echo __('Id'); ?></th>
		<th><?php echo __('Client Id'); ?></th>
		<th><?php echo __('Commodity Id'); ?></th>
		<th><?php echo __('Image Relative Url'); ?></th>
		<th><?php echo __('Remark'); ?></th>
		<th><?php echo __('Staff Id'); ?></th>
		<th><?php echo __('Created'); ?></th>
		<th><?php echo __('Modified'); ?></th>
		<th><?php echo __('Delete Flg'); ?></th>
		<th><?php echo __('Deleted'); ?></th>
		<th class="actions"><?php echo __('Actions'); ?></th>
	</tr>
	<?php
		$i = 0;
		foreach ($client['CommodityImage'] as $commodityImage): ?>
		<tr>
			<td><?php echo $commodityImage['id']; ?></td>
			<td><?php echo $commodityImage['client_id']; ?></td>
			<td><?php echo $commodityImage['commodity_id']; ?></td>
			<td><?php echo $commodityImage['image_relative_url']; ?></td>
			<td><?php echo $commodityImage['remark']; ?></td>
			<td><?php echo $commodityImage['staff_id']; ?></td>
			<td><?php echo $commodityImage['created']; ?></td>
			<td><?php echo $commodityImage['modified']; ?></td>
			<td><?php echo $commodityImage['delete_flg']; ?></td>
			<td><?php echo $commodityImage['deleted']; ?></td>
			<td class="actions">
				<?php echo $this->Html->link(__('View'), array('controller' => 'commodity_images', 'action' => 'view', $commodityImage['id'])); ?>
				<?php echo $this->Html->link(__('Edit'), array('controller' => 'commodity_images', 'action' => 'edit', $commodityImage['id'])); ?>
				<?php echo $this->Form->postLink(__('Delete'), array('controller' => 'commodity_images', 'action' => 'delete', $commodityImage['id']), null, __('Are you sure you want to delete # %s?', $commodityImage['id'])); ?>
			</td>
		</tr>
	<?php endforeach; ?>
	</table>
<?php endif; ?>

	<div class="actions">
		<ul>
			<li><?php echo $this->Html->link(__('New Commodity Image'), array('controller' => 'commodity_images', 'action' => 'add')); ?> </li>
		</ul>
	</div>
</div>
<div class="related">
	<h3><?php echo __('Related Commodity Item Reservations'); ?></h3>
	<?php if (!empty($client['CommodityItemReservation'])): ?>
	<table cellpadding = "0" cellspacing = "0">
	<tr>
		<th><?php echo __('Id'); ?></th>
		<th><?php echo __('Client Id'); ?></th>
		<th><?php echo __('Stock Group Id'); ?></th>
		<th><?php echo __('Commodity Item Id'); ?></th>
		<th><?php echo __('Stock Date'); ?></th>
		<th><?php echo __('Reservation Id'); ?></th>
		<th><?php echo __('Reservation Count'); ?></th>
		<th><?php echo __('Staff Id'); ?></th>
		<th><?php echo __('Created'); ?></th>
		<th><?php echo __('Modified'); ?></th>
		<th><?php echo __('Delete Flg'); ?></th>
		<th><?php echo __('Deleted'); ?></th>
		<th class="actions"><?php echo __('Actions'); ?></th>
	</tr>
	<?php
		$i = 0;
		foreach ($client['CommodityItemReservation'] as $commodityItemReservation): ?>
		<tr>
			<td><?php echo $commodityItemReservation['id']; ?></td>
			<td><?php echo $commodityItemReservation['client_id']; ?></td>
			<td><?php echo $commodityItemReservation['stock_group_id']; ?></td>
			<td><?php echo $commodityItemReservation['commodity_item_id']; ?></td>
			<td><?php echo $commodityItemReservation['stock_date']; ?></td>
			<td><?php echo $commodityItemReservation['reservation_id']; ?></td>
			<td><?php echo $commodityItemReservation['reservation_count']; ?></td>
			<td><?php echo $commodityItemReservation['staff_id']; ?></td>
			<td><?php echo $commodityItemReservation['created']; ?></td>
			<td><?php echo $commodityItemReservation['modified']; ?></td>
			<td><?php echo $commodityItemReservation['delete_flg']; ?></td>
			<td><?php echo $commodityItemReservation['deleted']; ?></td>
			<td class="actions">
				<?php echo $this->Html->link(__('View'), array('controller' => 'commodity_item_reservations', 'action' => 'view', $commodityItemReservation['id'])); ?>
				<?php echo $this->Html->link(__('Edit'), array('controller' => 'commodity_item_reservations', 'action' => 'edit', $commodityItemReservation['id'])); ?>
				<?php echo $this->Form->postLink(__('Delete'), array('controller' => 'commodity_item_reservations', 'action' => 'delete', $commodityItemReservation['id']), null, __('Are you sure you want to delete # %s?', $commodityItemReservation['id'])); ?>
			</td>
		</tr>
	<?php endforeach; ?>
	</table>
<?php endif; ?>

	<div class="actions">
		<ul>
			<li><?php echo $this->Html->link(__('New Commodity Item Reservation'), array('controller' => 'commodity_item_reservations', 'action' => 'add')); ?> </li>
		</ul>
	</div>
</div>
<div class="related">
	<h3><?php echo __('Related Commodity Item Stocks'); ?></h3>
	<?php if (!empty($client['CommodityItemStock'])): ?>
	<table cellpadding = "0" cellspacing = "0">
	<tr>
		<th><?php echo __('Id'); ?></th>
		<th><?php echo __('Client Id'); ?></th>
		<th><?php echo __('Stock Group Id'); ?></th>
		<th><?php echo __('Commodity Item Id'); ?></th>
		<th><?php echo __('Stock Date'); ?></th>
		<th><?php echo __('Stock Count'); ?></th>
		<th><?php echo __('Staff Id'); ?></th>
		<th><?php echo __('Created'); ?></th>
		<th><?php echo __('Modified'); ?></th>
		<th><?php echo __('Delete Flg'); ?></th>
		<th><?php echo __('Deleted'); ?></th>
		<th class="actions"><?php echo __('Actions'); ?></th>
	</tr>
	<?php
		$i = 0;
		foreach ($client['CommodityItemStock'] as $commodityItemStock): ?>
		<tr>
			<td><?php echo $commodityItemStock['id']; ?></td>
			<td><?php echo $commodityItemStock['client_id']; ?></td>
			<td><?php echo $commodityItemStock['stock_group_id']; ?></td>
			<td><?php echo $commodityItemStock['commodity_item_id']; ?></td>
			<td><?php echo $commodityItemStock['stock_date']; ?></td>
			<td><?php echo $commodityItemStock['stock_count']; ?></td>
			<td><?php echo $commodityItemStock['staff_id']; ?></td>
			<td><?php echo $commodityItemStock['created']; ?></td>
			<td><?php echo $commodityItemStock['modified']; ?></td>
			<td><?php echo $commodityItemStock['delete_flg']; ?></td>
			<td><?php echo $commodityItemStock['deleted']; ?></td>
			<td class="actions">
				<?php echo $this->Html->link(__('View'), array('controller' => 'commodity_item_stocks', 'action' => 'view', $commodityItemStock['id'])); ?>
				<?php echo $this->Html->link(__('Edit'), array('controller' => 'commodity_item_stocks', 'action' => 'edit', $commodityItemStock['id'])); ?>
				<?php echo $this->Form->postLink(__('Delete'), array('controller' => 'commodity_item_stocks', 'action' => 'delete', $commodityItemStock['id']), null, __('Are you sure you want to delete # %s?', $commodityItemStock['id'])); ?>
			</td>
		</tr>
	<?php endforeach; ?>
	</table>
<?php endif; ?>

	<div class="actions">
		<ul>
			<li><?php echo $this->Html->link(__('New Commodity Item Stock'), array('controller' => 'commodity_item_stocks', 'action' => 'add')); ?> </li>
		</ul>
	</div>
</div>
<div class="related">
	<h3><?php echo __('Related Commodity Items'); ?></h3>
	<?php if (!empty($client['CommodityItem'])): ?>
	<table cellpadding = "0" cellspacing = "0">
	<tr>
		<th><?php echo __('Id'); ?></th>
		<th><?php echo __('Client Id'); ?></th>
		<th><?php echo __('Commodity Id'); ?></th>
		<th><?php echo __('Car Class Id'); ?></th>
		<th><?php echo __('Need Commodity Stocks'); ?></th>
		<th><?php echo __('Staff Id'); ?></th>
		<th><?php echo __('Created'); ?></th>
		<th><?php echo __('Modified'); ?></th>
		<th><?php echo __('Delete Flg'); ?></th>
		<th><?php echo __('Deleted'); ?></th>
		<th class="actions"><?php echo __('Actions'); ?></th>
	</tr>
	<?php
		$i = 0;
		foreach ($client['CommodityItem'] as $commodityItem): ?>
		<tr>
			<td><?php echo $commodityItem['id']; ?></td>
			<td><?php echo $commodityItem['client_id']; ?></td>
			<td><?php echo $commodityItem['commodity_id']; ?></td>
			<td><?php echo $commodityItem['car_class_id']; ?></td>
			<td><?php echo $commodityItem['need_commodity_stocks']; ?></td>
			<td><?php echo $commodityItem['staff_id']; ?></td>
			<td><?php echo $commodityItem['created']; ?></td>
			<td><?php echo $commodityItem['modified']; ?></td>
			<td><?php echo $commodityItem['delete_flg']; ?></td>
			<td><?php echo $commodityItem['deleted']; ?></td>
			<td class="actions">
				<?php echo $this->Html->link(__('View'), array('controller' => 'commodity_items', 'action' => 'view', $commodityItem['id'])); ?>
				<?php echo $this->Html->link(__('Edit'), array('controller' => 'commodity_items', 'action' => 'edit', $commodityItem['id'])); ?>
				<?php echo $this->Form->postLink(__('Delete'), array('controller' => 'commodity_items', 'action' => 'delete', $commodityItem['id']), null, __('Are you sure you want to delete # %s?', $commodityItem['id'])); ?>
			</td>
		</tr>
	<?php endforeach; ?>
	</table>
<?php endif; ?>

	<div class="actions">
		<ul>
			<li><?php echo $this->Html->link(__('New Commodity Item'), array('controller' => 'commodity_items', 'action' => 'add')); ?> </li>
		</ul>
	</div>
</div>
<div class="related">
	<h3><?php echo __('Related Commodity Prices'); ?></h3>
	<?php if (!empty($client['CommodityPrice'])): ?>
	<table cellpadding = "0" cellspacing = "0">
	<tr>
		<th><?php echo __('Id'); ?></th>
		<th><?php echo __('Client Id'); ?></th>
		<th><?php echo __('Price Rank Id'); ?></th>
		<th><?php echo __('Price Span Id'); ?></th>
		<th><?php echo __('Span Count'); ?></th>
		<th><?php echo __('Excluding Tax'); ?></th>
		<th><?php echo __('Tax'); ?></th>
		<th><?php echo __('Price'); ?></th>
		<th><?php echo __('Commodity Item Id'); ?></th>
		<th><?php echo __('Staff Id'); ?></th>
		<th><?php echo __('Created'); ?></th>
		<th><?php echo __('Modified'); ?></th>
		<th><?php echo __('Delete Flg'); ?></th>
		<th><?php echo __('Deleted'); ?></th>
		<th class="actions"><?php echo __('Actions'); ?></th>
	</tr>
	<?php
		$i = 0;
		foreach ($client['CommodityPrice'] as $commodityPrice): ?>
		<tr>
			<td><?php echo $commodityPrice['id']; ?></td>
			<td><?php echo $commodityPrice['client_id']; ?></td>
			<td><?php echo $commodityPrice['price_rank_id']; ?></td>
			<td><?php echo $commodityPrice['price_span_id']; ?></td>
			<td><?php echo $commodityPrice['span_count']; ?></td>
			<td><?php echo $commodityPrice['excluding_tax']; ?></td>
			<td><?php echo $commodityPrice['tax']; ?></td>
			<td><?php echo $commodityPrice['price']; ?></td>
			<td><?php echo $commodityPrice['commodity_item_id']; ?></td>
			<td><?php echo $commodityPrice['staff_id']; ?></td>
			<td><?php echo $commodityPrice['created']; ?></td>
			<td><?php echo $commodityPrice['modified']; ?></td>
			<td><?php echo $commodityPrice['delete_flg']; ?></td>
			<td><?php echo $commodityPrice['deleted']; ?></td>
			<td class="actions">
				<?php echo $this->Html->link(__('View'), array('controller' => 'commodity_prices', 'action' => 'view', $commodityPrice['id'])); ?>
				<?php echo $this->Html->link(__('Edit'), array('controller' => 'commodity_prices', 'action' => 'edit', $commodityPrice['id'])); ?>
				<?php echo $this->Form->postLink(__('Delete'), array('controller' => 'commodity_prices', 'action' => 'delete', $commodityPrice['id']), null, __('Are you sure you want to delete # %s?', $commodityPrice['id'])); ?>
			</td>
		</tr>
	<?php endforeach; ?>
	</table>
<?php endif; ?>

	<div class="actions">
		<ul>
			<li><?php echo $this->Html->link(__('New Commodity Price'), array('controller' => 'commodity_prices', 'action' => 'add')); ?> </li>
		</ul>
	</div>
</div>
<div class="related">
	<h3><?php echo __('Related Commodity Privileges'); ?></h3>
	<?php if (!empty($client['CommodityPrivilege'])): ?>
	<table cellpadding = "0" cellspacing = "0">
	<tr>
		<th><?php echo __('Id'); ?></th>
		<th><?php echo __('Client Id'); ?></th>
		<th><?php echo __('Commodity Id'); ?></th>
		<th><?php echo __('Privilege Id'); ?></th>
		<th><?php echo __('Staff Id'); ?></th>
		<th><?php echo __('Created'); ?></th>
		<th><?php echo __('Modified'); ?></th>
		<th><?php echo __('Delete Flg'); ?></th>
		<th><?php echo __('Deleted'); ?></th>
		<th class="actions"><?php echo __('Actions'); ?></th>
	</tr>
	<?php
		$i = 0;
		foreach ($client['CommodityPrivilege'] as $commodityPrivilege): ?>
		<tr>
			<td><?php echo $commodityPrivilege['id']; ?></td>
			<td><?php echo $commodityPrivilege['client_id']; ?></td>
			<td><?php echo $commodityPrivilege['commodity_id']; ?></td>
			<td><?php echo $commodityPrivilege['privilege_id']; ?></td>
			<td><?php echo $commodityPrivilege['staff_id']; ?></td>
			<td><?php echo $commodityPrivilege['created']; ?></td>
			<td><?php echo $commodityPrivilege['modified']; ?></td>
			<td><?php echo $commodityPrivilege['delete_flg']; ?></td>
			<td><?php echo $commodityPrivilege['deleted']; ?></td>
			<td class="actions">
				<?php echo $this->Html->link(__('View'), array('controller' => 'commodity_privileges', 'action' => 'view', $commodityPrivilege['id'])); ?>
				<?php echo $this->Html->link(__('Edit'), array('controller' => 'commodity_privileges', 'action' => 'edit', $commodityPrivilege['id'])); ?>
				<?php echo $this->Form->postLink(__('Delete'), array('controller' => 'commodity_privileges', 'action' => 'delete', $commodityPrivilege['id']), null, __('Are you sure you want to delete # %s?', $commodityPrivilege['id'])); ?>
			</td>
		</tr>
	<?php endforeach; ?>
	</table>
<?php endif; ?>

	<div class="actions">
		<ul>
			<li><?php echo $this->Html->link(__('New Commodity Privilege'), array('controller' => 'commodity_privileges', 'action' => 'add')); ?> </li>
		</ul>
	</div>
</div>
<div class="related">
	<h3><?php echo __('Related Commodity Rankings'); ?></h3>
	<?php if (!empty($client['CommodityRanking'])): ?>
	<table cellpadding = "0" cellspacing = "0">
	<tr>
		<th><?php echo __('Id'); ?></th>
		<th><?php echo __('Client Id'); ?></th>
		<th><?php echo __('Commodity Item Id'); ?></th>
		<th><?php echo __('Commodity Ranking Category Id'); ?></th>
		<th><?php echo __('Rank'); ?></th>
		<th><?php echo __('Staff Id'); ?></th>
		<th><?php echo __('Created'); ?></th>
		<th><?php echo __('Modified'); ?></th>
		<th><?php echo __('Delete Flg'); ?></th>
		<th><?php echo __('Deleted'); ?></th>
		<th class="actions"><?php echo __('Actions'); ?></th>
	</tr>
	<?php
		$i = 0;
		foreach ($client['CommodityRanking'] as $commodityRanking): ?>
		<tr>
			<td><?php echo $commodityRanking['id']; ?></td>
			<td><?php echo $commodityRanking['client_id']; ?></td>
			<td><?php echo $commodityRanking['commodity_item_id']; ?></td>
			<td><?php echo $commodityRanking['commodity_ranking_category_id']; ?></td>
			<td><?php echo $commodityRanking['rank']; ?></td>
			<td><?php echo $commodityRanking['staff_id']; ?></td>
			<td><?php echo $commodityRanking['created']; ?></td>
			<td><?php echo $commodityRanking['modified']; ?></td>
			<td><?php echo $commodityRanking['delete_flg']; ?></td>
			<td><?php echo $commodityRanking['deleted']; ?></td>
			<td class="actions">
				<?php echo $this->Html->link(__('View'), array('controller' => 'commodity_rankings', 'action' => 'view', $commodityRanking['id'])); ?>
				<?php echo $this->Html->link(__('Edit'), array('controller' => 'commodity_rankings', 'action' => 'edit', $commodityRanking['id'])); ?>
				<?php echo $this->Form->postLink(__('Delete'), array('controller' => 'commodity_rankings', 'action' => 'delete', $commodityRanking['id']), null, __('Are you sure you want to delete # %s?', $commodityRanking['id'])); ?>
			</td>
		</tr>
	<?php endforeach; ?>
	</table>
<?php endif; ?>

	<div class="actions">
		<ul>
			<li><?php echo $this->Html->link(__('New Commodity Ranking'), array('controller' => 'commodity_rankings', 'action' => 'add')); ?> </li>
		</ul>
	</div>
</div>
<div class="related">
	<h3><?php echo __('Related Commodity Rent Offices'); ?></h3>
	<?php if (!empty($client['CommodityRentOffice'])): ?>
	<table cellpadding = "0" cellspacing = "0">
	<tr>
		<th><?php echo __('Id'); ?></th>
		<th><?php echo __('Client Id'); ?></th>
		<th><?php echo __('Commodity Id'); ?></th>
		<th><?php echo __('Office Id'); ?></th>
		<th><?php echo __('Staff Id'); ?></th>
		<th><?php echo __('Created'); ?></th>
		<th><?php echo __('Modified'); ?></th>
		<th><?php echo __('Delete Flg'); ?></th>
		<th><?php echo __('Deleted'); ?></th>
		<th class="actions"><?php echo __('Actions'); ?></th>
	</tr>
	<?php
		$i = 0;
		foreach ($client['CommodityRentOffice'] as $commodityRentOffice): ?>
		<tr>
			<td><?php echo $commodityRentOffice['id']; ?></td>
			<td><?php echo $commodityRentOffice['client_id']; ?></td>
			<td><?php echo $commodityRentOffice['commodity_id']; ?></td>
			<td><?php echo $commodityRentOffice['office_id']; ?></td>
			<td><?php echo $commodityRentOffice['staff_id']; ?></td>
			<td><?php echo $commodityRentOffice['created']; ?></td>
			<td><?php echo $commodityRentOffice['modified']; ?></td>
			<td><?php echo $commodityRentOffice['delete_flg']; ?></td>
			<td><?php echo $commodityRentOffice['deleted']; ?></td>
			<td class="actions">
				<?php echo $this->Html->link(__('View'), array('controller' => 'commodity_rent_offices', 'action' => 'view', $commodityRentOffice['id'])); ?>
				<?php echo $this->Html->link(__('Edit'), array('controller' => 'commodity_rent_offices', 'action' => 'edit', $commodityRentOffice['id'])); ?>
				<?php echo $this->Form->postLink(__('Delete'), array('controller' => 'commodity_rent_offices', 'action' => 'delete', $commodityRentOffice['id']), null, __('Are you sure you want to delete # %s?', $commodityRentOffice['id'])); ?>
			</td>
		</tr>
	<?php endforeach; ?>
	</table>
<?php endif; ?>

	<div class="actions">
		<ul>
			<li><?php echo $this->Html->link(__('New Commodity Rent Office'), array('controller' => 'commodity_rent_offices', 'action' => 'add')); ?> </li>
		</ul>
	</div>
</div>
<div class="related">
	<h3><?php echo __('Related Commodity Return Offices'); ?></h3>
	<?php if (!empty($client['CommodityReturnOffice'])): ?>
	<table cellpadding = "0" cellspacing = "0">
	<tr>
		<th><?php echo __('Id'); ?></th>
		<th><?php echo __('Client Id'); ?></th>
		<th><?php echo __('Commodity Id'); ?></th>
		<th><?php echo __('Office Id'); ?></th>
		<th><?php echo __('Staff Id'); ?></th>
		<th><?php echo __('Created'); ?></th>
		<th><?php echo __('Modified'); ?></th>
		<th><?php echo __('Delete Flg'); ?></th>
		<th><?php echo __('Deleted'); ?></th>
		<th class="actions"><?php echo __('Actions'); ?></th>
	</tr>
	<?php
		$i = 0;
		foreach ($client['CommodityReturnOffice'] as $commodityReturnOffice): ?>
		<tr>
			<td><?php echo $commodityReturnOffice['id']; ?></td>
			<td><?php echo $commodityReturnOffice['client_id']; ?></td>
			<td><?php echo $commodityReturnOffice['commodity_id']; ?></td>
			<td><?php echo $commodityReturnOffice['office_id']; ?></td>
			<td><?php echo $commodityReturnOffice['staff_id']; ?></td>
			<td><?php echo $commodityReturnOffice['created']; ?></td>
			<td><?php echo $commodityReturnOffice['modified']; ?></td>
			<td><?php echo $commodityReturnOffice['delete_flg']; ?></td>
			<td><?php echo $commodityReturnOffice['deleted']; ?></td>
			<td class="actions">
				<?php echo $this->Html->link(__('View'), array('controller' => 'commodity_return_offices', 'action' => 'view', $commodityReturnOffice['id'])); ?>
				<?php echo $this->Html->link(__('Edit'), array('controller' => 'commodity_return_offices', 'action' => 'edit', $commodityReturnOffice['id'])); ?>
				<?php echo $this->Form->postLink(__('Delete'), array('controller' => 'commodity_return_offices', 'action' => 'delete', $commodityReturnOffice['id']), null, __('Are you sure you want to delete # %s?', $commodityReturnOffice['id'])); ?>
			</td>
		</tr>
	<?php endforeach; ?>
	</table>
<?php endif; ?>

	<div class="actions">
		<ul>
			<li><?php echo $this->Html->link(__('New Commodity Return Office'), array('controller' => 'commodity_return_offices', 'action' => 'add')); ?> </li>
		</ul>
	</div>
</div>
<div class="related">
	<h3><?php echo __('Related Commodity Services'); ?></h3>
	<?php if (!empty($client['CommodityService'])): ?>
	<table cellpadding = "0" cellspacing = "0">
	<tr>
		<th><?php echo __('Id'); ?></th>
		<th><?php echo __('Client Id'); ?></th>
		<th><?php echo __('Commodity Id'); ?></th>
		<th><?php echo __('Service Id'); ?></th>
		<th><?php echo __('Staff Id'); ?></th>
		<th><?php echo __('Created'); ?></th>
		<th><?php echo __('Modified'); ?></th>
		<th><?php echo __('Delete Flg'); ?></th>
		<th><?php echo __('Deleted'); ?></th>
		<th class="actions"><?php echo __('Actions'); ?></th>
	</tr>
	<?php
		$i = 0;
		foreach ($client['CommodityService'] as $commodityService): ?>
		<tr>
			<td><?php echo $commodityService['id']; ?></td>
			<td><?php echo $commodityService['client_id']; ?></td>
			<td><?php echo $commodityService['commodity_id']; ?></td>
			<td><?php echo $commodityService['service_id']; ?></td>
			<td><?php echo $commodityService['staff_id']; ?></td>
			<td><?php echo $commodityService['created']; ?></td>
			<td><?php echo $commodityService['modified']; ?></td>
			<td><?php echo $commodityService['delete_flg']; ?></td>
			<td><?php echo $commodityService['deleted']; ?></td>
			<td class="actions">
				<?php echo $this->Html->link(__('View'), array('controller' => 'commodity_services', 'action' => 'view', $commodityService['id'])); ?>
				<?php echo $this->Html->link(__('Edit'), array('controller' => 'commodity_services', 'action' => 'edit', $commodityService['id'])); ?>
				<?php echo $this->Form->postLink(__('Delete'), array('controller' => 'commodity_services', 'action' => 'delete', $commodityService['id']), null, __('Are you sure you want to delete # %s?', $commodityService['id'])); ?>
			</td>
		</tr>
	<?php endforeach; ?>
	</table>
<?php endif; ?>

	<div class="actions">
		<ul>
			<li><?php echo $this->Html->link(__('New Commodity Service'), array('controller' => 'commodity_services', 'action' => 'add')); ?> </li>
		</ul>
	</div>
</div>
<div class="related">
	<h3><?php echo __('Related Commodity Specials'); ?></h3>
	<?php if (!empty($client['CommoditySpecial'])): ?>
	<table cellpadding = "0" cellspacing = "0">
	<tr>
		<th><?php echo __('Id'); ?></th>
		<th><?php echo __('Client Id'); ?></th>
		<th><?php echo __('Commodity Id'); ?></th>
		<th><?php echo __('Special Id'); ?></th>
		<th><?php echo __('Staff Id'); ?></th>
		<th><?php echo __('Created'); ?></th>
		<th><?php echo __('Modified'); ?></th>
		<th><?php echo __('Delete Flg'); ?></th>
		<th><?php echo __('Deleted'); ?></th>
		<th class="actions"><?php echo __('Actions'); ?></th>
	</tr>
	<?php
		$i = 0;
		foreach ($client['CommoditySpecial'] as $commoditySpecial): ?>
		<tr>
			<td><?php echo $commoditySpecial['id']; ?></td>
			<td><?php echo $commoditySpecial['client_id']; ?></td>
			<td><?php echo $commoditySpecial['commodity_id']; ?></td>
			<td><?php echo $commoditySpecial['special_id']; ?></td>
			<td><?php echo $commoditySpecial['staff_id']; ?></td>
			<td><?php echo $commoditySpecial['created']; ?></td>
			<td><?php echo $commoditySpecial['modified']; ?></td>
			<td><?php echo $commoditySpecial['delete_flg']; ?></td>
			<td><?php echo $commoditySpecial['deleted']; ?></td>
			<td class="actions">
				<?php echo $this->Html->link(__('View'), array('controller' => 'commodity_specials', 'action' => 'view', $commoditySpecial['id'])); ?>
				<?php echo $this->Html->link(__('Edit'), array('controller' => 'commodity_specials', 'action' => 'edit', $commoditySpecial['id'])); ?>
				<?php echo $this->Form->postLink(__('Delete'), array('controller' => 'commodity_specials', 'action' => 'delete', $commoditySpecial['id']), null, __('Are you sure you want to delete # %s?', $commoditySpecial['id'])); ?>
			</td>
		</tr>
	<?php endforeach; ?>
	</table>
<?php endif; ?>

	<div class="actions">
		<ul>
			<li><?php echo $this->Html->link(__('New Commodity Special'), array('controller' => 'commodity_specials', 'action' => 'add')); ?> </li>
		</ul>
	</div>
</div>
<div class="related">
	<h3><?php echo __('Related Commodity Terms'); ?></h3>
	<?php if (!empty($client['CommodityTerm'])): ?>
	<table cellpadding = "0" cellspacing = "0">
	<tr>
		<th><?php echo __('Id'); ?></th>
		<th><?php echo __('Client Id'); ?></th>
		<th><?php echo __('Commodity Id'); ?></th>
		<th><?php echo __('Available From'); ?></th>
		<th><?php echo __('Available To'); ?></th>
		<th><?php echo __('Is Deadline Hours'); ?></th>
		<th><?php echo __('Deadline Hours'); ?></th>
		<th><?php echo __('Deadline Time'); ?></th>
		<th><?php echo __('Staff Id'); ?></th>
		<th><?php echo __('Created'); ?></th>
		<th><?php echo __('Modified'); ?></th>
		<th><?php echo __('Delete Flg'); ?></th>
		<th><?php echo __('Deleted'); ?></th>
		<th class="actions"><?php echo __('Actions'); ?></th>
	</tr>
	<?php
		$i = 0;
		foreach ($client['CommodityTerm'] as $commodityTerm): ?>
		<tr>
			<td><?php echo $commodityTerm['id']; ?></td>
			<td><?php echo $commodityTerm['client_id']; ?></td>
			<td><?php echo $commodityTerm['commodity_id']; ?></td>
			<td><?php echo $commodityTerm['available_from']; ?></td>
			<td><?php echo $commodityTerm['available_to']; ?></td>
			<td><?php echo $commodityTerm['is_deadline_hours']; ?></td>
			<td><?php echo $commodityTerm['deadline_hours']; ?></td>
			<td><?php echo $commodityTerm['deadline_time']; ?></td>
			<td><?php echo $commodityTerm['staff_id']; ?></td>
			<td><?php echo $commodityTerm['created']; ?></td>
			<td><?php echo $commodityTerm['modified']; ?></td>
			<td><?php echo $commodityTerm['delete_flg']; ?></td>
			<td><?php echo $commodityTerm['deleted']; ?></td>
			<td class="actions">
				<?php echo $this->Html->link(__('View'), array('controller' => 'commodity_terms', 'action' => 'view', $commodityTerm['id'])); ?>
				<?php echo $this->Html->link(__('Edit'), array('controller' => 'commodity_terms', 'action' => 'edit', $commodityTerm['id'])); ?>
				<?php echo $this->Form->postLink(__('Delete'), array('controller' => 'commodity_terms', 'action' => 'delete', $commodityTerm['id']), null, __('Are you sure you want to delete # %s?', $commodityTerm['id'])); ?>
			</td>
		</tr>
	<?php endforeach; ?>
	</table>
<?php endif; ?>

	<div class="actions">
		<ul>
			<li><?php echo $this->Html->link(__('New Commodity Term'), array('controller' => 'commodity_terms', 'action' => 'add')); ?> </li>
		</ul>
	</div>
</div>
<div class="related">
	<h3><?php echo __('Related Contracts'); ?></h3>
	<?php if (!empty($client['Contract'])): ?>
	<table cellpadding = "0" cellspacing = "0">
	<tr>
		<th><?php echo __('Id'); ?></th>
		<th><?php echo __('Client Id'); ?></th>
		<th><?php echo __('Reservation Datetime'); ?></th>
		<th><?php echo __('Reservation Id'); ?></th>
		<th><?php echo __('Commodity Item Id'); ?></th>
		<th><?php echo __('Office Id'); ?></th>
		<th><?php echo __('Commodity Price Id'); ?></th>
		<th><?php echo __('Price Span Id'); ?></th>
		<th><?php echo __('Span Count'); ?></th>
		<th><?php echo __('Cars Count'); ?></th>
		<th><?php echo __('Total Price'); ?></th>
		<th><?php echo __('Total Tax'); ?></th>
		<th><?php echo __('Amount'); ?></th>
		<th><?php echo __('Fee Back'); ?></th>
		<th><?php echo __('Commodity Json'); ?></th>
		<th><?php echo __('Reservation Json'); ?></th>
		<th><?php echo __('Staff Id'); ?></th>
		<th><?php echo __('Created'); ?></th>
		<th><?php echo __('Modified'); ?></th>
		<th><?php echo __('Delete Flg'); ?></th>
		<th><?php echo __('Deleted'); ?></th>
		<th class="actions"><?php echo __('Actions'); ?></th>
	</tr>
	<?php
		$i = 0;
		foreach ($client['Contract'] as $contract): ?>
		<tr>
			<td><?php echo $contract['id']; ?></td>
			<td><?php echo $contract['client_id']; ?></td>
			<td><?php echo $contract['reservation_datetime']; ?></td>
			<td><?php echo $contract['reservation_id']; ?></td>
			<td><?php echo $contract['commodity_item_id']; ?></td>
			<td><?php echo $contract['office_id']; ?></td>
			<td><?php echo $contract['commodity_price_id']; ?></td>
			<td><?php echo $contract['price_span_id']; ?></td>
			<td><?php echo $contract['span_count']; ?></td>
			<td><?php echo $contract['cars_count']; ?></td>
			<td><?php echo $contract['total_price']; ?></td>
			<td><?php echo $contract['total_tax']; ?></td>
			<td><?php echo $contract['amount']; ?></td>
			<td><?php echo $contract['fee_back']; ?></td>
			<td><?php echo $contract['commodity_json']; ?></td>
			<td><?php echo $contract['reservation_json']; ?></td>
			<td><?php echo $contract['staff_id']; ?></td>
			<td><?php echo $contract['created']; ?></td>
			<td><?php echo $contract['modified']; ?></td>
			<td><?php echo $contract['delete_flg']; ?></td>
			<td><?php echo $contract['deleted']; ?></td>
			<td class="actions">
				<?php echo $this->Html->link(__('View'), array('controller' => 'contracts', 'action' => 'view', $contract['id'])); ?>
				<?php echo $this->Html->link(__('Edit'), array('controller' => 'contracts', 'action' => 'edit', $contract['id'])); ?>
				<?php echo $this->Form->postLink(__('Delete'), array('controller' => 'contracts', 'action' => 'delete', $contract['id']), null, __('Are you sure you want to delete # %s?', $contract['id'])); ?>
			</td>
		</tr>
	<?php endforeach; ?>
	</table>
<?php endif; ?>

	<div class="actions">
		<ul>
			<li><?php echo $this->Html->link(__('New Contract'), array('controller' => 'contracts', 'action' => 'add')); ?> </li>
		</ul>
	</div>
</div>
<div class="related">
	<h3><?php echo __('Related Drop Off Area Rates'); ?></h3>
	<?php if (!empty($client['DropOffAreaRate'])): ?>
	<table cellpadding = "0" cellspacing = "0">
	<tr>
		<th><?php echo __('Id'); ?></th>
		<th><?php echo __('Rent Drop Off Area Id'); ?></th>
		<th><?php echo __('Return Drop Off Area Id'); ?></th>
		<th><?php echo __('Price'); ?></th>
		<th><?php echo __('Client Id'); ?></th>
		<th><?php echo __('Delete Flg'); ?></th>
		<th><?php echo __('Created'); ?></th>
		<th><?php echo __('Modified'); ?></th>
		<th class="actions"><?php echo __('Actions'); ?></th>
	</tr>
	<?php
		$i = 0;
		foreach ($client['DropOffAreaRate'] as $dropOffAreaRate): ?>
		<tr>
			<td><?php echo $dropOffAreaRate['id']; ?></td>
			<td><?php echo $dropOffAreaRate['rent_drop_off_area_id']; ?></td>
			<td><?php echo $dropOffAreaRate['return_drop_off_area_id']; ?></td>
			<td><?php echo $dropOffAreaRate['price']; ?></td>
			<td><?php echo $dropOffAreaRate['client_id']; ?></td>
			<td><?php echo $dropOffAreaRate['delete_flg']; ?></td>
			<td><?php echo $dropOffAreaRate['created']; ?></td>
			<td><?php echo $dropOffAreaRate['modified']; ?></td>
			<td class="actions">
				<?php echo $this->Html->link(__('View'), array('controller' => 'drop_off_area_rates', 'action' => 'view', $dropOffAreaRate['id'])); ?>
				<?php echo $this->Html->link(__('Edit'), array('controller' => 'drop_off_area_rates', 'action' => 'edit', $dropOffAreaRate['id'])); ?>
				<?php echo $this->Form->postLink(__('Delete'), array('controller' => 'drop_off_area_rates', 'action' => 'delete', $dropOffAreaRate['id']), null, __('Are you sure you want to delete # %s?', $dropOffAreaRate['id'])); ?>
			</td>
		</tr>
	<?php endforeach; ?>
	</table>
<?php endif; ?>

	<div class="actions">
		<ul>
			<li><?php echo $this->Html->link(__('New Drop Off Area Rate'), array('controller' => 'drop_off_area_rates', 'action' => 'add')); ?> </li>
		</ul>
	</div>
</div>
<div class="related">
	<h3><?php echo __('Related Drop Off Areas'); ?></h3>
	<?php if (!empty($client['DropOffArea'])): ?>
	<table cellpadding = "0" cellspacing = "0">
	<tr>
		<th><?php echo __('Id'); ?></th>
		<th><?php echo __('Name'); ?></th>
		<th><?php echo __('Client Id'); ?></th>
		<th><?php echo __('Delete Flg'); ?></th>
		<th><?php echo __('Created'); ?></th>
		<th><?php echo __('Modified'); ?></th>
		<th class="actions"><?php echo __('Actions'); ?></th>
	</tr>
	<?php
		$i = 0;
		foreach ($client['DropOffArea'] as $dropOffArea): ?>
		<tr>
			<td><?php echo $dropOffArea['id']; ?></td>
			<td><?php echo $dropOffArea['name']; ?></td>
			<td><?php echo $dropOffArea['client_id']; ?></td>
			<td><?php echo $dropOffArea['delete_flg']; ?></td>
			<td><?php echo $dropOffArea['created']; ?></td>
			<td><?php echo $dropOffArea['modified']; ?></td>
			<td class="actions">
				<?php echo $this->Html->link(__('View'), array('controller' => 'drop_off_areas', 'action' => 'view', $dropOffArea['id'])); ?>
				<?php echo $this->Html->link(__('Edit'), array('controller' => 'drop_off_areas', 'action' => 'edit', $dropOffArea['id'])); ?>
				<?php echo $this->Form->postLink(__('Delete'), array('controller' => 'drop_off_areas', 'action' => 'delete', $dropOffArea['id']), null, __('Are you sure you want to delete # %s?', $dropOffArea['id'])); ?>
			</td>
		</tr>
	<?php endforeach; ?>
	</table>
<?php endif; ?>

	<div class="actions">
		<ul>
			<li><?php echo $this->Html->link(__('New Drop Off Area'), array('controller' => 'drop_off_areas', 'action' => 'add')); ?> </li>
		</ul>
	</div>
</div>
<div class="related">
	<h3><?php echo __('Related Estimates'); ?></h3>
	<?php if (!empty($client['Estimate'])): ?>
	<table cellpadding = "0" cellspacing = "0">
	<tr>
		<th><?php echo __('Id'); ?></th>
		<th><?php echo __('Client Id'); ?></th>
		<th><?php echo __('User Session Id'); ?></th>
		<th><?php echo __('Estimate Datetime'); ?></th>
		<th><?php echo __('Commodity Item Id'); ?></th>
		<th><?php echo __('Rent Datetime'); ?></th>
		<th><?php echo __('Rent Office Id'); ?></th>
		<th><?php echo __('Rent Hotel Name'); ?></th>
		<th><?php echo __('Return Datetime'); ?></th>
		<th><?php echo __('Return Office Id'); ?></th>
		<th><?php echo __('Return Hotel Name'); ?></th>
		<th><?php echo __('Price Span Id'); ?></th>
		<th><?php echo __('Span Count'); ?></th>
		<th><?php echo __('Cars Count'); ?></th>
		<th><?php echo __('Total Price'); ?></th>
		<th><?php echo __('Total Tax'); ?></th>
		<th><?php echo __('Amount'); ?></th>
		<th><?php echo __('Staff Id'); ?></th>
		<th><?php echo __('Created'); ?></th>
		<th><?php echo __('Modified'); ?></th>
		<th><?php echo __('Delete Flg'); ?></th>
		<th><?php echo __('Deleted'); ?></th>
		<th class="actions"><?php echo __('Actions'); ?></th>
	</tr>
	<?php
		$i = 0;
		foreach ($client['Estimate'] as $estimate): ?>
		<tr>
			<td><?php echo $estimate['id']; ?></td>
			<td><?php echo $estimate['client_id']; ?></td>
			<td><?php echo $estimate['user_session_id']; ?></td>
			<td><?php echo $estimate['estimate_datetime']; ?></td>
			<td><?php echo $estimate['commodity_item_id']; ?></td>
			<td><?php echo $estimate['rent_datetime']; ?></td>
			<td><?php echo $estimate['rent_office_id']; ?></td>
			<td><?php echo $estimate['rent_hotel_name']; ?></td>
			<td><?php echo $estimate['return_datetime']; ?></td>
			<td><?php echo $estimate['return_office_id']; ?></td>
			<td><?php echo $estimate['return_hotel_name']; ?></td>
			<td><?php echo $estimate['price_span_id']; ?></td>
			<td><?php echo $estimate['span_count']; ?></td>
			<td><?php echo $estimate['cars_count']; ?></td>
			<td><?php echo $estimate['total_price']; ?></td>
			<td><?php echo $estimate['total_tax']; ?></td>
			<td><?php echo $estimate['amount']; ?></td>
			<td><?php echo $estimate['staff_id']; ?></td>
			<td><?php echo $estimate['created']; ?></td>
			<td><?php echo $estimate['modified']; ?></td>
			<td><?php echo $estimate['delete_flg']; ?></td>
			<td><?php echo $estimate['deleted']; ?></td>
			<td class="actions">
				<?php echo $this->Html->link(__('View'), array('controller' => 'estimates', 'action' => 'view', $estimate['id'])); ?>
				<?php echo $this->Html->link(__('Edit'), array('controller' => 'estimates', 'action' => 'edit', $estimate['id'])); ?>
				<?php echo $this->Form->postLink(__('Delete'), array('controller' => 'estimates', 'action' => 'delete', $estimate['id']), null, __('Are you sure you want to delete # %s?', $estimate['id'])); ?>
			</td>
		</tr>
	<?php endforeach; ?>
	</table>
<?php endif; ?>

	<div class="actions">
		<ul>
			<li><?php echo $this->Html->link(__('New Estimate'), array('controller' => 'estimates', 'action' => 'add')); ?> </li>
		</ul>
	</div>
</div>
<div class="related">
	<h3><?php echo __('Related Hotel Pickups'); ?></h3>
	<?php if (!empty($client['HotelPickup'])): ?>
	<table cellpadding = "0" cellspacing = "0">
	<tr>
		<th><?php echo __('Id'); ?></th>
		<th><?php echo __('Landmark Id'); ?></th>
		<th><?php echo __('Office Id'); ?></th>
		<th><?php echo __('Client Id'); ?></th>
		<th><?php echo __('Send Flg'); ?></th>
		<th><?php echo __('Welcome Flg'); ?></th>
		<th><?php echo __('From Time'); ?></th>
		<th><?php echo __('To Time'); ?></th>
		<th><?php echo __('Application Flg'); ?></th>
		<th><?php echo __('Staff Id'); ?></th>
		<th><?php echo __('Created'); ?></th>
		<th><?php echo __('Modified'); ?></th>
		<th><?php echo __('Delete Flg'); ?></th>
		<th class="actions"><?php echo __('Actions'); ?></th>
	</tr>
	<?php
		$i = 0;
		foreach ($client['HotelPickup'] as $hotelPickup): ?>
		<tr>
			<td><?php echo $hotelPickup['id']; ?></td>
			<td><?php echo $hotelPickup['landmark_id']; ?></td>
			<td><?php echo $hotelPickup['office_id']; ?></td>
			<td><?php echo $hotelPickup['client_id']; ?></td>
			<td><?php echo $hotelPickup['send_flg']; ?></td>
			<td><?php echo $hotelPickup['welcome_flg']; ?></td>
			<td><?php echo $hotelPickup['from_time']; ?></td>
			<td><?php echo $hotelPickup['to_time']; ?></td>
			<td><?php echo $hotelPickup['application_flg']; ?></td>
			<td><?php echo $hotelPickup['staff_id']; ?></td>
			<td><?php echo $hotelPickup['created']; ?></td>
			<td><?php echo $hotelPickup['modified']; ?></td>
			<td><?php echo $hotelPickup['delete_flg']; ?></td>
			<td class="actions">
				<?php echo $this->Html->link(__('View'), array('controller' => 'hotel_pickups', 'action' => 'view', $hotelPickup['id'])); ?>
				<?php echo $this->Html->link(__('Edit'), array('controller' => 'hotel_pickups', 'action' => 'edit', $hotelPickup['id'])); ?>
				<?php echo $this->Form->postLink(__('Delete'), array('controller' => 'hotel_pickups', 'action' => 'delete', $hotelPickup['id']), null, __('Are you sure you want to delete # %s?', $hotelPickup['id'])); ?>
			</td>
		</tr>
	<?php endforeach; ?>
	</table>
<?php endif; ?>

	<div class="actions">
		<ul>
			<li><?php echo $this->Html->link(__('New Hotel Pickup'), array('controller' => 'hotel_pickups', 'action' => 'add')); ?> </li>
		</ul>
	</div>
</div>
<div class="related">
	<h3><?php echo __('Related Hotels'); ?></h3>
	<?php if (!empty($client['Hotel'])): ?>
	<table cellpadding = "0" cellspacing = "0">
	<tr>
		<th><?php echo __('Id'); ?></th>
		<th><?php echo __('Landmark Id'); ?></th>
		<th><?php echo __('Office Id'); ?></th>
		<th><?php echo __('Client Id'); ?></th>
		<th><?php echo __('Allocation Flg'); ?></th>
		<th><?php echo __('Return Flg'); ?></th>
		<th><?php echo __('From Time'); ?></th>
		<th><?php echo __('To Time'); ?></th>
		<th><?php echo __('Application Flg'); ?></th>
		<th><?php echo __('Staff Id'); ?></th>
		<th><?php echo __('Created'); ?></th>
		<th><?php echo __('Modified'); ?></th>
		<th><?php echo __('Delete Flg'); ?></th>
		<th class="actions"><?php echo __('Actions'); ?></th>
	</tr>
	<?php
		$i = 0;
		foreach ($client['Hotel'] as $hotel): ?>
		<tr>
			<td><?php echo $hotel['id']; ?></td>
			<td><?php echo $hotel['landmark_id']; ?></td>
			<td><?php echo $hotel['office_id']; ?></td>
			<td><?php echo $hotel['client_id']; ?></td>
			<td><?php echo $hotel['allocation_flg']; ?></td>
			<td><?php echo $hotel['return_flg']; ?></td>
			<td><?php echo $hotel['from_time']; ?></td>
			<td><?php echo $hotel['to_time']; ?></td>
			<td><?php echo $hotel['application_flg']; ?></td>
			<td><?php echo $hotel['staff_id']; ?></td>
			<td><?php echo $hotel['created']; ?></td>
			<td><?php echo $hotel['modified']; ?></td>
			<td><?php echo $hotel['delete_flg']; ?></td>
			<td class="actions">
				<?php echo $this->Html->link(__('View'), array('controller' => 'hotels', 'action' => 'view', $hotel['id'])); ?>
				<?php echo $this->Html->link(__('Edit'), array('controller' => 'hotels', 'action' => 'edit', $hotel['id'])); ?>
				<?php echo $this->Form->postLink(__('Delete'), array('controller' => 'hotels', 'action' => 'delete', $hotel['id']), null, __('Are you sure you want to delete # %s?', $hotel['id'])); ?>
			</td>
		</tr>
	<?php endforeach; ?>
	</table>
<?php endif; ?>

	<div class="actions">
		<ul>
			<li><?php echo $this->Html->link(__('New Hotel'), array('controller' => 'hotels', 'action' => 'add')); ?> </li>
		</ul>
	</div>
</div>
<div class="related">
	<h3><?php echo __('Related Late Night Fees'); ?></h3>
	<?php if (!empty($client['LateNightFee'])): ?>
	<table cellpadding = "0" cellspacing = "0">
	<tr>
		<th><?php echo __('Id'); ?></th>
		<th><?php echo __('Target Time From'); ?></th>
		<th><?php echo __('Target Time To'); ?></th>
		<th><?php echo __('Price'); ?></th>
		<th><?php echo __('Price Addition Flg'); ?></th>
		<th><?php echo __('Client Id'); ?></th>
		<th><?php echo __('Delete Flg'); ?></th>
		<th><?php echo __('Created'); ?></th>
		<th><?php echo __('Modified'); ?></th>
		<th class="actions"><?php echo __('Actions'); ?></th>
	</tr>
	<?php
		$i = 0;
		foreach ($client['LateNightFee'] as $lateNightFee): ?>
		<tr>
			<td><?php echo $lateNightFee['id']; ?></td>
			<td><?php echo $lateNightFee['target_time_from']; ?></td>
			<td><?php echo $lateNightFee['target_time_to']; ?></td>
			<td><?php echo $lateNightFee['price']; ?></td>
			<td><?php echo $lateNightFee['price_addition_flg']; ?></td>
			<td><?php echo $lateNightFee['client_id']; ?></td>
			<td><?php echo $lateNightFee['delete_flg']; ?></td>
			<td><?php echo $lateNightFee['created']; ?></td>
			<td><?php echo $lateNightFee['modified']; ?></td>
			<td class="actions">
				<?php echo $this->Html->link(__('View'), array('controller' => 'late_night_fees', 'action' => 'view', $lateNightFee['id'])); ?>
				<?php echo $this->Html->link(__('Edit'), array('controller' => 'late_night_fees', 'action' => 'edit', $lateNightFee['id'])); ?>
				<?php echo $this->Form->postLink(__('Delete'), array('controller' => 'late_night_fees', 'action' => 'delete', $lateNightFee['id']), null, __('Are you sure you want to delete # %s?', $lateNightFee['id'])); ?>
			</td>
		</tr>
	<?php endforeach; ?>
	</table>
<?php endif; ?>

	<div class="actions">
		<ul>
			<li><?php echo $this->Html->link(__('New Late Night Fee'), array('controller' => 'late_night_fees', 'action' => 'add')); ?> </li>
		</ul>
	</div>
</div>
<div class="related">
	<h3><?php echo __('Related Office Areas'); ?></h3>
	<?php if (!empty($client['OfficeArea'])): ?>
	<table cellpadding = "0" cellspacing = "0">
	<tr>
		<th><?php echo __('Id'); ?></th>
		<th><?php echo __('Client Id'); ?></th>
		<th><?php echo __('Office Id'); ?></th>
		<th><?php echo __('Area Id'); ?></th>
		<th><?php echo __('Staff Id'); ?></th>
		<th><?php echo __('Created'); ?></th>
		<th><?php echo __('Modified'); ?></th>
		<th><?php echo __('Delete Flg'); ?></th>
		<th><?php echo __('Deleted'); ?></th>
		<th class="actions"><?php echo __('Actions'); ?></th>
	</tr>
	<?php
		$i = 0;
		foreach ($client['OfficeArea'] as $officeArea): ?>
		<tr>
			<td><?php echo $officeArea['id']; ?></td>
			<td><?php echo $officeArea['client_id']; ?></td>
			<td><?php echo $officeArea['office_id']; ?></td>
			<td><?php echo $officeArea['area_id']; ?></td>
			<td><?php echo $officeArea['staff_id']; ?></td>
			<td><?php echo $officeArea['created']; ?></td>
			<td><?php echo $officeArea['modified']; ?></td>
			<td><?php echo $officeArea['delete_flg']; ?></td>
			<td><?php echo $officeArea['deleted']; ?></td>
			<td class="actions">
				<?php echo $this->Html->link(__('View'), array('controller' => 'office_areas', 'action' => 'view', $officeArea['id'])); ?>
				<?php echo $this->Html->link(__('Edit'), array('controller' => 'office_areas', 'action' => 'edit', $officeArea['id'])); ?>
				<?php echo $this->Form->postLink(__('Delete'), array('controller' => 'office_areas', 'action' => 'delete', $officeArea['id']), null, __('Are you sure you want to delete # %s?', $officeArea['id'])); ?>
			</td>
		</tr>
	<?php endforeach; ?>
	</table>
<?php endif; ?>

	<div class="actions">
		<ul>
			<li><?php echo $this->Html->link(__('New Office Area'), array('controller' => 'office_areas', 'action' => 'add')); ?> </li>
		</ul>
	</div>
</div>
<div class="related">
	<h3><?php echo __('Related Office Guides'); ?></h3>
	<?php if (!empty($client['OfficeGuide'])): ?>
	<table cellpadding = "0" cellspacing = "0">
	<tr>
		<th><?php echo __('Id'); ?></th>
		<th><?php echo __('Client Id'); ?></th>
		<th><?php echo __('Office Id'); ?></th>
		<th><?php echo __('Content'); ?></th>
		<th><?php echo __('Main Image'); ?></th>
		<th><?php echo __('Top Image'); ?></th>
		<th><?php echo __('Under Image'); ?></th>
		<th><?php echo __('Staff Id'); ?></th>
		<th><?php echo __('Created'); ?></th>
		<th><?php echo __('Modified'); ?></th>
		<th><?php echo __('Delete Flg'); ?></th>
		<th><?php echo __('Deleted'); ?></th>
		<th class="actions"><?php echo __('Actions'); ?></th>
	</tr>
	<?php
		$i = 0;
		foreach ($client['OfficeGuide'] as $officeGuide): ?>
		<tr>
			<td><?php echo $officeGuide['id']; ?></td>
			<td><?php echo $officeGuide['client_id']; ?></td>
			<td><?php echo $officeGuide['office_id']; ?></td>
			<td><?php echo $officeGuide['content']; ?></td>
			<td><?php echo $officeGuide['main_image']; ?></td>
			<td><?php echo $officeGuide['top_image']; ?></td>
			<td><?php echo $officeGuide['under_image']; ?></td>
			<td><?php echo $officeGuide['staff_id']; ?></td>
			<td><?php echo $officeGuide['created']; ?></td>
			<td><?php echo $officeGuide['modified']; ?></td>
			<td><?php echo $officeGuide['delete_flg']; ?></td>
			<td><?php echo $officeGuide['deleted']; ?></td>
			<td class="actions">
				<?php echo $this->Html->link(__('View'), array('controller' => 'office_guides', 'action' => 'view', $officeGuide['id'])); ?>
				<?php echo $this->Html->link(__('Edit'), array('controller' => 'office_guides', 'action' => 'edit', $officeGuide['id'])); ?>
				<?php echo $this->Form->postLink(__('Delete'), array('controller' => 'office_guides', 'action' => 'delete', $officeGuide['id']), null, __('Are you sure you want to delete # %s?', $officeGuide['id'])); ?>
			</td>
		</tr>
	<?php endforeach; ?>
	</table>
<?php endif; ?>

	<div class="actions">
		<ul>
			<li><?php echo $this->Html->link(__('New Office Guide'), array('controller' => 'office_guides', 'action' => 'add')); ?> </li>
		</ul>
	</div>
</div>
<div class="related">
	<h3><?php echo __('Related Office Photos Introductions'); ?></h3>
	<?php if (!empty($client['OfficePhotosIntroduction'])): ?>
	<table cellpadding = "0" cellspacing = "0">
	<tr>
		<th><?php echo __('Id'); ?></th>
		<th><?php echo __('Client Id'); ?></th>
		<th><?php echo __('Office Id'); ?></th>
		<th><?php echo __('Number'); ?></th>
		<th><?php echo __('Name'); ?></th>
		<th><?php echo __('Text'); ?></th>
		<th><?php echo __('Created'); ?></th>
		<th class="actions"><?php echo __('Actions'); ?></th>
	</tr>
	<?php
		$i = 0;
		foreach ($client['OfficePhotosIntroduction'] as $officePhotosIntroduction): ?>
		<tr>
			<td><?php echo $officePhotosIntroduction['id']; ?></td>
			<td><?php echo $officePhotosIntroduction['client_id']; ?></td>
			<td><?php echo $officePhotosIntroduction['office_id']; ?></td>
			<td><?php echo $officePhotosIntroduction['number']; ?></td>
			<td><?php echo $officePhotosIntroduction['name']; ?></td>
			<td><?php echo $officePhotosIntroduction['text']; ?></td>
			<td><?php echo $officePhotosIntroduction['created']; ?></td>
			<td class="actions">
				<?php echo $this->Html->link(__('View'), array('controller' => 'office_photos_introductions', 'action' => 'view', $officePhotosIntroduction['id'])); ?>
				<?php echo $this->Html->link(__('Edit'), array('controller' => 'office_photos_introductions', 'action' => 'edit', $officePhotosIntroduction['id'])); ?>
				<?php echo $this->Form->postLink(__('Delete'), array('controller' => 'office_photos_introductions', 'action' => 'delete', $officePhotosIntroduction['id']), null, __('Are you sure you want to delete # %s?', $officePhotosIntroduction['id'])); ?>
			</td>
		</tr>
	<?php endforeach; ?>
	</table>
<?php endif; ?>

	<div class="actions">
		<ul>
			<li><?php echo $this->Html->link(__('New Office Photos Introduction'), array('controller' => 'office_photos_introductions', 'action' => 'add')); ?> </li>
		</ul>
	</div>
</div>
<div class="related">
	<h3><?php echo __('Related Office Review Star Averages'); ?></h3>
	<?php if (!empty($client['OfficeReviewStarAverage'])): ?>
	<table cellpadding = "0" cellspacing = "0">
	<tr>
		<th><?php echo __('Id'); ?></th>
		<th><?php echo __('Client Id'); ?></th>
		<th><?php echo __('Office Id'); ?></th>
		<th><?php echo __('Client Star Average'); ?></th>
		<th><?php echo __('Question1 Star Average'); ?></th>
		<th><?php echo __('Question2 Star Average'); ?></th>
		<th><?php echo __('Question3 Star Average'); ?></th>
		<th><?php echo __('Question4 Star Average'); ?></th>
		<th><?php echo __('Question5 Star Average'); ?></th>
		<th><?php echo __('Review Count'); ?></th>
		<th><?php echo __('Star Count'); ?></th>
		<th><?php echo __('Created'); ?></th>
		<th><?php echo __('Modified'); ?></th>
		<th class="actions"><?php echo __('Actions'); ?></th>
	</tr>
	<?php
		$i = 0;
		foreach ($client['OfficeReviewStarAverage'] as $officeReviewStarAverage): ?>
		<tr>
			<td><?php echo $officeReviewStarAverage['id']; ?></td>
			<td><?php echo $officeReviewStarAverage['client_id']; ?></td>
			<td><?php echo $officeReviewStarAverage['office_id']; ?></td>
			<td><?php echo $officeReviewStarAverage['client_star_average']; ?></td>
			<td><?php echo $officeReviewStarAverage['question1_star_average']; ?></td>
			<td><?php echo $officeReviewStarAverage['question2_star_average']; ?></td>
			<td><?php echo $officeReviewStarAverage['question3_star_average']; ?></td>
			<td><?php echo $officeReviewStarAverage['question4_star_average']; ?></td>
			<td><?php echo $officeReviewStarAverage['question5_star_average']; ?></td>
			<td><?php echo $officeReviewStarAverage['review_count']; ?></td>
			<td><?php echo $officeReviewStarAverage['star_count']; ?></td>
			<td><?php echo $officeReviewStarAverage['created']; ?></td>
			<td><?php echo $officeReviewStarAverage['modified']; ?></td>
			<td class="actions">
				<?php echo $this->Html->link(__('View'), array('controller' => 'office_review_star_averages', 'action' => 'view', $officeReviewStarAverage['id'])); ?>
				<?php echo $this->Html->link(__('Edit'), array('controller' => 'office_review_star_averages', 'action' => 'edit', $officeReviewStarAverage['id'])); ?>
				<?php echo $this->Form->postLink(__('Delete'), array('controller' => 'office_review_star_averages', 'action' => 'delete', $officeReviewStarAverage['id']), null, __('Are you sure you want to delete # %s?', $officeReviewStarAverage['id'])); ?>
			</td>
		</tr>
	<?php endforeach; ?>
	</table>
<?php endif; ?>

	<div class="actions">
		<ul>
			<li><?php echo $this->Html->link(__('New Office Review Star Average'), array('controller' => 'office_review_star_averages', 'action' => 'add')); ?> </li>
		</ul>
	</div>
</div>
<div class="related">
	<h3><?php echo __('Related Office Reviews'); ?></h3>
	<?php if (!empty($client['OfficeReview'])): ?>
	<table cellpadding = "0" cellspacing = "0">
	<tr>
		<th><?php echo __('Id'); ?></th>
		<th><?php echo __('Client Id'); ?></th>
		<th><?php echo __('Office Id'); ?></th>
		<th><?php echo __('Review Id'); ?></th>
		<th><?php echo __('Star Average'); ?></th>
		<th><?php echo __('Sort'); ?></th>
		<th><?php echo __('Created'); ?></th>
		<th><?php echo __('Modified'); ?></th>
		<th class="actions"><?php echo __('Actions'); ?></th>
	</tr>
	<?php
		$i = 0;
		foreach ($client['OfficeReview'] as $officeReview): ?>
		<tr>
			<td><?php echo $officeReview['id']; ?></td>
			<td><?php echo $officeReview['client_id']; ?></td>
			<td><?php echo $officeReview['office_id']; ?></td>
			<td><?php echo $officeReview['review_id']; ?></td>
			<td><?php echo $officeReview['star_average']; ?></td>
			<td><?php echo $officeReview['sort']; ?></td>
			<td><?php echo $officeReview['created']; ?></td>
			<td><?php echo $officeReview['modified']; ?></td>
			<td class="actions">
				<?php echo $this->Html->link(__('View'), array('controller' => 'office_reviews', 'action' => 'view', $officeReview['id'])); ?>
				<?php echo $this->Html->link(__('Edit'), array('controller' => 'office_reviews', 'action' => 'edit', $officeReview['id'])); ?>
				<?php echo $this->Form->postLink(__('Delete'), array('controller' => 'office_reviews', 'action' => 'delete', $officeReview['id']), null, __('Are you sure you want to delete # %s?', $officeReview['id'])); ?>
			</td>
		</tr>
	<?php endforeach; ?>
	</table>
<?php endif; ?>

	<div class="actions">
		<ul>
			<li><?php echo $this->Html->link(__('New Office Review'), array('controller' => 'office_reviews', 'action' => 'add')); ?> </li>
		</ul>
	</div>
</div>
<div class="related">
	<h3><?php echo __('Related Office Stock Groups'); ?></h3>
	<?php if (!empty($client['OfficeStockGroup'])): ?>
	<table cellpadding = "0" cellspacing = "0">
	<tr>
		<th><?php echo __('Id'); ?></th>
		<th><?php echo __('Client Id'); ?></th>
		<th><?php echo __('Stock Group Id'); ?></th>
		<th><?php echo __('Office Id'); ?></th>
		<th><?php echo __('Staff Id'); ?></th>
		<th><?php echo __('Created'); ?></th>
		<th><?php echo __('Modified'); ?></th>
		<th><?php echo __('Delete Flg'); ?></th>
		<th><?php echo __('Deleted'); ?></th>
		<th class="actions"><?php echo __('Actions'); ?></th>
	</tr>
	<?php
		$i = 0;
		foreach ($client['OfficeStockGroup'] as $officeStockGroup): ?>
		<tr>
			<td><?php echo $officeStockGroup['id']; ?></td>
			<td><?php echo $officeStockGroup['client_id']; ?></td>
			<td><?php echo $officeStockGroup['stock_group_id']; ?></td>
			<td><?php echo $officeStockGroup['office_id']; ?></td>
			<td><?php echo $officeStockGroup['staff_id']; ?></td>
			<td><?php echo $officeStockGroup['created']; ?></td>
			<td><?php echo $officeStockGroup['modified']; ?></td>
			<td><?php echo $officeStockGroup['delete_flg']; ?></td>
			<td><?php echo $officeStockGroup['deleted']; ?></td>
			<td class="actions">
				<?php echo $this->Html->link(__('View'), array('controller' => 'office_stock_groups', 'action' => 'view', $officeStockGroup['id'])); ?>
				<?php echo $this->Html->link(__('Edit'), array('controller' => 'office_stock_groups', 'action' => 'edit', $officeStockGroup['id'])); ?>
				<?php echo $this->Form->postLink(__('Delete'), array('controller' => 'office_stock_groups', 'action' => 'delete', $officeStockGroup['id']), null, __('Are you sure you want to delete # %s?', $officeStockGroup['id'])); ?>
			</td>
		</tr>
	<?php endforeach; ?>
	</table>
<?php endif; ?>

	<div class="actions">
		<ul>
			<li><?php echo $this->Html->link(__('New Office Stock Group'), array('controller' => 'office_stock_groups', 'action' => 'add')); ?> </li>
		</ul>
	</div>
</div>
<div class="related">
	<h3><?php echo __('Related Offices'); ?></h3>
	<?php if (!empty($client['Office'])): ?>
	<table cellpadding = "0" cellspacing = "0">
	<tr>
		<th><?php echo __('Id'); ?></th>
		<th><?php echo __('Client Id'); ?></th>
		<th><?php echo __('Sort'); ?></th>
		<th><?php echo __('Name'); ?></th>
		<th><?php echo __('Area Id'); ?></th>
		<th><?php echo __('Office Image'); ?></th>
		<th><?php echo __('Office Image Comment'); ?></th>
		<th><?php echo __('Office Image Sub1'); ?></th>
		<th><?php echo __('Office Image Sub1 Comment'); ?></th>
		<th><?php echo __('Office Image Sub2'); ?></th>
		<th><?php echo __('Office Image Sub2 Comment'); ?></th>
		<th><?php echo __('Office Image Sub3'); ?></th>
		<th><?php echo __('Office Image Sub3 Comment'); ?></th>
		<th><?php echo __('Office Movie Tag'); ?></th>
		<th><?php echo __('Staff Image'); ?></th>
		<th><?php echo __('Staff Image Comment'); ?></th>
		<th><?php echo __('Office Hours From'); ?></th>
		<th><?php echo __('Office Hours To'); ?></th>
		<th><?php echo __('Office Hours Remark'); ?></th>
		<th><?php echo __('Office Holiday Remark'); ?></th>
		<th><?php echo __('Tel'); ?></th>
		<th><?php echo __('Reserve Mail'); ?></th>
		<th><?php echo __('Address'); ?></th>
		<th><?php echo __('Access'); ?></th>
		<th><?php echo __('Station Landmark Id'); ?></th>
		<th><?php echo __('Accept Rent'); ?></th>
		<th><?php echo __('Accept Return'); ?></th>
		<th><?php echo __('Provide Charge Exc Tax'); ?></th>
		<th><?php echo __('Provide Charge Tax'); ?></th>
		<th><?php echo __('Provide Charge'); ?></th>
		<th><?php echo __('Provide Time From'); ?></th>
		<th><?php echo __('Provide Time To'); ?></th>
		<th><?php echo __('Drop Off Charge Exc Tax'); ?></th>
		<th><?php echo __('Drop Off Charge Tax'); ?></th>
		<th><?php echo __('Drop Off Charge'); ?></th>
		<th><?php echo __('Drop Off Time From'); ?></th>
		<th><?php echo __('Drop Off Time To'); ?></th>
		<th><?php echo __('Can Pickup'); ?></th>
		<th><?php echo __('Hotel Dispatch Flg'); ?></th>
		<th><?php echo __('Hotel Pickup Flg'); ?></th>
		<th><?php echo __('Loan Airport Flg'); ?></th>
		<th><?php echo __('Loan Airport From'); ?></th>
		<th><?php echo __('Loan Airport To'); ?></th>
		<th><?php echo __('Is Provide'); ?></th>
		<th><?php echo __('Is Drop'); ?></th>
		<th><?php echo __('Latitude'); ?></th>
		<th><?php echo __('Longitude'); ?></th>
		<th><?php echo __('Image Relative Url'); ?></th>
		<th><?php echo __('Hotel Pdf'); ?></th>
		<th><?php echo __('Is Top'); ?></th>
		<th><?php echo __('Seo'); ?></th>
		<th><?php echo __('Travel Time Airport'); ?></th>
		<th><?php echo __('Area Drop Off Id'); ?></th>
		<th><?php echo __('Late Night Fee Flg'); ?></th>
		<th><?php echo __('Airport Id'); ?></th>
		<th><?php echo __('Bullet Train Id'); ?></th>
		<th><?php echo __('Staff Id'); ?></th>
		<th><?php echo __('Created'); ?></th>
		<th><?php echo __('Modified'); ?></th>
		<th><?php echo __('Delete Flg'); ?></th>
		<th><?php echo __('Deleted'); ?></th>
		<th class="actions"><?php echo __('Actions'); ?></th>
	</tr>
	<?php
		$i = 0;
		foreach ($client['Office'] as $office):
		?>
		<tr>
			<td><?php echo $office['id']; ?></td>
			<td><?php echo $office['client_id']; ?></td>
			<td><?php echo $office['sort']; ?></td>
			<td><?php echo $office['name']; ?></td>
			<td><?php echo $office['area_id']; ?></td>
			<td><?php echo $office['office_image']; ?></td>
			<td><?php echo $office['office_image_comment']; ?></td>
			<td><?php echo $office['office_image_sub1']; ?></td>
			<td><?php echo $office['office_image_sub1_comment']; ?></td>
			<td><?php echo $office['office_image_sub2']; ?></td>
			<td><?php echo $office['office_image_sub2_comment']; ?></td>
			<td><?php echo $office['office_image_sub3']; ?></td>
			<td><?php echo $office['office_image_sub3_comment']; ?></td>
			<td><?php echo $office['office_movie_tag']; ?></td>
			<td><?php echo $office['staff_image']; ?></td>
			<td><?php echo $office['staff_image_comment']; ?></td>
			<td><?php echo $office['office_hours_from']; ?></td>
			<td><?php echo $office['office_hours_to']; ?></td>
			<td><?php echo $office['office_hours_remark']; ?></td>
			<td><?php echo $office['office_holiday_remark']; ?></td>
			<td><?php echo $office['tel']; ?></td>
			<td><?php echo $office['reserve_mail']; ?></td>
			<td><?php echo $office['address']; ?></td>
			<td><?php echo $office['access']; ?></td>
			<td><?php echo $office['station_landmark_id']; ?></td>
			<td><?php echo $office['accept_rent']; ?></td>
			<td><?php echo $office['accept_return']; ?></td>
			<td><?php echo $office['provide_charge_exc_tax']; ?></td>
			<td><?php echo $office['provide_charge_tax']; ?></td>
			<td><?php echo $office['provide_charge']; ?></td>
			<td><?php echo $office['provide_time_from']; ?></td>
			<td><?php echo $office['provide_time_to']; ?></td>
			<td><?php echo $office['drop_off_charge_exc_tax']; ?></td>
			<td><?php echo $office['drop_off_charge_tax']; ?></td>
			<td><?php echo $office['drop_off_charge']; ?></td>
			<td><?php echo $office['drop_off_time_from']; ?></td>
			<td><?php echo $office['drop_off_time_to']; ?></td>
			<td><?php echo $office['can_pickup']; ?></td>
			<td><?php echo $office['hotel_dispatch_flg']; ?></td>
			<td><?php echo $office['hotel_pickup_flg']; ?></td>
			<td><?php echo $office['loan_airport_flg']; ?></td>
			<td><?php echo $office['loan_airport_from']; ?></td>
			<td><?php echo $office['loan_airport_to']; ?></td>
			<td><?php echo $office['is_provide']; ?></td>
			<td><?php echo $office['is_drop']; ?></td>
			<td><?php echo $office['latitude']; ?></td>
			<td><?php echo $office['longitude']; ?></td>
			<td><?php echo $office['image_relative_url']; ?></td>
			<td><?php echo $office['hotel_pdf']; ?></td>
			<td><?php echo $office['is_top']; ?></td>
			<td><?php echo $office['seo']; ?></td>
			<td><?php echo $office['travel_time_airport']; ?></td>
			<td><?php echo $office['area_drop_off_id']; ?></td>
			<td><?php echo $office['late_night_fee_flg']; ?></td>
			<td><?php echo $office['airport_id']; ?></td>
			<td><?php echo $office['bullet_train_id']; ?></td>
			<td><?php echo $office['staff_id']; ?></td>
			<td><?php echo $office['created']; ?></td>
			<td><?php echo $office['modified']; ?></td>
			<td><?php echo $office['delete_flg']; ?></td>
			<td><?php echo $office['deleted']; ?></td>
			<td class="actions">
				<?php echo $this->Html->link(__('View'), array('controller' => 'offices', 'action' => 'view', $office['id'])); ?>
				<?php echo $this->Html->link(__('Edit'), array('controller' => 'offices', 'action' => 'edit', $office['id'])); ?>
				<?php echo $this->Form->postLink(__('Delete'), array('controller' => 'offices', 'action' => 'delete', $office['id']), null, __('Are you sure you want to delete # %s?', $office['id'])); ?>
			</td>
		</tr>
	<?php endforeach; ?>
	</table>
<?php endif; ?>

	<div class="actions">
		<ul>
			<li><?php echo $this->Html->link(__('New Office'), array('controller' => 'offices', 'action' => 'add')); ?> </li>
		</ul>
	</div>
</div>
<div class="related">
	<h3><?php echo __('Related Price Rank Calendars'); ?></h3>
	<?php if (!empty($client['PriceRankCalendar'])): ?>
	<table cellpadding = "0" cellspacing = "0">
	<tr>
		<th><?php echo __('Id'); ?></th>
		<th><?php echo __('Client Id'); ?></th>
		<th><?php echo __('Price Rank List Id'); ?></th>
		<th><?php echo __('Price Rank Date'); ?></th>
		<th><?php echo __('Price Rank Id'); ?></th>
		<th><?php echo __('Staff Id'); ?></th>
		<th><?php echo __('Created'); ?></th>
		<th><?php echo __('Modified'); ?></th>
		<th><?php echo __('Delete Flg'); ?></th>
		<th><?php echo __('Deleted'); ?></th>
		<th class="actions"><?php echo __('Actions'); ?></th>
	</tr>
	<?php
		$i = 0;
		foreach ($client['PriceRankCalendar'] as $priceRankCalendar): ?>
		<tr>
			<td><?php echo $priceRankCalendar['id']; ?></td>
			<td><?php echo $priceRankCalendar['client_id']; ?></td>
			<td><?php echo $priceRankCalendar['price_rank_list_id']; ?></td>
			<td><?php echo $priceRankCalendar['price_rank_date']; ?></td>
			<td><?php echo $priceRankCalendar['price_rank_id']; ?></td>
			<td><?php echo $priceRankCalendar['staff_id']; ?></td>
			<td><?php echo $priceRankCalendar['created']; ?></td>
			<td><?php echo $priceRankCalendar['modified']; ?></td>
			<td><?php echo $priceRankCalendar['delete_flg']; ?></td>
			<td><?php echo $priceRankCalendar['deleted']; ?></td>
			<td class="actions">
				<?php echo $this->Html->link(__('View'), array('controller' => 'price_rank_calendars', 'action' => 'view', $priceRankCalendar['id'])); ?>
				<?php echo $this->Html->link(__('Edit'), array('controller' => 'price_rank_calendars', 'action' => 'edit', $priceRankCalendar['id'])); ?>
				<?php echo $this->Form->postLink(__('Delete'), array('controller' => 'price_rank_calendars', 'action' => 'delete', $priceRankCalendar['id']), null, __('Are you sure you want to delete # %s?', $priceRankCalendar['id'])); ?>
			</td>
		</tr>
	<?php endforeach; ?>
	</table>
<?php endif; ?>

	<div class="actions">
		<ul>
			<li><?php echo $this->Html->link(__('New Price Rank Calendar'), array('controller' => 'price_rank_calendars', 'action' => 'add')); ?> </li>
		</ul>
	</div>
</div>
<div class="related">
	<h3><?php echo __('Related Price Ranks'); ?></h3>
	<?php if (!empty($client['PriceRank'])): ?>
	<table cellpadding = "0" cellspacing = "0">
	<tr>
		<th><?php echo __('Id'); ?></th>
		<th><?php echo __('Client Id'); ?></th>
		<th><?php echo __('Is Default'); ?></th>
		<th><?php echo __('Name'); ?></th>
		<th><?php echo __('Is Campaign'); ?></th>
		<th><?php echo __('Staff Id'); ?></th>
		<th><?php echo __('Created'); ?></th>
		<th><?php echo __('Modified'); ?></th>
		<th><?php echo __('Delete Flg'); ?></th>
		<th><?php echo __('Deleted'); ?></th>
		<th class="actions"><?php echo __('Actions'); ?></th>
	</tr>
	<?php
		$i = 0;
		foreach ($client['PriceRank'] as $priceRank): ?>
		<tr>
			<td><?php echo $priceRank['id']; ?></td>
			<td><?php echo $priceRank['client_id']; ?></td>
			<td><?php echo $priceRank['is_default']; ?></td>
			<td><?php echo $priceRank['name']; ?></td>
			<td><?php echo $priceRank['is_campaign']; ?></td>
			<td><?php echo $priceRank['staff_id']; ?></td>
			<td><?php echo $priceRank['created']; ?></td>
			<td><?php echo $priceRank['modified']; ?></td>
			<td><?php echo $priceRank['delete_flg']; ?></td>
			<td><?php echo $priceRank['deleted']; ?></td>
			<td class="actions">
				<?php echo $this->Html->link(__('View'), array('controller' => 'price_ranks', 'action' => 'view', $priceRank['id'])); ?>
				<?php echo $this->Html->link(__('Edit'), array('controller' => 'price_ranks', 'action' => 'edit', $priceRank['id'])); ?>
				<?php echo $this->Form->postLink(__('Delete'), array('controller' => 'price_ranks', 'action' => 'delete', $priceRank['id']), null, __('Are you sure you want to delete # %s?', $priceRank['id'])); ?>
			</td>
		</tr>
	<?php endforeach; ?>
	</table>
<?php endif; ?>

	<div class="actions">
		<ul>
			<li><?php echo $this->Html->link(__('New Price Rank'), array('controller' => 'price_ranks', 'action' => 'add')); ?> </li>
		</ul>
	</div>
</div>
<div class="related">
	<h3><?php echo __('Related Privilege Prices'); ?></h3>
	<?php if (!empty($client['PrivilegePrice'])): ?>
	<table cellpadding = "0" cellspacing = "0">
	<tr>
		<th><?php echo __('Id'); ?></th>
		<th><?php echo __('Client Id'); ?></th>
		<th><?php echo __('Price'); ?></th>
		<th><?php echo __('Span Count'); ?></th>
		<th><?php echo __('Privilege Id'); ?></th>
		<th><?php echo __('Staff Id'); ?></th>
		<th><?php echo __('Created'); ?></th>
		<th><?php echo __('Modified'); ?></th>
		<th><?php echo __('Delete Flg'); ?></th>
		<th><?php echo __('Deleted'); ?></th>
		<th class="actions"><?php echo __('Actions'); ?></th>
	</tr>
	<?php
		$i = 0;
		foreach ($client['PrivilegePrice'] as $privilegePrice): ?>
		<tr>
			<td><?php echo $privilegePrice['id']; ?></td>
			<td><?php echo $privilegePrice['client_id']; ?></td>
			<td><?php echo $privilegePrice['price']; ?></td>
			<td><?php echo $privilegePrice['span_count']; ?></td>
			<td><?php echo $privilegePrice['privilege_id']; ?></td>
			<td><?php echo $privilegePrice['staff_id']; ?></td>
			<td><?php echo $privilegePrice['created']; ?></td>
			<td><?php echo $privilegePrice['modified']; ?></td>
			<td><?php echo $privilegePrice['delete_flg']; ?></td>
			<td><?php echo $privilegePrice['deleted']; ?></td>
			<td class="actions">
				<?php echo $this->Html->link(__('View'), array('controller' => 'privilege_prices', 'action' => 'view', $privilegePrice['id'])); ?>
				<?php echo $this->Html->link(__('Edit'), array('controller' => 'privilege_prices', 'action' => 'edit', $privilegePrice['id'])); ?>
				<?php echo $this->Form->postLink(__('Delete'), array('controller' => 'privilege_prices', 'action' => 'delete', $privilegePrice['id']), null, __('Are you sure you want to delete # %s?', $privilegePrice['id'])); ?>
			</td>
		</tr>
	<?php endforeach; ?>
	</table>
<?php endif; ?>

	<div class="actions">
		<ul>
			<li><?php echo $this->Html->link(__('New Privilege Price'), array('controller' => 'privilege_prices', 'action' => 'add')); ?> </li>
		</ul>
	</div>
</div>
<div class="related">
	<h3><?php echo __('Related Privileges'); ?></h3>
	<?php if (!empty($client['Privilege'])): ?>
	<table cellpadding = "0" cellspacing = "0">
	<tr>
		<th><?php echo __('Id'); ?></th>
		<th><?php echo __('Client Id'); ?></th>
		<th><?php echo __('Name'); ?></th>
		<th><?php echo __('Image Relative Url'); ?></th>
		<th><?php echo __('Description'); ?></th>
		<th><?php echo __('Excluding Tax'); ?></th>
		<th><?php echo __('Tax'); ?></th>
		<th><?php echo __('Price'); ?></th>
		<th><?php echo __('Maximum'); ?></th>
		<th><?php echo __('Unit Name'); ?></th>
		<th><?php echo __('Option Flg'); ?></th>
		<th><?php echo __('Shape Flg'); ?></th>
		<th><?php echo __('Staff Id'); ?></th>
		<th><?php echo __('Created'); ?></th>
		<th><?php echo __('Modified'); ?></th>
		<th><?php echo __('Delete Flg'); ?></th>
		<th><?php echo __('Deleted'); ?></th>
		<th class="actions"><?php echo __('Actions'); ?></th>
	</tr>
	<?php
		$i = 0;
		foreach ($client['Privilege'] as $privilege): ?>
		<tr>
			<td><?php echo $privilege['id']; ?></td>
			<td><?php echo $privilege['client_id']; ?></td>
			<td><?php echo $privilege['name']; ?></td>
			<td><?php echo $privilege['image_relative_url']; ?></td>
			<td><?php echo $privilege['description']; ?></td>
			<td><?php echo $privilege['excluding_tax']; ?></td>
			<td><?php echo $privilege['tax']; ?></td>
			<td><?php echo $privilege['price']; ?></td>
			<td><?php echo $privilege['maximum']; ?></td>
			<td><?php echo $privilege['unit_name']; ?></td>
			<td><?php echo $privilege['option_flg']; ?></td>
			<td><?php echo $privilege['shape_flg']; ?></td>
			<td><?php echo $privilege['staff_id']; ?></td>
			<td><?php echo $privilege['created']; ?></td>
			<td><?php echo $privilege['modified']; ?></td>
			<td><?php echo $privilege['delete_flg']; ?></td>
			<td><?php echo $privilege['deleted']; ?></td>
			<td class="actions">
				<?php echo $this->Html->link(__('View'), array('controller' => 'privileges', 'action' => 'view', $privilege['id'])); ?>
				<?php echo $this->Html->link(__('Edit'), array('controller' => 'privileges', 'action' => 'edit', $privilege['id'])); ?>
				<?php echo $this->Form->postLink(__('Delete'), array('controller' => 'privileges', 'action' => 'delete', $privilege['id']), null, __('Are you sure you want to delete # %s?', $privilege['id'])); ?>
			</td>
		</tr>
	<?php endforeach; ?>
	</table>
<?php endif; ?>

	<div class="actions">
		<ul>
			<li><?php echo $this->Html->link(__('New Privilege'), array('controller' => 'privileges', 'action' => 'add')); ?> </li>
		</ul>
	</div>
</div>
<div class="related">
	<h3><?php echo __('Related Reservations'); ?></h3>
	<?php if (!empty($client['Reservation'])): ?>
	<table cellpadding = "0" cellspacing = "0">
	<tr>
		<th><?php echo __('Id'); ?></th>
		<th><?php echo __('Client Id'); ?></th>
		<th><?php echo __('User Session Id'); ?></th>
		<th><?php echo __('User Agent'); ?></th>
		<th><?php echo __('Reservation Datetime'); ?></th>
		<th><?php echo __('Reservation Status Id'); ?></th>
		<th><?php echo __('Reservation Key'); ?></th>
		<th><?php echo __('Reservation Hash'); ?></th>
		<th><?php echo __('Estimate Id'); ?></th>
		<th><?php echo __('Commodity Item Id'); ?></th>
		<th><?php echo __('Rent Datetime'); ?></th>
		<th><?php echo __('Rent Office Id'); ?></th>
		<th><?php echo __('Rent Hotel Name'); ?></th>
		<th><?php echo __('Return Datetime'); ?></th>
		<th><?php echo __('Return Office Id'); ?></th>
		<th><?php echo __('Return Hotel Name'); ?></th>
		<th><?php echo __('Rent Hotelharbor Name'); ?></th>
		<th><?php echo __('Return Hotelharbor Name'); ?></th>
		<th><?php echo __('Loan Airport Flg'); ?></th>
		<th><?php echo __('Adults Count'); ?></th>
		<th><?php echo __('Children Count'); ?></th>
		<th><?php echo __('Infants Count'); ?></th>
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
		<th><?php echo __('Is Send Mail'); ?></th>
		<th><?php echo __('Leisure Mail Flg'); ?></th>
		<th><?php echo __('Tel'); ?></th>
		<th><?php echo __('Prefecture Id'); ?></th>
		<th><?php echo __('Need Pickup'); ?></th>
		<th><?php echo __('Arrival Airline Id'); ?></th>
		<th><?php echo __('Arrival Flight Number'); ?></th>
		<th><?php echo __('Airline Hour'); ?></th>
		<th><?php echo __('Airline Minute'); ?></th>
		<th><?php echo __('Sent To Enduser'); ?></th>
		<th><?php echo __('Sent To Client'); ?></th>
		<th><?php echo __('Mail Status'); ?></th>
		<th><?php echo __('Cancel Flg'); ?></th>
		<th><?php echo __('Cancel Datetime'); ?></th>
		<th><?php echo __('Cancel Contact Method Id'); ?></th>
		<th><?php echo __('Cancel Staff Id'); ?></th>
		<th><?php echo __('Cancel Remark'); ?></th>
		<th><?php echo __('Cancel Reason Id'); ?></th>
		<th><?php echo __('Staff Id'); ?></th>
		<th><?php echo __('Created'); ?></th>
		<th><?php echo __('Modified'); ?></th>
		<th><?php echo __('Delete Flg'); ?></th>
		<th><?php echo __('Deleted'); ?></th>
		<th class="actions"><?php echo __('Actions'); ?></th>
	</tr>
	<?php
		$i = 0;
		foreach ($client['Reservation'] as $reservation): ?>
		<tr>
			<td><?php echo $reservation['id']; ?></td>
			<td><?php echo $reservation['client_id']; ?></td>
			<td><?php echo $reservation['user_session_id']; ?></td>
			<td><?php echo $reservation['user_agent']; ?></td>
			<td><?php echo $reservation['reservation_datetime']; ?></td>
			<td><?php echo $reservation['reservation_status_id']; ?></td>
			<td><?php echo $reservation['reservation_key']; ?></td>
			<td><?php echo $reservation['reservation_hash']; ?></td>
			<td><?php echo $reservation['estimate_id']; ?></td>
			<td><?php echo $reservation['commodity_item_id']; ?></td>
			<td><?php echo $reservation['rent_datetime']; ?></td>
			<td><?php echo $reservation['rent_office_id']; ?></td>
			<td><?php echo $reservation['rent_hotel_name']; ?></td>
			<td><?php echo $reservation['return_datetime']; ?></td>
			<td><?php echo $reservation['return_office_id']; ?></td>
			<td><?php echo $reservation['return_hotel_name']; ?></td>
			<td><?php echo $reservation['rent_hotelharbor_name']; ?></td>
			<td><?php echo $reservation['return_hotelharbor_name']; ?></td>
			<td><?php echo $reservation['loan_airport_flg']; ?></td>
			<td><?php echo $reservation['adults_count']; ?></td>
			<td><?php echo $reservation['children_count']; ?></td>
			<td><?php echo $reservation['infants_count']; ?></td>
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
			<td><?php echo $reservation['is_send_mail']; ?></td>
			<td><?php echo $reservation['leisure_mail_flg']; ?></td>
			<td><?php echo $reservation['tel']; ?></td>
			<td><?php echo $reservation['prefecture_id']; ?></td>
			<td><?php echo $reservation['need_pickup']; ?></td>
			<td><?php echo $reservation['arrival_airline_id']; ?></td>
			<td><?php echo $reservation['arrival_flight_number']; ?></td>
			<td><?php echo $reservation['airline_hour']; ?></td>
			<td><?php echo $reservation['airline_minute']; ?></td>
			<td><?php echo $reservation['sent_to_enduser']; ?></td>
			<td><?php echo $reservation['sent_to_client']; ?></td>
			<td><?php echo $reservation['mail_status']; ?></td>
			<td><?php echo $reservation['cancel_flg']; ?></td>
			<td><?php echo $reservation['cancel_datetime']; ?></td>
			<td><?php echo $reservation['cancel_contact_method_id']; ?></td>
			<td><?php echo $reservation['cancel_staff_id']; ?></td>
			<td><?php echo $reservation['cancel_remark']; ?></td>
			<td><?php echo $reservation['cancel_reason_id']; ?></td>
			<td><?php echo $reservation['staff_id']; ?></td>
			<td><?php echo $reservation['created']; ?></td>
			<td><?php echo $reservation['modified']; ?></td>
			<td><?php echo $reservation['delete_flg']; ?></td>
			<td><?php echo $reservation['deleted']; ?></td>
			<td class="actions">
				<?php echo $this->Html->link(__('View'), array('controller' => 'reservations', 'action' => 'view', $reservation['id'])); ?>
				<?php echo $this->Html->link(__('Edit'), array('controller' => 'reservations', 'action' => 'edit', $reservation['id'])); ?>
				<?php echo $this->Form->postLink(__('Delete'), array('controller' => 'reservations', 'action' => 'delete', $reservation['id']), null, __('Are you sure you want to delete # %s?', $reservation['id'])); ?>
			</td>
		</tr>
	<?php endforeach; ?>
	</table>
<?php endif; ?>

	<div class="actions">
		<ul>
			<li><?php echo $this->Html->link(__('New Reservation'), array('controller' => 'reservations', 'action' => 'add')); ?> </li>
		</ul>
	</div>
</div>
<div class="related">
	<h3><?php echo __('Related Reviews'); ?></h3>
	<?php if (!empty($client['Review'])): ?>
	<table cellpadding = "0" cellspacing = "0">
	<tr>
		<th><?php echo __('Id'); ?></th>
		<th><?php echo __('Client Id'); ?></th>
		<th><?php echo __('Office Id'); ?></th>
		<th><?php echo __('Reservation Id'); ?></th>
		<th><?php echo __('Answer'); ?></th>
		<th><?php echo __('Contents Json'); ?></th>
		<th><?php echo __('Pick Up Flg'); ?></th>
		<th><?php echo __('Office Pick Up Flg'); ?></th>
		<th><?php echo __('View Flg'); ?></th>
		<th><?php echo __('Rent Datetime'); ?></th>
		<th><?php echo __('Star Average'); ?></th>
		<th><?php echo __('Sort'); ?></th>
		<th><?php echo __('Created'); ?></th>
		<th><?php echo __('Modified'); ?></th>
		<th class="actions"><?php echo __('Actions'); ?></th>
	</tr>
	<?php
		$i = 0;
		foreach ($client['Review'] as $review): ?>
		<tr>
			<td><?php echo $review['id']; ?></td>
			<td><?php echo $review['client_id']; ?></td>
			<td><?php echo $review['office_id']; ?></td>
			<td><?php echo $review['reservation_id']; ?></td>
			<td><?php echo $review['answer']; ?></td>
			<td><?php echo $review['contents_json']; ?></td>
			<td><?php echo $review['pick_up_flg']; ?></td>
			<td><?php echo $review['office_pick_up_flg']; ?></td>
			<td><?php echo $review['view_flg']; ?></td>
			<td><?php echo $review['rent_datetime']; ?></td>
			<td><?php echo $review['star_average']; ?></td>
			<td><?php echo $review['sort']; ?></td>
			<td><?php echo $review['created']; ?></td>
			<td><?php echo $review['modified']; ?></td>
			<td class="actions">
				<?php echo $this->Html->link(__('View'), array('controller' => 'reviews', 'action' => 'view', $review['id'])); ?>
				<?php echo $this->Html->link(__('Edit'), array('controller' => 'reviews', 'action' => 'edit', $review['id'])); ?>
				<?php echo $this->Form->postLink(__('Delete'), array('controller' => 'reviews', 'action' => 'delete', $review['id']), null, __('Are you sure you want to delete # %s?', $review['id'])); ?>
			</td>
		</tr>
	<?php endforeach; ?>
	</table>
<?php endif; ?>

	<div class="actions">
		<ul>
			<li><?php echo $this->Html->link(__('New Review'), array('controller' => 'reviews', 'action' => 'add')); ?> </li>
		</ul>
	</div>
</div>
<div class="related">
	<h3><?php echo __('Related Staffs'); ?></h3>
	<?php if (!empty($client['Staff'])): ?>
	<table cellpadding = "0" cellspacing = "0">
	<tr>
		<th><?php echo __('Id'); ?></th>
		<th><?php echo __('Client Id'); ?></th>
		<th><?php echo __('Name'); ?></th>
		<th><?php echo __('Username'); ?></th>
		<th><?php echo __('Password'); ?></th>
		<th><?php echo __('Is Client Admin'); ?></th>
		<th><?php echo __('Staff Id'); ?></th>
		<th><?php echo __('Created'); ?></th>
		<th><?php echo __('Modified'); ?></th>
		<th><?php echo __('Delete Flg'); ?></th>
		<th><?php echo __('Deleted'); ?></th>
		<th class="actions"><?php echo __('Actions'); ?></th>
	</tr>
	<?php
		$i = 0;
		foreach ($client['Staff'] as $staff): ?>
		<tr>
			<td><?php echo $staff['id']; ?></td>
			<td><?php echo $staff['client_id']; ?></td>
			<td><?php echo $staff['name']; ?></td>
			<td><?php echo $staff['username']; ?></td>
			<td><?php echo $staff['password']; ?></td>
			<td><?php echo $staff['is_client_admin']; ?></td>
			<td><?php echo $staff['staff_id']; ?></td>
			<td><?php echo $staff['created']; ?></td>
			<td><?php echo $staff['modified']; ?></td>
			<td><?php echo $staff['delete_flg']; ?></td>
			<td><?php echo $staff['deleted']; ?></td>
			<td class="actions">
				<?php echo $this->Html->link(__('View'), array('controller' => 'staffs', 'action' => 'view', $staff['id'])); ?>
				<?php echo $this->Html->link(__('Edit'), array('controller' => 'staffs', 'action' => 'edit', $staff['id'])); ?>
				<?php echo $this->Form->postLink(__('Delete'), array('controller' => 'staffs', 'action' => 'delete', $staff['id']), null, __('Are you sure you want to delete # %s?', $staff['id'])); ?>
			</td>
		</tr>
	<?php endforeach; ?>
	</table>
<?php endif; ?>

	<div class="actions">
		<ul>
			<li><?php echo $this->Html->link(__('New Staff'), array('controller' => 'staffs', 'action' => 'add')); ?> </li>
		</ul>
	</div>
</div>
<div class="related">
	<h3><?php echo __('Related Statistics'); ?></h3>
	<?php if (!empty($client['Statistic'])): ?>
	<table cellpadding = "0" cellspacing = "0">
	<tr>
		<th><?php echo __('Id'); ?></th>
		<th><?php echo __('Client Id'); ?></th>
		<th><?php echo __('Reservation Id'); ?></th>
		<th><?php echo __('Rent Office Id'); ?></th>
		<th><?php echo __('Commodity Item Id'); ?></th>
		<th><?php echo __('Rent Datetime'); ?></th>
		<th><?php echo __('Reservation Datetime'); ?></th>
		<th><?php echo __('Reservation Status Id'); ?></th>
		<th><?php echo __('Price Span Id'); ?></th>
		<th><?php echo __('Span Count'); ?></th>
		<th><?php echo __('Price'); ?></th>
		<th><?php echo __('Cancel Flg'); ?></th>
		<th><?php echo __('Statistic Date'); ?></th>
		<th><?php echo __('Staff Id'); ?></th>
		<th><?php echo __('Created'); ?></th>
		<th><?php echo __('Modified'); ?></th>
		<th><?php echo __('Delete Flg'); ?></th>
		<th><?php echo __('Deleted'); ?></th>
		<th class="actions"><?php echo __('Actions'); ?></th>
	</tr>
	<?php
		$i = 0;
		foreach ($client['Statistic'] as $statistic): ?>
		<tr>
			<td><?php echo $statistic['id']; ?></td>
			<td><?php echo $statistic['client_id']; ?></td>
			<td><?php echo $statistic['reservation_id']; ?></td>
			<td><?php echo $statistic['rent_office_id']; ?></td>
			<td><?php echo $statistic['commodity_item_id']; ?></td>
			<td><?php echo $statistic['rent_datetime']; ?></td>
			<td><?php echo $statistic['reservation_datetime']; ?></td>
			<td><?php echo $statistic['reservation_status_id']; ?></td>
			<td><?php echo $statistic['price_span_id']; ?></td>
			<td><?php echo $statistic['span_count']; ?></td>
			<td><?php echo $statistic['price']; ?></td>
			<td><?php echo $statistic['cancel_flg']; ?></td>
			<td><?php echo $statistic['statistic_date']; ?></td>
			<td><?php echo $statistic['staff_id']; ?></td>
			<td><?php echo $statistic['created']; ?></td>
			<td><?php echo $statistic['modified']; ?></td>
			<td><?php echo $statistic['delete_flg']; ?></td>
			<td><?php echo $statistic['deleted']; ?></td>
			<td class="actions">
				<?php echo $this->Html->link(__('View'), array('controller' => 'statistics', 'action' => 'view', $statistic['id'])); ?>
				<?php echo $this->Html->link(__('Edit'), array('controller' => 'statistics', 'action' => 'edit', $statistic['id'])); ?>
				<?php echo $this->Form->postLink(__('Delete'), array('controller' => 'statistics', 'action' => 'delete', $statistic['id']), null, __('Are you sure you want to delete # %s?', $statistic['id'])); ?>
			</td>
		</tr>
	<?php endforeach; ?>
	</table>
<?php endif; ?>

	<div class="actions">
		<ul>
			<li><?php echo $this->Html->link(__('New Statistic'), array('controller' => 'statistics', 'action' => 'add')); ?> </li>
		</ul>
	</div>
</div>
<div class="related">
	<h3><?php echo __('Related Stock Groups'); ?></h3>
	<?php if (!empty($client['StockGroup'])): ?>
	<table cellpadding = "0" cellspacing = "0">
	<tr>
		<th><?php echo __('Id'); ?></th>
		<th><?php echo __('Client Id'); ?></th>
		<th><?php echo __('Name'); ?></th>
		<th><?php echo __('Staff Id'); ?></th>
		<th><?php echo __('Sort'); ?></th>
		<th><?php echo __('Created'); ?></th>
		<th><?php echo __('Modified'); ?></th>
		<th><?php echo __('Delete Flg'); ?></th>
		<th><?php echo __('Deleted'); ?></th>
		<th class="actions"><?php echo __('Actions'); ?></th>
	</tr>
	<?php
		$i = 0;
		foreach ($client['StockGroup'] as $stockGroup): ?>
		<tr>
			<td><?php echo $stockGroup['id']; ?></td>
			<td><?php echo $stockGroup['client_id']; ?></td>
			<td><?php echo $stockGroup['name']; ?></td>
			<td><?php echo $stockGroup['staff_id']; ?></td>
			<td><?php echo $stockGroup['sort']; ?></td>
			<td><?php echo $stockGroup['created']; ?></td>
			<td><?php echo $stockGroup['modified']; ?></td>
			<td><?php echo $stockGroup['delete_flg']; ?></td>
			<td><?php echo $stockGroup['deleted']; ?></td>
			<td class="actions">
				<?php echo $this->Html->link(__('View'), array('controller' => 'stock_groups', 'action' => 'view', $stockGroup['id'])); ?>
				<?php echo $this->Html->link(__('Edit'), array('controller' => 'stock_groups', 'action' => 'edit', $stockGroup['id'])); ?>
				<?php echo $this->Form->postLink(__('Delete'), array('controller' => 'stock_groups', 'action' => 'delete', $stockGroup['id']), null, __('Are you sure you want to delete # %s?', $stockGroup['id'])); ?>
			</td>
		</tr>
	<?php endforeach; ?>
	</table>
<?php endif; ?>

	<div class="actions">
		<ul>
			<li><?php echo $this->Html->link(__('New Stock Group'), array('controller' => 'stock_groups', 'action' => 'add')); ?> </li>
		</ul>
	</div>
</div>
<div class="related">
	<h3><?php echo __('Related Updated Tables'); ?></h3>
	<?php if (!empty($client['UpdatedTable'])): ?>
	<table cellpadding = "0" cellspacing = "0">
	<tr>
		<th><?php echo __('Id'); ?></th>
		<th><?php echo __('Table Id'); ?></th>
		<th><?php echo __('Updated Datetime'); ?></th>
		<th><?php echo __('Operation Id'); ?></th>
		<th><?php echo __('Category'); ?></th>
		<th><?php echo __('Content'); ?></th>
		<th><?php echo __('Url'); ?></th>
		<th><?php echo __('Client Id'); ?></th>
		<th><?php echo __('Staff Id'); ?></th>
		<th><?php echo __('Created'); ?></th>
		<th><?php echo __('Modified'); ?></th>
		<th><?php echo __('Delete Flg'); ?></th>
		<th><?php echo __('Deleted'); ?></th>
		<th class="actions"><?php echo __('Actions'); ?></th>
	</tr>
	<?php
		$i = 0;
		foreach ($client['UpdatedTable'] as $updatedTable): ?>
		<tr>
			<td><?php echo $updatedTable['id']; ?></td>
			<td><?php echo $updatedTable['table_id']; ?></td>
			<td><?php echo $updatedTable['updated_datetime']; ?></td>
			<td><?php echo $updatedTable['operation_id']; ?></td>
			<td><?php echo $updatedTable['category']; ?></td>
			<td><?php echo $updatedTable['content']; ?></td>
			<td><?php echo $updatedTable['url']; ?></td>
			<td><?php echo $updatedTable['client_id']; ?></td>
			<td><?php echo $updatedTable['staff_id']; ?></td>
			<td><?php echo $updatedTable['created']; ?></td>
			<td><?php echo $updatedTable['modified']; ?></td>
			<td><?php echo $updatedTable['delete_flg']; ?></td>
			<td><?php echo $updatedTable['deleted']; ?></td>
			<td class="actions">
				<?php echo $this->Html->link(__('View'), array('controller' => 'updated_tables', 'action' => 'view', $updatedTable['id'])); ?>
				<?php echo $this->Html->link(__('Edit'), array('controller' => 'updated_tables', 'action' => 'edit', $updatedTable['id'])); ?>
				<?php echo $this->Form->postLink(__('Delete'), array('controller' => 'updated_tables', 'action' => 'delete', $updatedTable['id']), null, __('Are you sure you want to delete # %s?', $updatedTable['id'])); ?>
			</td>
		</tr>
	<?php endforeach; ?>
	</table>
<?php endif; ?>

	<div class="actions">
		<ul>
			<li><?php echo $this->Html->link(__('New Updated Table'), array('controller' => 'updated_tables', 'action' => 'add')); ?> </li>
		</ul>
	</div>
</div>
