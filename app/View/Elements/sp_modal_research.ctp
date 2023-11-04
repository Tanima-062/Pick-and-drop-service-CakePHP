<div class="js-modalfs-window modalfs-window">
	<section class="modal_contents_wrap">
		<div class="modal_header">
			<div class="modal-title">詳細検索/絞り込み</div>
			<div class="js-modalfs_close btn-close">
				<i class="icm-modal-close"></i>
			</div>
		</div>
<?php
	echo $this->Form->create('Search',
		array(
			'action'=>'index',
			'id'=>'SearchIndexForm',
			'inputDefaults'=>array(
				'label'=>false,
				'div'=>false,
				'hiddenField'=>false,
				'legend'=>false,
				'fieldset'=>false
			),
			'type'=>'get'
		)
	);
?>

		<div class="modal_contents">

			<section class="search_section">
				<?php
					// 出発日時や返却日時などは共通項目のためエレメント化
					echo $this->element('sp_new_searchform_main');
					// 詳細条件検索をエレメント化
					echo $this->element('sp_searchform_options');
				?>
			</section>		

		</div>

		<div class="modal_footer">
			<div id="search_form_floating" class="btm_button_wrap">
				
<?php
	if (!empty($current_sort)) {
		echo $this->Form->hidden('sort', array('value' => $current_sort));
	}
	if (!empty($isList)) { // マップ検索で再検索する時も常にtype=mapを維持するため
		echo $this->Form->hidden('type', array('value' => 'list'));
	} else {
		echo $this->Form->hidden('type', array('value' => 'map'));
	}
	echo $this->element('sp_searchform_submit');
?>
			</div>
		</div>
<?php	
	echo $this->Form->end();
?>

	</section>
</div>
