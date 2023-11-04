<?php
	echo $this->Html->script(['/js/modal_float'], ['inline' => false, 'defer' => true]);
	echo $this->Html->script(['/js/modal_fullscreen'], ['inline' => false, 'defer' => true]);
	echo $this->Html->css('sp/jquery-ui',null,array('inline'=>false));
	echo $this->Html->script('/js/jquery.countdown.min');
?>
<script>
	function toggleOtherRentOffice(commodity_id) {
		var state = $('a.show_hide_other_rent_office_'+commodity_id).attr('area-hidden');
		if ( state === 'true' ) {
			$('a.show_hide_other_rent_office_'+commodity_id).attr('area-hidden', 'none');
			$('div.other_rent_office_area_'+commodity_id).show('normal');
			$('a.show_hide_other_rent_office_'+commodity_id).children('i').removeClass('fa-plus-circle').addClass('fa-minus-circle');
			$('a.show_hide_other_rent_office_'+commodity_id).attr('area-hidden', 'false');
		} else if ( state === 'false' ) {
			$('a.show_hide_other_rent_office_'+commodity_id).attr('area-hidden', 'none');
			$('div.other_rent_office_area_'+commodity_id).hide('normal');
			$('a.show_hide_other_rent_office_'+commodity_id).children('i').removeClass('fa-minus-circle').addClass('fa-plus-circle');
			$('a.show_hide_other_rent_office_'+commodity_id).attr('area-hidden', 'true');
		}
	}
</script>

<!-- AppsFlyer CVタグ -->
<?php
	if( USER_TERMINAL_SOFT>=TERMINAL_SOFT_APP ){
		$replaceFromDate = str_replace("/", "-", $fromDate);
		$replaceToDate = str_replace("/", "-", $toDate);
?>
<script type="text/javascript">
	function trackEvent(){
		var eventName = "af_rentacar_search"
		var eventParams = "{\"af_content_type\":\"rentacar\",\"af_destination_a\":\"<?=$searchPlace;?>\",\"af_date_a\":\"<?=$replaceFromDate;?>\",\"af_date_b\":\"<?=$replaceToDate;?>\"}";
		var iframe = document.createElement("IFRAME");
		iframe.setAttribute("src", "af-event://inappevent?eventName="+eventName+"&eventValue="+eventParams);
		document.documentElement.appendChild(iframe);
		iframe.parentNode.removeChild(iframe);
		iframe = null;
	}
	trackEvent();
</script>
<?php
	}
?>
<!-- // AppsFlyer CVタグ -->

<!-- adwaysアプリ検索計測 -->
<?php
	if( USER_TERMINAL_SOFT>=TERMINAL_SOFT_APP && USER_TERMINAL_OS==TERMINAL_OS_IOS ){
		if( USER_TERMINAL_SOFT_APP==TERMINAL_SOFT_APP_SKYTICKET ){
			if( USER_TERMINAL_SOFT_VERSION_CODE==TERMINAL_SOFT_VERSION_CODE_133 ){
				//iOS skyticketアプリ 1.33以降
?>
<script>
	$(function(){
		location.href = "partytrackcall://params/?party_event_id=63784";
	});
</script>
<?php
			}
		} elseif( USER_TERMINAL_SOFT_APP==TERMINAL_SOFT_APP_RENTACAR ) {
			//iOS レンタカーアプリ
?>
<script>
	$(function(){
		location.href = "partytrackcall://params/?party_event_id=66025";
	});
</script>
<?php
		}
	} elseif( USER_TERMINAL_SOFT>=TERMINAL_SOFT_APP && USER_TERMINAL_OS==TERMINAL_OS_ANDROID ) {
		if( USER_TERMINAL_SOFT_APP==TERMINAL_SOFT_APP_SKYTICKET ){
			if( USER_TERMINAL_SOFT_VERSION_CODE==TERMINAL_SOFT_VERSION_CODE_133 ){
				//android skyticketアプリ 1.33以降
?>
<script>
	$(function(){
		location.href = "partytrackcall://params/?party_event_id=63785";
	});
</script>
<?php
			}
		} elseif( USER_TERMINAL_SOFT_APP==TERMINAL_SOFT_APP_RENTACAR ) {
			//android レンタカーアプリ
?>
<script>
	$(function(){
		location.href = "partytrackcall://params/?party_event_id=66027";
	});
</script>
<?php
		}
	}
?>
<!-- /Adwaysアプリ検索計測 -->

<div id="list_top">
	<section class="search_outline_wrap">
		<!--search_outline_wrap-->
<?php
	// オリジナル
	if($testPatternNum === '0') {
		echo $this->Html->script(['/js/sp/search']);
?>
		<div class="search_outline">
			<h1 class="search_outline_content">
						
<?php
		if(!empty($departureDate) && !empty($returnDate)) {
			$departureTimeColon = str_replace("-", ":", $departureTime);
			$returnTimeColon = str_replace("-", ":", $returnTime);
			$searchTargetTime = $departureDate." ".$departureTimeColon." ～ ".$returnDate." ".$returnTimeColon;
?>
				<span><?= $searchTargetTime ?></span>
<?php
		}
?>
						
<?php
		if(!empty($searchPlace)) {
			echo $searchPlace;
		}
?>
			</h1>
			<div class="re_search_button">
				<a href="javascript:void(0);" class="js-modalfs_open">
					<i class="icm-search"></i>詳細検索
				</a>
			</div>
			<?php echo $this->element('sp_modal_research'); ?>
		</div>	
<?php
	// テストパターン
	} else {
		echo $this->Html->script(['/js/sp/search_separated_ab']);

		// ABテストで詳細検索モーダルを二つに分けることになったため、Formを二つのモーダルを囲むようにしないといけない
		// 囲まないと内部にあるinputがrequestに含まれない
		echo $this->Form->create('Search',
		array(
			'action'=>'index',
			'id'=>'SearchIndexFormAB',
			'inputDefaults'=>array(
				'label'=>false,
				'div'=>false,
				'hiddenField'=>false,
				'legend'=>false,
				'fieldset'=>false
			),
			'type'=>'get'
		)
	);
?>
		<div class="search_outline-ab">
			<h1 class="search_outline_content-ab">
						
<?php
		if(!empty($departureDate) && !empty($returnDate)) {
			$departureTimeColon = str_replace("-", ":", $departureTime);
			$returnTimeColon = str_replace("-", ":", $returnTime);
			$searchTargetTime = $departureDate." ".$departureTimeColon." ～ ".$returnDate." ".$returnTimeColon;
?>
					<span><?= $searchTargetTime ?></span>
<?php
		}
?>
						
<?php
		if(!empty($searchPlace)) {
			echo $searchPlace;
		}
?>
			</h1>
			<div class="cond_change_button-ab">
				<a href="javascript:void(0);" class="js-modalf_open">
					<i class="icm-search"></i>条件変更
				</a>
			</div>
			<?php echo $this->element('sp_modal_search_cond_change'); ?>
		</div>
		<div class="search_outline_bottom-ab">
			<a href="javascript:void(0);" class="js-modalfs_open">
				<i class="icm-filter"></i>詳細条件から絞り込む
			</a>
		</div>
		<?php echo $this->element('sp_modal_search_filter'); ?>
<?php
		if (!empty($current_sort)) {
			echo $this->Form->hidden('sort', array('value' => $current_sort));
		}
		if (!empty($isList)) { // マップ検索で再検索する時も常にtype=mapを維持するため
			echo $this->Form->hidden('type', array('value' => 'list'));
		} else {
			echo $this->Form->hidden('type', array('value' => 'map'));
		}
		echo $this->Form->end();
	}
?>
<?php
	if (!empty($hokkaidoCampaignFlg)) {
?>
		<div class="campaign-banner">
			<img src="/rentacar/img/campaign/campaign_hokkaidrive2022_sp.jpg"/>
		</div>
<?php
	}
?>
		<div class="search-tab">
			<div id="plan-list-tab" class="search-tab_item">
				プラン一覧
			</div>
			<div id="map-tab" class="search-tab_item">
				地図から探す
			</div>
		</div>
		<!--/search_outline_wrap-->
	</section>
	<section>
		<p class="search_result_number <?php if(empty($isList)){?>map<?php }; ?>">
<?php
	$errFlg = false;
	if (empty($validationErrors) && !empty($commodities)) {
		if(!empty($isList)) {
			$resultCount = $this->Paginator->counter('{:count}');
			$textResultCount = $departureDate."に";
			if($departureDate != $returnDate){
				$textResultCount = $departureDate."～".$returnDate."の期間に";
			}
			$textResultCount .= "空きのあるレンタカーは全".$resultCount."件 ";

			echo $textResultCount;
			echo $this->Paginator->counter('{:start}～{:end}件表示中<br />');
			if( $resultCount < 21 ){
				echo "<span>人気の日程のためすぐに埋まる可能性があります</span>";
			}
			if(!empty($resetCondLink)) {
				echo '<a href="/rentacar/searches?'.$resetCondLink.'">絞り込み条件解除</a>';
			}
		} else {
			// マップのㅁ場合
			echo '<b>'.$searchPlace.'周辺</b>の店舗が<span>'.count($rentOfficeList).'</span>件見つかりました';
			if(!empty($resetCondLink)) {
				echo '<a href="/rentacar/searches?'.$resetCondLink.'">絞り込み条件解除</a>';
			}
		}
	} else if (!empty($validationErrors)) { // パラメータに不備がある場合
		echo 'ご指定の条件のレンタカーがございません。条件を変えて再度検索して下さい。';
		$errFlg = true;
	} else { // 検索結果がない場合
		echo 'ご指定の条件のレンタカーがございません。条件を変えて再度検索して下さい。';
		if(!empty($resetCondLink)) {
			echo '<a href="/rentacar/searches?'.$resetCondLink.'">絞り込み条件解除</a>';
		}
		$errFlg = true;
	}
?>
		</p>
<?php
	// ソート順リンクの出力
	if (!empty($commodities)) {
		if (!empty($isList)) {
?>
		<div class="search_sort_wrap">
<?php
		echo $this->Form->input('sort', array('type'=>'select', 'options'=>$searchSortList, 'default'=>$current_sort, 'class'=>'search_sort_select'));
?>
			<div class="search_sort_option"><?=$searchSortList[$current_sort];?></div>
			<i class="icm-right-arrow icon-right-arrow_down"></i>
		</div>
<?php
		}
	}
?>
<?php
	if(!empty($commodities)) {
		if (!empty($isList)) {
			/* プラン一覧検索 */
			echo $this->element('sp_list_search_view');
		} else {
			/* マップ検索 */
			echo $this->element('sp_map_search_view');
		}
	} 
?>

<?php echo $this->element('sp_modal-plan'); ?>

<?php
	// WHEN NO RESULTS
	if($errFlg){
		echo $this->element('search_hints');
	}
?>

	</section>
</div>

<?php
	// Re-search bottom button-----
	if(!$errFlg){
?>
<div class="research-btn-wrap">
<?php
		// テストパターン
		if($testPatternNum !== '0') {
?>
	<div>
		<a href="javascript: void(0);" class="js-modalfs_open btn-type-secondary">詳細検索・絞り込み</a>
	</div>
<?php
			echo $this->Html->script(['/js/sp/search_combined_ab']);
			echo $this->element('sp_modal_research');
		} 
		// オリジナル
		else {
?>
	<a href="javascript: void(0);" class="js-modalfs_open btn-type-secondary">詳細検索・絞り込み</a>
<?php
		}
?>
</div>
<?php
	}
	// -----Re-search bottom button
?>

<?php echo $this->element('sp_modal-equipment_desc'); ?>

<script>
$(function(){
	// 再検索する時、選択した店舗の情報を消す。
	$('#SearchIndexForm').submit(function() {
		window.sessionStorage.removeItem('selected_office_id')
		return true;
	});

<?php
	// 検索タブ切り替え　list or map
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
	
	if( (isset($resultCount) && $resultCount > 0) && (isset($viewNumber)) ){
?>
	$(".cake-sql-log").hide();

	// 使ってなさそう
	// $(".other_rent_icon").on("click", function(){
	// 	var dataComId = $(this).data("commodity_id");

	// 	if( $(this).hasClass("fa-plus-circle") ){
	// 		$(this).addClass("fa-minus-circle").removeClass("fa-plus-circle");
	// 		$(".other_rent_office_area_"+dataComId).slideDown();
	// 	}else if( $(this).hasClass("fa-minus-circle") ){
	// 		$(this).addClass("fa-plus-circle").removeClass("fa-minus-circle");
	// 		$(".other_rent_office_area_"+dataComId).slideUp();
	// 	}
	// });
<?php
	}
?>

/*
	var obj_cont = $(".filter_accordion_detail");
	$(".filter_accordion_btn").on("click", function(){
		if( obj_cont.is(":visible") ){
			obj_cont.slideUp();
			$(this).removeClass("open");
			$(".filter_numbers").slideDown();

		}else{
			obj_cont.slideDown();
			$(this).addClass("open");
			$(".filter_numbers").slideUp();
		}
	});
*/
	var beforePos = 0;
	var headerHeight = $("#header").height() + $(".search_outline").height();
	var windowHeight = $(window).height();

	$(window).scroll(function() {
		var nowPos =  $(this).scrollTop();
		// 下方向スクロール かつ 上部の人数フィルタを超えたとき表示
		if(nowPos > beforePos && nowPos > headerHeight){
			$("#floating_filter_numbers").fadeIn();
		// 上方向スクロール かつ 上部の人数フィルタに到達したとき非表示
		}else if(nowPos <= beforePos && nowPos <= headerHeight){
			$("#floating_filter_numbers").fadeOut();
		}
		beforePos = nowPos;

		$(".plan_detail_notes_wrap").each( function(){
			var objPos = $(this).find(".js-search_result_notes_blue").offset().top;
			var remain_flg = ( $(this).find(".js-search_result_notes_red").length > 0 );
			var display_flg = $(this).find(".js-search_result_notes_blue > .result_notes_p").is(":visible");

			if (nowPos > objPos - windowHeight + windowHeight/5){
				// 在庫が残り少ない場合
				if( remain_flg && !display_flg ){
					$(this).find(".js-search_result_notes_blue > .result_notes_p").slideDown();
					$(this).find(".js-search_result_notes_red > .result_notes_p").delay(500).slideDown();

				// 在庫がある場合
				}else if(!display_flg){
					$(this).find(".js-search_result_notes_blue > .result_notes_p").slideDown();
				}
			}
		});
	});

	// 使ってない？
	// $("[name=adults_count]").on("change", function(){
	// 	$("[name=adults_count]").val( $(this).val() );
	// 	var this_id = $(this).attr("id");
	// 	if(this_id == "adultsCountTops" || this_id == "adultsCountFloat"){
	// 		$("#SearchIndexForm").submit();
	// 	}
	// });
	// $("[name=children_count]").on("change", function(){
	// 	$("[name=children_count]").val( $(this).val() );
	// 	var this_id = $(this).attr("id");
	// 	if(this_id == "childrenCountTops" || this_id == "childrenCountFloat"){
	// 		$("#SearchIndexForm").submit();
	// 	}
	// });
	// $("[name=infants_count]").on("change", function(){
	// 	$("[name=infants_count]").val( $(this).val() );
	// 	var this_id = $(this).attr("id");
	// 	if(this_id == "infantsCountTops" || this_id == "infantsCountFloat"){
	// 		$("#SearchIndexForm").submit();
	// 	}
	// });
	
	$("[name=data\\[sort\\]]").on("change", function(){
		$(".search_sort_option").html( $("[name=data\\[sort\\]] option:selected").text() );
		$("[name=sort]").val( $(this).val() );
		$("#SearchIndexForm").submit();
	});

});
</script>
