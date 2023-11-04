<?php
App::uses('AppController', 'Controller');
App::uses('Sanitize', 'Utility');
App::uses('SkyticketCakeEmail', 'Vendor');
App::uses('CakeTime', 'Utility');
require_once("encrypt_class.php");
/**
 * Reservations Controller
 *
 * @property Reservation $Reservation
 */
class ReservationsController extends AppController
{

    public $reservationDetail = array();
    public $basicPrice;
    public $dayCount;
    private $salesType;

    public $components = array('YotpoAPI', 'ReservationAPISelect', 'CancelPolicy');

    public $uses = array(
        'Reservation', 'ReservationStatus', 'Office', 'Airline',
        'ReservationMail', 'Prefecture', 'Plan', 'Commodity', 'CarClass', 'CarType',
        'ReservationPrivilege', 'Privilege', 'ChildSheetPrice',
        'ReservationDetail', 'ReservationChildSheet', 'CommodityPrice', 'PriceRankCalendar',
        'CarClassReservation', 'CommodityPrivilege',
        'CommodityRentOffice', 'CommodityReturnOffice', 'CommodityItem', 'CancelReason',
        'ClientTemplate', 'DeliveryMail', 'PublicHoliday', 'CommodityGroup',
        'PaymentLog','MessageBoard', 'UnlockClientEdit', 'CancelDetail', 'Refund'
    );

    /**
     * 前処理
     *
     * @return void
     */
    public function beforeFilter()
    {

        parent::beforeFilter();

        $mailStatus = array(
            0 => '未返信',
            1 => '返信済み',
            2 => '対応完了',
            3 => '設定なし',
        );

        $this->set('mailStatus', $mailStatus);

        $registeredFlgArray = array(
            0 => '未処理',
            1 => '登録済み',
        );

        $this->set('registeredFlgArray', $registeredFlgArray);

        if ($this->clientData['Client']['accept_prepay']) {
            $paymentMethod = array(
                1 => 'WEB事前決済',
                0 => '現地精算',
            );
            $this->set('paymentMethod', $paymentMethod);
        }

        if (empty($this->clientData['is_system_admin'])) {
            $this->loginStaffId = $this->clientData['id'];
        }

        $this->set('is_check_user', true);
        $this->salesType = Constant::salesType();
    }

    /**
     * 一覧画面処理
     *
     * @return void
     */
    public function index()
    {
        /**
         * 現在のページを保持
         */
        if ($this->Session->check('clientReferer')) {
            $this->Session->delete('clientReferer');
        }


        $acceptPrepay = $this->clientData['Client']['accept_prepay'];
        $this->set('acceptPrepay', $acceptPrepay);

        $conditions['conditions'] = array();
        $conditions['conditions']['Reservation.client_id'] = $this->clientData['client_id'];

        $conditions['joins'] = [
            [
                'type' => 'INNER',
                'table' => 'commodity_items',
                'alias' => 'CommodityItem',
                'conditions' => [
                    'CommodityItem.id = Reservation.commodity_item_id',
                ]
            ],
            [
                'type' => 'INNER',
                'table' => 'commodities',
                'alias' => 'Commodity',
                'conditions' => [
                    'Commodity.id = CommodityItem.commodity_id',
                ]
            ]
        ];

        $conditionsPaymentMethod = '';

        // CSV用の設定
        $joinsCsv = array();

        // ステータス
        if (isset($this->request->query['ReservationStatus'])) {
            if (is_numeric($this->request->query['ReservationStatus'])) {
                $conditions['conditions']['Reservation.reservation_status_id'] = $this->request->query['ReservationStatus'];
            }
        }

        // 返信状況
        if (isset($this->request->query['mail_status'])) {
            if (is_numeric($this->request->query['mail_status'])) {
                $conditions['conditions']['Reservation.mail_status'] = $this->request->query['mail_status'];
            }
        }

        // 予約番号
        if (!empty($this->request->query['ReservationKeyId'])) {
            $this->request->query['ReservationKeyId'] = preg_replace("/(^\s+)|(\s+$)/u", "", $this->request->query['ReservationKeyId']);
            $resKeyIds = Sanitize::clean($this->request->query['ReservationKeyId']);
            $conditions['conditions']['Reservation.reservation_key'] = $resKeyIds;
        }

        // 管理番号
        if (!empty($this->request->query['control_number'])) {
            $conditions['conditions']['Reservation.control_number'] = $this->request->query['control_number'];
        }


        // 電話番号
        if (!empty($this->request->query['ReservationTel'])) {
            $tel = Sanitize::clean($this->request->query['ReservationTel']);
            $conditions['conditions']['Reservation.tel'] = $tel;
        }

        // セイ
        if (!empty($this->request->query['ReservationLastName'])) {
            $lastName = Sanitize::clean($this->request->query['ReservationLastName']);
            $conditions['conditions']['Reservation.last_name like'] = '%' . $lastName . '%';
        }

        // メイ
        if (!empty($this->request->query['ReservationFirstName'])) {
            $firstName = Sanitize::clean($this->request->query['ReservationFirstName']);
            $conditions['conditions']['Reservation.first_name like'] = '%' . $firstName . '%';
        }

        // 利用開始日
        $rentSetCurrentMonth = false;
        $rentSetCurrentDay = false;
        if (empty($this->request->query)) {
            $rentSetCurrentDay = true;
            $currentYear = date('Y');
            $currentMonth = date('m');
            $currentDay = date('d');
            $conditions['conditions']['Reservation.rent_datetime >='] = $currentYear . '-' . $currentMonth . '-'.$currentDay.' 00:00:00';
            $conditions['conditions']['Reservation.rent_datetime <='] = $currentYear . '-' . $currentMonth . '-'.$currentDay.' 23:59:59';
        } else {
            if (isset($this->request->query['ReservationRentDate'])) {
                // 年
                if (!empty($this->request->query['ReservationRentDate']['year'])) {
                    $rentDate = $this->request->query['ReservationRentDate']['year'];
                    // 月
                    if (!empty($this->request->query['ReservationRentDate']['month'])) {
                        $rentDate .=  '-' . $this->request->query['ReservationRentDate']['month'];
                        // 日まで絞り込んだ時
                    } else {
                        $rentDate .=  '-01';
                    }

                    if (!empty($this->request->query['ReservationRentDate']['day'])) {
                        $rentDate .= '-' . $this->request->query['ReservationRentDate']['day'];
                    } else {
                        $rentDate .=  '-01';
                    }

                    $conditions['conditions']['Reservation.rent_datetime >='] = $rentDate;

                    $this->request->data['ReservationRentDate']['ReservationRentDate'] = $this->request->query['ReservationRentDate'];
                }
            }

            if (isset($this->request->query['ReservationRentDate2'])) {
                // 年
                if (!empty($this->request->query['ReservationRentDate2']['year'])) {
                    $rentDate = $this->request->query['ReservationRentDate2']['year'];
                    // 月
                    if (!empty($this->request->query['ReservationRentDate2']['month'])) {
                        $rentDate .=  '-' . $this->request->query['ReservationRentDate2']['month'];
                    } else {
                        $rentDate .=  '-12';
                    }

                    // 日
                    if (!empty($this->request->query['ReservationRentDate2']['day'])) {
                        $rentDate .= '-' . $this->request->query['ReservationRentDate2']['day'];
                    } else {
                        $rentDate .= '-'.date("t", strtotime($rentDate.'-01'));
                    }
                    $rentDate .= '23:59:59';

                    $conditions['conditions']['Reservation.rent_datetime <='] = $rentDate;

                    $this->request->data['ReservationRentDate2']['ReservationRentDate2'] = $this->request->query['ReservationRentDate2'];
                }
            }
        }

        // 利用終了日
        if (isset($this->request->query['ReservationReturnDate'])) {
            // 年
            if (!empty($this->request->query['ReservationReturnDate']['year'])) {
                $rentDate = $this->request->query['ReservationReturnDate']['year'];
                // 月
                if (!empty($this->request->query['ReservationReturnDate']['month'])) {
                    $rentDate .=  '-' . $this->request->query['ReservationReturnDate']['month'];
                    // 日まで絞り込んだ時
                } else {
                    $rentDate .=  '-01';
                }

                if (!empty($this->request->query['ReservationReturnDate']['day'])) {
                    $rentDate .= '-' . $this->request->query['ReservationReturnDate']['day'];
                } else {
                    $rentDate .=  '-01';
                }

                $conditions['conditions']['Reservation.return_datetime >='] = $rentDate;

                $this->request->data['ReservationReturnDate']['ReservationReturnDate'] = $this->request->query['ReservationReturnDate'];
            }
        }

        if (isset($this->request->query['ReservationReturnDate2'])) {
            // 年
            if (!empty($this->request->query['ReservationReturnDate2']['year'])) {
                $rentDate = $this->request->query['ReservationReturnDate2']['year'];
                // 月
                if (!empty($this->request->query['ReservationReturnDate2']['month'])) {
                    $rentDate .=  '-' . $this->request->query['ReservationReturnDate2']['month'];
                } else {
                    $rentDate .=  '-12';
                }

                // 日
                if (!empty($this->request->query['ReservationReturnDate2']['day'])) {
                    $rentDate .= '-' . $this->request->query['ReservationReturnDate2']['day'];
                } else {
                    $rentDate .= '-'.date("t", strtotime($rentDate.'-01'));
                }
                $rentDate .= '23:59:59';

                $conditions['conditions']['Reservation.return_datetime <='] = $rentDate;

                $this->request->data['ReservationReturnDate2']['ReservationReturnDate2'] = $this->request->query['ReservationReturnDate2'];
            }
        }

        // 営業所
        if (isset($this->request->query['ReservationOfficeName'])) {
            if (is_numeric($this->request->query['ReservationOfficeName'])) {
                $conditions['conditions'][] = array(
                    'OR'=>
                    array(
                        'Reservation.rent_office_id'=> $this->request->query['ReservationOfficeName'],
                        'Reservation.return_office_id'=> $this->request->query['ReservationOfficeName']
                    )
                );
            }
        }

        // 車両ｸﾗｽ、商品ｸﾞﾙｰﾌﾟ
        $isCarClassSet = isset($this->request->query['ReservationCarClassName']) ?
            (is_numeric($this->request->query['ReservationCarClassName']) ? true : false) : false;
        $isCommodityGroupSet = isset($this->request->query['ReservationCommodityGroupName']) ?
            (is_numeric($this->request->query['ReservationCommodityGroupName']) ? true : false) : false;
        if ($isCarClassSet) {
            $conditions['conditions'][] = [
                    'CommodityItem.car_class_id' => $this->request->query['ReservationCarClassName'],
            ];
        }
        if ($isCommodityGroupSet) {
            $conditions['conditions'][] = [
                'Commodity.commodity_group_id' => $this->request->query['ReservationCommodityGroupName'],
            ];
        }

        // 支払方法
        if ($acceptPrepay) {
            if (isset($this->request->query['PaymentMethod']) && is_numeric($this->request->query['PaymentMethod'])) {
                if ($this->request->query['PaymentMethod'] == 1) {
                    $conditionsPaymentMethod = array(
                        'OR' => array(
                            array(
                                'Commodity.sales_type' => Constant::SALES_TYPE_ARRANGED,
                                'Reservation.payment_status IS NOT NULL'
                            ),
                            array(
                                'Commodity.sales_type' => Constant::SALES_TYPE_AGENT_ORGANIZED,
                                'Reservation.sales_price > 0'
                            )
                        )
                    );
                } else {
                    $conditionsPaymentMethod = array(
                        'OR' => array(
                            array(
                                'Commodity.sales_type' => Constant::SALES_TYPE_ARRANGED,
                                'Reservation.payment_status IS NULL'
                            ),
                            array(
                                'Commodity.sales_type' => Constant::SALES_TYPE_AGENT_ORGANIZED,
                                'Reservation.sales_price' => 0
                            )
                        )
                     );
                }
            }
        }

        $joinsCsv = array_merge(
            $conditions['joins'],
            $joinsCsv,
            array(
                array(
                    "type" => "LEFT",
                    "table" => "car_classes",
                    "alias" => "CarClass",
                    "conditions" => "CommodityItem.car_class_id = CarClass.id"
                ),
                array(
                    "type" => "LEFT",
                    "table" => "car_types",
                    "alias" => "CarType",
                    "conditions" => "CarType.id = CarClass.car_type_id"
                ),
                array(
                    "type" => "LEFT",
                    "table" => "offices",
                    "alias" => "RentOffice",
                    "conditions" => "RentOffice.id = Reservation.rent_office_id"
                ),
                array(
                    "type" => "LEFT",
                    "table" => "offices",
                    "alias" => "ReturnOffice",
                    "conditions" => "ReturnOffice.id = Reservation.return_office_id"
                ),
                array(
                    "type" => "INNER",
                    'table' => "reservation_statuses",
                    "alias" => "ReservationStatus",
                    "conditions" => "Reservation.reservation_status_id =  ReservationStatus.id"
                ),
            )
        );

        // 申込日時
        if (isset($this->request->query['ReservationCreatedDate'])) {
            // 年
            if (!empty($this->request->query['ReservationCreatedDate']['year'])) {
                $rentDate = $this->request->query['ReservationCreatedDate']['year'];
                // 月
                if (!empty($this->request->query['ReservationCreatedDate']['month'])) {
                    $rentDate .=  '-' . $this->request->query['ReservationCreatedDate']['month'];
                    // 日まで絞り込んだ時
                } else {
                    $rentDate .=  '-01';
                }

                if (!empty($this->request->query['ReservationCreatedDate']['day'])) {
                    $rentDate .= '-' . $this->request->query['ReservationCreatedDate']['day'];
                } else {
                    $rentDate .=  '-01';
                }

                $conditions['conditions']['Reservation.created >='] = $rentDate;

                $this->request->data['ReservationCreatedDate']['Created'] = $this->request->query['ReservationCreatedDate'];
            }
        }

        if (isset($this->request->query['ReservationCreatedDate2'])) {
            // 年
            if (!empty($this->request->query['ReservationCreatedDate2']['year'])) {
                $rentDate = $this->request->query['ReservationCreatedDate2']['year'];
                // 月
                if (!empty($this->request->query['ReservationCreatedDate2']['month'])) {
                    $rentDate .=  '-' . $this->request->query['ReservationCreatedDate2']['month'];
                } else {
                    $rentDate .=  '-12';
                }

                // 日
                if (!empty($this->request->query['ReservationCreatedDate2']['day'])) {
                    $rentDate .= '-' . $this->request->query['ReservationCreatedDate2']['day'];
                } else {
                    $rentDate .= '-'.date("t", strtotime($rentDate.'-01'));
                }
                $rentDate .= '23:59:59';

                $conditions['conditions']['Reservation.created <='] = $rentDate;

                $this->request->data['ReservationCreatedDate2']['Created2'] = $this->request->query['ReservationCreatedDate2'];
            }
        }

        // キャンセル日時
        if (isset($this->request->query['ReservationCancelDate'])) {
            // 年
            if (!empty($this->request->query['ReservationCancelDate']['year'])) {
                $rentDate = $this->request->query['ReservationCancelDate']['year'];
                // 月
                if (!empty($this->request->query['ReservationCancelDate']['month'])) {
                    $rentDate .=  '-' . $this->request->query['ReservationCancelDate']['month'];
                    // 日まで絞り込んだ時
                } else {
                    $rentDate .=  '-01';
                }

                if (!empty($this->request->query['ReservationCancelDate']['day'])) {
                    $rentDate .= '-' . $this->request->query['ReservationCancelDate']['day'];
                } else {
                    $rentDate .=  '-01';
                }

                $conditions['conditions']['Reservation.cancel_datetime >='] = $rentDate;

                $this->request->data['ReservationCancelDate']['Cancel'] = $this->request->query['ReservationCancelDate'];
            }
        }

        if (isset($this->request->query['ReservationCancelDate2'])) {
            // 年
            if (!empty($this->request->query['ReservationCancelDate2']['year'])) {
                $rentDate = $this->request->query['ReservationCancelDate2']['year'];
                // 月
                if (!empty($this->request->query['ReservationCancelDate2']['month'])) {
                    $rentDate .=  '-' . $this->request->query['ReservationCancelDate2']['month'];
                } else {
                    $rentDate .=  '-12';
                }

                // 日
                if (!empty($this->request->query['ReservationCancelDate2']['day'])) {
                    $rentDate .= '-' . $this->request->query['ReservationCancelDate2']['day'];
                } else {
                    $rentDate .= '-'.date("t", strtotime($rentDate.'-01'));
                }
                $rentDate .= '23:59:59';

                $conditions['conditions']['Reservation.cancel_datetime <='] = $rentDate;

                $this->request->data['ReservationCancelDate2']['Cancel2'] = $this->request->query['ReservationCancelDate2'];
            }
        }

        // 登録処理
        if (isset($this->request->query['RegisteredFlg'])) {
            if (is_numeric($this->request->query['RegisteredFlg'])) {
                $conditions['conditions']['Reservation.registered_flg'] =  $this->request->query['RegisteredFlg'];
            }
        }

        // 販売方法
        if (isset($this->request->query['SalesType']) &&
            is_string($this->request->query['SalesType']) &&
            array_key_exists($this->request->query['SalesType'], $this->salesType)
        ) {
            $conditions['conditions']['Commodity.sales_type'] = $this->request->query['SalesType'];
        }

        $cancelReason = $this->CancelReason->find('list', array('fields' => array('id', 'reason'), 'conditions' => array('delete_flg' => 0), 'recursive' => -1));
        $reservationStatus = $this->ReservationStatus->find('list', array('fields' => array('ReservationStatus.name')));
        $officeName = $this->Office->find('list', array(
            'conditions' => array(
                'Office.client_id' => $this->clientData['client_id'],
                // 実績が見えなくなるのでフラグ除外
                //'Office.delete_flg'=>0
            ),
            'fields' => array(
                'Office.id',
                'Office.name'
            ),
            'order' => array('Office.sort ASC'),
        ));

        if ($this->clientData['is_system_admin'] == 0) {
            $officeIdArray = array_keys($officeName);
            $conditions['conditions']['OR'] = array(
                'Reservation.rent_office_id'=>$officeIdArray,
            );
        }

        // 車両ｸﾗｽ ﾌﾟﾙﾀﾞｳﾝ
        $carClassName = $this->CarClass->getCarClassLists($this->clientData['client_id']);

        // 商品ｸﾞﾙｰﾌﾟ ﾌﾟﾙﾀﾞｳﾝ
        $commodityGroupName = $this->CommodityGroup->getList($this->clientData['client_id']);

        // 販売方法 プルダウン
        $salesType = $this->salesType;

        $conditions['fields'] = array('Reservation.*', "DATE_FORMAT(Reservation.created, '%Y%m%d')", 'Commodity.sales_type');
        $conditions['order']= '
            Reservation.mail_status = 0 desc,
            Reservation.created desc,
            Reservation.mail_status = 3 desc,
            Reservation.mail_status = 1 desc,
            Reservation.mail_status = 2 desc
        ';
        if ($acceptPrepay) {
            $conditions['fields'][] = 'Reservation.payment_status';
        }

        $tempOfficeList = $this->Office->find('all', array(
            'conditions' => array(
                'Office.id <>' => 0,
                'Office.client_id' => $this->clientData['client_id'],
                'Office.delete_flg' => 0
            ),
            'fields' => array(
                'Office.id',
                'Office.name',
                'Office.office_code',
            ),
            'recursive' => -1,
            //'order' => array('Office.sort ASC'),
        ));
        $clientOfficeAll = array();
        foreach ($tempOfficeList as $v) {
            $clientOfficeAll[$v['Office']['id']] = array('name' => $v['Office']['name'], 'office_code' => $v['Office']['office_code']);
        }

        /**
         * CSV出力
         */
        if (!empty($this->request->query['getCsv'])) {
            $conditions['joins'] = $joinsCsv;
            $this->__downloadCsvData($conditions, $conditionsPaymentMethod);
        }

        if (!empty($conditionsPaymentMethod)) {
            $conditions['conditions'][] = $conditionsPaymentMethod;
        }

        $this->paginate = $conditions;

        $this->Reservation->recursive = -1;
        $reservations = $this->paginate();
        $this->set('reservations', $reservations);

        /**
         *  予約番号取得
         */
        $reservationIdList = Hash::extract($reservations, '{n}.Reservation.id');

        $reservationMail = $this->ReservationMail->find(
            'list',
            array(
                'fields' => array('ReservationMail.reservation_id','ReservationMail.contents'),
                'conditions' => array(
                    'ReservationMail.reservation_id' => $reservationIdList,
                ),
            )
        );

        $this->set(
            compact(
                'reservationStatus',
                'officeName',
                'reservationMail',
                'cancelReason',
                'carClassName',
                'commodityGroupName',
                'clientOfficeAll',
                'salesType'
            )
        );

        $this->request->data['Reservation'] = $this->request->query;

        $this->__setViewVars($rentSetCurrentMonth, $rentSetCurrentDay);
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
        if (preg_match('/client\/reservations\/\?ReservationStatus/', $referer)) {
            $this->Session->write('clientReferer', $referer);
        } elseif (preg_match('/client\/Reservations\/index\/sort/i', $referer)) {
            $this->Session->write('clientReferer', $referer);
        } elseif (preg_match('/'.preg_quote('client/reservations/?', '/').'/', $referer)) {
            $this->Session->write('clientReferer', $referer);
        }

        // 曜日
        $this->set('wday', $this->wday);

        $params = array();
        if (!empty($this->data['Reservation']['rent_datetime'])) {
            $this->dayCount = $this->_getDayCount();
        }

        // 編集不可だったらログアウト
        $isEditable = $this->Reservation->isEditableByThisStaff($id, $this->clientData['client_id']);
        if (!$isEditable) {
            $this->redirect(array("controller" => "users", "action" => "logout"));
        }

        // お問い合わせ＆メール送信
        if (isset($this->data['Mail'])) {
            $mailData = $this->data['ReservationMail'];
            $mailData['mail_datetime'] = date('Y-m-d H:i:s');

            if ($this->ReservationMail->save($mailData)) {
                // 苗字取得
                $reservationData = $this->Reservation->getReservationFirstData($id);
                $params['last_name'] = $reservationData['Reservation']['last_name'];
                $params['first_name'] = $reservationData['Reservation']['first_name'];
                $params['client_name'] = $reservationData['Client']['name'];
                $params['domain'] = $_SERVER['SERVER_NAME'];

                $params['hash'] = $this->data['E']['hash'];

                // 継承してメールクラスを利用
                // $email = new CakeEmail('smtp');
                $email = new SkyticketCakeEmail('smtp');
                // ユーザには非表示
                $email->non_show_user_flg = 1;
                $email
                ->config('smtp')
                ->template('inquiry', 'suggestions_layout')
                ->subject('【skyticket】'.$params['client_name'].'様からご連絡です。')
                ->viewVars($params)
                ->to(trim($this->data['E']['mail']))
                ->send();

                // ステータス変更
                $this->Reservation->save($this->data['Reservation']);

                $this->redirect('/'.$this->request->url.'#inquiry');
            } else {
                $this->set('sendError', '<span style="color: red;">送信に失敗しました</span>');
            }
        }

        $this->__setViewVars();
        $this->Reservation->unbindModel(array(
            'hasMany' => array(
                'Contract',
                'ReservationDetail',
            ),
            'belongsTo' => array(
                'Client',
                'UserSession',
                'ReservationStatus',
                'Estimate',
                'PriceSpan',
                'Prefecture',
                'CancelContactMethod',
                'Staff'
            )
        ), false);
        $this->Reservation->hasMany['ReservationMail']['fields'] = array('*');
        $this->Commodity->unbindModel(array('belongsTo' => array('Client', 'Plan', 'Language', 'Staff')));
        $this->CarClass->unbindModel(
            array(
                'belongsTo' => array('Client', 'CarType', 'Staff'),
                'hasMany' => array('CarClassReservation', 'CarClassStock', 'ClientCarModel', 'CommodityItem')
            )
        );
        $this->CarType->unbindModel(array('belongsTo' => array('Client', 'Plan', 'Language', 'Staff')));
        $this->Privilege->unbindModel(array('belongsTo' => array('Client', 'Staff')));
        $this->ReservationPrivilege->unbindModel(array('belongsTo' => array('Reservation', 'Privilege', 'Staff')));

        if (!$this->Reservation->exists($id)) {
            throw new NotFoundException(__('Invalid reservation'));
        }

        $setData = $this->Reservation->getReservationFirstData($id);
        $this->log(sprintf('reservation_id(%d) staff_id(%d) setData[Reservation]: ', $id, $this->clientData['id']) . json_encode($setData['Reservation']), LOG_DEBUG);

        // 手数料マイナス(クライアントには手数料が入っていない金額を表示)
        $administrativeFee = !empty($setData['Reservation']['administrative_fee']) ? $setData['Reservation']['administrative_fee'] : 0;
        $setData['Reservation']['amount'] -= $administrativeFee;

        if (isset($this->data['reservation'])) {
            $saveData = $this->request->data;
            $this->log(sprintf('reservation_id(%d) staff_id(%d) saveData: ', $id, $this->clientData['id']) . json_encode($saveData), LOG_DEBUG);

            $isCancel = false;
            $isDateChange = false;

            if ($setData['Reservation']['modified'] > $saveData['Reservation']['default_modified'] ||
                $setData['Reservation']['cancel_datetime'] > $saveData['Reservation']['default_cancel_datetime']) {
                $this->Session->setFlash('編集中に予約データが更新されました。', 'default', array('class' => 'alert alert-error'));
                $this->redirect($this->referer());
            }

            // キャンセルされた場合（初回キャンセル：キャンセルデータをさらに更新する場合は除く）
            if (!empty($saveData['Reservation']['reservation_status_id']) &&
                $saveData['Reservation']['reservation_status_id'] != $saveData['Reservation']['default_status']
            ) {
                // キャンセル処理date追加
                if ($saveData['Reservation']['reservation_status_id'] == 3) {
                    $saveData['Reservation']['cancel_flg'] = 1;
                    $saveData['Reservation']['cancel_datetime'] = date('Y-m-d H:i:s');
                    $saveData['Reservation']['cancel_staff_id'] = $this->clientData['id'];
                    // キャンセル処理を行った人によって理由を分ける
                    if($this->clientData['is_system_admin']){
                        $saveData['Reservation']['cancel_reason_id'] = Constant::SYSTEM_ADMIN_CANCEL;
                    }else{
                        $saveData['Reservation']['cancel_reason_id'] = Constant::CLIENT_USER_CANCEL;
                    }

                    if (!empty($setData['Reservation']['payment_status'])) {
                        $paymentStatus = $this->__getPaymentStatus(
                            $saveData['Reservation']['reservation_status_id'],
                            $saveData['Reservation']['cancel_reason_id']
                        );
                        $saveData['Reservation']['payment_status'] = $paymentStatus;
                    }

                    $isCancel = true;
                }
            }

            $saveData['Reservation']['span_count'] = $this->dayCount;
            if ($saveData['Reservation']['can_edit']) {
                if (!empty($saveData['Reservation']['rent_datetime'])) {
                    $saveData['Reservation']['rent_datetime'] = $this->_convertDatetime($saveData['Reservation']['rent_datetime']);
                }
                if (!empty($saveData['Reservation']['return_datetime'])) {
                    $saveData['Reservation']['return_datetime'] = $this->_convertDatetime($saveData['Reservation']['return_datetime']);
                }

                // ご利用期間が貸出日<返却日となっているか確認
                if (!empty($saveData['Reservation']['rent_datetime']) && !empty($saveData['Reservation']['return_datetime'])) {
                    if (strtotime($saveData['Reservation']['rent_datetime']) >= strtotime($saveData['Reservation']['return_datetime'])) {
                        $this->Session->setFlash('利用終了日時は利用開始日時より後にしてください。', 'default', array('class' => 'alert alert-error'));
                        $this->redirect($this->referer());
                    }
                }
            }

            // レンナビ予約連携API用
            if ($this->ReservationAPISelect->isRennaviApiTarget($setData['Reservation']['client_id'])) {
                if ($isCancel) {
                    if ($setData['Reservation']['rennavi_status'] == Constant::RENNAVI_STATUS_RESERVE ||
                        $setData['Reservation']['rennavi_status'] == Constant::RENNAVI_STATUS_RESERVE_FIXED ||
                        $setData['Reservation']['rennavi_status'] == Constant::RENNAVI_STATUS_RESERVE_CHANGED) {
                        $saveData['Reservation']['rennavi_status'] = Constant::RENNAVI_STATUS_CANCEL_CLIENT;
                    }
                } else {
                    // スカイレンタカーは全予約の「登録処理」を登録済みに更新してそうなので、それ以外で変更あるか判定する
                    // しないと、キャンセル以外の全予約が「料金変更あり」になって、連携件数が膨大に（「料金変更あり」は何度でも連携対象）
                    if ($this->isChanged($saveData)) {
                        if ($setData['Reservation']['rennavi_status'] == Constant::RENNAVI_STATUS_RESERVE_FIXED) {
                            $saveData['Reservation']['rennavi_status'] = Constant::RENNAVI_STATUS_RESERVE_CHANGED;
                        }
                    }
                }
            }

            // 画面では入金額から手数料を引いて表示していたので、手数料を足して保存する。
            $saveData['Reservation']['amount'] += $administrativeFee;

            $reservationAPI = null;
            $apiErrorMailRequired = false;
            try {
                $this->Reservation->begin();

                // 予約データ保存
                list($saveFlg, $errorString) = $this->saveReservationData($saveData);

                if ($saveFlg) {
                    // ご利用期間が変更されていたか判定(キャンセルの場合,予約在庫を再取得する必要がないので飛ばす)
                    if (!($setData['Reservation']['rent_datetime'] == $saveData['Reservation']['rent_datetime'] && $setData['Reservation']['return_datetime'] == $saveData['Reservation']['return_datetime']) && !$isCancel) {
                        // 後で更新するので予約在庫データの商品情報を取得しておく
                        $conditions = array('reservation_id' => $saveData['Reservation']['id']);
                        $options = array(
                            'fields' => array(
                                'stock_date',
                                'stock_group_id',
                                'car_class_id',
                                'reservation_count',
                                'delete_flg'
                            ),
                            'conditions' => array(
                                'reservation_id' => $saveData['Reservation']['id'],
                            ),
                            'order' => array('stock_date'),
                            'recursive' => -1
                        );
                        $result = $this->CarClassReservation->find('all',$options);
                        $stockDates = Hash::extract($result, '{n}.CarClassReservation[delete_flg=0].stock_date');
                        $carClassReservationData = end(Hash::extract($result, '{n}.CarClassReservation[delete_flg=0]'));
                        $isDateChange = true;
                    }
                    // 予約を削除
                    if ($isCancel) {
                        $this->CarClassReservation->updateAll(
                            array('staff_id' => $this->clientData['id'], 'delete_flg' => 1, 'deleted' => date("'Y-m-d H:i:s'", time())),
                            array('reservation_id' => $saveData['Reservation']['id'])
                        );
                    }
                    // ご利用期間が変更されていた時だけ予約在庫の更新をする
                    if ($isDateChange) {
                        $extensionFlg = '0';
                        $shorteningFlg = '0';
                        // ご利用期間のfromが過去/未来どちら方向に編集されたか
                        if (strtotime($saveData['Reservation']['rent_datetime']) < strtotime($setData['Reservation']['rent_datetime'])) {
                            // ご利用期間のfromが過去方向に編集された(延長)
                            $extensionFlg = '1';
                        } elseif (strtotime($saveData['Reservation']['rent_datetime']) > strtotime($setData['Reservation']['rent_datetime'])) {
                            // ご利用期間のfromが未来方向に編集された(短縮)
                            $shorteningFlg = '1';
                        }
                        // ご利用期間のtoが過去/未来どちら方向に編集されたか
                        if (strtotime($saveData['Reservation']['return_datetime']) < strtotime($setData['Reservation']['return_datetime'])) {
                            // ご利用期間のtoが過去方向に編集された(短縮)
                            $shorteningFlg = '1';
                        } elseif (strtotime($saveData['Reservation']['return_datetime']) > strtotime($setData['Reservation']['return_datetime'])) {
                            // ご利用期間のtoが未来方向に編集された(延長)
                            $extensionFlg = '1';
                        }
                        // ご利用期間延長対応(toが今日以前の場合範囲が全て過去なので延長処理は不要)
                        if ($extensionFlg == '1' && strtotime($saveData['Reservation']['return_datetime']) >= strtotime(date('Y/m/d'))) {
                            // 過去の在庫を取得する必要がないため、出発日が過去の場合は今日を始点とする
                            if (strtotime($saveData['Reservation']['rent_datetime']) < strtotime(date('Y/m/d'))) {
                                    $rentDatetime = date('Y/m/d H:i:s');
                            } else {
                                    $rentDatetime = $saveData['Reservation']['rent_datetime'];
                            }
                            $from = strtotime(date('Y-m-d', strtotime($rentDatetime)));
                            $to = strtotime(date('Y-m-d', strtotime($saveData['Reservation']['return_datetime'])));
                            $step = 60 * 60 * 24;
                            $arrayTime = range($from, $to, $step);
                            foreach ($arrayTime as $time) {
                                $inputDate = date('Y-m-d', $time);
                                $inputDates[] = $inputDate;
                                if (in_array($inputDate, $stockDates)) {
                                    continue;
                                }
                                $carClassReservationParams[] = array(
                                    'client_id' => $this->clientData['client_id'],
                                    'stock_group_id' => $carClassReservationData['stock_group_id'],
                                    'car_class_id' => $carClassReservationData['car_class_id'],
                                    'stock_date' => $inputDate,
                                    'reservation_id' => $saveData['Reservation']['id'],
                                    'reservation_count' => $carClassReservationData['reservation_count'],
                                );
                            }
                            // この配列にデータがない=在庫の変動がない
                            if (!empty($carClassReservationParams)) {
                                // 在庫チェック
                                $remainingStock = $this->CommodityItem->getOfficeStocks($carClassReservationData['car_class_id'], $saveData['Reservation'], $stockDates);
                                if (empty($remainingStock) || empty($remainingStock[$saveData['Reservation']['rent_office_id']])) {
                                    $saveFlg = false;
                                    $errorString = '在庫が見つかりませんでした。';
                                }
                                if ($saveFlg) {
                                    $carClassReservationResult = $this->CarClassReservation->saveMany($carClassReservationParams);
                                    if (empty($carClassReservationResult)) {
                                        $saveFlg = false;
                                        $errorString = '在庫の取得に失敗しました。';
                                        $this->log($this->Session->id().'[createReservation] carClassReservationResult empty error', LOG_DEBUG);
                                    }
                                }
                            }
                        }
                        // ご利用期間短縮対応
                        if ($shorteningFlg == '1') {
                            $inputDates = array();
                            $from = strtotime(date('Y-m-d', strtotime($saveData['Reservation']['rent_datetime'])));
                            $to = strtotime(date('Y-m-d', strtotime($saveData['Reservation']['return_datetime'])));
                            $step = 60 * 60 * 24;
                            $arrayTime = range($from, $to, $step);
                            foreach ($arrayTime as $time) {
                                $inputDates[] = date('Y-m-d', $time);
                            }

                            $from = strtotime($stockDates[0]);
                            $to = strtotime(end($stockDates));
                            $step = 60 * 60 * 24;
                            $arrayTime = range($from, $to, $step);
                            foreach ($arrayTime as $time) {
                                $setDate = date('Y-m-d', $time);
                                if (in_array($setDate, $inputDates)) {
                                    continue;
                                }
                                $deleteDates[] = $setDate;
                            }
                            if (!empty($deleteDates)) {
                                $this->CarClassReservation->updateAll(
                                    array('staff_id' => $this->clientData['id'], 'delete_flg' => 1, 'deleted' => date("'Y-m-d H:i:s'", time())),
                                    array('reservation_id' => $saveData['Reservation']['id'], 'stock_date' => $deleteDates)
                                );
                            }
                        }
                    }
                }

                if ($saveFlg) {
                    // 予約連携API

                    // 初回キャンセルか更新の場合
                    if ($isCancel || $saveData['Reservation']['reservation_status_id'] != 3) {
                        // 予約マスタのAPIステータスが対象外ではない場合
                        // 対象外：連携していない会社のデータ or 連携開始前のデータ
                        if ($setData['Reservation']['api_status_id'] != Constant::API_STATUS_EXCLUDED) {
                            $componentName = $this->ReservationAPISelect->getApiComponentName($this->clientData['client_id']);
                            if (!empty($componentName)) {
                                // 会社別コンポーネントロード
                                $reservationAPI = $this->Components->load($componentName);

                                // 送信データセット
                                $reservationAPI->setClientReservationData(
                                    $saveData['Reservation']['id'],
                                    ($isCancel ? Constant::API_STATUS_CANCEL : Constant::API_STATUS_CHANGE)
                                );

                                // 送信
                                list($success, $result) = $reservationAPI->sendReservationData();
                                if ($success) {
                                    if ($result['status']) {
                                        $apiErrorMailRequired = true;
                                    } else {
                                        $saveFlg = false;
                                        $errorString = sprintf(
                                            "%s連携が失敗しました。(%s)",
                                            ($isCancel ? 'キャンセル' : '変更'),
                                            (!empty($result['message']) ? $result['message'] : '')
                                        );
                                    }
                                } else {
                                    $saveFlg = false;
                                    $apiErrorMailRequired = true;
                                    $errorString = ($isCancel ? 'キャンセル' : '変更') . '連携中に何らかのエラーが発生しました。';
                                }
                            }
                        }
                    }
                }

                if ($saveFlg) {
                    $this->Reservation->commit();
                    $apiErrorMailRequired = false;
                }
            } catch (Exception $e) {
                $errorString = sprintf("%s\n%s", $e->getMessage(), $e->getTraceAsString());
                $saveFlg = false;
            }

            if ($saveFlg) {
                if ($isCancel) {
                    //BOC Yotpo delete order
                    $yotpoOrderIDsToDelete=array($saveData['Reservation']['id']);
                    $this->YotpoAPI->deleteOrder($yotpoOrderIDsToDelete);
                    //EOC Yotpo delete order

                    if ($saveData['Reservation']['default_status'] == 1 && $setData['Commodity']['sales_type'] == Constant::SALES_TYPE_ARRANGED) {
                        $reservation = $this->Reservation->getReservationDataForMail($saveData['Reservation']['id']);

                        // 曜日
                        $weekday = array('日', '月', '火', '水', '木', '金', '土');
                        $rentWeekDay = $weekday[date('w', strtotime($reservation['Reservation']['rent_datetime']))];
                        $returnWeekDay = $weekday[date('w', strtotime($reservation['Reservation']['return_datetime']))];

                        $count = $this->Reservation->find('count', [
                            'conditions' => [
                                'Reservation.id' => $reservation['Reservation']['id'],
                                'NOT' => ['payment_status' => null],
                            ]
                        ]);

                        $fromStep1 = !($count);
                        $mailParam = array(
                            'reservation_id' => $reservation['Reservation']['id'],
                            'client_id' => $reservation['Reservation']['client_id'],
                            'client_name' => $reservation['Client']['name'],
                            'reservation_key' => $reservation['Reservation']['reservation_key'],
                            'reservation_hash' => $reservation['Reservation']['reservation_hash'],
                            'rent_date' => date('Y年m月d日', strtotime($reservation['Reservation']['rent_datetime'])),
                            'rent_week' => $rentWeekDay,
                            'rent_time' => date('H:i', strtotime($reservation['Reservation']['rent_datetime'])),
                            'return_date' => date('Y年m月d日', strtotime($reservation['Reservation']['return_datetime'])),
                            'return_week' => $returnWeekDay,
                            'return_time' => date('H:i', strtotime($reservation['Reservation']['return_datetime'])),
                            'last_name' => $reservation['Reservation']['last_name'],
                            'first_name' => $reservation['Reservation']['first_name'],
                            'email' => $reservation['Reservation']['email'],
                            'tel' => $reservation['Reservation']['tel'],
                            'amount' => $reservation['Reservation']['amount'],
                            'arrival_flight_number' => $reservation['Reservation']['arrival_flight_number'],
                            'departure_flight_number' => $reservation['Reservation']['departure_flight_number'],
                            'adults_count' => $reservation['Reservation']['adults_count'],
                            'children_count' => $reservation['Reservation']['children_count'],
                            'infants_count' => $reservation['Reservation']['infants_count'],
                            'rent_office_name' => $reservation['RentOffice']['name'],
                            'rent_office_tel' => $reservation['RentOffice']['tel'],
                            'return_office_name' => $reservation['ReturnOffice']['name'],
                            'commodity_name' => mb_convert_kana($reservation['Commodity']['name'], 'KV'),
                            'car_class' => $reservation['CarClass']['name'],
                            'car_type' => $reservation['CarType']['name'],
                            'domain' => $_SERVER['HTTP_HOST'],
                            'is_send_mail' => $reservation['Reservation']['is_send_mail'],
                            'from_step1' => $fromStep1,
                            'cancel_policy' => $this->CancelPolicy->getTextLines(
                                $reservation['Reservation']['client_id'],
                                $reservation['Reservation']['rent_datetime'],
                                false
                            ),
                            'client_cancel_policy' => $reservation['Client']['cancel_policy'],
                            'rent_office_reserve_mail' => $reservation['RentOffice']['reserve_mail'],
                            'rent_office_reserve_mail2' => $reservation['RentOffice']['reserve_mail2'],
                            'rent_office_reserve_mail3' => $reservation['RentOffice']['reserve_mail3'],
                        );
                        // INCIDENT-3044 取消手続料の徴収を廃止する
                        //$mailParam[0]['advCancelFee'] = $this->CancelPolicy->getAdvCancelFee();
                        // キャンセル通知メール送信
                        $this->sendCancelMail($mailParam);
                    }
                }

                $this->Session->setFlash('変更しました', 'default', array('class' => 'alert alert-success'));
            } else {
                $this->Reservation->rollback();
                if ($apiErrorMailRequired) {
                    $reservationAPI->sendAlertFromClient($setData['Reservation']['control_number'], $_SERVER['HTTP_HOST']);
                }

                $this->log(sprintf("ReservationId : %s\n%s", $saveData['Reservation']['id'], $errorString), 'error');
                $this->Session->setFlash('失敗しました : '.$errorString, 'default', array('class' => 'alert alert-error'));
            }

            $this->redirect($this->referer());
        }

        $this->request->data = $setData;

        $cm_application_id = '';
        if ($this->clientData['is_system_admin'] == 1) {
            $cm_application_id = $this->Reservation->getCmApplicationId($id);
        }
        $this->set('cm_application_id', $cm_application_id);

        if ($this->DeliveryMail->checkReturnMail($this->request->data['Reservation'])) {
            $this->set('mailError', '1');
        }

        // オプション共通
        $privilegeCommonOptions = array(
            'conditions' => array(
                'Privilege.client_id' => $this->clientData['Client']['id'],
                'Privilege.delete_flg' => 0,
            ),
            'recursive' => -1,
        );
        if (!$this->clientData['is_client_admin']) {
            $privilegeCommonOptions['conditions']['OR'] = array(
                array('Privilege.scope' => 0),
                array('Privilege.scope' => $this->clientData['id'])
            );
        }

        // オプション（チャイルドシート）
        $privilegeSheetOptions = array_merge_recursive($privilegeCommonOptions, array('conditions' => array('Privilege.option_flg' => 1)));
        $privilegeSheet = $this->Privilege->find('all', $privilegeSheetOptions);
        $this->set('privilegeSheet', $privilegeSheet);
        // 予約オプション（チャイルドシート）取得
        $reservationChildSheetOptions = array(
            'conditions' => array(
                'ReservationChildSheet.reservation_id' => $id,
                'ReservationChildSheet.delete_flg' => 0,
            ),
            'recursive' => -1,
        );
        $reservationChildSheetData = $this->ReservationChildSheet->find('all', $reservationChildSheetOptions);
        $reservationChildSheetData = Hash::combine($reservationChildSheetData, '{n}.ReservationChildSheet.child_sheet_id', '{n}.ReservationChildSheet');
        $this->set('reservationChildSheetData', $reservationChildSheetData);

        // オプション（特典）
        $privilegeOptions = array_merge_recursive($privilegeCommonOptions, array('conditions' => array('Privilege.option_flg' => 0)));
        $privilegeData = $this->Privilege->find('all', $privilegeOptions);
        $this->set('privilegeData', $privilegeData);
        // 予約オプション（特典）取得
        $reservationPrivilegeOptions = array(
            'conditions' => array(
                'ReservationPrivilege.reservation_id' => $id,
                'ReservationPrivilege.delete_flg' => 0,
            ),
            'recursive' => -1,
        );
        $reservationPrivilegeData = $this->ReservationPrivilege->find('all', $reservationPrivilegeOptions);
        $reservationPrivilegeData = Hash::combine($reservationPrivilegeData, '{n}.ReservationPrivilege.privilege_id', '{n}.ReservationPrivilege');
        $this->set('reservationPrivilegeData', $reservationPrivilegeData);

        // お問い合わせデータ
        $reservationMailOptions = array(
            'fields' => array(
                'ReservationMail.id',
                'ReservationMail.mail_datetime',
                'ReservationMail.staff_id',
                'ReservationMail.contents',
                'ReservationMail.read_flg',
                'Staff.name',
            ),
            'joins' => array(
                array(
                    'type' => 'LEFT',
                    'alias' => 'Staff',
                    'table' => 'staffs',
                    'conditions' => 'Staff.id = ReservationMail.staff_id',
                ),
            ),
            'conditions' => array(
                    'ReservationMail.reservation_id' => $id,
            ),
            'order' => array(
                    'ReservationMail.created DESC'
            ),
            'recursive' => -1,
        );
        $reservationMails = $this->ReservationMail->find('all', $reservationMailOptions);
        $this->request->data['ReservationMail'] = $reservationMails;


        $officeName = $this->Office->find(
            'list',
            array(
                'conditions' => array(
                    'Office.client_id' => $this->clientData['client_id'],
                    // 物理削除されたかを見たい
                    //'Office.delete_flg' => 0,
                ),
                'fields' => array(
                    'Office.name'
                ),
                'callbacks' => false,
            )
        );
        $rentOfficeId = $this->request->data['Reservation']['rent_office_id'];
        $returnOfficeId = $this->request->data['Reservation']['return_office_id'];
        // 両方存在しなくても表示したい
        //if (empty($officeName[$rentOfficeId]) && empty($officeName[$returnOfficeId])) {
        //    $this->redirect($this->referer());
        //}

        $rentOfficeMap = $this->CommodityRentOffice->getRentOffice($this->request->data['CommodityItem']['commodity_id'], $this->clientData['client_id']);

        $rentOfficeName = array();
        foreach ($rentOfficeMap as $i => $rentOffice) {
            $rentOfficeName[$i] = $rentOffice['Office']['name'];
        }

        // 受取営業所が存在しない場合
        if (empty($officeName[$rentOfficeId])) {
            $rentOfficeName[$rentOfficeId] = '(削除店舗)';
        } else if (empty($rentOfficeName[$rentOfficeId])) {
            $rentOfficeName[$rentOfficeId] = $officeName[$rentOfficeId] . '(現在受取対象外)';
        }
        ksort($rentOfficeName);

        $returnOfficeMap = $this->CommodityReturnOffice->getReturnOffice($this->request->data['CommodityItem']['commodity_id'], $this->clientData['client_id']);

        $returnOfficeName = array();
        foreach ($returnOfficeMap as $i => $rentOffice) {
            $returnOfficeName[$i] = $rentOffice['Office']['name'];
        }

        // 返却営業所が存在しない場合
        if (empty($officeName[$returnOfficeId])) {
            $returnOfficeName[$returnOfficeId] = '(削除店舗)';
        } else if (empty($returnOfficeName[$returnOfficeId])) {
            $returnOfficeName[$returnOfficeId] = $officeName[$returnOfficeId] . '(現在返却対象外)';
        }
        ksort($returnOfficeName);

        $reservationStatus = $this->ReservationStatus->find('list', array('conditions' => array('delete_flg' => 0)));
        if ($this->clientData['is_system_admin'] == 0) {
            if ($setData['Reservation']['reservation_status_id'] == Constant::STATUS_RESERVATION) {
                // 予約→成約の手動変更は不可
                unset($reservationStatus[Constant::STATUS_CONTRACT]);
            } else if ($setData['Reservation']['reservation_status_id'] == Constant::STATUS_CONTRACT) {
                // 成約→予約の手動変更は不可
                unset($reservationStatus[Constant::STATUS_RESERVATION]);
            }
        }
        $carClass = $this->CarClass->find('first', array('conditions'=>array('CarClass.id' => $this->request->data['CommodityItem']['car_class_id'])));
        $carType = $this->CarType->find('list');
        $commodityPrivileges = $this->CommodityPrivilege->getCommodityPrivilege($this->data['Reservation']['commodity_item_id']);
        $reservationPrivilege = $this->ReservationPrivilege->find('all', array('conditions'=>array('ReservationPrivilege.reservation_id' => $id)));

        $cancelReason = $this->CancelReason->find('list', array('fields' => array('id', 'reason'), 'conditions' => array('delete_flg' => 0), 'recursive' => -1));

        $nowDate = date('Y-m-d H:i:s', time());
        $this->set('clientId', $this->clientData['id']);
        $this->set('acceptPrepay', $this->clientData['Client']['accept_prepay']);
        $this->set(
            compact(
                'rentOfficeName',
                'rentOfficeMap',
                'returnOfficeName',
                'returnOfficeMap',
                'reservationStatus',
                'carClass',
                'carType',
                'nowDate',
                'reservationPrivilege',
                'commodityPrivileges',
                'officeName',
                'cancelReason'
            )
        );

        // 予約明細データ(合計料金の内訳)
        $breakDownData = $this->ReservationDetail->getBreakDownData($id);
        foreach ($breakDownData as $key => $val) {
            $breakDown[$key]['data_type_id'] = $val['DetailType']['id'];
            $breakDown[$key]['name'] = $val['DetailType']['name'];
            $breakDown[$key]['amount'] = $val['ReservationDetail']['amount'];
        }
        $breakDownContent = array();
        foreach ($breakDown as $val) {
            // 初期化処理
            if (empty($breakDownContent[$val['data_type_id']]['sum'])) {
                $breakDownContent[$val['data_type_id']]['sum'] = 0;
            }
            $breakDownContent[$val['data_type_id']]['data_type_id'] = $val['data_type_id'];
            $breakDownContent[$val['data_type_id']]['name'] = $val['name'];
            $breakDownContent[$val['data_type_id']]['sum'] += $val['amount'];
            $breakDownContent[$val['data_type_id']]['cars_count'] = $this->data['Reservation']['cars_count'];
        }
        $this->set(compact('breakDownContent'));

        $this->set('clientTemplateList', $this->ClientTemplate->getClientTemplateList($this->clientData['Client']['id'], $this->loginStaffId));
        $this->set('clientTemplate', $this->ClientTemplate->getClientTemplateContentList($this->clientData['Client']['id'], $this->loginStaffId));

        if ($this->clientData['is_system_admin']) {
            $this->set('responseHistories', $this->MessageBoard->getReservationResponseHistories($id));
        }

        $showDescUnlockBtn = false;
        $canEdit = true;
        $canStatusEdit = true;

        if ($this->request->data['Commodity']['sales_type'] == Constant::SALES_TYPE_AGENT_ORGANIZED && !$this->clientData['is_system_admin']) {
            $canEdit = false; // 募集型企画の場合はクライアントによる編集不可（CAR-344）
            $canStatusEdit = false;
            $this->log(sprintf('reservation_id(%d) staff_id(%d) AGENT_ORGANIZED ROUTE', $id, $this->clientData['id']), LOG_DEBUG);
        } else if ($this->request->data['Reservation']['reservation_status_id'] == 3) {
            $canEdit = false;
            $canStatusEdit = false;
            if ($this->clientData['is_system_admin']) {
                $showDescUnlockBtn = true;
            }
            $this->log(sprintf('reservation_id(%d) staff_id(%d) CANCELLED ROUTE', $id, $this->clientData['id']), LOG_DEBUG);
        } else {
            $currentTime = strtotime('now');

            if ($this->clientData['Client']['conclusion_contract_criteria']) {
                // 締め日は返却日
                $conclusion = $this->request->data['Reservation']['return_datetime'];
            } else {
                // 締め日は貸出日
                $conclusion = $this->request->data['Reservation']['rent_datetime'];
            }

            $conclusionYear = date('Y', strtotime($conclusion));
            $conclusionMonth = date('n', strtotime($conclusion));
            $currentYear = date('Y', $currentTime);
            $currentMonth = date('n', $currentTime);

            // 締め日の翌々月以降は編集不可
            if ($currentMonth + ($currentYear - $conclusionYear) * 12 > $conclusionMonth + 1) {
                $canEdit = false;
                $canStatusEdit = false;
                if ($this->clientData['is_system_admin']) {
                    $showDescUnlockBtn = true;
                }
                $this->log(sprintf('reservation_id(%d) staff_id(%d) >= SETTLEMENT MONTH + 2', $id, $this->clientData['id']), LOG_DEBUG);
                // 締め日の翌月
            } elseif ($currentMonth + ($currentYear - $conclusionYear) * 12 == $conclusionMonth + 1) {
                $today = date('j', $currentTime);
                if ($today >= 3) {
                    $holidays = $this->PublicHoliday->getHolidaysByMonth(date('Y-m', $currentTime));
                    if ($currentMonth == 1) {
                        // 1/2 & 1/3 が休日になるが、祝日マスタには登録できない
                        // （祝日とすると、店舗の営業時間も祝日仕様になってしまう）
                        $holidays[2] = $currentYear.'-01-02'; // emptyでなければ何でも良い
                        $holidays[3] = $currentYear.'-01-03';
                    }

                    $businessDayCount = 0;
                    for ($i = 1; $i <= $today; $i++) {
                        $timeStamp = mktime(0, 0, 0, $currentMonth, $i, $currentYear);
                        $dayOfWeek = date('w', $timeStamp);
                        if ($dayOfWeek == 0 || $dayOfWeek == 6) {
                            continue;
                        } elseif (!empty($holidays[$i])) {
                            continue;
                        }
                        // 第三営業日以降は編集不可
                        if (++$businessDayCount >= 3) {
                            $canEdit = false;
                            $canStatusEdit = false;
                            if ($this->clientData['is_system_admin']) {
                                $showDescUnlockBtn = true;
                            }
                            $this->log(sprintf('reservation_id(%d) staff_id(%d) = SETTLEMENT MONTH + 1', $id, $this->clientData['id']), LOG_DEBUG);
                            break;
                        }
                    }
                }
            }
            if ($canEdit) {
                if (!is_null($setData['Reservation']['payment_status']) && !$this->clientData['is_system_admin']) {
                    $canEdit = false; // web事前決済の場合はクライアントによる編集不可 (INCIDENT-605)
                    // 予約ステータスの変更は可
                    $this->log(sprintf('reservation_id(%d) staff_id(%d) PAID IN ADVANCE ROUTE', $id, $this->clientData['id']), LOG_DEBUG);
                }
            }
        }

        $unlockClientEdit = $this->UnlockClientEdit->findBy($this->clientData['id'], $id);
        // ロック解除中なので画面上にロック解除ボタンは表示しない
        if ($unlockClientEdit && $unlockClientEdit['UnlockClientEdit']['end_datetime'] >= date('Y-m-d H:i:s')) {
            $showDescUnlockBtn = false;
            $canEdit = true;
            $canStatusEdit = true;
        }

        $this->set('canEdit', $canEdit);
        $this->set('canStatusEdit', $canStatusEdit);
        $this->set('showDescUnlockBtn', $showDescUnlockBtn);

        $this->log(sprintf('reservation_id(%d) staff_id(%d) canEdit: ', $id, $this->clientData['id']) . ($canEdit ? 'true' : 'false'), LOG_DEBUG);
        $this->log(sprintf('reservation_id(%d) staff_id(%d) canStatusEdit: ', $id, $this->clientData['id']) . ($canStatusEdit ? 'true' : 'false'), LOG_DEBUG);

        $count = $this->Reservation->find('count', [
            'conditions' => [
                'Reservation.id' => $id,
                'NOT' => ['payment_status' => null],
            ]
        ]);
        $this->set('isPaidInAdvance', $count);
        $this->log(sprintf('reservation_id(%d) staff_id(%d) isPaidInAdvance: ', $id, $this->clientData['id']) . $count, LOG_DEBUG);

        $this->set('isRennaviApiTarget', $this->ReservationAPISelect->isRennaviApiTarget($this->request->data['Reservation']['client_id']));

        $cancelFeeSum = $this->CancelDetail->find('first', [
            'fields' => [
                'SUM(CancelDetail.amount * CancelDetail.count) AS cancel_fee_sum'
            ],
            'conditions' => [
                'CancelDetail.reservation_id' => $id,
                'CancelDetail.account_code NOT IN' => [
                    'ADVENTURE_FEE',
                    'ADMINISTRATIVE_FEE'
                ]
            ],
            'recursive' => -1
        ]);
        $this->set('cancelFee', $cancelFeeSum[0]['cancel_fee_sum']);
    }

    /**
     * キャンセル処理
     *
     * @return void
     */
    public function cancel()
    {

        set_time_limit(0);

        $conditions['conditions'] = array();
        $conditions['paramType'] = 'querystring';
        // ステータス
        $this->request->query['ReservationStatus'] = $conditions['conditions']['Reservation.reservation_status_id'] = 3;

        // クライアント
        $conditions['conditions']['Reservation.client_id'] = $this->clientData['Client']['id'];

        // キャンセル理由
        if (!empty($this->request->query['cancel_reason_id'])) {
            $conditions['conditions']['Reservation.cancel_reason_id'] = $this->request->query['cancel_reason_id'];
        }

        // 申込日時が入力されていたとき
        if (!empty($this->request->query['ReservationCreatedDate'])) {
            $searchDate = $this->request->query['ReservationCreatedDate'];
            // 年が入力されている場合
            if (!empty($searchDate['year'])) {
                $rentDate = $searchDate['year'];

                // 月が入力されている場合
                if (!empty($searchDate['month'])) {
                    $rentDate .= '-' . $searchDate['month'];

                    // 日が入力されている場合
                    if (!empty($searchDate['day'])) {
                        $rentDate .= '-' . $searchDate['day'];
                    }
                }

                $conditions['conditions']['Reservation.created LIKE'] = $rentDate . '%';
            }

            $this->request->data['ReservationCreatedDate']['Created'] = $this->request->query['ReservationCreatedDate'];
        } else {
            $currentYear = date('Y');
            $currentMonth = date('m');
            $conditions['conditions']['Reservation.created LIKE'] = $currentYear . '-' . $currentMonth . '%';
            $this->request->query['ReservationCreatedDate']['year'] = $currentYear;
            $this->request->query['ReservationCreatedDate']['month'] = $currentMonth;
            $this->request->query['ReservationCreatedDate']['day'] = '';
        }

        // キャンセル日時
        if (!empty($this->request->query['ReservationCancelDate'])) {
            $searchDate = $this->request->query['ReservationCancelDate'];
            // 年が入力されている場合
            if (!empty($searchDate['year'])) {
                $rentDate = $searchDate['year'];

                // 月が入力されている場合
                if (!empty($searchDate['month'])) {
                    $rentDate .= '-' . $searchDate['month'];

                    // 日が入力されている場合
                    if (!empty($searchDate['day'])) {
                        $rentDate .= '-' . $searchDate['day'];
                    }
                }

                $conditions['conditions']['Reservation.cancel_datetime LIKE'] = $rentDate . '%';
            }

            $this->request->data['ReservationCancelDate']['Cancel'] = $this->request->query['ReservationCancelDate'];
        } else {
            $currentYear = date('Y');
            $currentMonth = date('m');
            $conditions['conditions']['Reservation.cancel_datetime LIKE'] = $currentYear . '-' . $currentMonth . '%';
            $this->request->query['ReservationCancelDate']['year'] = $currentYear;
            $this->request->query['ReservationCancelDate']['month'] = $currentMonth;
            $this->request->query['ReservationCancelDate']['day'] = '';
        }

        $conditions['order'] = 'Reservation.id desc';

        if ($this->clientData['is_system_admin'] == 0) {
            $officeName = $this->Office->find('list', array(
                'conditions' => array(
                    'Office.client_id' => $this->clientData['client_id'],
                    // 実績が見えなくなるのでフラグ除外
                    //'Office.delete_flg'=>0
                ),
                'fields' => array(
                    'Office.id',
                    'Office.name'
                ),
                'order' => array('Office.sort ASC'),
            ));
            $officeIdArray = array_keys($officeName);
            $conditions['conditions']['OR'] = array(
                'Reservation.rent_office_id' => $officeIdArray,
            );
        }

        /**
         * CSV出力
         */

        if (!empty($this->request->query['getCsv'])) {
            $this->__downloadCsvCancelData($conditions);
        }

        $this->Paginator->settings = $this->Reservation->getReservationDataOptions($conditions);

        $data = $this->Paginator->paginate('Reservation');
        $this->set('reservations', $data);

        $this->set('count', $this->Reservation->getReservationCount($conditions));

        $cancelReasonList = $this->CancelReason->find('list', array('fields' => 'id,reason'));
        $this->set(compact('cancelReasonList'));

        $this->__setViewVars(true, false, true, false);


        $this->request->data['Reservation'] = $this->request->query;
        $this->request->data['ReservationRentDate'] = $this->request->query;
        $this->request->data['ReservationReturnDate'] = $this->request->query;
    }


    /**
     * 予約完了メール再送確認ページ
     *
     * @param string $id
     * @return void
     */
    public function retransmission($id = null)
    {
        if (!$id) {
            $this->Session->setFlash(__('大変申し訳ありませんが処理に失敗いたしました'));
            $this->redirect(array('action' => 'edit', $id));
        } else {
            $count = $this->Reservation->find('count', [
                'conditions' => [
                    'Reservation.id' => $id,
                    'NOT' => ['payment_status' => null],
                ]
            ]);

            $fromStep1 = !($count);

            $options = array(
                'conditions' => array(
                    'Reservation.id' => $id
                ),
                'order' => 'ReservationMail.id DESC',
                'recursive' => -1
            );
            $params = $this->Reservation->getReservationMailData($options);
            $params['domain'] = $_SERVER['HTTP_HOST'];

            $params['Reservation']['rent_date'] = date('Y年m月d日', strtotime($params['Reservation']['rent_datetime']));
            $params['Reservation']['rent_week'] = $this->wday[date('w', strtotime($params['Reservation']['rent_datetime']))];
            $params['Reservation']['rent_time'] = date('H:i', strtotime($params['Reservation']['rent_datetime']));
            $params['Reservation']['return_date'] = date('Y年m月d日', strtotime($params['Reservation']['return_datetime']));
            $params['Reservation']['return_week'] = $this->wday[date('w', strtotime($params['Reservation']['return_datetime']))];
            $params['Reservation']['return_time'] = date('H:i', strtotime($params['Reservation']['return_datetime']));
            $params['Reservation']['fromStep1'] = $fromStep1;
            $params['Commodity']['name'] = mb_convert_kana($params['Commodity']['name'], 'KV');
            $params['RentOffices']['rent_meeting_info'] = mb_convert_kana($params['RentOffices']['rent_meeting_info'], 'KV');
            $params['RentOffices']['rent_office_notification'] = mb_convert_kana($params['RentOffices']['notification'], 'KV');
            $params['ReturnOffices']['return_meeting_info'] = mb_convert_kana($params['ReturnOffices']['return_meeting_info'], 'KV');
            $params['CancelPolicy'] = $this->CancelPolicy->getTextLines($params['Reservation']['client_id'], $params['Reservation']['rent_datetime']);
            // INCIDENT-3044 取消手続料の徴収を廃止する
            //$params['AdvCancelFee'] = $this->CancelPolicy->getAdvCancelFee();

            // 手数料マイナス(クライアントには手数料が入っていない金額を表示)
            $administrativeFee = !empty($params['Reservation']['administrative_fee']) ? $params['Reservation']['administrative_fee'] : 0;
            $params['Reservation']['amount'] -= $administrativeFee;

            $this->set(compact('params'));
        }
    }

    /**
     * 予約完了メール再送
     *
     * @param string $id
     * @return void
     */
    public function again_mail($id)
    {
        if (!$id) {
            $this->Session->setFlash(__('大変申し訳ありませんが処理に失敗いたしました'));
            $this->redirect(array('action'=>'edit',$id));
        } else {
            $count = $this->Reservation->find('count', [
                'conditions' => [
                    'Reservation.id' => $id,
                    'NOT' => ['payment_status' => null],
                ]
            ]);

            $fromStep1 = !($count);

            $options = array(
                'conditions' => array(
                    'Reservation.id' => $id,
                ),
                'order' => 'ReservationMail.id DESC',
                'recursive' => -1
            );
            $params = $this->Reservation->getReservationMailData($options);

            $params['domain'] = $_SERVER['HTTP_HOST'];

            $params['reservation_id'] = $id;
            $params['Reservation']['rent_date'] = date('Y年m月d日', strtotime($params['Reservation']['rent_datetime']));
            $params['Reservation']['rent_week'] = $this->wday[date('w', strtotime($params['Reservation']['rent_datetime']))];
            $params['Reservation']['rent_time'] = date('H:i', strtotime($params['Reservation']['rent_datetime']));
            $params['Reservation']['return_date'] = date('Y年m月d日', strtotime($params['Reservation']['return_datetime']));
            $params['Reservation']['return_week'] = $this->wday[date('w', strtotime($params['Reservation']['return_datetime']))];
            $params['Reservation']['return_time'] = date('H:i', strtotime($params['Reservation']['return_datetime']));
            $params['Reservation']['fromStep1'] = $fromStep1;
            $params['Commodity']['name'] = mb_convert_kana($params['Commodity']['name'], 'KV');
            $params['RentOffices']['rent_meeting_info'] = mb_convert_kana($params['RentOffices']['rent_meeting_info'], 'KV');
            $params['RentOffices']['rent_office_notification'] = mb_convert_kana($params['RentOffices']['notification'], 'KV');
            $params['ReturnOffices']['return_meeting_info'] = mb_convert_kana($params['ReturnOffices']['return_meeting_info'], 'KV');
            $params['CancelPolicy'] = $this->CancelPolicy->getTextLines($params['Reservation']['client_id'], $params['Reservation']['rent_datetime'], false);
            // INCIDENT-3044 取消手続料の徴収を廃止する
            //$params['AdvCancelFee'] = $this->CancelPolicy->getAdvCancelFee();

            // お客様には手数料が入った金額で送付するので、retransmission関数で行っている手数料のマイナス計算は不要

            $this->againSendReservationMail($params);
        }
    }

    /**
     * 印刷用ページ
     *
     * @param string $id
     * @return void
     */
    public function printData($id = null)
    {
        // プリント用のレイアウト
        $this->layout = 'print';
        $this->edit($id);

        // 曜日取得
        $week = array("日", "月", "火", "水", "木", "金", "土");
        $fromTime = strtotime($this->data['Reservation']['rent_datetime']);
        $w = date("w", $fromTime);
        $fromWeek = $week[$w];
        $toTime = strtotime($this->data['Reservation']['return_datetime']);
        $w = date("w", $toTime);
        $toWeek = $week[$w];

        $rent = strtotime(date('Y-m-d', strtotime($this->data['Reservation']['rent_datetime'])));
        $return = strtotime(date('Y-m-d', strtotime($this->data['Reservation']['return_datetime'])));

        // 日数取得
        $dayCount = ceil(($return - $rent) / (3600 * 24));
        $this->set(compact('fromWeek', 'toWeek', 'dayCount'));
    }

    /**
     * ロック解除処理
     *
     * @param string $id
     * @return void
     */
    public function unlocked($id = null)
    {
        $now = date('Y-m-d H:i:s');

        $saveData = $this->UnlockClientEdit->findBy($this->clientData['id'], $id);
        if ($saveData && $saveData['UnlockClientEdit']['end_datetime'] >= $now) { // ロック中
            $this->redirect(array('action' => 'edit', $id));
        }

        $saveData['UnlockClientEdit']['staff_id'] = $this->clientData['id'];
        $saveData['UnlockClientEdit']['reservation_id'] = $id;
        $saveData['UnlockClientEdit']['start_datetime'] = $now;
        $saveData['UnlockClientEdit']['end_datetime'] = date('Y-m-d H:i:s', strtotime($now . "+5 minute"));
        $saveData['UnlockClientEdit']['created_at'] = $now;

        $this->UnlockClientEdit->save($saveData['UnlockClientEdit']);
        $this->redirect(array('action' => 'edit', $id));
    }

    /**
     * 画面表示用変数設定
     *
     * @param boolean $rentSetCurrentMonth
     * @param boolean $rentSetCurrentDay
     * @param boolean $cancelSetCurrentMonth
     * @param boolean $cancelSetCurrentDay
     * @return void
     */
    private function __setViewVars(
        $rentSetCurrentMonth = false,
        $rentSetCurrentDay = false,
        $cancelSetCurrentMonth = false,
        $cancelSetCurrentDay = false
    ) {

        // 日付fromオプション
        $this->set('datetimeRentOptions', array(
            'formName' => 'ReservationRentDate',
            'fieldName' => 'ReservationRentDate',
            'dateFormat' => 'YMD',
            'class' => 'span3',
            'minYear' => '2013',
            'empty' => '---',
            'setCurrentMonth' => $rentSetCurrentMonth,
            'setCurrentDay' => $rentSetCurrentDay
        ));

        $this->set('datetimeRentOptions2', array(
            'formName' => 'ReservationRentDate2',
            'fieldName' => 'ReservationRentDate2',
            'dateFormat' => 'YMD',
            'class' => 'span3',
            'minYear' => '2013',
            'empty' => '---',
            'setCurrentMonth' => $rentSetCurrentMonth,
            'setCurrentDay' => $rentSetCurrentDay
        ));

        $this->set('datetimeReturnOptions', array(
            'formName' => 'ReservationReturnDate',
            'fieldName' => 'ReservationReturnDate',
            'dateFormat' => 'YMD',
            'class' => 'span3',
            'minYear' => '2013',
            'empty' => '---',
            'setCurrentMonth' => false
        ));

        $this->set('datetimeReturnOptions2', array(
            'formName' => 'ReservationReturnDate2',
            'fieldName' => 'ReservationReturnDate2',
            'dateFormat' => 'YMD',
            'class' => 'span3',
            'minYear' => '2013',
            'empty' => '---',
            'setCurrentMonth' => false
        ));


        // 申込日時オプション
        $this->set('datetimeBookingOptions', array(
            'formName' => 'ReservationCreatedDate',
            'fieldName' => 'Created',
            'dateFormat' => 'YMD',
            'class' => 'span3',
            'minYear' => '2013',
            'empty' => '---',
            'setCurrentMonth' => false
        ));

        $this->set('datetimeBookingOptions2', array(
            'formName' => 'ReservationCreatedDate2',
            'fieldName' => 'Created2',
            'dateFormat' => 'YMD',
            'class' => 'span3',
            'minYear' => '2013',
            'empty' => '---',
            'setCurrentMonth' => false
        ));

        // キャンセル日時オプション
        $this->set('datetimeCancelOptions', array(
            'formName' => 'ReservationCancelDate',
            'fieldName' => 'Cancel',
            'dateFormat' => 'YMD',
            'class' => 'span3',
            'minYear' => '2013',
            'empty' => '---',
            'setCurrentMonth' => $cancelSetCurrentMonth,
            'setCurrentDay' => $cancelSetCurrentDay
        ));

        $this->set('datetimeCancelOptions2', array(
            'formName' => 'ReservationCancelDate2',
            'fieldName' => 'Cancel2',
            'dateFormat' => 'YMD',
            'class' => 'span3',
            'minYear' => '2013',
            'empty' => '---',
            'setCurrentMonth' => $cancelSetCurrentMonth,
            'setCurrentDay' => $cancelSetCurrentDay
        ));
    }

    /**
     * 日付データを表示形式に変換
     *
     * @param array $dateArray
     * @return string
     */
    protected function _convertDatetime($dateArray)
    {
        $result = $dateArray['year'].'-'.$dateArray['month'].'-'.$dateArray['day'].' '.$dateArray['hour'].':'.$dateArray['min'].':'.'00';

        return $result;
    }

    /**
     * 日数計算
     *
     * @return int|float
     */
    protected function _getDayCount()
    {
        $fromDate = $this->request->data['Reservation']['rent_datetime'];
        $toDate = $this->request->data['Reservation']['return_datetime'];

        $diffDate = gmmktime(0, 0, 0, $toDate['month'], $toDate['day'], $toDate['year'])
            - gmmktime(0, 0, 0, $fromDate['month'], $fromDate['day'], $fromDate['year']);

        $this->dayCount = $diffDate / (60 * 60 * 24) + 1;

        return $this->dayCount;
    }

    /**
     * 予約データ保存
     *
     * @param array $saveData
     * @return void
     */
    public function saveReservationData($saveData)
    {
        if (!empty($saveData['ReservationChildSheet'])) {
            foreach ($saveData['ReservationChildSheet'] as $child_sheet_id => $sheet) {
                if (empty($sheet['id']) && empty($sheet['count'])) {
                    $saveData['ReservationChildSheet'][$child_sheet_id] = '';
                } else {
                    $saveData['ReservationChildSheet'][$child_sheet_id]['child_sheet_id'] = $child_sheet_id;
                    $saveData['ReservationChildSheet'][$child_sheet_id]['reservation_id'] = $saveData['Reservation']['id'];
                    $saveData['ReservationChildSheet'][$child_sheet_id]['staff_id'] = $this->clientData['id'];
                }
            }
            $saveData['ReservationChildSheet'] = array_filter($saveData['ReservationChildSheet']);
        }

        if (!empty($saveData['ReservationPrivilege'])) {
            foreach ($saveData['ReservationPrivilege'] as $privilege_id => $privilege) {
                if (empty($privilege['id']) && empty($privilege['count'])) {
                    $saveData['ReservationPrivilege'][$privilege_id] = '';
                } else {
                    $saveData['ReservationPrivilege'][$privilege_id]['privilege_id'] = $privilege_id;
                    $saveData['ReservationPrivilege'][$privilege_id]['reservation_id'] = $saveData['Reservation']['id'];
                    $saveData['ReservationPrivilege'][$privilege_id]['staff_id'] = $this->clientData['id'];
                }
            }
            $saveData['ReservationPrivilege'] = array_filter($saveData['ReservationPrivilege']);
        }

        if (!empty($saveData['CancelDetail'])) {
            $saveData['CancelDetail']['reservation_id'] = $saveData['Reservation']['id'];
            $saveData['CancelDetail']['account_code'] = 'MANUAL_INPUT';
            $saveData['CancelDetail']['count'] = 1;
            $saveData['CancelDetail']['remarks'] = '顧客編集画面から手入力';
            $saveData['CancelDetail']['staff_id'] = $this->clientData['id'];

            if ($saveData['Reservation']['amount'] - $saveData['CancelDetail']['amount'] > 0) {
                $saveData['Refund'] = array(
                    'reservation_id' => $saveData['Reservation']['id'],
                    'amount' => $saveData['Reservation']['amount'] - $saveData['CancelDetail']['amount'],
                    'status' => 'SCHEDULED'
                );
            }
        }

        $this->log(sprintf('reservation_id(%d) staff_id(%d) saveData in save method: ', $saveData['Reservation']['id'], $this->clientData['id']) . json_encode($saveData), LOG_DEBUG);

        $saveFlg = true;
        $errorString = '';

        // 予約データ
        $saveData['Reservation']['staff_id'] = $this->clientData['id'];
        if (!$this->Reservation->save($saveData['Reservation'])) {
            $saveFlg = false;
            $errorString = $this->Reservation->getValidationErrorsString();
        }
        // 予約シート
        if (!empty($saveData['ReservationChildSheet'])) {
            if (!$this->ReservationChildSheet->saveMany($saveData['ReservationChildSheet'])) {
                $saveFlg = false;
                $errorString = $this->ReservationChildSheet->getValidationErrorsString();
            }
        }
        // 予約オプション(特典)
        if (!empty($saveData['ReservationPrivilege'])) {
            if (!$this->ReservationPrivilege->saveMany($saveData['ReservationPrivilege'])) {
                $saveFlg = false;
                $errorString = $this->ReservationPrivilege->getValidationErrorsString();
            }
        }
        if (!empty($saveData['CancelDetail'])) {
            if (!$this->CancelDetail->save($saveData['CancelDetail'])) {
                $saveFlg = false;
                $errorString = $this->CancelDetail->getValidationErrorsString();
            }
        }
        if (!empty($saveData['Refund'])) {
            if (!$this->Refund->save($saveData['Refund'])) {
                $saveFlg = false;
                $errorString = $this->Refund->getValidationErrorsString();
            }
        }

        return array($saveFlg, $errorString);
    }

    /**
     * キャンセルメール送信処理
     *
     * @param array $params
     * @return void
     */
    public function sendCancelMail($params)
    {
        // 継承してメールクラスを利用
        $email = new SkyticketCakeEmail('smtp');

        // 社内管理者の場合かつ5社(ニッポン、トヨタ、オリックス、ジェット、日産)以外にメール送信
        if ($this->clientData['is_system_admin'] && !in_array($params['client_id'], Constant::notSendmailClientIds())) {

            // 今日明日出発の場合はクライアント宛メールの件名変化
            $urgent = '';
            $rentDay = new DateTime(date('Y-m-d', strtotime($params['rent_datetime'])));
            $today = new DateTime(date('Y-m-d'));
            $interval = $today->diff($rentDay);
            if ($interval->invert == 0) {
                if (
                    $interval->days == 0
                ) {
                    $urgent = '【本日】';
                } elseif ($interval->days == 1) {
                    $urgent = '【明日】';
                }
            }

            // ユーザには非表示
            $email->non_show_user_flg = 1;
            // クライアントにメール
            $email
                ->viewVars($params)
                ->template('after_reserve', 'suggestions_layout')
                ->subject('【skyticket】' . $urgent . $params['status'] . '　' . $params['last_name'] . '　' . $params['first_name'] . '様 / ' . $params['car_type']);
            $ClientEmail = ClassRegistry::init('ClientEmail');
            $clientEmail = $ClientEmail->getClientEmail($params['client_id']);
            foreach ($clientEmail as $val) {
                if (!empty($val['ClientEmail']['reservation_email'])) {
                    $email->to(trim($val['ClientEmail']['reservation_email']));
                    $email->send();
                }
            }
            // 貸出店舗にメールアドレスが設定されていれば送信
            if (!empty($params['rent_office_reserve_mail'])) {
                $email->to(trim($params['rent_office_reserve_mail']));
                $email->send();
            }
            if (!empty($params['rent_office_reserve_mail2'])) {
                $email->to(trim($params['rent_office_reserve_mail2']));
                $email->send();
            }
            if (!empty($params['rent_office_reserve_mail3'])) {
                $email->to(trim($params['rent_office_reserve_mail3']));
                $email->send();
            }
        }

        // メール送信
        if ($params['is_send_mail']) {
            // ユーザにも表示
            $email->non_show_user_flg = 0;
            $email
            ->viewVars($params)
            ->template('client_cancel', 'suggestions_layout')
            ->subject('【skyticket】レンタカー予約キャンセルのお知らせ')
            ->to(trim($params['email']));

            $email->send();
        }
    }

    /**
     * 予約メール再送処理
     *
     * @param array $params
     * @return void
     */
    public function againSendReservationMail($params)
    {
        $id = $this->params->pass[0];

        // 継承してメールクラスを利用
        // $email = new CakeEmail();
        $email = new SkyticketCakeEmail('smtp');
        $email
            ->viewVars($params)
            ->template('again_reserve', 'suggestions_layout')
            ->subject('【再送】【skyticket】レンタカー予約完了のお知らせ')
            ->to(trim($params['Reservation']['email']));

        $email->send();

        $this->Session->setFlash(__('予約完了メールを再送しました'));
        $this->redirect(array('action'=>'edit',$id));
    }

    /**
     * csvを出力
     *
     * @param array $conditions
     * @param array $conditionsPaymentMethod
     * @return void
     */
    private function __downloadCsvData($conditions, $conditionsPaymentMethod)
    {
        Configure::write('debug', 0); // debugコードを出さない
        $this->autoRender = false; // Viewを使わない

        $for_csv_datetime_format = "%Y/%m/%d %H:%M:%S"; // 日時の書式

        $smokingArray = array(
            0 => '禁煙',
            1 => '喫煙',
            2 => 'どちらでもない'
        );

//        // クエリをslaveに向ける
//        foreach ((array)$this->uses as $model) {
//            $this->$model->setDataSource('default_slave');
//        }

        $acceptPrepay = $this->clientData['Client']['accept_prepay'];

        $reservationConditions = $conditions;
        if (!empty($conditionsPaymentMethod)) {
            $reservationConditions['conditions'][] = $conditionsPaymentMethod;
        }
        $reservationData = $this->Reservation->getReservationData($reservationConditions, $acceptPrepay);

        if (isset($conditions['conditions'])) {
            $encrypt = new Encrypt();
            if (isset($conditions['conditions']['Reservation.last_name like'])) {
                $conditions['conditions']['Reservation.last_name like'] = str_replace("%", "", $conditions['conditions']['Reservation.last_name like']);
                $conditions['conditions']['Reservation.last_name like'] = $encrypt->encrypt($conditions['conditions']['Reservation.last_name like']);
            }
            if (isset($conditions['conditions']['Reservation.first_name like'])) {
                $conditions['conditions']['Reservation.first_name like'] = str_replace("%", "", $conditions['conditions']['Reservation.first_name like']);
                $conditions['conditions']['Reservation.first_name like'] = '%'.$encrypt->encrypt($conditions['conditions']['Reservation.first_name like']).'%';
            }
            if (isset($conditions['conditions']['Reservation.tel'])) {
                $conditions['conditions']['Reservation.tel'] = $encrypt->encrypt($conditions['conditions']['Reservation.tel']);
            }
        }
        // シート
        $childSheetConditions = array(
            'fields' => array(
                'ReservationChildSheet.reservation_id',
                'ReservationChildSheet.count',
                'Privilege.name',
            ),
            'joins' => array(
                array(
                    'type' => 'INNER',
                    'table' => 'reservations',
                    'alias' => 'Reservation',
                    'conditions' => 'Reservation.id = ReservationChildSheet.reservation_id',
                ),
                array(
                    'type' => 'INNER',
                    'table' => 'privileges',
                    'alias' => 'Privilege',
                    'conditions' => 'Privilege.id = ReservationChildSheet.child_sheet_id',
                ),
                array(
                    'type' => 'INNER',
                    'table' => 'commodity_items',
                    'alias' => 'CommodityItem',
                    'conditions' => 'CommodityItem.id = Reservation.commodity_item_id',
                ),
                array(
                    'type' => 'INNER',
                    'table' => 'commodities',
                    'alias' => 'Commodity',
                    'conditions' => 'Commodity.id = CommodityItem.commodity_id',
                ),
            ),
            'conditions' => $conditions['conditions'] + array(
                'ReservationChildSheet.delete_flg' => 0,
                'Privilege.client_id' => $this->clientData['Client']['id'],
                'Privilege.option_flg' => 1,
            ),
            'recursive' => -1,
        );

        $tmpChildSheetData = $this->ReservationChildSheet->find('all', $childSheetConditions);
        unset($childSheetConditions);

        $childSheetData = array();
        foreach ((array)$tmpChildSheetData as $val) {
            $item['count'] = $val['ReservationChildSheet']['count'];
            $item['name'] = $val['Privilege']['name'];

            $childSheetData[$val['ReservationChildSheet']['reservation_id']][] = $item;
        }
        unset($tmpChildSheetData);

        // 特典
        $privilegeConditions = array(
            'fields' => array(
                'ReservationPrivilege.reservation_id',
                'ReservationPrivilege.count',
                'Privilege.name',
            ),
            'joins' => array(
                array(
                    'type' => 'INNER',
                    'table' => 'reservations',
                    'alias' => 'Reservation',
                    'conditions' => 'Reservation.id = ReservationPrivilege.reservation_id',
                ),
                array(
                    'type' => 'INNER',
                    'table' => 'commodity_items',
                    'alias' => 'CommodityItem',
                    'conditions' => 'CommodityItem.id = Reservation.commodity_item_id',
                ),
                array(
                    'type' => 'INNER',
                    'table' => 'commodities',
                    'alias' => 'Commodity',
                    'conditions' => 'Commodity.id = CommodityItem.commodity_id',
                ),
                array(
                    'type' => 'INNER',
                    'table' => 'privileges',
                    'alias' => 'Privilege',
                    'conditions' => 'Privilege.id = ReservationPrivilege.privilege_id',
                ),
            ),
            'conditions' => $conditions['conditions'] + array(
                'ReservationPrivilege.delete_flg' => 0,
                'Privilege.client_id' => $this->clientData['Client']['id'],
                'Privilege.option_flg' => 0,
            ),
            'recursive' => -1,
        );

        $tmpPrivilegeData = $this->ReservationPrivilege->find('all', $privilegeConditions);
        unset($privilegeConditions);

        $privilegeData = array();
        foreach ((array)$tmpPrivilegeData as $val) {
            $item['count'] = $val['ReservationPrivilege']['count'];
            $item['name'] = $val['Privilege']['name'];

            $privilegeData[$val['ReservationPrivilege']['reservation_id']][] = $item;
        }
        unset($tmpPrivilegeData);

        // 基本料金
        $basicPricesConditions = array(
            'fields' => array(
                'ReservationDetail.reservation_id',
                'ReservationDetail.detail_type_id',
                'ReservationDetail.amount',
            ),
            'joins' => array(
                array(
                    'type' => 'INNER',
                    'table' => 'reservations',
                    'alias' => 'Reservation',
                    'conditions' => 'Reservation.id = ReservationDetail.reservation_id',
                ),
                array(
                    'type' => 'INNER',
                    'table' => 'commodity_items',
                    'alias' => 'CommodityItem',
                    'conditions' => 'CommodityItem.id = Reservation.commodity_item_id',
                ),
                array(
                    'type' => 'INNER',
                    'table' => 'commodities',
                    'alias' => 'Commodity',
                    'conditions' => 'Commodity.id = CommodityItem.commodity_id',
                ),
            ),
            'conditions' => $conditions['conditions'],
            'recursive' => -1,
        );
        $basicPrices = $this->ReservationDetail->find('all', $basicPricesConditions);
        unset($basicPricesConditions);

        foreach ((array)$this->uses as $model) {
            $this->$model->setDataSource('default');
        }

        $basicPriceArray = array();
        foreach ($basicPrices as $basicPrice) {
            $reservationId = $basicPrice['ReservationDetail']['reservation_id'];
            $detailTypeId = $basicPrice['ReservationDetail']['detail_type_id'];
            $basicPriceArray[$reservationId][$detailTypeId] = $basicPrice['ReservationDetail']['amount'];
        }
        unset($basicPrices);

        /**
         * CSVの処理
         */
        $csvFile = date('YmdHis'). '.csv';

        // ヘッダ出力
        header("Content-disposition: attachment; filename=" . $csvFile);
        header("Content-type: application/octet-stream; name=" . $csvFile);

        // ストリーム出力
        $fp = @fopen('php://output', 'w');
        if (!$fp) {
            exit;
        }

        // SJIS指定
        stream_filter_prepend($fp, 'convert.iconv.utf-8/cp932//TRANSLIT');

        $csvData = '事業者名,予約番号,氏名カナ,ご利用人数(大人),ご利用人数(子供),'.
            'ご利用人数(幼児),出発日,返却日,到着便名,出発便名,電話番号,メールアドレス,'.
            'ステータス,合計金額,基本料金合計,免責補償料金,乗捨料金,オプション料金合計,'.
            'オプション項目,深夜手数料,お申込みプラン,禁煙/喫煙,車両クラス,車両タイプ,'.
            '車両台数,貸出店舗,返却店舗,シート,返信状況,申込み日時,キャンセル日時,管理番号,貸出店舗コード,返却店舗コード';
        if ($acceptPrepay) {
            $csvData .= ',支払方法';
        }
        if ($this->clientData['Client']['is_managed_package']) {
            $csvData .= ',販売方法';
        }
        $csvData .= "\r\n";
        fwrite($fp, $csvData);

        foreach ($reservationData as $key => $val) {
            $reservationId = $val['Reservation']['id'];
            $salesType = ($this->clientData['Client']['is_managed_package']) ? ",{$this->salesType[$val['Commodity']['sales_type']]}" : '';

            // オプション料金
            $optionPrice = !empty($basicPriceArray[$reservationId][2]) ? $basicPriceArray[$reservationId][2] : 0;
            // チャイルドシート
            $childSheetPrice = !empty($basicPriceArray[$reservationId][3]) ? $basicPriceArray[$reservationId][3] : 0;
            $optionPrice = $optionPrice + $childSheetPrice;

            // キャンセル日時
            $cancelDate = '';
            if ($val['Reservation']['reservation_status_id'] == 3) {
                $cancelDate = date('Y/m/d H:i:s', strtotime($val['Reservation']['cancel_datetime']));
            }

            if ($val['Reservation']['mail_status'] == 0) {
                $mailStatus = '未返信';
            } elseif ($val['Reservation']['mail_status'] == 1) {
                $mailStatus = '返信済み';
            } elseif ($val['Reservation']['mail_status'] == 2) {
                $mailStatus = '対応完了';
            } elseif ($val['Reservation']['mail_status'] == 3) {
                $mailStatus = '設定なし';
            }

            $childSheet = '';
            foreach ((array)$childSheetData[$val['Reservation']['id']] as $value) {
                if (!empty($value['count'])) {
                    if (!empty($childSheet)) {
                        $childSheet .= ' ';
                    }
                    $childSheet .= $value['name'].'×'.$value['count'];
                }
            }

            $privilege = '';
            foreach ((array)$privilegeData[$val['Reservation']['id']] as $value) {
                if (!empty($value['count'])) {
                    if (empty($privilege)) {
                        $privilege .= ' ';
                    }
                    $privilege .= $value['name'].'×'.$value['count'];
                }
            }

            $csvData =
                '株式会社アドベンチャー' . ',' .
                $val['Reservation']['reservation_key'] . ',' .
                
                $val['Reservation']['last_name']. ' ' . $val['Reservation']['first_name']. ',' .


                $val['Reservation']['adults_count']. ',' .
                $val['Reservation']['children_count']. ',' .
                $val['Reservation']['infants_count']. ',' .

                CakeTime::format($val['Reservation']['rent_datetime'], $for_csv_datetime_format). ',' .
                CakeTime::format($val['Reservation']['return_datetime'], $for_csv_datetime_format). ',' .

                $val['Reservation']['arrival_flight_number'] . ',' .
                $val['Reservation']['departure_flight_number'] . ',' .
                $val['Reservation']['tel']. ',' .
                $val['Reservation']['email']. ',' .

                $val['ReservationStatus']['name']. ',' .

                $val['Reservation']['amount']. ',' .
                // 基本料金合計
                $basicPriceArray[$reservationId][1] . ',' .
                $basicPriceArray[$reservationId][6] . ',' .
                $basicPriceArray[$reservationId][4] . ',' .
                $optionPrice . ',' .

                $privilege . ','.

                $basicPriceArray[$reservationId][5] . ',' .

                $this->commaEscape4csv($val['Commodity']['name']). ',' .
                $smokingArray[$val['Commodity']['smoking_flg']] . ',' .
                str_replace(',', '', $val['CarClass']['name']) . ',' .
                $val['CarType']['name']. ',' .
                $val['Reservation']['cars_count'] . ',' .
                $val['RentOffice']['name']. ',' .
                $val['ReturnOffice']['name']. ',' .
                $childSheet .',' .
                $mailStatus . ',' .
                CakeTime::format($val['Reservation']['created'], $for_csv_datetime_format). ',' .
                   $cancelDate.','. $val['Reservation']['control_number'] . ',' .
                $val['RentOffice']['office_code']. ',' .
                $val['ReturnOffice']['office_code'];
            if ($acceptPrepay) {
                $csvData .= ',' . (($val['Commodity']['sales_type'] == Constant::SALES_TYPE_ARRANGED) ? (isset($val['Reservation']['payment_status']) ? 'WEB事前決済' : '現地精算') : ($val['Reservation']['sales_price'] > 0 ? 'WEB事前決済' : '現地精算'));
            }
            $csvData .= $salesType;
            $csvData .= "\r\n";

            fwrite($fp, $csvData);
        }

        fclose($fp);
        exit;
    }


    /**
     * キャンセル一覧csvを出力
     *
     * @param array $conditions
     * @return void
     */
    private function __downloadCsvCancelData($conditions)
    {
        Configure::write('debug', 0); // debugコードを出さない
        $this->autoRender = false; // Viewを使わない

        $for_csv_datetime_format = "%Y/%m/%d %H:%M:%S"; // 日時の書式

        /**
         * データ取得
         */
        $this->Reservation->unbindModel(array(
            'belongsTo' => array(
                'CommodityItem',
            )
        ), false);

//        // クエリをslaveに向ける
//        $this->Reservation->setDataSource('default_slave');
        $reservationData = $this->Reservation->find('all', $this->Reservation->getReservationDataOptions($conditions));
//        $this->Reservation->setDataSource('default');

        /**
         * CSVの処理
         */

        $csvFile = date('YmdHis'). '.csv';

        // ヘッダ出力
        header("Content-disposition: attachment; filename=" . $csvFile);
        header("Content-type: application/octet-stream; name=" . $csvFile);

        // ストリーム出力
        $fp = @fopen('php://output', 'w');
        if (!$fp) {
            exit;
        }

        // SJIS指定
        stream_filter_prepend($fp, 'convert.iconv.utf-8/cp932//TRANSLIT');

        $csvData = '予約番号,商標,プラン,車両クラス,車両タイプ,出発店舗,返却店舗,予約台数,合計金額,ご利用期間,申込日時,'.
                'キャンセル日時,キャンセル理由,キャンセル理由詳細' . "\r\n";
        fwrite($fp, $csvData);

        foreach ($reservationData as $key => $val) {
            $csvData =
                $val['Reservation']['reservation_key'] . ',' .

                $val['Client']['name'] .',' .

                $this->commaEscape4csv($val['Commodity']['name']) .',' .

                str_replace(',', '', $val['CarClasses']['name']) . ',' .
                $val['CarType']['name']. ',' .

                $val['RentOffices']['name']. ',' .

                $val['ReturnOffices']['name']. ',' .

                $val['Reservation']['cars_count']. ',' .

                $val['Reservation']['amount']. ',' .

                CakeTime::format($val['Reservation']['rent_datetime'], $for_csv_datetime_format).'～'.
                CakeTime::format($val['Reservation']['return_datetime'], $for_csv_datetime_format).','.

                CakeTime::format($val['Reservation']['created'], $for_csv_datetime_format). ',' .

                CakeTime::format($val['Reservation']['cancel_datetime'], $for_csv_datetime_format). ',' .

                $val['CancelReason']['reason']. ',' .
                str_replace(array("\r\n","\r","\n"), '', $val['Reservation']['cancel_remark']). ',' ."\r\n";

            fwrite($fp, $csvData);
        }

        fclose($fp);
        exit;
    }

    /**
     * 対応履歴保存処理
     *
     * @return string
     * @throws NotFoundException
     */
    public function saveResponseHistory()
    {
        $this->autoRender = false;
        if (!$this->request->isAjax()) {
            throw new NotFoundException();
        }
        try {
            $this->MessageBoard->save(array(
                'reservation_id' => $this->request->data['reservation_id'],
                'category_cd' => 'RESERVATION_DETAIL',
                'message' => $this->request->data['message'],
                'staff_id' => $this->clientData['id']
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
     * カンマがあれば、ダブルコーテーションのエスケープ
     *
     * @param string $name
     * @return string
     */
    public function commaEscape4csv($str)
    {
        if (!(strstr($str, ',') === false)) {
            $str = preg_replace('/"/', '""', $str);
            $str = '"' . $str . '"';
        }
        return $str;
    }

    /**
     * 予約ステータス、キャンセル理由から入金ステータスを返す
     *
     * @param int $statuId
     * @param int $cancelReasonId
     * @return string
     * @throws Exception
     */
    private function __getPaymentStatus($statuId, $cancelReasonId)
    {
        switch ($statuId) {
            case Constant::STATUS_CANCEL: // キャンセル
                if ($cancelReasonId == 1) { // お客様都合によるキャンセル(ここは通らないはず)
                    return 'REFUNDED'; // 返金処理済
                } else {
                    return 'REFUND_REQUEST'; // 返金依頼中
                }
                // no break
            default:
                throw new Exception('返金処理：予約ステータス異常');
        }
    }

    /**
     * 変更判定
     *
     * @param array $data
     * @return boolean
     */
    private function isChanged($data)
    {
        foreach ((array)$data['ReservationDefault'] as $k => $v) {
            if ($v != $data['Reservation'][$k]) {
                return true;
            }
        }
        foreach ((array)$data['ReservationPrivilegeDefault'] as $k => $v) {
            if ($v['count'] != $data['ReservationPrivilege'][$k]['count'] ||
                $v['price'] != $data['ReservationPrivilege'][$k]['price']) {
                return true;
            }
        }
        foreach ((array)$data['ReservationChildSheetDefault'] as $k => $v) {
            if ($v['count'] != $data['ReservationChildSheet'][$k]['count'] ||
                $v['price'] != $data['ReservationChildSheet'][$k]['price']) {
                return true;
            }
        }
        return false;
    }
}
