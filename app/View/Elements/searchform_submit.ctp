<!-- 検索フォーム　SUBMITボタンパート -->
<div class="searchform_submit_section">
	<p id="js-searchform_error" class="searchform_error" style="display:none;"></p>

<?php
	// ページごとの文言分岐がある場合はこちらで分岐を記述する

	if ($this->params['controller'] == 'tops' && $this->action == 'index') { // TOP

		echo $this->Form->button('レンタカーの最安値を検索',array('class'=>'js-btn_search_submit btn-type-primary', 'data-ga_category'=>'pc_top', 'data-ga_label'=>'トップ検索ボタン'));

	} else if ($this->params['controller'] == 'searches' && $this->action == 'index') { // Searches

		echo $this->Form->button('上記の条件で再検索',array('class'=>'js-btn_search_submit btn-type-primary', 'data-ga_category'=>'pc_searches', 'data-ga_label'=>'詳細検索ボタン'));

	} else {

		echo $this->Form->button('レンタカーを検索',array('class'=>'js-btn_search_submit btn-type-primary'));

	}

?>
</div>