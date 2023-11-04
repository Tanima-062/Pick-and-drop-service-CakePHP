<?php
App::uses('AppShell', 'Console/Command');

class StockPartitionShell extends AppShell {

	public $uses = array('CarClassStock');

	public function main() {
		
		$now = date("Y-m-d H:i:s");
		$this->out("Start Batch ($now)");
		
		$add_ret = $this->addPartition($now);
		$del_ret = $this->dropPartition($now);
		
		$now = date("Y-m-d H:i:s");
		$this->out("End Batch ($now)");
		
		return ($add_ret && $del_ret) ? 0 : 1;
	}

	private function addPartition($time) {
		// 23ヶ月後のパーティションを追加する
		$pname = 'p' . date('Ym', strtotime($time . ' +23 month'));
		$value = date('Y-m-01 00:00:00', strtotime($time . ' +24 month'));
		
		$sql = "ALTER TABLE `rentacar`.`car_class_stocks` ADD PARTITION ("
			 . "PARTITION `{$pname}` VALUES LESS THAN ('{$value}')"
			 . ")";

		try {
			$ret = $this->CarClassStock->query($sql);
			$this->out("Add partition: {$pname}");
			
		} catch (Exception $e) {
			$ret = false;
			$this->err('Add partition: ' . $e->getMessage());
		}
		
		return $ret;
	}

	private function dropPartition($time) {
		// 2ヶ月前のパーティションを削除する
		$pname = 'p' . date('Ym', strtotime($time . ' -2 month'));
		
		$sql = "ALTER TABLE `rentacar`.`car_class_stocks` DROP PARTITION `{$pname}`";

		try {
			$ret = $this->CarClassStock->query($sql);
			$this->out("Drop partition: {$pname}");
			
		} catch (Exception $e) {
			$ret = false;
			$this->err('Drop partition: ' . $e->getMessage());
		}
		
		return $ret;
	}

}