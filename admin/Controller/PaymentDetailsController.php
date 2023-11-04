<?php

App::uses('AppController', 'Controller');
App::uses('CakeEmail', 'Network/Email');
App::uses('SkyticketCakeEmail', 'Vendor');

/**
 * PaymentDetails Controller
 *
 * @property PaymentEconComponent $PaymentEcon
 * @property PaymentAPIComponent $PaymentAPI
 * @property CancelFeeCalculationComponent $CancelFeeCalculation
 * @property Reservation $Reservation
 * @property ReservationDetail $ReservationDetail
 * @property ReservationCommodity $ReservationCommodity
 * @property Payment $Payment
 * @property PaymentDetail $PaymentDetail
 * @property CancelDetail $CancelDetail
 * @property CancelFee $CancelFee
 * @property MessageBoard $MessageBoard
 * @property Refund $Refund
 * @property RefundRequest $RefundRequest
 */
class PaymentDetailsController extends AppController
{

    public $components = array('PaymentEcon', 'CancelFeeCalculation', 'PaymentAPI', 'Receipt');

    public $uses = array(
        'Reservation',
        'ReservationDetail',
        'Payment',
        'PaymentDetail',
        'CancelDetail',
        'CancelFee',
        'MessageBoard',
        'Refund',
        'RefundRequest',
        'PaymentToken',
        'PublicHoliday',
        'CmThReceipt',
        'Prefecture'
    );

    public $reservationId;
    public $cmApplicationId;
    public $paymentFlag;
    public $paidPrice;
    public $refundPrice;
    public $refundedPrice;

    /**
     * 前処理
     *
     * @return void
     */
    public function beforeFilter()
    {
        parent::beforeFilter();

        if (isset($this->request->data['cm_application_id'])) {
            $this->cmApplicationId = $this->request->data['cm_application_id'];
        } else {
            $this->reservationId = isset($this->params['named']['reservation_id']) ?
                $this->params['named']['reservation_id'] :
                    (isset($this->request->data['reservation_id']) ?
                        $this->request->data['reservation_id'] : 0
                    );
            $this->cmApplicationId = !empty($this->reservationId) ?
                $this->Reservation->getCmApplicationId($this->reservationId)['CmThApplicationDetail']['cm_application_id'] : 0;
        }

        $this->set('receiptMailFlg', [
            '0' => 'ＤＬ',
            '1' => '郵送',
            '2' => 'メール',
            '3' => '手書き',
        ]);
        $this->set('receiptStatus', [
            '0' => '未送付',
            '1' => '送付済',
        ]);
        $this->set('receiptDeleteFlg', [
            '0' => '有効',
            '1' => '無効',
        ]);
        $this->set('receiptLangId', [
            '1' => '日本語',
            '2' => '英語',
        ]);

        // 新規決済APIフラグ（カートIDが存在するかどうか）
        $this->paymentFlag = $this->PaymentAPI->getPaymentFlag($this->cmApplicationId);

        if ($this->paymentFlag) {
            // カートID取得API
            $targetUrl = $this->PaymentAPI->getApiUrlByApplicationId($this->cmApplicationId);
            $res = $this->PaymentAPI->runApi($targetUrl, 'get');
            $body = json_decode($res->body, true);
            if (empty($body)) {
                $this->log("failed URL:".print_r($targetUrl, true), LOG_ERROR);
                $this->log("failed result:".print_r($res, true), LOG_ERROR);
                // 新カート基盤で取れなかった場合、旧カート基盤も見る
                $targetUrl = $this->PaymentAPI->getApiUrlOldByApplicationId($this->cmApplicationId);
                $res = $this->PaymentAPI->runApi($targetUrl, 'get');
                if ($res->code === '404') {
                    $this->log("failed URL:".print_r($targetUrl, true), LOG_ERROR);
                    $this->log("failed result:".print_r($res, true), LOG_ERROR);
                    return;
                }
                $cartId = json_decode($res->body, true)['cart_id'];
            } else {
                $cartId = $body[0]['cartId'];
            }

            // 入金情報取得API
            $targetUrl = $this->PaymentAPI->getApiUrlPayments();
            $res = $this->PaymentAPI->runApi($targetUrl, 'get', 'cartId='. $cartId . '&serviceCd=rc');

            if ($res->code !== '200') {
                $this->log("failed URL:".print_r($targetUrl, true), LOG_ERROR);
                $this->log("failed result:".print_r($res, true), LOG_ERROR);
                return;
            }
            $res = json_decode($res->body);

            $this->paidPrice = $res->paidPrice;
            $this->refundPrice = $res->refundPrice;
            $this->refundedPrice = $res->refundedPrice;
        }
    }

    /**
     * 入金詳細表示
     *
     * @return void
     */
    public function index()
    {
        $reservation_id = $this->reservationId;

        if (!$this->__view($reservation_id)) {
            throw new NotFoundException(__('Invalid reservation'));
        }
    }

    /**
     * 入金情報表示
     *
     * @param string $reservation_id
     * @return bool
     */
    private function __view($reservation_id)
    {

        $reservationData = $this->Reservation->findById($reservation_id);

        if (!$reservationData) {
            return false;
        }

        if (!$this->__setMessageBoardView($reservation_id)) { // 伝言板
            return false;
        }

        if (!$this->__setCmApplicationId()) {
            return false;
        }

        if (!$this->__setReservationRelationshipView($reservationData)) { // 予約情報 + econ入金情報
            return false;
        }

        if (!$this->__setReceiptView($reservation_id)) { // 領収書情報
            return false;
        }

        if (!$this->__setPaymentDetailView($reservationData)) { // 入金明細
            return false;
        }

        if (!$this->__setCancelDetailView($reservationData)) { // キャンセル明細
            return false;
        }

        if (!$this->__setRefundView($reservationData)) {
            return false;
        }

        return true;
    }

    /**
     * cmApplicationId設定
     *
     * @return void
     */
    private function __setCmApplicationId()
    {

        $this->set('cmApplicationId', $this->cmApplicationId);

        return true;
    }

    /**
     * メッセージボード情報設定
     *
     * @param string $reservation_id
     * @return bool
     */
    private function __setMessageBoardView($reservation_id)
    {
        $messageBoards = $this->MessageBoard->find('all', [
            'conditions' => [
                'MessageBoard.reservation_id' => $reservation_id,
                'MessageBoard.category_cd' => 'PAYMENT_DETAIL', // カテゴリコードには画面名を入れる
                'MessageBoard.delete_flg' => 0
            ]
        ]);

        $this->set('MessageBoards', $messageBoards);

        return true;
    }

    /**
     * 予約関連情報設定
     *
     * @param array $reservationData
     * @return bool
     */
    private function __setReservationRelationshipView($reservationData)
    {
        $cond = $this->Payment->makePRConditions(['reservation_id' => $reservationData['Reservation']['id'], 'payment_result' => 'success']);
        $payment = $this->Payment->find('all', $cond);

        $PRView = $this->Payment->changeViewList($payment);

        $PRs = $this->Reservation->makePRConditions(['reservation_id' => $reservationData['Reservation']['id']]);
        
        $this->set('PR', (isset($PRs[0])) ? $PRs[0] : []); // 予約情報
        $this->set('Payments', $PRView); // 入金情報

        // 入金ステータスのセレクトボックス
        $paymentStatusSelect = [];
        $paymentStatus = Constant::paymentStatus();
        // オペレーション上ありえるステータスの組み合わせに限定する
        if ($PRs[0]['Reservation']['payment_status'] == 'PAYED') {
            $paymentStatusSelect['PAYED'] = $paymentStatus['PAYED'];
            $paymentStatusSelect['TMP_REFUND_REQUEST'] = $paymentStatus['TMP_REFUND_REQUEST'];
        } elseif ($PRs[0]['Reservation']['payment_status'] == 'REFUND_REQUEST') {
            $paymentStatusSelect['REFUND_REQUEST'] = $paymentStatus['REFUND_REQUEST'];
            $paymentStatusSelect['REFUNDED'] = $paymentStatus['REFUNDED'];
            $paymentStatusSelect['WAIT_REFUND'] = $paymentStatus['WAIT_REFUND'];
            // キャンセル時のみここを通るはずだけど判定をしておく
            if ($PRs[0]['Reservation']['reservation_status_id'] == '3') {
                $paymentStatusSelect['NO_REFUND'] = $paymentStatus['NO_REFUND'];
            }
        } elseif ($PRs[0]['Reservation']['payment_status'] == 'WAIT_REFUND') {
            $paymentStatusSelect['WAIT_REFUND'] = $paymentStatus['WAIT_REFUND'];
            // キャンセル時のみここを通るはずだけど判定をしておく
            if ($PRs[0]['Reservation']['reservation_status_id'] == '3') {
                $paymentStatusSelect['NO_REFUND'] = $paymentStatus['NO_REFUND'];
            }
        } elseif ($PRs[0]['Reservation']['payment_status'] == 'REFUNDED') {
            $paymentStatusSelect['REFUNDED'] = $paymentStatus['REFUNDED'];
            $paymentStatusSelect['WAIT_REFUND'] = $paymentStatus['WAIT_REFUND'];
        } elseif ($PRs[0]['Reservation']['payment_status'] == 'NO_REFUND') {
            $paymentStatusSelect['REFUND_REQUEST'] = $paymentStatus['REFUND_REQUEST'];
            $paymentStatusSelect['NO_REFUND'] = $paymentStatus['NO_REFUND'];
        } else {
            $paymentStatusSelect[$PRs[0]['Reservation']['payment_status']] = $paymentStatus[$PRs[0]['Reservation']['payment_status']];
        }

        $this->set('paymentStatusSelect', $paymentStatusSelect);
        $this->set('defaultPaymentStatus', $PRs[0]['Reservation']['payment_status']);

        return true;
    }

    /**
     * レシート情報設定
     *
     * @param string $reservation_id
     * @return bool
     */
    private function __setReceiptView($reservation_id)
    {
        $prefectures = $this->Prefecture->find('list');
        $this->set('prefectures', $prefectures);

        $receipts = $this->CmThReceipt->find('all', [
            'conditions' => ['CmThReceipt.cm_application_id' => $this->cmApplicationId],
            'recursive' => -1
        ]);
        $this->set('Receipts', $receipts);

        return true;
    }

    /**
     * 支払明細情報設定
     *
     * @param array $reservationData
     * @return bool
     */
    private function __setPaymentDetailView($reservationData)
    {
        $reservationDetailAmountSum = 0;

        $reservationDetails = $this->ReservationDetail->find('all', [
            'conditions' => [
                'reservation_id' => $reservationData['Reservation']['id']
            ]
        ]);

        $this->log(print_r($reservationDetails, true), LOG_DEBUG);
        // 予約詳細の合計額
        foreach ($reservationDetails as $reservationDetail) {
            $reservationDetailAmountSum += $reservationDetail['ReservationDetail']['amount'];
        }

        // 追加科目があれば取得
        $paymentDetails = $this->PaymentDetail->find('all', [
            'conditions' => [
                'reservation_id' => $reservationData['Reservation']['id'],
                'delete_flg' => 0
            ]
        ]);

        $paymentDetailAmountSum = 0;
        foreach ($paymentDetails as $paymentDetail) {
            $paymentDetailAmountSum += ($paymentDetail['PaymentDetail']['amount'] * $paymentDetail['PaymentDetail']['count']);
        }

        // 予約時入金 合計金額
        $reservationDetailAmountSum += $reservationData['Reservation']['administrative_fee'];

        // 調整後 合計金額
        $totalAmount = $reservationDetailAmountSum + $paymentDetailAmountSum;
        // 入金済み
        if ($this->paymentFlag) {
            $payedAmonut = $this->paidPrice;
        } else {
            $payedAmonut = $reservationDetailAmountSum;
        }

        $this->set('reservationDetails', $reservationDetails);
        $this->set('reservationDetailAmountSum', $reservationDetailAmountSum);
        $this->set('administrative_fee', $reservationData['Reservation']['administrative_fee']);
        $this->set('paymentDetails', $paymentDetails);
        $this->set('totalAmount', $totalAmount);
        $this->set('payedAmount', $payedAmonut);

        return true;
    }

    /**
     * キャンセル明細情報設定
     *
     * @param array $reservationData
     * @return bool
     */
    private function __setCancelDetailView($reservationData)
    {
        $testCancelFeeData = $this->CancelFeeCalculation->calculate($reservationData['Reservation']['id'], false);

        $cancelDetails = $this->CancelDetail->find('all', [
            'conditions' => [
                'reservation_id' => $reservationData['Reservation']['id'],
            ]
        ]);

        $cancelDetailAmountSum = 0;
        foreach ($cancelDetails as $cancelDetail) {
            $cancelDetailAmountSum += $cancelDetail['CancelDetail']['amount'] * $cancelDetail['CancelDetail']['count'];
        }

        $canEdit = $this->Reservation->canEditReservation($reservationData['Reservation']['id'], $this->cdata['id']);

        $this->set('testCancelFeeData', $testCancelFeeData);
        $this->set('cancelDetails', $cancelDetails);
        $this->set('cancelDetailAmountSum', $cancelDetailAmountSum);
        $this->set('canEdit', $canEdit);

        return true;
    }

    /**
     * 返金情報設定
     *
     * @param array $reservationData
     * @return bool
     */
    private function __setRefundView($reservationData)
    {
        if ($reservationData['Reservation']['payment_status'] == 'AUTH' ||
            $reservationData['Reservation']['payment_status'] == 'AUTH_CANCEL') { // 与信では返金できない+入金もされていない
            $this->set('payedAmount', 0);
            $this->set('schedulRefundAmount', 0);
            $this->set('refundingAmount', 0);
            $this->set('refundedAmount', 0);
            $this->set('remainingAmount', 0);
        } else {
            $amount = 0;
            if ($this->paymentFlag) {
                $amount = $this->paidPrice + $this->refundPrice + $this->refundedPrice;
            } else {
                $payments = $this->viewVars['Payments'];
                foreach ($payments as $payment) {
                    $ret = $this->PaymentEcon->inquiry($payment['Payment']['order_id']); // ECON API で残額を取得
                    if (isset($ret['data']['amount']) && ($ret['data']['status'] == 0 || $ret['data']['status'] == 1)) { // 0:与信取得済、1:計上済
                        $amount += (int)$ret['data']['amount'];
                    }
                }
            }

            // 返金情報取得
            $refunds = $this->Refund->find('all', [
                'conditions' => [
                    'reservation_id' => $reservationData['Reservation']['id']
                ]
            ]);

            $schedulRefundAmount = 0; // 返金予定額
            $refundingAmount = 0; // 返金依頼中
            $refundedAmount = 0; // 返金済額
            foreach ((array)$refunds as $refund) {
                switch ($refund['Refund']['status']) {
                    case Constant::STATUS_SCHEDULED_REFUND:
                        $schedulRefundAmount += $refund['Refund']['amount'];
                        break;
                    case Constant::STATUS_REFUNDING:
                        $refundingAmount += $refund['Refund']['amount'];
                        break;
                    case Constant::STATUS_REFUNDED:
                        $refundedAmount += $refund['Refund']['amount'];
                        break;
                    default:
                        break;
                }
            }

            $remainingAmount = $amount;

            if ($reservationData['Reservation']['cancel_datetime'] !== '0000-00-00 00:00:00') {
                $this->log("payedAmount:".$this->viewVars['payedAmount'], LOG_DEBUG);
                $this->log("cancelDetailAmountSum:".$this->viewVars['cancelDetailAmountSum'], LOG_DEBUG);
                $this->log("refundedAmount:".$refundedAmount, LOG_DEBUG);
            }

            $this->set('schedulRefundAmount', $schedulRefundAmount);
            $this->set('refundingAmount', $refundingAmount);
            $this->set('refundedAmount', $refundedAmount);
            $this->set('remainingAmount', $remainingAmount);
        }
        return true;
    }

    /**
     * Ajaxでの支払情報取得処理
     *
     * @return array
     */
    public function ajaxPaymentInfo()
    {
        $this->autoRender = false;

        if (!$this->request->is('ajax')) {
            return false;
        }

        return $this->getPaymentInfo($this->request->data['cm_application_id']);
    }

    /**
     * 支払い情報取得
     *
     * @param string $cmApplicationId
     * @return mixed
     */
    public function getPaymentInfo($cmApplicationId)
    {
        // カートID取得API
        $targetUrl = $this->PaymentAPI->getApiUrlByApplicationId($cmApplicationId);
        $res = $this->PaymentAPI->runApi($targetUrl, 'get');
        $body = json_decode($res->body, true);
        if (empty($body)) {
            $this->log("failed URL:".print_r($targetUrl, true), LOG_ERROR);
            $this->log("failed result:".print_r($res, true), LOG_ERROR);
            // 新カート基盤で取れなかった場合、旧カート基盤も見る
            $targetUrl = $this->PaymentAPI->getApiUrlOldByApplicationId($cmApplicationId);
            $res = $this->PaymentAPI->runApi($targetUrl, 'get');
            if ($res->code === '404') {
                $this->log("failed URL:".print_r($targetUrl, true), LOG_ERROR);
                $this->log("failed result:".print_r($res, true), LOG_ERROR);
                return;
            }
            $cartId = json_decode($res->body, true)['cart_id'];
        } else {
            $cartId = $body[0]['cartId'];
        }


        // 入金情報取得API
        $targetUrl = $this->PaymentAPI->getApiUrlPayments();
        $res = $this->PaymentAPI->runApi($targetUrl, 'get', 'cartId='. $cartId . '&serviceCd=rc');

        if ($res->code !== '200') {
            $this->log("failed URL:".print_r($targetUrl, true), LOG_ERROR);
            $this->log("failed result:".print_r($res, true), LOG_ERROR);
        }


        return $res;
    }

    /**
     * Ajaxでのメッセージボード保存処理
     *
     * @return array|false
     */
    public function ajaxSaveMessageBoard()
    {
        $this->autoRender = false;

        if ($this->request->is('ajax')) {
            $this->MessageBoard->set('staff_id', $this->cdata['id']);
            $this->MessageBoard->set('category_cd', 'PAYMENT_DETAIL');
            try {
                if ($this->MessageBoard->save($this->request->data)) {
                    return json_encode(['ret' => 'ok']);
                } else {
                    return json_encode(['ret' => 'error', 'message' => 'save error']);
                }
            } catch (Exception $e) {
                return json_encode(['ret' => 'error', 'message' => $e->getMessage()]);
            }
        }
        return false;
    }

    /**
     * Ajaxでの予約保存処理
     *
     * @return array|false
     */
    public function ajaxSaveReservation()
    {
        $this->autoRender = false;

        if ($this->request->is('ajax')) {
            $this->Reservation->set('staff_id', $this->cdata['id']);
            try {
                if ($this->Reservation->save($this->request->data)) {
                    if ($this->request->data['payment_status'] === 'REFUNDED') {
                        // 取り残されたREFUNDING更新処理
                        $this->Refund->updateAll(
                            array(
                                'status' => "'".Constant::STATUS_REFUNDED."'",
                                'refunded' => "'". date('Y-m-d H:i:s'). "'"
                            ),
                            array('reservation_id' => $this->request->data['id'])
                        );
                    }
                    return json_encode(['ret' => 'ok']);
                } else {
                    return json_encode(['ret' => 'error', 'message' => 'save error']);
                }
            } catch (Exception $e) {
                return json_encode(['ret' => 'error', 'message' => $e->getMessage()]);
            }
        }
        return false;
    }

    /**
     * Ajaxでの支払明細保存処理
     *
     * @return array|false
     */
    public function ajaxSavePaymentDetail()
    {
        $this->autoRender = false;

        if ($this->request->is('ajax')) {
            $this->log(print_r($this->request->data, true), LOG_DEBUG);

            $reservationId = $this->request->data['reservation_id'];

            $this->PaymentDetail->set('staff_id', $this->cdata['id']);
            $this->PaymentDetail->begin();
            try {
                $reservationData = [
                    'id' => $reservationId,
                    'payment_limit_datetime' => date('Y-m-d', strtotime('+3 day')).' 23:59:59',
                    'payment_status' => '', // 追加入金が必要になった場合一旦未入金に戻す
                    'staff_id' => $this->cdata['id']
                ];

                if (($this->request->data['account_code'] == 'ADJUST_DIFFERENCE' && $this->request->data['amount'] > 0)
                || $this->request->data['account_code'] == 'ADJUST_ADDITION') {
                    $this->Reservation->save($reservationData);
                }

                if ($this->PaymentDetail->save($this->request->data)) {
                    if ($this->request->data['amount'] > 0) { // マイナスで相殺処理とかした場合はメールは送らない
                        $this->sendPaymentMail($reservationData['id'], $reservationData['payment_limit_datetime']);
                    } else { // マイナスの場合
                        if ($this->request->data['account_code'] == 'ADJUST_REDUCTION') {
                            $refundAmount = abs($this->request->data['amount'] * $this->request->data['count']);

                            $this->Refund->save([
                                'reservation_id' => $reservationId,
                                'amount' => $refundAmount,
                                'status' => Constant::STATUS_SCHEDULED_REFUND,
                                'remarks' => 'ADJUST_REDUCTION'
                            ]);

                            $this->Reservation->id = $reservationId;
                            $amount = $this->Reservation->field('amount');
                            $this->Reservation->save([
                                'id' => $reservationId,
                                'amount' => $amount - $refundAmount,
                                'staff_id' => $this->cdata['id']
                            ]);
                        }
                    }
                    $this->PaymentDetail->commit();
                    return json_encode(['ret' => 'ok']);
                } else {
                    $this->PaymentDetail->rollback();
                    return json_encode(['ret' => 'error', 'message' => reset($this->PaymentDetail->validationErrors)[0]]);
                }
            } catch (Exception $e) {
                $this->PaymentDetail->rollback();
                return json_encode(['ret' => 'error', 'message' => $e->getMessage()]);
            }
        }
        return false;
    }

    /**
     * Ajaxでのキャンセル明細保存処理
     *
     * @return array|false
     */
    public function ajaxSaveCancelDetail()
    {
        $this->autoRender = false;

        if ($this->request->is('ajax')) {
            $this->log(print_r($this->request->data, true), LOG_DEBUG);

            $this->CancelDetail->set('staff_id', $this->cdata['id']);
            $this->CancelDetail->begin();
            try {
                $refundAmount = -1 * $this->request->data['amount'] * $this->request->data['count'];

                // 返金予定額が入っていなかったら、一旦全額返金予定額を登録する(ただし、指定したキャンセル料に相当する分は引いておく)
                $count = $this->Refund->find('count', [
                    'conditions' => ['reservation_id' => $this->request->data['reservation_id'],
                                    'remarks' => null
                    ]
                ]);

                $this->log(print_r($count, true), LOG_DEBUG);
                if ($count == 0 && $refundAmount < 0) {
                    $this->Reservation->recursive = -1;
                    $reservationData = $this->Reservation->findById($this->request->data['reservation_id']);
                    if (!$reservationData['Reservation']['cancel_flg']) {
                        throw new Exception('This reservation has not been canceled');
                    }
                    $refunds = $this->Refund->find('all', [
                        'conditions' => ['reservation_id' => $this->request->data['reservation_id'],
                                        'remarks' => 'ADJUST_REDUCTION'
                        ]
                    ]);
                    $refunded = 0;
                    foreach ($refunds as $refund) {
                        $refunded = $refunded + $refund['Refund']['amount'];
                    }
                    $refundAmount = $reservationData['Reservation']['amount'] + $refundAmount - $refunded;
                    $this->log("reservationData:".print_r($reservationData, true), LOG_DEBUG);
                    $this->log("refundAmount:".print_r($refundAmount, true), LOG_DEBUG);
                    $this->log("refunded:".print_r($refunded, true), LOG_DEBUG);
                }

                $this->log(print_r($refundAmount, true), LOG_DEBUG);
                $refundData = [
                    'reservation_id' => $this->request->data['reservation_id'],
                    'amount' => $refundAmount,
                    'status' => Constant::STATUS_SCHEDULED_REFUND
                ];
                if ($this->CancelDetail->save($this->request->data) && $this->Refund->save($refundData)) {
                    $this->CancelDetail->commit();
                    return json_encode(['ret' => 'ok']);
                } else {
                    $this->CancelDetail->rollback();
                    return json_encode(['ret' => 'error', 'message' => reset($this->CancelDetail->validationErrors)[0]]);
                }
            } catch (Exception $e) {
                $this->CancelDetail->rollback();
                return json_encode(['ret' => 'error', 'message' => $e->getMessage()]);
            }
        }
        return false;
    }

    /**
     * Ajaxでの返金実行処理
     *
     * @return array|false
     */
    public function ajaxExecRefund()
    {
        $this->autoRender = false;

        if ($this->request->is('ajax')) {
            $this->log(print_r($this->request->data, true), LOG_DEBUG);

            $remaining_amount = $this->request->data['remaining_amount']; // 返金予定額
            $cmApplicationId = $this->request->data['cm_application_id'];

            if (!$this->paymentFlag) {
                $cond = $this->Payment->makePRConditions(['reservation_id' => $this->request->data['id'], 'payment_result' => 'success']);
                $Payments = $this->Payment->find('all', $cond);

                $econ_remaining_amount = 0; // イーコンから取得した残額合計

                $retArr = [];
                foreach ($Payments as $payment) {
                    $order_id = $payment['Payment']['order_id'];
                    $ret = $this->PaymentEcon->inquiry($order_id);
                    if (!isset($ret['data']['amount'])) {
                        return json_encode(['ret' => 'error', 'msg' => '注文を照会出来ません']);
                    }

                    if ($ret['data']['status'] == 2 || $ret['data']['status'] == 3) { // 2:与信取消、3:計上取消
                        continue;
                    }

                    $econ_remaining_amount += $ret['data']['amount'];
                    $ret['order_id'] = $order_id; // イーコン返却値にorder_idが含まれていないので追加しておく
                    $retArr[] = $ret;
                }

                if ((int)$econ_remaining_amount != (int)$remaining_amount) { // イーコンと残額が一致するか
                    return json_encode(['ret' => 'error', 'msg' => '残額が一致しません']);
                }
            }
            /* --------------- 返金可能 -----------------*/

            // 返金予定データ取得
            $refunds = $this->Refund->find('all', [
                'conditions' => [
                    'reservation_id' => $this->request->data['id'],
                    'status' => Constant::STATUS_SCHEDULED_REFUND
                ]
            ]);

            $refunding_amount = 0; // 返金予定額
            $is_cancel = true;
            foreach ((array)$refunds as $refund) {
                $refunding_amount += $refund['Refund']['amount'];
                if ($refund['Refund']['remarks'] === 'ADJUST_REDUCTION') {
                    $is_cancel = false;
                }
            }

            if ((int)$refunding_amount != 0) {
                if ($this->paymentFlag) {
                    $res = json_decode($this->getPaymentInfo($cmApplicationId), true);
                    $url = $this->PaymentAPI->getApiUrlCreateOrUpdateRefund();
                    $retArr = [];
                    foreach ($res['detail'] as $val) {
                        switch ($val['progress']) {
                            case 3: // 入金
                            case 5: // 返金要求
                            case 6: // 決済代行へ返金リクエスト送信
                            case 7: // 決済代行側で返金処理
                                if (isset($retArr[$val['orderCode']])) {
                                    $retArr[$val['orderCode']]['price'] = $retArr[$val['orderCode']]['price'] + $val['price'];
                                } else {
                                    $retArr[$val['orderCode']] = ['price' => $val['price']];
                                }
                                // no break
                            default:
                        }
                    }

                    // 返金要求予定のIDをすべて取得
                    $refundIds = [];
                    foreach ($refunds as $refund) {
                        $refundIds[] = $refund['Refund']['id'];
                    }
                    
                    // 返金要求テーブルに登録
                    $this->RefundRequest->save(
                        array(
                            'reservation_id' => $this->request->data['id'],
                            'refund_ids' => implode(',', $refundIds),
                            'created_at' => date('Y-m-d H:i:s')
                        )
                    );

                    foreach ($retArr as $orderCode => $priceData) {
                        if ($priceData['price'] <= 0 || $refunding_amount <= 0) { // 該当注文番号が返金済み、返金額がマイナスになったらならパス
                            continue;
                        }
                        $this->log("econ_amount:".$priceData['price'].", ".$refunding_amount.", order_code:".$orderCode, LOG_DEBUG);

                        $targetPrice = ($priceData['price'] <= $refunding_amount) ? $priceData['price'] : $refunding_amount;
                        $arr = $this->PaymentAPI->createRefund(
                            $targetPrice,
                            $cmApplicationId,
                            $this->request->data['id'],
                            $orderCode,
                            $this->RefundRequest->id,
                            $is_cancel
                        );
                        $res = $this->PaymentAPI->runApi($url, 'put', $arr);
                        if ($res->code != 200) {
                            $this->log($res, LOG_ERROR);
                            return json_encode(['ret' => 'error', 'msg' => 'refundエラー']);
                        } else {
                            $body = json_decode($res->body, true);
                            $results = $this->PaymentToken->saveInsertUpdate($cmApplicationId, $body['token']);
                        }

                        $refunding_amount -= $priceData['price'];
                    }
                } else {
                    foreach ($retArr as $econ) {
                        if ($econ['data']['amount'] == 0) { // 該当注文番号が返金済みならパス
                            continue;
                        }

                        $this->log("econ_amount:".$econ['data']['amount'].", ".$refunding_amount.", order_id:".$econ['order_id'], LOG_DEBUG);
                        if ($econ['data']['amount'] <= $refunding_amount) {
                            if (!$this->PaymentEcon->refund($econ['order_id'], 0)) {
                                return json_encode(['ret' => 'error', 'msg' => 'refundエラー(1)']);
                            }
                            $refunding_amount -= $econ['data']['amount'];
                        } else {
                            if (!$this->PaymentEcon->refund($econ['order_id'], ($econ['data']['amount'] - $refunding_amount))) {
                                return json_encode(['ret' => 'error', 'msg' => 'refundエラー(2)']);
                            }
                        }
                    }
                }
            }

            $this->Reservation->set('staff_id', $this->cdata['id']);
            if ($this->paymentFlag) {
                $this->Reservation->set('payment_status', 'REFUND_REQUEST');
            } else {
                $this->Reservation->set('payment_status', 'REFUNDED');
            }
            $this->Reservation->begin();
            try {
                if ($this->Reservation->save($this->request->data)) {
                    foreach ((array)$refunds as $refund) {
                        if ($this->paymentFlag) {
                            $refund['Refund']['status'] = Constant::STATUS_REFUNDING;
                        } else {
                            $refund['Refund']['status'] = Constant::STATUS_REFUNDED;
                            $refund['Refund']['refunded'] = date('Y-m-d H:i:s');
                        }
                        $this->Refund->save($refund);
                    }

                    $this->Reservation->commit();
                    return json_encode(['ret' => 'ok']);
                } else {
                    $this->Reservation->rollback();
                    return json_encode(['ret' => 'error', 'message' => 'save error']);
                }
            } catch (Exception $e) {
                $this->Reservation->rollback();
                return json_encode(['ret' => 'error', 'message' => $e->getMessage()]);
            }
        }
        return false;
    }

    /**
     * 追加料金支払いメール送信
     *
     * @param string $reservation_id
     * @param string $payment_limit_datetime
     * @return void
     */
    private function sendPaymentMail($reservation_id, $payment_limit_datetime)
    {
        $params = $this->Reservation->find('first', [
            'fields' => [
                'Reservation.last_name',
                'Reservation.first_name',
                'Reservation.reservation_hash',
                'Reservation.email',
            ],
            'conditions' => [
                'id' => $reservation_id
            ],
            'recursive' => -1,
        ]);

        $params['payment_limit_datetime'] = $payment_limit_datetime;
        $params['domain'] = $_SERVER["HTTP_HOST"];
        // 継承してメールクラスを利用
        $email = new SkyticketCakeEmail('smtp');
        $email
            ->viewVars($params)
            ->template('add_payment_request', 'suggestions_layout')
            ->subject('【skyticket】レンタカー追加代金お支払いのお願い')
            ->to(trim($params['Reservation']['email']))
            ->send();
    }

    /**
     * Ajaxでの領収書明細取得処理
     *
     * @return array|false
     */
    public function ajaxGetReceiptDetail()
    {
        $this->autoRender = false;

        if ($this->request->is('ajax')) {
            return json_encode($this->Receipt->getDetail($this->request->query('receipt_id')));
        }
        return false;
    }

    /**
     * Ajaxでの領収書発行可能金額取得
     *
     * @return array|false
     */
    public function ajaxGetReceiptIssuableAmount()
    {
        $this->autoRender = false;

        if ($this->request->is('ajax')) {
            return json_encode($this->Receipt->getIssuableAmount($this->request->query('reservation_id')));
        }
        return false;
    }

    /**
     * Ajaxでの領収書保存処理
     *
     * @return array|false
     */
    public function ajaxSaveReceipt()
    {
        $this->autoRender = false;

        if ($this->request->is('ajax')) {
            $param = $this->request->data;
            $param['staff_id'] = $this->cdata['id'];
            return json_encode(['ret' => $this->Receipt->save($param)]);
        }
        return false;
    }

    /**
     * 領収書ダウンロード処理
     *
     * @return void
     */
    public function downloadReceipt()
    {
        // 領収証詳細データを取得
        $receiptDetail = $this->Receipt->getDetail($this->request->query('receipt_id'));
        // ダウンロード実行
        $this->Receipt->download($receiptDetail);
    }

    /**
     * 領収書プレビュー処理
     *
     * @return void
     */
    public function previewReceipt()
    {
        // プレビュー表示用のデータを生成
        $previewData = $this->request->data;
        $previewData['receipt_payment_type'] = ['1' => 'クレジットカード', '2' => 'Credit Card'][isset($previewData['lang_id']) ? $previewData['lang_id'] : '1'];
        $previewData['create_dt'] = date('Y-m-d');
        // プレビューモードでダウンロード実行
        $this->Receipt->download($previewData, true);
    }
}
