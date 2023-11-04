<?php
App::uses('AppShell', 'Console/Command');

class InsertDevStockShell extends AppShell {

	public $uses = array('CarClassStock');

	const MONTH_LIMIT = 6;
	const STOCK_COUNT = 10;

	// 開発環境の在庫を自動的に作る
	public function main() {
		if (IS_PRODUCTION) {
			// 本番で動かしちゃダメ
			return;
		}

		$now = date('Y-m-d H:i:s');
		echo "InsertDevStock start : $now \n";

		$limitMonth = date('Y-m', strtotime(date('Y-m-01').' +'.self::MONTH_LIMIT.' months'));

		// 対象はすでに在庫が入っている在庫管理地域x車両クラス
		$this->CarClassStock->virtualFields['max_stock_month'] = '';
		$records = $this->CarClassStock->find('all', array(
			'fields' => array(
				'CarClassStock.client_id',
				'CarClassStock.car_class_id',
				'CarClassStock.stock_group_id',
				"max(date_format(CarClassStock.stock_date, '%Y-%m')) as CarClassStock__max_stock_month"
			),
			'group' => array(
				'CarClassStock.client_id',
				'CarClassStock.car_class_id',
				'CarClassStock.stock_group_id'
			),
			'having' => array(
				"max(date_format(CarClassStock.stock_date, '%Y-%m')) <" => $limitMonth
			)
		));

		foreach ($records as $r) {
			$data = array();
			for ($i = 0; $i <= self::MONTH_LIMIT; $i++) {
				$time = strtotime(date('Y-m-01').' +'.$i.' months');
				$month = date('Y-m', $time);
				if ($r['CarClassStock']['max_stock_month'] >= $month) {
					continue;
				}
				$lastDay = date('t', $time);
				for ($j = 1; $j <= $lastDay; $j++) {
					$data[] = sprintf(
						"(%d,%d,%d,'%s-%02d',%d,0,now(),now())",
						$r['CarClassStock']['client_id'],
						$r['CarClassStock']['stock_group_id'],
						$r['CarClassStock']['car_class_id'],
						$month, $j, self::STOCK_COUNT
					);
				}
			}
			if (!empty($data)) {
				$this->CarClassStock->bulkInsert($data);
			}
		}

		$now = date('Y-m-d H:i:s');
		echo "InsertDevStock end   : $now \n";
	}
}