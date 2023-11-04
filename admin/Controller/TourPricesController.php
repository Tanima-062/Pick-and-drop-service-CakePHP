<?php
App::uses('AppController', 'Controller');

class TourPricesController extends AppController
{

    public $uses = array('TourPrice', 'Landmark');

    /**
     * 前処理
     *
     * @return void
     */
    public function beforeFilter()
    {
        parent::beforeFilter();

        $airportList = $this->Landmark->getAirportList();
        $airportList = Hash::combine($airportList, '{n}.Landmark.iata_cd', '{n}.Landmark.iata_cd');
        $this->set('airportList', array_merge($airportList, array('TYO' => 'TYO', 'OSA' => 'OSA')));

        $this->set('dateFromOptions', array(
            'formName' => 'TourPrice',
            'fieldName' => 'date_from',
            'dateFormat' => 'YMD',
            'class' => 'form',
            'minYear' => '2020',
            'empty' => '---'
        ));
        $this->set('dateToOptions', array(
            'formName' => 'TourPrice',
            'fieldName' => 'date_to',
            'dateFormat' => 'YMD',
            'class' => 'form',
            'minYear' => '2020',
            'empty' => '---'
        ));

        $this->set('timeStartOptions', array(
            'formName' => 'TourPrice',
            'fieldName' => 'time_start',
            'dateFormat' => 'HI',
            'class' => 'form',
            'empty' => false
        ));
        $this->set('timeEndOptions', array(
            'formName' => 'TourPrice',
            'fieldName' => 'time_end',
            'dateFormat' => 'HI',
            'class' => 'form',
            'empty' => false
        ));

        $this->set('passengerOptions', array(
            1 => '1人',
            2 => '2人',
            3 => '3人',
            4 => '4人',
            5 => '5人',
            6 => '6人',
            7 => '7人',
            8 => '8人'
        ));

        $this->set('carTypes', Constant::tourCarTypes());
    }

    /**
     * 一覧画面処理
     *
     * @return void
     */
    public function index()
    {
        $this->TourPrice->recursive = 0;
        $conditions = array();

        if (!empty($this->request->query['iata_cd'])) {
            $conditions['iata_cd'] = $this->request->query['iata_cd'];
            $this->request->data['TourPrice']['iata_cd'] = $this->request->query['iata_cd'];
        }
        if (!empty($this->request->query['date'])) {
            $date = $this->request->query['date'];
            if (!empty($date['year'])) {
                if (empty($date['month'])) {
                    $conditions[] = array(
                        "DATE_FORMAT(date_from, '%Y') <=" => $date['year'],
                        "DATE_FORMAT(date_to, '%Y') >=" => $date['year']
                    );
                } elseif (empty($date['day'])) {
                    $conditions[] = array(
                        "DATE_FORMAT(date_from, '%Y-%m') <=" => $date['year'].'-'.$date['month'],
                        "DATE_FORMAT(date_to, '%Y-%m') >=" => $date['year'].'-'.$date['month']
                    );
                } else {
                    $conditions[] = array(
                        'date_from <=' => $date['year'].'-'.$date['month'].'-'.$date['day'],
                        'date_to >=' => $date['year'].'-'.$date['month'].'-'.$date['day']
                    );
                }
            }
            $this->request->data['TourPrice']['date'] = $date;
        }
        if (!empty($this->request->query['passenger_count'])) {
            $conditions['passenger_count'] = $this->request->query['passenger_count'];
            $this->request->data['TourPrice']['passenger_count'] = $this->request->query['passenger_count'];
        }
        if (isset($this->request->query['delete_flg']) && is_numeric($this->request->query['delete_flg'])) {
            $conditions['delete_flg'] = $this->request->query['delete_flg'];
            $this->request->data['TourPrice']['delete_flg'] = $this->request->query['delete_flg'];
        }

        $this->set('dtOptions', array(
            'formName' => 'TourPrice',
            'fieldName' => 'date',
            'dateFormat' => 'YMD',
            'class' => 'form',
            'minYear' => '2020',
            'empty' => '---'
        ));

        $this->paginate = array('conditions' => $conditions, 'limit' => 20);

        $this->set('prices', $this->paginate());
    }

    /**
     * 募集型料金CSVインポート画面処理
     *
     * @return void
     */
    public function import()
    {
        if ($this->request->is('post')) {
            $insCount = 0;
            $errCount = 0;
            $errList = array();
            $tmpFileName = $this->request->data['TourPrice']['import_csv']['tmp_name'];
            if (is_uploaded_file($tmpFileName)) {
                $file = fopen($tmpFileName, 'r');
                $carTypes = Constant::tourCarTypes();
                $lineNo = 0;
                while (($line = fgetcsv($file, 0, ',')) !== false) {
                    $lineNo++;
                    foreach ($carTypes as $carType) {
                        if (in_array($line[5], $carType['passenger'])) {
                            $car = $carType;
                        }
                    }
                    $data = array(
                        'iata_cd' => $line[0],
                        'date_from' => date('Y-m-d', strtotime($line[1])).' 00:00',
                        'date_to' => date('Y-m-d', strtotime($line[2])).' 23:59',
                        'time_start' => $line[3],
                        'time_end' => $line[4],
                        'passenger_count' => $line[5],
                        'tour_car_type_id' => isset($car) ? $car['id'] : 0,
                        'tour_car_type_name' => isset($car) ? $car['name'] : '',
                        'tour_car_example' => isset($car) ? $car['example'] : '',
                        'price' => $line[6],
                        'staff_id' => $this->cdata['id']
                    );
                    $this->TourPrice->create();
                    if ($this->TourPrice->save($data)) {
                        $insCount += 1;
                    } else {
                        $errCount += 1;
                        $line['no'] = $lineNo;
                        $line['errors'] = implode('<br>', array_unique(Hash::extract($this->TourPrice->validationErrors, '{s}.{n}')));
                        $errList[] = $line;
                    }
                }
            }
            $success = false;
            if ($insCount) {
                $message = '料金データをインポートしました。';
                if ($errCount == 0) {
                    $class = 'alert alert-success';
                    $success = true;
                } else {
                    $class = 'alert alert-warning';
                    $message .= '取り込めなかったデータをご確認ください。';
                }
            } else {
                if ($errCount == 0) {
                    $message = 'CSVファイルが空です。';
                    $class = 'alert alert-warning';
                } else {
                    $message = '料金データをインポートできませんでした。取り込めなかったデータをご確認ください。';
                    $class = 'alert alert-error';
                }
            }
            $this->Session->setFlash($message, 'default', array('class' => $class));
            if ($success) {
                $this->redirectReferer();
            }
            $this->set('errList', $errList);
        }
    }

    /**
     * 料金追加画面処理
     *
     * @return void
     */
    public function add()
    {
        if ($this->request->is('post')) {
            $saveData = $this->request->data['TourPrice'];

            $saveData['date_from'] = sprintf(
                '%s-%s-%s 00:00:00',
                $saveData['date_from']['year'],
                $saveData['date_from']['month'],
                $saveData['date_from']['day']
            );
            $saveData['date_to'] = sprintf('%s-%s-%s 23:59:59', $saveData['date_to']['year'], $saveData['date_to']['month'], $saveData['date_to']['day']);

            $saveData['time_start'] = sprintf('%02d:%02d', $saveData['time_start']['hour'], $saveData['time_start']['min']);
            $saveData['time_end'] = sprintf('%02d:%02d', $saveData['time_end']['hour'], $saveData['time_end']['min']);

            $saveData['staff_id'] = $this->cdata['id'];
            if ($saveData['delete_flg'] == 1) {
                $saveData['deleted'] = date("Y-m-d H:i:s");
            }

            if ($this->TourPrice->save($saveData)) {
                $this->Session->setFlash('募集型料金を追加しました', 'default', array('class' => 'alert alert-success'));
                $this->redirectReferer();
            } else {
                $this->Session->setFlash('募集型料金の追加に失敗しました', 'default', array('class' => 'alert alert-error'));
            }
        }
    }

    /**
     * 編集画面処理
     *
     * @param string $id
     * @return void
     */
    public function edit($id = null)
    {
        if ($this->request->is('post') || $this->request->is('put')) {
            $saveData = $this->request->data['TourPrice'];

            $saveData['date_from'] = sprintf(
                '%s-%s-%s 00:00:00',
                $saveData['date_from']['year'],
                $saveData['date_from']['month'],
                $saveData['date_from']['day']
            );
            $saveData['date_to'] = sprintf('%s-%s-%s 23:59:59', $saveData['date_to']['year'], $saveData['date_to']['month'], $saveData['date_to']['day']);

            $saveData['time_start'] = sprintf('%02d:%02d', $saveData['time_start']['hour'], $saveData['time_start']['min']);
            $saveData['time_end'] = sprintf('%02d:%02d', $saveData['time_end']['hour'], $saveData['time_end']['min']);

            $saveData['staff_id'] = $this->cdata['id'];
            if ($saveData['delete_flg'] == 1) {
                $this->TourPrice->id = $id;
                if ($this->TourPrice->field('delete_flg') == 0) {
                    $saveData['deleted'] = date("Y-m-d H:i:s");
                }
            } else {
                $saveData['deleted'] = null;
            }

            if ($this->TourPrice->save($saveData)) {
                $this->Session->setFlash('募集型料金を編集しました', 'default', array('class' => 'alert alert-success'));
                $this->redirectReferer();
            } else {
                $this->Session->setFlash('募集型料金の編集に失敗しました', 'default', array('class' => 'alert alert-error'));
            }
        } else {
            $this->TourPrice->recursive = -1;
            $price = $this->TourPrice->find('first', array('conditions' => array('TourPrice.id' => $id)));
            if (empty($price)) {
                throw new NotFoundException(__('Invalid price'));
            }
            $this->request->data['TourPrice'] = $price['TourPrice'];
        }
    }

    /**
     * 非公開処理
     *
     * @return void
     */
    public function unpublish()
    {
        if ($this->request->is('post') || $this->request->is('put')) {
            if (empty($this->request->data['check'])) {
                $this->Session->setFlash('料金がチェックされていません。', 'default', array('class' => 'alert alert-warning'));
            } else {
                $this->TourPrice->updateAll(
                    array(
                        'staff_id' => $this->cdata['id'],
                        'modified' => 'NOW()',
                        'delete_flg' => 1,
                        'deleted' => 'NOW()'
                    ),
                    array(
                        'id' => $this->request->data['check'],
                        'delete_flg' => 0
                    )
                );
                $this->Session->setFlash('選択された料金を非公開にしました。', 'default', array('class' => 'alert alert-success'));
            }
        } else {
            $this->Session->setFlash('不正なアクセスです。', 'default', array('class' => 'alert alert-error'));
        }
        $this->redirect(array('action' => 'index'));
    }

    /**
     * 元画面へ遷移
     * ※元画面情報がなければ初期画面へ
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
