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

$cakeDescription = __d('cake_dev', 'CakePHP: the rapid development php framework');
?>
<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
	<?php echo $this->Html->charset(); ?>
	<title>
		レンタカー社内管理画面 : <?php echo $title_for_layout; ?>
	</title>
	<?php
		echo $this->Html->meta(array('name'=>'robots','content'=>'noindex'));
		echo $this->Html->meta(array('name'=>'robots','content'=>'nofollow'));
		echo $this->Html->meta(array('name'=>'robots','content'=>'noarchive'));

		echo $this->Html->css('bootstrap.min');
		echo $this->Html->css('bootstrap-responsive.min');
		echo $this->Html->css('custom');

		echo $this->Html->script('jquery-1.9.1');
		echo $this->Html->script('bootstrap.min');
		echo $this->Html->script('jquery-ui-1.10.2.custom.min');
		echo $this->Html->script('custom');

		echo $this->fetch('meta');
		echo $this->fetch('css');
		echo $this->fetch('script');
	?>
</head>
<body style="padding-top: 60px;">
	<div id="container" class="container-fluid">
		<div class="navbar navbar-fixed-top">
			<div class="navbar-inner" style="background-color:#009944;">
				<div class="container">
					<ul class="nav">
						<li class="divider-vertical" style="border-color:#009944;"><?php echo $this->Html->link('レンタカー社内管理画面', '/',array('taget'=>'_blank','class'=>'brand')); ?></li>
						<li class="divider-vertical" style="border-color:#009944;"><?php echo $this->Html->link('ログアウト', '/users/login',array('taget'=>'_blank','class'=>'brand')); ?></li>
					</ul>
					<div style="color:#fff;font-size:16px;float:right;padding-top:15px;">
						スタッフ名:　<?php if(!empty($cdata['name'])) { echo $cdata['name']; } ?>
					</div>
				</div>
			</div>
		</div>

	<div class="container-fluid">

		<div class="row-fluid">
		<?php if(empty($sideLock)){?>
			<div class="span2 sidebar-menu">
				<?php echo $this->element('sidebar');?>
			</div>
		<?php }?>
			<div class="span10 offset2 main-column">
				<?php echo $this->Session->flash(); ?>
				<?php echo $this->fetch('content'); ?>
			</div>
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
