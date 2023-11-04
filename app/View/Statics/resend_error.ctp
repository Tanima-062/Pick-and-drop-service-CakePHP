<!-- このページは使われてない -->

<main class="wrap contents clearfix">

	<!-- パンくずsection -->
	<section>
	<?php
		echo $this->element('progress_bar'); 
	?>
	</section>
	<!-- /パンくずsection -->

	<div class="st-table rent-margin-bottom">
		<div class="h3_wrap st-table">
			<div class="st-table_cell">
				<h3>予約内容未再送</h3>
			</div>
		</div>
	</div>

	<p>
		<span style="color:red;">入力いただいたメールアドレスでは、ご予約確認できませんでしたので、予約内容を再送できませんでした。</span><br>
		<br>
		もう一度、入力いただいたメールアドレスに誤りがないかご確認お願いいたします。<br>
		もし、誤りがない場合、予約時にご登録いただいたメールアドレスに誤りがあった可能性がございます。<br>
		<br>
		予約番号を控えている方は<?php echo $this->Html->link('予約内容確認ページ', '/mypages/login/'); ?>でメールアドレスをご確認ください。<br>
		<br>
	</p>

	<?php echo $this->Form->create(false, array('url' => '/resend/', 'inputDefaults' => array('label' => false, 'div' => false))); ?>
	<table class="contents_confirm_tbl rent-margin-bottom">
		<tr>
			<th>メールアドレス</th>
			<td><?php echo $this->Form->input('email', array('type' => 'email', 'class' => 'rent-input width_half', 'value' => $emailAdress)); ?></td>
		</tr>
	</table>
	<?php echo $this->Form->submit('予約内容を送信する', array('class' => 'btn btn_submit rent-margin-bottom-important text_bold')); ?>
	<?php echo $this->Form->end(); ?>

</main>