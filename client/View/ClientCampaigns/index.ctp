<?php

/* 
* To change this license header, choose License Headers in Project Properties.
* To change this template file, choose Tools | Templates
* and open the template in the editor.
*/

?>
<div>
    <h3>クライアントキャンペーン一覧</h3>

    <table class="table table-bordered">
     <tr>
      <th><?php echo $this->Paginator->sort('id','キャンペーンID'); ?></th>
<?php /* <th><?php echo $this->Paginator->sort('client_id','クライアントID'); ?></th> */ ?>
      <th><?php echo $this->Paginator->sort('title','タイトル'); ?></th>
      <th><?php echo $this->Paginator->sort('overview','概要'); ?></th>
      <th><?php echo $this->Paginator->sort('list_explanation','一覧用説明文'); ?></th>
      <th><?php echo $this->Paginator->sort('period_start','キャンペーン対象期間'); ?></th>
      <th><?php echo $this->Paginator->sort('booking_start','ご予約可能期間'); ?></th>
      <th><?php echo $this->Paginator->sort('delete_flg','公開状況'); ?></th>
      <th class="actions">
       <?php echo $this->Html->link('新規追加', array('action' => 'add'),array('class'=>'btn btn-success')); ?>
      </th>
     </tr>
     
     <?php foreach ($clientCampaign as $Campaign) { 
     	$class = '';
        //非公開、もしくはキャンペーン終了日が本日以前のものを灰色とする
	if($Campaign['ClientCampaign']['delete_flg'] == '1') {
            $class = 'deleted';
        } else if(!empty($Campaign['ClientCampaign']['period_end']) && strtotime(date("Y/m/d")) > strtotime($Campaign['ClientCampaign']['period_end'])){
            $class = 'deleted';
        }
     ?>            
     <tr class="<?php echo $class;?>">
         <td><?php echo $Campaign['ClientCampaign']['id'] ; ?></td>
<?php /* <td><?php echo $Campaign['ClientCampaign']['client_id'] ; ?></td> */ ?>
         <td><?php echo $Campaign['ClientCampaign']['title'] ; ?></td>
         <td><?php echo $Campaign['ClientCampaign']['overview'] ; ?></td>
         <td><?php echo $Campaign['ClientCampaign']['list_explanation'] ; ?></td>
         <td>
             <?php
                if(!empty($Campaign['ClientCampaign']['period_start']) || !empty($Campaign['ClientCampaign']['period_end'])){ 
                    $dateString = $Campaign['ClientCampaign']['period_start'] . ' ～ <br> ' . 
                    $Campaign['ClientCampaign']['period_end'] ;     
                    echo $dateString;
                }
             ?></td>
         <td>
             <?php
                if(!empty($Campaign['ClientCampaign']['booking_start']) || !empty($Campaign['ClientCampaign']['booking_end'])){ 
                    $dateString = $Campaign['ClientCampaign']['booking_start'] . ' ～ <br> ' . 
                    $Campaign['ClientCampaign']['booking_end'] ;     
                    echo $dateString;
                }
             ?></td>
         <td>
             <?php
                if($Campaign['ClientCampaign']['delete_flg'] == '0'){
                    echo '公開';
                } else if($Campaign['ClientCampaign']['delete_flg'] == '1'){
                    echo '非公開';
                }
              ?>
         </td>
         <td class="actions">
          <?php echo $this->Html->link('編集', array('action' => 'edit', $Campaign['ClientCampaign']['id']),array('class'=>'btn btn-warning')); ?>
	 </td>
      </tr>
    <?php } ?> 
    </table>
    <p>
     <?php
        echo $this->Paginator->counter(
            array('format' => __('ページ {:page} / {:pages}　：　総レコード/ {:count}件')));    
     ?>
    </p>
    <div class="pagination pagination-small pagination-right">
            <ul>
                <?php
                    echo '<li>'.$this->Paginator->prev('< ' . __('戻る'), array(), null, array('class' => 'prev disabled')). '</li>';
                    echo '<li>'.$this->Paginator->numbers(array('separator' => '')). '</li>';
                    echo '<li>'.$this->Paginator->next(__('次へ') . ' >', array(), null, array('class' => 'next disabled')). '</li>';
                ?>
            </ul>
    </div>

</div>
