<?php
App::uses('AppController', 'Controller');
/**
 * OfficeSettlements Controller
 *
 * @property Office $Office
 * @property SettlementCompany $SettlementCompany
 * @property Client $Client
 * @property Prefecture $Prefecture
 */
class OfficeSettlementsController extends AppController {

	public $uses = ['Office', 'SettlementCompany', 'Prefecture', 'Client'];

	public function beforeFilter() {
		parent::beforeFilter();

	}

/**
 * index method
 *
 * @return void
 */
	public function index() {

		// 検索パラメータ
		$params = [
			'id'                    => (isset($this->request->query['id']))	         			? trim($this->request->query['id']) : '',
			'client_id'             => (isset($this->request->query['client_id']))				? trim($this->request->query['client_id']) : '',
			'settlement_company_id' => (isset($this->request->query['settlement_company_id']))	? trim($this->request->query['settlement_company_id']) : '',
			'prefecture_id'         => (isset($this->request->query['prefecture_id']))			? trim($this->request->query['prefecture_id']) : '',
			'delete_flg'			=> (isset($this->request->query['delete_flg']))				? trim($this->request->query['delete_flg']) : '',
		];

		foreach($params as $key => $value) {
			$this->set($key, $value);
		}

		$this->set('clientList', $this->Client->find('list', ['conditions' => ['delete_flg' => 0]]));
		$this->set('settlementCompanyList', $this->SettlementCompany->find('list'));
		$this->set('prefectureList', $this->Prefecture->find('list'));

		// 一覧表示
		$this->Paginator->settings = ['Office' =>
			[
				'recursive' => 0,
				'fields' => [
					'Office.*',
					'Client.*',
					'Area.*',
					'SettlementCompany.*',
					'Prefecture.*'
				],
				'joins' => [
					[
						'type' => 'left',
						'table' => 'prefectures',
						'alias' => 'Prefecture',
						'conditions' => ['Prefecture.id = Area.prefecture_id']
					],
				],
				'paramType' => 'querystring'
			]
		];

		if (!empty($params['id'])) {
			$this->Paginator->settings['Office']['conditions']['Office.id'] = $params['id'];
		}

		if (!empty($params['client_id'])) {
			$this->Paginator->settings['Office']['conditions']['Client.id'] = $params['client_id'];
		}

		if (!empty($params['settlement_company_id'])) {
			$this->Paginator->settings['Office']['conditions']['SettlementCompany.id'] = $params['settlement_company_id'];
		}

		if (!empty($params['prefecture_id'])) {
			$this->Paginator->settings['Office']['conditions']['Prefecture.id'] =$params['prefecture_id'];
		}

		if (isset($params['delete_flg']) && is_numeric($params['delete_flg'])) {
			$this->Paginator->settings['Office']['conditions']['Office.delete_flg'] = $params['delete_flg'];
		}

		$this->set('Offices', $this->Paginator->paginate('Office'));
	}

/**
 * edit method
 *
 * @throws NotFoundException
 * @param string $id
 * @return void
 */
	public function edit($id = null) {
		$this->Office->recursive = 0;
		if (!$this->Office->exists($id)) {
			throw new NotFoundException(__('Invalid settlement company'));
		}
		if ($this->request->is('post') || $this->request->is('put')) {
			if ($this->Office->save($this->request->data)) {
				$this->Session->setFlash('営業所精算管理を編集しました','default',array('class'=>'alert alert-success'));
				$this->redirectReferer();
			} else {
				$this->Session->setFlash('営業所精算管理の編集に失敗しました','default',array('class'=>'alert alert-error'));
			}
		} else {
			$options = array('conditions' => array('Office.' . $this->Office->primaryKey => $id));
			$this->request->data = $this->Office->find('first', $options);
			$this->set('settlementCompanyies', $this->SettlementCompany->find('list', array(
				'conditions' => array('client_id' => $this->request->data['Office']['client_id'])
			)));
		}
	}

    /**
     * 元画面へ遷移
     * ※元画面がなければ初期画面へ
     *
     * @return void
     */
    private function redirectReferer()
    {
        // refererがあれば戻す
        if (!empty($this->request->data['Custom']['referer'])) {
            $this->redirect($this->request->data['Custom']['referer']);
        } else {
            $this->redirect(array('action' => 'index'));
        }
    }
}
