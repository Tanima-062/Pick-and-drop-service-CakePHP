<?php
require_once('const/common_const.php');
require_once('config/mailsend_conf.php');
require_once('config/google_apikey_conf.php');

class Constant
{

    // サイト共通タイトル
    const SITE_TITLE = 'レンタカー予約サイト【スカイチケット】';

    // メール送信先
    const EMAIL_FROM_KEY = EMAIL_ADDRESS_RENTACAR;
    const EMAIL_FROM_VAL = 'スカイチケット';
    //const EMAIL_HOST = MAILSEND_HOST;  //使ってないみたい
    const EMAIL_USERNAME = EMAIL_ADDRESS_RENTACAR;
    const EMAIL_ADDITIONALPARAMETERS = EMAIL_RENTACAR_ADDITIONALPARAMETERS;

    // アクセスデバイス
    const DEVICE_PC = 'pc';
    const DEVICE_MOBILE = 'mobile';
    const DEVICE_SMART_PHONE = 'smartphone';

    // 最大人数
    const MAX_ADULTS_COUNT = 10;    // 大人
    const MAX_CHILDREN_COUNT = 10;    // 子供
    const MAX_INFANTS_COUNT = 10;    // 幼児

    // 明細種別
    const DETAIL_TYPE_BASICPRICE = 1;    // 基本料金
    const DETAIL_TYPE_OPTIONPRICE = 2;    // オプション（特典）
    const DETAIL_TYPE_CHILDSHEET = 3;    // チャイルドシート
    const DETAIL_TYPE_DROPOFFPRICE = 4;    // 乗り捨て
    const DETAIL_TYPE_NIGHTFEE = 5;        // 深夜料金
    const DETAIL_TYPE_DISCLAIMER = 6;    // 免責補償料金

    // 予約ステータス
    const STATUS_RESERVATION = 1;    // 予約
    const STATUS_CONTRACT = 2;        // 成約
    const STATUS_CANCEL = 3;        // キャンセル

    // 予約ステータス判定
    public static function isReservedStatus($statusId)
    {
        return in_array($statusId, array(1, 2));
    }

    public static function isCanceledStatus($statusId)
    {
        return in_array($statusId, array(3));
    }

    // 返金ステータス
    const STATUS_SCHEDULED_REFUND    = 'SCHEDULED'; // 返金予定
    const STATUS_REFUNDING           = 'REFUNDING'; // 返金要求
    const STATUS_REFUNDED            = 'REFUNDED'; // 返金済み

    // 入金ステータス
    const REFUND_REQUEST             = 'REFUND_REQUEST'; // 返金依頼中

    // Google reCAPTCHA
    const RECAPTCHA_SITE_KEY_PROD     = '6Le7ar8UAAAAALBbDGpQ24jziwyRBzfWCSFskgRa';
    const RECAPTCHA_SECRET_KEY_PROD     = '6Le7ar8UAAAAAJHBvHtgGZeQeZYhe_DKcl_PCW-U';
    const RECAPTCHA_SITE_KEY_DEV     = '6LfGar8UAAAAAD6IKFx4QJ7dwvkFp2nvdio5oXN2';
    const RECAPTCHA_SECRET_KEY_DEV     = '6LfGar8UAAAAAL4UDFN-QxI8JkIDO5ozXcf8ao3j';
    const RECAPTCHA_BOT_THRESHOLD     = 0.2;

    // エリアリスト
    private static $regions = array(
        'hokkaido' => '北海道',
        'tohoku' => '東北',
        'kanto' => '関東',
        'hokuriku' => '北陸',
        'koushinetsu' => '甲信越',
        'tokai' => '東海',
        'kansai' => '関西',
        'chugoku' => '中国',
        'shikoku' => '四国',
        'kyushu' => '九州',
        'okinawa' => '沖縄'
    );

    public static function regions()
    {
        return self::$regions;
    }

    // 地方別、代表都道府県
    private static $regionRepresentative = array(
        'hokkaido' => 1,        // 北海道
        'tohoku' => 4,            // 宮城
        'kanto' => 13,            // 東京
        'hokuriku' => 17,        // 石川
        'koushinetsu' => 20,    // 長野
        'tokai' => 23,            // 愛知
        'kansai' => 27,            // 大阪
        'chugoku' => 34,        // 広島
        'shikoku' => 38,        // 愛媛
        'kyushu' => 40,            // 福岡
        'okinawa' => 47            // 沖縄
    );

    public static function regionRepresentative($region)
    {
        return isset(self::$regionRepresentative[$region]) ? self::$regionRepresentative[$region] : 0;
    }

    // オプションカテゴリ 主に外部連携用
    private static $optionCategories = array(
        1     => array('id' => 1,    'name' => '喫煙',                'travelko_id' => '10101'),
        2     => array('id' => 2,    'name' => '禁煙',                'travelko_id' => '10102'),
//        3     => array('id' => 3,    'name' => '免責補償',            'travelko_id' => '10103'),
        4     => array('id' => 4,    'name' => 'カーナビ',            'travelko_id' => '10104'),
        5     => array('id' => 5,    'name' => 'ETC搭載',                'travelko_id' => '10105'),
        6     => array('id' => 6,    'name' => 'スタッドレスタイヤ',    'travelko_id' => '10106'),
        7     => array('id' => 7,    'name' => 'タイヤチェーン',        'travelko_id' => '10107'),
        8     => array('id' => 8,    'name' => '4WD',                'travelko_id' => '10108'),
        9     => array('id' => 9,    'name' => 'ジュニアシート',        'travelko_id' => '10109'),
        10     => array('id' => 10,    'name' => 'チャイルドシート',    'travelko_id' => '10110'),
        11     => array('id' => 11,    'name' => 'ベビーシート',        'travelko_id' => '10111'),
        12     => array('id' => 12,    'name' => 'ETCカード',            'travelko_id' => null),
        13     => array('id' => 13,    'name' => 'NOC補償',            'travelko_id' => null),
        14     => array('id' => 14,    'name' => '運転サポート',        'travelko_id' => null),
        15     => array('id' => 15,    'name' => 'バックモニター',        'travelko_id' => null),
        16     => array('id' => 16,    'name' => 'AUXケーブル',        'travelko_id' => null),
        17     => array('id' => 17,    'name' => 'Bluetooth',            'travelko_id' => null),
        18     => array('id' => 18,    'name' => 'ドライブレコーダー',    'travelko_id' => null),
        999     => array('id' => 999,    'name' => 'その他',                'travelko_id' => null),
    );
    public static function optionCategories()
    {
        return self::$optionCategories;
    }

    // 駅タイプ
    private static $stationTypes = array(
        0 => '駅',
        1 => '停留場',
    );

    public static function stationTypes()
    {
        return self::$stationTypes;
    }

    // 期限(キャンセル料マスタ)
    private static $cancelLimitUnit = array(
        'DAY' => '日',
        'TIME' => '時間'
    );

    public static function cancelLimitUnit()
    {
        return self::$cancelLimitUnit;
    }

    // 検索ソート順
    private static $searchSortOrders = array(
        1     => array('id' => 1,    'name' => 'おすすめ順',                'order' => 'recommended',    'direction' => SORT_ASC),
        2     => array('id' => 2,    'name' => '料金が安い順',            'order' => 'price',            'direction' => SORT_ASC),
        3     => array('id' => 3,    'name' => 'レビュー評価が高い順',    'order' => 'rating',        'direction' => SORT_DESC),
        4     => array('id' => 4,    'name' => '車両が新しい順',            'order' => 'year',            'direction' => SORT_ASC),
        5     => array('id' => 5,    'name' => '空港から近い順',            'order' => 'nearest',        'direction' => SORT_ASC),
    );

    public static function searchSortOrders()
    {
        return self::$searchSortOrders;
    }

    // バジェットレンタカーID
    const BUDGET_CLIENT_ID = 13;

    // バジェットレンタカーAPIエラー通知先
    const BUDGET_ERROR_EMAIL = 'webmaster@budgetrentacar.co.jp';

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

    // 予約連携APIステータス(連携時)
    const API_STATUS_RESERVATION = 1;    // 予約
    const API_STATUS_CHANGE         = 2;    // 変更
    const API_STATUS_CANCEL         = 3;    // キャンセル
    private static $apiStatusNames = array(
        self::API_STATUS_RESERVATION => '予約',
        self::API_STATUS_CHANGE         => '変更',
        self::API_STATUS_CANCEL         => 'キャンセル',
    );

    public static function apiStatusNames()
    {
        return self::$apiStatusNames;
    }

    // 予約連携APIステータス(マスタ)
    const API_STATUS_EXCLUDED = 0;    // 対象外
    const API_STATUS_INCLUDED = 1;    // 対象

    // 予約連携API販売種別(連携時)
    const API_SALES_TYPE_ARRANGED = 1;    // 手配旅行
    const API_SALES_TYPE_AGENT_ORGANIZED = 2;    // 募集型企画

    // トラベルコAPI用
    const TRAVELKO_MENU_CODE = 'j_rentacar';    // メニューコード
    const TRAVELKO_AGENT_CODE = 2709;            // エージェントコード
    
    // 予約連携API更新者
    const API_CHANGED_BY_USER = 1;        // ユーザー

    // スカイレンタカーID
    const SKY_CLIENT_ID = 43;
    // JネットID
    const JNET_CLIENT_ID = 5;

    // レンナビ予約ステータス
    const RENNAVI_STATUS_EXCLUDED             = 0;    // 対象外
    const RENNAVI_STATUS_RESERVE             = 1;
    const RENNAVI_STATUS_RESERVE_FIXED         = 2;
    const RENNAVI_STATUS_CANCEL_NOTIME         = 3;
    const RENNAVI_STATUS_CANCEL_USER         = 4;
    const RENNAVI_STATUS_CANCEL_FIXED         = 5;
    const RENNAVI_STATUS_RESERVE_CHANGED     = 6;
    const RENNAVI_STATUS_CANCEL_CLIENT         = 7;
    const RENNAVI_STATUS_NOSHOW                 = 8;    // スカチケには存在しない
    const RENNAVI_STATUS_GET_ALL             = 0;    // 未確認全て(APIの引数で使用)
    const RENNAVI_STATUS_CXL_RESERVE_FIXING     = 13;    // 予約後、API連携中（確定前）にマイページからキャンセルされた場合

    // レンナビ予約ステータス日本語名
    private static $rennaviStatusNames = array(
        Constant::RENNAVI_STATUS_RESERVE             => '予約未確認',
        Constant::RENNAVI_STATUS_RESERVE_FIXED         => '予約確認済',
        Constant::RENNAVI_STATUS_CANCEL_NOTIME         => 'キャンセル済（予約直後）',    // マイページより
        Constant::RENNAVI_STATUS_CANCEL_USER         => 'キャンセル未確認',            // マイページより
        Constant::RENNAVI_STATUS_CANCEL_FIXED         => 'キャンセル確認済',
        Constant::RENNAVI_STATUS_RESERVE_CHANGED     => '予約確認済（料金変更あり）',    // 料金以外の変更もこれに含める
        Constant::RENNAVI_STATUS_CANCEL_CLIENT         => 'キャンセル済（管理画面より）',
        Constant::RENNAVI_STATUS_NOSHOW                 => 'NOSHOW',
        Constant::RENNAVI_STATUS_CXL_RESERVE_FIXING     => '予約未確認'
    );

    public static function rennaviStatusNames()
    {
        return self::$rennaviStatusNames;
    }

    // レンナビ予約API対象ステータス
    private static $rennaviStatusApiTarget = array(
        Constant::RENNAVI_STATUS_RESERVE,
        Constant::RENNAVI_STATUS_CANCEL_NOTIME,
        Constant::RENNAVI_STATUS_CANCEL_USER,
        Constant::RENNAVI_STATUS_RESERVE_CHANGED,
        Constant::RENNAVI_STATUS_CANCEL_CLIENT,
        Constant::RENNAVI_STATUS_NOSHOW,
    );

    public static function rennaviStatusApiTarget()
    {
        return self::$rennaviStatusApiTarget;
    }

    // レンナビトランザクションステータス
    const RENNAVI_TRANSACTION_COUNT         = 0;
    const RENNAVI_TRANSACTION_DOWNLOAD     = 1;
    const RENNAVI_TRANSACTION_FIX         = 2;

    // 販売方法設定
    const SALES_TYPE_ARRANGED = 'ARRANGED';
    const SALES_TYPE_AGENT_ORGANIZED = 'AGENT-ORGANIZED';

    private static $salesType = [
        self::SALES_TYPE_ARRANGED        => '手配旅行',
        self::SALES_TYPE_AGENT_ORGANIZED => '募集型企画'
    ];

    public static function salesType()
    {
        return self::$salesType;
    }

    const PAYMENT_STATUS_PAYED           = "PAYED";
    //販売金額計算レート
    const ADDITIONAL_RATE = 1.0;
    
    private static $popularAirportList = array(
        array(
            "area_name" => "北海道",
            "airport_list" => array(
                array("airport_name" => "新千歳空港", "airport_link" => "/rentacar/hokkaido/chitose_international_airport/"),
                array("airport_name" => "函館空港", "airport_link" => "/rentacar/hokkaido/hakodate_airport/"),
                array("airport_name" => "稚内空港", "airport_link" => "/rentacar/hokkaido/wakkanai_airport/"),
                array("airport_name" => "女満別空港", "airport_link" => "/rentacar/hokkaido/memanbetsu_airport/"),
                array("airport_name" => "旭川空港", "airport_link" => "/rentacar/hokkaido/asahikawa_airport/"),
                array("airport_name" => "釧路空港", "airport_link" => "/rentacar/hokkaido/kushiro_airport/"),
                array("airport_name" => "帯広空港", "airport_link" => "/rentacar/hokkaido/tokachi_obihiro_airport/"),
            )
        ),
        array(
            "area_name" => "東北",
            "airport_list" => array(
                array("airport_name" => "青森空港", "airport_link" => "/rentacar/tohoku/aomori/aomori_airport/"),
                array("airport_name" => "仙台空港", "airport_link" => "/rentacar/tohoku/miyagi/sendai_airport/"),
                array("airport_name" => "山形空港", "airport_link" => "/rentacar/tohoku/yamagata/yamagata_airport_junmachi_airport/"),
            )
        ),
        array(
            "area_name" => "関東",
            "airport_list" => array(
                array("airport_name" => "成田空港", "airport_link" => "/rentacar/kanto/chiba/narita_international_airport/"),
                array("airport_name" => "羽田空港", "airport_link" => "/rentacar/kanto/tokyo/haneda_airport/"),
            )
        ),
        array(
            "area_name" => "甲信越",
            "airport_list" => array(
                array("airport_name" => "新潟空港", "airport_link" => "/rentacar/koushinetsu/niigata/niigata_airport/"),
            )
        ),
        array(
            "area_name" => "北陸",
            "airport_list" => array(
                array("airport_name" => "小松空港", "airport_link" => "/rentacar/hokuriku/ishikawa/komatsu_airport_kanazawa_airport/"),
            )
        ),
        array(
            "area_name" => "東海",
            "airport_list" => array(
                array("airport_name" => "中部国際空港(セントレア)", "airport_link" => "/rentacar/tokai/aichi/chubu_centrair_international_airport/"),
            )
        ),
        array(
            "area_name" => "関西",
            "airport_list" => array(
                array("airport_name" => "伊丹空港(大阪国際空港)", "airport_link" => "/rentacar/kansai/osaka/itami_airport/"),
                array("airport_name" => "関西国際空港", "airport_link" => "/rentacar/kansai/osaka/kansai_international_airport/"),
            )
        ),
        array(
            "area_name" => "四国",
            "airport_list" => array(
                array("airport_name" => "高松空港", "airport_link" => "/rentacar/shikoku/kagawa/takamatsu_airport/"),
                array("airport_name" => "松山空港", "airport_link" => "/rentacar/shikoku/ehime/matsuyama_airport/"),
            )
        ),
        array(
            "area_name" => "中国",
            "airport_list" => array(
                array("airport_name" => "出雲空港(出雲縁結び空港)", "airport_link" => "/rentacar/chugoku/shimane/izumo_airport/"),
                array("airport_name" => "広島空港", "airport_link" => "/rentacar/chugoku/hiroshima/hiroshima_airport/"),
            )
        ),
        array(
            "area_name" => "九州",
            "airport_list" => array(
                array("airport_name" => "福岡空港", "airport_link" => "/rentacar/kyushu/fukuoka/fukuoka_airport_itazuke_air_base/"),
                array("airport_name" => "長崎空港", "airport_link" => "/rentacar/kyushu/nagasaki/nagasaki_airport/"),
                array("airport_name" => "熊本空港", "airport_link" => "/rentacar/kyushu/kumamoto/kumamoto_airport/"),
                array("airport_name" => "大分空港", "airport_link" => "/rentacar/kyushu/oita/oita_airport/"),
                array("airport_name" => "鹿児島空港", "airport_link" => "/rentacar/kyushu/kagoshima/kagoshima_airport/"),
            )
        ),
        array(
            "area_name" => "沖縄",
            "airport_list" => array(
                array("airport_name" => "那覇空港", "airport_link" => "/rentacar/okinawa/naha_airport/"),
                array("airport_name" => "宮古空港(宮古島空港)", "airport_link" => "/rentacar/okinawa/miyako_airport/"),
                array("airport_name" => "下地島空港", "airport_link" => "/rentacar/okinawa/shimojishima_airport/"),
                array("airport_name" => "新石垣空港", "airport_link" => "/rentacar/okinawa/ishigaki_airport/"),
            )
        ),
    );

    public static function popularAirportList()
    {
        return self::$popularAirportList;
    }

    const FROM_CLIENT_AD_PREFIX = 'fm-cl';

    // CAR-388 キーに紐付け先client_id,値に紐付けたいclient_idを設定する
    private static $searchClientIdBind = array(
        '35' => array('134', '148', '152'),
    );

    public static function searchClientIdBind()
    {
        return self::$searchClientIdBind;
    }

    // Yotpoレビュー最大件数
    const YOTPO_REVIEW_LIMIT = 100;

    // レコメンド表示上限数(admin配下にも同じものがあるので値を一致させること)
    const RECOMMEND_LIMIT_CNT = 2;

    // レコメンド引き回し用クッキー名
    const PR_MAP_COOKIE_NAME = 'pr_map';
    const PR_PLAN_COOKIE_NAME = 'pr_plan';
    // レコメンド引き回し寿命
    // 60 * 60 * 24 * 1 = 86400(seconds) = 1(days)
    const PR_COOKIE_DURATION = 86400;

}
