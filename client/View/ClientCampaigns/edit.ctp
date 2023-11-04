<script>
$(function() {
	$.datepicker.setDefaults($.datepicker.regional["ja"]);
	var startOption = {
			numberOfMonths: 1,
			changeMonth: true,
			changeYear: true,
			monthNames: ['1月','2月','3月','4月','5月','6月','7月','8月','9月','10月','11月','12月'],
			monthNamesShort: ['1月','2月','3月','4月','5月','6月','7月','8月','9月','10月','11月','12月'],
			dayNamesShort: ['日','月','火','水','木','金','土'],
			dayNamesMin: ['日','月','火','水','木','金','土'],
			showMonthAfterYear: true,
			dateFormat: 'yy-mm-dd',
	};
	$("#periodStartDatePicker").datepicker(startOption);
	$("#periodEndDatePicker").datepicker(startOption);
        $("#bookingStartDatePicker").datepicker(startOption);
	$("#bookingEndDatePicker").datepicker(startOption);
});
</script>

<link rel="stylesheet" type="text/css" href="/rentacar/client/css/client_campaign.css">
<div class="client_campaigns form">
	<?php
            echo $this->Form->create('ClientCampaign',array('type' => 'file')); 
        ?>
            
	<h3>クライアントキャンペーン修正</h3>
        <table class="table table-bordered table-striped table-hover" style="font-size:small">
	 <tr>
	  <th class="span3">タイトル</th>
	  <td><?php echo $this->Form->input('title', array('div'=>false, "label" => false, "style" => "width:300px;")); ?>
                <?php echo $this->Form->hidden('id'); ?>
          </td>
         </tr>
         <tr>
          <th class="span3" >概要</th>
	  <td>
           <?php
                $error = $this->Form->Error('ClientCampaign.overview');
                if( $this->Form->Error('ClientCampaign.overview') ){
                echo $this->Form->textarea("overview", array('div'=>false, "cols"=>20, "rows"=>5, 'style' => array('width:100%;box-sizing:border-box;min-height:200px;border-color:red;'))); 
            ?>
            <span style='color:red;'>
            <?php    
                    echo $this->Form->Error('ClientCampaign.overview');
            ?></span> <?php
                } else { 
                    echo $this->Form->textarea("overview",  array( 'div'=>false, "cols"=>20, "rows"=>5, 'style' => array('width:100%;box-sizing:border-box;min-height:200px;')));
                }
            ?>        
          </td>
         </tr>
         <tr>
          <th class="span3">一覧用説明文</th>
	  <td>
            <?php
               if( $this->Form->Error('ClientCampaign.list_explanation') ){
                echo $this->Form->textarea("list_explanation",  array('div'=>false, "cols"=>20, "rows"=>5, 'style' => array('width:100%;box-sizing:border-box;min-height:200px;border-color:red;'))); 
            ?>
            <span style='color:red;'>
            <?php    
                echo $this->Form->Error('ClientCampaign.list_explanation');
            ?></span> <?php
                } else { 
                    echo $this->Form->textarea("list_explanation",  array('div'=>false, "cols"=>20, "rows"=>5, 'style' => array('width:100%;box-sizing:border-box;min-height:200px;')));
                }
            ?>      
          </td>
         </tr>
         <tr>
         <th class="span3">キャンペーン対象期間開始日</th>
	  <td >
            <?php
                if( $this->Form->Error('ClientCampaign.period_start') ){
                    echo $this->Form->input('period_start', array('div'=>false, 'type' => 'text', 'label' => false,'readonly' => true, 'id' => 'periodStartDatePicker', 'div' => false, 'style' => 'border-color:red;')); 
                }else{
                    echo $this->Form->input('period_start', array('div'=>false, 'type' => 'text', 'label' => false,'readonly' => true, 'id' => 'periodStartDatePicker', 'div' => false)); 
                }
            ?>
          </td> 
        </tr> 
　        <th class="span3">キャンペーン対象期間終了日</th>
	  <td>
            <?php
                if( $this->Form->Error('ClientCampaign.period_end') ){
                    echo $this->Form->input('period_end', array('div'=>false, 'type' => 'text', 'label' => false,'readonly' => true, 'id' => 'periodEndDatePicker', 'div' => false, 'style' => 'border-color:red;'));
                }else{
                    echo $this->Form->input('period_end', array('div'=>false, 'type' => 'text', 'label' => false,'readonly' => true, 'id' => 'periodEndDatePicker', 'div' => false));
                }
            ?>  
          </td>
         </tr>
         <tr>
　        <th class="span3">ご予約可能期間開始日</th>
	  <td>
           <?php
                if( $this->Form->Error('ClientCampaign.booking_start') ){
                    echo $this->Form->input('booking_start', array('div'=>false, 'type' => 'text', 'label' => false,'readonly' => true, 'id' => 'bookingStartDatePicker', 'div' => false, 'style' => 'border-color:red;')); 
                }else{
                    echo $this->Form->input('booking_start', array('div'=>false, 'type' => 'text', 'label' => false,'readonly' => true, 'id' => 'bookingStartDatePicker', 'div' => false)); 
                }
            ?>
          </td>
         </tr>
　        <th class="span3">ご予約可能期間終了日</th>
	  <td>
          <?php
            if( $this->Form->Error('ClientCampaign.booking_end') ){
                    echo $this->Form->input('booking_end', array('div'=>false, 'type' => 'text', 'label' => false,'readonly' => true, 'id' => 'bookingEndDatePicker', 'div' => false, 'style' => 'border-color:red;')); 
                }else{
                    echo $this->Form->input('booking_end',  array('div'=>false, 'type' => 'text', 'label' => false,'readonly' => true, 'id' => 'bookingEndDatePicker', 'div' => false)); 
                }
            ?>
          </td>
         </tr>
         <tr>
　        <th class="span3">車両クラス・料金例</th>
	  <td>
           <?php
                if( $this->Form->Error('ClientCampaign.vehicle_fee_example') ){
                    echo $this->Form->textarea("vehicle_fee_example", array('div'=>false, "cols"=>20, "rows"=>5, 'style' => array('width:100%;box-sizing:border-box;min-height:200px;border-color:red;'))); 
            ?>
            <span style='color:red;'>
            <?php    
                    echo $this->Form->Error('ClientCampaign.vehicle_fee_example');
            ?></span> <?php
                } else { 
                    echo $this->Form->textarea("vehicle_fee_example", array('div'=>false, "cols"=>20, "rows"=>5, 'style' => array('width:100%;box-sizing:border-box;min-height:200px;')));
                }
            ?>
          </td>
         </tr>
         <tr>
　        <th class="span3">画像ファイル1</th>
	  <td>
            <?php 
                if(!empty($this->request->data['ClientCampaign']['image1'])) {
                    $strPath = '../../img/ClientCampaigns/' . $this->request->data['ClientCampaign']['id'] . DS . $this->request->data['ClientCampaign']['image1'];
                    echo $this->Html->image($strPath); 
                }
                echo $this->Form->input('image1', array('div'=>false, 'type' => 'file', 'label' => false));
             ?>            
           </td>
         </tr>
         <tr>
　        <th class="span3">画像ファイル2</th>
	  <td>
            <?php 
                if(!empty($this->request->data['ClientCampaign']['image2'])) {
                    $strPath = '../../img/ClientCampaigns/' . $this->request->data['ClientCampaign']['id'] . DS . $this->request->data['ClientCampaign']['image2'];
                    echo $this->Html->image($strPath); 
                }
                echo $this->Form->input('image2', array('div'=>false, 'type' => 'file', 'label' => false));
            ?>
          </td>
         </tr>
         <tr>
　        <th class="span3">画像ファイル3</th>
	  <td>
            <?php 
                if(!empty($this->request->data['ClientCampaign']['image3'])) {
                    $strPath = '../../img/ClientCampaigns/' . $this->request->data['ClientCampaign']['id'] . DS . $this->request->data['ClientCampaign']['image3'];
                    echo $this->Html->image($strPath); 
                }
                echo $this->Form->input('image3', array('div'=>false, 'type' => 'file', 'label' => false));
            ?>
          </td>
         <tr>
　        <th class="span3">サムネイル画像</th>
	  <td>
           <?php 
                if(!empty($this->request->data['ClientCampaign']['thumbnail_image'])) {
                    $strPath = '../../img/ClientCampaigns/' . $this->request->data['ClientCampaign']['id'] . DS . $this->request->data['ClientCampaign']['thumbnail_image'];
                    echo $this->Html->image($strPath); 
                }
                echo $this->Form->input('thumbnail_image', array('div'=>false, 'type' => 'file', 'label' => false));
            ?>
          </td>
         </tr>
         <tr>
　        <th class="span3">優先順位</th>
	  <td><?php echo $this->Form->input('rank', array('div'=>false, 'label' => false, 'min' => '1', 'max' => '10' )); ?></td>
         </tr>
         <tr>
　        <th class="span3">公開/非公開</th>
	  <td><?php
		echo $this->Form->input('delete_flg', array('div'=>false, 'type'=>'select','options'=>$campaignDeleteFlgOptions,'div'=>false, 'label' => false));
            ?>
          </td>
         </tr>
        </table>
        
        <div class="right">
		<?php
		echo $this->Form->submit('登録',array('class'=>'btn btn-success'));
		echo $this->Form->end();
		?>
	</div>
</div>        