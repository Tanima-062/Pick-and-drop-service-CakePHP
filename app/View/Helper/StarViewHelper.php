<?php
App::uses('AppHelper', 'View/Helper');

class StarViewHelper extends AppHelper {
	var $helpers = array('Html');

	function view($star,$width='84',$height='16') {
		if(!empty($star)) {
			//～1.4 -> 1の星
			if(is_numeric($star)) {
				if($star <= 1.4) {

					echo $this->Html->image('company/icon_star_02.png',array('width'=>$width,'height'=>$height));
				//1.5～1.9 -> 1.5の星
				} else if($star >= 1.5 && $star <= 1.9) {
					echo $this->Html->image('company/icon_star_03.png',array('width'=>$width,'height'=>$height));
				//2.0～2.4　→　2の星
				} else if($star >= 2 && $star <= 2.4) {
					echo $this->Html->image('company/icon_star_04.png',array('width'=>$width,'height'=>$height));
				//2.5～2.9　→　2.5の星
				} else if($star >= 2.5 && $star <= 2.9) {
					echo $this->Html->image('company/icon_star_05.png',array('width'=>$width,'height'=>$height));
				//3.0～3.4　→　3の星
				} else if($star >= 3 && $star <= 3.4) {
					echo $this->Html->image('company/icon_star_06.png',array('width'=>$width,'height'=>$height));
				//3.5～3.9　→　3.5の星
				} else if($star >= 3.5 && $star <= 3.9) {
					echo $this->Html->image('company/icon_star_07.png',array('width'=>$width,'height'=>$height));
				//4.0～4.4　→　4の星
				} else if($star >= 4 && $star <= 4.4) {
					echo $this->Html->image('company/icon_star_08.png',array('width'=>$width,'height'=>$height));
				//4.5～4.9　→　4.5の星
				} else if($star >= 4.5 && $star <= 4.9) {
					echo $this->Html->image('company/icon_star_09.png',array('width'=>$width,'height'=>$height));
				//5以上
				} else if($star >= 5) {
					echo $this->Html->image('company/icon_star_10.png',array('width'=>$width,'height'=>$height));
				} else {
				//
					echo $this->Html->image('company/icon_star_00.png',array('width'=>$width,'height'=>$height));
				}
			} else {
					echo $this->Html->image('company/icon_star_10.png',array('width'=>$width,'height'=>$height));
			}
		} else {
			echo $this->Html->image('company/icon_star_00.png',array('width'=>$width,'height'=>$height));
		}
	}
}
?>