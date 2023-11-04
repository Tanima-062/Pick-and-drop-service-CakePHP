<?php
/**
 *
 * PHP 5
 *
 * CakePHP(tm) : Rapid Development Framework (http://cakephp.org)
 * Copyright 2005-2012, Cake Software Foundation, Inc. (http://cakefoundation.org)
 *
 * Licensed under The MIT License
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright     Copyright 2005-2012, Cake Software Foundation, Inc. (http://cakefoundation.org)
 * @link          http://cakephp.org CakePHP(tm) Project
 * @package       Cake.View.Layouts
 * @since         CakePHP(tm) v 0.10.0.1076
 * @license       MIT License (http://www.opensource.org/licenses/mit-license.php)
 */

?>
<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
	<?php echo $this->Html->charset(); ?>
	<title>skyticketレンタカー管理画面 : <?php echo $clientData['Client']['name'];?> : <?php echo $title_for_layout; ?></title>
	<?php
		echo $this->Html->meta(array('name'=>'robots','content'=>'noindex'));
		echo $this->Html->meta(array('name'=>'robots','content'=>'nofollow'));
		echo $this->Html->meta(array('name'=>'robots','content'=>'noarchive'));

		echo $this->Html->css('bootstrap.min');
		echo $this->Html->css('bootstrap-responsive');
		echo $this->Html->css('bootstrap-responsive.min');
		echo $this->Html->css('jquery-ui-1.10.2.custom');
		echo $this->Html->css('custom');

		echo $this->Html->css('bootstrap-tagsinput');
		echo $this->Html->css('bootstrap-tagsinput-typeahead');

		echo $this->Html->script('jquery-1.9.1');
		echo $this->Html->script('bootstrap');
		echo $this->Html->script('bootstrap.min');
		echo $this->Html->script('jquery-ui.min.js');
		echo $this->Html->script('jquery-ui-1.10.2.custom.min');

		echo $this->Html->script('bootstrap-tagsinput.min');
		echo $this->Html->script('typeahead-min');

		echo $this->Html->script('custom');

		echo $this->fetch('meta');
		echo $this->fetch('css');
		echo $this->fetch('script');

	?>

	<style>
		/* 在庫管理 日付選択時 */
		.is-dateSelected {
			background-color: #6ff !important;
		}
	</style>
</head>
<body>
	<div id="container" class="container-fluid">
		<div class="navbar navbar-inverse navbar-fixed-top">
			<div style="background-color:#009944;">
				<div class="container" style="width: auto;">
				<?php echo $this->Html->link('skyticketレンタカー管理画面', '/',array('taget'=>'_blank','class'=>'brand')); ?>
				<div class="nav-collapse">
					<ul class="nav">
						<?php if(!empty($clientData) && $clientData['is_system_admin'] == 1) {?>
						<li style="margin-left:30px;"><?php echo $this->Html->link('クライアント切替', '/users/changeClientId',array('taget'=>'_blank','class'=>'brand')); ?></li>
						<?php } ?>
						<li style="margin-left:30px;"><?php echo $this->Html->link('ログアウト', '/users/logout',array('taget'=>'_blank','class'=>'brand')); ?></li>
					</ul>
				</div>
				<div style="color:#fff;font-size:16px;float:right;padding:3px;">
					<?php if(!empty($clientData)) {?>
						<?php echo $clientData['Client']['name'];?><br>
						スタッフ名:　<?php echo $clientData['name'];?>　
					<?php }?>
				</div>
				</div>
			</div>
		</div>
		<br><br><br>
		<div class="container-fluid">

			<div class="row-fluid">
					<?php
					if(!empty($clientData['Client']['id']) && !empty($pages)) {
					?>

						<div class="span2 sidebar-menu">
							<?php echo $this->element('sidebar'); ?>
						</div>
						<div class="span10 offset2 main-column">
							<?php echo $this->Session->flash(); ?>
							<?php echo $this->fetch('content'); ?>
						</div>
					<?php
					} else {
					?>
						<div class="span10">
							<?php echo $this->Session->flash(); ?>
							<?php echo $this->fetch('content'); ?>
						</div>
					<?php
					}
					?>
			</div>
		</div>

<!--
		<div id="content">
			<?php //echo $this->Session->flash(); ?>

			<?php //echo $this->fetch('content'); ?>
			<?php //echo $this->element('admin_sidebar');?>
		</div>
 -->



		<div id="footer">
<!-- webbot  bot="HTMLMarkup" startspan -->
<!-- GeoTrust QuickSSL [tm] Smart  Icon tag. Do not edit. -->
<script language="javascript" type="text/javascript" src="//smarticon.geotrust.com/si.js"></script>
<!-- end  GeoTrust Smart Icon tag -->
<!-- webbot  bot="HTMLMarkup" endspan -->
		</div>
	</div>
	<?php echo $this->element('sql_dump'); ?>
</body>
</html>
