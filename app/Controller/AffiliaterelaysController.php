<?php
App::uses('AppController', 'Controller');
/**
 * Tops Controller
 *
 * @property Affiliaterelays $Affiliaterelays
 */
class AffiliaterelaysController extends AppController {

	public function beforeFilter() {
		parent::beforeFilter();
	}

/**
 * index method
 *
 * @return void
 */
	public function index() {
		if (empty($this->params['affiliate_id'])) {
			throw new NotFoundException();
		} elseif (!in_array($this->params['affiliate_id'], array(1))) {
			throw new NotFoundException();
		}
		
		// タイムズにアフィリエイトで流すようのリンク
		$this->set('link_times_car_rental', 'http://rental.timescar.jp/af/5000103075/');
		
		$this->autoLayout = false;
	}

}
