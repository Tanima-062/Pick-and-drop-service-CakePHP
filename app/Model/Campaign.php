<?php
App::uses('AppModel', 'Model');
App::uses('PublicHoliday','Model');
/**
 * Campaign Model
 *
 */
class Campaign extends AppModel {

	protected $cacheConfig = '1hour';

	public function getCampaignIds($dayCheck, $commodityItemIds, $dateFrom, $dateTo = null) {
		if (empty($commodityItemIds) || empty($dateFrom)) {
			return array();
		}

		$db = $this->getDataSource();
		$subQuery = $db->buildStatement(
			array(
				'fields' => array(
					'DISTINCT CommodityCampaignPrice.campaign_id',
				),
				'table' => $db->fullTableName('commodity_campaign_prices'),
				'alias' => 'CommodityCampaignPrice',
				'conditions' => array(
					'CommodityCampaignPrice.commodity_item_id' => $commodityItemIds,
					'CommodityCampaignPrice.delete_flg' => 0,
				),
			), $this
		);

		if (empty($dateTo)) {
			$dateEnd = $dateFrom;
		} else {
			$dateEnd = $dateTo;
		}

		$options = array(
			'fields' => array(
				'CampaignTerm.campaign_id',
			),
			'joins' => array(
				array(
					'type' => 'INNER',
					'table' => "({$subQuery})",
					'alias' => 'CampaignPrice',
					'conditions' => array(
						'CampaignPrice.campaign_id = Campaign.id',
					),
				),
			),
			'conditions' => array(
				'Campaign.delete_flg' => 0,
			),
			'recursive' => -1,
		);

		$campaignTermCondition = array(
			'type' => 'INNER',
			'table' => 'campaign_terms',
			'alias' => 'CampaignTerm',
			'conditions' => array(
				'CampaignTerm.campaign_id = Campaign.id',
				'CampaignTerm.start_date <=' => $dateEnd,
				'CampaignTerm.end_date >=' => $dateFrom,
				'CampaignTerm.delete_flg' => 0,
			),
		);
		if ($dayCheck) {
			// 曜日条件を追加する
			$this->PublicHoliday = new PublicHoliday();
			$date = date('Y-m-d',strtotime($dateFrom));
			$dateInfo = $this->PublicHoliday->getDayInfo($date);
			$campaignTermCondition['conditions'] = array_merge($campaignTermCondition['conditions'], ['CampaignTerm.'.$dateInfo['identifier'] => 1]);
		}
		array_push($options['joins'], $campaignTermCondition);

		return $this->findC('all', $options);
	}
}
