<div class="company_outline">
	<div class="company_outline_text limited">
		<?= $outlineText; ?>
<?php
	if (!empty($clausePdf)) {
?>
	<br><a href="/rentacar/files/clause_pdf/<?= $clausePdf; ?>"><span>約款・規約(PDF)</span></a>
<?php
	}
?>
	</div>
<?php
	if (!empty($outlineText)) {
?>	
	<div class="js-open_outline btn_outline"></div>
<?php
	}
?>
</div>
<script>
$(function(){
	$('.js-open_outline').click(function(){
		$(".company_outline_text").toggleClass( "limited" );
	});
});
</script>