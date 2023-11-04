<!-- 店舗wrap -->
    <div class="contents_result_list st-table rent-margin-bottom">
      <div class="contents_result_list_head st-table">
        <?php echo $this->Html->image('../../img/logo/square/'.$commodityInfo['Client']['id'].'/'.$commodityInfo['Client']['sp_logo_image'], array('alt' => $commodityInfo['Client']['name'], 'width' => '190', 'class' => 'st-table_cell contents_result_list_head_elm va-middle is-shopLogo')); ?>
        <h4 class="contents_result_list_head_elm va-middle is-shopName" style="display:table-cell;width:770px;"><?php echo $commodityInfo['Client']['name']; ?></h4>
      </div>

      <!-- 店舗 -->
      <div class="contents_result_list_body st-table">
        <div class="contents_result_list_body_left">
          <div class="contents_result_list_body_detail st-table rent-margin-bottom-s">
            <?php echo $this->Html->image('../../img/commodity_main/'.$commodityInfo['Client']['id'].'/'.$commodityInfo['Commodity']['image_relative_url'], array('class' => 'contents_result_list_body_detail_img st-table_cell va-middle')); ?>
            <div class="contents_result_list_body_detail_condition st-table_cell va-middle">
              <p>
                <span><?php echo $commodityInfo['CarType']['name']; ?>
                  <?php
                  if(!empty($newCarRegistration[$commodityInfo['Commodity']['new_car_registration']])) {
                  ?>
                  （<?php echo $newCarRegistration[$commodityInfo['Commodity']['new_car_registration']]; ?>）
                  <?php
                  }
                  ?>
                </span>
                <span>車種：
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
              <p class="text_underline"><?php echo $commodityInfo['Commodity']['name']; ?></p>
            </div>
          </div>
          <div class="contents_result_list_body_detail st-table">
            <div class="contents_result_list_body_detail_condition st-table_cell">
            <?php
            // 推奨人数
            $recommendedCapacity = Hash::extract($commodityInfo['CarModel'], '{n}.recommended_capacity');
            // 推奨荷物数
            $packageNum = Hash::extract($commodityInfo['CarModel'], '{n}.package_num');
            // 受け付け締め切り時間を設定する場合
            if (isset($commodityInfo['CommodityTerm']['deadline_days']) && isset($commodityInfo['CommodityTerm']['deadline_time'])) {
                $dayStr = (empty($commodityInfo['CommodityTerm']['deadline_days'])) ? '当日の' : 'から' . $commodityInfo['CommodityTerm']['deadline_days'] . '日前の';
                $deadline = '受取日' . $dayStr . date('H:i', strtotime($commodityInfo['CommodityTerm']['deadline_time'])) . 'まで';
            } else if (isset($commodityInfo['CommodityTerm']['deadline_hours'])) {
                $deadline = '受取時間の' . $commodityInfo['CommodityTerm']['deadline_hours'] . '時間前まで';
            } else {
              $deadline = '未設定';
            }
            ?>
              <p>推奨目安<span class="indication_man"><?php echo $recommendedCapacity[0]; ?></span><span class="indication_buggage"><?php echo $packageNum[0]; ?></span>申込締切：<span><?php echo $deadline; ?></span></p>
            </div>
          </div>
          <div class="car_spec st-table" style="margin-left: 10px;">
            <?php
            foreach($equipmentList as $equipmentId => $equipment) {
                if (!empty($commodityEquipment[$equipmentId])) {
                    $active = 'is-active';
                } else {
                    $active = '';
                }
            ?>
            <div class="car_spec_item st-table_cell <?php echo $active; ?>">
              <p><?php echo $equipment; ?></p>
            </div>
            <?php } ?>
          </div>
        </div>

        <div class="contents_result_list_body_right st-table_cell">
          <p class="rent-margin-bottom-s">基本料金（免責補償・税込）</p>
          <p class="contents_result_list_body_right_price text_danger text_large text_bold rent-margin-bottom-s">&yen;<span>---</span></p>
        </div>
      </div>
      <!-- /店舗inner -->
    </div>
<!-- /店舗wrap -->