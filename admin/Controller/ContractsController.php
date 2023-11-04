<?php
App::uses('AppController', 'Controller');

class ContractsController extends AppController
{

    public $components = ['PaymentAPI'];
    public $uses = ['Reservation'];

    public function beforeFilter()
    {
        parent::beforeFilter();
    }

    // --------------------------------
    // 成約年月ベースの予約データをもとにデータリストを作成
    // --------------------------------
    public function index()
    {
        $this->autoRender = false;

        // --------------------------------
        //  ダウンロードを実行した人
        // --------------------------------
        $userId = $this->cdata['id'];
        $userName = $this->cdata['username'];
        $msg = 'csv_download_user userId:' . $userId . ' userName:' . $userName;
        $this->log($msg, LOG_DEBUG);

        // --------------------------------
        //  パラメーター取得
        // --------------------------------
        if (empty($this->request->query('date'))) {
            return;
        }

        $Ym = $this->request->query('date');
        $year = substr($Ym, 0, 4);
        $month = substr($Ym, 4, 2);
        $yyyymm = $year . $month;

        // --------------------------------
        //  指定年月が成約日となる予約情報を取得
        // --------------------------------
        $reservations = $this->Reservation->getContractStatusData($yyyymm);
        if (empty($reservations)) {
            echo '対象データはありませんでした。';
            exit;
        }

        // --------------------------------
        //  出力
        // --------------------------------
        $fp = fopen('php://temp/maxmemory:'.(5*1024*1024), 'a');
        $this->response->download('contracts_' . $yyyymm . '_' .date('YmdHis') . '.csv');

        $fieldName =[
            '顧客姓',
            '顧客名',
            'クライアントID',
            'クライアント名',
            '精算管理会社ID',
            '精算管理会社名',
            '成約タイミング',
            '支払方法(決済API情報)',
            '予約番号',
            '販売方法',
            '予約ステータス',
            '貸出日時',
            '返却日時',
            '金額',
            '事前決済CXL状況(決済API情報)',
            '事前決済キャンセル料',
            '取消手続料金',
            '入金ステータス'
        ];
        fputcsv($fp, $fieldName);

        foreach ($reservations as $key => $val) {

            $lastName = $val['Reservation']['last_name'];
            $firstName = $val['Reservation']['first_name'];

            // 精算管理会社ID
            $settlementCompanyId = !empty($val['SettlementCompany']['id']) ? $val['SettlementCompany']['id'] : '未設定';

            // 精算管理会社名
            $settlementCompanyName = !empty($val['SettlementCompany']['name']) ? $val['SettlementCompany']['name'] : '未設定';

            // 成約タイミング
            $contractTiming = $val['Client']['conclusion_contract_criteria'] === 0 ? '出発日で成約にする' : '返却日で成約にする';

            // 支払方法
            $strPaid = is_null($val['Reservation']['payment_status']) ? '現地精算' : 'WEB事前決済';

            // 販売方法
            $salesTypeArray = Constant::salesType();
            $salesType = isset($salesTypeArray[$val['Commodity']['sales_type']]) ? $salesTypeArray[$val['Commodity']['sales_type']] : $val['Commodity']['sales_type'];

            // 予約ステータス,
            $reservationStatus = '';
            switch ($val['Reservation']['reservation_status_id']) {
                case 1:
                    $reservationStatus = '予約';
                    break;
                case 2:
                    $reservationStatus = '成約';
                    break;
                case 3:
                    $reservationStatus = 'キャンセル';
                    break;
                default:
                    $reservationStatus = '予約';
            }

            // 事前決済キャンセル料
            $cancelFee = isset($val['CancelDetail']['cancel_fee']) ? $val['CancelDetail']['cancel_fee'] : 0;

            // 取消手続料金,
            $adventureFee = isset($val['CancelDetail']['adventure_fee']) ? $val['CancelDetail']['adventure_fee'] : 0;

            // 事前決済CXL
            $cxlFlg = 0;
            if ($val['Reservation']['reservation_status_id'] === '3' &&
                (($val['CancelDetail']['cancel_fee'] > 0) || ($val['CancelDetail']['adventure_fee'] > 0))) {
                $cxlFlg = 1;
            }

            // 入金ステータス
            switch ($val['Reservation']['payment_status']) {
                case 'PAYED':
                    $payemntStatus = '入金済み';
                    break;
                case 'REFUND_REQUEST':
                    $payemntStatus = '返金依頼中';
                    break;
                case 'WAIT_REFUND':
                    $payemntStatus = '返金処理待ち';
                    break;
                case 'REFUNDED':
                    $payemntStatus = '返金処理済';
                    break;
                case 'TMP_REFUND_REQUEST':
                    $payemntStatus = '返金依頼受付中';
                    break;
                case 'REFUND_EXPIRED':
                    $payemntStatus = '返金期限切れ';
                    break;
                case 'NO_REFUND':
                    $payemntStatus = '返金なし';
                    break;
                default:
                    $payemntStatus = '未入金';
                    break;
            }

            $tmp = null;
            $tmp = [
                $lastName,
                $firstName,
                $val['Reservation']['client_id'],
                $val['Client']['name'],
                $settlementCompanyId,
                $settlementCompanyName,
                $contractTiming,
                $strPaid,
                $val['Reservation']['reservation_key'],
                $salesType,
                $reservationStatus,
                $val['Reservation']['rent_datetime'],
                $val['Reservation']['return_datetime'],
                $val['Reservation']['amount'],
                $cxlFlg,
                $cancelFee ,
                $adventureFee,
                $payemntStatus
            ];

            fputcsv($fp, $tmp);
        }

        rewind($fp);
        $csv = stream_get_contents($fp);
        $this->response->type('csv');
        $csv = mb_convert_encoding($csv, 'SJIS-win', 'utf8');
        $this->response->body($csv);
        fclose($fp);
    }
}
