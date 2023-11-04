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
	<title></title>
	<?php
		echo $this->Html->css('bootstrap.min');
		echo $this->Html->css('custom');
		echo $this->Html->css('bootstrap-responsive');
		echo $this->Html->css('bootstrap-responsive.min');
		echo $this->Html->css('jquery-ui-1.10.2.custom');

		echo $this->Html->script('jquery-1.9.1');
		echo $this->Html->script('bootstrap');
		echo $this->Html->script('bootstrap.min');
		echo $this->Html->script('jquery-ui-1.10.2.custom.min');
		echo $this->Html->script('custom');

		echo $this->fetch('meta');
		echo $this->fetch('css');
		echo $this->fetch('script');
	?>
</head>
				<div class="span10">
					<?php echo $this->fetch('content'); ?>
				</div>
</body>
</html>
