<?php
App::uses('AppController', 'Controller');
/**
 * Recommends Controller
 *
 * @property Recommend $Recommend
 */
class RecommendsController extends AppController
{

    public $uses = array('Recommend', 'Prefecture', 'RecommendPrefecture', 'MessageBoard');

    /**
     * 前処理
     *
     * @return void
     */
    public function beforeFilter()
    {
        parent::beforeFilter();
        $allPrefectureList = $this->Prefecture->getPrefectureList();
        $this->set('allPrefectureList', $allPrefectureList);

        $fixedPrefectureList = $this->Prefecture->getRecommendPrefectureList();
        if (count($fixedPrefectureList) > 0) {
            $fixedPrefectureList[0] = "全て";
            ksort($fixedPrefectureList);
        }
        $this->set('fixedPrefectureList', $fixedPrefectureList);

        $randomPrefectureList = $this->Prefecture->getRecommendRandomPrefectureList();
        if (count($randomPrefectureList) > 0) {
            $randomPrefectureList[0] = "全て";
            ksort($randomPrefectureList);
        }
        $this->set('randomPrefectureList', $randomPrefectureList);

        $this->set('clientList', $this->Client->find('list', ['conditions' => ['delete_flg' => 0]]));

        $this->set('applyTermFromOptions', array(
            'formName' => 'Recommend',
            'fieldName' => 'apply_term_from',
            'dateFormat' => 'YMD',
            'class' => 'span2',
            'minYear' => 2021,
            'maxYear' => date('Y')+4,
            'empty' => '---',
            'setCurrentMonth'=>false
        ));

        $this->set('applyTermToOptions', array(
            'formName' => 'Recommend',
            'fieldName' => 'apply_term_to',
            'dateFormat' => 'YMD',
            'minYear' => 2021,
            'maxYear' => date('Y')+4,
            'class' => 'span2',
            'empty' => '---',
            'setCurrentMonth'=>false
        ));

        $this->set('selectDateOptions', array(
            'formName' => 'Recommend',
            'fieldName' => 'select_date',
            'dateFormat' => 'YMD',
            'minYear' => 2021,
            'maxYear' => date('Y')+4,
            'class' => 'span2',
            'empty' => '---',
            'setCurrentMonth'=>false
        ));

        $this->set('isPublishedOptions', array(
            '0' => '非公開',
            '1' => '公開',
        ));

        $this->set('recommendFeeUnit', array(
            '0' => '円',
            '1' => '%',
        ));

        $this->set('settlementTiming', array(
            '0' => '成約',
            '1' => '申込',
        ));

        for ($i=0;$i<Constant::RECOMMEND_LIMIT_CNT;$i++) {
            $spaceOptions[$i] = chr(65+$i);
        }
        $this->set('spaceOptions', $spaceOptions);
    }

    /**
     * 一覧ページ
     *
     * @return void
     */
    public function index()
    {

        // 検索パラメータ
        $params = [
            'id'                    => (isset($this->request->query['id']))                    ? $this->request->query['id'] : '',
            'space'                    => (isset($this->request->query['space']))                ? $this->request->query['space'] : '',
            'client_id'                => (isset($this->request->query['client_id']))            ? $this->request->query['client_id'] : '',
            'apply_term_from'        => (isset($this->request->query['apply_term_from']))    ? $this->request->query['apply_term_from'] : '',
            'apply_term_to'         => (isset($this->request->query['apply_term_to']))        ? $this->request->query['apply_term_to'] : '',
            'select_date'         => (isset($this->request->query['select_date']))        ? $this->request->query['select_date'] : '',
            'prefecture_id'            => (isset($this->request->query['prefecture_id']))        ? $this->request->query['prefecture_id'] : '',
            'is_published'            => (isset($this->request->query['is_published']))        ? $this->request->query['is_published'] : '',
        ];

        foreach ($params as $key => $value) {
            $this->set($key, $value);
        }

        // 一覧表示
        $this->Paginator->settings = ['Recommend' =>
            [
                'recursive' => 0,
                'fields' => [
                    'Recommend.*',
                    'Client.*',
                ],
                'joins' => [
                    [
                        'type' => "LEFT",
                        'alias' => "RecommendPrefecture",
                        'table' => "recommend_prefectures",
                        'conditions' => "Recommend.id = RecommendPrefecture.recommend_id"
                    ]
                ],
                'conditions' => [
                    'Recommend.delete_flg' => 0,
                    'RecommendPrefecture.delete_flg' => 0,
                ],
                'group' => ['RecommendPrefecture.recommend_id'],
                'paramType' => 'querystring'
            ]
        ];

        if (!empty($params['id'])) {
            $this->Paginator->settings['Recommend']['conditions']['Recommend.id'] = $params['id'];
            $this->request->data['Recommend']['id'] = $params['id'];
        }

        if ($params['space'] != '') {
            $this->Paginator->settings['Recommend']['conditions']['Recommend.space'] = $params['space'];
            $this->request->data['Recommend']['space'] = $params['space'];
        }

        if (!empty($params['client_id'])) {
            $this->Paginator->settings['Recommend']['conditions']['Recommend.client_id'] = $params['client_id'];
            $this->request->data['Recommend']['client_id'] = $params['client_id'];
        }

        // 開始日
        if (!empty($params['apply_term_from'])) {
            $applyTermFrom = '';
            if (! empty($params['apply_term_from']['year'])) {
                $applyTermFrom = $params['apply_term_from']['year'];
                if (! empty($params['apply_term_from']['month'])) {
                    $applyTermFrom .= '-' . $params['apply_term_from']['month'];
                    if (! empty($params['apply_term_from']['day'])) {
                        $applyTermFrom .= '-' . $params['apply_term_from']['day'];
                    }
                }
                $this->Paginator->settings['Recommend']['conditions']['Recommend.apply_term_from >= '] = $applyTermFrom;
                $this->request->data['Recommend']['apply_term_from'] = $params['apply_term_from'];
            }
        }

        // 終了日
        if (!empty($params['apply_term_to'])) {
            $applyTermTo = '';
            if (! empty($params['apply_term_to']['year'])) {
                $applyTermTo = $params['apply_term_to']['year'];
                if (! empty($params['apply_term_to']['month'])) {
                    $applyTermTo .= '-' . $params['apply_term_to']['month'];
                    if (! empty($params['apply_term_to']['day'])) {
                        // 登録時に「23:59:59」を付与されるため検索条件にも追加
                        $applyTermTo .= '-' . $params['apply_term_to']['day'] . ' 23:59:59';
                    }
                }
                $this->Paginator->settings['Recommend']['conditions']['Recommend.apply_term_to <= '] = $applyTermTo;
                $this->request->data['Recommend']['apply_term_to'] = $params['apply_term_to'];
            }
        }

        // 対象日
        if (!empty($params['select_date'])) {
            $selectDate = '';
            if (! empty($params['select_date']['year'])) {
                $selectDate = $params['select_date']['year'];
                if (! empty($params['select_date']['month'])) {
                    $selectDate .= '-' . $params['select_date']['month'];
                    if (! empty($params['select_date']['day'])) {
                        $selectDate .= '-' . $params['select_date']['day'];
                    }
                }
                $this->Paginator->settings['Recommend']['conditions']['Recommend.apply_term_from <= '] = $selectDate;
                $this->Paginator->settings['Recommend']['conditions']['Recommend.apply_term_to >= '] = $selectDate;
                $this->request->data['Recommend']['select_date'] = $params['select_date'];
            }
        }

        if ($params['prefecture_id'] != '') {
            $this->Paginator->settings['Recommend']['conditions']['RecommendPrefecture.prefecture_id'] = $params['prefecture_id'];
            $this->request->data['Recommend']['prefecture_id'] = $params['prefecture_id'];
        }
        
        if ($params['is_published'] != '') {
            $this->Paginator->settings['Recommend']['conditions']['Recommend.is_published'] = $params['is_published'];
            $this->request->data['Recommend']['is_published'] = $params['is_published'];
        }

        $recommends = $this->Paginator->paginate('Recommend');

        $recommend_ids = Hash::extract($recommends, '{n}.Recommend.id');
        $recommendPrefectures = $this->RecommendPrefecture->find('all', [
            'conditions' => [
                'RecommendPrefecture.recommend_id' => $recommend_ids,
                'Prefecture.delete_flg' => 0,
                'RecommendPrefecture.delete_flg' => 0
            ],
            'joins' => [
                [
                    'type' => "LEFT",
                    'alias' => "Prefecture",
                    'table' => "prefectures",
                    'conditions' => "Prefecture.id = RecommendPrefecture.prefecture_id"
                ]
            ],
            'order' => ['Prefecture.sort'],
            'recursive' => -1,
        ]);
        $recommendPrefectures = Hash::combine($recommendPrefectures, '{n}.RecommendPrefecture.id', '{n}', '{n}.RecommendPrefecture.recommend_id');
        foreach ($recommends as $key => $recommend) {
            $recommends[$key]['RecommendPrefecture'] = $recommendPrefectures[$recommend['Recommend']['id']];
        }
        
        $this->set('recommends', $recommends);
    }

    /**
     * 新規追加
     *
     * @return void
     */
    public function add()
    {
        if ($this->request->is('post')) {
            $this->Recommend->begin();
            try {
                $this->Recommend->create();
                $this->request->data['Recommend']['staff_id'] = $this->cdata['id'];
                $this->request->data['Recommend']['apply_term_to'] = sprintf(
                    '%s-%s-%s 23:59:59',
                    $this->request->data['Recommend']['apply_term_to']['year'],
                    $this->request->data['Recommend']['apply_term_to']['month'],
                    $this->request->data['Recommend']['apply_term_to']['day']
                );
                if ($this->Recommend->save($this->request->data)) {
                    // 固定地域とランダム地域を合算して不要な「全て(0)」を削除する
                    $fixedPrefectures = (!empty($this->request->data['Recommend']['prefectures'])) ? $this->request->data['Recommend']['prefectures'] : array();
                    $randomPrefectures = (!empty($this->request->data['Recommend']['randomPrefectures'])) ? $this->request->data['Recommend']['randomPrefectures'] : array();
                    $mergePrefectures = array_merge($fixedPrefectures, $randomPrefectures);
                    $deleteTarget = array(0);
                    $prefectures = array_diff($mergePrefectures, $deleteTarget);
                    foreach ($prefectures as $val) {
                        $this->RecommendPrefecture->create();
                        $prefecturesSaveData = [];
                        $prefecturesSaveData['RecommendPrefecture']['recommend_id'] = $this->Recommend->getLastInsertId();
                        $prefecturesSaveData['RecommendPrefecture']['prefecture_id'] = $val;
                        $prefecturesSaveData['RecommendPrefecture']['staff_id'] = $this->cdata['id'];

                        if (!$this->RecommendPrefecture->save($prefecturesSaveData)) {
                            throw new Exception(__('failed to save recommend_prefecture'));
                        }
                    }
                    $this->Recommend->commit();
                    $this->Session->setFlash('レコメンドを登録しました', 'default', array('class' => 'alert alert-success'));
                    $this->redirectReferer();
                } else {
                    throw new Exception(__('failed to save recommend'));
                }
            } catch (Exception $e) {
                $this->Recommend->rollback();
                $this->Session->setFlash('レコメンドの登録に失敗しました', 'default', array('class' => 'alert alert-error'));
            }
        }
    }

    /**
     * 編集ページ
     *
     * @param string $id
     * @return void
     */
    public function edit($id = null)
    {
        if (empty($id)) {
            throw new Exception(__('Invalid Recommend'));
        }
        $prefectures = [];
        $this->request->data['Recommend']['id'] = $id;

        if ($this->request->is('post') || $this->request->is('put')) {
            $this->Recommend->begin();
            try {
                $this->Recommend->create();
                $this->request->data['Recommend']['staff_id'] = $this->cdata['id'];
                $this->request->data['Recommend']['apply_term_to'] = sprintf(
                    '%s-%s-%s 23:59:59',
                    $this->request->data['Recommend']['apply_term_to']['year'],
                    $this->request->data['Recommend']['apply_term_to']['month'],
                    $this->request->data['Recommend']['apply_term_to']['day']
                );
                // エラー時にPOSTしたデータは補完してくれるけど念の為セットしておく
                $fixedPrefectures = (!empty($this->request->data['Recommend']['prefectures'])) ? $this->request->data['Recommend']['prefectures'] : array();
                $randomPrefectures = (!empty($this->request->data['Recommend']['randomPrefectures'])) ? $this->request->data['Recommend']['randomPrefectures'] : array();
		// disabledで送信されないパラメータの「0」対策
		if (empty($this->request->data['Recommend']['recommend_fee'])) {
			$this->request->data['Recommend']['recommend_fee'] = 0;
		}
		if (empty($this->request->data['Recommend']['recommend_fee_unit'])) {
			$this->request->data['Recommend']['recommend_fee_unit'] = 0;
		}
		if (empty($this->request->data['Recommend']['is_internal_tax'])) {
			$this->request->data['Recommend']['is_internal_tax'] = 0;
		}
		if (empty($this->request->data['Recommend']['settlement_timing'])) {
			$this->request->data['Recommend']['settlement_timing'] = 0;
		}

                if ($this->Recommend->save($this->request->data)) {
                    // 固定地域とランダム地域を合算して不要な「全て(0)」を削除する
                    $mergePrefectures = array_merge($fixedPrefectures, $randomPrefectures);
                    $deleteTarget = array(0);
                    $prefectures = array_diff($mergePrefectures, $deleteTarget);
                    $savedPrefectures = $this->RecommendPrefecture->find('all', ['conditions' => ['recommend_id' => $this->request->data['Recommend']['id']]]);

                    $savedPrefectureIds = Hash::extract($savedPrefectures, '{n}.RecommendPrefecture.prefecture_id');

                    // すでに登録されているデータを整理する
                    foreach ($savedPrefectures as $val) {
                        if (in_array($val['RecommendPrefecture']['prefecture_id'], $prefectures)) {
                            if ($val['RecommendPrefecture']['delete_flg'] == 0) {
                                continue;
                            }
                            $val['RecommendPrefecture']['delete_flg'] = 0;
                            $val['RecommendPrefecture']['deleted'] = null;
                            if (!$this->RecommendPrefecture->save($val)) {
                                throw new Exception(__('failed to save recommend_prefecture'));
                            }
                        } else {
                            if ($val['RecommendPrefecture']['delete_flg'] == 1) {
                                continue;
                            }
                            $val['RecommendPrefecture']['delete_flg'] = 1;
                            $val['RecommendPrefecture']['deleted'] = date("Y-m-d H:i:s", time());
                            if (!$this->RecommendPrefecture->save($val)) {
                                throw new Exception(__('failed to save recommend_prefecture'));
                            }
                        }
                    }
                    
                    foreach ($prefectures as $val1) {
                        if (in_array($val1, $savedPrefectureIds)) {
                            continue;
                        }
                        $this->RecommendPrefecture->create();

                        $prefecturesSaveData = [];
                        $prefecturesSaveData['RecommendPrefecture']['recommend_id'] = $id;
                        $prefecturesSaveData['RecommendPrefecture']['prefecture_id'] = $val1;
                        $prefecturesSaveData['RecommendPrefecture']['staff_id'] = $this->cdata['id'];

                        if (!$this->RecommendPrefecture->save($prefecturesSaveData)) {
                            throw new Exception(__('failed to save recommend_prefecture'));
                        }
                    }
                    $this->Recommend->commit();
                    $this->Session->setFlash('レコメンドを登録しました', 'default', array('class' => 'alert alert-success'));
                    $this->redirectReferer();
                } else {
                    throw new Exception(__('failed to save recommend'));
                }
            } catch (Exception $e) {
                $this->Recommend->rollback();
                $this->Session->setFlash('レコメンドの登録に失敗しました', 'default', array('class' => 'alert alert-error'));
            }
        } else {
            $this->request->data = $this->Recommend->read(null, $id);

            $prefectureData = Hash::combine($this->request->data['RecommendPrefecture'], '{n}.prefecture_id', '{n}.prefecture_id');

            $fixedPrefectureList = $this->Prefecture->getRecommendPrefectureList();
            $randomPrefectureList = $this->Prefecture->getRecommendRandomPrefectureList();
            $inputPrefecture = array_intersect_key($prefectureData, $fixedPrefectureList);
            $inputRandomPrefecture = array_intersect_key($prefectureData, $randomPrefectureList);
            if (count($inputPrefecture) == count($fixedPrefectureList)) {
                $inputPrefecture[0] = '0';
            }
            if (count($inputRandomPrefecture) == count($randomPrefectureList)) {
                $inputRandomPrefecture[0] = '0';
            }


            $messageBoards = $this->MessageBoard->find('all', [
                'conditions' => [
                    'MessageBoard.reservation_id' => $id,
                    'MessageBoard.category_cd' => 'RECOMMEND_DETAIL', // カテゴリコードには画面名を入れる
                    'MessageBoard.delete_flg' => 0
                ]
            ]);
            $this->set('MessageBoards', $messageBoards);

            // referer持ち回し対応
            if (empty($this->request->data('Custom.referer')) && !empty($this->request->query('Custom.referer'))) {
                $this->request->data['Custom']['referer'] = $this->request->query('Custom.referer');
            }
            $fixedPrefectures = $inputPrefecture;
            $randomPrefectures = $inputRandomPrefecture;
        }
        $this->set(compact('fixedPrefectures', 'randomPrefectures'));
        
    }

    /**
     * 削除メソッド
     * 論理削除
     *
     * @param string $id
     * @return void
     */
    public function delete($id = null)
    {
        if (empty($id)) {
            throw new Exception(__('Invalid Recommend'));
        }

        $this->Recommend->validate = false;
        $saveData = $this->Recommend->find('first', ['conditions' => ['Recommend.id' => $id]]);

        $saveData['Recommend']['id'] = $id;
        $saveData['Recommend']['staff_id'] = $this->cdata['id'];
        $saveData['Recommend']['delete_flg'] = 1;
        $saveData['Recommend']['deleted'] = date("Y-m-d H:i:s", time());

        try {
            $this->Recommend->begin();
            if ($this->Recommend->save($saveData)) {
                $savedPrefectures = $this->RecommendPrefecture->find('all', ['conditions' => ['recommend_id' => $id]]);

                $this->RecommendPrefecture->create();
                foreach ($savedPrefectures as $val) {
                    if ($val['RecommendPrefecture']['delete_flg'] == 1) {
                        continue;
                    }
                    $val['RecommendPrefecture']['delete_flg'] = 1;
                    $val['RecommendPrefecture']['deleted'] = date("Y-m-d H:i:s", time());
                    
                    if (!$this->RecommendPrefecture->save($val)) {
                        throw new Exception(__('failed to save recommend_prefecture'));
                    }
                }
                $this->Recommend->commit();
                $this->Session->setFlash('削除しました', 'default', array('class' => 'alert alert-success'));
                $this->redirect($this->referer());
            } else {
                throw new Exception(__('failed to save recommend'));
            }
        } catch (Exception $e) {
            $this->Recommend->rollback();
            $this->Session->setFlash('削除に失敗しました', 'default', array('class' => 'alert alert-error'));
        }

        $this->redirect($this->referer());
    }

    /**
     * 対応履歴保存
     *
     * @return json
     */
    public function saveResponseHistory()
    {
        $this->autoRender = false;
        if (!$this->request->isAjax()) {
            throw new NotFoundException();
        }
        if (strlen($this->request->data['message']) > 65535) {
            return json_encode(array(
                'ret' => 'error',
                'message' => '入力内容が長すぎます。半角で65535文字、全角で3万文字程度以下にしてください。'
            ));
        }
        try {
            $this->MessageBoard->save(array(
                'reservation_id' => $this->request->data['recommend_id'],
                'category_cd' => 'RECOMMEND_DETAIL',
                'message' => $this->request->data['message'],
                'staff_id' => $this->cdata['id']
            ));
            return json_encode(array('ret' => 'ok'));
        } catch (\Exception $e) {
            return json_encode(array(
                'ret' => 'error',
                'message' => $e->getMessage()
            ));
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
