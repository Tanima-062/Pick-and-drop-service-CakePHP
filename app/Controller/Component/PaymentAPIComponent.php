<?php
App::uses('HttpSocket', 'Network/Http');

require_once("const/common_const.php");
require_once("db_class.php");

class PaymentAPIComponent extends Component
{

    public $uses = ['PaymentToken'];
    public $components = ['Session'];

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
     * 返金要求を作成/更新するURL
     *
     * @return string
     */
    public function getApiUrlCreateOrUpdateRefund()
    {
        return $this->apiNowEnvUrl . '/payments/refund';
    }

    /**
     * サービス戻り先URL
     *
     * @return string
     */
    public function getReturnUrl()
    {
        return URL . 'rentacar/reservations/callBackReturn/';
    }

    /**
     * サービス取消戻り先URL
     *
     * @return string
     */
    public function getCancelReturnUrl()
    {
        return URL . 'rentacar/reservations/callBackCancelReturn/';
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
     * 与信コールバック先
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
     * 取消コールバック先
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
     * 計上コールバック先
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
     * MyPage サービス戻り先URL
     *
     * @return string
     */
    public function getMyPageReturnUrl()
    {
        return URL . 'rentacar/mypages/callBackReturn/';
    }

    /**
     * MyPage サービス取消戻り先URL
     *
     * @return string
     */
    public function getMyPageCancelReturnUrl()
    {
        return URL . 'rentacar/mypages/callBackCancelReturn/';
    }

    /**
     * MyPage 与信コールバック先
     *
     * @return string
     */
    public function getMyPageAuthorizeUrl()
    {
        $url = '';
        if ($_SERVER['HTTP_HOST'] == 'jp-local.skyticket.jp') {
            // APIからローカル環境へコールバックできない為、API側で用意されている試験用URLを使用する
            $url = 'https://jp-pay.skyticket.jp/api/test/authorize';
        } else {
            $url = URL . 'rentacar/mypages/callBackAuthorize/';
        }
        return $url;
    }

    /**
     * MyPage 取消コールバック先
     *
     * @return string
     */
    public function getMyPageCancelUrl()
    {
        $url = '';
        if ($_SERVER['HTTP_HOST'] == 'jp-local.skyticket.jp') {
            // APIからローカル環境へコールバックできない為、API側で用意されている試験用URLを使用する
            $url = 'https://jp-pay.skyticket.jp/api/test/cancel';
        } else {
            $url = URL . 'rentacar/mypages/callBackCancel/';
        }
        return $url;
    }

    /**
     * MyPage 計上コールバック先
     *
     * @return string
     */
    public function getMyPageCaptureUrl()
    {
        $url = '';
        if ($_SERVER['HTTP_HOST'] == 'jp-local.skyticket.jp') {
            // APIからローカル環境へコールバックできない為、API側で用意されている試験用URLを使用する
            $url = 'https://jp-pay.skyticket.jp/api/test/capture';
        } else {
            $url = URL . 'rentacar/mypages/callBackCapture/';
        }
        return $url;
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
     * cm_application_idに紐づくカートIDと紐づくcm_applicationId一式を取得
     *
     * @param string $cmApplicationId
     * @return string
     */
    public function getApiUrlCentralCartData($cmApplicationId)
    {
        return $this->apiNowEnvUrlCentral . '/cart/reverse?offerId=rc' . $cmApplicationId;
    }

    /**
     * カートIDを逆引きするURL取得（旧カート基盤）
     *
     * @param string $cmApplicationId
     * @return string
     */
    public function getApiUrlOldCentralCartData($cmApplicationId)
    {
        return $this->apiNowEnvUrlCentralOld . "/cart/includes/rc/{$cmApplicationId}";
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
     * @return array
     */
    public function callBackAuthorize()
    {
        $result = $this->callBackCommon(__FUNCTION__);
        // 与信で止める
        $result['capture'] = false;
        return $result;
    }

    /**
     * 与信取消コールバック
     *
     * @return array
     */
    public function callBackCancel()
    {
        $result = $this->callBackCommon(__FUNCTION__);
        unset($result['capture']);
        unset($result['applicationIds']);
        return $result;
    }

    /**
     * 計上コールバック
     *
     * @return array
     */
    public function callBackCapture()
    {
        $result =  $this->callBackCommon(__FUNCTION__);
        unset($result['capture']);
        unset($result['applicationIds']);
        return $result;
    }

    /**
     * 返金取消コールバック
     *
     * @return array
     */
    public function callBackCancelRefund()
    {
        return $this->callBackCommon(__FUNCTION__);
    }

    /**
     * 返金要求コールバック
     *
     * @return array
     */
    public function callBackRefund()
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
        $apiCode = !empty($data['code']) ? $data['code'] : '';
        $apiToken = !empty($data['token']) ? $data['token'] : '';
        if ($funcName === 'callBackRefund' || $funcName === 'callBackCancelRefund') {
            $apiCmApplicationId = !empty($data['details']['cmApplicationId']) ? $data['details']['cmApplicationId'] : '';
        } else {
            $apiCmApplicationId = !empty($data['details'][0]['cmApplicationId']) ? $data['details'][0]['cmApplicationId'] : '';
        }
        
        if ($apiCode != 'success') {
            // APIからの戻り値がsuccess以外でもサービス側で対処する処理がないので常にsuccessを返す.
            $this->log('Post data code was not success.', LOG_DEBUG);
            $msg = $this->getErrorMessage($apiCode);
            $tmp = $this->createCallBackErrorJson($apiToken, 'error', $msg, false, $apiCmApplicationId);
            return $tmp;
        }

        if (empty($apiCmApplicationId)) {
            $msg = 'Post data cmApplicationId was empty.';
            return $this->createCallBackErrorJson($apiToken, 'error100', $msg, false, $apiCmApplicationId);
        }

        if (empty($apiToken)) {
            $msg = 'Post token was empty.';
            return $this->createCallBackErrorJson($apiToken, 'error101', $msg, false, $apiCmApplicationId);
        }

        $token = $this->modelPaymentToken->getTokenByCmApplicationIdAndToken($apiCmApplicationId, $apiToken);
        if (empty($token)) {
            $msg = 'The token was not found in the database.';
            return $this->createCallBackErrorJson($apiToken, 'error102', $msg, false, $apiCmApplicationId);
        }

        if ($token != $apiToken) {
            $msg = 'Post token and save token were different.';
            return $this->createCallBackErrorJson($apiToken, 'error103', $msg, false, $apiCmApplicationId);
        }

        if ($funcName == 'callBackAuthorize') {
            $this->saveCallBackValues($apiCmApplicationId, $apiToken, $data);
        }

        $tmp = [
            'token' => $token,
            'code' => 'success',
            'message' => '成功',
            'capture' => true,
            'applicationIds' => [$apiCmApplicationId],
        ];

        $this->log($tmp, LOG_DEBUG);
        return $tmp;
    }

    /**
     * Undocumented function
     *
     * @param string $errorCode
     * @return string
     */
    private function getErrorMessage($errorCode)
    {
        switch ($errorCode) {
            case 'error001': // 入力エラー
            case 'error002':
            case 'error007':
                return 'ご入力内容に間違いがあります。';
            case 'error003': // カードエラー
            case 'error004':
            case 'error005':
            case 'error006':
                return 'ご入力のカードは使用できません。';
            case 'error011': // 決済エラー
            case 'error901':
            case 'error911':
            case 'error912':
            case 'error913':
            case 'error914':
            case 'error921':
            case 'error951':
            case 'error952':
            case 'error953':
            case 'error954':
            case 'error963':
            case 'error998':
            case 'error999':
            default:
                return '決済処理がエラーになりました。しばらく待ってからご利用ください。';
        }
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
     * @param string $token
     * @param array $callBackValues
     * @return void
     */
    public function saveCallBackValues($cmApplicationId, $token, $callBackValues)
    {
        $id = $this->modelPaymentToken->getIdByCmApplicationIdAndToken($cmApplicationId, $token);
        if (empty($id)) {
            return;
        }
        $orderCode = $callBackValues['orderCode'];
        $json = json_encode($callBackValues, JSON_UNESCAPED_UNICODE);
        $result = $this->modelPaymentToken->updateCallBackValues($id, $json, $orderCode);
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
        $url = $this->getApiUrlCentralCartData($cmApplicationId);
        $options = ['header' => ['Content-Type' => 'application/json']];
        $results = $this->runApi($url, 'get', null, $options);

        $body = json_decode($results->body, true);
        if (!empty($body)) {
            return $body[0]['cartId'];
        }

        // 新カート基盤で取れなかった場合、旧カート基盤も見る
        $url = $this->getApiUrlOldCentralCartData($cmApplicationId);
        $results = $this->runApi($url, 'get', null, $options);

        if (!empty($results->body)) {
            $body = json_decode($results->body, true);
            $CartId = $body['cart_id'];
        }
        return $CartId;
    }

    /**
     * 返金要求データ作成
     *
     * @param int $price
     * @param string $cmApplicationId
     * @param string $reservationId
     * @param string $orderCode
     * @param int $refundRequestId
     * @return array
     */
    public function createRefund($price, $cmApplicationId, $reservationId, $orderCode, $refundRequestId)
    {
        return [
            'orderCode' => $orderCode,
            'currency' => 'JPY',
            'rate' => 1,
            'refundInfo' => [$this->createRefundInfo($cmApplicationId, $price, $reservationId, $refundRequestId)]
        ];
    }

    /**
     * 返金情報作成
     *
     * @param string $cmApplicationId
     * @param int $price
     * @param string $reservationId
     * @param int $refundRequestId
     * @return array
     */
    public function createRefundInfo($cmApplicationId, $price, $reservationId, $refundRequestId)
    {
        return [
            'cmApplicationId' => $cmApplicationId,
            'serviceRefundId' => $refundRequestId,
            'serviceCd' => 'rc',
            'refundId' => '',
            'price' => $price,
            'otherPrice' => $price,
            'reasonId' => 8, // キャンセル
            'isCancel' => 1,
            'refundDt' => date('Y-m-d H:i:s'),
            'limitDt' => date('Y-m-d H:i:s', strtotime('30 days')),
            'cancelRefundUrl' => $this->getCancelRefundUrl($reservationId),
            'refundUrl' => $this->getMyPageRefundUrl($reservationId)
        ];
    }
    
    /**
     * 支払い済検証
     *
     * @param string $cmApplicationId
     * @return bool
     */
    public function getPaymentFlag($cmApplicationId)
    {
        $url = $this->getApiUrlCentralCartData($cmApplicationId);
        $res = $this->runApi($url, 'get');

        // カートIDが存在する場合はフラグを立て新規決済APIに行く
        $body = json_decode($res->body, true);
        if (!empty($body)) {
            return true;
        }

        // 新カート基盤で取れなかった場合、旧カート基盤も見る
        $url = $this->getApiUrlOldCentralCartData($cmApplicationId);
        $res = $this->runApi($url, 'get');

        if ($res->code === '200') {
            return true;
        }

        return false;
    }

    /**
     * 検知バッチ
     *
     * @param string $progress
     * @return array
     */
    public function getMistakeDataForBatch($progress)
    {
        $this->httpSocket = new HttpSocket;
        $this->setApiUrl();

        if (date('G') === '8') { // 朝8時だけバッチの回っていない夜間分も取得する
            $start = date('Y-m-d', strtotime('-9 hours'));
        } else {
            if ($progress === '3') {
                $start = date('Y-m-d', strtotime('-1 hour'));
            } else {
                $start = date('Y-m-d', strtotime('-2 hours'));
            }
        }

        $params = [
            'id'              => '',
            'orderCode'       => '',
            'paymentFlg'      => '',
            'cartId'          => '',
            'userId'          => '',
            'cmApplicationId' => '',
            'serviceCd'       => 'rc',
            'paymentMethodId' => '',
            'createdAtStart'  => $start,
            'createdAtEnd'    => date('Y-m-d'),
            'progress'        => $progress
        ];

        $url = $this->getApiUrlPaymentsList();
        $res = $this->runApi($url, 'get', $params);
        
        $arr = json_decode($res->body, true)['list'];

        return $arr['data'];
    }

    public function createEmptyApplication() {
        $db = GetDBInstance(DB_MAIN_MASTER);
        $db->beginTransaction();

        // 申込データにはユーザIDが必要なので作成する
        $user_id = $this->createUser($db);
        $this->log($this->Session->id()." user_id:".$user_id, LOG_DEBUG);

        $cm_application_id_old = $this->Session->read('payment.econ.cm_application_id');
        if (!empty($cm_application_id_old)) {
            $db->commit();
            return $cm_application_id_old;
        }

        $application_data = array(
            "user_id" => $user_id,
        );

        $data = array(
            "application_id" => 0,
            "service_cd" => SERVICE_CD_RC,
        );
        $data_array = array($data);
        $application = new Application($db);
        $cm_application_id = $application->insertApplication($application_data, $data_array, $db);
        if ($cm_application_id) {
            $db->commit();
            $this->Session->write('payment.econ.cm_application_id', $cm_application_id);
            $this->Session->write('payment.cm_application_id', $cm_application_id); // 後の処理でチェックされるためニコスのときの実装に合わせる。
            return $cm_application_id;
        } else {
            $db->rollback();
            return '';
        }
    }

    private function createUser($db) {
        // 新規登録 cm_th_application, cm_th_application_detail
        $user_id = (!empty($this->Session->read('payment.econ.user_id')) && $this->Session->read('payment.econ.user_equal') === true) ? $this->Session->read('payment.econ.user_id') : 0;
        if (!$user_id) {
            if (!_isLogin()){
                // common.cm_tm_user
                $cmTmUserParams = array(
                    'family_name' => (!empty($this->Session->read('payment.econ.family_name'))) ? $this->Session->read('payment.econ.family_name') : '',
                    'first_name' => (!empty($this->Session->read('payment.econ.first_name'))) ? $this->Session->read('payment.econ.first_name') : '',
                    'tel' => (!empty($this->Session->read('payment.econ.tel'))) ? $this->Session->read('payment.econ.tel') : '',
                    'email' => (!empty($this->Session->read('payment.econ.email'))) ? $this->Session->read('payment.econ.email') : '',
                    'mailmagazine_recept_flg' => 0,
                    'password' => '',
                    'member_status' => 0,
                );

                $user = new User($db);
                $user_id = $user->insertUser($cmTmUserParams, $db);
                if (!$user_id) {
                    $this->log($this->Session->id()." user regist fail", LOG_DEBUG);
                    $db->rollback();
                    return '';
                }
            } else {
                $user_id = $_SESSION['user_id'];
            }
        }

        $this->Session->write('payment.econ.user_id', $user_id);

        return $user_id;
    }
}
