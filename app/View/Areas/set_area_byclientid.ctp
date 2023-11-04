<?php
if(!empty($areaList)) {
	if(!empty($rentFlg)) {
		echo $this->Form->input('area_id',array('options'=>$areaList,'label'=>false,'div'=>false,'name'=>'area_id'));
	} else {
		echo $this->Form->input('return_area_id',array('options'=>$areaList,'label'=>false,'div'=>false,'name'=>'return_area_id'));
	}
}
?>