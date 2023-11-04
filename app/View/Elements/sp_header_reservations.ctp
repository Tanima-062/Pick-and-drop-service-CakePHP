    <!-- ヘッダー step1/step2-->
    <header id="header" class="header_reservations">
        <div class="header_wrap">
            <div class="back_button_wrap">
                <?php 
                    if ($this->action == 'sp_step1') {
                        echo '<a href="';
                        echo $refererPlan;
                        echo '" class="back_button">';
                    } else if ($this->action == 'sp_step2') {
                        echo '<a href="';
                        echo '/rentacar/reservations/step1/'.$this->data['Reservation']['uniqId'].'/';
                        echo '" class="back_button">';
                    }
                ?>
					<i class="icm-arrow back_arrow"></i>
				</a>
			</div>
			<?php 
				if ($this->action == 'sp_step1') {
					echo '<h1>お客様情報の入力</h1>';
				} else if ($this->action == 'sp_step2') {
					echo '<h1>申込内容の確認</h1>';
				}
			?>
        </div><!-- [/.header_wrap] -->
    </header><!-- header End -->
