<!-- このページは使われてない -->

<div id="js-content">
	<h2 class="title_blue_line"><span>予約内容再送</span></h2>
	<section class="plan_form">
		<div class="inner">
		<?php echo $this->Form->create(false, array(
				'url' => '/resend/',
				'inputDefaults' => array(
						'label' => false,
						'div' => false,
				),
		)); ?>
			<table>
				<tbody>
					<tr>
						<th>メールアドレス</th>
						<td class="select_type_line1"><?php echo $this->Form->input('email', array('type' => 'email')); ?></td>
					</tr>
				</tbody>
			</table>
			<?php echo $this->Form->submit('予約内容を再送信する', array('class' => 'btn btn btn_cancel rent-margin-bottom-important')); ?>
		<?php echo $this->Form->end(); ?>
		</div>
	</section>
</div>
