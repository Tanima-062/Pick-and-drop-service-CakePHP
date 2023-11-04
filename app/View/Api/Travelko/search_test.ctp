<!DOCTYPE HTML>
<html lang="ja">
<head>
	<meta charset="UTF-8">
	<title>トラベルコ - プラン検索APIテスト</title>
</head>
<body>
<div style="margin:10px;">
<?php echo $this->Form->create(false, array(
	'controller' => 'travelko',
	'action' => 'api_plan_list',
	'type' => 'get',
	'target' => '_blank',
)); ?>
<br>
<div class="input select"><label for="rental_area">貸出場所 </label>
<select name="rental_area" id="rental_area">
<?php echo $optionsText; ?></select>
</div>
<br>
<?php echo $this->Form->input('rental_area_type', $rentalAreaTypeOptions); ?>
<br>
<div class="input select"><label for="return_area">返却場所 </label>
<select name="return_area" id="return_area">
<?php echo $optionsText; ?></select>
</div>
<br>
<?php echo $this->Form->input('return_area_type', $returnAreaTypeOptions); ?>
<br>
<?php echo $this->Form->input('rental_time', $rentalTimeTypeOptions); ?>
<br>
<?php echo $this->Form->input('return_time', $returnTimeTypeOptions); ?>
<br>
<?php echo $this->Form->button('検索'); ?>
<?php echo $this->Form->hidden('debug', array('value' => '1')); ?>
<?php echo $this->Form->end(); ?>
</div>
<a href="/rentacar/travelko/api_shop_list?debug=1" target="_blank">店舗検索APIテスト</a>
</body>
</html>
