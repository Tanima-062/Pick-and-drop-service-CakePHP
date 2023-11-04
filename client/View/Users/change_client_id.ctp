<?php if (IS_PRODUCTION && $isExistAlert) { ?>
<div class="alert alert-error">
<?php foreach ((array)$unsetCategories as $option_name => $client_name) { ?>
<?=$client_name?>の<?=$option_name;?>のカテゴリが未設定です。<br>
<?php } ?>
<?php foreach ((array)$unsetOfficeUrls as $office_name => $client_name) { ?>
<?=$client_name?>の<?=$office_name;?>のリンク用URLが未設定です。<br>
<?php } ?>
<?php foreach ((array)$unsetOfficeCityIds as $office_name => $client_name) { ?>
<?=$client_name?>の<?=$office_name;?>の郵便番号または対応市区町村が未設定です。<br>
<?php } ?>
<?php foreach ((array)$unsetSippCodes as $commodity_name => $client_name) { ?>
<?=$client_name?>の<?=$commodity_name;?>のSIPPコードが未設定です。<br>
<?php } ?>
</div>
<?php } ?>
<div class="staffs form">
<?php echo $this->Form->create('ClientData');?>
	<h2><?php echo 'ログインするクライアントを選択してください'; ?></h2>
	<div class="error"><?php echo $this->Session->flash('auth'); ?></div>
	<?php echo $this->Form->input('client',$clientFormOptions); ?>
	<?php echo $this->Form->submit('ログイン', array('class' => 'btn btn-large btn-primary')); ?>
<?php echo $this->Form->end();?>
</div>
