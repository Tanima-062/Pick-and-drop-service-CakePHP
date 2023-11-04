<?php
class ApiAuthComponent extends Component {

	public function initialize(Controller $controller) {
		$this->controller = $controller;
	}

	public function authenticate($request, $clientId) {

		// バジェットは対象外（グローバルIP制限のみ）
		if ($clientId == Constant::BUDGET_CLIENT_ID) {
			return true;
		}

		$authKey = $request->header('x-auth-key');
		if (empty($authKey)) {
			return false;
		}

		// 必要な会社出てきたら復活させる
		/*switch ($clientId) {
			case Constant::BUDGET_CLIENT_ID:	// バジェット
				if ($authKey !== 'DcGWXLQGiEDNL2Y_gZJr') {
					return false;
				}
				break;
			default:
				return false;
				break;
		}*/

		return true;
	}

}
