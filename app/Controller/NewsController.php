<?php
App::uses('AppController', 'Controller');

class NewsController extends AppController {

	public $uses = array('Message');
	public $components = array('BreadCrumb');
	
	public function beforeFilter() {
		parent::beforeFilter();
	}

	private function redirect404(){
		$this->response->statusCode(404);
		$this->render('/Errors/error404');
	}

	public function show($id) {
		if(!empty($id)){
			if (!preg_match('/^[0-9]*$/', $id)) {
				$this->redirect404();
				return;
		    }

			$title = '';
			if(!empty($id)){
				$this->set('title_for_layout', 'お知らせの詳細');
				$conditions = array('Message.id' => $id,
									'Message.ui_website_flg' => 1);
				$message = $this->Message->find('first', array('conditions' => $conditions));
				if(!empty($message)){
					$title = $message['Message']['title'];
				} else {
					$this->redirect404();
					return;
				}
				$this->set('message', $message);
			}

			$this->set('title_for_layout',$title);
			$this->set('h1_for_layout',$title);
			$this->set('top_txt',$title);
			$this->set('description_for_layout',$title);

			//  パンくずリスト設定
			$progressArr = $this ->BreadCrumb->setNews($this->action, $title, $this->request->here);
			$this->set('progress_arr', $progressArr);
		}
	}
	public function sp_show($id) {
        $this->show($id);
	}

	public function index() {
		$this->set('title_for_layout', 'レンタカーのお知らせ バックナンバー');
		$this->set('h1_for_layout', 'レンタカーのお知らせ バックナンバー');
		$this->set('top_txt', 'レンタカーのお知らせ バックナンバー');
		$this->set('description_for_layout', 'レンタカーのお知らせ バックナンバー');

		$now = date("Y-m-d H:i:s");
		$conditions = array('Message.ui_website_flg' => 1,
						'Message.delete_flg' => 0,
						'Message.to_time <' => $now
					);
		
		$order = array('Message.from_time DESC');
		$messages = $this->Message->find('all', array('conditions' => $conditions, 'order' => $order));

		$this->set('messages', $messages);

		//  パンくずリスト設定
		$progressArr = $this ->BreadCrumb->setNews($this->action);
		$this->set('progress_arr', $progressArr);
	}
	public function sp_index() {
		 $this->index();
	}
}