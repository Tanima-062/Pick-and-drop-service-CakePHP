<?php
App::uses('AppShell', 'Console/Command');

class AccessDynamicShell extends AppShell {

	public $uses = array('Client', 'Office');

	const selectSql = '
		SELECT
		  ro.id,
		  ro.name,
		  ros.id,
		  ros.nearest_transport,
		  ros.other_transport,
		  ros.method_of_transport,
		  ros.required_transport_time
		FROM
		  rentacar.offices ro
		LEFT JOIN
		  rentacar.office_supplements ros
		    ON ros.office_id = ro.id
		WHERE
		  ro.client_id = :client_id AND
		  ro.delete_flg = 0
	';

	const updateSql = '
		UPDATE
		  rentacar.offices
		SET
		  access_dynamic = :access_dynamic,
		  modified = NOW()
		WHERE
		  id = :id
	';

	public function update() {

		$now = date('Y-m-d H:i:s');
		echo "AccessDynamic update start : $now \n";

		// パラメータの会社ID（複数可）を取得
		$clientIdArray = array();
		if (!empty($this->args)) {
			foreach ($this->args as $arg) {
				if (!preg_match('/^[1-9][0-9]*$/', $arg)) {
					echo sprintf("会社IDが不正です。(%s)\n", $arg);
					return;
				}
				$clientIdArray[] = $arg;
			}
		}

		// 会社ID指定されていればその会社、指定なければ全会社
		$clientOptions =  array(
			'conditions' => array(
				'Client.delete_flg' => 0,
			),
			'recursive' => -1,
		);
		if (!empty($clientIdArray)) {
			$clientOptions['conditions']['Client.id'] = $clientIdArray;
		}

		// 対象レンタカー会社の一覧
		$clientList = $this->Client->find('list', $clientOptions);
		if (empty($clientList)) {
			echo "処理対象の会社が存在しませんでした。\n";
			return;
		}

		// 会社ごとに処理
		$noAccessInfos = array();
		foreach ($clientList as $clientId => $clientName) {
			$officeData = $this->Office->query(self::selectSql, array('client_id' => $clientId));

			$updateData = array();
			foreach ($officeData as $data) {
				if (empty($data['ros']['id'])) {
					if (!isset($noAccessInfos[$clientId])) {
						$noAccessInfos[$clientId] = array();
					}
					$noAccessInfos[$clientId][] = $data['ro'];
					continue;
				}

				$accessDynamic = $this->Office->getAccessDynamic(
					$data['ros']['nearest_transport'],
					$data['ro']['id'],
					$data['ros']['other_transport'],
					$data['ros']['method_of_transport'],
					$data['ros']['required_transport_time'],
					true										// バッチから呼び出し
				);

				$updateData[] = array('id' => $data['ro']['id'], 'name' => $data['ro']['name'], 'access_dynamic' => $accessDynamic);
			}

			if (!empty($updateData)) {
				$this->Office->begin();

				$error = false;
				$exMessage = '';
				foreach ($updateData as $data) {
					try {
						$ret = $this->Office->query(self::updateSql, array('id' => $data['id'], 'access_dynamic' => $data['access_dynamic']));
						if ($ret === false) {
							$error = true;
						}
					} catch (Exception $ex) {
						$error = true;
						$exMessage = sprintf("(%s)\n", $ex->getMessage());
					}
					if ($error) {
						echo sprintf("%s %s (%d) の更新に失敗しました。\n%s", $clientName, $data['name'], $data['id'], $exMessage);
						break;
					}
				}

				if ($error) {
					echo sprintf("%s (%d) の店舗データは更新されませんでした。(ロールバック)\n", $clientName, $clientId);
					$this->Office->rollback();
				} else {
					$this->Office->commit();
				}
			}
		}

		if (!empty($noAccessInfos)) {
			echo "以下の店舗は交通アクセス情報が未入力です。\n";
			foreach ($noAccessInfos as $clientId => $data) {
				echo sprintf("%s (%d) : \n", $clientList[$clientId], $clientId);
				foreach ($data as $v) {
					echo sprintf("\t%s (%d)\n", $v['name'], $v['id']);
				}
			}
		}

		$now = date('Y-m-d H:i:s');
		echo "AccessDynamic update end   : $now \n";
	}
}
