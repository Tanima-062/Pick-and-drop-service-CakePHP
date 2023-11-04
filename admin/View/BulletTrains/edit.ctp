<div class="landmarks">
  <?php echo $this -> Form -> create('BulletTrain', array('inputDefaults' => array('label' => false))); ?>
  <h3>新幹線駅編集</h3>
  <?php echo $this -> Form -> hidden('id'); ?>
  <table class="table table-bordered">
    <tr>
      <th>新幹線エリア</th>
      <td><?php echo $this -> Form -> input('bullet_train_area_id', array('options' => $bulletTrainAreaList)); ?></td>
    </tr>
    <tr>
      <th>新幹線駅名</th>
      <td><?php echo $this -> Form -> input('name', array('required' => true)); ?></td>
    </tr>
    <tr>
      <th>都道府県</th>
      <td><?php echo $this -> Form -> input('prefecture_id', array('options' => $prefectureList)); ?></td>
    </tr>
    <tr>
      <th>公開・非公開</th>
      <td><?php echo $this -> Form -> input('delete_flg', array('type' => 'input', 'default' => 0, 'options' => $deleteFlgOptions)); ?></td>
    </tr>
    <tr>
      <th>ソート</th>
      <td>
    <?php echo $this -> Form -> input('sort', array('required' => false)); ?>
      </td>
    </tr>
  </table>
  <?php echo $this -> Form -> submit('編集する', array('class' => 'btn btn-success ')); ?>
  <?php echo $this -> Form -> end(); ?>
</div>