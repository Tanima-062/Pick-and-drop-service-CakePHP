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
		$('#CastOrder').val(data.toString());
	},
	update : function(){
		$('#submit').removeAttr('disabled');
	},
	cancel:'.stop'
});

//$('#sortable-div td').sortable({cancel : '.stop'});

});
</script>

<div class="clientCarModelSorts index">
	<h3>車種ソート</h3>

	<div class="right">
		<?php
			echo $this->Form->create('Client');
			echo $this->Form->hidden('order',array('id'=>'CastOrder'));
			echo $this->Form->submit('並び順を保存する',array('id'=>'submit','class'=>'btn btn-primary','disabled'=>'disabled'));
			echo $this->Form->end();
		?>
	</div>

	<table class="table table-striped table-bordered">
		<thead>
			<tr class="alert-info">
				<th>順番</th>
				<th>車種名</th>
			</tr>
		</thead>
		<tbody id="sortable-div">
			<?php
			$i = 1;
			foreach ($clientCarModelSorts as $clientCarModelSort) {
			?>
			<tr id="<?php echo $clientCarModelSort['CarModel']['id'];?>" class="ui-state" >
				<td><?php echo $i;?>&nbsp;</td>
				<td>
					<?php
					echo $clientCarModelSort['CarModel']['name'];
					?>
				</td>
			</tr>
			<?php
				$i++;
			}
			?>
		</tbody>
	</table>
</div>
