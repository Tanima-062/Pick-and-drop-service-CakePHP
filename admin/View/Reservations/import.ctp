<div class="reservations import">
    <h3>予約修正CSVインポート</h3>
    <?php echo $this->Form->create('Reservation', array('type' => 'file')); ?>
    <table class="table-bordered table-condensed">
        <tr>
            <th>CSVファイル</th>
            <td><?php echo $this->Form->file('import_csv', array('div' => false, 'label' => false, 'accept' => '.csv')); ?></td>
        </tr>
    </table>
    <div style="padding:10px 0px 0px 0px;">
        <?php echo $this->Form->submit('インポート', array('id' => 'import_btn', 'class' => 'btn btn-primary')); ?>
    </div>
    <?php echo $this->Form->end(); ?>

    <?php if (!empty($errList)) { ?>
        <table class="table table-bordered">
            <tr>
                <th>行番号</th>
                <th>予約番号</th>
                <th>ステータス</th>
                <th>料金</th>
                <th>エラー</th>
            </tr>
            <?php
            foreach ($errList as $err) :
            ?>
                <tr>
                    <td><?php echo h($err['no']); ?></td>
                    <td><?php echo h($err[0]); ?></td>
                    <td><?php echo h($err[1]); ?></td>
                    <td><?php echo h($err[2]); ?></td>
                    <td><?php echo $err['errors']; ?></td>
                </tr>
            <?php endforeach; ?>
        </table>
    <?php } ?>
</div>
<script>
    $(function() {
        $('#import_btn').on('click', function(e) {
            if ($('#TourPriceImportCsv').val() == '') {
                alert('CSVファイルを選択してください');
                e.preventDefault();
                return;
            }
            if (!window.confirm('CSVファイルのインポートを実行してもよろしいですか？')) {
                e.preventDefault();
            }
        });
    });
</script>
