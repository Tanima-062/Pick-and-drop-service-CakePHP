<?php
	echo $this->Html->script(['/js/modal_float'],['defer'=>true, 'inline' => false]);
	echo $this->Html->script(array('/js/jquery.countdown.min'));
?>

<?php echo $this->element('pc_modal-plan'); ?>

<div class="container">
	<!-- [wrap] -->
	<div class="wrap contents clearfix">

<?php
	echo $this->element('progress_bar');
?>

<?php
	// SEARCH FORM-----
	echo $this->Form->create('Search', array('action' => 'index', 'inputDefaults' => array('label' => false, 'div' => false, 'hiddenField' => false, 'legend' => false, 'fieldset' => false), 'type' => 'get', 'class' => 'contents_search'));
?>
		<section class="search_wrap">
<?php
	// 出発日時や返却日時などは共通項目のためエレメント化
	echo $this->element('searchform_main');
	// 詳細条件検索をエレメント化
	echo $this->element('searchform_options');
?>
			<section class="searchform_submit_section_wrap">
<?php
	if (!empty($current_sort)) {
		echo $this->Form->hidden('sort', array('value' => $current_sort));
	}
	if (!empty($isList)) { // マップ検索で再検索する時も常にtype=mapを維持するため
		echo $this->Form->hidden('type', array('value' => 'list'));
	} else {
		echo $this->Form->hidden('type', array('value' => 'map'));
	}
?>
<?php
	echo $this->element('searchform_submit');
?>
			</section><!-- /searchform_submit_section_wrap -->
		</section><!-- /search_wrap -->
<?php
	echo $this->Form->end();
	// -----SEARCH FORM
?>

<?php
	// CANPAIGN BANNER-----
	if (!empty($hokkaidoCampaignFlg)) {
?>
		<div class="campaign-banner">
			<img src="/rentacar/img/campaign/campaign_hokkaidrive2022_pc.jpg"/>
		</div>
<?php
	}
	// -----CANPAIGN BANNER
?>

		<div class="search-tab">
			<div id="plan-list-tab" class="search-tab_item">
				プラン一覧
			</div>
			<div id="map-tab" class="search-tab_item">
				地図から探す
			</div>
		</div>

<?php
	// NUMBER OF HITS-----
	$errFlg = false; 
	if (empty($validationErrors) && !empty($commodities)) { // 検索結果がある場合
		if(!empty($isList)) {
?>
		<div class="contents_result">
			<div class="contents_result_number">
				<p>
					選択中の条件：
					<span><?php echo $searchPlace; ?></span>
					<?php if(!empty($resetCondLink)) echo '<a href="/rentacar/searches?'.$resetCondLink.'">絞り込み条件解除</a>' ?> <br>
					<?php echo $this->Paginator->counter('{:count}件 表示中 {:start}-{:end}'); ?>
				</p>			
			</div>
		</div>
		
<?php
		} else {
?>
		<div class="contents_result">
			<div class="contents_result_number">
				<?= $searchPlace ?>周辺の店舗が<span><?= count($rentOfficeList); ?></span>件見つかりました
				<?php if(!empty($resetCondLink)) echo '<a href="/rentacar/searches?'.$resetCondLink.'">絞り込み条件解除</a>' ?>
			</div>
		</div>
<?php
		}
	} else if (!empty($validationErrors)) { // パラメータに不備がある場合
		$errFlg = true;
?>
		<div class="contents_result">
			<div class="contents_result_number">
				<p>
					選択中の条件：
					<span><?php echo $searchPlace; ?></span><br>
					ご指定の条件のレンタカーがございません。条件を変えて再度検索して下さい。
				</p>
			</div>
		</div>
<?php
	} else { // 検索結果がない場合
		$errFlg = true;
?>
		<div class="contents_result">
			<div class="contents_result_number">
				<p>
					選択中の条件：
					<span><?php echo $searchPlace; ?></span><br>
					ご指定の条件のレンタカーがございません。条件を変えて再度検索して下さい。
				</p>
<?php
		if(!empty($resetCondLink)) {
?>
				<a class="remove_total_cond" href="/rentacar/searches?<?= $resetCondLink; ?>">絞り込み条件解除</a>
<?php
		}
?>
			</div>
		</div>
<?php
	}
	// -----NUMBER OF HITS
?>

<?php
	if(!empty($commodities)) {
		if(!empty($isList)) {
			/* プラン一覧検索 */
			echo $this->element('list_search_view');
		} else {
			/* マップ検索 */
			echo $this->element('map_search_view');
		}
	}
?>

<?php
	// WHEN NO RESULTS
	if($errFlg){
		echo $this->element('search_hints');
	}
?>
	</div><!-- [/wrap] -->
</div><!-- [/container] -->

<script>
$(function(){
	// 再検索する時、選択した店舗の情報を消す。
	$('#SearchIndexForm').submit(function() {
		window.sessionStorage.removeItem('selected_office_id')
		return true;
	});
<?php
	// 検索タブ切り替え
	if(!empty($isList)) {
?>
		$('#plan-list-tab').addClass('active')
		$('#map-tab').click(function() {
			$('#SearchType').val('map')
<?php if (!empty($refPage)) { ?>
			$('#SearchIndexForm').append('<input type="hidden" name="ref_page" value="<?php echo $refPage; ?>">');
<?php } ?>
			$('#SearchIndexForm').submit()
		})
<?php
	}
	else {
?>
		$('#map-tab').addClass('active')
		$('#plan-list-tab').click(function() {
			$('#SearchType').val('list')
<?php if (!empty($page)) { ?>
			$('#SearchIndexForm').append('<input type="hidden" name="page" value="<?php echo $page; ?>">');
<?php } ?>
			$('#SearchIndexForm').submit()
		})
<?php
	}
?>
	
	//リンク先をフォームへ
	$(".plan_contents_list_right form").each(function() {
	var obj = jQuery(this);
	var link = obj.attr("action");
	obj.attr("action",link)
	});

	var beforePos = 0;
	var windowHeight = $(window).height();
	$(window).scroll(function() {
		var nowPos =  $(this).scrollTop();

		$(".plan_contents_list_right").each( function(){
			var objPos = $(this).find(".js_page_viewer").offset().top;
			var remain_flg = ( $(this).find(".js_car_stock").length > 0 );
			var display_flg = $(this).find(".js_page_viewer > .plan_notes_p").is(":visible");

			if (nowPos > objPos - windowHeight + windowHeight/5){
				// 在庫が残り少ない場合
				if( remain_flg && !display_flg ){
					$(this).find(".js_page_viewer > .plan_notes_p").slideDown();
					$(this).find(".js_car_stock > .plan_notes_p").delay(500).slideDown();

				// 在庫がある場合
				}else if(!display_flg){
					$(this).find(".js_page_viewer > .plan_notes_p").slideDown();
				}
			}
		});
	});
});

</script>
