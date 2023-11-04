<?php
App::uses('AppShell', 'Console/Command');
App::uses('ComponentCollection', 'Controller');
App::uses('Controller', 'Controller');
App::uses('YotpoAPIComponent', 'Controller/Component');
require_once("encrypt_class.php");

class YotpoReviewShell extends AppShell {

	public $uses = array('YotpoReview', 'Office');

	public function startup() {
		$collection = new ComponentCollection();
		$this->controller = new Controller();
		$this->YotpoAPI = new YotpoAPIComponent($collection);
		$this->YotpoAPI->startup($this->controller);
		parent::startup();
	}

	public function insertYotpoReviews(){
		$now = date('Y-m-d H:i:s');
		echo "Yotpo Review Insert Start : $now \n";

		$encrypt = new Encrypt();

		$t = new DateTime('-1 day');
		$t->setTimeZone(new DateTimeZone('UTC'));
		$sinceUpdatedAt = $t->format('Y-m-d');

		$this->YotpoReview->begin();

		$error = false;
		$reviewCount = 0;
		for ($i = 1; $i <= 10; $i++) {
			// 編集日時が$sinceUpdatedAt以降のレビューの$iページ目(100件毎)を取得
			// サイトから賛成・反対の投票や、管理画面から公開・非公開の変更等されていない場合、編集日時は投稿日時にほぼ等しい
			$result = (array) $this->YotpoAPI->retrieveAllReviews($sinceUpdatedAt, $i);

			$reviews = $result['reviews'];
			if (empty($reviews)) {
				break;
			}

			$now = date('Y-m-d H:i:s');
			$datas = array();
			foreach ($reviews as $review) {
				if (is_numeric($review->sku)) {
					$officeId = intval($review->sku);
					$office = $this->Office->find('first', array(
						'fields' => array('Office.client_id'),
						'conditions' => array('Office.id' => $officeId),
						'recursive' => -1
					));
					$clientId = !empty($office) ? intval($office['Office']['client_id']) : 0;
					$datas[] = array(
						'review_id' => $review->id,
						'title' => $review->title,
						'content' => $review->content,
						'score' => $review->score,
						'votes_up' => $review->votes_up,
						'votes_down' => $review->votes_down,
						'created_at' => date('Y-m-d H:i:s', strtotime($review->created_at)),
						'updated_at' => date('Y-m-d H:i:s', strtotime($review->updated_at)),
						'sku' => $review->sku,
						'name_enc' => $encrypt->encrypt($review->name),
						'email_enc' => $encrypt->encrypt($review->email),
						'reviewer_type' => $review->reviewer_type,
						'unpublished' => intval($review->deleted),
						'client_id' => $clientId,
						'office_id' => $officeId,
						'created' => $now,
						'modified' => $now,
						'delete_flg' => 0
					);
				} else {
					// sku = ダミーIDの場合、削除フラグ立てて登録
					$datas[] = array(
						'review_id' => $review->id,
						'title' => $review->title,
						'content' => $review->content,
						'score' => $review->score,
						'votes_up' => $review->votes_up,
						'votes_down' => $review->votes_down,
						'created_at' => date('Y-m-d H:i:s', strtotime($review->created_at)),
						'updated_at' => date('Y-m-d H:i:s', strtotime($review->updated_at)),
						'sku' => $review->sku,
						'name_enc' => $encrypt->encrypt($review->name),
						'email_enc' => $encrypt->encrypt($review->email),
						'reviewer_type' => $review->reviewer_type,
						'unpublished' => intval($review->deleted),
						'client_id' => 0,
						'office_id' => 0,
						'created' => $now,
						'modified' => $now,
						'delete_flg' => 1
					);
				}
			}
			$reviewTuples = $this->getReviewTuples($datas);

			$sql = "INSERT INTO
					  rentacar.yotpo_reviews
					    (review_id, title, content, score, votes_up, votes_down, created_at, updated_at, sku, name_enc, email_enc, reviewer_type, unpublished, client_id, office_id, created, modified, delete_flg)
					VALUES
					  $reviewTuples
					ON DUPLICATE KEY UPDATE
					  review_id = VALUES(review_id), votes_up = VALUES(votes_up), votes_down = VALUES(votes_down), updated_at = VALUES(updated_at), unpublished = VALUES(unpublished), modified = VALUES(modified)";

			try {
				$result = $this->YotpoReview->query($sql);
				if ($result === false) {
					$error = true;
				}
			} catch (PDOException $e) {
				$message = $e->getMessage();
				echo "Yotpo Review Insert PDOEx : $message \n";
				$error = true;
			}

			if ($error) {
				$errorIds = implode(',', Hash::extract($datas, '{n}.review_id'));
				echo "Yotpo Review Insert Error : $errorIds \n";
				break;
			} else {
				$reviewCount += count($reviews);
			}

			sleep(10);
		}

		if ($error) {
			$this->YotpoReview->rollback();
			echo "Yotpo Review Insert Error : Rollback \n";
		} else {
			$this->YotpoReview->commit();
			echo "Yotpo Review Insert Count : $reviewCount \n";
		}

		$now = date('Y-m-d H:i:s');
		echo "Yotpo Review Insert End   : $now \n";
	}

	private function getReviewTuples($datas) {
		$tuples = '';
		for ($i = 0; $i < count($datas); $i++) {
			$data = $datas[$i];
			if ($i !== 0) {
				$tuples .= ',';
			}
			$tuples .= "(".
				$data['review_id'].",'".$data['title']."','".$data['content']."',".
				$data['score'].",".$data['votes_up'].",".$data['votes_down'].",'".$data['created_at']."','".$data['updated_at']."','".
				$data['sku']."','".$data['name_enc']."','".$data['email_enc']."','".$data['reviewer_type']."',".$data['unpublished'].",".
				$data['client_id'].",".$data['office_id'].",'".$data['created']."','".$data['modified']."',".$data['delete_flg'].
			")";
		}
		return $tuples;
	}

	public function main() {
		$now = date('Y-m-d H:i:s');
		echo "Yotpo Review Batch Start : $now \n";

		$this->insertYotpoReviews();

		$now = date('Y-m-d H:i:s');
		echo "Yotpo Review Batch End   : $now \n";
	}
}