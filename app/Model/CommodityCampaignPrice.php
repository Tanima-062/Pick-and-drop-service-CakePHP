<?php
App::uses('AppModel', 'Model');
App::uses('PublicHoliday', 'Model');
/**
 * CommodityCampaignPrice Model
 *
 * @property Client $Client
 * @property CommodityItem $CommodityItem
 * @property Staff $Staff
 */
class CommodityCampaignPrice extends AppModel {
	/**
	* 予約予定日全てのキャンペーン無効日(時)を返す
	* @param string $date           予約開始日
	* @param string $dateTo         予約終了日
	* @param string $dateFlg        歴日制は1,時間制は0
	* @return array $campaignPrice  キャンペーンIDとその金額(0円)
	*/
	public function getCampaignInvalidDate($commodityItemIds, $date, $dateTo, $dateFlg) {
		if (empty($date) || empty($dateTo) || $date == $dateTo) {
			return array();
		}
		if($dateFlg == 1){
			//歴日制は「以後1日」
			$spanCount = '0';
		}else{
			//時間制は「超過1時間」
			$spanCount = '25';
		}

		//予約初日は別処理で判定済のため飛ばす
		$date = date('Y-m-d',strtotime($date. " +1 day"));

		//予定日全ての曜日を取得する
		$this->PublicHoliday = new PublicHoliday();
		$dateInfo = $this->PublicHoliday->getMultiDayInfo($date,$dateTo);
		foreach($dateInfo as $dk => $dv){
			$subCondition[] = array(
				'CampaignTerm.start_date <= ' => $dk,
				'CampaignTerm.end_date >= ' => $dk,
				'CampaignTerm.'.$dv['identifier'] => '1'
			);
		}

		$options = array(
			'fields' => array(
				'CommodityCampaignPrice.commodity_item_id',
				'CommodityCampaignPrice.price'
			),
			'joins' => array(
				array(
					'type' => 'INNER',
					'table' => 'campaign_terms',
					'alias' => 'CampaignTerm',
					'conditions' => array('CommodityCampaignPrice.campaign_id = CampaignTerm.campaign_id')
				)
			),
			'conditions' => array(
				'CommodityCampaignPrice.span_count' => $spanCount,
				'CommodityCampaignPrice.price' => '0',
				'CommodityCampaignPrice.commodity_item_id' => $commodityItemIds,
				'OR' => $subCondition
			),
			'recursive' => -1
		);
		$ret = $this->findC('all',$options);
		return Hash::combine($ret,'{n}.CommodityCampaignPrice.commodity_item_id','{n}.CommodityCampaignPrice.price');
	}


}
