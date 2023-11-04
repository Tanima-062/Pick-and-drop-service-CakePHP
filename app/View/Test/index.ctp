<?php $this->Html->css(array('swiper.min', 'jquery.fancybox-1.3.4'), null, array('inline' => false)); ?>
<?php // $this->Html->script(array('plan', 'swiper.min', 'jquery.fancybox-1.3.4'), array('inline' => false)); ?>
  <div class="wrap contents clearfix">
    <!-- パンくずリスト -->
    <div id="progress" class="rent-margin-bottom">
      <ul class="breadcrumb">
        <li class="breadcrumb-list">
          <?php echo $this->Html->link('トップ','/');?>
        </li>
        <li class="breadcrumb-list">
          <a>検索</a>
        </li>
        <li class="breadcrumb-list">
          <a>検索結果・レンタカー選択</a>
        </li>
        <li class="breadcrumb-list is-current">
          <a>プラン詳細</a>
        </li>
        <li class="breadcrumb-list">
          <a>お客様情報入力</a>
        </li>
        <li class="breadcrumb-list">
          <a>申込内容確認</a>
        </li>
        <li class="breadcrumb-list">
          <a>申込完了</a>
        </li>
      </ul>
    </div>
    <!-- /パンくずリスト -->

    <?php if (!empty($sessionMessage)) { ?>
    <?php echo $this->element('session_message'); ?>
    <?php } ?>

<?php echo $this->element('plan_view'); ?>

    <div class="st-table rent-margin-bottom-l">
      <div class="h3_wrap st-table rent-margin-bottom">
        <div class="st-table_cell">
          <h3>プランの詳細</h3>
        </div>
      </div>
      <div class="st-table">
        <div class="st-table_cell is-col2" style="vertical-align:top;">
          <?php echo nl2br($commodityInfo['Commodity']['description']); ?>
        </div>
        <div class="st-table_cell is-col2">
            <?php if (!empty($commodityImages[0]['image_relative_url'])) { ?>
		<div class="swiper-container">
                    <div class="swiper-wrapper" style="width:420px;">
			<?php foreach ($commodityImages as $key => $carReference) { ?>
                            <?php if (!empty($carReference['image_relative_url'])) { ?>
				<div class="swiper-slide">
                                    <a class="grouped_elements" title="<?php echo nl2br($carReference['remark']); ?>" rel="group1" href="<?php echo $this->Html->url('/img/commodity_reference/'.$commodityInfo['Client']['id'].'/'.$carReference['image_relative_url']); ?>" style="margin:0 auto;display:block;width:420px;height:310px;">
					<?php echo $this->Html->image('commodity_reference/'.$commodityInfo['Client']['id'].'/'.$carReference['image_relative_url'], array('title' => $carReference['remark'], 'style' => 'width:100%;')); ?>
                                    </a>
                                    <p style="width:420px;margin:0 auto;"><?php echo nl2br($carReference['remark']); ?></p>
				</div>
                            <?php } ?>
			<?php } ?>
                    </div>
                    <div class="swiper-button-prev" style="background:none;"><?php echo $this->Html->image('/img/btn_return.png'); ?></div>
                    <div class="swiper-button-next" style="background:none;"><?php echo $this->Html->image('/img/btn_next.png'); ?></div>
		</div>
                <div class="contents_result_detail_remarks_body">
                    <p style="font-weight:bold;">参考車両イメージ
                    <span style="font-weight:normal;font-size:10px;">
                    <?php
                        foreach ($commodityInfo['CarModel'] as $key => $carModel) {
                            if ($carModel === reset($commodityInfo['CarModel'])) {
				echo $carModel['name'];
                            } else {
				echo '・'.$carModel['name'];
                            }
			}
                    ?>
                    </span>
                    </p>
                    <p style="font-size:10px;">※写真はイメージです</p>
		</div>
            <?php } ?>
<?php	if (!empty($commodityInfo['Commodity']['remark'])) {	?>
          <div class="contents_result_detail_remarks_hd st-table st-table_head bg-deepGray">
            <h4>備考</h4>
          </div>
          <div class="contents_result_detail_remarks contents_result_detail_remarks_body">
            <?php echo nl2br($commodityInfo['Commodity']['remark']); ?>
          </div>
<?php	}	?>
        </div>
      </div>
    </div>
<?php echo $this->Form->create('Reservation', array(
			'type' => 'post',
			'url' => 'step1/',
			'class' => 'st-table rent-margin-bottom-l',
			'inputDefaults' => array(
					'div' => false,
					'label' => false,
					'legend' => false,
			),
	)); ?>

      <div class="h3_wrap st-table rent-margin-bottom">
        <div class="st-table_cell">
          <h3>お見積り</h3>
        </div>
      </div>

      <h4 class="hd-left-bordered">貸出期間</h4>
      <table class="contents_detail_tbl rent-margin-bottom">
        <tr>
          <th>
            <span class="va-middle">貸出期間</span>
            <span class="label-require va-middle">必須</span>
          </th>
          <td>
           <div><?php echo date('Y年m月d日 H時i分', strtotime($requestData['from']));; ?> ～ <?php echo date('Y年m月d日 H時i分', strtotime($requestData['to'])); ?></div>
          </td>
        </tr>
      </table>

      <h4 class="hd-left-bordered">車両の受取・返却</h4>
      <table class="contents_detail_tbl rent-margin-bottom">
        <tr>
          <th>
            <span class="va-middle">受取営業所</span>
            <span class="label-require va-middle">必須</span>
          </th>
          <td>
            <div id="officeStock" class="search_select" style="margin-bottom: 10px;">
              <span class="search_select_inner">
                <?php
                echo $this->Form->input('from_office', array(
                  'type' => 'select',
                  'options' => $fromOfficeList,
                  'default' => $this->request->data['from_office']
                ));
                ?>
              </span>
            </div>
            <div>
              <?php foreach ($officeDatas as $key => $officeData) { ?>
              <div id="fromOfficeId<?php echo $officeData['Office']['id']; ?>" class="from-office office-data">
                <p><span class="label-item label-item_gray">営業時間</span><span><?php echo date('H:i', strtotime($officeData['Office']['office_hours_from'])); ?>～<?php echo date('H:i', strtotime($officeData['Office']['office_hours_to'])); ?></span></p>
                <p><span class="label-item label-item_gray">アクセス</span><span><?php echo $officeData['Office']['access']; ?></span></p>
              </div>
              <?php } ?>
            </div>
          </td>
        </tr>
      </table>

      <table class="contents_detail_tbl rent-margin-bottom">
        <tr>
          <th>
            <span class="va-middle">返却営業所</span>
            <span class="label-require va-middle">必須</span>
          </th>
          <td id="returnOfficeBox">
           <div class="search_select" style="margin-bottom: 10px;">
              <span class="search_select_inner">
                <?php
                echo $this->Form->input('return_office', array(
                  'type' => 'select',
                  'options' => $returnOfficeList,
                  'default' => $this->request->data['return_office']
                ));
                ?>
              </span>
            </div>
            <div>
            <?php foreach ($returnOfficeDatas as $key => $returnOfficeData) { ?>
            <div id="returnOfficeId<?php echo $returnOfficeData['Office']['id']; ?>" class="return-office office-data">
              <p><span class="label-item label-item_gray">営業時間</span><span><?php echo date('H:i', strtotime($returnOfficeData['Office']['office_hours_from'])); ?>～<?php echo date('H:i', strtotime($returnOfficeData['Office']['office_hours_to'])); ?></span></p>
              <p><span class="label-item label-item_gray">アクセス</span><span><?php echo $returnOfficeData['Office']['access']; ?></span></p>
            </div>
            <?php } ?>
            </div>
          </td>
        </tr>
      </table>

      <h4 class="hd-left-bordered">ご利用人数</h4>
      <table class="contents_detail_tbl rent-margin-bottom">
        <tr>
          <th>
            <span class="va-middle">利用人数</span>
            <span class="label-require va-middle">必須</span>
          </th>
          <td>
            <span>大人（12歳以上）</span>
            <div class="search_select">
              <span class="search_select_inner">
              <?php
              echo $this->Form->input('adults', array(
                  'type' => 'select',
                  'options' => $adultPassengers,
                  'default' => $requestData['adults'],
              )); ?>
              </span>
            </div>
            <span class="rent-margin-right">名</span>
            <span>子供（6〜11歳）</span>
            <div class="search_select">
              <span class="search_select_inner">
             <?php
              echo $this->Form->input('children', array(
                  'type' => 'select',
                  'options' => $passengers,
                  'default' => $requestData['children'],
              )); ?>
              </span>
            </div>
            <span class="rent-margin-right">名</span>
            <span>幼児（6歳未満）</span>
            <div class="search_select">
              <span class="search_select_inner">
             <?php
              echo $this->Form->input('infants', array(
                  'type' => 'select',
                  'options' => $passengers,
                  'default' => $requestData['infants'],
              )); ?>
              </span>
            </div>
            <span>名</span>
          </td>
        </tr>
      </table>

      <h4 class="hd-left-bordered">オプション</h4>
      <table class="contents_detail_tbl rent-margin-bottom">
        <tr>
          <th>
            <span class="va-middle">チャイルドシート</span>
          </th>
          <td id="sheet-option">
            <p id="sheet-note" class="label-item label-item_green text_left" style="display: none;">車両に必要なチャイルドシートをお選びください</p>
            <div class="rent-margin-bottom">
            <?php $privilege_option_flg_zero_cnt = 0; // 下のリクエスト表示・非表示のためのカウント用 ?>
            <?php foreach ($commodityPrivilegeData as $key => $commodityPrivilege) { ?>
              <?php if ($commodityPrivilege['Privilege']['option_flg'] == 1) { ?>
              <div style="margin-bottom:10px;">
                <span>・<?php echo $commodityPrivilege['Privilege']['name']; ?></span>
                <div class="search_select">
                <span class="search_select_inner">
                <?php echo $this->Form->input('sheet.'.$commodityPrivilege['Privilege']['id'], array(
                    'type' => 'select',
                    'options' => $sheetOptions[$commodityPrivilege['Privilege']['id']],
                    'empty' => '---',
                    'data-id' => $commodityPrivilege['Privilege']['id'],
                    'data-price' => $commodityPrivilege[0]['Sum'],
                    'max' => $commodityPrivilege['Privilege']['maximum'],
                )); ?>
                </span>
                </div>
                <?php echo $commodityPrivilege['Privilege']['unit_name']; ?>
              </div>
              <?php } elseif ($commodityPrivilege['Privilege']['option_flg'] == 0) { $privilege_option_flg_zero_cnt++; } ?>
            <?php } ?>
            </div>
          </td>
        </tr>
	<?php if(!empty($privilege_option_flg_zero_cnt)) { ?>
        <tr>
          <th>リクエスト</th>
          <td id="privilege-option">
            <?php foreach ($commodityPrivilegeData as $key => $commodityPrivilege) { ?>
              <?php if ($commodityPrivilege['Privilege']['option_flg'] == 0) { ?>
                <div class="rent-margin-bottom">
                  <span>・<?php echo $commodityPrivilege['Privilege']['name']; ?></span>
                  <div class="search_select">
                  <span class="search_select_inner">
                  <?php echo $this->Form->input('privilege.'.$commodityPrivilege['Privilege']['id'], array(
                      'type' => 'select',
                      'options' => $privilegeOptions[$commodityPrivilege['Privilege']['id']],
                      'empty' => '---',
                      'data-id' => $commodityPrivilege['Privilege']['id'],
                      'data-price' => $commodityPrivilege[0]['Sum'],
                      'max' => $commodityPrivilege['Privilege']['maximum'],
                  )); ?>
                   </span>
                 </div><?php echo $commodityPrivilege['Privilege']['unit_name']; ?>
               </div>
             <?php } ?>
            <?php } ?>
          </td>
        </tr>
	<?php } ?>
      </table>
      <h4 class="hd-left-bordered">料金の確認</h4>
      <table class="contents_detail_tbl rent-margin-bottom">
        <tr>
          <th>オプション料金</th>
          <td class="clearfix">
            <div id="other-price">
              <div id="other-none"></div>
              <div id="drop">
                <span>乗り捨て料金</span>
                <span class="text_bold rent-margin-right price" style="float:right;">0円</span>
              </div>
              <div id="nightfee">
                <span>深夜手数料</span>
                <span class="text_bold rent-margin-right price" style="float:right;">0円</span>
              </div>
              <?php foreach ($commodityPrivilegeData as $key => $commodityPrivilege) { ?>
              <div id="privilege<?php echo $commodityPrivilege['Privilege']['id']; ?>" class="clearfix">
                <span>・<?php echo $commodityPrivilege['Privilege']['name']; ?></span>
                <span class="count"><span class="num"></span></span><?php echo $commodityPrivilege['Privilege']['unit_name']; ?>
                <span class="text_bold rent-margin-right price" style="float:right;">0円</span>
              </div>
              <?php } ?>
            </div>
          </td>
        </tr>
        <tr>
          <th>お支払合計金額</th>
          <td class="contents_result_detail_amount">
            <div class="text_right rent-padding">
              <span class="bubble bubble-right">税込価格</span>
              <span id="total-place" class="contents_result_detail_amount_price"></span>
            </div>
            <hr class="rent-margin">
            <div class="rent-padding">
            <?php if ($commodityInfo['Client']['accept_cash'] == 1 && $commodityInfo['Client']['accept_card'] == 1) { ?>
              <?php if (!empty($commodityInfo['Cards'])) { ?>
                <?php foreach ($commodityInfo['Cards']['url'] as $key => $card) { ?>
                  <?php echo $this->Html->image($card, array('alt' => $commodityInfo['Cards']['name'][$key])); ?>
                <?php } ?>
              <?php } ?>
              <p>・原則クレジットカードまたは現金</p>
            <?php } elseif ($commodityInfo['Client']['accept_cash'] == 1) { ?>
              <p>・現金のみ</p>
            <?php } elseif ($commodityInfo['Client']['accept_card'] == 1) { ?>
              <?php if (!empty($commodityInfo['Cards'])) { ?>
                <?php foreach ($commodityInfo['Cards']['url'] as $key => $card) { ?>
                  <?php echo $this->Html->image($card, array('alt' => $commodityInfo['Cards']['name'][$key])); ?>
                <?php } ?>
              <?php } ?>
              <p>・クレジットカードのみ</p>
            <?php } ?>
            </div>
          </td>
        </tr>
      </table>

    <?php echo $this->Form->hidden('uniqId', array('value' => $sessionUniqId)); ?>
    <?php echo $this->Form->hidden('basicPrice', array('value' => $basicCharge)); ?>
    <?php echo $this->Form->hidden('from', array('value' => $requestData['from'])); ?>
    <?php echo $this->Form->hidden('to', array('value' => $requestData['to'])); ?>
    <?php echo $this->Form->hidden('carClassId', array('value' => $commodityInfo['CarClass']['id'])); ?>
    <?php echo $this->Form->hidden('commodityItemId', array('value' => $commodityInfo['CommodityItem']['id'])); ?>
    <?php echo $this->Form->hidden('commodityId', array('value' => $commodityInfo['Commodity']['id'])); ?>
    <?php echo $this->Form->hidden('clientId', array('value' => $commodityInfo['Client']['id'])); ?>
    <?php echo $this->Form->hidden('estimationTotalPrice', array('value' => '0')); ?>
    <?php echo $this->Form->hidden('dayTimeFlg', array('value' => $commodityInfo['Commodity']['day_time_flg'])); ?>
    <?php echo $this->Form->hidden('capacity', array('value' => $commodityInfo['CarType']['capacity'])); ?>

    <?php echo $this->Form->hidden('submitFlg', array('value' => 1)); ?>

    <?php echo $this->Form->submit('次へ（お客様情報入力）', array('class' => 'btn btn_submit rent-margin-bottom-important')); ?>
    <div style="text-align:center;">
    <?php echo $this->Html->link('レンタカーの検索へ戻る', $backSearch, array('class' => 'btn btn_cancel', 'style' => 'padding:4px 34px;display:inline;')); ?>
    </div>
<?php echo $this->Form->end(); ?>
  </div>