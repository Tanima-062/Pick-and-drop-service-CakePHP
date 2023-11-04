<div id="js-content" class="processing-contents">
<?php
	if (!empty($sessionMessage)) {
		echo $this->element('session_message');
	}
?>
	<div class="processing_img">
		<img src="/img/loading.gif" alt="loading" width="100" />
	</div>
	<p class="processing_text">
		決済要求を受け付けました。<br />
		二重決済を防ぐため、ページの再読込等は行わないでください。
	</p>
<?php
	echo $this->Form->create('Reservation', array(
		'type' => 'post',
		'url' => 'completion/' . ($fromRentacarClient ? '?from_rentacar_client=true' : ''),
		'id' => 'form1',
		'class' => 'st-table rent-margin-bottom-l',
		'inputDefaults' => array(
			'div' => false,
			'label' => false,
			'legend' => false,
		),
	));
	echo $this->Form->hidden('uniqId', ['value' => $uniqId]);
	echo $this->Form->hidden('isStep1', ['value' => 0]);
	echo $this->Form->end();
?>
</div>
<script>
	$(window).on('load', function(){
		$('#form1').submit();
	});
</script>
