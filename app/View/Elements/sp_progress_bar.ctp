<!-- パンくずリスト -->
<?php 
	if(isset($progress_arr) && !$fromRentacarClient){
?>
<div id="progress" class="container breadcrumb_area">
	<ol class="breadcrumb clearfix" itemscope itemtype="http://schema.org/BreadcrumbList">
	<?php
		foreach ($progress_arr as $k => $array) {
	?>
		<li class="breadcrumb-list <?php echo $array['class']; ?>" itemprop="itemListElement" itemscope itemtype="http://schema.org/ListItem">
			<a class="breadcrumb-list-text" href="<?php echo $array['url']; ?>" itemtype="http://schema.org/Thing" itemprop="item">
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