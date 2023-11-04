<div class="wrap contents clearfix">
<?php
	echo $this->element('progress_bar');
?>
	<div class="contents_type clearfix">
		<div class="contents_type_main">
			<div class="page_title">
				<h1 class="page_title_h2">全国の主要駅一覧からレンタカーを探す</h1>
			</div>

			<section class="section_station_list">
				<p><?=$stationlistContents['rentacar_header']?></p>

				<div class="station_list_block">
					<div class="station_list">
						<ul>
							<li class="station_list_title"><h2>北海道</h2></li>
							<li><a href="/rentacar/hokkaido/sapporo_station/">札幌駅</a></li>
							<li><a href="/rentacar/hokkaido/asahikawa_station/">旭川駅</a></li>
							<li><a href="/rentacar/hokkaido/hakodate_station/">函館駅</a></li>
						</ul>
					</div>
					<div class="station_list">
						<ul>
							<li class="station_list_title"><h2>東北</h2></li>
							<li><a href="/rentacar/tohoku/miyagi/miyagi_sendai_station/">仙台駅</a></li>
							<li><a href="/rentacar/tohoku/iwate/morioka_station/">盛岡駅</a></li>
							<li><a href="/rentacar/tohoku/aomori/shinaomori_station/">新青森駅</a></li>
							<li><a href="/rentacar/tohoku/fukushima/fukushima_station/">福島駅</a></li>
						</ul>
					</div>
					<div class="station_list">
						<ul>
							<li class="station_list_title"><h2>首都圏</h2></li>
							<li><a href="/rentacar/kanto/tokyo/tokyo_station/">東京駅</a></li>
							<li><a href="/rentacar/kanto/tokyo/shinagawa_station/">品川駅</a></li>
							<li><a href="/rentacar/kanto/kanagawa/yokohama_station/">横浜駅</a></li>
							<li><a href="/rentacar/kanto/chiba/chiba_station/">千葉駅</a></li>
							<li><a href="/rentacar/kanto/saitama/omiya_station/">大宮駅</a></li>
						</ul>
					</div>
					<div class="station_list">
						<ul>
							<li class="station_list_title"><h2>北陸</h2></li>
							<li><a href="/rentacar/hokuriku/ishikawa/kanazawa_station/">金沢駅</a></li>
						</ul>
					</div>
					<div class="station_list">
						<ul>
							<li class="station_list_title"><h2>長野・新潟</h2></li>
							<li><a href="/rentacar/koushinetsu/niigata/niigata_station/">新潟駅</a></li>
							<li><a href="/rentacar/koushinetsu/nagano/nagano_station/">長野駅</a></li>
						</ul>
					</div>
				</div>
			 <div class="station_list_block">
					<div class="station_list">
						<ul>
							<li class="station_list_title"><h2>中部</h2></li>
							<li><a href="/rentacar/tokai/aichi/nagoya_station/">名古屋駅</a></li>
							<li><a href="/rentacar/tokai/shizuoka/shizuoka_station/">静岡駅</a></li>
							<li><a href="/rentacar/tokai/shizuoka/hamamatsu_station/">浜松駅</a></li>
						</ul>
					</div>
					<div class="station_list">
						<ul>
							<li class="station_list_title"><h2>関西</h2></li>
							<li><a href="/rentacar/kansai/osaka/shinosaka_station/">新大阪駅</a></li>
							<li><a href="/rentacar/kansai/osaka/osaka_station/">大阪駅</a></li>
							<li><a href="/rentacar/kansai/kyoto/kyoto_station/">京都駅</a></li>
							<li><a href="/rentacar/kansai/hyogo/sannomiya_station/">三宮駅</a></li>
						</ul>
					</div>
					<div class="station_list">
						<ul>
							<li class="station_list_title"><h2>中国</h2></li>
							<li><a href="/rentacar/chugoku/hiroshima/hiroshima_station/">広島駅</a></li>
							<li><a href="/rentacar/chugoku/okayama/okayama_station/">岡山駅</a></li>
							<li><a href="/rentacar/chugoku/hiroshima/fukuyama_station/">福山駅</a></li>
							<li><a href="/rentacar/chugoku/yamaguchi/shinyamaguchi_station/">新山口駅</a></li>
						</ul>
					</div>
					<div class="station_list">
						<ul>
							<li class="station_list_title"><h2>九州</h2></li>
							<li><a href="/rentacar/kyushu/fukuoka/hakata_station/">博多駅</a></li>
							<li><a href="/rentacar/kyushu/fukuoka/kokura_station/">小倉駅</a></li>
							<li><a href="/rentacar/kyushu/kumamoto/kumamoto_station/">熊本駅</a></li>
							<li><a href="/rentacar/kyushu/kagoshima/kagoshimachuo_station/">鹿児島中央駅</a></li>
						</ul>
					</div>
				</div>
			</section>

			<section class="rentacar_txt_area rent-margin-bottom-l">

				<h2><?=$stationlistContents['rentacar_title']?></h2>

			<?php foreach($stationlistContents['rentacar-body-list'] as $val):?>
				<?php if(!empty($val['img'])):?>
					<div class="renta_img_wrap">
						<img src="<?=$val['photo-guid']?>" width="100%" height="auto" alt="<?=$cont_title?>"/>
					<?php if($val['photo-guid']):?>
						<?php if(!empty($val['img-url'])):?>
						<span class="img_source"><a href="<?=$val['img-url']?>" target="_blank"><?php if(!empty($val['img-name'])):?>出典：<?=$val['img-name']?><?php else:?>出典：<?=$val['img-url']?><?php endif;?></a></span>
					<?php else:?>
						<?php if(!empty($val['img-name'])):?>
						<span class="img_source">出典：<?=$val['img-name']?></span>
						<?php endif;?>
					<?php endif;?>
				<?php endif;?>
					</div>
				<?php endif;?>

				<?php if(!empty($val['title'])): 
						$cont_title = $val['title'] ?>
					<h3><?=$val['title']?></h3>
				<?php endif;?>

				<?php if(!empty($val['text'])):?>
					<p class="renta_body_wrap"><?=$val['text']?></p>
				<?php endif;?>
			<?php endforeach; ?>

			</section>
		</div>

		<div class="contents_type_side">
			<?php echo $this->element("sidebar"); ?>
		</div>
	</div>

</div>
