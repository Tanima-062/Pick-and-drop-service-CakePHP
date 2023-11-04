<div class="js-modalfs-window modalfs-window">
	<section class="modal_contents_wrap">
		<div class="modal_header-filter">
			<div class="js-modalfs_close btn-close">
				<i class="icm-modal-close"></i>
			</div>
			<div class="modal-title">絞り込み</div>
			<a href="javascript: void(0);" class="js-search_reset btn-reset">リセット</a>
		</div>
		<div class="modal_contents">

			<section class="search_section">
				<?php
					// 詳細条件検索をエレメント化
					echo $this->element('sp_searchform_options_ab');
				?>
			</section>		

		</div>

		<div class="modal_footer">
			<div class="btm_button_wrap">
				
<?php
	echo $this->element('sp_searchform_submit_ab');
?>
			</div>
		</div>
	</section>
</div>
