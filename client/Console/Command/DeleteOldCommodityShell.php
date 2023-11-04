<?php
App::uses('AppShell', 'Console/Command');

class DeleteOldCommodityShell extends AppShell {

	public $uses = array('Commodity', 'CommodityRentOffice', 'CommodityReturnOffice');

	// 1クエリで更新するレコード数
	private $limit = 1000;
	// 次の更新への待機秒数
	private $wait = 60;

	public function main() {
		$now = date("Y-m-d H:i:s");
		$this->out("Start Batch ($now)");

		$commodityIds = $this->getOldCommodityIds();

		// 商品の論理削除
		if ($this->softDeleteCommodity($commodityIds)
			// 受取営業所の物理削除
			&& $this->deleteModelByCommodityId($this->CommodityRentOffice, $commodityIds, true)
			// 返却営業所の物理削除
			&& $this->deleteModelByCommodityId($this->CommodityReturnOffice, $commodityIds, true)) {
			$this->out('Deleted success');
		}

		$now = date("Y-m-d H:i:s");
		$this->out("End Batch ($now)");
	}

	/**
	 * 削除する商品IDを取得する
	 * 1:非公開商品で最後の商品情報編集から半年以上経過した商品
	 * 2:公開商品で提供終了日時を過ぎてから1年以上経過した商品
	 *
	 * @return array
	 */
	private function getOldCommodityIds() {
		$options = array(
			'fields' => 'Commodity.id',
			'joins' => array(
				array(
					'type' => 'INNER',
					'alias' => 'CommodityTerm',
					'table' => 'commodity_terms',
					'conditions' => 'Commodity.id = CommodityTerm.commodity_id',
				),
			),
			'conditions' => array(
				'Commodity.delete_flg' => 0,
				'OR' => array(
					array(
						// 非公開商品で最後の商品情報編集から半年以上経過した商品
						'Commodity.is_published' => 0,
						'Commodity.modified < DATE(NOW() - INTERVAL 6 MONTH)',
					),
					array(
						// 公開商品で提供終了日時を過ぎてから1年以上経過した商品
						'Commodity.is_published' => 1,
						'CommodityTerm.available_to < DATE(NOW() - INTERVAL 1 YEAR)',
					),
				),
			),
			'recursive' => -1,
		);

		$ret = $this->Commodity->find('list', $options);

		return array_keys($ret);
	}

	/**
	 * 商品を論理削除する
	 *
	 * @param array $commodityIds
	 * @return boolean
	 */
	private function softDeleteCommodity($commodityIds) {
		return $this->repeatDeleteRecord($this->Commodity, $commodityIds);
	}

	/**
	 * 商品IDから別マスタのレコードを削除する
	 *
	 * @param Model $model
	 * @param array $commodityIds
	 * @param boolean $physical
	 * @return boolean
	 */
	private function deleteModelByCommodityId(Model $model, $commodityIds, $physical = false) {
		$options = array(
			'fields' => 'id',
			'conditions' => array(
				'commodity_id' => $commodityIds,
			),
			'recursive' => -1,
		);

		$ids = $model->find('list', $options);

		if (empty($ids)) {
			return true;
		}

		return $this->repeatDeleteRecord($model, array_keys($ids), true);
	}

	/**
	 * 一定件数毎にレコードを削除する
	 *
	 * @param Model $model
	 * @param array $ids
	 * @param boolean $physical
	 * @return boolean
	 */
	private function repeatDeleteRecord(Model $model, $ids, $physical = false) {
		// joinを防ぐためアソシエーションオフ
		$model->unbindModel(array(
			'belongsTo' => array_keys($model->belongsTo),
		));

		$loop = 0;
		$maxLoop = (int)ceil(count($ids) / $this->limit);

		// limitで指定された件数毎にクエリ実行する
		while ($loop < $maxLoop) {
			$sliceIds = array_slice($ids, $loop * $this->limit, $this->limit);

			$conditions = array(
				$model->name . '.id' => $sliceIds,
			);

			if (!$this->deleteRecord($model, $sliceIds, $conditions, $physical)) {
				return false;
			}

			if (++$loop < $maxLoop) {
				sleep($this->wait);
			}
		}

		$this->out(
			(!$physical ? 'Updated ' : 'Deleted ')
			. $model->useTable . ' records: '
			. count($ids)
		);

		return true;
	}

	/**
	 * 削除処理の実行関数
	 *
	 * @param Model $model
	 * @param array $ids
	 * @param array $conditions
	 * @param boolean $physical
	 * @return boolean
	 */
	private function deleteRecord(Model $model, $ids, $conditions, $physical = false) {
		$model->begin();

		// 更新か削除か
		if (!$physical) {
			$now = "'" . date('Y-m-d H:i:s') . "'";
			$fields =  array(
				'delete_flg' => 1,
				'modified' => $now,
				'deleted' => $now,
			);
			$ret = $model->updateAll($fields, $conditions);
		} else {
			$ret = $model->deleteAll($conditions);
		}

		if (!$ret) {
			$model->rollback();
			$this->out('Error : Rollback');
			return false;
		}

		$model->commit();

		if (Configure::read('debug') >= 1) {
			$this->out(implode(',', $ids));
		}

		return true;
	}

}