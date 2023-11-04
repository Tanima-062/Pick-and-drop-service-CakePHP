<?php
	if ($this->params['controller'] === 'company') {
	// 会社ページ（h1タグ使用、など）
?>



<h1 class="company_title">
<?php
		if($clientInfo['Client']['sp_logo_image']){
?>
	<img src="/rentacar/img/logo/square/<?php echo $clientInfo['Client']['id']; ?>/<?php echo $clientInfo['Client']['sp_logo_image']; ?>" class="company_icon" alt="<?=$clientInfo['Client']['name']; ?>" width="32" height="32" loading="lazy" importance="low" decoding="async""/>
<?php
		}
?>
	<div class="company_name"><?=$clientInfo['Client']['name']; ?>の予約・プラン比較</div>
</h1>



<?php
	} else if ($this->params['controller'] === 'wpcampaign') {
	// キャンペーンページ（h2タグ使用、など）
?>



<h2 class="company_title">
<?php
		if($clientInfo['Client']['sp_logo_image']){
?>
	<img src="/rentacar/img/logo/square/<?php echo $clientInfo['Client']['id']; ?>/<?php echo $clientInfo['Client']['sp_logo_image']; ?>" class="company_icon" alt="<?php echo $clientInfo['Client']['name']; ?>" width="32" height="32" loading="lazy" importance="low" decoding="async"/>
<?php
		}
?>
	<div class="company_name">
		<?=$data['cp-head-title']; ?>
	</div>
</h2>



<?php
	}
?>
