<!DOCTYPE html>
<html lang="ja">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>プレビュー | プラン詳細</title>
<?php
	echo $this->Html->css(array('../../lib/lib', '../../css/style_new', '../../css/icomoon/fonts/icomoon-style', '../../css/swiper.min'));
	echo $this->Html->script(array('../../lib/lib.min', '../../js/common', '../../js/swiper.min'));
?>
</head>
<body>
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
	</div><!-- /modal-content-planview -->

	<div class="wrap contents clearfix" style="margin-top: 24px;">

		<section class="plan_info_block plan_view">
			<h3 class="plan_info_block_head">
				<div class="plan_contents_list_shop_name">
					<?php echo $commodityInfo['Client']['name']; ?>
				</div>
			</h3>
			
			<div class="plan_info_block_body">
				<div class="plan_contents_list_left">
					<?php
						$imageRelativeUrl = !empty($commodityInfo['Commodity']['image_relative_url']) ?
							'../../img/commodity_reference/' . $commodityInfo['Client']['id'] . '/' . $commodityInfo['Commodity']['image_relative_url'] :
							'../../img/noimage.png';

						echo $this->Html->image($imageRelativeUrl, array('width' => '268', 'height' => 'auto', 'class' => 'plan_contents_img', 'alt' => ''));
					?>
				</div>
				<div class="plan_contents_list_center">
					<p class="plan_contents_name_wrap">
						<?php
							foreach ($commodityInfo['CarModel'] as $key => $carModel) {
								if ($carModel === reset($commodityInfo['CarModel'])) {
									$carModels = $carModel['name'];
								} else {
									$carModels .= '・'.$carModel['name'];
								}
							}
							$link_name = $commodityInfo['CarType']['name'] .'（'. $carModels;
							// 車種指定フラグ
							$flgModelSelect = ( !empty( $commodityInfo['CommodityItem']['car_model_id']) );
							( $flgModelSelect ) ? $link_name .= '）' : $link_name .= '他）';
						?>
						<span class="plan_contents_name"><?=$link_name;?></span>
					</p>
					<ul class="plan_car_spec_ul">
						<li class="plan_car_spec_li">
<?php
	// 喫煙・禁煙
	$smokingCarString = $smokingCarList[$commodityInfo['Commodity']['smoking_flg']];
	if($commodityInfo['Commodity']['smoking_flg'] == 0){
?>
							<p class="plan_car_spec is_no_smoking">
								<i class="icm-no_smoking"></i> <?=$smokingCarString;?>
							</p>
<?php
	}else if($commodityInfo['Commodity']['smoking_flg'] == 1){
?>
							<p class="plan_car_spec is_smoking">
								<i class="icm-smoking"></i> <?=$smokingCarString; ?>
							</p>
<?php
	}
?>
						</li>
						<li class="plan_car_spec_li">
							<p class="plan_car_spec">
								<?php
									// 定員
									$recommendedCapacity = Hash::extract($commodityInfo['CarModel'], '{n}.capacity');
									echo '定員'.$recommendedCapacity[0].'名';
								?>
							</p>
						</li>
						<li class="plan_car_spec_li">
							<p class="plan_car_spec is_car_model <?php if(!$flgModelSelect){ ?> is_inactive<?php } ?>"><i class="icm-car-side"></i> 車種指定</p>
						</li>
						<li class="plan_car_spec_li">
							<p class="plan_car_spec is_new_car <?php if($commodityInfo['Commodity']['new_car_registration'] != 1 &&  $commodityInfo['Commodity']['new_car_registration'] != 2){ ?> is_inactive<?php } ?>"><i class="icm-sparkle"></i> 新車</p>
						</li>
					</ul>
					
					<ul class="plan_equipment_ul">
						<li class="plan_equipment_li is_active">
							<p>免責補償</p>
							<aside class="plan_equipment_aside">免責補償料金込みプラン</aside>
						</li>
<?php
	foreach ($equipmentList as $equipmentId => $equipment) {
		$equipment = $equipment['Equipment'];
		if (!empty($commodityEquipment[$equipment['id']])) {
?>
						<li class="plan_equipment_li is_active">
							<p><?=$equipment['name']; ?></p>
							<aside class="plan_equipment_aside"><?=$equipment['description']; ?></aside>
						</li>
<?php
		}else{
?>
						<li class="plan_equipment_li">
							<p><?=$equipment['name']; ?></p>
						</li>
<?php
		}
	}
?>
<?php
				if ($commodityInfo['Commodity']['transmission_flg'] == 0) {
?>
						<li class="plan_equipment_li is_active">
							<p>AT車</p>
							<aside class="plan_equipment_aside">
								<p class="plan_equipment_description">オートマチックトランスミッションの車です</p>
							</aside>
						</li>
<?php
				}else if ($commodityInfo['Commodity']['transmission_flg'] == 1){
?>
						<li class="plan_equipment_li">
							<p>AT車</p>
						</li>
<?php
				}
?>
					</ul>

<?php
		$planName = $commodityInfo['Commodity']['name'];

		$CommodityItemid = $commodityInfo['CommodityItem']['id'];
?>
					<div class="plan_contents_list_plandetail">
						<a href="javascript:void(0);" class="js-modalOpen modal-open" data-code="<?= $commodityInfo['CommodityItem']['id']; ?>"><?= $planName; ?></a>
					</div>

				</div>
				<div class="plan_contents_list_right">
					<div class="payment_labels">
						<p class="menseki_label">免責補償込み</p>

						<?php //決済種別
							$payment_method = $commodityInfo['Commodity']['payment_method'];
							if(!is_null($payment_method)){
								switch ($payment_method){
									case '0':
										echo '<p class="payment_type">現地決済</p>';
										break;
									case '1':
										echo '<p class="payment_type">WEB決済限定料金</p>';
										break;
									case '2':
										echo '<p class="payment_type">WEB決済/現地決済</p>';
										break;
									default:
								}
							};
						?>
					</div>

<?php
	if(isset($estimationTotalPrice)){
		$labelPrice = "料金";
		$price = $estimationTotalPrice;
	} else {
		$labelPrice = "基本料金";
		$price = $basicCharge;
	}
?>
					<p class="plan_contents_price_title"><?= $labelPrice; ?> [<?= $rentalPeriod; ?>]</p>
					<p class="plan_contents_price">&yen;<?php echo number_format($price); ?><span>(税込)</span></p>
				</div>
			</div>
		</section>

	</div>
	<!-- wrap -->
	<script>
	$(function(){

		//プラン詳細を取得
		$('.js-modalOpen').on('click',function(){
			$(this).prop("disabled", true);
			var CommodityItemId = $(this).data('code');
			$('.modal-content-inner').scrollTop(0);
			$.ajax({
				url:'/rentacar/client/Commodities/getPlanInfo?id=' + CommodityItemId,
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
				$(function(){
					modalWindow('body');
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
</body>
</html>
