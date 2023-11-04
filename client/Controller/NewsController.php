<?php
App::uses('AppController', 'Controller');

class NewsController extends AppController {

	public $uses = array('Message');

	public function beforeFilter() {
		parent::beforeFilter();
	}

	public function index() {
		$this->set('title_for_layout', 'お知らせ バックナンバー');

		$now = date("Y-m-d H:i:s");
		$conditions = array('Message.ui_client_flg' => 1,
						'Message.delete_flg' => 0,
						'Message.to_time <' => $now
					);
		$order = array('Message.from_time DESC');
		$messages = $this->Message->find('all', array('conditions' => $conditions, 'order' => $order));

		$this->set('messages', $messages);
	}

	public function show($id) {
		if(!empty($id)){
			if (!preg_match('/^[0-9]*$/', $id)) {
				$this->redirect('/Tops/');
				return;
		    }

			$title = '';
			if(!empty($id)){
				$this->set('title_for_layout', 'お知らせの詳細');
				$conditions = array('Message.id' => $id,
									'Message.ui_client_flg' => 1);
				$message = $this->Message->find('first', array('conditions' => $conditions));
				if(!empty($message)){
					$title = $message['Message']['title'];
				} else {
					$this->redirect('/Tops/');
					return;
				}
				$this->set('message', $message);
			}

			$this->set('title_for_layout',$title);
			$this->set('h1_for_layout',$title);
			$this->set('top_txt',$title);
			$this->set('description_for_layout',$title);
		} else {
			$this->redirect('/Tops/');
		}
	}
}