<h3>商品グループ編集</h3>

<p>
	<?php echo $this->Html->link('商品一覧', '/Commodities/', array ('escape' => false)); ?>
	　＞　商品グループ編集
</p>

<?php echo $this->Form->create('CommodityGroup', array ('inputDefaults' => array('label' => false, 'div' => false, 'legend' => false),)); ?>
<table class="table-striped table-condensed">
	<tr>
		<td>グループ名</td>
		<td><?php
		echo $this->Form->hidden('id');
		echo $this->Form->input('name',array('style'=>'width:700px;','required')); ?></td>
	</tr>
	<tr>
		<td>提供開始日時</td>
		<td>
		<?php
		echo $this->Form->input('available_from', array('class' => 'jquery-ui-datepicker-from','div' => false, 'label' => false,
				'type'=>'text','required','value'=>date('Y-m-d',strtotime($this->data['CommodityGroup']['available_from'])))); ?>
		<?php echo $this->Form->hour('from_hour',1,$timeOption); ?>時
		<?php echo $this->Form->minute('from_min',$timeOption); ?>分</td>
	</tr>
	<tr>
		<td>提供終了日時</td>
		<td>
		<?php echo $this->Form->input('available_to', array('class' => 'jquery-ui-datepicker-to', 'div' => false, 'label' => false,
				'type'=>'text','required','value'=>date('Y-m-d',strtotime($this->data['CommodityGroup']['available_to'])))); ?>
		<?php echo $this->Form->hour('to_hour',1,$timeOption); ?>時
		<?php echo $this->Form->minute('to_min',$timeOption); ?>分</td>
	</tr>

	<?php /*?>
	<tr>
		<td>公開フラグ</td>
		<td><?php echo $this->Form->input('is_published'); ?></td>
	</tr>
	<?php */?>
</table>
<br/>
<div>
	<?php echo $this->Html->link('<span class="btn btn-warning">戻る</span>', '/Commodities/', array ('escape' => false)); ?>
	<?php echo $this->Form->submit('登録', array ('class' => 'btn btn-success', 'div' => false)); ?>
</div>
<?php echo $this->Form->end(); ?>


<script>
<!--
jQuery( function() {

    var dates = jQuery( '.jquery-ui-datepicker-from, .jquery-ui-datepicker-to' ) . datepicker( {
    	dateFormat: 'yy-mm-dd',
        showAnim: 'clip',
        monthNames: ['1月','2月','3月','4月','5月','6月',
                     '7月','8月','9月','10月','11月','12月'],
        changeMonth: false,
        numberOfMonths: 3,
        showCurrentAtPos: 1,
        onSelect: function( selectedDate ) {

            var option = this . className  == 'jquery-ui-datepicker-from hasDatepicker' ? 'minDate' : 'maxDate',
                instance = jQuery( this ) . data( 'datepicker' ),
                date = jQuery . datepicker . parseDate(
                    instance . settings . dateFormat ||
                    jQuery . datepicker . _defaults . dateFormat,
                    selectedDate, instance . settings );
            dates . not( this ) . datepicker( 'option', option, date );
        }
    } );
} );
// -->
</script>
