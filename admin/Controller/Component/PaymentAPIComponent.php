<?php
App::uses('HttpSocket', 'Network/Http');

require_once("const/common_const.php");

class PaymentAPIComponent extends Component
{

    public $uses = ['PaymentToken'];

    const API_PRO_URL = 'https://pay.skyticket.jp/api/v1';
    const API_STG_URL = 'https://test-pay.skyticket.jp/api/v1';
    const API_DEV_URL = 'https://jp-pay.skyticket.jp/api/v1';

    const API_CENTRA_PRO_URL = 'https://bridge.skyticket.jp';
    const API_CENTRA_DEV_URL = 'https://dev-bridge.skyticket.jp';

    const API_CENTRA_PRO_URL_OLD = 'https://central.skyticket.jp/api/v1';
    const API_CENTRA_STG_URL_OLD = 'https://stg-central.skyticket.jp/api/v1';
    const API_CENTRA_DEV_URL_OLD = 'https://dev-central.skyticket.jp/api/v1';

    private $apiNowEnvUrl = ''; // 現在の環境で使用するAPIのベースURL
    private $apiNowEnvUrlCentral = ''; // 現在の環境で使用するAPIのベースURL(central. 新カート基盤カートIDを取得する際に使用)
    private $apiNowEnvUrlCentralOld = ''; // 現在の環境で使用するAPIのベースURL(central. 旧カート基盤カートIDを取得する際に使用)

    private $httpSocket = null;
    private $modelPaymentToken = null; // PaymentTokenモデルを使用する際につかう

    /**
     * 初期処理
     *
     * @param Controller $controller
     * @return void
     */
    public function initialize(Controller $controller)
    {
        $this->controller = $controller;
        $this->setApiUrl();
        $this->httpSocket = new HttpSocket;
        $this->modelPaymentToken = ClassRegistry::init('PaymentToken');
    }

    /**
     * APIのベースURL取得
     *
     * @return string
     */
    public function getApiUrl()
    {
        return $this->apiNowEnvUrl;
    }

    /**
     * post:入金情報取得 put:支払確定 delete:取り消し URL
     *
     * @return string
     */
    public function getApiUrlPayments()
    {
        return $this->apiNowEnvUrl . '/payments';
    }

    /**
     * 支払情報を登録し、決済APIのリダイレクト先を取得するURL
     *
     * @return string
     */
    public function getApiUrlPaymentsRegister()
    {
        return $this->apiNowEnvUrl . '/payments/register';
    }

    /**
     * 入金一覧取得URL
     *
     * @return string
     */
    public function getApiUrlPaymentsList()
    {
        return $this->apiNowEnvUrl . '/payments/list';
    }

    /**
     * 決済成功 or サービス予約失敗時にリダイレクトする戻りURL
     *
     * @return string
     */
    public function getReturnUrl()
    {
        return URL . 'rentacar/reservations/callBackReturn/';
    }

    /**
     * サービス取消戻り先URL（cancelReturnUrl）決済システムを途中で離脱した時にリダイレクトする戻りURL。
     *
     * @return string
     */
    public function getCancelReturnUrl()
    {
        return URL . 'rentacar/reservations/callBackCancelReturn/';
    }

    /**
     * 与信コールバック先：与信処理結果をサービスにコールバックするURLを設定する（決済基盤バックエンド → サービスバックエンド）
     *
     * @return string
     */
    public function getAuthorizeUrl()
    {
        $url = '';
        if ($_SERVER['HTTP_HOST'] == 'jp-local.skyticket.jp') {
            // APIからローカル環境へコールバックできない為、API側で用意されている試験用URLを使用する
            $url = 'https://jp-pay.skyticket.jp/api/test/authorize';
        } else {
            $url = URL . 'rentacar/reservations/callBackAuthorize/';
        }
        return $url;
    }

    /**
     * 与信コールバック先：与信処理結果をサービスにコールバックするURLを設定する（決済基盤バックエンド → サービスバックエンド）
     *
     * @return string
     */
    public function getCancelUrl()
    {
        $url = '';
        if ($_SERVER['HTTP_HOST'] == 'jp-local.skyticket.jp') {
            // APIからローカル環境へコールバックできない為、API側で用意されている試験用URLを使用する
            $url = 'https://jp-pay.skyticket.jp/api/test/cancel';
        } else {
            $url = URL . 'rentacar/reservations/callBackCancel/';
        }
        return $url;
    }

    /**
     * 計上コールバック先：計上処理結果をサービスにコールバックするURLを設定する（決済基盤バックエンド → サービスバックエンド）
     *
     * @return string
     */
    public function getCaptureUrl()
    {
        $url = '';
        if ($_SERVER['HTTP_HOST'] == 'jp-local.skyticket.jp') {
            // APIからローカル環境へコールバックできない為、API側で用意されている試験用URLを使用する
            $url = 'https://jp-pay.skyticket.jp/api/test/capture';
        } else {
            $url = URL . 'rentacar/reservations/callBackCapture/';
        }
        return $url;
    }

    /**
     * APIのベースURL取得(central. カートIDを取得する際に使用)
     *
     * @return string
     */
    public function getApiUrlCentral()
    {
        return $this->apiNowEnvUrlCentral;
    }

    /**
     * カートIDを外部から登録するURL取得
     *
     * @return string
     */
    public function getApiUrlCentralCart()
    {
        return $this->apiNowEnvUrlCentral . '/cart/offers';
    }

    /**
     * カートIDを逆引きするURL取得（新カート基盤）
     *
     * @param string $cmApplicationId
     * @return string
     */
    public function getApiUrlByApplicationId($cmApplicationId)
    {
        return $this->apiNowEnvUrlCentral . "/cart/reverse?offerId=rc{$cmApplicationId}";
    }

    /**
     * カートIDを逆引きするURL取得（旧カート基盤）
     *
     * @param string $cmApplicationId
     * @return string
     */
    public function getApiUrlOldByApplicationId($cmApplicationId)
    {
        return $this->apiNowEnvUrlCentralOld . "/cart/includes/rc/{$cmApplicationId}";
    }

    /**
     * 返金要求を作成/更新するURL
     *
     * @return string
     */
    public function getApiUrlCreateOrUpdateRefund()
    {
        return $this->apiNowEnvUrl . '/payments/refund';
    }

    /**
     * 返金取消コールバック先：返金取消をサービスにコールバックするURLを設定する（決済基盤バックエンド → サービスバックエンド）
     *
     * @param string $reservationId
     * @return string
     */
    public function getCancelRefundUrl($reservationId)
    {
        $url = '';
        if ($_SERVER['HTTP_HOST'] == 'jp-local.skyticket.jp') {
            // APIからローカル環境へコールバックできない為、API側で用意されている試験用URLを使用する
            $url = 'https://jp-pay.skyticket.jp/api/test/refund';
        } else {
            $url = URL . 'rentacar/callBackCancelRefund/'. $reservationId;
        }
        return $url;
    }

    /**
     * 返金処理後コールバック先：返金処理結果をサービスにコールバックするURLを設定する（決済基盤バックエンド → サービスバックエンド）
     *
     * @param string $reservationId
     * @return string
     */
    public function getMyPageRefundUrl($reservationId)
    {
        $url = '';
        if ($_SERVER['HTTP_HOST'] == 'jp-local.skyticket.jp') {
            // APIからローカル環境へコールバックできない為、API側で用意されている試験用URLを使用する
            $url = 'https://jp-pay.skyticket.jp/api/test/refund';
        } else {
            $url = URL . 'rentacar/callBackRefund/'. $reservationId;
        }
        return $url;
    }
    
    /**
     * API実行
     *
     * @param string $url
     * @param string $type
     * @param array $param
     * @param array $options
     * @return object
     */
    public function runApi($url, $type, $param = null, $options = null)
    {
        $this->log($url, LOG_DEBUG);
        if (!empty($param)) {
            $this->log($param, LOG_DEBUG);
        }
        
        $results = null;
        if ($type == 'post') {
            $results = $this->httpSocket->post($url, $param, $options);
        } elseif ($type == 'get') {
            $results = $this->httpSocket->get($url, $param, $options);
        } elseif ($type == 'delete') {
            $results = $this->httpSocket->delete($url, $param, $options);
        } elseif ($type == 'put') {
            $results = $this->httpSocket->put($url, $param, $options);
        }
        return $results;
    }

    /**
     * APIのベースURL設定
     *
     * @return void
     */
    private function setApiUrl()
    {
        $url = null;
        $urlCentral = null;
        $urlCentralOld = null;
        if (IS_STAGING) {
            $url = self::API_STG_URL;
            $urlCentral = self::API_CENTRA_PRO_URL;
            $urlCentralOld = self::API_CENTRA_STG_URL_OLD;
        } elseif (IS_PRODUCTION) {
            $url = self::API_PRO_URL;
            $urlCentral = self::API_CENTRA_PRO_URL;
            $urlCentralOld = self::API_CENTRA_PRO_URL_OLD;
        } else {
            $url = self::API_DEV_URL;
            $urlCentral = self::API_CENTRA_DEV_URL;
            $urlCentralOld = self::API_CENTRA_DEV_URL_OLD;
        }

        $this->apiNowEnvUrl = $url;
        $this->apiNowEnvUrlCentral = $urlCentral;
        $this->apiNowEnvUrlCentralOld = $urlCentralOld;
    }

    /**
     * 与信コールバックチェック
     *
     * @return array|string
     */
    public function callBackAuthorize()
    {
        return $this->callBackCommon(__FUNCTION__);
    }

    /**
     * 与信取消コールバック
     *
     * @return string
     */
    public function callBackCancel()
    {
        return $this->callBackCommon(__FUNCTION__);
    }

    /**
     * コールバックチェック共通処理
     *
     * @param string $funcName
     * @return array
     */
    public function callBackCommon($funcName = '')
    {
        $this->log('Start executing ' . __FUNCTION__ . ' function.', LOG_DEBUG);

        if (empty($this->controller->request->data)) {
            return $this->createCallBackErrorJson('', 'error', 'Post data was empty.', false, '');
        }
        $this->log($this->controller->request->data, LOG_DEBUG);

        $data = $this->controller->request->data;
        if ($data['code'] != 'success') {
            $tmp = $this->createCallBackErrorJson('', 'error', 'Post data code was not success.', false, '');
            $tmp['api_error'] = 1;
            return $tmp;
        }

        $cmApplicationId = '';
        if (!empty($data['details'][0]['cmApplicationId'])) {
            $cmApplicationId = $data['details'][0]['cmApplicationId'];
        }

        if (empty($cmApplicationId)) {
            return $this->createCallBackErrorJson('', 'error', 'Post data cmApplicationId was empty.', false, '');
        }

        $token = $this->modelPaymentToken->getTokenByCmApplicationId($cmApplicationId);
        if (empty($token)) {
            $msg = 'The token was not found in the database.';
            return $this->createCallBackErrorJson('', 'error', $msg, false, $cmApplicationId);
        }

        if (empty($data['token'])) {
            return $this->createCallBackErrorJson('', 'error', 'Post token was empty.', false, $cmApplicationId);
        }

        if ($token != $data['token']) {
            $msg = 'Post token and save token were different.';
            return $this->createCallBackErrorJson('', 'error', $msg, false, $cmApplicationId);
        }

        if ($funcName == 'callBackAuthorize') {
            $this->saveCallBackValues($cmApplicationId, $data);
        }

        $tmp = [
            'token' => $token,
            'code' => 'success',
            'message' => 'success',
            'capture' => true,
            'applicationIds' => [$cmApplicationId],
        ];

        $this->log($tmp, LOG_DEBUG);
        return $tmp;
    }

    /**
     * エラーJsonの作成とログ出力
     *
     * @param string $token
     * @param string $code
     * @param string $msg
     * @param bool $capture
     * @param string $cmApplicationId
     * @return array
     */
    private function createCallBackErrorJson($token, $code, $msg, $capture, $cmApplicationId)
    {
        $tmp = [
            'token' => $token,
            'code' => $code,
            'message' => $msg,
            'capture' => $capture,
            'applicationIds' => [$cmApplicationId],
        ];
        $this->log($tmp, LOG_ERROR);
        return $tmp;
    }

    /**
     * コールバックされた値を保存
     *
     * @param string $cmApplicationId
     * @param array $callBackValues
     * @return void
     */
    public function saveCallBackValues($cmApplicationId, $callBackValues)
    {
        $id = $this->modelPaymentToken->getIdByCmApplicationId($cmApplicationId);
        if (empty($id)) {
            return;
        }
        $json = json_encode($callBackValues, JSON_UNESCAPED_UNICODE);
        $result = $this->modelPaymentToken->updateCallBackValues($id, $json);
    }

    /**
     * カートIDを取得
     *
     * @param string $cmApplicationId
     * @return string
     */
    public function getCartID($cmApplicationId)
    {
        $CartId = null;
        $url = $this->getApiUrlByApplicationId($cmApplicationId);
        $options = ['header' => ['Content-Type' => 'application/json']];
        $results = $this->runApi($url, 'get', null, $options);

        $body = json_decode($results->body, true);
        if (!empty($body)) {
            return $body[0]['cartId'];
        }

        // 新カート基盤で取れなかった場合、旧カート基盤も見る
        $url = $this->getApiUrlOldByApplicationId($cmApplicationId);
        $results = $this->runApi($url, 'get', null, $options);

        if (!empty($results->body)) {
            $body = json_decode($results->body, true);
            $CartId = $body['cart_id'];
        }
        return $CartId;
    }

    /**
     * 返金要求Json作成
     *
     * @param int $price
     * @param string $cmApplicationId
     * @param string $reservationId
     * @param string $orderCode
     * @param int $refundRequestId
     * @param bool $isCancel
     * @return array
     */
    public function createRefund($price, $cmApplicationId, $reservationId, $orderCode, $refundRequestId, $isCancel)
    {
        return [
            'orderCode' => $orderCode,
            'currency' => 'JPY',
            'rate' => 1,
            'refundInfo' => [$this->createRefundInfo($cmApplicationId, $price, $reservationId, $refundRequestId, $isCancel)]
        ];
    }

    /**
     * 返金情報作成
     *
     * @param string $cmApplicationId
     * @param int $price
     * @param string $reservationId
     * @param int $refundRequestId
     * @param bool $isCancel
     * @return array
     */
    public function createRefundInfo($cmApplicationId, $price, $reservationId, $refundRequestId, $isCancel)
    {
        return [
            'cmApplicationId' => $cmApplicationId,
            'serviceRefundId' => $refundRequestId,
            'serviceCd' => 'rc',
            'refundId' => '',
            'price' => $price,
            'otherPrice' => $price,
            'reasonId' => $isCancel ? 8 : 2, // 8:キャンセル 2:差額
            'isCancel' => $isCancel ? 1 : 0,
            'refundDt' => date('Y-m-d H:i:s'),
            'limitDt' => date('Y-m-d H:i:s', strtotime('30 days')),
            'cancelRefundUrl' => $this->getCancelRefundUrl($reservationId),
            'refundUrl' => $this->getMyPageRefundUrl($reservationId)
        ];
    }

    /**
     * 支払い済みフラグ取得
     *
     * @param string $cmApplicationId
     * @return bool
     */
    public function getPaymentFlag($cmApplicationId)
    {
        $url = $this->getApiUrlByApplicationId($cmApplicationId);
        $res = $this->runApi($url, 'get');

        // カートIDが存在する場合はフラグを立て新規決済APIに行く
        $body = json_decode($res->body, true);
        if (!empty($body)) {
            return true;
        }

        // 新カート基盤で取れなかった場合、旧カート基盤も見る
        $url = $this->getApiUrlOldByApplicationId($cmApplicationId);
        $res = $this->runApi($url, 'get');

        if ($res->code === '200') {
            return true;
        }

        return false;
    }

    /**
     * APIでの与信キャンセル処理
     *
     * @param string $orderCode
     * @return bool
     */
    public function yoshinCancelForAPI($orderCode)
    {
        $url = $this->getApiUrlPayments();
        $param =[
            'orderCode' => $orderCode
        ];
        
        $results = $this->runApi($url, 'delete', $param);
        $this->log($results, LOG_DEBUG);
        if ($results->code != 200) {
            $this->log($results, LOG_ERROR);
            return false;
        }
        
        return true;
    }
    
    /**
     * APIでの与信計上処理
     *
     * @param string $orderCode
     * @param string $reservationId
     * @return bool
     */
    public function cardCaptureForAPI($orderCode, $reservationId)
    {
        $url = $this->getApiUrlPayments();
        $param =[
            'orderCode' => $orderCode
        ];

        try {
            // トランザクション
            $this->controller->Reservation->begin();
            $this->controller->Reservation->save(
                [
                    'id' => $reservationId,
                    'payment_status' => 'PAYED',
                ]
            );
            

            $results = $this->runApi($url, 'put', $param);
            $this->log($results, LOG_DEBUG);
            if ($results->code != 200) {
                $this->log($results, LOG_ERROR);
                $this->controller->Reservation->rollback();
                return false;
            }
        } catch (Exception $e) {
            $this->controller->Reservation->rollback();
            return false;
        }

        $this->controller->Reservation->commit();
        return true;
    }
}
