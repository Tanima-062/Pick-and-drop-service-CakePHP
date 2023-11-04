<?php
class MessagesController extends AppController
{

    public $uses = array('Message', 'Staff');

    /**
     * 前処理
     *
     * @return void
     */
    public function beforeFilter()
    {
        parent::beforeFilter();
        $staffList = $this->Staff->getStaffList();
        $this->set('staffList', $staffList);

        $this->set('fromTimeOptions', array(
            'formName' => 'Message',
            'fieldName' => 'from_time',
            'dateFormat' => 'YMDHIS',
            'class' => 'span2',
            'empty' => '---',
            'setCurrentMonth'=>false
        ));

        $this->set('toTimeOptions', array(
            'formName' => 'Message',
            'fieldName' => 'to_time',
            'dateFormat' => 'YMDHIS',
            'class' => 'span2',
            'empty' => '---',
            'setCurrentMonth'=>false
        ));
    }

    /**
     * 一覧画面処理
     *
     * @return void
     */
    public function index()
    {
        $this->set('is_system_admin', $this->cdata['is_system_admin']);
        $this->Message->recursive = -1;

        $conditions = array();

        if (!empty($this->request->query['Staff'])) {
            $conditions['staff_id'] = $this->request->query['Staff'];
        }

        if (!empty($this->request->query['from_time'])) {
            $date = $this->get_date($this->request->query['from_time']);
            if (!empty($date)) {
                $conditions['from_time >='] = $date;
            }
        }
        if (!empty($this->request->query['to_time'])) {
            $date = $this->get_date($this->request->query['to_time']);
            if (!empty($date)) {
                $conditions['to_time <='] = $date;
            }
        }
        $this->request->data['Message'] = $this->request->query;
        $this->paginate = array('conditions'=> $conditions,'order' => 'Message.id desc');

        $this->set('messages', $this->paginate());
    }

    /**
     * 日付情報より表示形式取得処理
     *
     * @param array $date
     * @return void
     */
    private function get_date($date)
    {
        $ret = '';
        if (!empty($date['year']) && !empty($date['month']) && !empty($date['day'])) {
            $ret = $date['year'].'-'.$date['month'].'-'.$date['day'];
            if (!empty($date['hour'])) {
                $ret = $ret . ' '. $date['hour'];
            }
            if (!empty($date['hour']) && !empty($date['min'])) {
                $ret = $ret . ':'. $date['min'];
            } else {
                if (!empty($date['hour'])) {
                    $ret = $ret . ':00';
                }
            }
        }
        return $ret;
    }

    /**
     * 追加画面処理
     *
     * @return void
     */
    public function add()
    {
        if ($this->request->is('post')) {
            $saveData = $this->request->data['Message'];

            $saveData['staff_id'] = $this->cdata['id'];
            $saveData['modified_staff_id']  = $this->cdata['id'];
            if ($saveData['delete_flg'] == 1) {
                $saveData['deleted'] = date("Y-m-d H:i:s", time());
            }

            if ($this->Message->save($saveData)) {
                $this->Session->setFlash('お知らせを追加しました', 'default', array('class' => 'alert alert-success'));
                $this->redirectReferer();
            } else {
                $err_msg = 'お知らせの登録に失敗しました';
                if (isset($this->Message->validationErrors)) {
                    $errors = $this->Message->validationErrors;
                    foreach ($errors as $error) {
                        $err_msg .= '<br>'.$error[0];
                    }
                }
                $this->Session->setFlash($err_msg, 'default', array('class' => 'alert alert-error'));
            }
        }
    }

    /**
     * 編集画面処理
     *
     * @param string $id
     * @return void
     */
    public function edit($id = null)
    {

        if (!$this->Message->exists($id)) {
            throw new NotFoundException(__('Invalid Message'));
        }

        if ($this->request->is('post') || $this->request->is('put')) {
            $saveData = $this->request->data['Message'];

            $saveData['modified_staff_id']  = $this->cdata['id'];
            if ($saveData['delete_flg'] == 1) {
                $this->Message->id = $id;
                if ($this->Message->field('delete_flg') == 0) {
                    $saveData['deleted'] = date("Y-m-d H:i:s", time());
                }
            } else {
                $saveData['deleted'] = null;
            }

            if ($this->Message->save($saveData)) {
                $this->Session->setFlash('お知らせを編集しました', 'default', array('class' => 'alert alert-success'));
                $this->redirectReferer();
            } else {
                $err_msg = 'お知らせの編集に失敗しました';
                if (isset($this->Message->validationErrors)) {
                    $errors = $this->Message->validationErrors;
                    foreach ($errors as $error) {
                        $err_msg .= '<br>'.$error[0];
                    }
                }
                $this->Session->setFlash($err_msg, 'default', array('class' => 'alert alert-error'));
            }
        } else {
            $this->Message->recursive = -1;
            $message = $this->Message->find('first', array('conditions' => array('Message.id'=> $id)));
            $this->request->data['Message'] = $message['Message'];
        }
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
