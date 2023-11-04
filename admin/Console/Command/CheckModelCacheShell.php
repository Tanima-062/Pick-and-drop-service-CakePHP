<?php
App::uses('AppShell', 'Console/Command');

class CheckModelCacheShell extends AppShell {

	public function startup() {
		parent::startup();
	}

	/**
	 * モデルキャッシュの状態を確認する
	 */
	public function main() {
		foreach ($this->args as $modelName) {
			try {
				$this->loadModel($modelName);
				$this->$modelName->recursive = - 1;
				$firstRow = $this->$modelName->find('first');
				$this->out(date('Y/m/d H:i:s ') . $this->name . ': load ' . $modelName . "\n" . print_r(array_keys($firstRow[$modelName]), true));
			} catch (Exception $e) {
				$this->out(date('Y/m/d H:i:s ') . $this->name . ': load ' . $modelName . ' failed (' . $e->getMessage() . ' )');
			}
		}
	}

}
