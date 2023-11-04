<?php
App::uses('DispatcherFilter', 'Routing');

class SkyticketAppliFilter extends DispatcherFilter {

	public $priority = 9;

	// query にゴミデータが入った場合に削除 (?_app=1)
	public function beforeDispatch(CakeEvent $event) {

		$query_arr = $event->data['request']->query;


		//アプリ用判別
		if(!empty($query_arr['_app'])){
			$_SESSION['user_agent'] = 'app';
		} else {
			$_SESSION['user_agent'] = $_SERVER['HTTP_USER_AGENT'];
		}

		// Agent 情報を環境変数に展開
		require_once('process/ua2env.php');	// Agent展開ライブラリ
		_UserAgent2DefineEmvironment();

		$correct_arr = $this->_correctQueryArray($query_arr);
		if ($correct_arr) {
			$event->data['request']->query = $correct_arr;
		}
	}
	
	private function _correctQueryArray($array) {
		$ret_array = $array;
		$correct_string = '?_app=';
		if (!empty($array)) {
			$cnt = count($array);
			$i = 0;
			foreach ($array AS $k => $v) {
				$i++;
				if ($i === $cnt) {
					if (is_array($v)) {
						$correct_arr = $this->_correctQueryArray($v);
						if ($correct_arr) {
							$ret_array[$k] = $correct_arr;
							return $ret_array;
						} else {
							return false;
						}
					} elseif (mb_strlen($v) >= 7) {
						if (mb_substr($v, -7, 6) === $correct_string) {
							$ret_array[$k] = preg_replace('/\?_app=[0-9]/', '', $v);
							return $ret_array;
						} else {
							return false;
						}
					}
				}
			}
		}
		return false;
	}
}