<?php
// エラーレスポンスの内容を共通化
class ApiException extends CakeException {
	const NO_PARAM = 'パラメータがありません';
	const NO_PLAN = 'プランがありません';
	const NO_RESERVATION = '予約が見つかりません';
	const NO_CHANGE = '変更された項目はありません';
	const DO_NOT_DROPOFF = '貸し出し店舗からの乗り捨てが出来ない店舗です';
	const CAPACITY_OVER = '定員オーバーです';
	const SEAT_MAX_OVER = 'シートの最大数を超えています';
	const OPTION_MAX_OVER = 'オプションの最大数を超えています';
	const SEAT_REQUIRED = '幼児の乗車にはベビーシートやチャイルドシートが必要です';
	const PRICE_DIFFERENCE = '合計料金が相違です';
	const STOCK_OUT = '他のお客様のご予約により在庫がなくなりました';
	const NO_RESERVE_TAG = '予約タグの取得に失敗しました';
	const RESERVE_NO_DUPLICATE = '予約番号が重複しました';
	const RESERVE_INSERT_ERROR = '予約の登録に失敗しました';
	const REMARKS_INSERT_ERROR = '備考の登録に失敗しました';
	const SEAT_INSERT_ERROR = 'シートの登録に失敗しました';
	const OPTION_INSERT_ERROR = 'オプションの登録に失敗しました';
	const DETAIL_INSERT_ERROR = '予約明細の登録に失敗しました';
	const RESERVE_STOCK_INSERT_ERROR = '在庫引当に失敗しました';
	const USER_INSERT_ERROR = 'ユーザーの登録に失敗しました';
	const APPLICATION_INSERT_ERROR = '申込の登録に失敗しました';
	const APPLICATION_DETAIL_INSERT_ERROR = '申込詳細の登録に失敗しました';
	const RESERVE_UPDATE_ERROR = '予約の変更に失敗しました';
	const CANCEL_DEADLINE = 'キャンセル可能な期限を過ぎています';
	const UNDER_MAINTENANCE = '現在システムメンテナンス中です';
	const RESERVE_NO_CHANGE = 'この予約はキャンセル済みのため変更出来ません';
	const RESERVE_NO_CANCEL = 'この予約はキャンセル出来ません';
	const RESERVE_CANCEL_ERROR = '予約のキャンセルに失敗しました';
	const RESERVE_FIND_ERROR = '予約の検索に失敗しました';
	
	protected $responseData = array();

	public function __construct($message = null, $code = 400) {
		$this->responseData = array(
			'code' => $code,
			'message' => $message,
		);
		parent::__construct($message, $code);
	}

	public function getResponseData() {
		return $this->responseData;
	}
}
