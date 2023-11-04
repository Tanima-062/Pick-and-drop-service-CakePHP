<?php
// エラーページのプレビュー用
class ErrorsController extends AppController {

	public function beforeFilter() {
		parent::beforeFilter();
		if (strcmp(uaCheck(), Constant::DEVICE_SMART_PHONE) == 0) {
			$this->layout = 'sp_default';
		}
	}

	public function error400() {}
	public function error404() {}
	public function missing_connection() {}

}