<?php

App::uses('AppController', 'Controller');

/**
 * CancelFees Controller
 *
 * @property CancelFee $CancelFee
 */
class CancelFeesController extends AppController
{

    /**
     * 一覧画面処理
     *
     * @return void
     */
    public function index()
    {
        $client_id = (isset($this->request->query['client_id'])) ? $this->request->query['client_id'] : '';
        $sales_type = (isset($this->request->query['sales_type'])) ? $this->request->query['sales_type'] : '';
        $is_published = (isset($this->request->query['is_published'])) ? $this->request->query['is_published'] : '';

        $this->CancelFee->recursive = 0;

        // 一覧表示
        $this->Paginator->settings = [
            'conditions' => [
                'CancelFee.delete_flg' => 0,
            ],
            'order' => [
                'CancelFee.id',
            ],
            'limit' => 20,
            'paramType' => 'querystring'
        ];

        // 検索ボタンが押されたら一覧表示に条件追加
        if ($client_id != '') {
            $this->Paginator->settings['conditions']['CancelFee.client_id'] = $client_id;
        }

        if ($sales_type != '') {
            $this->Paginator->settings['conditions']['CancelFee.sales_type'] = $sales_type;
        }

        if ($is_published != '') {
            $this->Paginator->settings['conditions']['CancelFee.is_published'] = $is_published;
        }

        $cancelFees = $this->paginate();
        $this->set('cancelFees', $this->CancelFee->changeViewList($cancelFees));
        $this->set('clientList', $this->Client->find('list', ['conditions' => ['delete_flg' => 0]]));
        $this->set('salesTypes', Constant::salesType());
        $this->set('client_id', $client_id);
        $this->set('sales_type', $sales_type);
        $this->set('is_published', $is_published);
    }

    /**
     * 追加画面処理
     *
     * @return void
     */
    public function add()
    {
        if ($this->request->is('post') || $this->request->is('put')) { // edit
            $this->log("post:".print_r($this->request->data, true), LOG_DEBUG);
            $this->CancelFee->set('staff_id', $this->cdata['id']);
            try {
                if ($this->CancelFee->save($this->request->data)) {
                    $this->Session->setFlash('キャンセル料を追加しました', 'default', array('class' => 'alert alert-success'));
                    $this->redirectReferer();
                } else {
                    $this->set('clientList', $this->Client->find('list', ['conditions' => ['delete_flg' => 0]]));
                    $this->Session->setFlash('キャンセル料の追加に失敗しました' . $this->CancelFee->errorMsg, 'default', array('class' => 'alert alert-alert'));
                }
            } catch (Exception $e) {
                $this->set('clientList', $this->Client->find('list', ['conditions' => ['delete_flg' => 0]]));
                $this->Session->setFlash('キャンセル料の追加に失敗しました。'.$e->getMessage(), 'default', array('class' => 'alert alert-alert'));
            }
        } else { // view
            $this->set('clientList', $this->Client->find('list', ['conditions' => ['delete_flg' => 0]]));
        }
    }

    /**
     * 編集画面処理
     *
     * @throws NotFoundException
     * @param string $id
     * @return void
     */
    public function edit($id = null)
    {
        if (!$this->CancelFee->exists($id)) {
            throw new NotFoundException(__('Invalid cancel fee'));
        }

        if ($this->request->is('post') || $this->request->is('put')) { // edit
            $this->log("post:".print_r($this->request->data, true), LOG_DEBUG);
            $this->CancelFee->set('staff_id', $this->cdata['id']);
            try {
                if ($this->CancelFee->save($this->request->data)) {
                    $this->Session->setFlash('キャンセル料を編集しました', 'default', array('class' => 'alert alert-success'));
                    $this->redirectReferer();
                } else {
                    $this->view($id);
                    $this->Session->setFlash('キャンセル料の編集に失敗しました' . $this->CancelFee->errorMsg, 'default', array('class' => 'alert alert-alert'));
                }
            } catch (Exception $e) {
                $this->view($id);
                $this->Session->setFlash('キャンセル料の編集に失敗しました。' .$e->getMessage(), 'default', array('class' => 'alert alert-alert'));
            }
        } else { // view
            $this->view($id);
        }
    }

    /**
     * 詳細画面処理
     *
     * @param string $id
     * @return void
     */
    private function view($id)
    {
        $cancelFee = $this->CancelFee->find('first', [
            'conditions' => [
                'CancelFee.id' => $id
            ]
        ]);

        $this->request->data = $cancelFee;
    }

    /**
     * 削除メソッド
     * 論理削除
     *
     * @param string $id
     * @return void
     */
    public function delete($id = null)
    {
        if (!$this->request->is('post')) {
            throw new MethodNotAllowedException();
        }

        $saveData['id'] = $id;
        $saveData['staff_id'] = $this->cdata['id'];
        $saveData['delete_flg'] = 1;

        try {
            if ($this->CancelFee->save($saveData, ['validate' => false, 'callbacks' => false])) {
                $this->Session->setFlash('削除しました', 'default', array('class' => 'alert alert-success'));
            } else {
                $this->Session->setFlash('削除に失敗しました', 'default', array('class' => 'alert alert-error'));
            }
        } catch (Exception $e) {
            $this->Session->setFlash('削除に失敗しました。'.$e->getMessage(), 'default', array('class' => 'alert alert-error'));
        }

        $this->redirect($this->referer());
    }

    /**
     * 元画面へ遷移
     * ※元画面がなければ初期画面へ
     *
     * @return void
     */
    private function redirectReferer()
    {
        // refererがあれば戻す
        if (!empty($this->request->data['Custom']['referer'])) {
            $this->redirect($this->request->data['Custom']['referer']);
        } else {
            $this->redirect(array('action' => 'index'));
        }
    }
}
