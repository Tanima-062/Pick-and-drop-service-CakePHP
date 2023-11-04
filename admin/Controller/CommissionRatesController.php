<?php

App::uses('AppController', 'Controller');

/**
 * CommissionRates Controller
 *
 * @property CommissionRate $CommissionRate
 */
class CommissionRatesController extends AppController
{

    public $uses = array('CommissionRate', 'SettlementCompany');

    /**
     * 一覧画面処理
     *
     * @return void
     */
    public function index()
    {
        $client_id = (isset($this->request->query['client_id'])) ? $this->request->query['client_id'] : '';
        $is_published = (isset($this->request->query['is_published'])) ? $this->request->query['is_published'] : '';

        $this->CommissionRate->recursive = 0;

        // 一覧表示
        $this->Paginator->settings = [
            'conditions' => [
                'CommissionRate.delete_flg' => 0,
            ],
            'order' => [
                'CommissionRate.id',
            ],
            'limit' => 20,
            'paramType' => 'querystring'
        ];

        // 検索ボタンが押されたら一覧表示に条件追加
        if ($client_id != '') {
            $this->Paginator->settings['conditions']['CommissionRate.client_id'] = $client_id;
        }

        if ($is_published != '') {
            $this->Paginator->settings['conditions']['CommissionRate.is_published'] = $is_published;
        }

        $commissionRates = $this->paginate();
        $this->set('commissionRates', $this->CommissionRate->changeViewList($commissionRates));
        $this->set('clientList', $this->Client->find('list', ['conditions' => ['delete_flg' => 0]]));
        $this->set('client_id', $client_id);
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
            if (isset($this->request->data['CommissionRate']['apply_term_from'])) {
                $month = $this->request->data['CommissionRate']['apply_term_from']['year'].
                    '-'.
                    $this->request->data['CommissionRate']['apply_term_from']['month'];
                $this->request->data['CommissionRate']['apply_term_from'] = date('Y-m-d 00:00:00', strtotime('first day of ' . $month));
            }

            if (isset($this->request->data['CommissionRate']['apply_term_to'])) {
                $month = $this->request->data['CommissionRate']['apply_term_to']['year'].
                    '-'.
                    $this->request->data['CommissionRate']['apply_term_to']['month'];
                $this->request->data['CommissionRate']['apply_term_to'] = date('Y-m-d 23:59:59', strtotime('last day of ' . $month));
            }
            $this->CommissionRate->set('staff_id', $this->cdata['id']);

            try {
                if ($this->CommissionRate->save($this->request->data)) {
                    $this->Session->setFlash('販売手数料を追加しました', 'default', array('class' => 'alert alert-success'));
                    $this->redirectReferer();
                } else {
                    $this->set('clientList', $this->Client->find('list', ['conditions' => ['delete_flg' => 0]]));
                    $this->Session->setFlash('販売手数料の追加に失敗しました' . $this->CommissionRate->errorMsg, 'default', array('class' => 'alert alert-alert'));
                }
            } catch (Exception $e) {
                $this->set('clientList', $this->Client->find('list', ['conditions' => ['delete_flg' => 0]]));
                $this->Session->setFlash('販売手数料の追加に失敗しました。'.$e->getMessage(), 'default', array('class' => 'alert alert-alert'));
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
        if (!$this->CommissionRate->exists($id)) {
            throw new NotFoundException(__('Invalid commission rate'));
        }

        if ($this->request->is('post') || $this->request->is('put')) { // edit
            $this->log("post:".print_r($this->request->data, true), LOG_DEBUG);
            if (isset($this->request->data['CommissionRate']['apply_term_from'])) {
                $month = $this->request->data['CommissionRate']['apply_term_from']['year'].
                    '-'.
                    $this->request->data['CommissionRate']['apply_term_from']['month'];
                $this->request->data['CommissionRate']['apply_term_from'] = date('Y-m-d 00:00:00', strtotime('first day of ' . $month));
            }

            if (isset($this->request->data['CommissionRate']['apply_term_to'])) {
                $month = $this->request->data['CommissionRate']['apply_term_to']['year'].
                    '-'.
                    $this->request->data['CommissionRate']['apply_term_to']['month'];
                $this->request->data['CommissionRate']['apply_term_to'] = date('Y-m-d 23:59:59', strtotime('last day of ' . $month));
            }
            $this->CommissionRate->set('staff_id', $this->cdata['id']);
            try {
                if ($this->CommissionRate->save($this->request->data)) {
                    $this->Session->setFlash('販売手数料を編集しました', 'default', array('class' => 'alert alert-success'));
                    $this->redirectReferer();
                } else {
                    $this->view($id);
                    $this->Session->setFlash('販売手数料の編集に失敗しました' . $this->CommissionRate->errorMsg, 'default', array('class' => 'alert alert-alert'));
                }
            } catch (Exception $e) {
                $this->view($id);
                $this->Session->setFlash('販売手数料の編集に失敗しました。' .$e->getMessage(), 'default', array('class' => 'alert alert-alert'));
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
        $commissionRate = $this->CommissionRate->find('first', [
            'conditions' => [
                'CommissionRate.id' => $id
            ]
        ]);

        $this->set(
            'settlementCompanies',
            $this->SettlementCompany->find(
                'list',
                ['conditions' => ['client_id' => $commissionRate['CommissionRate']['client_id']]]
            )
        );
        $this->request->data = $commissionRate;
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
            if ($this->CommissionRate->save($saveData, ['validate' => false, 'callbacks' => false])) {
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
     * Ajaxにて決済会社取得処理
     *
     * @return json
     */
    public function ajaxGetSettlementCompany()
    {
        $this->autoRender = false;

        if (!$this->request->is('ajax')) {
            return json_encode(['ret' => 'error', 'message' => 'リクエストエラー']);
        }

        $settlementCompany = $this->SettlementCompany->find('list', ['conditions' => ['client_id' => $this->request->data['client_id']]]);

        return json_encode(['ret' => 'ok', 'message' => $settlementCompany]);
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
