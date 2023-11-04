<!-- 詳細条件検索 -->
<div class="searchform_options_section">

    <div class="search_detail">
        <h2 class="search_detail_title">車両タイプ</h2>

<?php
    // TOP以外で表示
    if ($this->params['controller'] != 'tops' || $this->action != 'sp_index') {
?>

        <div class="js-about_price about_price">
            <i class="icm-info-button-fill"></i>
            <span class="about_price_text">表示金額について</span>
            <aside class="js-about_price_aside_ab about_price_aside_ab">
                <p>
                    表示の金額は検索時の条件に基づく車両タイプ別の最安値となります。<br>
                    日付や貸出場所の変更により、表示の最安値と検索結果の内容に差異が発生する場合があります。
                </p>
            </aside>
        </div>

<?php
    }
?>

        <ul class="select_car_type">
<?php
	foreach ($carTypeInfo as $val) {
		$val = $val['CarType'];
		$default = 0;
		$default = in_array($val['id'], $carTypeArray) ? $val['id'] : 0;
?>
            <li class="select_car_type_item">
									
<?php
		echo $this->Form->checkbox('car_type[]', array('id' => 'select_option_chk' . $val['id'] . '_ab', 'value' => $val['id'], 'hiddenField' => false, 'default' => $default, 'class'=>'form-checkbox'));
?>
                <label for="select_option_chk<?= $val['id'] ?>_ab">
                    <div class="cartype-name"><?= $val['name']; ?></div>
                    <div class="cartype-detail-wrapper">
                        <img src="/rentacar/img/car_type_<?=sprintf('%02d', $val['id'])?>.png" alt="<?=$val['name']?>" />
                        <div>
                            <div class="cartype-desc"><?= $val['description'] ?></div>

                    

<?php
        // TOP以外で表示
        if ($this->params['controller'] != 'tops' || $this->action != 'sp_index') {
            if ($val['lowestPrice']) {
                $lowestPrice = '¥' . number_format($val['lowestPrice']) . '〜';
?>

                            <div class="cartype-lowest-price"><?= $lowestPrice ?></div>
                        </div>

<?php
		    }
        }
?>
                    </div>
                </label>
            </li>
<?php
	}
?>
        </ul>
    </div>

<?php
    // TOP以外で表示
    if ($this->params['controller'] != 'tops' || $this->action != 'sp_index') {
?>

    <div class="search_detail">
        <h2 class="search_detail_title">禁煙/喫煙</h2>
        <div class="select_smoking">
            <?= $this->Form->input('smoking_flg', $spSmokingOptions); ?>
        </div>
    </div>

<?php
    }
?>


    <div class="search_detail">
        <h2 class="search_detail_title">車両オプション</h2>
        <ul class="select_option">

<?php
	foreach($options as $optionKey=> $val) {
		$default = 0;
		if(in_array($optionKey,$optionValueArray)) {
			$default = $optionKey;
		}
?>
            <li class="select_option_item">
<?php
		echo $this->Form->checkbox('option[]',array('id'=>'select_option_chk_'.$optionKey.'_ab','value'=>$optionKey,'hiddenField'=>false,'default'=>$default, 'class'=>'form-checkbox'));

		echo '<label for="select_option_chk_' . $optionKey . '_ab">' . htmlspecialchars($val) . '</label>';
?>
            </li>
<?php

	}
?>
        </ul>
    </div>


<?php
    if (!$fromRentacarClient && !isset($client_id_from_company_page)) {
?>
    <div class="search_detail">
        <h2 class="search_detail_title">レンタカー会社を指定</h2>
        <div class="select_client">
            <label class="form-select">
                <?= $this->Form->input('client_id', $clientIdOptions); ?>
            </label>
        </div>
    </div>
<?php
    }
?>
</div>

<script>
// 「表示金額について」表示切替
$(function(){
	$(document).on('click',function(e) {
		if(!$(e.target).closest('.js-about_price').length) {
			// 「表示金額について」以外のエリアをクリック時は非表示
			$('.js-about_price_aside_ab').hide();
		} else {
			// 「表示金額について」クリック時は表示切替
			$('.js-about_price_aside_ab').toggle();
		}
	});
});
</script>