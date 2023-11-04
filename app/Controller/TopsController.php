<?php
App::uses('AppController', 'Controller');
/**
 * Tops Controller
 *
 * @property Tops $Tops
 */
class TopsController extends AppController {

	public $uses = array('Message', 'Client', 'Content', 'Landmark', 'Office', 'Commodity', 'KeyValue', 'PublicHoliday');
	public $use_searchbox = true;
	public $use_yotpo = true;
	public $use_yotpo_rating = true;
	public $components = array('BreadCrumb');

	//仮で、option_manage.jsを指定
	public $new_js = true;

	public function beforeFilter() {
		parent::beforeFilter();
	}

/**
 * index method
 *
 * @return void
 */
	public function index() {

		//都道府県IDからエリアリストを取得　初期値は北海道
		$this->OptionsManage->setSearchOptions($this->request->query);

		// リンク用日付配列
		$link_date_arr = date_parse(date('Ymd',strtotime('+7 day')));		// 7日後の日付
		$link_date_arr2 = date_parse(date('Ymd',strtotime('+2 day')));		// 2日後の日付(2017.09.06変更)

		$this->set(compact('link_date_arr', 'link_date_arr2'));

		// タイムズにアフィリエイトで流すようのリンク
		$this->set('link_relay_times_car_rental', '/rentacar/af_relay/1/');
		$this->set('link_relay_option', ' target="_blank" rel="nofollow"');

		$now = date("Y-m-d H:i:s");
		$conditions = array('Message.ui_website_flg' => 1,
						'Message.delete_flg' => 0,
						'Message.from_time <=' => $now,
						'Message.to_time >=' => $now
					);
		$order = array('Message.from_time DESC');
		$messages = $this->Message->find('all', array('conditions' => $conditions,'order' => $order));

		// 那覇(326)、新千歳(330)、福岡(309)、新石垣(329)の各空港の最安値
		$airports = $this->Landmark->findC('list', array(
			'conditions' => array('id' => array(326, 330, 309, 329))
		));
		$airportPrices = array();
		foreach ($airports as $airportId => $airportName) {
			$price = $this->getPriceByAirport($airportId);
			if ($price !== -1) {
				$airportPrices[$airportId] = array(
					'airport_id' => $airportId,
					'airport_name' => $airportName,
					'price' => $price
				);
			}
        }

        // キャッシュされたYOTPOのjsonをDBから取得
        $main_widget = '';
        for ($page = 1; $page < 10; $page++) {
            $jsonKeyValue = $this->KeyValue->find('first', array('conditions' => array('key'=> 'yotpo_json_top_'.$page)));
            if($jsonKeyValue){
                $review = json_decode($jsonKeyValue['KeyValue']['value']);
                $main_widget .= $review[0]->result;
            }
        }
        
        $this->set('main_widget', $main_widget);
		$this->set('messages', $messages);
		$this->set('airportPrices', $airportPrices);
		$this->set('contents', $this->Content->getContentsList());
		$this->set('clientList', $this->Client->getClientList());

		//  パンくずリスト設定
		$progressArr = $this->BreadCrumb->setTops($this->action);
		$this->set('progress_arr', $progressArr);
	}

	public function sp_index() {
		$this->index();
	}

	public function photogallery() {
		$this->use_searchbox = false;
		$this->new_js = false;

		$this->set('use_searchbox', $this->use_searchbox);
		$this->set('new_js', $this->new_js);

		$this->set('title_for_layout','レンタカー会社に関する写真投稿');
		$this->set('description_for_layout','レンタカー会社に関する写真投稿');
		$this->set('keywords','レンタカー会社に関する写真投稿');

		//  パンくずリスト設定
		$progressArr = $this->BreadCrumb->setTops($this->action);
		$this->set('progress_arr', $progressArr);
	}
	public function sp_photogallery() {
		$this->photogallery();
	}

	private function getPriceByAirport($airportId) {
		$offices = $this->Office->getOfficeNearListByAirportId($airportId);
		if (empty($offices)) {
			return -1;
		}
		$officeIds = Hash::extract($offices, '{n}.Office.id');
		$prices = $this->Commodity->getPriceByOfficeId($officeIds);
		if (empty($prices)) {
			return -1;
		}
		return min(Hash::extract($prices, '{n}.price'));
	}

	public function getPublicHoliday() {
		$this->autoRender = false;
		$this->response->type('json');

		$now = new DateTime();
		$termFrom = $now->format('Y-m-d');
		$publicHolidays = $this->PublicHoliday->findC('list', array(
			'fields' => array(
				'date',
				'name',
			),
			'conditions' => array(
				'date >=' => $termFrom,
				'delete_flg' => 0,
			),
			'recursive' => -1,
		));

		return json_encode($publicHolidays);
	}
}
