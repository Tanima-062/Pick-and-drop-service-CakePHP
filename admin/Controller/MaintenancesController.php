<?php
App::uses('AppController', 'Controller');
/**
 * Maintenances Controller
 */
class MaintenancesController extends AppController {

	public $uses = array('Maintenance');

	public function index() {

		$options = array(
			'order' => array(
				'id' => 'asc',
			),
			'limit' => 30,
		);

		$this->paginate = $options;
		$this->set('maintenances', $this->paginate());
		$this->set('types', array(
			'ECON' => 'イーコンメンテモード',
			'PaymentAPI' => '決済API利用'
		));
		$this->set('states', array(
			0 => 'OFF',
			1 => 'ON'
		));
	}

	public function modeChange($id = null, $is_under_maintenance = false) {
		if (!$this->request->is('post')) {
			throw new MethodNotAllowedException();
		}
		$this->Maintenance->id = $id;
		if (!$this->Maintenance->exists()) {
			throw new NotFoundException(__('Invalid Maintenance Id'));
		}

		$saveData = array(
			'id' => $id,
			'staff_id' => $this->cdata['id'],
			'is_under_maintenance' => !$is_under_maintenance
		);
		if ($this->Maintenance->Save($saveData)) {
			$message = (!$is_under_maintenance) ? 'ONにしました。' : 'OFFにしました。';
			$this->Session->setFlash($message, 'default', array('class' => 'alert alert-success'));
			$this->redirect(array('action' => 'index'));
		}
		$message = (!$is_under_maintenance) ? 'ONにできませんました。' : 'OFFにできませんました。';
		$this->Session->setFlash($message, 'default', array('class' => 'alert alert-error'));
		$this->redirect(array('action' => 'index'));
	}
}
