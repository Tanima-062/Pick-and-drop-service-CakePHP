<!-- このページは使われてない -->

<div id="js-content">
	<h2 class="title_blue_line"><span>予約内容未再送</span></h2>
	<section class="plan_form">
		<div class="inner">
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
			<table>
				<tbody>
					<tr>
						<th>メールアドレス</th>
						<td class="select_type_line1"><?php echo $this->Form->input('email', array('type' => 'email', 'value' => $emailAdress)); ?></td>
					</tr>
				</tbody>
			</table>
			<?php echo $this->Form->submit('予約内容を再送信する', array('class' => 'btn')); ?>
			<?php echo $this->Form->end(); ?>
		</div>
	</section>
</div>
