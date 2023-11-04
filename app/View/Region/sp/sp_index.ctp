<?php
	echo $this->Html->script(['/js/smoothscroll'],['defer'=>true, 'inline' => false]); 
?>

<link rel="stylesheet" type="text/css" href="/rentacar/css/sp/jquery.bxslider.css">
<link rel="stylesheet" type="text/css" href="/rentacar/css/sp/jquery-ui.css">

<div class="region">
<?php 
	$activeWebp = $is_google_user_agent === true || strpos((string)env('HTTP_ACCEPT'), 'image/webp') !== false;
	$type = $activeWebp ? '.webp' : '.png';
?>
	<div class="region_head">
		<h1 class="region_head_ttl"><?= $regionContents['region-name']; ?>地方の格安レンタカーを比較・予約する</h1>
	</div>

	<section id="search">
		<?php
			echo $this->Form->create('Search', array(
				'controller'=>'searches','action'=>'index', 'inputDefaults'=>array(
					'label'=>false,
					'div'=>false,
					'hiddenField'=>false,
					'legend'=>false,
					'fieldset'=>false
				),
				'type'=>'get')
			);
		?>
			<section class="search_section">
				<?php
					// 出発日時や返却日時などは共通項目のためエレメント化
					echo $this->element('sp_searchform_main');
				?>
				<div class="searchform_submit_section_wrap">
					<?php echo $this->element('sp_searchform_submit'); ?>
				</div>
			</section>
		<?php
			echo $this->Form->end();
		?>
	</section>

	<p class="text-area">
		<?= $regionContents['pre-contents']; ?>
	</p>

	<table class="rentacar-grid">
		<tbody>
<?php
	if(isset($regionContents['region-id'])){
?>
			<tr>
<?php
		$i=0;
		foreach($regionContents['pref_details'] as $val){
			$i++;
?>
				<td class="rentacar-grid-half-list">
					<a href="#<?= $prefectureLinkCd[$val['id']]; ?>" class="rentacar-grid-list-link">
					<?= $prefectureList[$val['id']]; ?></a>
				</td>
<?php
			if($i % 2 == 0){
?>
			</tr>
			<tr>
<?php
			}
			if($i == count($regionContents['pref_details'])) {				
?>
				<td class="rentacar-grid-half-list">
					<a href="#reviews" class="rentacar-grid-list-link">ロコミ</a>
				</td>	
<?php
			}
		}
?>
			</tr>
<?php
	}
?>
		</tbody>
	</table>

<?php
	if(isset($regionContents['pref_details'])){
		$i=0;
		foreach($regionContents['pref_details'] as $val){
			$i++;
?>
	<div class="section_title_blue" id="<?= $prefectureLinkCd[$val['id']]; ?>">
		<h2><?= $prefectureList[$val['id']]; ?></h2>
	</div>

	<div class="page-cont-area">
<?php
			if (!empty($val['img'])){
?>
		<img src="/rentacar/wp/img/<?= $val['img']; ?>" alt="<?= $prefectureList[$val['id']]; ?>">
<?php
			}
?>
		<p class="text"><?= $val['text']; ?></p>
		<span>続きを読む<img src="/rentacar/img/sp/plus.png"></span>
	</div>

	<div class="for_airport_button_area">
		<a href="/rentacar/<?= $base_url . $prefectureLinkCd[$val['id']]; ?>/" class="store_search_btn" style="display: block; margin: 0 auto;"><?= $prefectureList[$val['id']]; ?>のレンタカー予約へ</a>
	</div>

	<div class="section_title_blue is-lightblue">
		<h3 class="page_title_header"><?= $prefectureList[$val['id']]; ?>内のドライブスポットから探す</h3>
	</div>

	<!-- スタメン -->
	<div class="select_area" id="thumbs<?= $i; ?>">
		<ul class="thumbs">
<?php
			$j=0;
			foreach($val['rows'] as $val2){
				$j++;
				if (!empty($val2['city'])){
?>
			<li><h4><a href="#thumbs<?= $i; ?>" slide="<?=$j?>"><?= $areaLinkCdList[$val2['city']]; ?></a></h4></li>
<?php
				}elseif (!empty($val2['airport'])){
?>
			<li><h4><a href="#thumbs<?= $i; ?>" slide="<?=$j?>"><?= $airportLinkCdList[$val2['airport']]; ?></a></h4></li>
<?php
				}
				if($j % 2 == 0){
?>
		</ul>
		<ul class="thumbs">
<?php
				}
			}
?>
		</ul>
	</div>

	<div id="slider<?= $i; ?>" class="slider">
<?php
			foreach($val['rows'] as $val2){
?>
		<div class="page-cont-area">
			<p class="ttl">
<?php
				if (!empty($val2['city'])){
					echo $areaLinkCdList[$val2['city']];
					$cdList = $areaLinkCdList[$val2['city']];
				}elseif (!empty($val2['airport'])){
					echo $airportLinkCdList[$val2['airport']];
					$cdList = $airportLinkCdList[$val2['airport']];
				}
?>
			</p>
			<img src="/rentacar/wp/img/<?= $val2['img']; ?>" alt="<?= $cdList ?>">
			<p class="text"><?= $val2['contents']; ?></p>
			<span class="clearfix">続きを読む<img src="/rentacar/img/sp/plus.png"></span>
			<div class="for_airport_button_area">
<?php
				if (!empty($val2['city'])){
?>
				<a href="/rentacar/<?=$base_url . $prefectureLinkCd[$val['id']] . DS . $val2['city'];?>/" class="store_search_btn" style="display: block; margin: 0 auto;"><?= $areaLinkCdList[$val2['city']]; ?>のレンタカー予約へ</a>
<?php
				}elseif (!empty($val2['airport'])){
?>
				<a href="/rentacar/<?=$base_url . $prefectureLinkCd[$val['id']] . DS . $val2['airport'];?>/" class="store_search_btn" style="display: block; margin: 0 auto;"><?= $airportLinkCdList[$val2['airport']]; ?>のレンタカー予約へ</a>
<?php
				}
?>
			</div>
		</div>
<?php
			}
?>
	</div>

<?php
		}
	}
?>

<?php 
	// YOTPO
	if($yotpo_is_active && $use_yotpo){ 
?>
	<section id="reviews" class="yotpo_api_custom_wrap">
		<h2 class="review_section_title"><?=$regionContents['region-name']?>地方でレンタカーを利用したお客様の口コミ・レビュー</h2>
		<?php echo $this->element('sp_yotpo_review'); ?>
	</section>
<?php 
	}
?>

	<?php echo $this->element('sp_popular_airport_linklist'); ?>

	<div class="section_title_blue">
		<h2>他の地方からレンタカーを探す</h2>
	</div>

	<label class="airport_select">
		<select class="js-jump_to_value">
			<option>こちらからエリアをお選びください</option>
<?php 
	foreach($regionList as $key => $val){ 
		if($key != $regionContents['region-id']){ 
?>
			<option value="/rentacar/<?= str_replace('area_', '', $key); ?>/"><?= $val; ?></option>
<?php 
		}
	} 
?>
		</select>
	</label>

</div>
