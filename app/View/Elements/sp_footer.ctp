
<!-- 共通フッター -->
<?php
		if (_isLogin()) {	//ログイン時の表示
?>
	<skyticket-footer-sp is-login></skyticket-footer-sp>
<?php
		} else {	//ログインしていない時の表示
?>
	<skyticket-footer-sp></skyticket-footer-sp>
<?php
		}
?>
