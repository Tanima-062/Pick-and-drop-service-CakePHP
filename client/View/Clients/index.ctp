<style>
table tr th {
	width: 20%;
}
</style>

<h3>基本情報管理</h3>

<table class="table table-bordered table-striped table-condensed">
	<tr>
		<th>商標</th>
		<td><?php echo $clients['Client']['name']; ?></td>
	</tr>
	<?php
	for ($i = 0; $i < 3; $i++) {
		if (!empty($clientEmails[$i]['ClientEmail']['reservation_email'])) {
		?>
			<tr>
				<th>予約通知先メールアドレス <?php echo $i + 1; ?></th>
				<td><?php echo  $clientEmails[$i]['ClientEmail']['reservation_email']; ?></td>
			</tr>
		<?php
		}
	}
	?>
	<tr>
		<th>予約申込時の備考欄の有無</th>
		<td><?php echo ($clients['Client']['need_remark'] == 1) ? '有り' : '無し'; ?></td>
	</tr>
	<tr>
		<th>キャンセルポリシー</th>
		<td>
			<?php echo $cancelPolicy; ?><br>
			・予約時間を１時間以上過ぎてもご連絡のない場合は、キャンセルとして処理させていただきます。
		</td>
	</tr>
	<tr>
		<th>キャンセルポリシー・補足</th>
		<td><?php echo nl2br($clients['Client']['cancel_policy']); ?></td>
	</tr>
	<tr>
		<th>お支払い方法</th>
		<td>
			<div><?php echo ($clients['Client']['accept_cash'] == 1) ? '現金' : ''; ?></div>
			<div><?php echo ($clients['Client']['accept_card'] == 1) ? 'クレジットカード' : ''; ?></div>
			<?php if (($clients['Client']['accept_card'] == 1) && (!empty($clientCards))) { ?>
				<ul style="margin-left:20px;">
					<?php foreach ($clientCards as $val) { ?>
						<li><?php echo $creditCardList[$val]; ?></li>
					<?php } ?>
				</ul>
			<?php } ?>
		</td>
	</tr>
	<tr>
		<th>注意事項</th>
		<td><?php echo nl2br($clients['Client']['precautions']); ?></td>
	</tr>
	<tr>
		<th>予約完了メール内定型文</th>
		<td><?php echo nl2br($clients['Client']['reservation_content']); ?></td>
	</tr>
	<tr>
		<th>PR文</th>
		<td><?php echo nl2br($clients['Client']['public_relations']); ?></td>
	</tr>
	<tr>
	  <th>免責補償約款PDF</th>
	  <td>
	    <?php
	    if(!empty($clients['Client']['clause_pdf'])) {
	      echo $this->Html->link('登録済', '/../files/clause_pdf/'.$clients['Client']['clause_pdf']);
	    } else {
	      echo '未登録';
	    }
	    ?>
	  </td>
	 </tr>
</table>
<div>
	<?php echo $this->Html->link('<span class="btn btn-warning">編集</span>', '/Clients/edit/', array ('escape' => false)); ?>
</div>
