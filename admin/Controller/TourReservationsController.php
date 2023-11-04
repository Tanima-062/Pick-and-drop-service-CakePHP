<?php
App::uses('AppController', 'Controller');

class TourReservationsController extends AppController
{

    public $uses = array('TourReservation', 'Landmark', 'Client', 'CarType', 'Office');

    /**
     * 前処理
     *
     * @return void
     */
    public function beforeFilter()
    {
        parent::beforeFilter();

        $this->set('statusList', Constant::tourReservationStatus());
    }

    /**
     * 一覧画面処理
     *
     * @return void
     */
    public function index()
    {
        $this->TourReservation->recursive = 0;
        $conditions = array();

        // ツアー予約番号
        if (!empty($this->request->query['cm_application_id'])) {
            $ids = array_diff(explode("\n", str_replace("\r", "", $this->request->query['cm_application_id'])), array(''));
            foreach ($ids as $key => $inputId) {
                // 前後の全半角スペース除去
                $ids[$key] = preg_replace("/(^\s+)|(\s+$)/u", "", $inputId);
            }
            if (count($ids) > 1) {
                $conditions['cm_application_id'] = $ids;
            } elseif (count($ids) > 0) {
                $conditions['cm_application_id like'] = '%' . current($ids) . '%';
            }
            $this->request->query['cm_application_id'] = implode("\n", $ids);
            $this->request->data['TourReservation']['cm_application_id'] = $this->request->query['cm_application_id'];
        }
        // RC予約番号
        if (!empty($this->request->query['reservation_key'])) {
            $ids = array_diff(explode("\n", str_replace("\r", "", $this->request->query['reservation_key'])), array(''));
            foreach ($ids as $key => $inputId) {
                // 前後の全半角スペース除去
                $ids[$key] = preg_replace("/(^\s+)|(\s+$)/u", "", $inputId);
            }
            if (count($ids) > 1) {
                $conditions['reservation_key'] = $ids;
            } elseif (count($ids) > 0) {
                $conditions['reservation_key like'] = '%' . current($ids) . '%';
            }
            $this->request->query['reservation_key'] = implode("\n", $ids);
            $this->request->data['TourReservation']['reservation_key'] = $this->request->query['reservation_key'];
        }
        if (isset($this->request->query['reservation_status_id']) && is_numeric($this->request->query['reservation_status_id'])) {
            $conditions['reservation_status_id'] = $this->request->query['reservation_status_id'];
            $this->request->data['TourReservation']['reservation_status_id'] = $this->request->query['reservation_status_id'];
        }
        if (!empty($this->request->query['rent_dt'])) {
            if (!empty($this->request->query['rent_dt']['year'])) {
                $rentDt = $this->request->query['rent_dt']['year'];
                if (!empty($this->request->query['rent_dt']['month'])) {
                    $rentDt .= '-' . $this->request->query['rent_dt']['month'];
                    if (!empty($this->request->query['rent_dt']['day'])) {
                        $rentDt .= '-' . $this->request->query['rent_dt']['day'];
                    }
                }
                $conditions['TourReservation.rent_dt LIKE'] = $rentDt . '%';
                $this->request->data['TourReservation']['rent_dt'] = $this->request->query['rent_dt'];
            }
        }
        if (!empty($this->request->query['return_dt'])) {
            if (!empty($this->request->query['return_dt']['year'])) {
                $returnDt = $this->request->query['return_dt']['year'];
                if (!empty($this->request->query['return_dt']['month'])) {
                    $returnDt .= '-' . $this->request->query['return_dt']['month'];
                    if (!empty($this->request->query['return_dt']['day'])) {
                        $returnDt .= '-' . $this->request->query['return_dt']['day'];
                    }
                }
                $conditions['TourReservation.return_dt LIKE'] = $returnDt . '%';
                $this->request->data['TourReservation']['return_dt'] = $this->request->query['return_dt'];
            }
        }
        if (!empty($this->request->query['last_name'])) {
            $conditions['last_name like'] = '%' . $this->request->query['last_name'] . '%';
            $this->request->data['TourReservation']['last_name'] = $this->request->query['last_name'];
        }
        if (!empty($this->request->query['first_name'])) {
            $conditions['first_name like'] = '%' . $this->request->query['first_name'] . '%';
            $this->request->data['TourReservation']['first_name'] = $this->request->query['first_name'];
        }
        if (!empty($this->request->query['client_id'])) {
            $conditions['client_id'] = $this->request->query['client_id'];
            $this->request->data['TourReservation']['client_id'] = $this->request->query['client_id'];
        }
        if (!empty($this->request->query['iata_cd'])) {
            $conditions['iata_cd'] = $this->request->query['iata_cd'];
            $this->request->data['TourReservation']['iata_cd'] = $this->request->query['iata_cd'];
        }
        if (!empty($this->request->query['booking_dt'])) {
            if (!empty($this->request->query['booking_dt']['year'])) {
                $bookingDt = $this->request->query['booking_dt']['year'];
                if (!empty($this->request->query['booking_dt']['month'])) {
                    $bookingDt .= '-' . $this->request->query['booking_dt']['month'];
                    if (!empty($this->request->query['booking_dt']['day'])) {
                        $bookingDt .= '-' . $this->request->query['booking_dt']['day'];
                    }
                }
                $conditions['TourReservation.booking_dt LIKE'] = $bookingDt . '%';
                $this->request->data['TourReservation']['booking_dt'] = $this->request->query['booking_dt'];
            }
        }

        if (!empty($this->request->query['getCsv'])) {
            $this->__downloadCsvData($conditions);
        }

        $airportList = $this->Landmark->getAirportList();
        $airportList = Hash::combine($airportList, '{n}.Landmark.iata_cd', '{n}.Landmark.iata_cd');
        $this->set('airportList', array_merge($airportList, array('TYO' => 'TYO', 'OSA' => 'OSA')));

        $this->set('clientList', $this->Client->find('list', array('conditions' => array('delete_flg' => 0))));

        $this->set('rentDtOptions', array(
            'formName' => 'TourReservation',
            'fieldName' => 'rent_dt',
            'dateFormat' => 'YMD',
            'class' => 'form',
            'minYear' => '2020',
            'empty' => '---'
        ));
        $this->set('returnDtOptions', array(
            'formName' => 'TourReservation',
            'fieldName' => 'return_dt',
            'dateFormat' => 'YMD',
            'class' => 'form',
            'minYear' => '2020',
            'empty' => '---'
        ));
        $this->set('bookingDtOptions', array(
            'formName' => 'TourReservation',
            'fieldName' => 'booking_dt',
            'dateFormat' => 'YMD',
            'class' => 'form',
            'minYear' => '2020',
            'empty' => '---'
        ));

        $this->paginate = array('conditions' => $conditions, 'order' => 'id desc', 'limit' => 20);

        $this->set('reservations', $this->paginate());
    }

    /**
     * 編集画面処理
     *
     * @param string $id
     * @return void
     */
    public function edit($id = null)
    {
        $isValidationError = false;
        if ($this->request->is('post') || $this->request->is('put')) {
            $saveData = $this->request->data['TourReservation'];

            $this->TourReservation->id = $saveData['id'];
            $defaultStatus = $this->TourReservation->field('reservation_status_id');
            if ($saveData['reservation_status_id'] != $defaultStatus) {
                if ($saveData['reservation_status_id'] == 1 && $defaultStatus == 0) {
                    $saveData['reservation_dt'] = date('Y-m-d H:i:s');
                }
                if ($saveData['reservation_status_id'] == 3) {
                    $saveData['cancel_dt'] = date('Y-m-d H:i:s');
                } else {
                    $saveData['cancel_dt'] = null;
                }
            }
            $saveData['rent_dt'] = sprintf(
                '%s-%s-%s %02d:%02d',
                $saveData['rent_dt']['year'],
                $saveData['rent_dt']['month'],
                $saveData['rent_dt']['day'],
                $saveData['rent_dt']['hour'],
                $saveData['rent_dt']['min']
            );
            $saveData['return_dt'] = sprintf(
                '%s-%s-%s %02d:%02d',
                $saveData['return_dt']['year'],
                $saveData['return_dt']['month'],
                $saveData['return_dt']['day'],
                $saveData['return_dt']['hour'],
                $saveData['return_dt']['min']
            );
            $saveData['staff_id'] = $this->cdata['id'];

            if ($this->TourReservation->save($saveData)) {
                $this->Session->setFlash('募集型予約を編集しました', 'default', array('class' => 'alert alert-success'));
                // refererがあれば戻す
                if (!empty($this->request->data['Custom']['referer'])) {
                    $this->redirect($this->request->data['Custom']['referer']);
                } else {
                    $this->redirect(array('action' => 'index'));
                }
            } else {
                $isValidationError = true;
                $this->Session->setFlash('募集型予約の編集に失敗しました', 'default', array('class' => 'alert alert-error'));
            }
        }

        $this->set('rentDtOptions', array(
            'formName' => 'TourReservation',
            'fieldName' => 'rent_dt',
            'dateFormat' => 'YMDHI',
            'class' => 'span2',
            'minYear' => '2020',
            'empty' => '---'
        ));
        $this->set('returnDtOptions', array(
            'formName' => 'TourReservation',
            'fieldName' => 'return_dt',
            'dateFormat' => 'YMDHI',
            'class' => 'span2',
            'minYear' => '2020',
            'empty' => '---'
        ));

        $this->set('wday', array('日', '月', '火', '水', '木', '金', '土'));

        if ($isValidationError) {
            $iataCd = $this->request->data['TourReservation']['iata_cd'];
            $clientId = $this->request->data['TourReservation']['client_id'];
        } else {
            $this->TourReservation->recursive = -1;
            $reservation = $this->TourReservation->find('first', array('conditions' => array('TourReservation.cm_application_id' => $id)));
            if (empty($reservation)) {
                throw new NotFoundException(__('Invalid reservation'));
            }
            $this->request->data['TourReservation'] = $reservation['TourReservation'];
            if (empty($reservation['TourReservation']['rent_dt'])) {
                $this->request->data['TourReservation']['rent_dt'] = date('Y-m-d', strtotime($reservation['TourReservation']['arrival_dt']));
            }
            if (empty($reservation['TourReservation']['return_dt'])) {
                $this->request->data['TourReservation']['return_dt'] = date('Y-m-d', strtotime($reservation['TourReservation']['departure_dt']));
            }
            $iataCd = $reservation['TourReservation']['iata_cd'];
            $clientId = $reservation['TourReservation']['client_id'];
        }
        $iataCd = $this->__convertIataCd($iataCd);
        $this->set('clientListXiata', $this->Client->getClientListByIata($iataCd));
        $this->set('officeList', !empty($clientId) ? $this->Office->getOfficeByClientIata($clientId, $iataCd) : array());
        $this->set('carTypeList', $this->CarType->find('list', array('conditions' => array('delete_flg' => 0))));
    }

    /**
     * ajax用 会社と空港に応じた店舗をgetする
     * @param string $clientId
     * @param string $iataCd
     */
    public function get_office_list($clientId = '', $iataCd = '')
    {
        $this->autoRender = false;
        $officeList = array();
        if (!empty($clientId) && !empty($iataCd) && $this->request->is('ajax')) {
            $officeList = $this->Office->getOfficeByClientIata($clientId, $this->__convertIataCd($iataCd));
        }
        return json_encode($officeList);
    }

    /**
     * ajax用 店舗の情報をgetする
     * @param string $officeId
     */
    public function get_office_info($officeId = '')
    {
        $this->autoRender = false;
        $info = array();
        if (!empty($officeId) && $this->request->is('ajax')) {
            $office = $this->Office->getTourOfficeInfo($officeId);
            $info['tel'] = $office['Office']['tel'];
            $info['url'] = $office['office_contents_url'];
        }
        return json_encode($info);
    }

    /**
     * 空港コード変換
     *
     * @param string $iataCd
     * @return void
     */
    private function __convertIataCd($iataCd)
    {
        // 空港コードでなく都市コード来る場合あり、変換する
        switch ($iataCd) {
            case 'TYO':
                // 東京（羽田、成田）
                return array('HND', 'NRT');
            case 'OSA':
                // 大阪（関西、伊丹）
                return array('KIX', 'ITM');
            default:
                return $iataCd;
        }
    }

    /**
     * CSVデータ出力
     *
     * @param array $conditions
     * @return void
     */
    private function __downloadCsvData($conditions)
    {
        Configure::write('debug', 0); // debugコードを出さない
        $this->autoRender = false; // Viewを使わない

        $count = $this->TourReservation->find('count', array('conditions' => $conditions));
        $limit = 5000;
        $loop  = ceil($count / $limit);

        if ($count > 0) {
            $fileName = date('YmdHis') . '.csv';
            $pathFile = TMP.$fileName;

            $csvFile = fopen(TMP.$fileName, 'w') or die('Unable to open file!');

            stream_filter_prepend($csvFile, 'convert.iconv.utf-8/cp932//TRANSLIT');

            $csvData = 'ツアー予約番号,RC予約番号,ステータス,利用期間,車両タイプ,予約者氏名,利用会社,受取店舗,返却店舗,利用空港,販売額,仕入額,利益,申込日時' . "\r\n";
            fwrite($csvFile, $csvData);

            for ($i = 0; $i < $loop; $i++) {
                $reservationData = $this->TourReservation->find('all', array('conditions' => $conditions, 'limit' => $limit, 'offset' => $limit * $i));
                foreach ($reservationData as $k => $v) {
                    $r = $v['TourReservation'];

                    $csvLine = $r['cm_application_id'] . ',';
                    $csvLine .= $r['reservation_key'] . ',';
                    $csvLine .= Constant::tourReservationStatus()[$r['reservation_status_id']] . ',';
                    $csvLine .= (!empty($r['rent_dt']) ? ($r['rent_dt'] . '～' . $r['return_dt']) : '') . ',';
                    $csvLine .= $r['car_type_name'] . ',';
                    $csvLine .= (!empty($r['last_name']) ? ($r['last_name'] . ' ' . $r['first_name']) : '') . ',';
                    $csvLine .= $r['client_name'] . ',';
                    $csvLine .= $r['rent_office_name'] . ',';
                    $csvLine .= $r['return_office_name'] . ',';
                    $csvLine .= $r['iata_cd'] . ',';
                    $csvLine .= $r['price'] . ',';
                    $csvLine .= $r['net_price'] . ',';
                    $csvLine .= ((isset($r['net_price']) && is_numeric($r['net_price'])) ? ($r['price'] - $r['net_price']) : '') . ',';
                    $csvLine .= $r['booking_dt'] . ',';
                    $csvLine .= "\r\n";

                    fwrite($csvFile, $csvLine);
                }
            }

            fclose($csvFile);

            header('Content-disposition: attachment; filename=' . $fileName);
            header('Content-type: application/octet-stream; name=' . $fileName);
            readfile($pathFile);
            unlink($pathFile);
        }

        exit();
    }
}
