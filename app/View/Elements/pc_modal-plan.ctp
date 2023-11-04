<?php
	// searches, planのプランモーダル

	echo $this->Html->css('swiper.min',null,array('inline'=>false));
	echo $this->Html->script(array('/js/swiper.min'));
?>

<div id="modal-content-planview" class="modal-view planview">
	<div class="modal-content-wrapper">
		<p class="icm-modal-close btn-close" id="modal-close-planview"></p>
		<div class="modal-content-scroll">
			<div class="modal-content-inner">
				<div class="modal-inner-left">
					<div class="swiper-container">
						<div class="swiper-wrapper" id="sliderSet"></div>
						<div class="swiper-pagination"></div>
						<div class="swiper-button-prev"><i class="icm-right-arrow icon-right-arrow_left"></i></div>
						<div class="swiper-button-next"><i class="icm-right-arrow"></i></div>
					</div>
					<div class="-description-carimg-wrapper">
						<p class="-title"><b>参考車両イメージ</b></p>
						<p class="-description-cartype"></p>
						<p class="-description-carmodel"></p>
						<p class="carimg_note">※写真はイメージです</p>
					</div>
					<div class="remark-wrapper">
						<table>
							<tr>
								<th>備考</th>
							</tr>
							<tr>
								<td class="-remark"></td>
							</tr>
						</table>
					</div>
				</div>
				<div class="modal-inner-right">
					<div class="-description-planname"></div>
					<div class="-description"></div>
				</div>
			</div>
		</div>
	</div><!--/modal-content-wrapper-->

<?php
	// Searchesページの時だけ見積もりボタン
	if ($this->params['controller'] == 'searches') {
?>
	<div class="modal-content-btn">
		<?php
			// レコメンド商品ID抽出
			if (!empty($commodities)) {
				foreach($commodities as $commodity){
					if($commodity['pr']){
						$commodityPrFlg = '1';
						echo '<input type="hidden" value="1" id="commodity_pr_'.$commodity['CommodityItem']['id'].'">';
					}
				}
			}
			// レコメンドクライアントID抽出
			if (!empty($rentOfficeList)) {
				foreach ($rentOfficeList as $officeId => $officeInfo) {
					if($officeInfo['pr']) {
						$officePrFlg = '1';
						echo '<input type="hidden" value="1" id="office_pr_'.$officeInfo['client_id'].'">';
					}
				}
			}
			echo $this->Form->create('Plan', array('controller' => 'reservations', 'action' => 'plan','type' => 'get', 'id' => 'PlanViewForm'));

			// GETボタンのhidden値生成
			parse_str($reserveUrl, $params);
			foreach ($params as $name => $value) {
				if (is_array($value)) {
					foreach ($value as $k => $v) {
						echo "<input type=\"hidden\" name=\"{$name}[{$k}]\" value=\"{$v}\" />\n";
					}
				} else {
					echo "<input type=\"hidden\" name=\"{$name}\" value=\"{$value}\" />\n";
				}
			}
			if(empty($isList)) {
				echo "<input type=\"hidden\" name=\"office_id\" value=\"\" />\n";
			}
			echo "<input type=\"hidden\" name=\"recommend_flg\" id=\"modal_recommend_flg\" value=\"0\" />\n";

			echo $this->Form->button('選択する', array('class' => 'btn-type-primary'));

			echo $this->Form->end();
		?>
	</div><!--/modal-content-btn-->
<?php
	}
?>

</div><!-- /modal-content-planview -->

<script>
$(function(){

	//プラン詳細を取得
	$('.js-modalOpen').on('click',function(){
		$(this).prop("disabled", true);
		var CommodityItemId = $(this).data('code');

<?php
	// Searchesページの時だけ
	if ($this->params['controller'] == 'searches') {
?>
		// マップ検索用
		const isList = <?= json_encode($isList); ?>;
		const officeId = $(this).data('office-id')
<?php
	}
?>

		$('.modal-content-inner').scrollTop(0);

		$.ajax({
			url:'/rentacar/plan/getPlanInfo?id=' + CommodityItemId,
			type:'GET'
		})
		.done(function(data) {
			var description = data["description"];
			var planname = data["plan_name"];
			var cartypename = data["car_type_name"];
			var carModels = data["models"];
			var flgmodelselect = data["flg_model_select"]; // 車種指定フラグ
			var item_id = data["id"];
			var remark = data["remark"];
			var client_id = data["client_id"];
			var images = data["images"];

			//プラン詳細本文
			if (planname.length) {
				$('.-description-planname').html(planname);
			}
			if (description.length) {
				$('.-description').html(description);
			}

			//備考欄
			if (remark.length) {
				$('.remark-wrapper').show();
				$('.-remark').html(remark);
			}
			
			//参考車両イメージ
			$('.-description-carmodel').html('');

			if (cartypename.length) {
				$('.-description-cartype').html(cartypename);
			}

			if (carModels.length) {
				$('.-description-carmodel').append('（');

				$.each(carModels, function(index, element) {
					$('.-description-carimg-wrapper').show();
					$('.-description-carmodel').append('<span>'+ element + '</span>');
				});
				
				var flgModelSelect = flgmodelselect ? '）' : ' 他）';
				$('.-description-carmodel').append( flgModelSelect );
			}

			$.each(images, function(index, element) {
				var imgUrl = "/rentacar/img/commodity_reference/" + client_id + "/" + element["url"];
				var imgIndex = index;
				var imgRemark = element["remark"];
				$('#sliderSet').append(
					$("<div/>").addClass('swiper-slide').append(
						$("<img/>").attr({ 'src' : imgUrl, 'alt' : imgRemark })
					)
				)
			});
			var formUrl = '/rentacar/plan/' + item_id + '/';
			$('#PlanViewForm').attr('action',formUrl);
<?php
	// 商品か地図かでレコメンドの性質が変わるため分岐
	if ($commodityPrFlg == '1') {
?>
			if ($("#commodity_pr_"+item_id).length){
				$("#modal_recommend_flg").val("1");
			}else {
				$("#modal_recommend_flg").val("0");
			}
<?php
	} elseif ($officePrFlg == '1') {
?>
			if ($("#office_pr_"+client_id).length){
				$("#modal_recommend_flg").val("1");
			}else {
				$("#modal_recommend_flg").val("0");
			}
<?php
	}
?>
<?php
	// Searchesページの時だけ
	if ($this->params['controller'] == 'searches') {
?>
			// dataにofficeIdが設定されていればマップ検索なのでofficeIdを設定
			if(!isList) {
				$('input:hidden[name="office_id"]').val(officeId)
			}
<?php
	}
?>

			$(function(){
				modalWindow('#wrapper');
				/*
				* プラン詳細表示Swiper
				*/
				var modalSwiper = new Swiper ('.swiper-container', {
					effect: "fade",
					pagination: '.swiper-pagination',
					paginationClickable:true,
					nextButton: '.swiper-button-next',
					prevButton: '.swiper-button-prev',
					observer: true,
					observeParents: true,
				})
			});
		})
		.fail(function(jqXHR, textStatus, errorThrown) {
			console.log('エラーが発生しました：' + jqXHR.status);
		})
	});

	/*
	* プラン詳細表示モーダル
	*/
	function modalWindow(mainWrap){
		var modalOverlay = "#modalOverlay";
		var modalClose = "#modal-close-planview";
		var modalCont = "#modal-content-planview";
		if($(modalOverlay)[0])return false;
		var scrollpos = $(window).scrollTop();
		$('#wrapper').addClass('is-fixed').css({'top': -scrollpos});
		$(mainWrap).prepend( '<div id="modalOverlay"></div>' );

		var imglength = $('.swiper-slide').length;
		for (var i=0; imglength > i ; i++ ) {
			if (imglength <= 1) {
				$('.swiper-button-prev').hide();
				$('.swiper-button-next').hide();
				$('.swiper-pagination').hide();
				$('.swiper-container').css('padding-bottom','0');
			} else {
				$('.swiper-button-prev').show();
				$('.swiper-button-next').show();
				$('.swiper-pagination').show();
				$('.swiper-container').css('padding-bottom','52px');
			}
		}
		$(modalOverlay).fadeIn( "slow" );
		centeringModalSyncer();
		$( modalCont ).fadeIn( "slow" );
		$(modalOverlay + "," + modalClose).unbind().click( function(){
			$(mainWrap).removeClass('is-fixed').css({'top': 0});
			window.scrollTo(0 , scrollpos);
			$(modalCont + "," + modalOverlay).fadeOut( "slow" , function(){
				$('.remark-wrapper').hide();
				$('.-remark').empty();
				$('#sliderSet').empty();
				$(modalOverlay).remove();
				$('.js-modalOpen').prop("disabled", false);
			});
		});
		return false;
		$( window ).resize( centeringModalSyncer );
		function centeringModalSyncer() {
			var w = $( window ).width();
			var h = $( window ).height();
			var cw = $( modalCont ).outerWidth();
			var ch = $( modalCont ).outerHeight();
			$( modalCont ).css( {"left": ((w - cw)/2) + "px","top": ((h - ch)/2) + "px"} );
		}
	}
});
</script>
