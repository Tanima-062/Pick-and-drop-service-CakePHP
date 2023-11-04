<?php
require_once('const/common_const.php');
require_once('config/mailsend_conf.php');
class Constant {

	// キャンセル理由ID
	const CLIENT_CANCEL = 5;
	const SYSTEM_ADMIN_CANCEL = 6;
	const CLIENT_USER_CANCEL = 7;

	// メール送信先
	const EMAIL_FROM_KEY = EMAIL_ADDRESS_RENTACAR;
	const EMAIL_FROM_VAL = 'スカイチケット';
	//const EMAIL_HOST = MAILSEND_HOST; // 使ってないみたい
	const EMAIL_USERNAME = EMAIL_ADDRESS_RENTACAR;
	const EMAIL_ADDITIONALPARAMETERS = EMAIL_RENTACAR_ADDITIONALPARAMETERS;

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

	// 明細種別
	const DETAIL_TYPE_BASICPRICE = 1;	// 基本料金
	const DETAIL_TYPE_OPTIONPRICE = 2;	// オプション（特典）
	const DETAIL_TYPE_CHILDSHEET = 3;	// チャイルドシート
	const DETAIL_TYPE_DROPOFFPRICE = 4;	// 乗り捨て
	const DETAIL_TYPE_NIGHTFEE = 5;		// 深夜料金
	const DETAIL_TYPE_DISCLAIMER = 6;	// 免責補償料金

	// 予約ステータス
	const STATUS_RESERVATION = 1;	// 予約
	const STATUS_CONTRACT = 2;		// 成約
	const STATUS_CANCEL = 3;		// キャンセル

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

	// トラベルコAPI用
	const TRAVELKO_MENU_CODE = 'j_rentacar';	// メニューコード
	const TRAVELKO_AGENT_CODE = 2709;			// エージェントコード

	// 予約連携API更新者
	const API_CHANGED_BY_CLIENT = 2;	// Client管理画面

	// スカイレンタカーID
	const SKY_CLIENT_ID = 43;
	// JネットID
	const JNET_CLIENT_ID = 5;

	// レンナビ予約ステータス
	const RENNAVI_STATUS_EXCLUDED			 = 0;	// 対象外
	const RENNAVI_STATUS_RESERVE			 = 1;
	const RENNAVI_STATUS_RESERVE_FIXED		 = 2;
	const RENNAVI_STATUS_CANCEL_NOTIME		 = 3;
	const RENNAVI_STATUS_CANCEL_USER		 = 4;
	const RENNAVI_STATUS_CANCEL_FIXED		 = 5;
	const RENNAVI_STATUS_RESERVE_CHANGED	 = 6;
	const RENNAVI_STATUS_CANCEL_CLIENT		 = 7;
	const RENNAVI_STATUS_NOSHOW				 = 8;	// スカチケには存在しない
	const RENNAVI_STATUS_CXL_RESERVE_FIXING	 = 13;	// 予約後、API連携中（確定前）にマイページからキャンセルされた場合

	// レンナビ予約ステータス日本語名
	private static $rennaviStatusNames = array(
		Constant::RENNAVI_STATUS_EXCLUDED			 => '対象外',
		Constant::RENNAVI_STATUS_RESERVE			 => '予約未確認',
		Constant::RENNAVI_STATUS_RESERVE_FIXED		 => '予約確認済',
		Constant::RENNAVI_STATUS_CANCEL_NOTIME		 => 'キャンセル済（予約直後）',	// マイページより
		Constant::RENNAVI_STATUS_CANCEL_USER		 => 'キャンセル未確認',			// マイページより
		Constant::RENNAVI_STATUS_CANCEL_FIXED		 => 'キャンセル確認済',
		Constant::RENNAVI_STATUS_RESERVE_CHANGED	 => '予約確認済（料金変更あり）',	// 料金以外の変更もこれに含める
		Constant::RENNAVI_STATUS_CANCEL_CLIENT		 => 'キャンセル済（管理画面より）',
		Constant::RENNAVI_STATUS_NOSHOW				 => 'NOSHOW',
		Constant::RENNAVI_STATUS_CXL_RESERVE_FIXING	 => '予約未確認'
	);
	public static function rennaviStatusNames() { return self::$rennaviStatusNames; }

	// 販売方法設定
	const SALES_TYPE_ARRANGED = 'ARRANGED';
	const SALES_TYPE_AGENT_ORGANIZED = 'AGENT-ORGANIZED';

	private static $salesType = [
		self::SALES_TYPE_ARRANGED		 => '手配旅行',
		self::SALES_TYPE_AGENT_ORGANIZED => '募集型企画'
	];
	public static function salesType() { return self::$salesType; }

	/**
	 * 募集型企画商品であるか
	 *
	 * @param bool   $isManagedPackage clients.is_managed_package
	 * @param string $salesType        commodities.sales_type
	 * @return boolean
	 */
	public static function isAgentOrganizedCommodity($isManagedPackage, $salesType)
	{
		return (
			($isManagedPackage === true) &&
			($salesType === self::SALES_TYPE_AGENT_ORGANIZED)
		);
	}

	// 週
	private static $week = array(
		['jp' => '月', 'en' => 'mon'],
		['jp' => '火', 'en' => 'tue'],
		['jp' => '水', 'en' => 'wed'],
		['jp' => '木', 'en' => 'thu'],
		['jp' => '金', 'en' => 'fri'],
		['jp' => '土', 'en' => 'sat'],
		['jp' => '日', 'en' => 'sun'],
		['jp' => '祝', 'en' => 'hol'],
	);
	public static function weekJp() { return Hash::extract(self::$week, '{n}.jp'); }
	public static function weekEn() { return Hash::extract(self::$week, '{n}.en'); }

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

	// 口座種別
	private static $accountType = array(
		0 => '普通',
		1 => '当座'
	);
	public static function accountType() { return self::$accountType; }

	// 精算書対応オールレンタカー命名用
	private static $allrentacarId = array('11', '88');
	public static function allrentacarId() { return self::$allrentacarId; }
	const ALLRENTACAR_NAME = 'オールレンタカー株式会社';

	// 精算書用消費税名(admin配下にも同じものがあるので値を一致させること)
	const TAX_NAME = '消費税';

	// オリックスレンタカーID
	const ORIX_CLIENT_ID = 4;

	// 日産レンタカーID
	const NISSAN_CLIENT_ID = 46;

	// ニッポンレンタカーID
	const NIPPON_CLIENT_ID = 55;

	// トヨタレンタカーID
	const TOYOTA_CLIENT_ID = 75;

	// メール送信しないクライアントID
	private static $notSendmailClientIdsList = array(
		self::ORIX_CLIENT_ID,
		self::BUDGET_CLIENT_ID,
		self::NISSAN_CLIENT_ID,
		self::NIPPON_CLIENT_ID,
		self::TOYOTA_CLIENT_ID
	);

	public static function notSendmailClientIds()
	{
		return self::$notSendmailClientIdsList;
	}
}
