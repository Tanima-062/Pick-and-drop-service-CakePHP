<?php

App::uses('AppModel', 'Model');

/**
 * Client Model
 *
 */
class Client extends AppModel {

	protected $cacheConfig = '1day';

	public function getClientById($id) {

		$options = array(
			'conditions' => array(
				'delete_flg' => 0,
				'id' => $id
			),
			'recursive' => -1
		);

		return $this->findC('first', $options);
	}

	/**
	 * クライアントテーブルのみ取得する
	 */
	public function getClientInfoList() {
		$this->recursive = -1;
		$clients = $this->findC('all', array('conditions' => array('delete_flg' => 0)));
		$clientList = array();
		foreach ($clients as $client) {
			$key = $client['Client']['id'];
			$clientList[$key] = $client['Client'];
		}

		return $clientList;
	}

	// 内部リンク変換用
	public function getAllClientList() {
		$result = $this->findC('all', array(
			'conditions' => 'Client.delete_flg = 0',
			'fields' => array(
				'Client.name',
				'Client.url'
			)
		));

		$combined = array();
		if (!empty($result)) {
			foreach ($result as $data) {
				$name = $data['Client']['name'];
				$url = '/rentacar/company/' . $data['Client']['url'] . '/';
				$combined[] = array(
					'name' => $name,
					'url' => $url,
					'link_cd' => $data['Client']['url'],
					'length' => mb_strlen($name)
				);
				// 空白除いた名前も登録
				if (mb_strpos($name, ' ') !== false) {
					$replaced = str_replace(' ', '', $name);
					$combined[] = array(
						'name' => $replaced,
						'url' => $url,
						'link_cd' => $data['Client']['url'],
						'length' => mb_strlen($replaced)
					);
				}
			}
		}

		return $combined;
	}

	public function getClientList() {
		$options = array(
			'fields' => array(
				'Client.id',
				'Client.name',
				'Client.url',
				'Client.logo_image',
				'Client.sp_logo_image',
			),
			'conditions' => array(
				'Client.delete_flg' => 0,
			),
			'order' => array(
				'Client.sort',
				'Client.id',
			),
		);

		$clientList = $this->findC('all', $options);
		return $clientList;
	}

	public function getClientListAndPostmetaData() {
		$options = array(
			'fields' => array(
				'Client.id',
				'MIN(RcPostmeta.post_id) as post_id'
			),
			'conditions' => array(
				'Client.area_type >' => 0,
				'Client.delete_flg' => 0,
			),
			'joins' => array(
				array(
					'type' => 'INNER',
					'alias' => 'RcPostmeta',
					'table' => 'rc_postmetas',
					'conditions' => array(
						'RcPostmeta.meta_key' => 'company-id',
						'RcPostmeta.meta_value = Client.id'
					)
				),
				array(
					'type' => 'INNER',
					'alias' => 'RcPost',
					'table' => 'rc_posts',
					'conditions' => array(
						'RcPostmeta.post_id = RcPost.id',
						'OR' => array(
							array('RcPost.post_status' => 'publish'),
							array('RcPost.post_status' => 'draft')
						),
					),
				),
			),
			'group' => array(
				'Client.id'
			),
			'recursive' => -1
		);
		$postInfo = $this->findC('all', $options);
		$postMetaIds = Hash::extract($postInfo, '{n}.0.post_id');

		$options = array(
			'fields' => array(
				'Client.id',
				'Client.name',
				'Client.url',
				'Client.logo_image',
				'Client.sp_logo_image',
				'RcPostmeta.meta_value',
			),
			'conditions' => array(
				'Client.area_type >' => 0,
				'Client.delete_flg' => 0,
			),
			'joins' => array(
				array(
					'type' => 'LEFT',
					'alias' => 'RcPostmetaPre',
					'table' => 'rc_postmetas',
					'conditions' => array(
						'RcPostmetaPre.post_id' => $postMetaIds,
						'RcPostmetaPre.meta_key' => 'company-id',
						'RcPostmetaPre.meta_value = Client.id'
					)
				),
				array(
					'type' => 'LEFT',
					'alias' => 'RcPostmeta',
					'table' => 'rc_postmetas',
					'conditions' => array(
						'RcPostmeta.post_id = RcPostmetaPre.post_id',
						'RcPostmeta.meta_key' => 'company-list',
					)
				)
			),
			'order' => array(
				'Client.sort',
				'Client.id',
			),
		);
		$clientList = $this->findC('all', $options);
		return $clientList;
	}

	public function getClientByLinkCd($link_cd) {
		$options = array(
			'conditions' => array(
				'url like' => $link_cd,
				'delete_flg' => 0,
			),
		);

		return $this->find('list', $options);
	}

	public function getSpecificClientByLinkCd($link_cd) {
		$options = array(
			'conditions' => array(
				'url' => $link_cd,
				'delete_flg' => 0,
			),
		);

		return $this->find('first', $options);
	}

	public function getClientListWithAreaType($client_ids = array()) {
		$options = array(
			'fields' => array(
				'Client.id',
				'Client.name',
				'Client.area_type',
				'Client.sp_logo_image',
			),
			'conditions' => array(
				'Client.area_type >' => 0,
				'Client.delete_flg' => false,
			),
			'order' => array(
				'Client.area_type',
				'Client.sort',
				'Client.id',
			),
			'recursive' => -1,
		);

		// IDで絞り込み
		if (is_array($client_ids) && count($client_ids) > 0) {
			$options['conditions']['Client.id'] = $client_ids;
		}

		return $this->findC('all', $options);
	}

	public function getClientListByAreaType($areaType) {
		if (empty($areaType)) {
			return array();
		}

		$options = array(
			'fields' => array(
				'Client.id',
			),
			'conditions' => array(
				'Client.area_type' => $areaType,
				'Client.delete_flg' => false,
			),
			'order' => array(
				'Client.sort',
				'Client.id',
			),
			'recursive' => -1,
		);

		$ret = $this->findC('all', $options);
		return Hash::extract($ret, '{n}.Client.id');
	}

	public function getClientListByPrefectureId($prefectureId) {
		$options = array(
			'fields' => array(
				'Client.id',
				'Client.name',
				'Client.url',
				'Client.logo_image',
				'Client.sp_logo_image',
				'Client.area_type'
			),
			'conditions' => array(
				'Client.area_type >' => 0,
				'Client.delete_flg' => 0
			),
			'joins' => array(
				array(
					'type' => 'INNER',
					'alias' => 'Office',
					'table' => 'offices',
					'conditions' => array(
						'Office.client_id = Client.id',
						'Office.delete_flg' => 0
					)
				),
				array(
					'type' => 'INNER',
					'alias' => 'Area',
					'table' => 'areas',
					'conditions' => array(
						'Area.id = Office.area_id',
						'Area.prefecture_id' => $prefectureId,
						'Area.delete_flg' => 0
					)
				)
			),
			'group' => array(
				'Client.id'
			),
			'order' => array(
				'Client.sort',
				'Client.id',
			),
			'recursive' => -1,
		);

		$result = $this->findC('all', $options);
		if (!empty($result)) {
			$result = Hash::combine($result, '{n}.Client.id', '{n}.Client');
		}

		return $result;
	}

	public function notSearchableList() {
		$options = array(
			'conditions' => array(
				'is_searchable' => 0,
			),
		);

		return $this->find('list', $options);
	}
}
