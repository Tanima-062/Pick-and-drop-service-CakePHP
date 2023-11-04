<!-- <?php echo $this->Html->css(array('sp/font-awesome.min', 'common_2.0'), null, array('inline' => false)); ?> -->

<div class="wrap contents clearfix">
<?php
	echo $this->element('progress_bar');
?>
	<div class="contents_type clearfix">
		<div class="contents_type_main">
			<?php if($articleContents['rentacar_title']):?>
				<h2 class="report_h2"><?=$articleContents['rentacar_title']?></h2>
			<?php endif;?>

			<section>
				<div class="report_sns">
					<ul>
						<li class="btn_report_share fb"><a href=""><span class="fa fa-facebook"></span><span class="share_count">シェア</span></a></li><!--
						--><li class="btn_report_share tw"><a href=""><span class="fa fa-twitter"></span><span class="share_count">ツイート</span></a></li><!--
						--><li class="btn_report_share hb"><a href=""><i class="fa icm-hatena"></i><span class="share_count">はてブ</span></a></li><!--
				 --></ul>
				</div>

				<?php if(isset($articleContents['rentacar-body-list'][0]['img'])):?>
					<div class="report_img_wrap">
						<img src="<?=$articleContents['rentacar-body-list'][0]['photo-guid']?>" width="100%" class="report_main_img" />
						<aside class="report_img_aside">画像出典:pixabay.com</aside>
					</div>
				<?php endif;?>
			</section>

			<section class="report_table_of_contents">
				<h3>目次</h3>
				<ol>
					<?php foreach($articleContents['rentacar-body-list'] as $val):?>
						<?php if(isset($val['title'])):?>
							<li><a href="#"><?=$val['title']?></a></li>
						<?php endif;?>
					<?php endforeach; ?>
				</ol>
			</section>

			<section class="report_text_body">
				<?php if($articleContents['rentacar_title']):?>
					<h3><?=$articleContents['rentacar_title']?></h3>
				<?php endif;?>
				<?php foreach($articleContents['rentacar-body-list'] as $key => $val):?>
					<?php if(!$key == 0):?>
						<?php if(!empty($val['img'])):?>
							<div class="report_img_wrap">
								<img src="<?=$val['photo-guid']?>" class="report_img" width="100%" />
								<aside class="report_img_aside">
									画像出典:&nbsp;<a href="" target="_blank">----.com</a>
								</aside>
							</div>
						<?php endif;?>
					<?php endif;?>
					<?php if(isset($val['title'])):?>
						<h4><?=$val['title']?></h4>
					<?php endif;?>
					<?php if(isset($val['text'])):?>
						<p><?=$val['text']?></p>
					<?php endif;?>
				<?php endforeach; ?>
			</section>

			<div class="report_btn_wrap">
				<?php if(isset($articleContents['rentacar_link']) && isset($articleContents['rentacar_anchor_text'])):?>
					<a href="<?=$articleContents['rentacar_anchor_text']?>" class="btn_report_search"><?=$articleContents['rentacar_link']?><i class="icm-right-arrow"></i></a>
				<?php endif;?>
			</div>
		</div>

		<div class="contents_type_side">
			<?php echo $this->element("sidebar"); ?>
		</div>
	</div>
</div>
<script>
$(function(){


});


</script>
