
<?php
	if (($this->params['controller'] == 'reservations') && ($this->action == 'step1' || 'step2' || 'sp_completion')) {
		echo '<section class="reservation_step">';

		if ($this->action == 'step1') {
			// STEP1
			if ($paymentMethod == 1) {
				echo $this->Html->image('reservation_step_bar/4steps-1_pc.png', array('alt' => '予約進行状況：ステップ1/4'));
			} else {
				echo $this->Html->image('reservation_step_bar/2steps-1_pc.png', array('alt' => '予約進行状況：ステップ1/2'));
			}
		} else if ($this->action == 'step2') {
			// STEP2
			echo $this->Html->image('reservation_step_bar/4steps-2_pc.png', array('alt' => '予約進行状況：ステップ2/4'));
		} else if ($this->action == 'completion') {
			// completion
			if (!$fromStep1) {
				echo $this->Html->image('reservation_step_bar/4steps-4_pc.png', array('alt' => '予約進行状況：ステップ4/4'));
			}else{
				echo $this->Html->image('reservation_step_bar/2steps-2_pc.png', array('alt' => '予約進行状況：ステップ2/2'));
			}
		}
		
		echo '</section>';
	}
?>
