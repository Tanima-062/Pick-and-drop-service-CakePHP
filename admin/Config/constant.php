<?php
require_once('const/common_const.php');

class Constant {

	const PATTERN_IDPASS = '^[0-9A-Za-z_-]+$';
	const PATTERN_LINKCD = '^[0-9a-z_-]+$';
	const PATTERN_GEOCODE = '^[0-9.]+$';

	// セキュリティポリシーによって変える
	const PATTERN_STRICT_PASSWORD = '\A(?=.*?[a-z])(?=.*?[A-Z])(?=.*?\d)[a-zA-Z\d]{8,20}+\z'; // JSでは動作しない
	const PASSWORD_EXPIRATION = '-100 year';

	// オプションカテゴリ 主に外部連携用
	private static $optionCategories = array(
		1	 => array('id' => 1,	'name' => '喫煙',				'travelko_id' => '10101'),
		2	 => array('id' => 2,	'name' => '禁煙',				'travelko_id' => '10102'),
//		3	 => array('id' => 3,	'name' => '免責補償',			'travelko_id' => '10103'),
		4	 => array('id' => 4,	'name' => 'カーナビ',			'travelko_id' => '10104'),
		5	 => array('id' => 5,	'name' => 'ETC搭載',				'travelko_id' => '10105'),
		6	 => array('id' => 6,	'name' => 'スタッドレスタイヤ',	'travelko_id' => '10106'),
		7	 => array('id' => 7,	'name' => 'タイヤチェーン',		'travelko_id' => '10107'),
		8	 => array('id' => 8,	'name' => '4WD',				'travelko_id' => '10108'),
		9	 => array('id' => 9,	'name' => 'ジュニアシート',		'travelko_id' => '10109'),
		10	 => array('id' => 10,	'name' => 'チャイルドシート',	'travelko_id' => '10110'),
		11	 => array('id' => 11,	'name' => 'ベビーシート',		'travelko_id' => '10111'),
		12	 => array('id' => 12,	'name' => 'ETCカード',			'travelko_id' => null),
		13	 => array('id' => 13,	'name' => 'NOC補償',			'travelko_id' => null),
		14	 => array('id' => 14,	'name' => '運転サポート',		'travelko_id' => null),
		15	 => array('id' => 15,	'name' => 'バックモニター',		'travelko_id' => null),
		16	 => array('id' => 16,	'name' => 'AUXケーブル',		'travelko_id' => null),
		17	 => array('id' => 17,	'name' => 'Bluetooth',			'travelko_id' => null),
		18	 => array('id' => 18,	'name' => 'ドライブレコーダー',	'travelko_id' => null),
		999	 => array('id' => 999,	'name' => 'その他',				'travelko_id' => null),
	);
	public static function optionCategories() { return self::$optionCategories; }

	// 駅タイプ
	private static $stationTypes = array(
		0 => '駅',
		1 => '停留場',
	);
	public static function stationTypes() { return self::$stationTypes; }

	// 期限(キャンセル料マスタ)
	private static $cancelLimitUnit = array(
		'DAY' => '日',
		'TIME' => '時間'
	);
	public static function cancelLimitUnit() { return self::$cancelLimitUnit; }

	// 期限単位(キャンセル料マスタ)
	private static $cancelFeeUnit = array(
		'RESERVE_FIXED_AMOUNT' => '1予約あたり(定額)',
		'RESERVE_FIXED_RATE' => '予約合計金額に対して(定率)',
		'RESERVE_BASIC_RATE' => '基本料金に対して(定率)'
	);
	public static function cancelFeeUnit() { return self::$cancelFeeUnit; }

	// 端数処理(円)(キャンセル料マスタ)
	private static $fractionUnit = array(
		1 => '1',
		10 => '10',
		100 => '100'
	);
	public static function fractionUnit() { return self::$fractionUnit; }

	// 端数処理(round)(キャンセル料マスタ)
	private static $fractionRound = array(
		'TRUNCATE' => '切り捨て',
		'ROUNDUP' => '切り上げ',
		'ROUNDINGOFF' => '四捨五入'
	);
	public static function fractionRound() { return self::$fractionRound; }


	// 入金ステータス
	private static $paymentStatus = array(
		'' => '未入金',
		'AUTH' => '与信',
		'PAYED' => '入金済み',
		'REFUND_REQUEST' => '返金依頼中',
		'WAIT_REFUND' => '返金処理待ち',
		'REFUNDED' => '返金処理済',
		'TMP_REFUND_REQUEST' => '返金依頼受付中',
		'REFUND_EXPIRED' => '返金期限切れ',
		'NO_REFUND' => '返金なし'
	);
	public static function paymentStatus() { return self::$paymentStatus; }

	// 入金明細科目
	private static $paymentAccountCode = array(
		'ADJUST_DIFFERENCE' => '差額調整', // この項目は廃止予定
		'ADJUST_REDUCTION' => '減額調整',  // 返金対象
		'ADJUST_ADDITION' => '追加調整'    //差分決済の対象
	);
	public static function paymentAccountCode() { return self::$paymentAccountCode; }

	// キャンセル明細科目
	private static $cancelAccountCode = array(
		'ADULT' => '【キャンセル料】大人',
		'CHILD' => '【キャンセル料】子供',
		'PRE_CHILD' => '【キャンセル料】幼児',
		'INFANT' => '【キャンセル料】乳児',
		'RESERVE_FIXED_AMOUNT' => '【キャンセル料】予約あたり',
		'RESERVE_BASIC_AMOUNT' => '【キャンセル料】基本料金あたり',
		'ADVENTURE_FEE' => '取消手続料',
		'ADMINISTRATIVE_FEE' => '決済手数料',
		'OTHER' => '【キャンセル料】その他',
		'MANUAL_INPUT' => '【キャンセル料】手入力'
	);
	public static function cancelAccountCode() { return self::$cancelAccountCode; }

	// 返金ステータス
	const STATUS_SCHEDULED_REFUND    = 'SCHEDULED'; // 返金予定
	const STATUS_REFUNDING           = 'REFUNDING'; // 返金要求
	const STATUS_REFUNDED            = 'REFUNDED'; // 返金済み

	private static $contractCondition = array(
		'CLIENT' => 'クライアント',
		'SETTLEMENT_COMPANY' => '精算管理会社'
	);
	public static function contractCondition() { return self::$contractCondition; }

	private static $accountingCondition = array(
		'FIXED_RATE' => '定率',
		'STEP_RATE' => '段階条件定率',
	);
	public static function accountingCondition() { return self::$accountingCondition; }

	private static $stepConditionType = array(
		'' => null,
		'CLOSE_NUM' => '成約数',
		'RESERVED_NUM' => '予約獲得数',
		'CLOSE_AMOUNT' => '成約金額',
		'RESERVED_AMOUNT' => '予約獲得金額',
	);
	public static function stepConditionType() { return self::$stepConditionType; }

	// 支払いサイクル
	private static $paymentCycle = array(
		1 => '1ヶ月',
		2 => '2ヶ月'
	);
	public static function paymentCycle() { return self::$paymentCycle; }

	// 成果基準額
	private static $amountIncludeTax = array(
		0 => '税込',
		1 => '税抜'
	);
	public static function amountIncludeTax() { return self::$amountIncludeTax; }

	// 手数料に係る消費税計算
	private static $isInternalTax = array(
		0 => '外税',
		1 => '内税'
	);
	public static function isInternalTax() { return self::$isInternalTax; }

	// 口座種別
	private static $accountType = array(
		0 => '普通',
		1 => '当座'
	);
	public static function accountType() { return self::$accountType; }

	// 募集型車両クラス設定
	private static $tourCarTypes = array(
		array('id' => 2, 'name' => 'コンパクト', 'example' => '(指定なしヴィッツ・フィット他)', 'passenger' => array(1, 2)),
		array('id' => 3, 'name' => 'ミドル', 'example' => '(指定なしカローラ・インプレッサ他)', 'passenger' => array(3, 4)),
		array('id' => 9, 'name' => 'RV/ミニバン', 'example' => '(指定なしフリード・エクストレイル他)', 'passenger' => array(5, 6)),
		array('id' => 5, 'name' => '1BOX/ワゴン', 'example' => '(指定なしノア・セレナ・ステップワゴン他)', 'passenger' => array(7, 8))
	);
	public static function tourCarTypes() { return self::$tourCarTypes; }
	// 募集型予約ステータス設定
	private static $tourReservationStatus = array(
		0 => '申込',
		1 => '予約',
		2 => '成約',
		3 => 'キャンセル',
		4 => '手配不可',
		5 => '緊急'
	);
	public static function tourReservationStatus() { return self::$tourReservationStatus; }

	// 販売方法設定
	const SALES_TYPE_ARRANGED = 'ARRANGED';
	const SALES_TYPE_AGENT_ORGANIZED = 'AGENT-ORGANIZED';

	private static $salesType = [
		self::SALES_TYPE_ARRANGED        => '手配旅行',
		self::SALES_TYPE_AGENT_ORGANIZED => '募集型企画'
	];
	public static function salesType() { return self::$salesType; }

	// エリアリスト
	private static $regions = array(
		'area_hokkaido' => '北海道',
		'area_tohoku' => '東北',
		'area_kanto' => '関東',
		'area_hokuriku' => '北陸',
		'area_koushinetsu' => '甲信越',
		'area_tokai' => '東海',
		'area_kansai' => '関西',
		'area_chugoku' => '中国',
		'area_shikoku' => '四国',
		'area_kyushu' => '九州',
		'area_okinawa' => '沖縄'
	);
	public static function regions() { return self::$regions; }

	// 明細種別
	const DETAIL_TYPE_BASICPRICE = 1;	// 基本料金
	const DETAIL_TYPE_OPTIONPRICE = 2;	// オプション（特典）
	const DETAIL_TYPE_CHILDSHEET = 3;	// チャイルドシート
	const DETAIL_TYPE_DROPOFFPRICE = 4;	// 乗り捨て
	const DETAIL_TYPE_NIGHTFEE = 5;		// 深夜料金
	const DETAIL_TYPE_DISCLAIMER = 6;	// 免責補償料金

	// バジェットレンタカーID
	const BUDGET_CLIENT_ID = 13;

	// バジェットレンタカーAPIエラー通知先
	const BUDGET_ERROR_EMAIL = 'webmaster@budgetrentacar.co.jp';

	// 予約連携APIステータス(連携時)
	const API_STATUS_RESERVATION = 1;	// 予約
	const API_STATUS_CHANGE		 = 2;	// 変更
	const API_STATUS_CANCEL		 = 3;	// キャンセル
	private static $apiStatusNames = array(
		self::API_STATUS_RESERVATION => '予約',
		self::API_STATUS_CHANGE		 => '変更',
		self::API_STATUS_CANCEL		 => 'キャンセル',
	);
	public static function apiStatusNames() { return self::$apiStatusNames; }

	// 予約連携APIステータス(マスタ)
	const API_STATUS_EXCLUDED = 0;	// 対象外
	const API_STATUS_INCLUDED = 1;	// 対象

	// 予約連携API販売種別(連携時)
	const API_SALES_TYPE_ARRANGED = 1;	// 手配旅行
	const API_SALES_TYPE_AGENT_ORGANIZED = 2;	// 募集型企画

	// 予約連携API更新者
	const API_CHANGED_BY_CLIENT = 2;	// Client管理画面（仕様書にAdmin管理画面ないのでこれ使う）

	// 精算書類パターン
	private static $settlementDocumentStatus = array(
		'INVOICE' => '請求書',
		'PAYMENT' => '支払通知書',
	);
	public static function settlementDocumentStatus() { return self::$settlementDocumentStatus; }

	// 精算書同期パターン
	private static $settlementSynchronizationStatus = array(
		'CREATED' => '新規作成',
		'SYNCHRONIZED' => '同期済',
	);
	public static function settlementSynchronizationStatus() { return self::$settlementSynchronizationStatus; }

	// 次月調整使用履歴パターン
	private static $nextAdjustmentsStatus = array(
		'NEW' => '新規作成',
		'USED' => '最新使用済',
		'PAST_USED' => '過去使用済',
	);
	public static function nextAdjustmentsStatus() { return self::$nextAdjustmentsStatus; }

	// 精算書営業日
	const SETTLEMENT_SUMMARY_CREATE_DATE = 3;
	const SETTLEMENT_CLOSING_DATE = 5;

	// 精算書対応オールレンタカー命名用
	private static $allrentacarId = array('11', '88');
	public static function allrentacarId() { return self::$allrentacarId; }
	const ALLRENTACAR_NAME = 'オールレンタカー株式会社';

	// 精算書用消費税名(client配下にも同じものがあるので値を一致させること)
	const TAX_NAME = '消費税';

	// ニッポンレンタカーID
	const NIPPON_CLIENT_ID = 55;

	// オリックスレンタカーID
	const ORIX_CLIENT_ID = 4;

	// 募集型予約でメール送信しないクライアントID
	private static $notSendmailClientIdsWhenAgentOrganized = array(
		self::ORIX_CLIENT_ID,
		self::BUDGET_CLIENT_ID,
		self::NIPPON_CLIENT_ID
	);

	public static function notSendmailClientIdsWhenAgentOrganized()
	{
		return self::$notSendmailClientIdsWhenAgentOrganized;
	}

	// レコメンド表示上限数(app配下にも同じものがあるので値を一致させること)
	const RECOMMEND_LIMIT_CNT = 2;

	// 一斉メール送信ステータス
	private static $bulkMailStatus = array(
		0 => '未送信',
		1 => '送信中',
		2 => '一部送信失敗',
		3 => '送信完了',
	);
	public static function bulkMailStatus() { return self::$bulkMailStatus; }

	// 一斉メール宛先送信ステータス
	private static $targetMailStatus = array(
		0 => '未送信',
		1 => '送信済',
	);
	public static function targetMailStatus() { return self::$targetMailStatus; }

}
