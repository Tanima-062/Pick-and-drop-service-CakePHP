<!-- 詳細条件検索 -->
<div class="searchform_options_section">

    <ul class="option_tab_ul" role="radiogroup">
        <li class="option_tab_li">
            <button type="button" class="btn_option_tab is_selected" data-section="car_type" role="radio" aria-selected="true">車両タイプ</button>
        </li>
        <li class="option_tab_li">
            <button type="button" class="btn_option_tab" data-section="option" role="radio" aria-selected="false">オプション</button>
        </li>
    <?php if (!$fromRentacarClient && !isset($client_id_from_company_page)) { ?>
        <li class="option_tab_li">
            <button type="button" class="btn_option_tab" data-section="company" role="radio" aria-selected="false">人気のレンタカー会社</button>
        </li>
    <?php } ?>
    </ul>



    <section id="search_select_car_type" class="js-options_tab_panel options_tab_panel" aria-hidden="false">
        <fieldset class="search_fieldset">
            <legend class="search_legend">車両タイプ</legend>

<?php
    // TOP以外で表示
    if ($this->params['controller'] != 'tops' || $this->action != 'index') {
?>

            <div class="js-about_price about_price">
                <i class="icm-info-button-fill"></i>
                <span class="about_price_text">表示金額について</span>
                <aside class="js-about_price_aside about_price_aside">
                    <p>
                        表示の金額は検索時の条件に基づく車両タイプ別の最安値となります。<br>
                        日付や貸出場所の変更により、表示の最安値と検索結果の内容に差異が発生する場合があります。
                    </p>
                </aside>
            </div>

<?php
    }
?>




            <div class="selected_all_wrap">
<?php
    // echo $this->Form->input('car_type_select', array(
    // 	'options' => array('1'=>'全て選択する', '2'=>'全て解除する'),
    // 	'type' => 'radio', 'class' => 'selected_all_radio', 'label' => true, 'default' => 1
    // ));
?>
            </div>



            <ul class="car_type_ul">
<?php
    foreach ($carTypeInfo as $carType) {
        $carType = $carType['CarType'];
        $checked = false;
        if(!empty($carTypeArray)){
            $checked = in_array($carType['id'], $carTypeArray) ? true : false;
        }
?>
                <li class="car_type_li">

<?php
        echo $this->Form->checkbox('car_type[]', array('id' => 'SearchCarType'.$carType['id'], 'value' => $carType['id'], 'hiddenField' => false, 'class' => 'car_type_input', 'checked' => $checked));
?>
                    <div class="btn_car_type_wrap">
                        <label for="SearchCarType<?=$carType['id']?>" class="btn_car_type">
                        
                            <i class="icm-checkbox-checked"></i>
                            <span class="car_type_checkbox"></span>
                            <span class="car_type_name"><?=$carType['name']?></span>
                            <img src="/rentacar/img/car_type_<?=sprintf('%02d', $carType['id'])?>.png" alt="<?=$carType['name']?>" />
                                
                            <span class="car_type_capacity"><?=$carType['description']?></span>

                        </label>
                    </div>

<?php
    // TOP以外で表示
        if ($this->params['controller'] != 'tops' || $this->action != 'index') {
            if (!empty($carType['lowestPrice'])) {
?>
                    <p class="lowestprice-bycartype">¥<?=number_format($carType['lowestPrice'])?>〜</p>
<?php
            }
        }
?>
                </li>
<?php
    }
?>
            </ul>
        </fieldset>
    </section>



    <section id="search_select_option" class="js-options_tab_panel options_tab_panel" aria-hidden="true">
        <fieldset class="search_fieldset">
            <legend class="search_legend">オプション</legend>
            <div class="search_select_option_list_wrap form-checkbox-wrap">
<?php
    echo $this->Form->input('option', array_merge($optionOptions, array("class"=>"equip_chk")));
?>
            </div>
        </fieldset>
    </section>


<?php
    if (!$fromRentacarClient && !isset($client_id_from_company_page)) {
?>
    <section id="search_select_company" class="js-options_tab_panel options_tab_panel" aria-hidden="true">
        <fieldset class="search_fieldset">
            <legend class="search_legend">人気の会社</legend>
            <div class="selected_all_wrap">
<?php
        echo $this->Form->input('area_type', array_merge($areaTypeOptions, array('class' => 'selected_all_radio')) );
?>
            </div>

            <div class="search_select_company_list_wrap form-checkbox-wrap">
<?php
        echo $this->Form->input('client_id', array_merge($clientIdOptions, array('class' => 'client_chk')) );
?>
            </div>
        </fieldset>
    </section>
<?php
    }
?>
</div>

<script>
// 「表示金額について」表示切替
$(function () {
    $('.js-about_price_aside').hide();
    $('.js-about_price').hover(
        // 「表示金額について」にマウスオーバー時は表示
        function() {
            $('.js-about_price_aside').show();
        }, 
        // 「表示金額について」からマウス外れた時は非表示
        function(){
            $('.js-about_price_aside').hide();
        }
    );
});
</script>




