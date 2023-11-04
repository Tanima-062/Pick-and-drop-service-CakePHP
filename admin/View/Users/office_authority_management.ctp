<style>
label {
	display: inline-block;
}
.blocks {
	border:solid #e3e3e3 1px;
	margin: 15px 0;
	padding: 0 19px;
}
</style>

<script type="text/javascript">
$(function(){
	// 都道府県表示
	var prefectureId = $("#prefecture_id").val();
	ajaxPrefecture(prefectureId);
	$("#prefecture_id").on('change', function(){
		prefectureId = $(this).val();
		ajaxPrefecture(prefectureId);
	});

	// 全営業所checkbox選択
	$('#prefectureAllCheck').on('click', function(){
		$("#officeList input:checkbox").prop({'checked':true});
	});
	$('#prefectureAllCheckClear').on('click', function(){
		$("#officeList input:checkbox").prop({'checked':false});
	});

	// 都道府県別営業所checkbox選択
	$('.officeAllCheck').on('click', function(){
		var checkClass = $('.'+$(this).data('prefecture')+" input:checkbox");
		if (checkClass.prop('checked') == false) {
			checkClass.prop({'checked':true});
		} else {
			checkClass.prop({'checked':false});
		}
	});

	// submit
	$('#btn_submit').on('click', function(){
		var officeId = [];
		$("#officeList input:checkbox").each( function() {
			if ($(this).prop('checked')) {
				officeId.push($(this).val());
			}
		});
		// 営業所数がPOST上限を超えるレンタカー会社が登場したため、営業所checkboxは送信したくない
		$('#officeList').find('input').remove();
		// JSON化して送信する
		$('#officeList').append('<input type="hidden" name="data[Staff][json_office_id]" value=\'' + JSON.stringify(officeId) + '\'>');
		$("#StaffOfficeAuthorityManagementForm").submit();
	});
});
function ajaxPrefecture(prefectureId) {
	var className = 'prefecture'+prefectureId;
	$('#officeList .blocks').show();
	if (prefectureId) {
		$('#officeList .blocks').not('.'+className).hide();
		$('#officeCheckBtn').hide();
	} else {
		$('#officeCheckBtn').show();
	}

}
</script>

<h2>営業所設定</h2>
<p>クライアント名：<?php echo $staffData['Client']['name']; ?>&emsp;&emsp;担当者名：<?php echo $staffData['Staff']['name']; ?></p>

<?php echo $this->Form->create(false, array('inputDefaults' => array('label' => false, 'div' => false), 'type' => 'post')); ?>
<?php echo $this->Form->input('prefecture_id', array('type' => 'select', 'options' => $prefectureList, 'empty' => '---')); ?>
<?php echo $this->Form->end(); ?>

<?php echo $this->Form->create('Staff',array('inputDefaults'=>array('label'=>false,'div'=>false))); ?>
	<div id="officeList">
		<div id="officeCheckBtn" style="text-align:right;">
			<span id="prefectureAllCheck" class="btn btn-mini btn-inverse">全営業所全てチェック</span>
			<span id="prefectureAllCheckClear" class="btn btn-mini">全営業所全て外す</span>
		</div>
		<?php
		foreach ($offices as $prefectureId => $office) {
			$displayPrefecter = 'prefecture'.$prefectureId;
		?>
		<div class="blocks <?php echo $displayPrefecter; ?>">
			<h3><?php echo $prefectureList[$prefectureId]; ?>
			<span class="officeAllCheck btn btn-mini btn-inverse" data-prefecture="<?php echo $displayPrefecter; ?>">
				<?php echo $prefectureList[$prefectureId]; ?>営業所チェック
			</span>
			</h3>
			<?php
			foreach ($office as $value) {
				echo $this->Form->input('Staff.office_id.'. $value['id'], array(
					'type'=>'checkbox',
					'label'=>$value['name'],
					'value'=>$value['id'],
				));
			}
			?>
		</div>
		<?php } ?>
	</div>
<?php
echo $this->Form->button('更新', array('type' => 'button', 'id' => 'btn_submit', 'class' => 'btn btn-success', 'div' => false));
echo $this->Form->end();
?>
<div class="clearfix">
<?php echo $this->Html->link('一覧へ戻る','/Users/',array('class'=>'btn btn-primary pull-right')); ?>
</div>