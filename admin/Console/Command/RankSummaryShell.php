<?php
App::uses('AppShell', 'Console/Command');

class RankSummaryShell extends AppShell {

	public $uses = array('Reservation','KeyValue');

	private $rank = 5; // 上位何件まで出すか

	public function startup() {
		parent::startup();
	}

	/**
	 * 
	 */
	public function main() {
		
		$summary = array();

		// 日別予約獲得(件数)
		$summary[] = $this->getReservationEarnedSummary('%Y/%m/%d', 'cnt');
		
		// 日別予約獲得(見込売上)
		$summary[] = $this->getReservationEarnedSummary('%Y/%m/%d', 'total');
		
		// 月別予約獲得(件数)
		$summary[] = $this->getReservationEarnedSummary('%Y/%m', 'cnt');
		
		// 月別予約獲得(見込売上)
		$summary[] = $this->getReservationEarnedSummary('%Y/%m', 'total');
		
		// 日別売上(成約数)
		$summary[] = $this->getSalesSummary('%Y/%m/%d', 'cnt');

		// 日別売上(確定売上)
		$summary[] = $this->getSalesSummary('%Y/%m/%d', 'total');
		
		// 月別売上(成約数)
		$summary[] = $this->getSalesSummary('%Y/%m', 'cnt');
		
		// 月別売上(確定売上)
		$summary[] = $this->getSalesSummary('%Y/%m', 'total');

		if(count($summary) > 0){
			$json = json_encode($summary);
		} else {
			$json = '';
		}

		$keyValue = $this->KeyValue->find('first', array('conditions' => array('key'=> 'admin_dashboard_data')));

		if(empty($keyValue)){
			$record = array();
			$record['id'] = null;
			$record['key'] = 'admin_dashboard_data';
		} else {
			$record = $keyValue['KeyValue'];
			$record['modified'] = date('Y-m-d H:i:s');
		}
		
		$record['value'] = $json;

		$this->KeyValue->save($record);

	}

	// 予約獲得数集計クエリ関数
	private function getReservationEarnedSummary($format, $order) {
		$salesType = Constant::SALES_TYPE_ARRANGED;
		$sql = "
			SELECT
				DATE_FORMAT(reservation_datetime, '{$format}') AS record,
				COUNT(*) AS cnt,
				SUM(amount) AS total
			FROM
				rentacar.reservations AS r
				INNER JOIN rentacar.commodity_items AS ci
					ON ci.id = r.commodity_item_id
				INNER JOIN rentacar.commodities AS co
					ON co.id = ci.commodity_id
			WHERE
				co.sales_type = '{$salesType}'
			GROUP BY
				record
			ORDER BY
				{$order} DESC
			LIMIT
				{$this->rank}
		";
		
		return $this->Reservation->queryC($sql, array(), '1hour');
	}

	// 売上集計クエリ関数
	private function getSalesSummary($format, $order) {
		$salesType = Constant::SALES_TYPE_ARRANGED;
		$sql = "
			SELECT
				(CASE
					WHEN c.conclusion_contract_criteria = 1
						THEN DATE_FORMAT(r.return_datetime, '{$format}')
					WHEN c.conclusion_contract_criteria = 0
						THEN DATE_FORMAT(r.rent_datetime, '{$format}')
					ELSE NULL
					END
				) AS record
				, COUNT(*) AS cnt
				, SUM(amount) AS total
			FROM
				rentacar.reservations AS r
				INNER JOIN rentacar.clients AS c
					ON c.id = r.client_id
				INNER JOIN rentacar.commodity_items AS ci
					ON ci.id = r.commodity_item_id
				INNER JOIN rentacar.commodities AS co
					ON co.id = ci.commodity_id
			WHERE
				r.reservation_status_id = 2 AND
				co.sales_type = '{$salesType}'
			GROUP BY
				record
			ORDER BY
				{$order} DESC
			LIMIT
				{$this->rank}
		";
		
		return $this->Reservation->queryC($sql, array(), '1hour');
	}

}
