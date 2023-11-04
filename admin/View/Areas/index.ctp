<script>
$(function() {
  $("#sortable-div").sortable({
    items: "tr",
    opacity: 1.5,
    revert: false,
    forcePlaceholderSize: false,
    placeholder: "alert-info",
    stop : function(){
      var data=[];
      $(".ui-state").each(function(i,v){
        data.push(v.id);
      });
      $('#sort').val(data.toString());
    },
    update : function(){
      $('#submit').removeAttr('disabled');
    },
    cancel:'.stop'
  });

//$('#sortable-div td').sortable({cancel : '.stop'});

});
</script>
<div class="areas index">
  <h2>エリア一覧</h2>
  <div class="right">
    <?php
      echo $this->Form->create('Sort');
      echo $this->Form->hidden('sort',array('id'=>'sort'));
      echo $this->Form->submit('並び順を保存する',array('id'=>'submit','class'=>'btn btn-primary','disabled'=>'disabled'));
      echo $this->Form->end();
    ?>
  </div>

  <table class="table table-bordered">
    <thead>
      <tr class="btn-primary">
        <th>ｴﾘｱID</th>
        <th>ｴﾘｱ名</th>
        <th>都道府県</th>
        <th>リンク用URL</th>
        <th class="actions">
        <?php echo $this -> Html -> link('新規追加', 'add', array('class' => 'btn btn-success')); ?>
        </th>
      </tr>
    </thead>
    <tbody id="sortable-div">
    <?php foreach ($areas as $area): ?>
    <tr id="<?php echo $area['Area']['id'];?>" class="ui-state">
      <td><?php echo h($area['Area']['id']); ?></td>
      <td><?php echo h($area['Area']['name']); ?></td>
      <td><?php echo $prefectureList[$area['Area']['prefecture_id']]; ?></td>
      <td><?php echo h($area['Area']['area_link_cd']); ?></td>
      <td class="actions">
      <?php echo $this -> Html -> link('編集', array('action' => 'edit', $area['Area']['id']), array('class' => 'btn btn-warning')); ?>
      <?php echo $this -> Form -> postLink('削除', array('action' => 'delete', $area['Area']['id']), array('class' => 'btn btn-danger'), __('「%s」を削除しますか?', $area['Area']['name'])); ?>
      </td>
    </tr>
    <?php endforeach; ?>
    </tbody>
  </table>

</div>
