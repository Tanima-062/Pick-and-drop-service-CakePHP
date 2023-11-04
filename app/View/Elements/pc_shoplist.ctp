<?php 
	echo $this->Html->script(['/js/pc_shoplist'],['inline' => false]); 
?>

<div class="js-shoplist-wrap shoplist-wrap">
	<ul class="shoplist-items">

<?php
	$shopList = [];

	if ($this->params['controller'] === 'station') {
		// 駅ページでは、設定されたおすすめ順に配列を並べ替える
		for($i = 0; $i < count($recommendOfficeIds); $i++){
			foreach ($officeInfoList as $key => $value) {
				if ($key == $recommendOfficeIds[$i]){
					$shopList[$key] = $officeInfoList[$key];
					break;
				}
			}
		}
	} else {
		// デフォルト順のままで使用
		$shopList = $officeInfoList;
	}
?>
<?php
	foreach($shopList as $eachShop){
		$shopInfo = [];
		$companyInfo = [];
		$searchPageUrl = [];

		if ($this->params['controller'] === 'station') {
			$shopInfo = $eachShop;
			$companyInfo = $clientList[$eachShop['client_id']];
			$searchPageUrl = $search['baseUrl'];
		} else {
			$shopInfo = $eachShop['Office'];
			$companyInfo = $eachShop['Client'];
			$searchPageUrl = $search['url'];
		}
?>
		<li class="js-shoplist-item-wrap shoplist-item-wrap">
			<div class="shoplist-item-header">
				<div class="logo-wrap">
<?php
		if($companyInfo['sp_logo_image']){
?>
					<img 
					src="/rentacar/img/logo/square/<?= $companyInfo['id']; ?>/<?= $companyInfo['sp_logo_image']; ?>" 
					alt="<?= $companyInfo['name']; ?>" 
					loading="lazy" importance="low" decoding="async">
<?php
		}
?>
				</div>

				<div>
					<h3 class="shop-name"><?= $companyInfo['name'] ?>&nbsp;|&nbsp;<?= $shopInfo['name'] ?></h3>
				</div>
			</div>
			
			<div class="shoplist-item-contents">
				<ul class="shop-info">
					<li>
						<dl>
							<dt>営業時間</dt>
							<dd><?= $shopInfo['businessHours']; ?></dd>
						</dl>
					</li>
					<li>
						<dl>
							<dt>アクセス</dt>
							<dd><?= $shopInfo['access_dynamic']; ?></dd>
						</dl>
					</li>
					<li>
						<dl>
							<dt>住所</dt>
							<dd><?= $shopInfo['address']; ?></dd>
						</dl>
					</li>
				</ul>
				<div class="btn-wrap">
					<a class="shoplink" href="/rentacar/company/<?= $companyInfo['url']; ?>/<?= $shopInfo['url']; ?>/">店舗詳細ページはこちら</a>

					<a class="btn-type-primary" href="<?= $this->CreateUrl->view($searchPageUrl,'client_id='.$companyInfo['id']); ?>">
						この店舗でレンタカーを探す
					</a>
				</div>
			</div>
		</li>
<?php
	}
?>
	</ul>

<?php
	if (count($shopList) > 5) {
?>
	<div class="show-more-shops-wrap">
		<a class="js-show-more-shops">すべての店舗を見る</a>
		<i class="icm-right-arrow icon-right-arrow_down"></i>
	</div>
<?php
	}
?>
</div>
