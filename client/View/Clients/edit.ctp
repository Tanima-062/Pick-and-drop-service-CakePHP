<h3>基本情報管理</h3>

<?php echo $this->Form->create('Client', array ('inputDefaults' => array('label' => false, 'div' => false, 'legend' => false),'enctype'=>'multipart/form-data')); ?>
<table class="table table-bordered ">
	<tr>
		<td class="alert-success" width="20%">商標</td>
		<td><?php echo $clients['Client']['name']; ?></td>
	</tr>
	<?php for ($i = 0; $i < 3; $i++) { ?>
		<tr>
			<td class="alert-success">予約通知先メールアドレス <?php echo $i + 1; ?></td>
			<td>
				<?php echo $this->Form->input('ClientEmail.' . $i . '.reservation_email', array ('value' => (!empty($clientEmails[$i]['ClientEmail']['reservation_email'])) ? $clientEmails[$i]['ClientEmail']['reservation_email'] : '', 'style' => 'width:80%;')); ?>
				<?php echo $this->Form->hidden('ClientEmail.' . $i . '.id', array ('value' => (!empty($clientEmails[$i]['ClientEmail']['id'])) ? $clientEmails[$i]['ClientEmail']['id'] : '')); ?>
			</td>
		</tr>
	<?php } ?>
	<tr>
		<td class="alert-success">予約申込時の備考欄の有無</td>
		<td><?php echo $this->Form->radio('need_remark', array (true => '有り　', false => '無し'), array ('value' => $clients['Client']['need_remark'], 'legend' => false, 'div' => false, 'label' => false)); ?></td>
	</tr>
	<tr>
		<td class="alert-success">キャンセルポリシー</td>
		<td>
			<?php echo $cancelPolicy; ?><br>
			・予約時間を１時間以上過ぎてもご連絡のない場合は、キャンセルとして処理させていただきます。
		</td>
	</tr>
	<tr>
		<td class="alert-success">キャンセルポリシー・補足</td>
		<td><?php echo $this->Form->textarea('cancel_policy', array ('value' => $clients['Client']['cancel_policy'], 'style' => 'width:80%;height:100px;')); ?></td>
	</tr>
	<tr>
		<td class="alert-success">お支払い方法</td>
		<td>
			<div><?php echo $this->Form->checkbox('accept_cash', array ('checked' => $clients['Client']['accept_cash']));?> 現金</div>
			<div><?php echo $this->Form->checkbox('accept_card', array ('checked' => $clients['Client']['accept_card']));?> クレジットカード</div>
			<div style="margin-left:20px;">
				<?php
				echo $this->Form->input(
					'ClientCard.credit_card_id',
					array (
				    	'type'      => 'select' ,
	  					'multiple'  => 'checkbox',
	  			    	'options'   => $creditCardList,
	  			    	'value'     => $clientCards,
					)
				);
				?>
			</div>
		</td>
	</tr>
	<tr>
		<td class="alert-success">注意事項</td>
		<td><?php echo $this->Form->textarea('precautions', array ('value' => $clients['Client']['precautions'], 'style' => 'width:80%;height:100px;')); ?></td>
	</tr>
	<tr>
		<td class="alert-success">予約完了メール内定型文</td>
		<td><?php echo $this->Form->textarea('reservation_content', array ('value' => $clients['Client']['reservation_content'], 'style' => 'width:80%;height:100px;')); ?></td>
	</tr>
	<tr>
		<td class="alert-success">PR文</td>
		<td><?php echo $this->Form->textarea('public_relations', array ('value' => $clients['Client']['public_relations'], 'style' => 'width:80%;height:100px;')); ?></td>
	</tr>
	<tr>
		<td class="alert-success">免責補償約款PDF</td>
		<td>
			<?php
			if(!empty($clients['Client']['clause_pdf'])) {
				echo "登録済み<br />";
			}

			echo $this->Form->file('clause_pdf');
			?>
		</td>
	</tr>
</table>
<div>
	<?php echo $this->Form->submit('更新', array ('class' => 'btn btn-success', 'div' => false)); ?>
	<?php echo $this->Html->link('<span class="btn btn-warning">戻る</span>', '/Clients/', array ('escape' => false)); ?>
</div>
<?php echo $this->Form->end(); ?>
