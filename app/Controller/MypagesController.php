<?php
App::uses('AppController', 'Controller');
App::uses('CakeEmail', 'Network/Email');
App::uses('SkyticketCakeEmail', 'Vendor');

require_once("log_class.php");
require_once("encrypt_class.php");
require_once("db_class.php");
require_once("user_class.php");

/**
 * Mypages Controller
 *
 * @property Mypages $Mypages
 */
class MypagesController extends AppController
{

    public $components = array(
        'Cookie', 'Session', 'Security', 'YotpoAPI', 'CancelFeeCalculation', 'PaymentEcon', 'PaymentAPI',
        'ReservationAPISelect', 'BreadCrumb', 'CancelPolicy', 'CancelDeadline', 'Validation', 'ReservationUtil'
    );

    public $uses = array(
        'Reservation', 'ReservationMail', 'ReservationStatus', 'Privilege', 'ClientEmail', 'CancelReason', 'Client', 'ClientCard',
        'CarClassReservation', 'CommodityItem', 'CommodityEquipment', 'Equipment', 'ReservationPrivilege', 'RefundRequest',
        'ReservationChildSheet', 'ReservationDetail', 'CancelDetail',  'CancelFee', 'Refund', 'Landmark', 'PaymentDetail',
        'ReservationPassenger', 'PaymentLog', 'Maintenance', 'PaymentToken', 'Area', 'BudgetReservationApiFailure'
    );

    // 車両年式
    public $newCarRegistration = array(
        1 => '新車登録1年以内',
        2 => '新車登録2年以内',
        3 => '新車登録3年以内',
        4 => '新車登録4年以内',
        5 => '新車登録5年以上'
    );

    // 喫煙・禁煙
    public $smokingCarList = array(
        0 => '禁煙車',
        1 => '喫煙車',
        2 => '指定なし',
    );

    private $errorTxt = '';
    private $isEconMaintenance = false;
    private $isPaymentAPI = false;
    private $reserveErrorMessage;

    /**
     * 前処理
     *
     * @return void
     */
    public function beforeFilter()
    {

        if ($this->action == 'call_back_cancel_refund' ||
            $this->action == 'call_back_refund') {
            // コールバックの際の穴あけ
            $this->Security->validatePost = false;
            $this->Security->csrfCheck = false;
        } else {
            parent::beforeFilter();
            // HTTP でない場合に実行するメソッド名
            $this->Security->blackHoleCallback = 'forceSSL';
            $this->Security->requireSecure();
            $this->Security->validatePost = false;
            $this->Security->csrfCheck = ($this->action == 'login');
        }

        $this->isEconMaintenance = $this->Maintenance->isEconMaintenance();
        $this->set('isEconMaintenance', $this->isEconMaintenance);

        if (!($this->action == 'login' || $this->action == 'sp_login')) {
            // robots noindex
            $this->set('meta_robots', 'noindex');
        }

        $this->set('smokingCarList', $this->smokingCarList);
        $this->isPaymentAPI = $this->Maintenance->isPaymentAPI();
        $this->set('paymentApi', $this->isPaymentAPI);

        // 予約失敗メッセージ
        // ※ rentacar/app/Controller/ReservationsController.phpにも同じものを入れているため、メッセージ変更の場合は一緒に修正する
        $this->reserveErrorMessage = '予約手続きに失敗しました。<br>お手数ですが改めてご予約いただくか、下記までお問合せください。<br>スカイチケットレンタカーサポート<br>'
                                    . 'お問い合わせ先：<a href="tel:' . str_replace('-', '', DISPLAY_RENTACAR_TEL) . '">'
                                    . DISPLAY_RENTACAR_TEL . '</a><br>平日：10:00〜18:00 / 土日祝日：10:00〜18:00';
    }

    /**
     * Undocumented function
     *
     * @param string $type
     * @return void
     */
    public function forceSSL($type)
    {
        if ($type == 'csrf') {
            $this->errorTxt = 'ご予約の確認ができませんでした。<br>入力内容を確認してください。';
        } else {
            $this->redirect("https://" . env('SERVER_NAME') . $_SERVER['REQUEST_URI']);
        }
    }

    /**
     * セッション削除処理
     *
     * @return void
     */
    private function deleteSession()
    {
        $this->Session->delete('Login');
        $this->Session->delete('payment');
        $this->Session->renew();
    }

    /**
     * マイページログアウト画面
     *
     * @return void
     */
    public function logout()
    {
        if ($this->Session->check('Login')) {
            $this->deleteSession();
        }
        $this->set('title_for_layout', 'ログアウト');
        $this->set('h1_for_layout', 'ログアウト');
        $this->set('top_txt', '');
        $this->set('description_for_layout', 'ログアウト');
    }

    /**
     * ログアウト処理（スマホ）
     *
     * @return void
     */
    public function sp_logout()
    {
        $this->logout();
    }

    /**
     * マイページログイン画面
     *
     * @return void
     */
    public function login()
    {
        if ($this->Session->check('Login')) {
            $this->deleteSession();
        }

        if (!empty($this->request->query['hash'])) {
            $reservationData = $this->Reservation->getReserveKeyByHash($this->request->query['hash']);
            if (!empty($reservationData)) {
                $this->request->data['Reservation']['reservation_key'] = $reservationData['Reservation']['reservation_key'];
            }
        }

        // skyticketログイン中は、ログインユーザのレンタカー申込データかチェックして問題なければログイン省略してレンタカーマイページに遷移
        $login_through_flg = false;
        $cm_application_id = '';
        if (_isLogin() && isset($reservationData['Reservation']['id'])) {
            $db = GetDBInstance(DB_MAIN_MASTER);    // master DB
            $sql = ""
                    . "SELECT "
                    . "  a.cm_application_id "
                    . " FROM "
                    . DB_NAME . ".cm_th_application AS a"
                    . " INNER JOIN "
                    . DB_NAME . ".cm_th_application_detail AS ad"
                    . " ON "
                    . "  a.cm_application_id = ad.cm_application_id "
                    . " WHERE a.user_id = :user_id"
                    . " AND ad.application_id = :application_id"
                    . " AND ad.service_cd = :service_cd"
                    . "";
            $param_arr = array(
                ":user_id" => $_SESSION['user_id'],
                ":application_id" => $reservationData['Reservation']['id'],
                ":service_cd" => "rc",
            );

            $result = $db->execute($sql, $param_arr, $st);
            if ($db->execute($sql, $param_arr, $st)) {
                if ($db->getRowCount($st) == 1) {
                    $login_through_flg = true;
                    // ログイン画面でキーと電話番号が入力されたのと同じ動きをするように
                    $this->request->data['Reservation']['reservation_key'] = $reservationData['Reservation']['reservation_key'];
                    $this->request->data['Reservation']['tel'] = $reservationData['Reservation']['tel'];
                }
            }
        }

        if ($this->errorTxt == '' && ($this->request->is('post') || $login_through_flg)) {
            if (empty($this->data['Reservation']['reservation_key']) || empty($this->data['Reservation']['tel'])) {
                $this->errorTxt = 'ご予約の確認ができませんでした。<br>入力内容を確認してください。';
            } elseif (!empty($this->data['Reservation']['reservation_key']) || !empty($this->data['Reservation']['tel'])) {
                $reservation = $this->Reservation->getMyPageLogin($this->data['Reservation']['reservation_key'], $this->data['Reservation']['tel']);
                if (!empty($reservation)) {
                    if ($this->__isTour($reservation['Reservation'])) {
                        $this->errorTxt = 'ツアーのレンタカーは個別の予約情報としては見ることはできません。';
                    } else {
                        $this->Session->write('Login', array(
                            'key' => $this->data['Reservation']['reservation_key'],
                            'tel' => $this->data['Reservation']['tel'],
                        ));
                        $this->redirect('/mypages/');
                    }
                } else {
                    $this->errorTxt = 'ご予約の確認ができませんでした。<br>入力内容を確認してください。';
                }
            }
        }

        if ($this->errorTxt != '') {
            $this->set('errorTxt', $this->errorTxt);
        }

        $this->set('title_for_layout', '予約内容確認');
        $this->set('h1_for_layout', '予約内容確認');
        $this->set('top_txt', '予約内容の確認・変更・キャンセルができます。');
        $this->set('description_for_layout', '予約内容の確認・変更・キャンセルができます。');

        //  パンくずリスト設定
        $progressArr = $this->BreadCrumb->setMypages($this->action);
        $this->set('progress_arr', $progressArr);
    }

    /**
     * ログイン処理（スマホ）
     *
     * @return void
     */
    public function sp_login()
    {
        $this->login();
    }

    /**
     * ログイン後の予約内容画面
     *
     * @return void
     */
    public function index()
    {
        if ($this->Session->check('Login')) {
            $this->request->data['Reservation'] = $this->Session->read('Login');
        } else {
            $this->redirect('/mypages/login/');
        }

        if ($this->Session->check('message')) {
            $sessionMessage = $this->Session->read('message');
            // 予約失敗メッセージがある場合は、メッセージ情報から削除する
            if (array_key_exists('session', $sessionMessage) && $sessionMessage['session'] == $this->reserveErrorMessage) {
                $this->Session->delete('message.session');
                $sessionMessage = $this->Session->read('message');
            }
            if (!empty($sessionMessage)) {
                $this->set('sessionMessage', $sessionMessage);
            }
            $this->Session->delete('message');
        }

        // マイページ表示データ
        $result = $this->Reservation->getMypageDatas($this->data['Reservation']['key'], $this->data['Reservation']['tel']);

        // オプションデータ取得
        $reservationPrivilege = $this->ReservationPrivilege->getReservationPrivilegeData($result['Reservation']['id']);
        // チャイルドシートデータ取得
        $reservationChildSheet = $this->ReservationChildSheet->getReservationChildSheetData($result['Reservation']['id']);

        $dropOffNightFee = $this->ReservationDetail->find('all', array(
            'fields' => array(
                'ReservationDetail.*',
                'DetailType.name',
            ),
            'joins' => array(
                array(
                    'table' => 'detail_types',
                    'alias' => 'DetailType',
                    'type' => 'INNER',
                    'conditions' => array(
                        'DetailType.id = ReservationDetail.detail_type_id'
                    ),
                ),
            ),
            'conditions' => array(
                'ReservationDetail.reservation_id' => $result['Reservation']['id'],
                'ReservationDetail.detail_type_id' => array(Constant::DETAIL_TYPE_DROPOFFPRICE, Constant::DETAIL_TYPE_NIGHTFEE),
                'ReservationDetail.delete_flg' => 0,
            ),
            'recursive' => -1,
        ));

        // 車情報取得
        $carInfoList = $this->CommodityItem->getCarInfo($result['CommodityItem']['id'], true);
        $carInfoArray = $carInfoList[$result['CommodityItem']['id']]['CarModel'];
        $arr_tmp = array();
        $result['CarModel'] = '';
        foreach ($carInfoArray as $key => $value) {
            if (!in_array($value['id'], $arr_tmp)) {
                $arr_tmp[] = $value['id'];
                if (empty($result['CarModel'])) {
                    $result['CarModel'] = $value['name'];
                } else {
                    $result['CarModel'] .= '・' . $value['name'];
                }
            }
        }
        // 推奨目安（人数）
        $result['Recommend']['capacity'] = $carInfoList[$result['CommodityItem']['id']]['CarModel'][0]['capacity'];
        // 推奨目安（荷物）
        $result['Recommend']['package_num'] = $carInfoList[$result['CommodityItem']['id']]['CarModel'][0]['package_num'];

        // 装備取得
        $equipmentList = $this->Equipment->getEquipment();
        $commodityEquipment = $this->CommodityEquipment->getCommodityEquipment($result['Commodity']['id']);
        if (!empty($commodityEquipment)) {
            $result['Equipment'] = $commodityEquipment[$result['Commodity']['id']];
        }

        // 支払方法（カード取得）
        if (!empty($result['Client']['accept_card'])) {
            $clientCards = $this->ClientCard->getCardByClientId($result['Client']['id']);
            $result['Cards'] = $clientCards[$result['Client']['id']];
        }

        // キャンセルポリシー
        $result['CancelPolicy'] = $this->CancelPolicy->getTextLines($result['Reservation']['client_id'], $result['Reservation']['rent_datetime']);
        // INCIDENT-3044 【レンタカー】取消手続料の徴収を廃止する
        //$result['AdvCancelFee'] = $this->CancelPolicy->getAdvCancelFee();

        // お問い合わせデータ
        $reservationMail = $this->ReservationMail->getMailById($result['Reservation']['id']);

        // 予約ステータス一覧取得
        $reservationStatus = $this->ReservationStatus->find('list', array('recursive' => -1,));
        $week = array("日", "月", "火", "水", "木", "金", "土");

        // 車両年式
        $newCarRegistration = $this->newCarRegistration;

        // 参考車両イメージ
        //$commodityImages = $this->CommodityImage->getImageByCommodityId($result['Commodity']['id']);

        // レンタル期間
        $dateFrom = date('Y-m-d', strtotime($result['Reservation']['rent_datetime']));
        $dateTo = date('Y-m-d', strtotime($result['Reservation']['return_datetime']));
        $dayNight = floor(abs((strtotime($dateFrom) - strtotime($dateTo)) / (60 * 60 * 24)));
        $period = $dayNight + 1;
        if ($result['Commodity']['day_time_flg'] == 1) {
            // 時間制
            $rentalTime = abs((strtotime($result['Reservation']['rent_datetime']) - strtotime($result['Reservation']['return_datetime'])) / (60 * 60));
            // レンタル期間
            $rentalPeriod = $rentalTime . '時間';
        } else {
            // 暦日制
            if ($dayNight > 0) {
                $rentalPeriod = $dayNight . '泊' . $period . '日';
            } else {
                $rentalPeriod = '日帰り';
            }
        }

        $airportList = $this->Landmark->find('list', array(
            'fields' => array('id', 'id'),
            'conditions' => array(
                'landmark_category_id' => 1
            ),
            'recursive' => -1,
        ));

        // 営業所が空港送迎に対応しているかチェック
        if (!empty($result['RentOffice']['airport_id']) && !empty($airportList[$result['RentOffice']['airport_id']])) {
            // ランドマークカテゴリが空港の場合のみ
            $methodOfTransport = $result['RentOfficeSupplement']['method_of_transport'];
            if ($methodOfTransport == 1 || $methodOfTransport == 2) {
                $this->set('arrivalAirport', true);
            }
        }
        if (!empty($result['ReturnOffice']['airport_id']) && !empty($airportList[$result['ReturnOffice']['airport_id']])) {
            // ランドマークカテゴリが空港の場合のみ
            $methodOfTransport = $result['ReturnOfficeSupplement']['method_of_transport'];
            if ($methodOfTransport == 1 || $methodOfTransport == 2) {
                $this->set('departureAirport', true);
            }
        }

        // 事前決済/現地決済
        $isPaidInAdvance = $this->PaymentLog->hasPaymentInfo($result['Reservation']['id']);

        // キャンセル手仕舞い日時
        $cancelDeadlineDatetime = $this->CancelDeadline->getDatetime($result['Reservation']['id']);
        
        // 北海道キャンペーン
        $hokkaidoCampaignFlg = false;
        $hokkaidoCampaignTargetClientIds = [55, 4, 75, 46, 33, 115, 25, 111, 13, 43, 5, 108, 26, 142];
        $hokkaidoAreaIds = Hash::extract($this->Area->getAreaInfoByPrefectureId(1), '{n}.Area.id');
        if (strtotime($result['Reservation']['rent_datetime']) >= strtotime('2022-09-01 00:00:00') &&
            strtotime($result['Reservation']['rent_datetime']) <= strtotime('2022-11-30 23:59:59') &&
            in_array($result['Reservation']['client_id'], $hokkaidoCampaignTargetClientIds) &&
            in_array($result['RentOffice']['area_id'], $hokkaidoAreaIds)) {
          $hokkaidoCampaignFlg = true;
        }
        
        $this->set(
            compact(
                'result',
                'reservationStatus',
                'week',
                'reservationMail',
                'newCarRegistration',
                'equipmentList',
                'reservationChildSheet',
                'reservationPrivilege',
                'dropOffNightFee',
                'rentalPeriod',
                'isPaidInAdvance',
                'cancelDeadlineDatetime',
                'hokkaidoCampaignFlg'
            )
        );

        $this->ReservationMail->readingMail($result['Reservation']['id']);

        // 各IDを取得
        $userId = null;
        $cmApplicationId = null;
        $cartId = null;
        list($cmApplicationId, $userId) = $this->getCmApplicationIDAndUserID($result['Reservation']['id']);
        $cartId = $this->getCartId($cmApplicationId); // 決済APIを使用せずに決済した場合は、カートIDは無い
        $this->Session->write('payment.ids.user_id', $userId);
        $this->Session->write('payment.ids.cm_application_id', $cmApplicationId);
        $this->Session->write('payment.ids.cart_id', $cartId);
        $this->set('cartId', $cartId);

        // 未入金があれば(支払額、入金額、差額)
        list($billingAmount, $depositAmount, $unpaidAmount) =
            $this->__getUnpaidAmount(
                $result['Reservation']['id'],
                $result['Reservation']['administrative_fee']
            );
        $this->Session->write('UnpaidAmount', $unpaidAmount);
        $this->set('unpaidAmount', $unpaidAmount);
        $this->set('title_for_layout', '予約内容変更・キャンセル');
        $this->set('h1_for_layout', '予約内容変更・キャンセル');
        $this->set('top_txt', '予約内容の確認ページです。変更・キャンセルはコチラから。');
        $this->set('description_for_layout', '予約内容の確認ページです。変更・キャンセルはコチラから。');

        // 決済API
        if ($this->isPaymentAPI && !empty($cartId) && $unpaidAmount > 0) {
            $this->paymentApi($result['Reservation'], $billingAmount, $unpaidAmount);
        }

        //  パンくずリスト設定
        $progressArr = $this->BreadCrumb->setMypages($this->action);
        $this->set('progress_arr', $progressArr);

        // バジェットAPIエラーチェック
        $errorCheckResult = $this->BudgetReservationApiFailure->checkDuplicateErrorMessage($result['Reservation']['reservation_key']);
        if (!$errorCheckResult) {
            $this->set('sessionMessage', array('エラーが発生しました。管理者にお問い合わせ下さい。'));
        }
        $this->set('isVisibleButton', $errorCheckResult);
    }

    /**
     * 予約内容表示（スマホ）
     *
     * @return void
     */
    public function sp_index()
    {
        $this->index();
    }

    /**
     * マイページデータ変更ページ
     *
     * @return void
     */
    public function edit()
    {
        if ($this->Session->check('Login')) {
            $this->request->data['Reservation'] = $this->Session->read('Login');
        } else {
            $this->redirect('/mypages/login/');
        }

        if ($this->Session->check('message')) {
            $sessionMessage = $this->Session->read('message');
            $this->set('sessionMessage', $sessionMessage);
            $this->Session->delete('message');
        }

        // マイページ表示データ
        $result = $this->Reservation->getMypageDatas($this->data['Reservation']['key'], $this->data['Reservation']['tel']);

        $airportList = $this->Landmark->find('list', array(
            'fields' => array('id', 'id'),
            'conditions' => array(
                'landmark_category_id' => 1
            ),
            'recursive' => -1,
        ));

        // 営業所が空港送迎に対応しているかチェック
        if (!empty($result['RentOffice']['airport_id']) && !empty($airportList[$result['RentOffice']['airport_id']])) {
            // ランドマークカテゴリが空港の場合のみ
            $methodOfTransport = $result['RentOfficeSupplement']['method_of_transport'];
            if ($methodOfTransport == 1 || $methodOfTransport == 2) {
                $this->set('arrivalAirport', true);
            }
        }
        if (!empty($result['ReturnOffice']['airport_id']) && !empty($airportList[$result['ReturnOffice']['airport_id']])) {
            // ランドマークカテゴリが空港の場合のみ
            $methodOfTransport = $result['ReturnOfficeSupplement']['method_of_transport'];
            if ($methodOfTransport == 1 || $methodOfTransport == 2) {
                $this->set('departureAirport', true);
            }
        }

        if ($this->params['content'] == 'airport') {
            $this->set('title_for_layout', '到着便/出発便の登録・変更');
            $this->set('h1_for_layout', '到着便/出発便の登録・変更');
            $this->set('top_txt', '到着便/出発便の登録・変更ができます。');
            $this->set('description_for_layout', '到着便/出発便の登録・変更ができます。');
        }
        if ($this->params['content'] == 'mail') {
            $this->set('title_for_layout', 'メールアドレスの変更');
            $this->set('h1_for_layout', 'メールアドレスの変更');
            $this->set('top_txt', 'メールアドレスの変更ができます。');
            $this->set('description_for_layout', 'メールアドレスの変更ができます。');
        }
        if ($this->params['content'] == 'tel') {
            $this->set('title_for_layout', '電話番号の変更');
            $this->set('h1_for_layout', '電話番号の変更');
            $this->set('top_txt', '電話番号の変更ができます。');
            $this->set('description_for_layout', '電話番号の変更ができます。');
        }
        if ($this->params['content'] == 'count') {
            $passengers = array();
            $adultPassengers = array();
            for ($i = 0; $i <= 10; $i++) {
                $passengers[$i] = $i;
                if ($i > 0) {
                    $adultPassengers[$i] = $i;
                }
            }
            $carInfoList = $this->CommodityItem->getCarInfo($result['CommodityItem']['id'], true);
            $this->set(compact('passengers', 'adultPassengers'));
            $this->set('capacity', $carInfoList[$result['CommodityItem']['id']]['CarModel'][0]['capacity']);
            $this->set('title_for_layout', 'ご利用人数の変更');
            $this->set('h1_for_layout', 'ご利用人数の変更');
            $this->set('top_txt', 'ご利用人数の変更ができます。');
            $this->set('description_for_layout', 'ご利用人数の変更ができます。');
        }
        if ($this->params['content'] == 'contents') {
            $this->set('title_for_layout', 'お問い合わせ');
            $this->set('h1_for_layout', 'お問い合わせ');
            $this->set('top_txt', 'お問い合わせができます。');
            $this->set('description_for_layout', 'お問い合わせができます。');
        }

        $this->set(compact('result'));
    }

    /**
     * 予約内容編集（スマホ）
     *
     * @return void
     */
    public function sp_edit()
    {
        $this->edit();
    }

    /**
     * マイページデータ登録内容変更の確認ページ
     *
     * @return void
     */
    public function check()
    {
        if ($this->Session->check('Login')) {
            $session = $this->Session->read('Login');
        } else {
            $this->redirect('/mypages/login/');
        }

        $result = $this->Reservation->getMyPageLogin($session['key'], $session['tel']);
        $params['Reservation']['id'] = $result['Reservation']['id'];

        if ($this->params['content'] == 'contents') {
            if (empty($this->data['Reservation']['contents']) || preg_match('/^(\s|　)+$/', $this->data['Reservation']['contents'])) {
                $this->redirect('/mypages/edit/contents/');
            }
        }

        if ($this->request->is('post') && isset($this->data['rewriteBtn'])) {
            $reserveChangeFlg = json_decode($this->data['Reservation']['reserveChangeFlg'], true);
            $this->ReservationUtil->reserveChangeFlg = array_filter($reserveChangeFlg);

            foreach ($this->ReservationUtil->reserveChangeFlg as $reserveChange => $val) {
                // 到着便変更
                if ($reserveChange == 'airline') {
                    $params['Reservation']['arrival_flight_number'] =
                        $this->Validation->removeControlChars($this->data['Reservation']['arrival_flight_number']);
                    $params['Reservation']['departure_flight_number'] =
                        $this->Validation->removeControlChars($this->data['Reservation']['departure_flight_number']);
                }
                // メールアドレス変更
                if ($reserveChange == 'mail') {
                    $params['Reservation']['email'] = $this->data['Reservation']['email'];
                }
                // 電話番号変更
                if ($reserveChange == 'tel') {
                    $params['Reservation']['tel'] = $this->data['Reservation']['edit_tel'];
                }
                // ご利用人数変更
                if ($reserveChange == 'passenger_count') {
                    if (isset($this->data['Reservation']['adults_count'])) {
                        $params['Reservation']['adults_count'] = $this->data['Reservation']['adults_count'];
                    }
                    if (isset($this->data['Reservation']['children_count'])) {
                        $params['Reservation']['children_count'] = $this->data['Reservation']['children_count'];
                    }
                }
            }

            // お問い合わせデータ
            if (!empty($this->data['Reservation']['contents'])) {
                $params['Reservation']['mail_status'] = 0;
                $paramsReservationMail['ReservationMail']['reservation_id'] = $result['Reservation']['id'];
                $paramsReservationMail['ReservationMail']['mail_datetime'] = date('Y-m-d H:i:s');
                $paramsReservationMail['ReservationMail']['contents'] = $this->data['Reservation']['contents'];
            }

            // レンナビ予約連携API用
            if ($this->ReservationAPISelect->isRennaviApiTarget($result['Reservation']['client_id'])) {
                if ($result['Reservation']['rennavi_status'] == Constant::RENNAVI_STATUS_RESERVE_FIXED) {
                    $params['Reservation']['rennavi_status'] = Constant::RENNAVI_STATUS_RESERVE_CHANGED;
                    $this->log(
                        sprintf(
                            "ReservationId : %s, ClientId : %s, ReservationStatusId : %s, RennaviStatus : %s 予約確認済(料金変更あり)",
                            $result['Reservation']['id'],
                            $result['Reservation']['client_id'],
                            $result['Reservation']['reservation_status_id'],
                            $result['Reservation']['rennavi_status']
                        ),
                        'debug'
                    );
                }
            }

            $errorString = '';
            $saveSuccess = true;
            $inquiryFlg = false;
            $apiErrorMailRequired = false;
            try {
                $this->Reservation->begin();
                if (!$this->Reservation->save($params)) {
                    $saveSuccess = false;
                    $errorString = '予約データの更新に失敗しました。';
                }
                if ($saveSuccess) {
                    if (!empty($paramsReservationMail)) {
                        if ($this->ReservationMail->save($paramsReservationMail)) {
                            $inquiryFlg = true;
                        } else {
                            $saveSuccess = false;
                            $errorString = '予約メールデータの更新に失敗しました。';
                        }
                    }
                }
                if ($saveSuccess) {
                    // 予約連携API

                    // 問い合わせの場合は連携しない
                    if (!$inquiryFlg) {
                        // 予約マスタのAPIステータスが対象外ではない場合
                        // 対象外：連携していない会社のデータ or 連携開始前のデータ
                        if ($result['Reservation']['api_status_id'] != Constant::API_STATUS_EXCLUDED) {
                            $componentName = $this->ReservationAPISelect->getApiComponentName($result['Reservation']['client_id']);
                            if (!empty($componentName)) {
                                // 会社別コンポーネントロード
                                $reservationAPI = $this->Components->load($componentName);

                                // 送信データ取得
                                $reservationAPI->setMypageReservationData($result['Reservation']['id'], Constant::API_STATUS_CHANGE);

                                // 送信
                                list($success, $apiResult) = $reservationAPI->sendReservationData();
                                if ($success) {
                                    if ($apiResult['status']) {
                                        $apiErrorMailRequired = true;
                                    } else {
                                        $budgetReservationApiFailure = [
                                            'reservation_key' => $reservationAPI->postData['request']['reservation']['reservation_key'],
                                            'created' => date("Y-m-d H:i:s"),
                                            'error_message' => !empty($apiResult['message']) ? $apiResult['message'] : ''
                                        ];
                                        $saveSuccess = false;
                                        $errorString = sprintf("変更連携が失敗しました。(%s)", (!empty($apiResult['message']) ? $apiResult['message'] : ''));
                                    }
                                } else {
                                    $budgetReservationApiFailure = [
                                        'reservation_key' => $reservationAPI->postData['request']['reservation']['reservation_key'],
                                        'created' => date("Y-m-d H:i:s"),
                                        'error_message' => !empty($apiResult['message']) ? $apiResult['message'] : ''
                                    ];
                                    $apiErrorMailRequired = true;
                                    $saveSuccess = false;
                                    $errorString = '変更連携中に何らかのエラーが発生しました。';
                                }
                            }
                        }
                    }
                }
                if ($saveSuccess) {
                    $this->Reservation->commit();
                    $apiErrorMailRequired = false;
                }
            } catch (Exception $e) {
                $errorString = sprintf("%s\n%s", $e->getMessage(), $e->getTraceAsString());
                $saveSuccess = false;
            }
            if ($saveSuccess) {
                // セッション電話番号変更
                if (!empty($params['Reservation']['tel'])) {
                    $this->Session->write('Login.tel', $params['Reservation']['tel']);
                }
                if ($inquiryFlg) {
                    // お問い合わせ通知
                    $this->ReservationUtil->sendNotificationMail($result['Reservation']['id'], 'inquiry');
                } else {
                    // 予約内容変更通知
                    $this->ReservationUtil->sendNotificationMail($result['Reservation']['id'], 'modify');
                }
                $this->redirect('/mypages/change_finish/');
            } else {
                $this->Reservation->rollback();

                // 予約番号、発生日時、エラー内容をDBに保存
                if (!empty($budgetReservationApiFailure)) {
                    $this->BudgetReservationApiFailure->save($budgetReservationApiFailure);
                }

                if ($apiErrorMailRequired) {
                    $reservationAPI->sendAlertFromMypage($result['Reservation']['control_number'], $this->domain);
                }

                $this->log(sprintf("ReservationId : %s\n%s", $result['Reservation']['id'], $errorString), 'error');
                if (!empty($params['Reservation']['arrival_flight_number']) || !empty($params['Reservation']['departure_flight_number'])) {
                    $this->log(
                        sprintf(
                            "ReservationId : %s, arrival_flight_number : %s, departure_flight_number : %s",
                            $result['Reservation']['id'],
                            $params['Reservation']['arrival_flight_number'],
                            $params['Reservation']['departure_flight_number']
                        )
                    );
                }
                $this->Session->write('message.editError', 'エラーが発生しました。管理者にお問い合わせ下さい。');
                $this->redirect('/mypages/');
            }
        }

        $this->set('changeFlag', $this->checkChangeField());
        $this->set('reserveChangeFlg', $this->ReservationUtil->reserveChangeFlg);

        $this->set('title_for_layout', '予約内容変更確認');
        $this->set('h1_for_layout', '予約内容変更確認');
        $this->set('top_txt', '予約内容変更確認');
        $this->set('description_for_layout', '予約内容変更確認');
    }

    /**
     * 編集内容確認（スマホ）
     *
     * @return void
     */
    public function sp_check()
    {
        $this->check();
    }

    /**
     * マイページデータ登録内容の変更完了ページ
     *
     * @return void
     */
    public function change_finish()
    {
        $this->set('noIndex', true);
        $this->set('title_for_layout', '登録内容の変更完了');
        $this->set('h1_for_layout', '登録内容の変更完了');
        $this->set('top_txt', 'お客様の登録内容は変更されました。');
        $this->set('description_for_layout', 'お客様の登録内容は変更されました。');
    }

    /**
     * 編集完了（スマホ）
     *
     * @return void
     */
    public function sp_change_finish()
    {
        $this->change_finish();
    }

    /**
     * マイページお問い合わせ
     *
     * @return void
     */
    public function contact()
    {
        $this->set('title_for_layout', '予約内容お問い合わせ');
        $this->set('h1_for_layout', '予約内容お問い合わせ');
        $this->set('top_txt', '予約内容お問い合わせ');
        $this->set('description_for_layout', '予約内容お問い合わせ');
    }

    /**
     * お問い合わせ（スマホ）
     *
     * @return void
     */
    public function sp_contact()
    {
        $this->contact();
    }

    /**
     * キャンセルページ
     *
     * @param string $ua
     * @return void
     */
    public function cancel($ua = 'pc')
    {
        $this->Session->write('Cancel', false);
        if ($this->Session->check('Login')) {
            $session = $this->Session->read('Login');
        } else {
            $this->redirect('/mypages/login/');
        }

        $this->set('noIndex', true);
        $this->result = $this->Reservation->getMypageDatas($session['key'], $session['tel']);

        // キャンセルポリシー
        $this->result['CancelPolicy'] =
            $this->CancelPolicy->getTextLines(
                $this->result['Reservation']['client_id'],
                $this->result['Reservation']['rent_datetime']
            );
        // INCIDENT-3044 取消手続料の徴収を廃止する
        //$this->result['AdvCancelFee'] = $this->CancelPolicy->getAdvCancelFee();

        $this->set('result', $this->result);

        // 事前決済/現地決済
        $count = $this->Reservation->getWebFlg($this->result['Reservation']['id']);
        $this->set('isPaidInAdvance', $count);

        $this->set('reason', $this->CancelReason->getCancelReasonList());

        $reservationStatus = $this->ReservationStatus->find('list', array('recursive' => -1,));
        $week = array("日", "月", "火", "水", "木", "金", "土");
        $client = $this->Client->getClientById($this->result['Reservation']['client_id']);
        $this->set(compact('reservationStatus', 'week', 'client'));

        $this->set('title_for_layout', 'キャンセルページ');
        $this->set('h1_for_layout', 'キャンセルページ');
        $this->set('top_txt', 'キャンセルページ');
        $this->set('description_for_layout', 'キャンセルページ');

        //  パンくずリスト設定
        $progressArr = $this->BreadCrumb->setMypages($this->action);
        $this->set('progress_arr', $progressArr);
    }

    /**
     * キャンセルページ（スマホ）
     *
     * @return void
     */
    public function sp_cancel()
    {
        $this->cancel();
    }

    /**
     * キャンセル確認ページ
     *
     * @param string $ua
     * @return void
     */
    public function cancel_check($ua = 'pc')
    {
        $this->set('noIndex', true);

        if ($this->request->isPost() && !empty($this->data['Reservation']['cancel_remark'])) {
            $this->result = $this->Reservation->getMyPageLogin($this->data['Reservation']['reservation_key'], $this->data['Reservation']['tel']);

            $cancelDeadlineDatetime = $this->CancelDeadline->getDatetime($this->result['Reservation']['id']);
            if ($cancelDeadlineDatetime <= date('Y-m-d H:i:s')) {
                $this->Session->write('message.cancelError', 'キャンセル可能な期限を過ぎています。');
                $this->redirect('/mypages/');
            }
            $isPaidInAdvance = $this->PaymentLog->hasPaymentInfo($this->result['Reservation']['id']);
            if ($this->isEconMaintenance && $isPaidInAdvance) {
                $this->Session->write('message.cancelError', '現在システムメンテナンス中のため、キャンセルできません。');
                $this->redirect('/mypages/');
            }

            $this->Reservation->set($this->data);
            $reason = $this->CancelReason->getCancelReasonList();
            $this->set('reason', $reason);
            if (!$this->Reservation->validates()) {
                $this->set('validationMsg', $this->Reservation->validationErrors);
                $this->set('result', $this->result);

                $this->cancel($ua);

                if (preg_match('/pc/', $ua)) {
                    $this->render('cancel');
                } elseif (preg_match('/sp/', $ua)) {
                    $this->render('sp_cancel');
                }
            }

            if (preg_match('/\/mypages\/cancel\//', $this->referer())) {
                $referer = $this->referer();
                $this->set('referer', $referer);
            }

            $this->set('title_for_layout', 'キャンセル同意確認');
            $this->set('h1_for_layout', 'キャンセル同意確認');
            $this->set('top_txt', 'キャンセル同意確認');
            $this->set('description_for_layout', 'キャンセル同意確認');
        } else {
            $this->Session->setFlash('キャンセル理由詳細が未入力です。', 'default', array('class' => 'canceErrorMsg'), 'cancelError');
            $this->redirect('/mypages/cancel/');
        }

        //  パンくずリスト設定
        $progressArr = $this->BreadCrumb->setMypages($this->action);
        $this->set('progress_arr', $progressArr);
    }

    /**
     * キャンセル確認ページ（スマホ）
     *
     * @return void
     */
    public function sp_cancel_check()
    {
        $this->cancel_check();
    }

    /**
     * キャンセル完了ページ
     *
     * @return void
     */
    public function cancel_finish($ua = 'pc')
    {
        $this->set('noIndex', true);

        if ($this->request->isPost() && !$this->Session->read('Cancel')) {
            $this->result = $this->Reservation->getMyPageLogin($this->data['Reservation']['reservation_key'], $this->data['Reservation']['tel']);

            $reservationId = $this->result['Reservation']['id'];
            $cmApplicationId = $this->Reservation->getCmApplicationId($reservationId);
            $paymentFlag = $this->PaymentAPI->getPaymentFlag($cmApplicationId);

            // 入金明細
            $paymentDetailCount = $this->PaymentDetail->find('count', [
                'conditions' => [
                    'reservation_id' => $this->result['Reservation']['id']
                    ]
                ]);

            $cancelDeadlineDatetime = $this->CancelDeadline->getDatetime($this->result['Reservation']['id']);
            if ($cancelDeadlineDatetime <= date('Y-m-d H:i:s')) {
                $this->Session->write('message.cancelError', 'キャンセル可能な期限を過ぎています。');
                $this->redirect('/mypages/');
            }
            $isPaidInAdvance = $this->PaymentLog->hasPaymentInfo($this->result['Reservation']['id']);
            if ($this->isEconMaintenance && $isPaidInAdvance) {
                $this->Session->write('message.cancelError', '現在システムメンテナンス中のため、キャンセルできません。');
                $this->redirect('/mypages/');
            }

            if ($this->result['Reservation']['reservation_status_id'] != Constant::STATUS_CANCEL) {
                // キャンセル処理
                $reservation['Reservation']['id'] = $this->result['Reservation']['id'];
                $reservation['Reservation']['reservation_status_id'] = Constant::STATUS_CANCEL;
                $reservation['Reservation']['cancel_flg'] = 1;
                $reservation['Reservation']['cancel_datetime'] = date('Y-m-d H:i:s');
                $reservation['Reservation']['cancel_staff_id'] = 0;
                $reservation['Reservation']['cancel_reason_id'] = $this->data['Reservation']['cancel_reason_id'];
                $reservation['Reservation']['cancel_remark'] = $this->data['Reservation']['cancel_remark'];

                $paymentStatus = '';
                if (!empty($this->result['Reservation']['payment_status'])) {
                    $paymentStatus = $this->__getPaymentStatus(
                        $reservation['Reservation']['reservation_status_id'],
                        $reservation['Reservation']['cancel_reason_id']
                    );
                    $reservation['Reservation']['payment_status'] = $paymentStatus;
                } elseif ($paymentDetailCount !== 0) {
                    // 未入金（差額決済前）
                    $paymentStatus = 'REFUNDED';
                }

                // レンナビ予約連携API用
                if ($this->ReservationAPISelect->isRennaviApiTarget($this->result['Reservation']['client_id'])) {
                    if ($this->result['Reservation']['rennavi_status'] == Constant::RENNAVI_STATUS_RESERVE) {
                        if (is_null($this->result['Reservation']['rennavi_one_time_pass'])) {
                            $reservation['Reservation']['rennavi_status'] = Constant::RENNAVI_STATUS_CANCEL_NOTIME;
                        } else {
                            $reservation['Reservation']['rennavi_status'] = Constant::RENNAVI_STATUS_CXL_RESERVE_FIXING;
                            $this->log('rennavi fixing reserve cancelled: '. $this->result['Reservation']['id'], LOG_DEBUG);
                        }
                    } elseif ($this->result['Reservation']['rennavi_status'] == Constant::RENNAVI_STATUS_RESERVE_FIXED ||
                            $this->result['Reservation']['rennavi_status'] == Constant::RENNAVI_STATUS_RESERVE_CHANGED) {
                        $reservation['Reservation']['rennavi_status'] = Constant::RENNAVI_STATUS_CANCEL_USER;
                    }
                }

                $errorString = '';
                $saveFlg = true;
                $reservationAPI = null;
                $apiErrorMailRequired = false;
                try {
                    $this->Reservation->begin();

                    // 在庫から削除
                    $this->CarClassReservation->updateAll(array('delete_flg' => 1), array('reservation_id' => $this->result['Reservation']['id']));

                    if ($paymentStatus == 'REFUNDED' && $paymentFlag) {
                        $reservation['Reservation']['payment_status'] = 'REFUND_REQUEST';
                    }

                    $saveResult = $this->Reservation->save($reservation, false);
                    if (!is_array($saveResult)) {
                        $saveFlg = false;
                        $errorString = '予約データのキャンセルに失敗しました。';
                    }

                    if ($saveFlg) {
                        // 返金処理
                        if ($paymentStatus == 'REFUNDED') {
                            $cal_ret = $this->CancelFeeCalculation->calculate($reservation['Reservation']['id']); // キャンセル料明細に登録
                            //$this->log("cal_ret:".print_r($cal_ret, true), LOG_DEBUG);
                            if (is_array($cal_ret) && $cal_ret['id'] != 0) { // キャンセル料明細に登録できた場合
                                if ($paymentFlag) {
                                    // 入金明細の差額/追加/減額調整を加減算していたがやめる
                                    //   差額：もう使われない想定 https://adven.backlog.jp/view/INCIDENT-3164#comment-155713357
                                    //   追加：入金前後どちらの場合も reservations.amount が実際に入金済みの金額（マイページから追加決済されたら amount に加算）
                                    //   減額：入金詳細画面から減額すると即時 reservations.amount に減額が反映される（かつ返金予定額も登録される）
                                    $price = $this->result['Reservation']['amount'] - $cal_ret['sum'];
                                    $this->log('price: '. $price, LOG_DEBUG);
                                    if ($price > 0) {
                                        // 返金処理に使用する各種IDはセッション登録値を使わない
                                        // 返金処理以外もそのうち見直した方が良い
                                        $cartId = $this->getCartId($cmApplicationId);

                                        // 返金テーブルに返金要求として登録
                                        $this->Refund->save([
                                            'reservation_id' => $reservation['Reservation']['id'],
                                            'amount' => $price,
                                            'status' => Constant::STATUS_REFUNDING
                                        ]);

                                        // 返金要求テーブルに登録
                                        $this->RefundRequest->save([
                                            'reservation_id' => $reservation['Reservation']['id'],
                                            'refund_ids' => $this->Refund->id,
                                            'created_at' => date('Y-m-d H:i:s')
                                        ]);

                                        // 入金情報取得API
                                        $targetUrl = $this->PaymentAPI->getApiUrlPayments();
                                        $res = $this->PaymentAPI->runApi(
                                            $targetUrl,
                                            'get',
                                            'cartId='. $cartId . '&serviceCd=rc'
                                        );

                                        if ($res->code !== '200') {
                                            $this->log("failed URL:".print_r($targetUrl, true), LOG_ERROR);
                                            $this->log("failed result:".print_r($res, true), LOG_ERROR);
                                            $saveFlg = false;
                                        }
                                        $res = json_decode($res->body, true);

                                        $arr = $this->PaymentAPI->createRefund(
                                            $price,
                                            $cmApplicationId,
                                            $reservation['Reservation']['id'],
                                            $res['detail'][0]['orderCode'],
                                            $this->RefundRequest->id
                                        );
                                        $url = $this->PaymentAPI->getApiUrlCreateOrUpdateRefund();
                                        $res = $this->PaymentAPI->runApi($url, 'put', $arr);
                                        if ($res->code != 200) {
                                            $this->log($res, LOG_ERROR);
                                            $saveFlg = false;
                                        }

                                        $body = json_decode($res->body, true);
                                        $this->PaymentToken->saveInsertUpdate($cmApplicationId, $body['token']);
                                    }
                                } else {
                                    if ($this->PaymentEcon->refundByReservationId($reservation['Reservation']['id'], ((int)$cal_ret['sum']))) { // 決済返金
                                        $this->Refund->save([ // 返金テーブルに返金済みとして登録
                                            'reservation_id' => $reservation['Reservation']['id'],
                                            'amount' => ($this->result['Reservation']['amount'] - $cal_ret['sum']),
                                            'status' => Constant::STATUS_REFUNDED,
                                            'refunded' => date('Y-m-d H:i:s')
                                        ]);
                                    } else {
                                        $this->Refund->save([ // 返金テーブルに返金予定として登録
                                            'reservation_id' => $reservation['Reservation']['id'],
                                            'amount' => ($this->result['Reservation']['amount'] - $cal_ret['sum']),
                                            'status' => Constant::STATUS_SCHEDULED_REFUND,
                                            'remarks' => '自動返金失敗につき'
                                        ]);
                                    }
                                }
                            }
                        } elseif ($paymentStatus == 'REFUND_REQUEST') {
                            $cal_ret = $this->CancelFeeCalculation->calculate($reservation['Reservation']['id']); // キャンセル料明細に登録
                            if (is_array($cal_ret) && $cal_ret['id'] == 0) { // キャンセル料明細に登録できた場合
                                $this->Refund->save([ // 返金テーブルに登録
                                    'reservation_id' => $reservation['Reservation']['id'],
                                    'amount' => ($this->result['Reservation']['amount'] - $cal_ret['sum']),
                                    'status' => Constant::STATUS_SCHEDULED_REFUND
                                ]);
                            }
                        }
                    }

                    if ($saveFlg) {
                        // 予約連携API

                        // 予約マスタのAPIステータスが対象外ではない場合
                        // 対象外：連携していない会社のデータ or 連携開始前のデータ
                        if ($this->result['Reservation']['api_status_id'] != Constant::API_STATUS_EXCLUDED) {
                            $componentName = $this->ReservationAPISelect->getApiComponentName($this->result['Reservation']['client_id']);
                            if (!empty($componentName)) {
                                // 会社別コンポーネントロード
                                $reservationAPI = $this->Components->load($componentName);

                                // 送信データセット
                                $reservationAPI->setMypageReservationData($this->result['Reservation']['id'], Constant::API_STATUS_CANCEL);

                                // 送信
                                list($success, $result) = $reservationAPI->sendReservationData();
                                if ($success) {
                                    if ($result['status']) {
                                        $apiErrorMailRequired = true;
                                    } else {
                                        $budgetReservationApiFailure = [
                                            'reservation_key' => $this->result['Reservation']['reservation_key'],
                                            'created' => date("Y-m-d H:i:s"),
                                            'error_message' => !empty($result['message']) ? $result['message'] : ''
                                        ];
                                        $saveFlg = false;
                                        $errorString = sprintf("キャンセル連携が失敗しました。(%s)", (!empty($result['message']) ? $result['message'] : ''));
                                    }
                                } else {
                                    $budgetReservationApiFailure = [
                                        'reservation_key' => $this->result['Reservation']['reservation_key'],
                                        'created' => date("Y-m-d H:i:s"),
                                        'error_message' => !empty($result['message']) ? $result['message'] : ''
                                    ];
                                    $apiErrorMailRequired = true;
                                    $saveFlg = false;
                                    $errorString = 'キャンセル連携中に何らかのエラーが発生しました。';
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

                if (!$saveFlg) {
                    $this->Reservation->rollback();

                    // 予約番号、発生日時、エラー内容をDBに保存
                    if (!empty($budgetReservationApiFailure)) {
                        $this->BudgetReservationApiFailure->save($budgetReservationApiFailure);
                    }
                    
                    if ($apiErrorMailRequired) {
                        $reservationAPI->sendAlertFromMypage($this->result['Reservation']['control_number'], $this->domain);
                    }

                    $this->log(sprintf("ReservationId : %s\n%s", $this->result['Reservation']['id'], $errorString), 'error');
                    $this->Session->write('message.cancelError', 'エラーが発生しました。管理者にお問い合わせ下さい。');
                    $this->redirect('/mypages/');
                }

                // 予約キャンセル通知
                $this->ReservationUtil->sendNotificationMail($this->result['Reservation']['id'], 'cancel');

                Configure::load('YotpoConfig', 'default');
                $yotpoConfig = Configure::read('Yotpo');
                if ($yotpoConfig['is_active']) {
                    //BOC Yotpo delete order
                    $yotpoOrderIDsToDelete = array($this->result['Reservation']['id']);
                    $this->YotpoAPI->deleteOrder($yotpoOrderIDsToDelete);
                    //EOC Yotpo delete order
                }
            }
            // セッション書込
            $this->Session->write('Cancel', true);
            // セッションログイン削除
            $this->deleteSession();
        } else {
            if (!empty($this->data['Reservation'])) {
                $this->result = $this->Reservation->getMyPageLogin($this->data['Reservation']['reservation_key'], $this->data['Reservation']['tel']);
            }
        }
        if (!empty($this->result)) {
            $this->request->data = $this->result;
        } else {
            $this->redirect('/mypages/');
        }
        // 検索フォーム用にデータ成形
        $this->set('title_for_layout', '予約キャンセルの完了');
        $this->set('h1_for_layout', '予約キャンセルの完了');
        $this->set('top_txt', '予約されていたレンタカープランはキャンセルされました。');
        $this->set('description_for_layout', '予約されていたレンタカープランはキャンセルされました。');

        //  パンくずリスト設定
        $progressArr = $this->BreadCrumb->setMypages($this->action);
        $this->set('progress_arr', $progressArr);
    }

    /**
     * キャンセル完了ページ（スマホ）
     *
     * @return void
     */
    public function sp_cancel_finish()
    {
        $this->cancel_finish();
    }

    /**
     * クレジット入力ページ
     *
     * @return void
     */
    public function input()
    {
        if (!$this->Session->check('Login')) {
            $this->redirect('/mypages/login/');
        }

        if ($this->Session->check('message')) {
            $sessionMessage = $this->Session->read('message');
            $this->set('sessionMessage', $sessionMessage);
            $this->Session->delete('message');
        }

        $unpaidAmount = $this->Session->read('UnpaidAmount');

        $this->set('unpaidAmount', $unpaidAmount);
        $this->set('econ_jsf_url', $this->PaymentEcon->getJsfUrl());

        //  パンくずリスト設定
        $progressArr = $this->BreadCrumb->setMypages($this->action);
        $this->set('progress_arr', $progressArr);
    }

    /**
     * クレジット入力ページ（スマホ）
     *
     * @return void
     */
    public function sp_input()
    {
        $this->input();
    }

    /**
     * AjaxによるECON事前DBログ登録
     *
     * @return string|void
     */
    public function prepare_payment()
    {
        if ($this->Session->check('Login')) {
            $session = $this->Session->read('Login');
        } else {
            $this->redirect('/mypages/login/');
        }

        $this->autoRender = false;

        $response_data_arr = [
            'result' => 'OK',
            'msg' => '',
            'session_token' => '',
            'request_id' => '',
        ];

        if ($this->request->is('ajax')) {
            $result = $this->Reservation->getMyPageLogin($session['key'], $session['tel']);

            list($billingAmount, $depositAmount, $unpaidAmount) =
                $this->__getUnpaidAmount(
                    $result['Reservation']['id'],
                    $result['Reservation']['administrative_fee']
                );
            if ($unpaidAmount <= 0) {
                $response_data_arr['msg'] = '未入金のものはありません。';
                return json_encode($response_data_arr);
            }

            $econ_res = $this->PaymentEcon->createTokenWithReservationId($result['Reservation']['id'], $this->Session->read('UnpaidAmount'));

            if (!$econ_res) {
                $this->log($this->Session->id().'[prepare_payment]PaymentEcon create token fail', LOG_DEBUG);
                $response_data_arr['msg'] = 'システムエラーが発生しました。しばらくの時間経過後もう一度お試しください。';
                return $response_data_arr;
            }

            if (!$this->PaymentEcon->save()) {
                $this->log($this->Session->id().'[preppare_payment]PaymentEcon save fail', LOG_DEBUG);
                $response_data_arr['msg'] = 'システムエラーが発生しました。しばらくの時間経過後もう一度お試しください。';
                return $response_data_arr;
            }

            $response_data_arr['session_token'] = (!empty($econ_res['session_token'])) ? $econ_res['session_token'] : '';
            $response_data_arr['request_id'] = (!empty($econ_res['request_id'])) ? $econ_res['request_id'] : '';

            return json_encode($response_data_arr);
        }
    }


    /**
     * クレジット完了ページ
     *
     * @return void
     */
    public function completion()
    {
        $cmApplicationId = '';
        if (!empty($this->Session->read('payment.ids.cm_application_id'))) {
            $cmApplicationId = $this->Session->read('payment.ids.cm_application_id');
        }
        $token = '';
        if (!empty($this->Session->read('payment.api.get.token'))) {
            $token = $this->Session->read('payment.api.get.token');
        }
        $cartId = '';
        if (!empty($this->Session->read('payment.ids.cart_id'))) {
            $cartId = $this->Session->read('payment.ids.cart_id');
        }

        if ($this->Session->check('payment')) {
            $this->Session->delete('payment');
        }

        if ($this->Session->check('Login')) {
            $session = $this->Session->read('Login');
        } else {
            $this->redirect('/mypages/login/');
        }

        $session_token = $this->request->data['sessionToken'];
        $cd3secResFlg = $this->request->data['cd3secResFlg'];
        $this->log($this->Session->id().'[completion]sessionToken:'.$session_token, LOG_DEBUG);
        $this->log($this->Session->id().'[completion]cd3secResFlg:'.$cd3secResFlg, LOG_DEBUG);


        if (!$this->isPaymentAPI) {
            if (!$this->PaymentEcon->complete($session_token, $cd3secResFlg)) {
                $this->redirect('/mypages/input/');
            }
        } else {
            // 決済APIをリリース後に旧データに対して追加支払い対応
            if (empty($cartId)) {
                if (!$this->PaymentEcon->complete($session_token, $cd3secResFlg)) {
                    $this->redirect('/mypages/input/');
                }
            }
        }

        $result = $this->Reservation->getMyPageLogin($session['key'], $session['tel']);

        if (!$this->isPaymentAPI) {
            if (!$this->PaymentEcon->cardCapture($result['Reservation']['id'], $this->Session->read('UnpaidAmount'))) {
                $this->PaymentEcon->notice(sprintf("予約番号:%s", $result['Reservation']['reservation_key']), "ECON与信->計上失敗(マイページ)");
                $this->redirect('/mypages/input/');
            }
            $this->PaymentEcon->saveResultData($result['Reservation']['reservation_key']);
        } else {
            // 決済APIをリリース後に旧データに対して追加支払い対応
            if (empty($cartId)) {
                if (!$this->PaymentEcon->cardCapture($result['Reservation']['id'], $this->Session->read('UnpaidAmount'))) {
                    $this->PaymentEcon->notice(sprintf("予約番号:%s", $result['Reservation']['reservation_key']), "ECON与信->計上失敗(マイページ)");
                    $this->redirect('/mypages/input/');
                }
                $this->PaymentEcon->saveResultData($result['Reservation']['reservation_key']);
            }
        }

        $this->Reservation->id = $result['Reservation']['id'];
        $sumAmount = ((int)$result['Reservation']['amount'] + (int)$this->Session->read('UnpaidAmount'));
        $credit_fee = 0;
        if (!$this->isPaymentAPI) {
            // 処理なし(既存処理は特に対応なし)
        } else {
            if (!empty($cartId)) {
                // 追加手数料
                $callBackValues = $this->PaymentToken->getCallBackValuesByCmApplicationId($cmApplicationId, $token);
                if (!empty($callBackValues['fee'])) {
                    $credit_fee = (int)$callBackValues['fee'];
                    if (isset($result['Reservation']['administrative_fee'])) {
                        $sum_fee = (int)$result['Reservation']['administrative_fee'] + $credit_fee;
                        $this->Reservation->saveField('administrative_fee', $sum_fee);
                    }
                }
                $sumAmount += $credit_fee;
            }
        }
        $this->Reservation->saveField('amount', $sumAmount);
        $this->Reservation->saveField('payment_status', 'PAYED');

        $this->sendPaymentMail($result['Reservation']['id']);
        $this->set('result', $result);

        //  パンくずリスト設定
        $progressArr = $this->BreadCrumb->setMypages($this->action);
        $this->set('progress_arr', $progressArr);
    }

    /**
     * クレジット完了ページ（スマホ）
     *
     * @return void
     */
    public function sp_completion()
    {
        $this->completion();
    }

    /**
     * 修正する項目判定
     *
     * @return bool
     */
    public function checkChangeField()
    {
        $changeFlg = false;

        // お問い合わせ
        if (!empty($this->data['contents'])) {
            $this->ReservationUtil->reserveChangeFlg['inquiry'];
        }

        // メールアドレス
        if (!empty($this->data['Reservation']['defaultEmail']) &&
            strcmp($this->data['Reservation']['defaultEmail'], $this->data['Reservation']['email']) != 0) {
            if (empty($this->data['Reservation']['email'])) {
                $this->Session->write('message.mailError', 'メールアドレスを入力してください。');
                $this->redirect('/mypages/edit/mail/');
            }
            if (!Validation::email($this->data['Reservation']['email'])) {
                $this->Session->write('message.mailError', 'メールアドレスの入力形式が間違っています。<br>「..」、「.@」、「スペース（空白）」が含まれるアドレスは使用できません。');
                $this->redirect('/mypages/edit/mail/');
            }
            $this->ReservationUtil->reserveChangeFlg['mail'] = true;
        }

        // 電話番号
        if (!empty($this->data['Reservation']['defaultTel'])) {
            if (empty($this->data['tel']) || !ctype_digit($this->data['tel'])) {
                $this->Session->write('message.telError', '電話番号を入力してください。');
                $this->redirect('/mypages/edit/tel/');
            }
            if (strcmp($this->data['Reservation']['defaultTel'], $this->data['tel']) != 0) {
                $this->ReservationUtil->reserveChangeFlg['tel'] = true;
            }
        }

        // ご利用人数
        if (isset($this->data['Reservation']['defaultAdults_count'])) {
            if (!preg_match('/(^[1-9]$)|(^10$)/', $this->data['Reservation']['adults_count'])) {
                $this->Session->write('message.adultError', '大人人数を1～10人で入力してください。');
                $this->redirect('/mypages/edit/count/');
            }
            if (strcmp($this->data['Reservation']['defaultAdults_count'], $this->data['Reservation']['adults_count']) != 0) {
                $this->ReservationUtil->reserveChangeFlg['passenger_count'] = true;
            }
        }
        if (isset($this->data['Reservation']['defaultChildren_count'])) {
            if (!preg_match('/(^[0-9]$)|(^10$)/', $this->data['Reservation']['children_count'])) {
                $this->Session->write('message.childError', '子供人数を0～10人で入力してください。');
                $this->redirect('/mypages/edit/count/');
            }
            if (strcmp($this->data['Reservation']['defaultChildren_count'], $this->data['Reservation']['children_count']) != 0) {
                $this->ReservationUtil->reserveChangeFlg['passenger_count'] = true;
            }
        }
        if (isset($this->data['Reservation']['defaultInfants_count'])) {
            if (strcmp($this->data['Reservation']['defaultInfants_count'], $this->data['Reservation']['infants_count']) != 0) {
                $this->Session->write('message.infantError', '幼児人数が不正に変更されました。');
                $this->redirect('/mypages/edit/count');
            }
        }
        if ($this->ReservationUtil->reserveChangeFlg['passenger_count']) {
            $personCnt = $this->ReservationUtil->calcPersonCount(
                $this->data['Reservation']['adults_count'],
                $this->data['Reservation']['children_count'],
                $this->data['Reservation']['infants_count']
            );
            if ($this->data['Reservation']['capacity'] < $personCnt) {
                $this->Session->write('message.infantError', '定員オーバーです。乗車人数は'.$this->data['Reservation']['capacity'].'名以下としてください。');
                $this->redirect('/mypages/edit/count');
            }
        }

        // 到着便
        if (isset($this->data['Reservation']['defaultFlightNumber']) &&
            strcmp(
                $this->data['Reservation']['defaultFlightNumber'],
                $this->data['Reservation']['arrival_flight_number']
            ) != 0) {
            $this->ReservationUtil->reserveChangeFlg['airline'] = true;
        } elseif (isset($this->data['Reservation']['departure_flight_number']) &&
            strcmp(
                $this->data['Reservation']['defaultDepartureFlightNumber'],
                $this->data['Reservation']['departure_flight_number']
            ) != 0) {
            $this->ReservationUtil->reserveChangeFlg['airline'] = true;
        }

        foreach ($this->ReservationUtil->reserveChangeFlg as $val) {
            if ($val) {
                $changeFlg = true;
            }
        }
        return $changeFlg;
    }

    /**
     * 募集型(ツアー)だった場合
     *
     * @return bool
     */
    private function __isTour($reservation)
    {
        if (isset($reservation['sales_price']) && $reservation['sales_price'] > 0) {
            return true;
        }

        return false;
    }

    /**
     * 予約ステータス、キャンセル理由から入金ステータスを返す
     *
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
     * 予約IDから未入金金額を産出する
     *
     * @return array
     */
    private function __getUnpaidAmount($reservationId, $administrativeFee)
    {
        $logSum = 0; // 入金済み合計額
        if ($this->isPaymentAPI) {
            $paymentIds = $this->Session->read('payment.ids');
            if (!empty($paymentIds['cart_id'])) {
                $url = $this->PaymentAPI->getApiUrlPayments();
                $param =['cartId' => $paymentIds['cart_id'], 'serviceCd' => 'rc'];
                $options = ['header' => ['Content-Type' => 'application/json']];
                $results = $this->PaymentAPI->runApi($url, 'get', $param, $options);
                // 決済APIを使用しないで決済した場合はデータが無い。
                if (!empty($results->body)) {
                    $body = json_decode($results->body, true);
                    foreach ($body['detail'] as $val) {
                        if ($val['progress'] == 3) {
                            $logSum += (int)$val['price'];
                        }
                    }
                }
            }
        }

        // この時点で$logSumが0の場合、決済APIを使用しないで決済した可能性もある。
        if ($logSum == 0) {
            $paymentLog = $this->PaymentEcon->findPaymentLog($reservationId, ['info_code' => '00000', 'keijou' => 1]);
            foreach ((array)$paymentLog as $p) {
                $logSum += $p['price'];
            }
            if ($logSum == 0) { // 入金額が0のときは与信のときなのでパスする
                return 0;
            }
        }

        $paymentDetails = $this->PaymentDetail->find('all', [
            'conditions' => [
                'reservation_id' => $reservationId,
                'delete_flg' => 0
            ]
        ]);

        $paymentDetailSum = 0;
        foreach ($paymentDetails as $paymentDetail) {
            if ($paymentDetail['PaymentDetail']['account_code'] == 'ADJUST_DIFFERENCE' ||
                $paymentDetail['PaymentDetail']['account_code'] == 'ADJUST_ADDITION') {
                if ($paymentDetail['PaymentDetail']['amount'] > 0) {
                    $paymentDetailSum += ($paymentDetail['PaymentDetail']['amount'] * $paymentDetail['PaymentDetail']['count']);
                }
            }
        }

        $reservationDetails = $this->ReservationDetail->find('all', [
            'conditions' => [
                'reservation_id' => $reservationId
            ]
        ]);

        $reservationDetailSum = 0;
        foreach ($reservationDetails as $reservationDetail) {
            $reservationDetailSum += $reservationDetail['ReservationDetail']['amount'];
        }

        $billingAmount = $paymentDetailSum + $reservationDetailSum + $administrativeFee;
        $unpaidAmount = $billingAmount - $logSum; // 請求額 - 入金額
        return [$billingAmount, $logSum, $unpaidAmount];
    }

    /**
     * 追加決済メール送信
     *
     * @param string $reservation_id
     * @return void
     */
    private function sendPaymentMail($reservation_id)
    {
        $reservation = $this->Reservation->getReservationData($reservation_id);

        $mailParam = $this->Components->load('ReservationMailParam');
        $emailView = $mailParam->getReservationMailParam($reservation);

        $emailView['status'] = '追加決済完了';
        $emailView['notification_detail'] = '下記のお客様より追加決済をいただきましたのでご報告いたします。';
        $emailView['domain'] = $this->domain;

        $emailView['cancel_policy'] = $this->CancelPolicy->getTextLines($reservation['Reservation']['client_id'], $reservation['Reservation']['rent_datetime'], false);
        // INCIDENT-3044 取消手続料の徴収を廃止する
        //$emailView['adv_cancel_fee'] = $this->CancelPolicy->getAdvCancelFee();

        $emailConfig = 'smtp';

        // 今日明日出発の場合はクライアント宛メールの件名変化
        $urgent = '';
        $rentDay = new DateTime(
            date('Y-m-d', strtotime($reservation['Reservation']['rent_datetime']))
        );
        $today = new DateTime(date('Y-m-d'));
        $interval = $today->diff($rentDay);
        if ($interval->invert == 0) {
            if ($interval->days == 0) {
                $urgent = '【本日】';
            } elseif ($interval->days == 1) {
                $urgent = '【明日】';
            }
        }

        // クライアント宛メールタイトル
        $clientSubject = '【skyticket】' .
            $urgent .
            '追加決済完了 ' .
            $reservation['Reservation']['last_name'] .
            ' ' .
            $reservation['Reservation']['first_name'] .
            '様 / ' . $reservation['CarType']['name'];

        // クライアントへの通知メール
        // 継承してメールクラスを利用
        $email = new SkyticketCakeEmail($emailConfig);
        // ユーザには非表示
        $email->non_show_user_flg = 1;
        $email
            ->viewVars($emailView)
            ->template('after_add_payment', 'suggestions_layout')
            ->subject($clientSubject);

        $clientEmail = $this->ClientEmail->getEmail($reservation['Client']['id']);
        foreach ($clientEmail as $val) {
            if (!empty($val['ClientEmail']['reservation_email'])) {
                // 各アドレスに送信
                $email->to(trim($val['ClientEmail']['reservation_email']));
                $email->send();
            }
        }
        // 貸出店舗にメールアドレスが設定されていれば送信
        if (!empty($reservation['RentOffice']['reserve_mail'])) {
            $email->to(trim($reservation['RentOffice']['reserve_mail']));
            $email->send();
        }
        if (!empty($reservation['RentOffice']['reserve_mail2'])) {
            $email->to(trim($reservation['RentOffice']['reserve_mail2']));
            $email->send();
        }
        if (!empty($reservation['RentOffice']['reserve_mail3'])) {
            $email->to(trim($reservation['RentOffice']['reserve_mail3']));
            $email->send();
        }

        // お客様へのメール
        if (!empty($reservation['Reservation']['email'])) {
            // 継承してメールクラスを利用
            $email = new SkyticketCakeEmail($emailConfig);
            // ユーザには表示
            $email->non_show_user_flg = 0;
            $email
                ->to(trim($reservation['Reservation']['email']))
                ->viewVars($emailView)
                ->template('add_payment_complete', 'suggestions_layout')
                ->subject('【skyticket】レンタカー追加代金 ご入金完了のお知らせ')
                ->send();
        }
    }

    /**
     * カートID取得
     *
     * @param string $cmApplicationId
     * @return string
     */
    private function getCartId($cmApplicationId)
    {
        return $this->PaymentAPI->getCartID($cmApplicationId);
    }

    /**
     * cm_application_idとuser_idを取得
     *
     * @param string $reservationID
     * @return array
     */
    private function getCmApplicationIDAndUserID($reservationID)
    {
        $db = GetDBInstance(DB_MAIN_MASTER);
        $sql = ""
                . "SELECT "
                . "  a.cm_application_id, a.user_id"
                . " FROM "
                . DB_NAME . ".cm_th_application AS a"
                . " INNER JOIN "
                . DB_NAME . ".cm_th_application_detail AS ad"
                . " ON "
                . "  a.cm_application_id = ad.cm_application_id "
                . " WHERE "
                . " ad.application_id = :application_id"
                . " AND ad.service_cd = :service_cd"
                . "";
        $param_arr = array(
            ":application_id" => $reservationID,
            ":service_cd" => "rc",
        );

        $result = $db->execute($sql, $param_arr, $st);
        $res = $db->fetchAll($st);
        
        $cmApplicationId = '';
        $userID = '';
        if (!empty($res['0']['cm_application_id'])) {
            $cmApplicationId = $res['0']['cm_application_id'];
        }
        if (!empty($res['0']['user_id'])) {
            $userID = $res['0']['user_id'];
        }

        return [$cmApplicationId, $userID];
    }

    /**
     * 決済API
     *
     * @param array $reservation
     * @param int $billingAmount
     * @param int $unpaidAmount
     * @return void
     */
    private function paymentApi($reservation, $billingAmount, $unpaidAmount)
    {
        $this->log('Start executing ' . __FUNCTION__ . ' function.', LOG_DEBUG);
        // 決済用ユニークのハッシュキーを発行
        while (1) {
            $hashKey = md5(uniqid(rand(), 1));
            if (!$this->PaymentToken->uniqueCheckHashKey($hashKey)) {
                break;
            }
        }

        $paymentIds    = $this->Session->read('payment.ids');
        $cmApplicationId = $paymentIds['cm_application_id'];
        $cartId = $paymentIds['cart_id'];
        $userId = $paymentIds['user_id'];

        $loginFlag = 0;
        $family_name_passport = ''; // 英語姓
        $first_name_passport = ''; // 英語名
        if (_isLogin()) {
            $loginUser = _getLoginUser();
            $loginFlag = 1;
            $family_name_passport = $loginUser['family_name_passport'];
            $first_name_passport = $loginUser['first_name_passport'];
        }

        $db = GetDBInstance(DB_MAIN_MASTER);
        $user = new User($db);
        $isCreditSave = $user->getCreditSaveByCmApplicationId($cmApplicationId);

        // -------------------------------------------------
        // appendix
        // -------------------------------------------------
        $appendix = [
            'discountData' => []
        ];
        //$paymentDetail['paymentInfo']['appendix'] = $appendix;

        // -------------------------------------------------
        // 車種名取得
        // -------------------------------------------------
        $carModels = '';
        $commodityInfo = $this->viewVars['result'];
        if (!empty($commodityInfo['CarType'])) {
            $carModels = $commodityInfo['CarType']['name'] .'（'. $commodityInfo['CarModel'];
            // 車種指定フラグ
            $flgModelSelect = (!empty($commodityInfo['CommodityItem']['car_model_id']));
            ( $flgModelSelect ) ? $carModels .= '）' : $carModels .= '他）';
        }

        // -------------------------------------------------
        // 明細の詳細
        // -------------------------------------------------
        $rcDetailOption[] = [
            'title' => '未払金額',
            'titleIndent' => false,
            'subTitle' => '',
            'currency' => 'JPY',
            'price' => $unpaidAmount,
            'quantity' => 1,
            'order' => 1,
        ];

        // -------------------------------------------------
        // 明細情報（paymentDetail）
        // -------------------------------------------------
        $paymentDetail['rc'][0] = [
            'subTotalPrice' => (int)$unpaidAmount,
            'currency' => 'JPY',
            'addPrice' => [[
                    'title' => '差額',
                    'currency' => 'JPY',
                    'price' => $unpaidAmount,
                    ]],
            'services'=>[]
        ];
        $paymentDetailJson = json_encode($paymentDetail, JSON_UNESCAPED_UNICODE);

        // -------------------------------------------------
        // 申込み情報（registrationData）
        // -------------------------------------------------
        // ログインユーザの場合は生年月日と性別、名前（英語）を取得
        $birthday = [];
        $gender = null;
        $firstName = '';
        $lastName = '';
        $loginFlag = 0;
        if (_isLogin()) {
            $loginUser = _getLoginUser();
            $birthday = $loginUser['birth_date_arr'];
            $firstName = $loginUser['first_name_passport'];
            $lastName = $loginUser['family_name_passport'];
            if ($loginUser['gender_id'] == 1) {
                $gender = 'Male';
            } else {
                $gender = 'Female';
            }
            $loginFlag = 1;
        }

        // -------------------------------------------------
        // 名前を取得(カナ)
        // -------------------------------------------------
        $firstNameKana = $reservation['Reservation']['first_name'];    // カタカナ名
        $lastNameKana = $reservation['Reservation']['last_name'];    // カタカナ姓

        // -------------------------------------------------
        // 申込情報（registrationData）
        // -------------------------------------------------
        $rcApplicant = [
            'userId' => $userId,
            'firstNameKana' => $firstNameKana,
            'lastNameKana' => $lastNameKana,
            'familyName' => $lastName,
            'firstName' => $firstName,
            'country' => 'JPN',
        ];
        if (!empty($birthday)) {
            $rcApplicant['birth'] = $birthday;
        }
        if (!empty($gender)) {
            $rcApplicant['gender'] = $gender;
        }

        $carDetail[] = [
            'shopName' => $commodityInfo['Client']['name'],
            'startDate' => $reservation['rent_datetime'],
            'endDate' => $reservation['return_datetime'],
            'totalPrice' => $billingAmount,
            'tax' => 0
        ];

        $rcDetail['jp'] = [
            'totalPrice' => $unpaidAmount,
            'totalOtherPrice' => $unpaidAmount,
            'userId' => $userId,
            'applicantFamilyName' => $lastName,
            'applicantFirstName' => $firstName,
            'applicantFamilyNameKana' => $lastNameKana,
            'applicantFirstNameKana' => $firstNameKana,
            'tel' => $reservation['tel'],
            'localContact' => null,
            'email' => $reservation['email'],
            'birth' => is_null($birthday) ? [] : $birthday,
            'gender' => $gender,
            'advertisingCode' => $reservation['advertising_cd'],
            'paymentLimit' => $reservation['payment_limit_datetime'],
            'systemFee' => 0,
            'detail' => [
                'rc' => [
                    'reservationKey' => $reservation['reservation_key'],
                    'shopName' => $commodityInfo['Client']['name'],
                    'startDate' => $reservation['rent_datetime'],
                    'startSalesOfficeName' => $commodityInfo['RentOffice']['name'],
                    'endDate' => $reservation['return_datetime'],
                    'endSalesOfficeName' => $commodityInfo['ReturnOffice']['name'],
                    'totalPrice' => $billingAmount,
                    'tax' => 0,
                    'option' => $rcDetailOption
                ]
            ]
        ];

        $rc = [
            'cmApplicationId' => $cmApplicationId,
            'currency' => 'JPY',
            'lang' => 'ja',
            'totalPrice' => $unpaidAmount,
            'totalOtherPrice' => $unpaidAmount,
            'totalOriginalPrice' => $unpaidAmount,
            'totalOtherOriginalPrice' => $unpaidAmount,
            'localPayment' => false,
            'basicAt' => $reservation['payment_limit_datetime'],
            // クーポンやgoto割引など
            'discountData' => [],
            // ポイント？
            'pointData' => [],
            'isPoint' => false,
            'detail' => $rcDetail,
        ];

        $registrationData['appendix'] = $appendix;
        $registrationData['reservation']['rc'] = [$rc];
        $registrationDataJson = json_encode($registrationData, JSON_UNESCAPED_UNICODE);

        $param = [
            // カートID
            'cartId' => $cartId,
            // 価格
            'price' => (int)$unpaidAmount,
            // 他通貨価格
            'otherPrice' => $unpaidAmount,
            // 事務手数料フラグ
            'isServiceFee' => 0,
            // 通貨
            'currency' => 'JPY',
            // レート
            'rate' => 1,
            // サービスコード
            'serviceCd' => 'rc',
            // サービス戻り先URL(cartIdがpostされます)
            'returnUrl' => $this->PaymentAPI->getMyPageReturnUrl(). $hashKey,
            // サービス取消戻り先URL
            'cancelReturnUrl' => $this->PaymentAPI->getMyPageCancelReturnUrl(). $hashKey,
            // ユーザーID
            'userId' => $userId,
            // Eメール
            'mail' => $reservation['email'],
            // 電話番号
            'tel' => $reservation['tel'],
            // カタカナ名
            'firstNameKana' => $reservation['first_name'],
            // カタカナ姓
            'lastNameKana' => $reservation['last_name'],
            // 英語名
            'firstName' => $first_name_passport,
            // 英語姓
            'lastName' => $family_name_passport,
            // 言語
            'lang' => 'ja',
            // 予約済み?(予約済み=1)
            'hold' => 0,
            // 支払期限日時
            'dueDate' => $reservation['payment_limit_datetime'],
            // 広告コード
            'advertisingCode' => '',
            // ログイン済みかどうか
            'isLogin' => $loginFlag,
            // 会員登録にチェックがあるかどうか
            'isMember' => 0,
            // クレジット登録済みかどうか（cm_tm_userのcredit_save)
            'isCreditSave' => $isCreditSave,
            // 与信コールバック先：与信処理結果をサービスにコールバックするURLを設定する（決済基盤バックエンド → サービスバックエンド）
            'authorizeUrl' => $this->PaymentAPI->getMyPageAuthorizeUrl(),
            // 取消コールバック先；取消処理結果をサービスにコールバックするURLを設定する（決済基盤バックエンド → サービスバックエンド）
            'cancelUrl' => $this->PaymentAPI->getMyPageCancelUrl(),
            // 計上コールバック先：計上処理結果をサービスにコールバックするURLを設定する（決済基盤バックエンド → サービスバックエンド）
            'captureUrl' => $this->PaymentAPI->getMyPageCaptureUrl(),
            // 明細情報
            'paymentDetail' => $paymentDetailJson,
            // 申込み情報
            'registrationData' => $registrationDataJson,
            // 追加請求か
            'isAdditionalCharge' => 1,
        ];

        $url = $this->PaymentAPI->getApiUrlPaymentsRegister();
        $results = $this->PaymentAPI->runApi($url, 'post', $param);
        if ($results->code != 200) {
            $this->log($results, LOG_ERROR);
            return ;
        }

        $body = json_decode($results->body, true);
        $this->set('paymentRedirectUrl', $body['url']);
        $this->Session->write('payment.api.get.token', $body['token']);

        $results = $this->PaymentToken->saveInsertUpdate($cmApplicationId, $body['token'], $hashKey);
        if (empty($results)) {
            $this->log('トークンの保存に失敗', LOG_ERROR);
            $this->set('paymentRedirectUrl', '');
        }
    }

    /**
     * サービスコールバック（完了処理の呼び出し）
     *
     * @return void
     */
    public function callBackReturn()
    {
        $this->log('Start executing ' . __FUNCTION__ . ' function.', LOG_DEBUG);

        $errorMsg = null;
        $query = $this->request->query;
        if (empty($query['code'])) {
            $errorMsg = 'Parameters was empty.';
        } else {
            if ($query['code'] != 'success') {
                $errorMsg = 'Error parameters';
                if (empty($query['message'])) {
                    $errorMsg = $query['message'];
                }
            }
        }

        $key = $this->request->params['identification_key'];
        $this->log('identification_key:'. $key, LOG_DEBUG);
        $arrInfo = $this->PaymentToken->getPaymentInfoByidentificationKey($key);
        $this->log('order_code:'. $arrInfo['order_code'], LOG_DEBUG);
        
        if (!empty($errorMsg)) {
            $this->set('sessionMessage', [$errorMsg]);
            $this->redirect('/mypages');
        } else {
            if (!$this->paymentApiKeijo($arrInfo['order_code'])) {
                $this->log('keijo failed', 'error');
            }
            $this->redirect('/mypages/completion/');
        }
    }

    /**
     * 決済システムを途中で離脱した時にリダイレクトする戻りURL
     *
     * @return void
     */
    public function callBackCancelReturn()
    {
        $this->log('Start executing ' . __FUNCTION__ . ' function.', LOG_DEBUG);

        $key = $this->request->params['identification_key'];
        $orderCode = $this->PaymentToken->getPaymentInfoByidentificationKey($key)['order_code'];
        $this->log('order_code:'. $orderCode, LOG_DEBUG);

        if (!empty($orderCode)) {
            // orderCode発行後の離脱
            $this->paymentApiCancel($orderCode);
        }

        $this->redirect('/mypages/');
    }

    /**
     * 与信コールバック
     *
     * @return void
     */
    public function callBackAuthorize()
    {
        $this->log('Start executing ' . __FUNCTION__ . ' function.', LOG_DEBUG);
        $result = $this->PaymentAPI->callBackAuthorize();
        $this->callBackEnd($result);
    }

    /**
     * 与信取消コールバック
     *
     * @return void
     */
    public function callBackCancel()
    {
        $this->log('Start executing ' . __FUNCTION__ . ' function.', LOG_DEBUG);
        $result = $this->PaymentAPI->callBackCancel();
        $this->callBackEnd($result);
    }

    /**
     * 計上コールバック
     *
     * @return void
     */
    public function callBackCapture()
    {
        $this->log('Start executing ' . __FUNCTION__ . ' function.', LOG_DEBUG);
        $result = $this->PaymentAPI->callBackCapture();
        $this->callBackEnd($result);
    }

    /**
     * コールバックの後処理
     *
     * @param array $result
     * @return void
     */
    public function callBackEnd($result)
    {
        $returnJson = json_encode($result, JSON_UNESCAPED_UNICODE);
        echo $returnJson;
        exit;
    }

    /**
     * 返金取消後、ステータスをSCHEDULEDに戻す
     *
     * @param string $reservationId
     * @return void
     */
    public function call_back_cancel_refund($reservationId)
    {
        $this->log('Start executing ' . __FUNCTION__ . ' function.', LOG_DEBUG);
        $this->log(print_r($this->request->data, true), LOG_DEBUG);

        try {
            // transaction開始
            $this->RefundRequest->begin();
            // Refund更新条件
            $conditions = array('reservation_id' => $reservationId);
            $refundRequestId = $this->request->data('details.serviceRefundId');
            if (!empty($refundRequestId)) {
                $refundRequest = $this->RefundRequest->find(
                    'first',
                    array('conditions' => array('id' => $refundRequestId))
                );
                if ($refundRequest) {
                    $refundIds = explode(',', $refundRequest['RefundRequest']['refund_ids']);
                    $conditions = array('id' => count($refundIds) === 1 ? $refundIds[0] : $refundIds);
                    // 返金要求データ更新
                    $this->RefundRequest->save(
                        array('id' => $refundRequestId, 'cancelled' => date('Y-m-d H:i:s'))
                    );
                }
            }

            // ステータス更新
            $this->Refund->updateAll(
                array('status' => "'".Constant::STATUS_SCHEDULED_REFUND."'"),
                $conditions
            );
            // 返金依頼中の返金情報が存在しなければ予約情報更新
            if (!$this->existsOtherRefunding($reservationId)) {
                $this->Reservation->save(['id' => $reservationId, 'payment_status' => Constant::REFUND_REQUEST]);
            }
             // コミット
            $this->RefundRequest->commit();
        } catch (Exception $e) {
            $this->log(print_r($e->getMessage(), true), LOG_DEBUG);
            // ロールバック
            $this->RefundRequest->rollback();
        }

        $result = $this->PaymentAPI->callBackCancelRefund();

        $msg = [
            'token' => $result['token'],
            'code' => 'success',
            'message' => '成功'
        ];

        $this->log(print_r($msg, true), LOG_DEBUG);

        $this->callBackEnd($msg);
    }

    /**
     * 返金依頼中の返金情報存在検証
     *
     * @param string $reservationId
     * @return bool
     */
    private function existsOtherRefunding($reservationId)
    {
        $count = $this->Refund->find(
            'count',
            array('conditions' => array(
                'reservation_id' => $reservationId,
                'status' => Constant::STATUS_REFUNDING
            ))
        );

        return $count !== 0;
    }

    /**
     * 返金処理後、ステータスをREFUNDEDにする
     *
     * @param string $reservationId
     * @return void
     */
    public function call_back_refund($reservationId)
    {
        $this->log('Start executing ' . __FUNCTION__ . ' function.', LOG_DEBUG);
        $this->log(print_r($this->request->data, true), LOG_DEBUG);
        $date = date('Y-m-d H:i:s');

        try {
            // transaction開始
            $this->RefundRequest->begin();
            // Refund更新条件
            $conditions = array('reservation_id' => $reservationId);
            $refundRequestId = $this->request->data('details.serviceRefundId');
            if (!empty($refundRequestId)) {
                $refundRequest = $this->RefundRequest->find(
                    'first',
                    array('conditions' => array('id' => $refundRequestId))
                );
                if ($refundRequest) {
                    $refundIds = explode(',', $refundRequest['RefundRequest']['refund_ids']);
                    $conditions = array('id' => count($refundIds) === 1 ? $refundIds[0] : $refundIds);
                    // 返金要求データ更新
                    $this->RefundRequest->save(
                        array('id' => $refundRequestId, 'refunded' => $date)
                    );
                }
            }

            // ステータス更新
            $this->Refund->updateAll(
                array('status' => "'".Constant::STATUS_REFUNDED."'", 'refunded' => "'". $date. "'"),
                $conditions
            );
            // 返金済以外が存在しなければ予約情報更新
            if (!$this->exitsOtherRefund($reservationId)) {
                $this->Reservation->save(['id' => $reservationId, 'payment_status' => Constant::STATUS_REFUNDED]);
            }
            // コミット
            $this->RefundRequest->commit();
        } catch (Exception $e) {
            // ロールバック
            $this->RefundRequest->rollback();
        }

        $result = $this->PaymentAPI->callBackRefund();

        $msg = [
            'token' => $result['token'],
            'code' => 'success',
            'message' => '成功'
        ];

        $this->log(print_r($msg, true), LOG_DEBUG);

        $this->callBackEnd($msg);
    }

    /**
     * 返金済以外の返金情報存在検証
     *
     * @param string $reservationId
     * @return bool
     */
    private function exitsOtherRefund($reservationId)
    {
        $count = $this->Refund->find(
            'count',
            array('conditions' => array(
                'reservation_id' => $reservationId,
                'status !=' => Constant::STATUS_REFUNDED
            ))
        );

        return $count !== 0;
    }

    /**
     * 決済API（計上）
     *
     * @param string $orderCode
     * @return bool
     */
    private function paymentApiKeijo($orderCode)
    {
        $this->log('Start executing ' . __FUNCTION__ . ' function.', LOG_DEBUG);
        $url = $this->PaymentAPI->getApiUrlPayments();
        $param =[
            'orderCode' => $orderCode
        ];

        $results = $this->PaymentAPI->runApi($url, 'put', $param);
        $this->log($results, LOG_DEBUG);
        if ($results->code != 200) {
            $this->log($results, LOG_ERROR);
            $this->Session->write('message.session', $results->reasonPhrase);
            return false;
        }

        return true;
    }

	/**
	 * 決済API（キャンセル）
	 */
	private function paymentApiCancel($orderCode)
	{
		$this->log('Start executing ' . __FUNCTION__ . ' function.', LOG_DEBUG);

		if (!$this->isYoshin($orderCode)) {
			$this->log('alredy keijo', LOG_DEBUG);
			return false;
		}

		$url = $this->PaymentAPI->getApiUrlPayments();
		$param =[
			'orderCode' => $orderCode
		];

		$results = $this->PaymentAPI->runApi($url, 'delete', $param);
		$this->log($results, LOG_DEBUG);
		if ($results->code != 200) {
			$this->log($results, LOG_ERROR);
			$this->Session->write('message.session', $results->reasonPhrase);
			return false;
		}

		return true;
	}

	/**
	 * 与信データか確認
	 */
	private function isYoshin($orderCode)
	{
		$params = [
			'id'              => '',
			'orderCode'       => $orderCode,
			'paymentFlg'      => '',
			'cartId'          => '',
			'userId'          => '',
			'cmApplicationId' => '',
			'serviceCd'       => 'rc',
			'paymentMethodId' => '',
			'createdAtStart'  => '',
			'createdAtEnd'    => '',
			'progress'        => '2', // 与信しかキャンセルできない
			'limit'           => '1',
			'page'            => '1'
		];

		$url = $this->PaymentAPI->getApiUrlPaymentsList();
		$res = $this->PaymentAPI->runApi($url, 'get', $params);
		if ($res->code != 200) {
			$this->log($res, LOG_ERROR);
			$this->Session->write('message.session', $res->reasonPhrase);
			return false;
		}
		$arr = json_decode($res->body, true)['list'];

		// 与信ではない時はfalse
		if (empty($arr['data'])) {
			return false;
		}

		return true;
	}
}
