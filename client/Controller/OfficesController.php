<?php
App::uses('AppController', 'Controller');
App::import('Vendor', 'imageResizeUpLoad');
/**
 * Offices Controller
 *
 * @property Office $Office
 * @property OfficeBusinessHour $OfficeBusinessHour
 */
class OfficesController extends AppController
{

    public $uses = array(
            'Office',
            'Landmark',
            'StockGroup',
            'OfficeStockGroup',
            'Area',
            'DropOffArea',
            'LateNightFee',
            'LandmarkCategory',
            'Prefecture',
            'OfficeBusinessHour',
            'OfficeSelectionPermission',
            'OfficeStation',
            'City',
            'OfficeSupplement'
    );

    /**
     * 前処理
     *
     * @return void
     */
    public function beforeFilter()
    {
        parent::beforeFilter();

        // 画像リサイズアップロード
        $this->ImageResize = new ImageResizeUpLoad();

        if (array_keys(array('edit', 'delete'), $this->action)) {
            //編集・削除対象IDが存在するかチェック
            if (!empty($this->passedArgs[0])) {
                /**
                 * 編集・削除対象IDとクライアントIDで検索
                 * データが存在しない場合一覧へリダイレクト
                 */
                if (!$this->Office->clientCheck($this->passedArgs[0], $this->clientData['Client']['id'])) {
                    $this->Session->setFlash('不正なアクセスです。', 'default', array( 'class' => 'alert alert-error'));
                    $this->redirect($this->getRedirectUrlForList());
                }
            }
        }

        // ログインユーザーの管理者権限フラグ
        $clientData = $this->Auth->user();
        $this->set('is_system_admin', $clientData['is_system_admin']);
        $this->set('is_check_user', true);
    }

    /**
     * 一覧画面処理
     *
     * @return void
     */
    public function index()
    {

        $this->Session->delete('clientReferer');
        /**
         * 並び順を保存するを押されたとき
         */
        if ($this->request->is('post')) {
            if (!empty($this->request->data['Office']['order'])) {
                //営業所リスト取得
                if (!empty($this->request->query['prefecture_id'])) {
                    $officeToPrefecture = $this->Office->getOfficeToPrefecture($this->clientData['Client']['id'], $this->request->query['prefecture_id']);
                } else {
                    $officeToPrefecture = $this->Office->getOfficeToPrefecture($this->clientData['Client']['id']);
                }

                $order = $this->request->data['Office']['order'];
                $orderArray = explode(',', $order);
                $saveData = array();
                $counters = array();
                for ($i = 1; $i <= 47; $i++) {
                    $counters[$i] = $i * 1000 + 1;
                }
                foreach ($orderArray as $val) {
                    if (empty($officeToPrefecture[$val])) {
                        continue;
                    }

                    $saveData[] = array(
                        'id' => $val,
                        'client_id' => $this->clientData['Client']['id'],
                        'sort' => $counters[$officeToPrefecture[$val]],
                    );
                    $counters[$officeToPrefecture[$val]]++;
                }

                if ($this->Office->saveAll($saveData)) {
                    $this->Session->setFlash('並び順を保存しました。', 'default', array( 'class' => 'alert alert-success'));
                    $this->redirect($this->referer());
                } else {
                    $this->Session->setFlash('エラー:並び順の保存に失敗しました。', 'default', array('class'=> 'alert alert-error'));
                }
            }
        } else {
            if (!empty($this->request->query['prefecture_id'])) {
                $this->request->data['Office']['prefecture_id'] = $prefectureId = $this->request->query['prefecture_id'];
            }
        }

        //営業所一覧を取得
        $query = array(
            'conditions'=>array(
                'Office.client_id'=>$this->clientData['client_id'],
                'Office.delete_flg'=>0,
                //'Area.prefecture_id'=>$prefectureId
            ),
            'joins'=>array(
                array(
                    'type'=>'LEFT',
                    'alias'=>'OfficeStockGroup',
                    'table'=>'office_stock_groups',
                    'conditions'=>'OfficeStockGroup.office_id = Office.id'
                ),
                array(
                    'type'=>'LEFT',
                    'alias'=>'Area',
                    'table'=>'areas',
                    'conditions'=>'Area.id = Office.area_id'
                ),
            ),
            'limit'=>100,
            'fields'=>array('Office.*,OfficeStockGroup.*'),
            'order'=>array('Office.sort', 'Office.id'),
            'recursive'=>-1
        );

        if (!empty($prefectureId)) {
            $query['conditions'] += array('Area.prefecture_id'=>$prefectureId);
            $query['limit'] = 1000;
            $query['maxLimit'] = 1000;
        }

        $this->paginate = $query;
        $tempOffices = $this->paginate();
        $offices = array();
        foreach ($tempOffices as $office) {
            $office['Office']['businessHours'] = $this->formatOfficeBusinessHours($office['Office']);
            $offices[] = $office;
        }
        $this->set('offices', $offices);

        //在庫管理地域リストを取得
        $stockGroupList = $this->StockGroup->getStockGroupList($this->clientData['client_id']);
        $this->set('stockGroupList', $stockGroupList);

        $officeToPrefecture = $this->Office->getOfficeToPrefecture($this->clientData['Client']['id']);
        $existPrefectureIds = array_values($officeToPrefecture);
        $prefectureList = $this->Prefecture->getPrefectureList();
        foreach ($prefectureList as $k => $v) {
            if (!in_array($k, $existPrefectureIds)) {
                unset($prefectureList[$k]);
            }
        }
        $this->set('prefectureList', $prefectureList);
    }

    /**
     * 営業所情報整形
     *
     * @param array $office
     * @return string
     */
    private function formatOfficeBusinessHours($office)
    {
        if (empty($office)) {
            return '';
        }

        $businessHours = array();
        $businessHours['mon'] = array('kanji' => '月', 'from' => $office['mon_hours_from'], 'to' => $office['mon_hours_to']);
        $businessHours['tue'] = array('kanji' => '火', 'from' => $office['tue_hours_from'], 'to' => $office['tue_hours_to']);
        $businessHours['wed'] = array('kanji' => '水', 'from' => $office['wed_hours_from'], 'to' => $office['wed_hours_to']);
        $businessHours['thu'] = array('kanji' => '木', 'from' => $office['thu_hours_from'], 'to' => $office['thu_hours_to']);
        $businessHours['fri'] = array('kanji' => '金', 'from' => $office['fri_hours_from'], 'to' => $office['fri_hours_to']);
        $businessHours['sat'] = array('kanji' => '土', 'from' => $office['sat_hours_from'], 'to' => $office['sat_hours_to']);
        $businessHours['sun'] = array('kanji' => '日', 'from' => $office['sun_hours_from'], 'to' => $office['sun_hours_to']);
        $businessHours['hol'] = array('kanji' => '祝日', 'from' => $office['hol_hours_from'], 'to' => $office['hol_hours_to']);

        $sameDayBusinessHours = array();
        foreach ($businessHours as $day => $businessHour) {
            $key = $businessHour['from'].'-'.$businessHour['to'];

            if (array_key_exists($key, $sameDayBusinessHours)) {
                $sameDayBusinessHours[$key]['days'] = $sameDayBusinessHours[$key]['days'].$businessHour['kanji'];
            } else {
                $sameDayBusinessHours[$key] = array('days' => $businessHour['kanji'],
                                                    'from' => $businessHour['from'],
                                                    'to' => $businessHour['to'],
                                            );
            }
        }
    
        $str = '';
        $i = 0;

        if (count($sameDayBusinessHours) == 1) {
            $v = array_pop($sameDayBusinessHours);
            $str = '毎日 '. date('H時i分', strtotime($v['from'])) . ' ～ ' . date('H時i分', strtotime($v['to']));
        } else {
            foreach ($sameDayBusinessHours as $v) {
                if (empty($v['from']) || empty($v['to'])) {
                      continue;
                }
                if ($i > 0) {
                    $str .= '<br>';
                }
                $str .= $v['days'].' '. date('H時i分', strtotime($v['from'])) . ' ～ ' . date('H時i分', strtotime($v['to']));
                $i++;
            }
        }

        return $str;
    }

    /**
     * save OfficeStations
     * 営業所の駅を保存
     *
     * @param string $officeId
     * @param string $stationIds
     * @param boolean $editMethod
     * @return void
     */
    private function saveOfficeStations($officeId, $stationIds, $editMethod = false)
    {

        $newStationIds = array();
        if (!empty($stationIds)) {
            $newStationIds = explode(',', $stationIds);
        }

        //編集メソッド
        if ($editMethod) {
            $officeStations = array();
            $registredOfficeStations = array();
            //既存のデータと比較
            $fields = array('OfficeStation.id','OfficeStation.station_id','OfficeStation.delete_flg');
            $registredOfficeStations = $this->OfficeStation->getStationsByOfficeId($officeId, $fields, null);

            foreach ($registredOfficeStations as $registredOfficeStation) {
                $officeStation = array();
                $officeStation['OfficeStation']['id'] = $registredOfficeStation['OfficeStation']['id'];
                $officeStation['OfficeStation']['staff_id'] = $this->clientData['id'];

                if (in_array($registredOfficeStation['OfficeStation']['station_id'], $newStationIds)) {
                    //削除フラグがつけていたものを回復する
                    if ($registredOfficeStation['OfficeStation']['delete_flg'] == 1) {
                        $officeStation['OfficeStation']['delete_flg'] = 0;
                        $officeStations[] = $officeStation;
                    }
                    //既存のレコードですから、新規対象外
                    $key = array_search(
                        $registredOfficeStation['OfficeStation']['station_id'],
                        $newStationIds
                    );
                    unset($newStationIds[$key]);
                } else {
                    //削除フラグ対象の場合
                    if ($registredOfficeStation['OfficeStation']['delete_flg'] == 0) {
                        $officeStation['OfficeStation']['delete_flg'] = 1;
                        $officeStation['OfficeStation']['deleted'] = date('Y/m/d H:i:s');
                        $officeStations[] = $officeStation;
                    }
                }
            }
        }

        //新規レコード
        foreach ($newStationIds as $stationId) {
            if (empty($stationId)) {
                continue;
            }
            $officeStation = array();
            $officeStation['OfficeStation']['id'] = null;
            $officeStation['OfficeStation']['office_id'] = $officeId;
            $officeStation['OfficeStation']['station_id'] = $stationId;
            $officeStation['OfficeStation']['delete_flg'] = 0;
            $officeStation['OfficeStation']['client_id'] = $this->clientData['client_id'];
            $officeStation['OfficeStation']['staff_id'] = $this->clientData['id'];
            $officeStations[] = $officeStation;
        }

        if (!empty($officeStations)) {
            $this->OfficeStation->saveAll($officeStations);
        }
    }

    /**
     * 追加画面処理
     *
     * @return void
     */
    public function add()
    {
        $referer = $this->referer();
        if (preg_match('/'.preg_quote('client/Offices', '/').'/', $referer) &&
            !preg_match('/'.preg_quote('client/Offices/add', '/').'/', $referer)) {
            $this->Session->write('clientReferer', $referer);
        } else {
            $this->Session->write('clientReferer', '/Offices');
        }

        if ($this->request->is('post')) {
            $data = $this->request->data;

            // PDFファイルのアップロード
            if (!empty($data['Office']['hotel_pdf']['tmp_name'])) {
                $file = $this->request->data['Office']['hotel_pdf'];
                $data['Office']['hotel_pdf'] = $this->Office->saveOfficePdf($file);
            } else {
                $data['Office']['hotel_pdf'] = false;
            }

            $data['Office']['client_id'] = $this->clientData['client_id'];
            $data['Office']['staff_id'] = $this->clientData['id'];
            if ($data['Office']['delete_flg'] == 1) {
                $data['Office']['deleted'] = date("Y-m-d H:i:s", time());
            }

            // ソート順のデフォルトは都道府県ID×1,000
            $prefecture = $this->Area->getPrefectureIdByAreaId($data['Office']['area_id']);
            $data['Office']['sort'] = $prefecture['Prefecture']['id'] * 1000;

            $this->Office->begin();
            $saveFlg = true;
            $this->Office->create();
            if ($this->Office->save($data['Office'])) {
                $lastID = $this->Office->getLastInsertID();
                $data['OfficeStockGroup']['client_id'] = $this->clientData['client_id'];
                $data['OfficeStockGroup']['office_id'] = $lastID;
                $data['OfficeStockGroup']['staff_id'] = $this->clientData['id'];

                $data['OfficeSupplement']['office_id'] = $lastID;
                $data['OfficeSupplement']['staff_id'] = $this->clientData['id'];
                $data['OfficeSupplement']['delete_flg'] = $data['Office']['delete_flg'];
                if ($data['Office']['delete_flg'] == 1) {
                    $data['OfficeSupplement']['deleted'] = date("Y-m-d H:i:s", time());
                }

                if (!$this->OfficeSupplement->save($data['OfficeSupplement'])) {
                    $this->Session->setFlash('入力に失敗しました、各項目を見直して下さい。', 'default', array('class'=>'alert alert-error'));
                    $saveFlg = false;
                }

                // 画像のアップロード
                if (!empty($data['Office']['file']['tmp_name'])) {
                    $upLoadDir = 'office'.DS.$lastID;
                    $file = $data['Office']['file'];
                    $imageData['Office']['id'] = $lastID;
                    $imageData['Office']['image_relative_url'] =$this->ImageResize->resizeUpLoad($file, $upLoadDir, $lastID);
                    if (!$this->Office->save($imageData['Office'])) {
                        $this->Session->setFlash('画像をアップロードできませんでした。', 'default', array('class'=>'alert alert-error'));
                        $saveFlg = false;
                    }
                }

                if (!empty($data['OfficeStockGroup']['stock_group_id']) && $this->OfficeStockGroup->save($data['OfficeStockGroup'])) {
                    $this->saveOfficeStations(
                        $lastID,
                        $data['Office']['station_ids'],
                        false
                    );

                    $this->Session->setFlash('登録が正しく完了しました。', 'default', array('class'=>'alert alert-info'));
                } else {
                    $this->Session->setFlash('入力に失敗しました、営業所在庫管理地域は必須です。', 'default', array('class'=>'alert alert-error'));
                    $saveFlg = false;
                }
            } else {
                $this->Session->setFlash('入力に失敗しました、各項目を見直して下さい。', 'default', array('class'=>'alert alert-error'));
                $saveFlg = false;
            }

            if ($saveFlg) {
                $this->Office->commit();

                if ($this->clientData['is_system_admin'] != 1) {
                    $lastId = $this->Office->getLastInsertID();
                    $saveData['staff_id'] = $this->clientData['id'];
                    $saveData['office_id'] = $lastId;
                    $this->OfficeSelectionPermission->save($saveData);
                }

                $this->redirect($this->Session->read('clientReferer'));
            } else {
                $this->Office->rollback();
            }
        }

        //在庫管理地域リストを取得
        $stocks = $this->StockGroup->getStockGroupListWithUnassociated($this->clientData['client_id'], '', $this->clientData['id']);

        //エリアリストを取得
        $area = $this->Area->getPrefectureAreaList();

        //乗捨エリアリストを取得
        $dropOffAreaList = $this->DropOffArea->getDropOffAreaList($this->clientData['client_id']);

        //深夜手数料リストを取得
        $lateNightFeeList = $this->LateNightFee->getLateNightFeeList($this->clientData['client_id']);

        //空港と港のリストを取得
        $landmarkList = $this->Landmark->getAllLandmarks();

        // 正しい入力を促すため、初期値設定しない
        //最寄り交通機関
        //$this->set('nearestTransport', 0);
        //交通手段
        //$this->set('methodOfTransport', 0);

        //送迎方法
        $this->set('pickupMethod', 0);
        //送迎電話
        $this->set('needPickupCall', 1);

        $this->set('stocks', $stocks);
        $this->set('area', $area);
        $this->set('dropOffAreaList', $dropOffAreaList);
        $this->set('lateNightFeeList', $lateNightFeeList);
        $this->set('landmarkList', $landmarkList);

        $this->__setViewVars();
    }

    /**
     * 編集画面処理
     *
     * @throws NotFoundException
     * @param string $id
     * @return void
     */
    public function edit($id = null)
    {
        $referer = $this->referer();
        if (preg_match('/'.preg_quote('client/Offices', '/').'/', $referer) &&
            !preg_match('/'.preg_quote('client/Offices/edit', '/').'/', $referer)) {
            $this->Session->write('clientReferer', $referer);
        } else {
            $this->Session->write('clientReferer', '/Offices');
        }

        $this->__setViewVars();

        if (!$this->Office->exists($id)) {
            throw new NotFoundException(__('Invalid office'));
        }

        $officeStations = array();

        if ($this->request->is('post') || $this->request->is('put')) {
            $data = $this->request->data;

            //画像がアップされていたら
            if (!empty($data['Office']['file']['tmp_name'])) {
                $file = $data['Office']['file'];
                $upLoadDir = 'office'.DS.$id;
                $data['Office']['image_relative_url'] = $this->ImageResize->resizeUpLoad($file, $upLoadDir, $id);
            }

            $data['Office']['client_id'] =  $this->clientData['client_id'];
            $data['Office']['staff_id'] = $this->clientData['id'];
            if ($data['Office']['delete_flg'] == 1) {
                $data['Office']['deleted'] = date("Y-m-d H:i:s", time());
            }

            // 営業所補足
            $data['OfficeSupplement']['office_id'] = $data['Office']['id'];
            $data['OfficeSupplement']['staff_id'] = $this->clientData['id'];
            $data['OfficeSupplement']['delete_flg'] = $data['Office']['delete_flg'];
            if ($data['Office']['delete_flg'] == 1) {
                $data['OfficeSupplement']['deleted'] = date("Y-m-d H:i:s", time());
            }

            $this->Office->begin();
            $saveFlg = true;

            if ($this->Office->save($data['Office']) && $this->OfficeSupplement->save($data['OfficeSupplement'])) {
                $data['OfficeStockGroup']['client_id'] =  $this->clientData['client_id'];
                $data['OfficeStockGroup']['staff_id'] = $this->clientData['id'];

                if (!empty($data['OfficeStockGroup']['stock_group_id']) && $this->OfficeStockGroup->save($data['OfficeStockGroup'])) {
                    $this->saveOfficeStations(
                        $data['Office']['id'],
                        $data['Office']['station_ids'],
                        true
                    );

                    $this->Session->setFlash('編集が正しく完了しました。', 'default', array('class'=>'alert alert-info'));
                } else {
                    $this->Session->setFlash('入力に失敗しました、各項目を見直して下さい。<br>営業所在庫管理地域は必須です。', 'default', array('class'=>'alert alert-error'));
                    $saveFlg = false;
                }
            } else {
                $this->Session->setFlash('入力に失敗しました、各項目を見直して下さい。', 'default', array('class'=>'alert alert-error'));
                $saveFlg = false;
            }

            if ($saveFlg) {
                $this->Office->commit();
                $this->redirect($this->getRedirectUrlForList());
            } else {
                $this->Office->rollback();
            }
        } else {
            //既存の駅を取得
            $fields = array('OfficeStation.id','Station.id','Station.name');
            $records = $this->OfficeStation->getStationsByOfficeId($id, $fields);
            foreach ($records as $record) {
                $station = array('id' => $record['Station']['id'],
                                 'name' => $record['Station']['name']);
                $officeStations[] = $station;
            }

            $conditions = array('Office.id' => $id,
                                'Office.client_id'=> $this->clientData['client_id']);
            $options = compact('conditions');
            $this->Office->recursive = 1;
            $officeData = $this->Office->find('first', $options);
            if (!empty($officeData)) {
                $this->request->data = $officeData;
            } else {
                $this->redirect($this->referer());
            }

            //最寄り交通機関
            $nearestTransport = 0;
            if (!empty($officeData['OfficeSupplement'][0]['nearest_transport'])) {
                $nearestTransport = $officeData['OfficeSupplement'][0]['nearest_transport'];
            }
            $this->set('nearestTransport', $nearestTransport);
            //交通手段
            $methodOfTransport = 0;
            if (!empty($officeData['OfficeSupplement'][0]['method_of_transport'])) {
                $methodOfTransport = $officeData['OfficeSupplement'][0]['method_of_transport'];
            }
            $this->set('methodOfTransport', $methodOfTransport);
            //送迎方法
            $pickupMethod = 0;
            if (!empty($officeData['OfficeSupplement'][0]['pickup_method'])) {
                $pickupMethod = $officeData['OfficeSupplement'][0]['pickup_method'];
            }
            $this->set('pickupMethod', $pickupMethod);
            //電話連絡
            $needPickupCall = 1;
            if (isset($officeData['OfficeSupplement'][0]['need_pickup_call'])) {
                $needPickupCall = $officeData['OfficeSupplement'][0]['need_pickup_call'];
            }
            $this->set('needPickupCall', $needPickupCall);
        }

        //在庫管理地域リストを取得
        $stocks = $this->StockGroup->getStockGroupListWithUnassociated($this->clientData['client_id'], '', $this->clientData['id']);

        //エリアリストを取得
        $area = $this->Area->getPrefectureAreaList();

        //市区町村リストを取得
        $cityList = $this->City->find(
            'list',
            array(
                'joins' => array(
                    array(
                        'type' => 'INNER',
                        'alias' => 'Zipcode',
                        'table' => 'zipcodes',
                        'conditions' => array(
                            'Zipcode.zipcode' => $this->request->data['Office']['zipcode'],
                            'Zipcode.city_id = City.id',
                            'Zipcode.delete_flg' => 0
                        )
                    )
                ),
                'conditions' => array('City.delete_flg' => 0)
            )
        );
        if (empty($this->request->data['Office']['city_id'])) {
            $cityList = array(0 => '') + $cityList;
        }

        //乗捨エリアリストを取得
        $dropOffAreaList = $this->DropOffArea->getDropOffAreaList($this->clientData['client_id']);

        //深夜手数料リストを取得
        $lateNightFeeList = $this->LateNightFee->getLateNightFeeList($this->clientData['client_id']);

        //空港と港のリストを取得
        $landmarkList = $this->Landmark->getAllLandmarks();

        $officeStocks = $this->OfficeStockGroup->find(
            'first',
            array(
                'conditions'=>array(
                    'OfficeStockGroup.client_id'=>$this->clientData['client_id'],
                    'OfficeStockGroup.office_id'=>$id
                )
            )
        );

        $this->set('stocks', $stocks);
        $this->set('area', $area);
        $this->set('cityList', $cityList);
        $this->set('dropOffAreaList', $dropOffAreaList);
        $this->set('lateNightFeeList', $lateNightFeeList);
        $this->set('landmarkList', $landmarkList);
        $this->set('officeStocks', $officeStocks);
        $this->set('officeStations', $officeStations);
    }

    /**
     * 削除処理
     *
     * @throws NotFoundException
     * @param string $id
     * @return void
     */
    public function delete($id = null)
    {
        $this->Office->id = $id;
        if (!$this->Office->exists()) {
            throw new NotFoundException(__('Invalid office'));
        }
        $this->request->onlyAllow('post', 'delete');
        if ($this->Office->save(array('delete_flg'=>1))) {
            $this->Session->setFlash(__('Office deleted'));
            $this->redirect($this->getRedirectUrlForList());
        }
        $this->Session->setFlash(__('Office was not deleted'));
        $this->redirect($this->getRedirectUrlForList());
    }

    /**
     * 画面表示用変数設定
     *
     * @return void
     */
    private function __setViewVars()
    {

        // 日付fromオプション
        $this->set('datetimeDeleteOptions', array(
                'fieldName' => 'deleted',
                'dateFormat' => 'YMD',
        ));

        // 日付toオプション
        $this->set('datetimeToOptions', array(
                'fieldName' => 'datetimeFrom',
                'dateFormat' => 'YMD',
        ));

        // 最寄り交通機関オプション
        $this->set('nearestTransportOptions', array(
                0 => '空港・港　　',
                1 => '駅　　',
                2 => 'その他　',
        ));

        // 交通手段オプション
        $this->set('methodOfTransportOptions', array(
                0 => '徒歩（送迎なし）　　',
                4 => '徒歩（送迎あり）　　',
                1 => '送迎車（無料）　　',
                2 => '送迎車（有料オプション）　　',
                3 => '車（送迎なし）',
        ));

        // 送迎方法オプション
        $this->set('pickupMethodOptions', array(
                0 => '店舗送迎のみ　　',
                1 => '最寄り交通機関内カウンター　　',
                3 => '最寄り交通機関への配車'
        ));

        // 送迎連絡オプション
        $this->set('pickupCallOptions', array(
                1 => '必要　　',
                0 => '不要'
        ));

        $this->set('redirectUrl', $this->getRedirectUrlForList());
    }

    /**
     * CSV出力処理
     *
     * @return void
     */
    public function csv()
    {

        if ($this->request->is('post') || $this->request->is('put')) {
            $saveFlg = true;

            if ($this->data['Office']['csv']['error'] != UPLOAD_ERR_OK) {
                $this->Session->setFlash('ファイルはアップロードされませんでした。もう一度ファイルを選択してください。 ', 'default', array('class' => 'alert alert-error'));
                $this->redirect(array('action' => 'csv'));
            }

            $insertArray = array();
            $now = date('Y-m-d H:i:s');
            $maxSortData = $this->Office->find('first', array(
                    'conditions' => array(
                            'Office.client_id' => $this->clientData['Client']['id'],
                            'Office.delete_flg' => 0,
                    ),
                    'order' => array(
                            'Office.sort' => 'DESC'
                    ),
                    'recursive' => -1,
            ));
            if (!empty($maxSortData)) {
                $maxSortNum = $maxSortData['Office']['sort'];
            } else {
                $maxSortNum = 0;
            }

            setlocale(LC_ALL, 'ja_JP.UTF-8');
            $fromEncoding = 'ASCII,SJIS,UTF-8,SJIS-win';
            $csvFile = file_get_contents($this->data['Office']['csv']['tmp_name']);
            $csvFile = mb_convert_encoding($csvFile, 'UTF-8', $fromEncoding);
            $csvLines = explode("\n", $csvFile);
            $filterLines = array_filter($csvLines);
            foreach ($filterLines as $key => $line) {
                // 一行目は項目名なのでスルーする
                if ($key === 0) {
                    continue;
                }
                $csv[] = str_getcsv($line);
            }

            $tmp = '';
            $errorMsg = '';
            foreach ($csv as $key => $line) {
/*
 * csv line の各項目
 0: 営業所名（必須）
 1: 電話番号（必須）
 2: 住所（必須）
 3: 交通アクセス（必須）
 4: 送迎や待ち合わせに関する情報（受取）
 5: 送迎や待ち合わせに関する情報（返却）
 6: 月）営業時間_開始（必須）
 7: 月）営業時間_終了（必須）
 8: 火）営業時間_開始（必須）
 9: 火）営業時間_終了（必須）
10: 水）営業時間_開始（必須）
11: 水）営業時間_終了（必須）
12: 木）営業時間_開始（必須）
13: 木）営業時間_終了（必須）
14: 金）営業時間_開始（必須）
15: 金）営業時間_終了（必須）
16: 土）営業時間_開始（必須）
17: 土）営業時間_終了（必須）
18: 日）営業時間_開始（必須）
19: 日）営業時間_終了（必須）
20: 祝日）営業時間_開始（必須）
21: 祝日）営業時間_終了（必須）
22: 営業時間＿補足
23: 営業日＿補足
24: 緯度
25: 経度
26: 予約完了通知メールアドレス
 *
 */
                if (empty($tmp[$key])) {
                    $tmp[$key] = '';
                }

                // 営業所名
                if (empty($line[0])) {
                    $tmp[$key] .= '「営業所名」';
                }

                // 電話番号
                if (empty($line[1])) {
                    $tmp[$key] .= '「電話番号」';
                }

                // 住所
                if (empty($line[2])) {
                    $tmp[$key] .= '「住所」';
                }
                // 交通アクセス
                if (empty($line[3])) {
                    $tmp[$key] .= '「交通アクセス」';
                }

                // 各曜日の営業時間
                $day_of_week_array = array(
                    0 => '月',
                    1 => '火',
                    2 => '水',
                    3 => '木',
                    4 => '金',
                    5 => '土',
                    6 => '日',
                    7 => '祝日'
                );
                $openTimeFormat = array();
                $closeTimeFormat = array();
                for ($i=0; $i<=7; $i++) {
                    $openTimeFormat[$i] = '';
                    if (!empty($line[6 + 2 * $i])) {
                        // 開始時間
                        $openTimeFormat[$i] = $this->__checkDateTimeFormat($line[6 + 2 * $i]);
                        if (empty($openTimeFormat[$i])) {
                            $tmp[$key] .= '「'.$day_of_week_array[$i].'）営業時間_開始」';
                        }
                    } else {
                        $tmp[$key] .= '「'.$day_of_week_array[$i].'）営業時間_開始」';
                    }
                    $closeTimeFormat[$i] = '';
                    if (!empty($line[7 + 2 * $i])) {
                        // 終了時間
                        $closeTimeFormat[$i] = $this->__checkDateTimeFormat($line[7 + 2 * $i]);
                        if (empty($closeTimeFormat[$i])) {
                            $tmp[$key] .= '「'.$day_of_week_array[$i].'）営業時間_終了」';
                        }
                    } else {
                        $tmp[$key] .= '「'.$day_of_week_array[$i].'）営業時間_終了」';
                    }
                }

                if (!empty($tmp[$key])) {
                    $saveFlg = false;
                    $errorMsg .= ($key+1).'行目：'.$tmp[$key].'のデータが不正です。<br>';
                }

                if ($saveFlg) {
                    $insertArray[] = array(
                            'id' => '',
                            'client_id' => $this->clientData['Client']['id'],
                            'sort' => ($maxSortNum + $key),
                            'name' => $line[0],
                            'area_id' => 0,
                            'office_hours_from' => $openTimeFormat[0],    // 月曜日のものを入れることとする
                            'office_hours_to' => $closeTimeFormat[0],    // 月曜日のものを入れることとする
                            'mon_hours_from' => $openTimeFormat[0],
                            'mon_hours_to' => $closeTimeFormat[0],
                            'tue_hours_from' => $openTimeFormat[1],
                            'tue_hours_to' => $closeTimeFormat[1],
                            'wed_hours_from' => $openTimeFormat[2],
                            'wed_hours_to' => $closeTimeFormat[2],
                            'thu_hours_from' => $openTimeFormat[3],
                            'thu_hours_to' => $closeTimeFormat[3],
                            'fri_hours_from' => $openTimeFormat[4],
                            'fri_hours_to' => $closeTimeFormat[4],
                            'sat_hours_from' => $openTimeFormat[5],
                            'sat_hours_to' => $closeTimeFormat[5],
                            'sun_hours_from' => $openTimeFormat[6],
                            'sun_hours_to' => $closeTimeFormat[6],
                            'hol_hours_from' => $openTimeFormat[7],
                            'hol_hours_to' => $closeTimeFormat[7],
                            'office_hours_remark' => $line[22],
                            'office_holiday_remark' => $line[23],
                            'tel' => $line[1],
                            'reserve_mail' => $line[26],
                            'address' => $line[2],
                            'access' => $line[3],
                            'rent_meeting_info' => $line[4],
                            'return_meeting_info' => $line[5],
                            'accept_rent' => 1,
                            'accept_return' => 1,
                            'can_pickup' => 0,
                            'pickup_from' => '00:00:00',
                            'pickup_to' => '00:00:00',
                            'latitude' => $line[24],
                            'longitude' => $line[25],
                            'image_relative_url' => '',
                            'hotel_pdf' => '',
                            'is_top' => '',
                            'seo' => '',
                            'travel_time_airport' => 0,
                            'area_drop_off_id' => '',
                            'late_night_fee_flg' => '',
                            'airport_id' => '',
                            'bullet_train_id' => '',
                            'staff_id' => $this->clientData['id'],
                            'created' => $now,
                            'modified' => $now,
                            'delete_flg' => 0,
                            'deleted' => null,
                    );
                }
            }

            if (!empty($errorMsg)) {
                $text = 'CSVファイルの<br>'.$errorMsg.'確認してください。';
            } else {
                $text = '各営業所の<span style="color:red;">「画像」「在庫管理地域」「乗捨対象エリア」</span>などの設定は個別に編集お願いいたします。<span style="color:red;">各営業所の「対応エリア」</span>も必ずご確認ください。';
            }

            $this->Office->begin();
            $this->Office->create();
            if (!empty($saveFlg)) {
                if ($this->Office->saveMany($insertArray)) {
                    $lastIDList = $this->Office->idList;
                    if (!empty($lastIDList)) {
                        $this->OfficeSelectionPermission->saveMany($lastIDList);
                    }

                    $this->Session->setFlash($text, 'default', array('class' => 'alert alert-success'));
                } else {
                    $saveFlg = false;
                }
            }

            if ($saveFlg) {
                $this->Office->commit();
                $this->redirect($this->getRedirectUrlForList());
            } else {
                $this->Session->setFlash($text, 'default', array('class' => 'alert alert-error'));
                $this->Office->rollback();
            }
        }
    }

    /**
     * 時間フォーマット検証
     *
     * @param string $time
     * @return bool
     */
    private function __checkDateTimeFormat($time)
    {

        if (!empty($time) && ctype_digit($time) && strlen($time) == 4) {
            $strSplitTime = str_split($time);
            $checkTime = $strSplitTime[0].$strSplitTime[1].':'.$strSplitTime[2].$strSplitTime[3];
            if ($checkTime === date('H:i', strtotime($checkTime))) {
                $result = $checkTime;
            } else {
                $result = false;
            }
        } else {
            $result = false;
        }

        return $result;
    }

    /**
     * 特別営業時間設定一覧
     *
     * @param string $officeId
     * @return void
     */
    public function special_business_hours($officeId)
    {

        // 共通変数
        $this->commonVariable($officeId);

        if (!$this->Office->exists($officeId)) {
            throw new NotFoundException(__('Invalid office'));
        }

        $options = array(
                'conditions' => array(
                        'Office.id' => $officeId,
                        'Office.client_id' => $this->clientData['client_id'],
                        'Office.delete_flg' => 0,
                ),
                'recursive' => -1,
        );
        // 営業所情報取得
        $officeData = $this->Office->find('first', $options);
        $specialBusinessHours = $this->OfficeBusinessHour->getSpecialBusinessHours($officeId);

        $this->set(compact('officeData', 'specialBusinessHours'));
    }

    /**
     * 特別営業時間新規登録
     *
     * @param string $officeId
     * @return void
     */
    public function add_special_business_hours($officeId)
    {

        // 共通変数
        $this->commonVariable($officeId);

        $options = array(
                'conditions' => array(
                        'Office.id' => $officeId,
                        'Office.client_id' => $this->clientData['client_id'],
                        'Office.delete_flg' => 0,
                ),
                'recursive' => -1,
        );
        // 営業所情報取得
        $officeData = $this->Office->find('first', $options);
        $this->set('officeData', $officeData);

        if ($this->request->is('post')) {
            $datas = $this->data;

            $startDayUnixtime = strtotime($datas['start_day'].' 00:00:00');
            $endDayUnixtime = strtotime($datas['end_day'].' 23:59:59');

            if (time() > $endDayUnixtime) {
                $this->Session->setFlash('期間が過ぎています。', 'default', array('class' => 'alert alert-error'));
                return;
            }

            // 重複チェック
            $check = $this->OfficeBusinessHour->dateDuplicateCheck($officeId, $startDayUnixtime, $endDayUnixtime);
            if (!empty($check)) {
                $this->Session->setFlash('期間の重複があります。', 'default', array('class' => 'alert alert-error'));
            } else {
                $saveData = array();

                $saveData['start_day'] = $datas['start_day'];
                $saveData['end_day'] = $datas['end_day'];
                $saveData['start_day_unixtime'] = strtotime($datas['start_day']);
                $saveData['end_day_unixtime'] = strtotime($datas['end_day']);
                $saveData['client_id'] = $this->clientData['Client']['id'];
                $saveData['staff_id'] = $this->clientData['id'];
                $saveData['office_id'] = $officeId;
                foreach ($datas['week_day'] as $key => $val) {
                    $hoursFrom = $val[$key.'_hours_from'];
                    $hoursTo = $val[$key.'_hours_to'];

                    if (empty($hoursFrom['hour']) || empty($hoursTo['min']) || empty($hoursTo['hour']) || empty($hoursTo['min'])) {
                        $saveData[$key.'_hours_from'] = '';
                        $saveData[$key.'_hours_to'] = '';
                    } else {
                        $saveData[$key.'_hours_from'] = $hoursFrom;
                        $saveData[$key.'_hours_to'] = $hoursTo;
                    }
                }

                $this->OfficeBusinessHour->create();
                if ($this->OfficeBusinessHour->save($saveData)) {
                    $this->Session->setFlash('登録しました。', 'default', array('class' => 'alert alert-success'));
                    $this->redirect('/Offices/special_business_hours/'.$officeId);
                } else {
                    $this->Session->setFlash('登録できませんでした。', 'default', array('class' => 'alert alert-error'));
                }
            }
        }
    }

    /**
     * 特別営業時間編集登録
     *
     * @param string $id
     * @return void
     */
    public function edit_special_business_hours($id)
    {

        $officeBusinessHour = $this->OfficeBusinessHour->getOfficeBusinessHour($id);

        $officeId = $officeBusinessHour['office_id'];

        if (empty($officeBusinessHour)) {
            $this->redirect($this->getRedirectUrlForList());
        }

        // 共通変数
        $this->commonVariable($officeId);

        $options = array(
                'conditions' => array(
                        'Office.id' => $officeId,
                        'Office.client_id' => $this->clientData['client_id'],
                        'Office.delete_flg' => 0,
                ),
                'recursive' => -1,
        );
        // 営業所情報取得
        $officeData = $this->Office->find('first', $options);
        $this->set('officeData', $officeData);

        $this->set(compact('startDay', 'endDay', 'officeBusinessHour'));

        if ($this->request->is('post')) {
            $datas = $this->data;

            $startDayUnixtime = strtotime($datas['start_day'].' 00:00:00');
            $endDayUnixtime = strtotime($datas['end_day'].' 23:59:59');

            // 重複チェック
            $check = $this->OfficeBusinessHour->dateDuplicateCheck($officeId, $startDayUnixtime, $endDayUnixtime, $id);
            if (!empty($check)) {
                $this->Session->setFlash('期間の重複があります。', 'default', array('class' => 'alert alert-error'));
            } else {
                $saveData = array();

                $saveData['id'] = $datas['id'];
                $saveData['start_day'] = $datas['start_day'];
                $saveData['end_day'] = $datas['end_day'];
                $saveData['start_day_unixtime'] = strtotime($datas['start_day']);
                $saveData['end_day_unixtime'] = strtotime($datas['end_day']);
                $saveData['client_id'] = $this->clientData['Client']['id'];
                $saveData['staff_id'] = $this->clientData['id'];
                $saveData['office_id'] = $officeId;
                if (!empty($datas['delete_flg'])) {
                    $saveData['delete_flg'] = $datas['delete_flg'];
                }

                foreach ($datas['week_day'] as $key => $val) {
                    $hoursFrom = $val[$key.'_hours_from'];
                    $hoursTo = $val[$key.'_hours_to'];

                    if (empty($hoursFrom['hour']) || empty($hoursTo['min']) || empty($hoursTo['hour']) || empty($hoursTo['min'])) {
                        $saveData[$key.'_hours_from'] = '';
                        $saveData[$key.'_hours_to'] = '';
                    } else {
                        $saveData[$key.'_hours_from'] = $hoursFrom;
                        $saveData[$key.'_hours_to'] = $hoursTo;
                    }
                }


                $this->OfficeBusinessHour->create();
                if (!empty($saveData['id']) && $this->OfficeBusinessHour->save($saveData)) {
                    $this->Session->setFlash('編集しました。', 'default', array('class' => 'alert alert-success'));
                    $this->redirect('/Offices/special_business_hours/'.$officeId);
                } else {
                    $this->Session->setFlash('登録できませんでした。', 'default', array('class' => 'alert alert-error'));
                }
            }
        }
    }

    /**
     * 特別営業時間（削除）
     *
     * @param string $id
     * @return void
     */
    public function deleteSpecialBusinessHours($id = null)
    {

        if (!$this->OfficeBusinessHour->getOfficeBusinessHourByClientId($id, $this->clientData['client_id'])) {
            exit;
            //$this->redirect(array("controller" => "Users", "action" => "logout"));
        }

        $this->OfficeBusinessHour->id = $id;
        if (!$this->OfficeBusinessHour->exists()) {
            throw new NotFoundException(__('無効です。'));
        }
        if ($this->OfficeBusinessHour->save(array('delete_flg'=>1))) {
            $this->Session->setFlash('削除しました。', 'default', array('class' => 'alert alert-success'));
            $this->redirect($this->referer());
        }
        $this->Session->setFlash('削除されませんでした。', 'default', array('class' => 'alert alert-error'));

        $this->redirect($this->referer());
    }

    /**
     * 特別営業時間共通処理
     *
     * @param string $officeId
     * @return void
     */
    public function commonVariable($officeId)
    {

        // クライアントが違ったらログアウト
        $count = $this->Office->find(
            'count',
            array(
                'conditions' => array(
                        'Office.id' => $officeId,
                        'Office.client_id' => $this->clientData['client_id']
                ),
                'recursive' => -1,
            )
        );
        if ($count == 0) {
            $this->redirect(array("controller" => "Users", "action" => "logout"));
        }

        // 曜日
        $weekArray = array(
                'mon' => '月',
                'tue' => '火',
                'wed' => '水',
                'thu' => '木',
                'fri' => '金',
                'sat' => '土',
                'sun' => '日',
                'hol' => '祝'
        );

        $this->set(compact('weekArray'));
    }

    /**
     * ajax用 都道府県に応じた市区町村をgetする
     *
     * @param string $zipcode
     * @return string
     */
    public function get_city_list($zipcode = '')
    {
        $this->autoRender = false;
        if (!empty($zipcode) && $this ->request->is('ajax')) {
            $cityList = $this->City->find(
                'list',
                array(
                    'joins' => array(
                        array(
                            'type' => 'INNER',
                            'alias' => 'Zipcode',
                            'table' => 'zipcodes',
                            'conditions' => array(
                                'Zipcode.zipcode' => $zipcode,
                                'Zipcode.city_id = City.id',
                                'Zipcode.delete_flg' => 0
                            )
                        )
                    ),
                    'conditions' => array('City.delete_flg' => 0)
                )
            );
            return json_encode($cityList);
        }
    }

    /**
     * ajax用 動的交通アクセスをgetする
     *
     * @param string $zipcode
     * @return string
     */
    public function get_access_dynamic()
    {
        $this->autoRender = false;
        if ($this ->request->is('ajax')) {
            return $this->Office->getAccessDynamic(
                $this->request->query['nt'],
                $this->request->query['nl'],
                $this->request->query['ot'],
                $this->request->query['mt'],
                $this->request->query['rt']
            );
        }
    }

    /**
     * 一覧画面へ遷移する際のURL等取得
     *
     * @return string|array
     */
    public function getRedirectUrlForList()
    {
        if ($this->Session->check('clientReferer')) {
            return $this->Session->read('clientReferer');
        }
        return array('action' => 'index');
    }
}
