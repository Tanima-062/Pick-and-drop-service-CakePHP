<!-- 共通フッター -->
<div style="min-width: 1000px;">
<?php
	if (_isLogin()) {	//ログイン時の表示
?>
	<skyticket-footer-pc is-login></skyticket-footer-pc>
<?php
	} else {	//ログインしていない時の表示
?>
	<skyticket-footer-pc></skyticket-footer-pc>
<?php
	}
?>
</div>