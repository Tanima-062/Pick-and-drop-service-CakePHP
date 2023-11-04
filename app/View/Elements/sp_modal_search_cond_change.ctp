<div class="js-modalf_overlay modalf-overlay"></div>

<div class="js-modalf-window modalf-window modal_search-cond-change">
	<section class="modal_contents_wrap">
		<div class="modal_header">
			<div class="modal-title">条件変更</div>
			<div class="js-modalf_close btn-close">
				<i class="icm-modal-close"></i>
			</div>
		</div>
		<div class="modal_contents">

			<section class="search_section">
				<?php
					// 出発日時や返却日時などは共通項目のためエレメント化
					echo $this->element('sp_new_searchform_main_ab');
				?>
			</section>		

		</div>

		<div class="modal_footer">
			<div class="btm_button_wrap">
				
<?php
	echo $this->element('sp_searchform_submit');
?>
			</div>
		</div>
	</section>
</div>
