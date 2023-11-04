<div id="js_company_list" class="variable_cont_area">
	<ul class="company_search_ul">
<?php
	$company_counter = 0;
	foreach($clientList as $clientData){
		if( !empty($clientData['sp_logo_image']) ){
			$company_counter++;
?>
		<li class="company_search_li<?php if($company_counter > 5){?> variable_cont_hidden<?php }?>">
			<a href="/rentacar/company/<?=$clientData['url']?>/" class="company_search_table">
				<div class="company_search_cell">
					<div class="company_logo_square">
						<img src="/rentacar/img/logo/square/<?=$clientData['id'];?>/<?=$clientData['sp_logo_image'];?>" alt="<?=$clientData['name'];?>のロゴ" height="45" width="45" class="company_img"  loading="lazy" importance="low" decoding="async"/>
					</div>
				</div>
				<div class="company_search_cell">
					<span class="company_search_name">
						<h3><?=$clientData['name'];?></h3>
					</span>
<?php
			if( !empty($clientRatings[$clientData['id']]) ){
				$clientRatingsData = $clientRatings[$clientData['id']];
?>
					<span class="company_rate_star">
<?php
				for($i=1;$i<6;$i++) {
					if ($i <= $clientRatingsData['rating']) {
						echo '<i class="icm-star-full"></i>';
					} else if ($i-1 <= $clientRatingsData['rating']) {
						echo '<i class="icm-star-half"></i>';
					} else {
						echo '<i class="icm-star-empty"></i>';
					}
				}
?>
					</span>
					<span class="company_rate_count">
						<?=$clientRatingsData['rating'];?>（<?=$clientRatingsData['count'];?>件）
					</span>
<?php
			}
?>
				</div>
			</a>
			<i class="icm-right-arrow"></i>
		</li>
<?php
		}
	}
?>
	</ul>
</div>

<?php
	if( $company_counter > 5 ){
?>
<div class="variable_cont_more">
	<a href="javascript:void(0);" id="js_btn_more_company" class="btn_more_cont">
		もっと見る&nbsp;<i class="icm-right-arrow icon-right-arrow_down"></i>
	</a>
</div>
<?php
	}
?>

<script>
$(function(){

	// more CompanyList
	var heightCompanyList = $("#js_company_list > ul").height();
	$("#js_company_list").height( heightCompanyList );
	$("#js_btn_more_company").on("click", function(){
		$(this).toggleClass("show_more");
		$("#js_company_list .variable_cont_hidden").toggle();

		var heightListAfter = $("#js_company_list > ul").height();
		$("#js_company_list").height( heightListAfter );

		// ボタン切り替え
		if ($(this).hasClass("show_more")) {
			$(this).html('閉じる&nbsp;<i class="icm-right-arrow icon-right-arrow_up"></i>');
		} else {
			$(this).html('もっと見る&nbsp;<i class="icm-right-arrow icon-right-arrow_down"></i>');
		}
	});
});
</script>