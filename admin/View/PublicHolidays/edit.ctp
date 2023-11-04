<div class="">
  <div class="span6">
    <?php echo $this->Form->create('PublicHoliday', array('class' => 'form-horizontal'));?>
        <h3>祝日追加</h3>
      <table class="table table-bordered">
        <tr>
          <th>祝日名</th>
          <td>
        <?php echo $this->Form->input('name',array('label'=>false,'div'=>false)); ?>
          </td>
        </tr>
        <tr>
            <th>日にち</th>
            <td>
            <?php echo $this->Form->input('date',array('label'=>false,'div'=>false, 'dateFormat' => 'YMD', 'monthNames' => false,'class'=>'span3')); ?>
            </td>
        </tr>
        <tr>
            <th>削除フラグ</th>
            <td>
              <?php
              echo $this->Form->input('delete_flg',array('type'=>'select', 'options'=>array(0=> '公開する',1=> '非公開にする'),'label'=>false,'div'=>false));
              ?>
            </td>
         </tr>
      </table>
    <?php
    echo $this->Form->hidden('id');
    echo $this->Form->submit('修正',array('class'=>'btn btn-warning','div'=>false));
    echo $this->Form->end();
    ?>
  </div>

</div>