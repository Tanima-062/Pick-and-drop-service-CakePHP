<div class="row-fluid">
	<h3>深夜手数料登録</h3>
	<div class="span9">
		<?php echo $this->Form->create('LateNightFee', array('class' => 'form-horizontal','inputDefaults'=>array('label'=>false,'div'=>false)));?>
		<table class="table table-bordered">
			<tr>
				<th class="alert-success">対象時間</th>
				<td>
				<?php echo $this->Form->hour('target_time_from',true,array('class'=>'span2','empty'=>false)); ?>
				:
				<?php echo $this->Form->minute('target_time_from',array('class'=>'span2','empty'=>false)); ?>
				～
				<?php echo $this->Form->hour('target_time_to',true,array('class'=>'span2','empty'=>false)); ?>
				:
				<?php echo $this->Form->minute('target_time_to',array('class'=>'span2','empty'=>false)); ?>
				</td>
			</tr>

			<tr>
				<th class="alert-success">料金</th>
				<td>
				<?php echo $this->Form->input('price',array('class'=>'span3','min'=>1)); ?>円
				</td>
			</tr>

			<tr>
				<th class="alert-success">加算回数</th>
				<td>
				<?php
				echo $this->Form->input('price_addition_flg',array('type'=>'select','options'=>$priceAdditionFlgOptions,'style'=>'width:260px;'));
				?>
				</td>
			</tr>
			<tr>
<?php
	$scopeOption = array('empty'=>false,'style'=>'width:100%;','required');
	if (!$isClientAdmin) {
		unset($scopeList[0]);
	}
?>
				<td class="alert-success">公開範囲</td>
				<td><?php echo $this->Form->select('scope', $scopeList, $scopeOption); ?></td>
			</tr>
		</table>
		<div class="left">
			<?php echo $this->Html->link('一覧へ戻る',array('action'=>'index'),array('class'=>'btn btn-warning'));?>
		</div>

		<div class="right">
			<?php echo $this->Form->submit('登録',array('class'=>'btn btn-success'));?>
		</div>
		<?php echo $this->Form->end();?>
	</div>

</div>