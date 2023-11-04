<div class="session_message_wrap">
<?php
	foreach ($sessionMessage as $key => $message) {
?>
	<div class="session_message">
		<i class="icm-warning"></i>
		<div class="session-message-text"><?php echo $message; ?></div>
	</div>
<?php
	}
?>
</div>