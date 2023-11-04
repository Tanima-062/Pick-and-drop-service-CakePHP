<!-- 検索フォーム　SUBMITボタンパート -->
<p id="js-searchform_error" class="searchform_error" style="display:none;"></p>

<?php
	// ページごとの文言分岐がある場合はこちらで分岐を記述する

	if ($this->params['controller'] == 'tops' && $this->action == 'sp_index') { // TOP

		echo $this->Form->button('最安値を検索',array('class'=>'js-btn_search_submit btn-type-primary','div'=>false, 'data-ga_category'=>'sp_top', 'data-ga_label'=>'トップ検索ボタン'));

	} else if ($this->params['controller'] == 'searches' && $this->action == 'sp_index') { // Searches

		echo $this->Form->button('この条件で最安値を検索',array('class'=>'js-btn_search_submit btn-type-primary', 'div'=>false, 'data-ga_category'=>'sp_search_form', 'data-ga_label'=>'フローティングボタン'));

	} else {

		echo $this->Form->button('レンタカーを検索',array('class'=>'js-btn_search_submit btn-type-primary','div'=>false));
		
	}

?>