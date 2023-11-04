<div class="pages form">
<?php echo $this->Form->create('Page',array('inputDefaults'=>array('div'=>false,'label'=>false,'class'=>false))); ?>
	<h3>ページ追加</h3>
	<table class="table table-bordered">
		<tr>
			<th class="span3">ページカテゴリー</th>
			<td>
				<?php
				echo $this->Form->input('page_category_id',array(
						'label'=>false
				));
				?>
			</td>
		</tr>
		<tr>
			<th class="span3">ページ名</th>
			<td>
				<?php
				echo $this->Form->input('name',array(
						'label'=>false
				));
				?>
			</td>
		</tr>
		<tr>
			<th class="span3">url</th>
			<td>
				https://example.com/rentacar/client/<?php echo $this->Form->input('url',array('label'=>false,'div'=>false)); ?>/
				<br /><code>※コントローラー名(一階層目のURL)はキャメルケースでご登録ください。</code>
				<br /> <code>例:OK→「TopPages/page_1」 NG→「top_pages/page_1」</code>
			</td>
		</tr>
		<tr>
			<th class="span3">新規タブフラグ</th>
			<td>
				<?php
				echo $this->Form->input('new_tab_flg',array(
						'options'=>array(0=>'現在のタブで開く',1=>'新規タブで開く'),
				));
				?>
			</td>
		</tr>
	</table>
	<div class="right">
		<?php echo $this->Form->submit('登録する',array('class'=>'btn btn-success')); ?>
		<?php echo $this->Form->end(); ?>
	</div>
</div>

