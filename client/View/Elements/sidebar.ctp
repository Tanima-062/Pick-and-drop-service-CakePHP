<div class="actions">
	<h3 id="list" style="background-color: #178ACC; color:#FFF; text-align:center;">MENU</h3>
	<div class="well" style="padding: 8px 0;">
		<ul class="nav nav-list">
			<?php
				foreach($pages as $page) {
			?>
			<li class="nav-header"><?php echo $page['category_name']; ?></li>
			<?php
				foreach ($page['page'] as $link) {
					$newTab = '_self';
					if (!empty($link['new_tab_flg'])) {
						$newTab = '_blank"';
					}
			?>
			<li><?php echo $this->Html->link($link['name'], '/'.$link['url'], array('target' => $newTab)); ?></li>
			<?php
				}
			?>

			<li class="divider"></li>
			<?php } ?>

		</ul>
	</div>
</div>
