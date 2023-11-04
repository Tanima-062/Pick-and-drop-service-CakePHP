<div class="row-fluid span8">
	<h3>営業所精算管理編集</h3>
	<?php echo $this->Form->create('Office', array('class' => 'form-horizontal','inputDefaults'=>array('label'=>false,'div'=>false)));?>
    <?php $referer = ($this->request->data['Custom']['referer'] ? $this->request->data['Custom']['referer'] : $this->request->referer()); ?>
	<?php echo $this->Form->hidden('Custom.referer', array('value' => $referer)); ?>
	<table class="table table-bordered form-inline">
		<tr>
			<th class="alert-success">クライアント名</th>
			<td><?php
					echo $this->Form->hidden('id');
					echo $this->data['Client']['name'];
				?>
			</td>
		</tr>
		<tr>
			<th class="alert-success">精算管理会社名</th>
			<td>
			<?php echo $this->Form->input('settlement_company_id',[
					'options' => $settlementCompanyies,
					'required' => true,
					'div' => false,
					'value' => $this->data['Office']['settlement_company_id']
				]);
				?>
			</td>
		</tr>
	</table>

	<span class="left">
		<?php echo $this->Form->submit('編集する',array('class'=>'btn btn-success right','div'=>false));?>
	</span>
	<?php echo $this->Form->end();?>

</div>