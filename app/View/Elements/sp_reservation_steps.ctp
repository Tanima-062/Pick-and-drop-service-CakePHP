
<?php
	if (($this->params['controller'] == 'reservations') && ($this->action == 'sp_step1' || 'sp_step2' || 'sp_completion')) {
		echo '<h2 class="reserve_step">';

		if ($this->action == 'sp_step1') {
			// STEP1
			if ($paymentMethod == 1) {
				echo $this->Html->image('reservation_step_bar/4steps-1_sp.png', array('alt' => '予約進行状況：ステップ1/4'));
			} else {
				echo $this->Html->image('reservation_step_bar/2steps-1_sp.png', array('alt' => '予約進行状況：ステップ1/2'));
			}
		} else if ($this->action == 'sp_step2') {
			// STEP2
			echo $this->Html->image('reservation_step_bar/4steps-2_sp.png', array('alt' => '予約進行状況：ステップ2/4'));
		} else if ($this->action == 'sp_completion') {
			// completion
			if (!$fromStep1) {
				echo $this->Html->image('reservation_step_bar/4steps-4_sp.png', array('alt' => '予約進行状況：ステップ4/4'));
			} else {
				echo $this->Html->image('reservation_step_bar/2steps-2_sp.png', array('alt' => '予約進行状況：ステップ2/2'));
			}
		}
		
		echo '</h2>';
	}
?>
