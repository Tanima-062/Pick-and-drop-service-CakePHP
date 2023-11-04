
<!-- 共通ヘッダー -->
<?php
	// ログイン時に付けるオプション
	$isLogin = '';
	if (_isLogin()) {
		$isLogin = ' is-login notice=\''. $_SESSION['user']['unread_cnt']. '\'';
	}
	
	// ラインメニュー表示オプションの条件
	$showLinemenu = '';
	if ( $this -> request -> here === '/rentacar/' ) { //afb流入含むトップのみ
		$showLinemenu = ' show-line-menu';
	}

	// バナー表示オプションの条件
	$showbanner = '';
	if ( $this -> request -> here === '/rentacar/' ) { //afb流入含むトップのみ
		$showBanner = ' show-banner';
	}

	// アプリ訴求バナー表示オプションの条件
	$showAppPromotion = '';
	if ($this->params['controller'] != 'reservations') { //予約導線以外
		$showAppPromotion = ' show-app-promotion';
	}
?>

<?php
	if ( //afb流入のトップ~決済直前画面まで
		(!empty($is_afb)) && (
		($this->params['controller'] === 'tops') 
		|| ($this->params['controller'] === 'searches') 
		|| ($this->params['controller'] === 'plan') 
		|| (($this->params['controller'] === 'reservations') && ($this->action === 'sp_processing'))
		)){
?>
		<skyticket-header-sp<?= $isLogin ?> service='rentacar'<?= $showBanner; ?><?= $showAppPromotion; ?>></skyticket-header-sp>
<?php
	} else { //全体通して基本バージョンの共通ヘッダー
?>	
		<skyticket-header-sp<?= $isLogin ?> service='rentacar' language='' country='' currency=''<?= $showLinemenu; ?><?= $showBanner; ?><?= $showAppPromotion; ?>></skyticket-header-sp>
<?php
	}
?>

<?php
// Typescript----------
// type islogin = boolean　// メニューの中身などが変わる
// type notice = number // 赤丸で表示される通知の数。0のとき赤丸は表示されなくなる
// type service = "da" | "tour" | "hotel" | "gourmet-takeout" | "rentacar" | "dp" | "tour-train" | "bus" | "ferry" | "ia" | "dp-ia" | "guide" | "wifi" | "insurance" | "premium" // サービスの増減で変更
// type showlinemenu = boolean // falseのときヘッダーが青色の部分だけになる（白の水平メニュー消える）
?>
