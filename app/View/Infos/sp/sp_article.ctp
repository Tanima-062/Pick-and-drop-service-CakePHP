<?php
echo $this->Html->css(array('sp/jquery-ui'),null,array('inline'=>false));
//echo $this->Html->css(array('sp/info_common', 'sp/jquery-ui', 'sp/common_2.0'),null,array('inline'=>false));
?>
<section>
	<div class="section_title_blue">
		<h2><?=$articleContents['rentacar_title']?></h2>
	</div>

	<section>
<?php
	if(isset($articleContents['rentacar-body-list'][0]['img'])){
?>
			<div class="report_img_wrap">
				<img src="<?=$articleContents['rentacar-body-list'][0]['photo-guid']?>" width="100%" class="report_main_img" />
				<aside class="report_img_aside">画像出典:pixabay.com</aside>
			</div>
<?php
	}
?>
		<div class="report_sns">
			<ul>
				<li class="btn_report_share fb"><a href=""><span class="fa fa-facebook"></span></a></li><!--
				--><li class="btn_report_share tw"><a href=""><span class="fa fa-twitter"></span></a></li><!--
				--><li class="btn_report_share hb"><a href=""><i class="fa icm-hatena"></i></a></li><!--
				--><li class="btn_report_share pk"><a href=""><i class="fa icm-pocket"></i></a></li><!--
		--></ul>
		</div>
<?php
	if(isset($articleContents['rentacar-body-list'][0]['img'])){
?>
		<p class="report_text"><?=$articleContents['rentacar-body-list'][1]['text']?></p>
<?php
	}
?>
	</section>
	<section class="report_table_of_contents">
		<h3>目次</h3>
		<ol>
<?php
	foreach($articleContents['rentacar-body-list'] as $val) {
		if(isset($val['title'])){
?>
			<li><a href="#"><?=$val['title']?></a></li>
<?php
		}
	}
?>
		</ol>
	</section>
	<section class="report_btn_wrap">
<?php
	if(isset($articleContents['rentacar_link']) && isset($articleContents['rentacar_anchor_text'])){
?>
		<a href="<?=$articleContents['rentacar_anchor_text']?>" class="btn_report_search"><?=$articleContents['rentacar_link']?><i class="icm-right-arrow"></i></a>
<?php
	}
?>
	</section>
	<section>
		<h3 class="report_h3">
			<span class="icon_car"></span><span class="report_sub_title"><?=$articleContents['rentacar_title']?></span>
		</h3>
<?php
	foreach($articleContents['rentacar-body-list'] as $key => $val){
?>
		<div class="report_body">
<?php
		if(isset($val['title'])){
?>
			<h4><?=$val['title']?></h4>
<?php
		}
		if($key != 0){
			if(!empty($val['img'])){
?>
			<div class="report_img_wrap">
				<img src="<?=$val['photo-guid']?>" width="100%" class="report_main_img" />
				<aside class="report_img_aside">画像出典:pixabay.com</aside>
			</div>
<?php
			}
		}
		if($key != 1){
			if(isset($val['text'])){
?>
			<p><?=$val['text']?></p>
<?php
			}
		}
?>
		</div>
<?php
	}
?>
	</section>
	<section class="report_btn_wrap">
<?php
	if(isset($articleContents['rentacar_link']) && isset($articleContents['rentacar_anchor_text'])){
?>
		<a href="<?=$articleContents['rentacar_anchor_text']?>" class="btn_report_search"><?=$articleContents['rentacar_link']?><i class="icm-right-arrow"></i></a>
<?php
	}
?>
	</section>
</section>
<?php
	echo $this->element("sp_sidebar");
?>
