<!-- パンくずリスト -->
<?php 
	if(isset($progress_arr) && !$fromRentacarClient) {
		$page_identifier = mb_strtolower($this->name);
		if($page_identifier == 'searches' || $page_identifier == 'plan'){
		 $progress_class = 'stepnav';
		}else{
		 $progress_class = 'breadcrumb';
		};
?>
<div id="progress" class="container">
	<ol class="<?= $progress_class ?>" itemscope itemtype="http://schema.org/BreadcrumbList">
	<?php
		foreach ($progress_arr as $k => $array) {
	?>
		<li class="<?= $progress_class ?>-list <?php echo $array['class']; ?>" itemprop="itemListElement" itemscope itemtype="http://schema.org/ListItem">
			<a href="<?php echo $array['url']; ?>" itemtype="http://schema.org/Thing" itemprop="item">
				<span itemprop="name"><?php echo $array['name']; ?></span>
			</a>
			<meta itemprop="position" content="<?php echo $k+1;?>" />
		</li>
	<?php
		}
	?>
	</ol>
</div>
<?php
	}
?>
<!-- パンくずリスト -->