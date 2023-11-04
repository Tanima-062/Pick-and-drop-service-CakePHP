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
				<h3>予約内容再送</h3>
			</div>
		</div>
	</div>
	<?php echo $this->Form->create(false, array(
			'url' => '/resend/',
			'inputDefaults' => array('label' => false, 'div' => false))); ?>
	<table class="contents_confirm_tbl rent-margin-bottom">
		<tr>
			<th>メールアドレス</th>
			<td><?php echo $this->Form->input('email', array('type' => 'email', 'class' => 'rent-input width_half')); ?></td>
		</tr>
	</table>
	<?php echo $this->Form->submit('予約内容を再送信する', array('class' => 'btn btn_cancel rent-margin-bottom-important')); ?>
	<?php echo $this->Form->end(); ?>
</main>