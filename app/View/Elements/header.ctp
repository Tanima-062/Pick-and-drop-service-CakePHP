<div style="min-width: 1000px;">
<!-- 共通ヘッダー -->
<?php
	// ログイン時に付けるオプション
	$isLogin = '';
	if (_isLogin()) {
		$isLogin = ' is-login notice=\''. $_SESSION['user']['unread_cnt']. '\'';
	}

	// バナー表示オプションの条件
	$showbanner = '';
	if ( $this -> request -> here === '/rentacar/' ) { //afb流入含むトップのみ
		$showBanner = ' show-banner';
	}
?>

<?php
	if ( //afb流入のトップ~決済直前画面まで
		(!empty($is_afb)) && (
		($this->params['controller'] === 'tops') 
		|| ($this->params['controller'] === 'searches') 
		|| ($this->params['controller'] === 'plan') 
		|| (($this->params['controller'] === 'reservations') && ($this->action === 'processing'))
		)){
?>
        <skyticket-header-pc-tb<?= $isLogin ?> service='rentacar'<?= $showBanner ?>></skyticket-header-pc-tb>

<?php
	} else { //全体通して基本バージョンの共通ヘッダー
?>	
        <skyticket-header-pc-tb<?= $isLogin ?> service='rentacar' language='' country='' currency='' show-line-menu<?= $showBanner ?>></skyticket-header-pc-tb>
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
</div>
